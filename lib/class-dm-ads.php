<?php if(!defined('DM_FIGHTERS_VERSION')) die('Fatal Error');

if(!class_exists('DMAds'))
{
    class DMAds
    {
        static $instance;
        public $option_fields = [
            'ab_banners' => [
                '_ab_banner_type' => [
                    'label' => 'Banner Type',
                    'description' => 'Type of Banner. Require',
                    'type' => 'text',
                    'required' => true
                ],
                '_ab_banner_code' => [
                    'label' => 'Banner Code',
                    'description' => 'The Banner HTML Code. Required',
                    'type' => 'htmlcode',
                    'required' => true
                ],
                '_ab_banner_width' => [
                    'label' => 'Banner Width',
                    'description' => 'Width in px. Required',
                    'type' => 'number',
                    'required' => true
                ],
                '_ab_banner_height' => [
                    'label' => 'Banner Height',
                    'description' => 'Height in px. Required',
                    'type' => 'number',
                    'required' => true
                ],
            ],
        ];

        public function __CONSTRUCT(){
            add_action('init', [&$this, 'main_init']);
            add_action('admin_init', [&$this, 'admin_init']);
            add_action( 'widgets_init', function(){
                register_widget( 'CageBanner_Widget' );
            });
        }

        public function main_init(){
            $this->create_banners_post_type();
            $this->create_banners_save_post();
        }

        public function admin_init(){
            $this->register_banners_meta_boxes();
            add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_styles'] );
            add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_scripts'] );
        }

        public function create_banners_post_type(){

            $registered = register_post_type( 'affiliate_banner',[
                'labels' => [
                    'name' => 'Affiliate Banners',
                    'singular_name' => 'Affiliate Banner',
                    'add_new' => 'Add New Affiliate Banner',
                    'add_new_item' => 'Add New Affiliate Banner',
                    'edit_item' => 'Edit Affiliate Banner',
                    'new_item' => 'Add New Affiliate Banner',
                    'view_item' => 'View Affiliate Banner',
                    'search_items' => 'Search Affiliate Banner',
                    'not_found' => 'No banners found',
                    'not_found_in_trash' => 'No banners found in trash'
                ],
                'public' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'menu_icon'           => 'dashicons-images-alt2',
                'rewrite' => [
                    'slug' => 'affiliate-banners'
                ],
                'supports' => [
                    'title',
                    // 'editor',
                    // 'thumbnail',
                    // 'excerpt',
                    // 'page-attributes',
                    'custom-fields',
                    // 'comments'
                ],
                'taxonomies' => ['post_tag']
            ]);

            flush_rewrite_rules();
        }

        public function register_banners_meta_boxes(){
            add_action( 'add_meta_boxes', function(){
                add_meta_box( 'ab_banners', 'Banner Upload', [&$this, 'cb_banner_upload_metabox'], 'affiliate_banner' , 'normal', 'high');
            });
        }

        public function cb_banner_upload_metabox(){
            global $post;
            $ab_upload_data  = [];
            foreach ($this->option_fields['ab_banners'] as $field => $options) {
                $ab_upload_data[$field] = [
                    'options' => $options,
                    'value' => get_post_meta( $post->ID, $field, true )
                ];
            }
            wp_nonce_field( basename( __FILE__ ), '_banner_upload_metabox_nonce' );
            include_once( DM_FIGHTERS_PLUGIN_DIR . 'partials/banner-upload-metabox.php' );
        }

        public function create_banners_save_post(){
            add_action( 'save_post_affiliate_banner', [ &$this , 'save_banner_upload_metabox' ]);
        }

        public function save_banner_upload_metabox(){

            global $post;
            // if( !isset( $_POST['_banner_upload_metabox_nonce'] ) || !wp_verify_nonce( $_POST['_banner_upload_metabox_nonce'], basename( __FILE__ ) ) ){
            //     return;
            // }
            if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                return;
            }
            if( ! current_user_can( 'edit_post', $post->id ) ){
                return;
            }
            foreach ($this->option_fields['ab_banners'] as $field => $options) {
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
            if($post->post_type != 'affiliate_banner')
            return;

            $styles = [
                // 'bootstrap' => 'http://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min',
                // 'admin' => ISB_GAME_bannerS_PLUGIN_URL . '/css/admin',
                // 'star-rating' => ISB_GAME_bannerS_PLUGIN_URL . '/assets/star-rating/css/star-rating.min',
                // 'star-rating-theme' => ISB_GAME_bannerS_PLUGIN_URL . '/assets/star-rating/themes/krajee-svg/theme.min',
            ];
            foreach ( $styles as $id => $path) {
                wp_register_style( $id . '-css' , $path . '.css', false);
                wp_enqueue_style( $id . '-css');
            };

            wp_enqueue_style('thickbox');
        }

        public function enqueue_admin_scripts($hook){
            global $post;
            if($post->post_type != 'affiliate_banner')
            return;

            $scripts = [
                // 'admin' => ISB_GAME_bannerS_PLUGIN_URL . 'js/admin',
                // 'star-rating' => ISB_GAME_bannerS_PLUGIN_URL . '/assets/star-rating/js/star-rating.min',
                // 'star-rating-theme' => ISB_GAME_bannerS_PLUGIN_URL . '/assets/star-rating/themes/krajee-svg/theme.min',
            ];

            foreach ($scripts as $id => $path) {
                wp_register_script( $id . '-js',  $path . '.js' ,['jquery']);
                wp_enqueue_script( $id . '-js');
            }

            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_script('suggest');

        }

        public function activate(){ }

        public function deactivate(){ }
    }

    class CageBanner_Widget extends WP_Widget {

        /**
        * Sets up the widgets name etc
        */
        public function __construct() {
            $widget_ops = array(
                'classname' => 'affiliate-banner-widget',
                'description' => 'Affiliate Banner',
            );
            parent::__construct( 'affiliate_banner_widget', 'Affiliate Banner', $widget_ops );
        }

        /**
        * Outputs the content of the widget
        *
        * @param array $args
        * @param array $instance
        */
        public function widget( $args, $instance ) {
            // outputs the content of the widget
            // echo $args['before_widget'];

            ob_start();
            $opts = [
                'post_type' => 'affiliate_banner',
                'status' => 'publish',
                'posts_per_page' => 1
            ];

            if(isset($instance['name']) && isset($instance['name'])!='random'){
                $opts['post_name'] = $instance['name'];
            }else{
                $opts['orderby'] = 'rand';
            }

            if(!empty($instance['type'])){
                $opts['meta_query'] = [
                    [
                        'key' =>  '_ab_banner_type',
                        'value' => $instance['type'],
                        'compare' => 'LIKE',
                    ]
                ];
            }
            $ads = get_posts($opts);

            if(!empty($ads)){
                $types = get_post_meta($ads[0]->ID,'_ab_banner_type',true);
                $index = array_search($instance['type'],$types);

                if($index!==FALSE){
                    $code = get_post_meta($ads[0]->ID,'_ab_banner_code',true)[$index];
                    $width = get_post_meta($ads[0]->ID,'_ab_banner_width',true)[$index];
                    $height = get_post_meta($ads[0]->ID,'_ab_banner_height',true)[$index];
                    include DM_FIGHTERS_PLUGIN_DIR . "partials/widget-affiliate-banner.php";
                }
            }

            echo ob_get_clean();
            // echo $args['after_widget'];
        }

        /**
        * Outputs the options form on admin
        *
        * @param array $instance The widget options
        */
        public function form( $instance ) {
            // outputs the options form on admin
            $name = ! empty( $instance['name'] ) ? $instance['name'] : 'random';
            $type = ! empty( $instance['type'] ) ? $instance['type'] : '';
            $activeAffiliates = [];
            $activeAffiliates = get_posts([
                'post_type' => 'affiliate_banner',
                'posts_per_page' => -1,
                'status' => 'publish'
            ]);
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>"><?php _e( esc_attr( 'Affiliate Name:' ) ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'name' ) ); ?>">
                    <option value="random" <?=( $name == 'random' ? 'selected' : '')?>>Randomize</option>
                    <?php foreach ($activeAffiliates as $key => $affiliate) {
                        ?>
                        <option value="<?=$affiliate->post_name?>" <?=( $name == $affiliate->post_name ? 'selected' : '')?>><?=$affiliate->post_title?></option>
                        <?php
                    }?>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php _e( esc_attr( 'Banner Type:' ) ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" type="text" value="<?php echo esc_attr( $type ); ?>">
            </p>
            <?
        }

        /**
        * Processing widget options on save
        *
        * @param array $new_instance The new options
        * @param array $old_instance The previous options
        */
        public function update( $new_instance, $old_instance ) {
            // processes widget options to be saved
            $data = array();
            $data['name'] = ( ! empty( $new_instance['name'] ) ) ? strip_tags( $new_instance['name'] ) : '';
            $data['type'] = ( ! empty( $new_instance['type'] ) ) ? strip_tags( $new_instance['type'] ) : '';
            return $data;
        }
    }

}
