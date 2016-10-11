<?php 

if(!defined('DM_FIGHTERS_VERSION')) die('Fatal Error');

if(!class_exists('DMFights')){
    class DMFights{
		
    	public $option_fields = [
            'fight_details' => [
            	'_fd_left_id',
            	'_fd_right_id',
            	'_fd_left_cat_id',
                '_fd_right_cat_id',
                '_fr_award',
                '_fr_result',
                '_fr_winner',
                '_fr_left_control',
                '_fr_left_knockdowns',
                '_fr_left_strikes_landed',
                '_fr_left_strikes_attempts',
                '_fr_left_s_strikes_landed',
                '_fr_left_s_strikes_attempts',
                '_fr_left_takedowns',
                '_fr_left_takedowns_attempts',
                '_fr_left_submission_attempts',
                '_fr_right_control',
                '_fr_right_knockdowns',
                '_fr_right_strikes_landed',
                '_fr_right_strikes_attempts',
                '_fr_right_s_strikes_landed',
                '_fr_right_s_strikes_attempts',
                '_fr_right_takedowns',
                '_fr_right_takedowns_attempts',
            	'_fr_right_submission_attempts',
            ]
        ];
        public $fight_result = [
            'draw'=>'Draw',
            'dec'=>'Decision',
            'ko_tko'=>'KO/TKO',
        ];

		public function __CONSTRUCT(){
			 add_action('init', [&$this, 'main_init']);
			 add_action('init', [&$this, 'admin_init']);
		}

		public function main_init(){
            $this->create_fights_post_type();
            $this->create_fights_save_post();
        }

        public function admin_init(){
            $this->register_fighters_meta_boxes();
            add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_styles'] );
            add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_scripts'] );
            add_action( 'wp_ajax_ajaxgetfightersbycategory', [@$this,'ajax_getFightersByCategory'] );
            add_action( 'wp_ajax_nopriv_ajaxgetfightersbycategory', [@$this,'ajax_getFightersByCategory'] );
        }

        public function ajax_getFightersByCategory(){
        	$cat_id = (int)$_POST['cat'];

        	$args = [
			    'post_type' => 'fighter',
                'posts_per_page' => -1,
			    'orderby' => 'name',
                'order'   => 'ASC',
			    'tax_query' => [
			        [
			            'taxonomy' => 'fighters_category',
			            'terms' => $cat_id,
			        ],
			    ],
			];
           	$fighters = new WP_Query( $args );
           	$_fighters = [];
           	if(!empty($fighters->posts)){
           		foreach ($fighters->posts as $f) {
           			$od = get_post_meta($f->ID);
           			$fighter = [
           				'id' => $f->ID,
           				'name' => $f->post_title,
           				'nickname' => $od['_uf_nickname'][0],
           				'age' => $od['_uf_age'][0],
           				'height' => $od['_uf_height'][0],
           				'weight' => $od['_uf_weight'][0],
           				'reach' => $od['_uf_reach'][0],
           				'legreach' => $od['_uf_leg_reach'][0],
           				'win' => $od['_uf_win'][0],
           				'loss' => $od['_uf_loss'][0],
                        'draw' => $od['_uf_draw'][0],
                        'striking' => $od['_uf_striking'][0]?:'NA',
                        'striking_attempts' => $od['_uf_striking_attempts'][0]?:'NA',
                        'striking_defense' => $od['_uf_striking_defense'][0]?:'NA',
                        'takedown' => $od['_uf_takedown'][0]?:'NA',
                        'takedown_attempts' => $od['_uf_stakedownattempts'][0]?:'NA',
                        'takedown_defense' => $od['_uf_takedown_defense'][0]?:'NA',
                        'imageleft' => wp_get_attachment_url($od['_uf_image_left'][0]),
                        'imageright' => wp_get_attachment_url($od['_uf_image_right'][0]),
           				'fimage' => wp_get_attachment_url(get_post_thumbnail_id($f->ID)),
           			];
           			array_push($_fighters, $fighter);
           		}
           	}
			echo json_encode($_fighters);
            die();
        }

        public function register_fighters_meta_boxes(){
            add_action( 'add_meta_boxes', [&$this, 'create_fighters_meta_boxes']);
        }

        public function create_fighters_meta_boxes(){
            add_meta_box( 'fight_details', 'Fight Details', [&$this, 'fight_details_metabox'], 'fights' , 'normal', 'high');
        }

        public function fight_details_metabox(){
            global $post;
            $fd_meta_data  = [];
            foreach ($this->option_fields['fight_details'] as $field) {
                $fd_meta_data[$field] = get_post_meta( $post->ID, $field, true );
            }
            $terms = get_terms( array(
			    'taxonomy' => 'fighters_category',
			    'hide_empty' => false,
			) );
            wp_nonce_field( basename( __FILE__ ), '_fight_metabox_nonce' );
            include_once( DM_FIGHTERS_PLUGIN_DIR . 'templates/dm-fight-details-metabox.php' );
        }

        public function create_fights_post_type(){
        	register_taxonomy(
	            'fights_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
	            'fights',        //post type name
	            [
	                'hierarchical' => true,
	                'label' => 'Fight Category',  //Display name
	                'labels' => [
	                    'name'              => 'Fight Category',
	                    'singular_name'     => 'Fighter Category',
	                    'search_items'      => 'Search Fights Category',
	                    'all_items'         => 'All Fight Category',
	                    'parent_item'       => 'Parent Fight Category',
	                    'parent_item_colon' => 'Parent Fight Category:',
	                    'edit_item'         => 'Edit Fight Category',
	                    'update_item'       => 'Update Fight Category',
	                    'add_new_item'      => 'Add New Fight Category',
	                    'new_item_name'     => 'New Fight Category',
	                    'menu_name'         => 'Fight Category',
	                ],
	                'public' => true,
	                'publicly_queryable' => true,
	                'exclude_from_search' => false,
	                'query_var' => true,
	                'show_ui' => true,
	                'show_admin_column' => true,
	                'capabilities' => [
	                    'manage_terms',
	                    'edit_terms',
	                    'delete_terms',
	                    'assign_terms',
	                ],
	                'rewrite' =>[
	                    'slug' => 'fights_category', // This controls the base slug that will display before each term
	                    'with_front' => false // Don't display the category base before
	                ]
	            ]
            );

           	register_post_type('fights',[
                'labels' => [
                    'name' => 'Fights',
                    'singular_name' => 'Fight',
                    'add_new' => 'Add New Fight',
                    'add_new_item' => 'Add New Fight',
                    'edit_item' => 'Edit Fight',
                    'new_item' => 'Add New Fight',
                    'view_item' => 'View Fight',
                    'search_items' => 'Search Fight',
                    'not_found' => 'No Fight found',
                    'not_found_in_trash' => 'No Fight found in trash'
                ],
                'public' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'menu_icon' => 'dashicons-groups',
                'rewrite' => [
                    'slug' => 'fights',
                ],
                'supports' => [
                    'title',
                    'editor',
                    'thumbnail',
                    'excerpt',
                    // 'page-attributes',
                    'custom-fields',
                    'comments'
                ],
            	'taxonomies' => ['post_tag','fights_category']
        	]);
            flush_rewrite_rules();
        }

        public function create_fights_save_post(){
            add_action( 'save_post_fights', [ &$this , 'save_fight_details_metabox' ]);
        }

    	public function save_fight_details_metabox(){
            global $post;
            if( !isset( $_POST['_fight_metabox_nonce'] ) || !wp_verify_nonce( $_POST['_fight_metabox_nonce'], basename( __FILE__ ) ) ){
                return;
            }
            if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                return;
            }
            if( ! current_user_can( 'edit_post', $post->id ) ){
                return;
            }
            foreach ($this->option_fields['fight_details'] as $field) {
                $this->save_meta_value($post->ID,$field,$_POST[$field]);
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

     	public function enqueue_admin_styles(){
            global $post;
            if((!empty($post)&&($post->post_type == 'fights'||$post->post_type == 'fighter'))||(!empty($_GET['page'])&&!strcasecmp($_GET['page'], 'generate-fighters-menu'))){
                $styles = [
                    'fights_admin' => DM_FIGHTERS_PLUGIN_URL . 'css/admin',
                ];
                foreach ( $styles as $id => $path) {
                    wp_register_style( $id . '-css' , $path . '.css', false);
                    wp_enqueue_style( $id . '-css');
                };
                wp_enqueue_style('thickbox');
            }else{
                return;
            }
        }
        public function enqueue_admin_scripts($hook){
            global $post;
            if((!empty($post)&&$post->post_type == 'fights')||(!empty($_GET['page'])&&!strcasecmp($_GET['page'], 'generate-fighters-menu'))){
                $scripts = [
                    'fights_admin' => DM_FIGHTERS_PLUGIN_URL . 'js/admin_fights',
                ];
                foreach ($scripts as $id => $path) {
                    wp_register_script($id . '-js',  $path . '.js', ['jquery'], null );
                    wp_enqueue_script($id . '-js');
                    wp_localize_script( $id . '-js', 'ajax_auth_object', array(
                        'ajaxurl' => admin_url( 'admin-ajax.php' ),
                        'redirecturl' => home_url(),
                        'loadingmessage' => __('Sending user info, please wait...')
                    ));
                    
                }
                wp_enqueue_script('media-upload');
                wp_enqueue_script('thickbox');
            }else{
                return;
            }
        }
	}
}