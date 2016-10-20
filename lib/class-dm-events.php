<?php 

if(!defined('DM_FIGHTERS_VERSION')) die('Fatal Error');

if(!class_exists('DMEvents')){
    class DMEvents{
		
    	public $option_fields = [
            'event_details' => [
                'ed_fighter',
            	'ed_fighter_id',
                'ed_opponent',
                'ed_opponent_id',
                'ed_location',
                'ed_result',
                'ed_decision',
                'ed_date',
                'ed_round',
                'ed_time',
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
            $this->create_events_post_type();
            $this->create_events_save_post();
        }

        public function admin_init(){
            $this->register_events_meta_boxes();
            add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_styles'] );
            add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_scripts'] );
            add_action('wp_ajax_fighter_lookup', [&$this,'fighter_lookup']);
            add_action('wp_ajax_nopriv_fighter_lookup', [&$this,'fighter_lookup']);
        }

        public function fighter_lookup() {
            global $wpdb;

            $search = like_escape($_REQUEST['q']);

            $query = 'SELECT ID,post_title FROM ' . $wpdb->posts . '
                WHERE post_title LIKE \'' . $search . '%\'
                AND post_type = \'fighter\'
                AND post_status = \'publish\'
                ORDER BY post_title ASC';
            foreach ($wpdb->get_results($query) as $row) {
                $post_title = $row->post_title;
                $id = $row->ID;
                echo $post_title . '|' . $id . "\n";
            }
            die();
        }

        public function register_events_meta_boxes(){
            add_action( 'add_meta_boxes', [&$this, 'create_events_meta_boxes']);
        }

        public function create_events_meta_boxes(){
            add_meta_box( 'event_details', 'Event Details', [&$this, 'event_details_metabox'], 'events' , 'normal', 'high');
        }

        public function event_details_metabox(){
            global $post;
            $ed_meta_data  = [];
            foreach ($this->option_fields['event_details'] as $field) {
                $ed_meta_data[$field] = get_post_meta( $post->ID, $field, true );
            }
            wp_nonce_field( basename( __FILE__ ), '_event_metabox_nonce' );
            include_once( DM_FIGHTERS_PLUGIN_DIR . 'templates/dm-event-details-metabox.php' );
        }

        public function create_events_post_type(){
        	register_taxonomy(
	            'events_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
	            'events',        //post type name
	            [
	                'hierarchical' => true,
	                'label' => 'Event Category',  //Display name
	                'labels' => [
	                    'name'              => 'Event Category',
	                    'singular_name'     => 'Event Category',
	                    'search_items'      => 'Search Event Category',
	                    'all_items'         => 'All Event Category',
	                    'parent_item'       => 'Parent Event Category',
	                    'parent_item_colon' => 'Parent Event Category:',
	                    'edit_item'         => 'Edit Event Category',
	                    'update_item'       => 'Update Event Category',
	                    'add_new_item'      => 'Add New Event Category',
	                    'new_item_name'     => 'New Event Category',
	                    'menu_name'         => 'Event Category',
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
	                    'slug' => 'events_category', // This controls the base slug that will display before each term
	                    'with_front' => false // Don't display the category base before
	                ]
	            ]
            );

           	register_post_type('events',[
                'labels' => [
                    'name' => 'Events',
                    'singular_name' => 'Event',
                    'add_new' => 'Add New Event',
                    'add_new_item' => 'Add New Event',
                    'edit_item' => 'Edit Event',
                    'new_item' => 'Add New Event',
                    'view_item' => 'View Event',
                    'search_items' => 'Search Event',
                    'not_found' => 'No Event found',
                    'not_found_in_trash' => 'No Event found in trash'
                ],
                'public' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'menu_icon' => 'dashicons-groups',
                'rewrite' => [
                    'slug' => 'events',
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
            	'taxonomies' => ['post_tag','events_category']
        	]);
            flush_rewrite_rules();
        }

        public function create_events_save_post(){
            add_action( 'save_post_events', [ &$this , 'save_event_details_metabox' ]);
        }

    	public function save_event_details_metabox(){
            global $post;
            if( !isset( $_POST['_event_metabox_nonce'] ) || !wp_verify_nonce( $_POST['_event_metabox_nonce'], basename( __FILE__ ) ) ){
                return;
            }
            if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                return;
            }
            if( ! current_user_can( 'edit_post', $post->id ) ){
                return;
            }
            foreach ($this->option_fields['event_details'] as $field) {
                if($field == 'ed_fighter_id'&&empty($_POST[$field])&&!empty($_POST['ed_fighter'])){
                    $_wp_post_fields = [
                        'post_title' => $_POST['ed_fighter'],
                        'post_status' => 'publish',
                        'post_type' => 'fighter',
                    ];
                    $_post_id = wp_insert_post($_wp_post_fields);
                    $this->save_meta_value($post->ID,$field,$_post_id);
                }elseif($field == 'ed_opponent_id'&&empty($_POST[$field])&&!empty($_POST['ed_opponent'])){
                    $_wp_post_fields = [
                        'post_title' => $_POST['ed_opponent'],
                        'post_status' => 'publish',
                        'post_type' => 'fighter',
                    ];
                    $_post_id = wp_insert_post($_wp_post_fields);
                    $this->save_meta_value($post->ID,$field,$_post_id);
                }else{
                    $this->save_meta_value($post->ID,$field,$_POST[$field]);
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

     	public function enqueue_admin_styles(){
            global $post;
            if(!empty($post)&&$post->post_type == 'events'){
                $styles = [
                    'events_admin' => DM_FIGHTERS_PLUGIN_URL . 'css/admin_events',
                ];
                foreach ( $styles as $id => $path) {
                    wp_register_style( $id . '-css' , $path . '.css', false);
                    wp_enqueue_style( $id . '-css');
                };
                 wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
                wp_enqueue_style( 'jquery-ui' ); 
            }else{
                return;
            }
        }
        public function enqueue_admin_scripts($hook){
            global $post;
            if(!empty($post)&&$post->post_type == 'events'){
                $scripts = [
                    'events_admin' => DM_FIGHTERS_PLUGIN_URL . 'js/admin_events',
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
                wp_enqueue_script('suggest');
                wp_enqueue_script('jquery-ui-datepicker');
            }else{
                return;
            }
        }
	}
}