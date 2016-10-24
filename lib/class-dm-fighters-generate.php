<?php
if(!defined('DM_FIGHTERS_VERSION')) die('Fatal Error');

if(!class_exists('DMFightersGenerate')){
    class DMFightersGenerate{
    	public $browser = null;
    	public $_is_get_next_page = true;

    	public $fighter_category = [
           'Flyweight'=>'//*[@id="dd-fighters-classes"]/ul/li[1]/a',
           'Bantamweight'=>'//*[@id="wc-1"]/div/a',
           'Featherweight'=>'//*[@id="wc-2"]/div/a',
           'Lightweight'=>'//*[@id="wc-3"]/div/a',
           'Welterweight'=>'//*[@id="wc-4"]/div/a',
           'Middleweight'=>'//*[@id="wc-5"]/div/a',
           'Light_Heavyweight'=>'//*[@id="wc-6"]/div/a',
           'Heavyweight'=>'//*[@id="wc-7"]/div/a',
           'Women_Strawweight'=>'//*[@id="dd-fighters-classes"]/ul/li[9]/a',
           'Women_Bantamweight'=>'//*[@id="dd-fighters-classes"]/ul/li[10]/a',
        ];

        public $generated_fighters = [];

        public $_fighter_details = [
        	'name' => [
	        	'gssFirstName' => null,
	        	'gssLastName' => null,
	        	'gssNickname' => null,
	        	'gssSmallThumbnail' => null,
	        	'gssHeightFormat' => null,
	        	'gssHeightCm' => null,
	        	'gssWeight' => null,
	        	'gssWeightKg' => null,
	        	'gssRecord' => null,
        	],
        	'id' => [
        		'fighter-skill-summary' => null,
        		'fighter-from' => null,
        		'fighter-lives-in' => null,
        		'fighter-age' => null,
        		'fighter-reach' => null,
        		'fighter-leg-reach' => null,
        		'total-takedowns-number' => null,
        		'successful-submissions' => null,
        		'successful-passes' => null,
        		'successful-sweeps' => null,
        		'striking-defense-pecentage' => null,
        		'takedown-defense-percentage' => null,
        		'biography' => null
        	],
        	'class' =>[
        		'fighter-ranking' => null,
        	],
        	'og' => [
        		'meta[property="og:image"]' => null
        	]
        ];

        public $feed_base_url = 'http://www.ufc.com';
        public $feed_category_url = 'http://www.ufc.com/fighter/Weight_Class/';
        public $fighter_thumb_base_url = 'http://media.ufc.tv/';

		public function __CONSTRUCT(){
			add_action('admin_menu', [$this,'cgp_menu_page']);
			// $this->admin_init();

		}

		public function save_to_post(){
			if(!empty($this->generated_fighters)){
				foreach ($this->generated_fighters as $fc => $gf) {
					$category = get_term_by('slug', strtolower($fc) , 'fighters' );
			        if ( $category->term_id ) {
			            $category = $category->term_id;
			        }else{
			            $category = wp_insert_category([
			                'cat_name' => ucfirst($fc),
			                'category_description' => '',
			                'category_nicename' => ucfirst($fc),
			                'taxonomy' => 'fighters'
			            ],false);
			        }
					foreach ($gf as $key => $f) {
						if($key<10){
							$_wp_post_fields = [
					        	'post_content' => $f['biography'],
					        	'post_title' => $f['gssFirstName'].' '.$f['gssLastName'],
					        	'post_status' => 'publish',
					        	'post_type' => 'fighter',
					        	'post_category' => array($category),
					        ];
							$_post_id = wp_insert_post($_wp_post_fields);
							if(!empty($_post_id)){
								wp_set_post_terms( $_post_id, [$category], 'fighters' );
								$option_fields = [
					                '_uf_nickname' => $f['gssNickname'],
					                '_uf_hometown' => $f['ighter-lives-in'],
					                '_uf_age' => $f['fighter-age'],
					                '_uf_height' => $f['gssHeightFormat'],
					                '_uf_weight' => $f['gssWeight'],
					                '_uf_reach' => $f['fighter-reach'],
					                '_uf_leg_reach' => $f['fighter-leg-reach'],
					                '_uf_win' => $f['win'],
					                '_uf_loss' => $f['loss'],
					                '_uf_draw' => $f['draw'],
					                '_uf_summary' => $f['fighter-skill-summary'],
					                '_uf_takedown' => '',
					                '_uf_striking' => '',
					                '_uf_image' => $f['featured_image']
						        ];
						        foreach ($option_fields as $key => $field) {
						        	if($key!='_uf_image')
			                        	$this->save_meta_value($_post_id,$key,$field);
			                        else{
			                        	$this->grab_thumbnail($field,$_post_id);
			                        	$this->save_meta_value($_post_id,$key,$this->grab_thumbnail($field,$_post_id,false));
			                        }
			                    }
							}
						}
					}
				}
			}
		}

		public function save_meta_value($id,$meta_id = '',$value = ''){
            if(!empty($meta_id)){
                if( isset( $value ) ){
                    update_post_meta( $id , $meta_id , $value );
                }else{
                    delete_post_meta( $id , $meta_id  );
                }
            }
        }

        public function grab_thumbnail( $image_url, $post_id , $thumbnail = true ){
            $upload_dir = wp_upload_dir();
            $opts = [
                'http' => [
                    'method'  => 'GET',
                    'user_agent '  => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36",
                    'header' => [
                        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
                        '
                    ]
                ]
            ];
            $context  = stream_context_create($opts);
            if(!empty($image_url)){
            	require_once(ABSPATH . 'wp-admin/includes/image.php');
            	$attach_id = [];
            	foreach ($image_url as $key => $url) {
            		if($key != 0)
            			$url = $this->fighter_thumb_base_url.$url;

        		 	$image_data = self::file_get_contents_curl($url);
		            $filename = basename($url);
		            // Remove Query Strings
		            $querypos = strpos($filename, '?');
		            if($querypos!==FALSE){
		                $filename = substr($filename,0,$querypos);
		            }
		            if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
		            else                                    $file = $upload_dir['basedir'] . '/' . $filename;
		            file_put_contents($file, $image_data);
		            $wp_filetype = wp_check_filetype($filename, null );
		            $attachment = array(
		                'post_mime_type' => $wp_filetype['type'],
		                'post_title' => sanitize_file_name($filename),
		                'post_content' => '',
		                'post_status' => 'inherit'
		            );
		            $tempid = wp_insert_attachment( $attachment, $file, $post_id );

		            if($key==1){
		            	$this->save_meta_value($post_id,'_uf_image_right',$tempid);
		            }elseif($key==2){
		            	$this->save_meta_value($post_id,'_uf_image_left',$tempid);
		            }

		            $attach_data = wp_generate_attachment_metadata( $tempid, $file );
		            $res1 = wp_update_attachment_metadata( $tempid, $attach_data );
		            array_push($attach_id, $tempid);
            	}

	            if($thumbnail){
	                $res2 = set_post_thumbnail( $post_id, $attach_id[0] );
	            }
	        }
            return implode(',', $attach_id);
        }

		public function admin_init(){
			$this->get_fighters_category_url();
			// $this->get_fighters_per_category();
			$this->get_allpages();
		}

		public function generatefightersperpage(){
			$c_fighters = $this->visit($_POST['page']);
			$this->get_each_fighter($c_fighters,$_POST['cat'],20);
			echo json_encode($_POST);
		}

		public function get_allpages(){

			$c_pages = [];

			foreach ($this->fighter_category as $_cat => $value) {
				if(!strcasecmp($_POST['cat'], $_cat)){
					array_push($c_pages, $this->feed_category_url.$_cat);
					$c_fighters = $this->visit($this->feed_category_url.$_cat);

					if($this->_is_get_next_page){
						$_next_pages = $c_fighters->findAll('css', 'a.step');
						if(!empty($_next_pages)){
							foreach ($_next_pages as $_np) {
								$_next_page_url = $_np->getAttribute('href');
								array_push($c_pages, $this->feed_base_url.$_next_page_url);
							}
						}
					}
				}

			}
			echo json_encode($c_pages);
		}

		public function get_fighters_category_url(){
			$f_category = $this->visit('http://www.ufc.com/fighters');
			foreach ($this->fighter_category as $key => $value) {
				if(!strcasecmp($_POST['cat'], $key)){
					$elem = $f_category->find('xpath', $value);
					if(!empty($elem)){
						$this->fighter_category[$key] = $elem->getAttribute('href');
					}
				}
			}
		}

		public function save_to_post_now($_cat,$_temp_fighter){
			$category = get_term_by('slug', strtolower($_cat) , 'fighters' );
	        if ( $category->term_id ) {
	            $category = $category->term_id;
	        }else{
	            $category = wp_insert_category([
	                'cat_name' => ucfirst($_cat),
	                'category_description' => '',
	                'category_nicename' => ucfirst($_cat),
	                'taxonomy' => 'fighters'
	            ],false);
	        }
	        $f = $_temp_fighter;
			$_wp_post_fields = [
	        	'post_content' => $f['biography'],
	        	'post_title' => $f['gssFirstName'].' '.$f['gssLastName'],
	        	'post_status' => 'publish',
	        	'post_type' => 'fighter',
	        	'post_category' => array($category),
	        ];

			$_post_id = wp_insert_post($_wp_post_fields);
			if(!empty($_post_id)){
				wp_set_post_terms( $_post_id, [$category], 'fighters' );
				$images = array($f['featured_image'],$f['RightFullBodyImage'],$f['LeftFullBodyImage']);
				$option_fields = [
	                '_uf_firstname' => $f['gssFirstName'],
	                '_uf_lastname' => $f['gssLastName'],
	                '_uf_nickname' => $f['gssNickname'],
	                '_uf_hometown' => $f['fighter-lives-in'],
	                '_uf_age' => $f['fighter-age'],
	                '_uf_height' => $f['gssHeightFormat'],
	                '_uf_weight' => $f['gssWeight'],
	                '_uf_reach' => $f['fighter-reach'],
	                '_uf_leg_reach' => $f['fighter-leg-reach'],
	                '_uf_win' => $f['win'],
	                '_uf_loss' => $f['loss'],
	                '_uf_draw' => $f['draw'],
	                '_uf_summary' => $f['fighter-skill-summary'],
	                '_uf_takedowndefense' => $f['takedown-defense-percentage'],
	                '_uf_strikingdefense' => $f['striking-defense-pecentage'],
	                '_uf_takedown' => $f['suc_takedowns'],
	                '_uf_striking' => $f['suc_strikes'],
	                '_uf_submission' => $f['successful-submissions'],
	                '_uf_passes' => $f['successful-passes'],
	                '_uf_sweeps' => $f['successful-sweeps'],
	                '_uf_rank' => $f['fighter-ranking'],
	                '_uf_image' => $images

		        ];
		        foreach ($option_fields as $key => $field) {
		        	if($key!='_uf_image')
                    	$this->save_meta_value($_post_id,$key,$field);
                    else{
                    	// $this->grab_thumbnail($field,$_post_id);
                    	$this->save_meta_value($_post_id,$key,$this->grab_thumbnail($field,$_post_id));
                    }
                }
			}
		}

		public function get_fighters_per_category(){
			$limit = 50;
			if(!empty($_POST['limit'])&&$_POST['limit']<50)
				$limit = $_POST['limit'];
			if($limit<=20)
				$this->_is_get_next_page = false;
			foreach ($this->fighter_category as $_cat => $value) {
				if(!strcasecmp($_POST['cat'], $_cat)){
					$c_fighters = $this->visit($this->feed_category_url.$_cat);
					$this->get_each_fighter($c_fighters,$_cat,$limit);
					if($this->_is_get_next_page){
						$_next_pages = $c_fighters->findAll('css', 'a.step');
						if(!empty($_next_pages)){
							foreach ($_next_pages as $_np) {
								$_next_page_url = $_np->getAttribute('href');
								$c_fighters = $this->visit($this->feed_base_url.$_next_page_url);
								$this->get_each_fighter($c_fighters,$_cat,$limit);
							}
						}
					}
				}
			}
		}

		public function get_each_fighter($c_fighters,$_cat,$limit){
			$elem = $c_fighters->findAll('css', 'a.fighter-name');
			if(!empty($elem)){
				foreach ($elem as $key => $value) {
					if($key<$limit){
						$_fighter_url = $value->getAttribute('href');
						$c_fighter = $this->visit($this->feed_base_url.$_fighter_url);
						// $istop = $c_fighter->find('css', '.fighter-ranking');
						// if(!empty($istop)){
							$_fname = $c_fighter->find('named', array('id_or_name','gssFirstName'));
							$_lname = $c_fighter->find('named', array('id_or_name','gssLastName'));
							$_title = preg_replace('!\s+!', ' ', $_fname->getAttribute('content')).' '.preg_replace('!\s+!', ' ', $_lname->getAttribute('content'));
							if(!post_exists($_title)){
								foreach ($this->_fighter_details['name'] as $meta => $value) {
									$_c_meta = $c_fighter->find('named', array('id_or_name',$meta));
									if(!empty($_c_meta)){
										if($meta=='gssSmallThumbnail'){
											$_thumb_img = $_c_meta->getAttribute('content');

											$this->_fighter_details['name'][$meta] = $this->fighter_thumb_base_url.preg_replace('!\s+!', ' ', $_thumb_img);
											$_thumburl = explode('_', $_thumb_img);
											$_thumburl = implode('_',array_slice($_thumburl,0,-2));
											$this->_fighter_details['name']['RightFullBodyImage'] = $_thumburl.'_RightFullBodyImage.png';
											$this->_fighter_details['name']['LeftFullBodyImage'] = $_thumburl.'_LeftFullBodyImage.png';
										}
										else if($meta=='gssRecord'){
											$record = preg_replace('!\s+!', ' ', $_c_meta->getAttribute('content'));
											$drecord = explode('-', $record);
											$this->_fighter_details['name'][$meta] = $record;
											$this->_fighter_details['name']['win'] = $drecord[0];
											$this->_fighter_details['name']['loss'] = $drecord[1];
											$this->_fighter_details['name']['draw'] = $drecord[2];
										}
										else
											$this->_fighter_details['name'][$meta] = preg_replace('!\s+!', ' ', $_c_meta->getAttribute('content'));
									}
								}
								foreach ($this->_fighter_details['id'] as $meta => $value) {

									if($meta=='total-takedowns-number'){
										$_c_meta = $c_fighter->findAll('css', '#'.$meta);
										foreach ($_c_meta as $key => $value) {
											if($key==0)
												$this->_fighter_details['id']['suc_strikes'] = $value->getText();
											elseif($key==1)
												$this->_fighter_details['id']['suc_takedowns'] = $value->getText();
										}
									}else{
										$_c_meta = $c_fighter->find('css', '#'.$meta);
										if(!empty($_c_meta))
											$this->_fighter_details['id'][$meta] = preg_replace('!\s+!', ' ', $_c_meta->getText());
									}
								}
								foreach ($this->_fighter_details['class'] as $meta => $value) {
									$_c_meta = $c_fighter->find('css', '.'.$meta);
									if(!empty($_c_meta))
										$this->_fighter_details['class'][$meta] = preg_replace('!\s+!', ' ', $_c_meta->getText());
								}
								foreach ($this->_fighter_details['og'] as $meta => $value) {
									$_c_meta = $c_fighter->find('css', $meta);
									if(!empty($_c_meta))
										$this->_fighter_details['og']['featured_image'] = preg_replace('!\s+!', ' ', $_c_meta->getAttribute('content'));
								}
								if(!isset($this->generated_fighters[$_cat])) $this->generated_fighters[$_cat] = [];
								$_temp_fighter = array_merge($this->_fighter_details['name'],$this->_fighter_details['id'],$this->_fighter_details['class'],$this->_fighter_details['og']);
								$this->save_to_post_now($_cat,$_temp_fighter);
								// echo '- <pre>';
								// print_r($_temp_fighter);
								// echo '</pre>';
							}
						// }
					}
					// array_push($this->generated_fighters[$_cat], $_temp_fighter);
				}
			}

		}

		public function visit($url){
            $driver = new \Behat\Mink\Driver\GoutteDriver();
            $this->browser = new \Behat\Mink\Session($driver);
            $this->browser->start();
            $this->browser->visit($url);
            return $this->browser->getPage();
        }

		public function cgp_menu_page(){
	  		add_menu_page('Generate Fighters', 'Generate Fighters', 'manage_options', 'generate-fighters-menu', [$this,'plugin_settings_page'], 'dashicons-update');


		}

		public function plugin_settings_page() {
			global $wpdb;
	  		$tt = $wpdb->prefix.'term_taxonomy';
	  		$t = $wpdb->prefix.'terms';
	  		$tax = 'fighters';
	  		$cat = $wpdb->get_results( ' SELECT t.`name`, t.`slug` FROM `'. $tt .'` AS tt, `'. $t .'` AS t WHERE tt.`taxonomy` = "'. $tax .'" AND tt.`term_id` = t.`term_id` ' );
		?>
		<div class="wrap">
		  <h2>Generate Fighters</h2>

		  <!--<label>Limit: </label><input id="inp-gen-limit" type="number" max="50" min="1" value="1"/>-->
		  <select id="dd-category">
		  	<?php
		  		foreach ($cat as $c) {
		  			?>
		  			<option><?=$c->name?></option>
		  			<?php
		  		}
		  	?>
		  </select>
		  <button type="button" id="btn-generate-fighters" class="button">Generate</button>
		   <div class="cont-progress">
		 	 <div class="progress"></div>
		  </div>
		  <div class="logs-cont"></div>
		</div>
		<?php
		}
    public function file_get_contents_curl($url){

		    $ch = curl_init();

		    curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Linux; Android 6.0.1; MotoG3 Build/MPI24.107-55) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.81 Mobile Safari/537.36");
		    // Disable SSL verification
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		    // Will return the response, if false it print the response
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    // Set the url
		    curl_setopt($ch, CURLOPT_URL,$url);
		    // Execute
		    $result=curl_exec($ch);
		    // Closing
		    curl_close($ch);

		    return $result;
		}
	}
}
