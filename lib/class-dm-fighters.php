<?php

if(!defined('DM_FIGHTERS_VERSION')) die('Fatal Error');

if(!class_exists('DMFighters')){
    class DMFighters extends DMFightersGenerate{
        static $instance;
        public $option_fields = [
            'fighter_details' => [
                '_uf_nickname',
                '_uf_hometown',
                '_uf_age',
                '_uf_height',
                '_uf_weight',
                '_uf_reach',
                '_uf_leg_reach',
                '_uf_win',
                '_uf_loss',
                '_uf_draw',
                '_uf_summary',
                '_uf_takedown',
                '_uf_takedownattempts',
                '_uf_takedowndefense',
                '_uf_striking',
                '_uf_strikingattempts',
                '_uf_strikingdefense',
                '_uf_submission',
                '_uf_rank',
                '_uf_birthday',
                '_uf_association',
                '_uf_firstname',
                '_uf_lastname',
                '_uf_wintko',
                '_uf_winsubmissions',
                '_uf_windecisions',
                '_uf_windq',
                '_uf_losstko',
                '_uf_losssubmissions',
                '_uf_lossdecisions',
                '_uf_lossdq',
            ],
            'fighter_images' => [
                '_uf_image'
            ]
        ];
        public function __CONSTRUCT(){
            add_action('init', [&$this, 'main_init']);
            add_action('admin_init', [&$this, 'admin_init']);
            parent::__CONSTRUCT();
            add_filter( 'manage_edit-fighter_columns', [&$this,'custom_fighter_columns'] ) ;
            add_action( 'manage_posts_custom_column' , [&$this,'fighter_columns_data'], 10, 2 );
            add_action( 'admin_head' , [&$this,'fighter_columns_css'] );

            add_action( 'wp_ajax_updatefeaturedfighter', [&$this,'updatefeaturedfighter'] );

            add_action('restrict_manage_posts',[&$this,'list_fightercategories']);
            add_action( 'request', [&$this,'filter_fighterbycategory'] );

            // add_filter( "manage_edit-fighter_sortable_columns", [&$this,"sortable_columns"] );
             add_filter( 'posts_where' , [&$this,'posts_where'] );
        }

        public function posts_where( $where ) {
            if( is_admin() ) {
                global $wpdb;       
                if ( isset( $_GET['dd_is_featured'] ) ) {
                    $is_featured = intval( $_GET['dd_is_featured'] );
                    if($is_featured == 1)
                        $where .= " AND ID IN (SELECT post_id FROM " . $wpdb->postmeta ." WHERE meta_key='_is_featured' AND meta_value=$is_featured  )";
                    elseif($is_featured == 0)
                        $where .= " AND ID IN (SELECT pm.post_id FROM " . $wpdb->postmeta ." AS pm WHERE (pm.meta_key='_is_featured' AND pm.meta_value=0) OR (SELECT COUNT(1) FROM " . $wpdb->postmeta ." AS pmc WHERE pmc.meta_id = pm.meta_id AND pmc.meta_key='_is_featured')  ) = 0 ";
                }
            }   
            return $where;
        }
       

        public function list_fightercategories($request) {
            global $typenow;
            if ($typenow=='fighter'){
                $args = array(
                    'show_option_all' => "Show All Categories",
                    'taxonomy'        => 'fighters',
                    'name'            => 'Fighter Category',
                    'selected'        => !empty($_GET['fighters'])?$_GET['fighters']:(!empty($_GET['Fighter_Category'])?$_GET['Fighter_Category']:'')
                );
                wp_dropdown_categories($args);
                $_selected = -1;
                if(isset($_GET['dd_is_featured']))
                    $_selected = $_GET['dd_is_featured'];
                print_r('<select name="dd_is_featured"><option value="-1" '.($_selected==-1?'selected':'').'>-- Is Featured --</option><option value="1" '.($_selected==1?'selected':'').'>Featured</option><option value="0" '.($_selected==0?'selected':'').'>Not Featured</option></select>');
            }
        }
        public function filter_fighterbycategory($request) {
            if (is_admin() && $GLOBALS['PHP_SELF'] == '/wp-admin/edit.php' && isset($request['post_type']) && $request['post_type']=='fighter') {
               if(empty($request['fighters'])&&!empty($_GET['Fighter_Category'])){
                    $term = get_term($_GET['Fighter_Category']);
                    if(empty($term->errors))
                        $request['fighters'] = $term->name;
               }
            }
            return $request;
        }

        public function updatefeaturedfighter(){
          if(!empty($_POST['post_ID'])&&!empty($_POST['action'])){
            $_post_ID = sanitize_text_field($_POST['post_ID']);
            $meta_key = '_is_featured';
            $isfeatured = json_decode(get_post_meta( $_post_ID, $meta_key,true));
            if(empty($isfeatured)){
              delete_post_meta($_post_ID, $meta_key);
              add_post_meta($_post_ID, $meta_key, '1');
              echo json_encode(['status'=>1]);
              die();
            }else{
              delete_post_meta($_post_ID, $meta_key);
              echo json_encode(['status'=>2]);
              die();
            }
          }
          echo json_encode(['status'=>0]);
          die();
        }


        public function sortable_columns() {
            return array(
                'title'      => 'Title',
                'date'      => 'Date',
                'is_featured_cr'      => 'Is Featured'
            );
        }

        public function fighter_columns_css(){
            echo '
            <style>
                .column-is_featured_cr{width:120px;}
                .btn-fighter-update-featured{cursor:pointer;color:#0073aa;}
            </style>
            ';
        }

        public function custom_fighter_columns( $columns ) {
            $newcolumns = array(
                'is_featured_cr' => __( 'Is Featured' )
            );
            $columns = array_slice($columns, 0, 5, true) + $newcolumns + array_slice($columns, 5, count($columns) - 1, true) ;
            return $columns;
        }

        public function fighter_columns_data( $column, $post_id ) {
            switch ( $column ) {
            case 'is_featured_cr':
                $isfeatured = json_decode(get_post_meta( $post_id, '_is_featured',true));
                $icon = empty($isfeatured)?'empty':'filled';
                echo '<div class="btn-fighter-update-featured" data-id="'.$post_id.'" title="Set as featured video"><span class="dashicons dashicons-star-'.$icon.'"></span></div>';
                break;
            }
        }


        

        public function main_init(){
            $this->create_fighters_post_type();
            $this->create_fighters_save_post();
        }
        public function admin_init(){
            $this->register_fighters_meta_boxes();
            add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_styles'] );
            add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_scripts'] );

            add_action( 'wp_ajax_ajaxgeneratefightersperpage', [@$this,'ajax_generatefightersperpage'] );
            add_action( 'wp_ajax_nopriv_ajaxgeneratefightersperpage', [@$this,'ajax_generatefightersperpage'] );

            add_action( 'wp_ajax_ajaxgeneratefighters', [@$this,'ajax_generatefighters'] );
            add_action( 'wp_ajax_nopriv_ajaxgeneratefighters', [@$this,'ajax_generatefighters'] );
        }
        public function create_fighters_post_type(){
            register_taxonomy(
            'fighters',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
            'fighter',        //post type name
            array(
                'hierarchical' => true,
                'label' => 'Fighter Category',  //Display name
                'labels' => 	[
                    'name'              => 'Fighter Category',
                    'singular_name'     => 'Fighter Category',
                    'search_items'      => 'Search Fighters Category',
                    'all_items'         => 'All Fighters Category',
                    'parent_item'       => 'Parent Fighter Category',
                    'parent_item_colon' => 'Parent Fighter Category:',
                    'edit_item'         => 'Edit Fighter Category',
                    'update_item'       => 'Update Fighter Category',
                    'add_new_item'      => 'Add New Fighter Category',
                    'new_item_name'     => 'New Fighter Category',
                    'menu_name'         => 'Fighter Category',
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
                        'slug' => 'fighters', // This controls the base slug that will display before each term
                        'with_front' => false // Don't display the category base before
                        ]
                    )
                );
                    $registered = register_post_type( 'fighter',[
                        'labels' => [
                            'name' => 'Fighters',
                            'singular_name' => 'Fighter',
                            'add_new' => 'Add New Fighter',
                            'add_new_item' => 'Add New Fighter',
                            'edit_item' => 'Edit Fighter',
                            'new_item' => 'Add New Fighter',
                            'view_item' => 'View Fighter',
                            'search_items' => 'Search Fighter',
                            'not_found' => 'No Fighter found',
                            'not_found_in_trash' => 'No Fighter found in trash'
                        ],
                        'public' => true,
                        'capability_type' => 'post',
                        'has_archive' => true,
                        'menu_icon' => 'dashicons-universal-access',
                        'rewrite' => [
                            'slug' => 'fighter',
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
                        'taxonomies' => ['post_tag','fighters']
                    ]);
                    flush_rewrite_rules();
                }

                public function register_fighters_meta_boxes(){
                    add_action( 'add_meta_boxes', [&$this, 'create_fighters_meta_boxes']);
                }

                public function create_fighters_meta_boxes(){
                    add_meta_box( 'fighter_details', 'Fighter Information', [&$this, 'fighter_details_metabox'], 'fighter' , 'normal', 'high');
                    add_meta_box( 'fighter_images', 'Fighter Images', [&$this, 'fighter_images_metabox'], 'fighter' , 'normal', 'high');
                }

                public function create_fighters_save_post(){

                    add_action( 'save_post_fighter', [ &$this , 'save_fighter_details_metabox' ]);
                    add_action( 'save_post_fighter', [ &$this , 'save_fighter_images_metabox' ]);
                }

                public function fighter_images_metabox(){
                    global $post;
                    $gr_ov_data  = [];
                    foreach ($this->option_fields['fighter_images'] as $field) {
                        $gr_ov_data[$field] = get_post_meta( $post->ID, $field, true );
                    }
                    wp_nonce_field( basename( __FILE__ ), '_fighter_images_metabox_nonce' );
                    include_once( DM_FIGHTERS_PLUGIN_DIR . 'templates/dm-fighters-images-metabox.php' );
                }

                public function save_fighter_images_metabox(){

                    global $post;

                    if( !isset( $_POST['_fighter_images_metabox_nonce'] ) || !wp_verify_nonce( $_POST['_fighter_images_metabox_nonce'], basename( __FILE__ ) ) ){
                        return;
                    }
                    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                        return;
                    }
                    if( ! current_user_can( 'edit_post', $post->id ) ){
                        return;
                    }

                    foreach ($this->option_fields['fighter_images'] as $field) {
                        $_images = explode(',',$_POST[$field]);
                        if(!empty($_images[1])){
                            $this->save_meta_value($post->ID,'_uf_image_left',$_images[1]);
                        }
                        if(!empty($_images[2])){
                            $this->save_meta_value($post->ID,'_uf_image_right',$_images[2]);
                        }
                        if(!empty($_images[3])){
                            $this->save_meta_value($post->ID,'_uf_bio_image',$_images[3]);
                        }
                        $this->save_meta_value($post->ID,$field,$_POST[$field]);
                    }
                }

                public function fighter_details_metabox(){
                    global $post;
                    $gr_ov_data  = [];
                    foreach ($this->option_fields['fighter_details'] as $field) {
                        $gr_ov_data[$field] = get_post_meta( $post->ID, $field, true );
                    }
                    wp_nonce_field( basename( __FILE__ ), '_fighter_metabox_nonce' );
                    include_once( DM_FIGHTERS_PLUGIN_DIR . 'templates/dm-fighters-details-metabox.php' );
                }

                public function save_fighter_details_metabox(){
                    global $post;
                    if( !isset( $_POST['_fighter_metabox_nonce'] ) || !wp_verify_nonce( $_POST['_fighter_metabox_nonce'], basename( __FILE__ ) ) ){
                        return;
                    }
                    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                        return;
                    }
                    if( ! current_user_can( 'edit_post', $post->id ) ){
                        return;
                    }
                    foreach ($this->option_fields['fighter_details'] as $field) {
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
                    if((!empty($post)&&$post->post_type == 'fighter')||(!empty($_GET['page'])&&!strcasecmp($_GET['page'], 'generate-fighters-menu'))){
                        $styles = [
                            'fighters_admin' => DM_FIGHTERS_PLUGIN_URL . 'css/admin',
                        ];
                        foreach ( $styles as $id => $path) {
                            wp_register_style( $id . '-css' , $path . '.css', false);
                            wp_enqueue_style( $id . '-css');
                        };
                    }else{
                        return;
                    }
                }
                public function enqueue_admin_scripts($hook){
                    global $post;
                    if((!empty($post)&&$post->post_type == 'fighter')||(!empty($_GET['page'])&&!strcasecmp($_GET['page'], 'generate-fighters-menu'))){
                        $scripts = [
                            'fighters_admin' => DM_FIGHTERS_PLUGIN_URL . 'js/admin',
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
                    }else{
                        return;
                    }
                }
                public function ajax_generatefighters(){
                    if(class_exists('DMFightersGenerate'))
                        parent::admin_init();
                    die();
                }
                public function ajax_generatefightersperpage(){
                    if(class_exists('DMFightersGenerate'))
                        parent::generatefightersperpage();
                        // echo 'yehey';
                    die();
                }
                static function activate(){ }
                static function deactivate(){ }
            }
        }
