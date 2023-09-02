<?php

if (!defined('ABSPATH')) exit;

class DS8Clientes {
  
        private static $instance = null;
        private static $initiated = false;
        private static $perpage_ = 12;
        public $version;
        public $themes;
        /**
         * Function constructor
         */
        function __construct() {
            $this->load_dependencies();
            $this->define_admin_hooks();
            
            add_action('widgets_init', array($this, 'ds8_columnist_register_widget'));
            
            add_action('admin_enqueue_scripts', array($this, 'ds8_selectively_enqueue_admin_script'), 10 );
            add_action('wp_enqueue_scripts', array($this, 'ds8_clientes_javascript'), 10);
            add_shortcode( 'ds8cliente', array($this, 'ds8cliente_shortcode_fn') );
            add_shortcode('simple-author-box-ds8', array($this, 'shortcode'));
            
            add_filter('author_template', array($this,'load_author_template'), 10, 1);
            add_filter('single_template', array($this,'load_single_template'), 10, 1);
            
            add_action('pre_get_posts', array($this,'ds8_authors_custom_number_of_posts'), 1 );
            add_filter('author_rewrite_rules', array($this,'ds8_authors_rewrite'), 1 );
            add_action('init', array($this,'set_new_author_base'));
            add_filter('user_contactmethods', array($this,'ds8_user_contactmethods'));
            
            add_action('wp_ajax_clientes_action', array($this, 'ajax_render_users'));
            add_action('wp_ajax_nopriv_clientes_action', array($this, 'ajax_render_users'));
            add_filter('get_pagenum_link', array($this,'clientes_pagenum_link' ));
            add_filter('query_vars', array($this, 'ds8_client_register_query_var') );
            
            add_filter('home_template', array($this,'load_cpt_clitenes_template'), 10, 1);
        }
        public static function load_cpt_clitenes_template($template) {
            global $post;

            $pagina = (get_query_var( 'providencia' ) ? get_query_var( 'providencia' ) : get_query_var( 'prospecto') );
            
            $ff = $GLOBALS['bodyd'];
            
            if (!empty($pagina) && $GLOBALS['bodyd'] !== null && $GLOBALS['bodyd'] !== false) {
              
//                $response = wp_remote_get( 'https://fd.deseisaocho.com/wp-json/wp/v2/posts?slug='.$dictamen);
//                $body     = json_decode(wp_remote_retrieve_body( $response ),true);
//                $body = $body[0];
//                
//  
//                $data = array('data' => array('content' => $body['content']['rendered'],
//                              'title' => $body['title']['rendered'],
//                              'date' => $body['date'],
//                              'guid' => $body['guid']['rendered']));

                //Your plugin path 
                $plugin_path = plugin_dir_path( __FILE__ );

                // The name of custom post type single template
                $template_name = 'template-parts/singular.php';

                // A specific single template for my custom post type exists in theme folder? Or it also doesn't exist in my plugin?
                if($template === get_stylesheet_directory() . '/' . $template_name
                    || !file_exists($plugin_path . $template_name)) {

                    //Then return "single.php" or "single-my-custom-post-type.php" from theme directory.
                    return $template;
                }

                // If not, return my plugin custom post type template.
                return $plugin_path . $template_name;
            }

            //This is not my custom post type, do nothing with $template
            return $template;
        }
        
        public static function ds8_clientes_redirect(){
            //$id = get_the_ID();
            $pagina = (get_query_var( 'providencia' ) ? get_query_var( 'providencia' ) : get_query_var( 'prospecto') );
            
            if (!empty($pagina)) {
                $response = wp_remote_get( 'https://finanzasdigital.com/wp-json/wp/v2/posts?slug='.$pagina);
                $body_ds8     = json_decode(wp_remote_retrieve_body( $response ),true);
                if (empty($body_ds8)){
                  $GLOBALS['bodyd'] = false;
                  global $wp_query;
                  $wp_query->set_404();
                  status_header( 404 );
                  get_template_part( 404 );
                  exit();
                  //return;
                }else{
                  $bodyd = $body_ds8[0];
                  $GLOBALS['bodyd'] = $bodyd;
                }
            }
            //status_header(200);
        }
        
        public static function ds8_client_register_query_var($query_vars){
            $query_vars[] = 'orderc';
            $query_vars[] = 'providencia';
            $query_vars[] = 'prospecto';
            return $query_vars;
        }

        public function clientes_pagenum_link( $link ) {
          $link = filter_input( INPUT_GET, 'action' )
              ? remove_query_arg( 'action', $link )
              : $link;
          $link = filter_input( INPUT_GET, 'orderc' )
              ? remove_query_arg( 'orderc', $link )
              : $link;
          $link = filter_input( INPUT_GET, 'security' )
              ? remove_query_arg( 'security', $link )
              : $link;
          
          if (isset($_REQUEST['orderc'])){
              $link = add_query_arg('orderc', $_REQUEST['orderc'], $link);
          }
          
          return $link;
        }
        
        public static function ajax_render_users() {
          
            if (!check_ajax_referer('gl_authors_security_nonce', 'security')) {
              wp_send_json_error('Invalid security token sent.');
              wp_die();
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              extract($_POST);
            }else{
              extract($_GET);
            }
            
            $perpage = self::$perpage_;
            
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            if($paged==1){
              $offset=0;  
            }else {
              $offset= ($paged-1)*$perpage;
            }
            
            $params = array(  'role'   => 'Contributor',
                              'number' => $perpage, 
                              'offset' => $offset );
            
            if ($orderc == 'fecha'){
              $params['orderby'] ='post_date_gmt';
              $params['order'] = 'DESC';
            }else{
              $params['orderby'] = 'display_name';
              $params['order'] = 'ASC';
            }
            //$users = new WP_User_Query($params);
            $result = self::_prefix_get_users_by_post_date_v2($perpage, $params['orderby'], $params['order']);
            $users = $result[0];
            $total_users = $result[1];
          
            ob_start();
            include('template-parts/lista-clientes.php');
            $html = ob_get_contents();
            ob_end_clean();
            wp_send_json_success( array('page'=>$html) );
        }

        public function ds8_user_contactmethods($user_contactmethods){
            $user_contactmethods['twitter'] = 'URL del perfil de Twitter';
            $user_contactmethods['facebook'] = 'URL del perfil de Facebook';
            $user_contactmethods['instagram'] = 'URL del perfil de Instagram';
            return $user_contactmethods;
        }
        
        public function set_new_author_base(){
            global $wp_rewrite;
            $wp_rewrite->author_base = 'cliente';
        }
        
        public function ds8_authors_rewrite( $author_rewrite ) {
          
          $author_rewrite = array('cliente/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?author_name=$matches[1]&paged=$matches[2]',
                                  'cliente/([^/]+)/?$' => 'index.php?author_name=$matches[1]');
          
          return $author_rewrite;
        }
        
        public function ds8_authors_custom_number_of_posts( $query ) {
          
          if (is_admin() || !$query->is_main_query())
            return;

          if ( is_author() ) {
            $query->set('posts_per_page', 6);
            return;
          }
          
        }

  /**
        * Singleton pattern
        *
        * @return void
        */
        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }
        
        private function load_dependencies() {
            
            require_once DS8CLIENTES_PLUGIN_DIR . 'includes/class-ds8-columnist-helper.php';
            require_once DS8CLIENTES_PLUGIN_DIR . 'includes/class-ds8-cliente-widget.php';
          
            if (is_admin()) {
                require_once DS8CLIENTES_PLUGIN_DIR . 'includes/class-ds8-profile-user-image.php';
            }
        }
        
        /**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;
                add_rewrite_rule( "providencia/([a-z0-9-]+)[/]?$", 'index.php?providencia=$matches[1]', 'top' );
                add_rewrite_rule( "prospecto/([a-z0-9-]+)[/]?$", 'index.php?prospecto=$matches[1]', 'top' );
                add_action('template_redirect', array('DS8Clientes', 'ds8_clientes_redirect') );
        }
        
        public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}
        
        /**
          * Admin hooks
          *
          * @return void
          */
        private function define_admin_hooks() {
            add_filter('get_avatar', array($this, 'replace_gravatar_image'), 10, 6);
        }
        
        public function ds8_columnist_register_widget() {
          register_widget('DS8_Columnist_Widget');
        }

        /**
        * See this: https://codex.wordpress.org/Plugin_API/Filter_Reference/get_avatar
        *
        * Custom function to overwrite WordPress's get_avatar function
        *
        * @param [type] $avatar
        * @param [type] $id_or_email
        * @param [type] $size
        * @param [type] $default
        * @param [type] $alt
        * @param [type] $args
        *
        * @return void
        */
        public function replace_gravatar_image($avatar, $id_or_email, $size, $default, $alt, $args = array()) {
            // Process the user identifier.
            $user = false;
            if (is_numeric($id_or_email)) {
                $user = get_user_by('id', absint($id_or_email));
            } elseif (is_string($id_or_email)) {

                $user = get_user_by('email', $id_or_email);
            } elseif ($id_or_email instanceof WP_User) {
                // User Object
                $user = $id_or_email;
            } elseif ($id_or_email instanceof WP_Post) {
                // Post Object
                $user = get_user_by('id', (int) $id_or_email->post_author);
            } elseif ($id_or_email instanceof WP_Comment) {

                if (!empty($id_or_email->user_id)) {
                    $user = get_user_by('id', (int) $id_or_email->user_id);
                }
            }

            if (!$user || is_wp_error($user)) {
                return $avatar;
            }

            $custom_profile_image = get_user_meta($user->ID, 'ds8box-profile-image', true);
            $class                = array('avatar', 'avatar-' . (int) $args['size'], 'photo');

            if (!$args['found_avatar'] || $args['force_default']) {
                $class[] = 'avatar-default';
            }

            if ($args['class']) {
                if (is_array($args['class'])) {
                    $class = array_merge($class, $args['class']);
                } else {
                    $class[] = $args['class'];
                }
            }

            $class[] = 'sab-custom-avatar';

            if ('' !== $custom_profile_image && true !== $args['force_default']) {

                $avatar = sprintf(
                    "<img alt='%s' src='%s' srcset='%s' class='%s' height='%d' width='%d' %s/>",
                    esc_attr($args['alt']),
                    esc_url($custom_profile_image),
                    esc_url($custom_profile_image) . ' 2x',
                    esc_attr(join(' ', $class)),
                    (int) $args['height'],
                    (int) $args['width'],
                    $args['extra_attr']
                );
            }

            return $avatar;
        }
        
        /**
        * Enqueue a script in the WordPress admin user-edit.php.
        *
        * @param int $pagenow Hook suffix for the current admin page.
        */
        public function ds8_selectively_enqueue_admin_script( $hook ) {
             global $pagenow;
             if ($pagenow != 'user-edit.php') {
                 return;
             }
             wp_enqueue_media();
             wp_enqueue_script('media-upload');
             wp_enqueue_script('thickbox');
             wp_enqueue_style('thickbox');
             wp_register_script( 'profile-image', plugin_dir_url( __FILE__ ) .'/assets/js/profile-image.js', array('jquery-core'), false, true );
             wp_enqueue_script( 'profile-image' );
             
             wp_enqueue_style('ds8boxplugin-admin-style', plugin_dir_url( __FILE__ ) . '/assets/css/admin-opinion.css');
        }
        
        public function shortcode($atts) {
            $defaults = array(
                'ids' => '',
            );

            $atts = wp_parse_args($atts, $defaults);

            if ('' != $atts['ids']) {


                if ('all' != $atts['ids']) {
                    $ids = explode(',', $atts['ids']);
                } else {
                    $ids = get_users(array('fields' => 'ID'));
                }

                ob_start();
                $sabox_options = DS8_Columnist_Helper::get_option('saboxplugin_options');
                if (!empty($ids)) {
                    foreach ($ids as $user_id) {

                        $template        = DS8_Columnist_Helper::get_template();
                        $sabox_author_id = $user_id;
                        echo '<div class="sabox-plus-item">';
                        include($template);
                        echo '</div>';
                    }
                }

                $html = ob_get_clean();
            } else {
                $html = wpsabox_author_box_ds8();
            }

            return $html;
        }
        
        public static function _prefix_get_users_by_post_date_v2($perpage, $orderby = 'display_name', $order = 'ASC') {
          
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            if($paged==1){
              $offset=0;  
            }else {
              $offset= ($paged-1)*$perpage;
            }
            
            $users = self::get_users_by_last_post_date($offset, $perpage, $orderby, $order);
            return $users;
        }
        
        public static function get_users_by_last_post_date( $offset, $perpage, $orderby = 'post_date_gmt', $order = 'DESC', $post_type = 'post' ) {
            global $wpdb;
            $query = "SELECT COUNT(*)
                    FROM {$wpdb->users}
                    JOIN {$wpdb->usermeta} b
                    ON {$wpdb->users}.id = b.user_id
                    LEFT OUTER JOIN (
                        SELECT post_author, MAX(post_date_gmt) AS latest
                        FROM {$wpdb->posts} WHERE post_type='%s' AND post_status = 'publish'
                        GROUP BY post_author
                    ) conv ON {$wpdb->users}.id = conv.post_author 
                    LEFT OUTER JOIN {$wpdb->posts}
                      ON {$wpdb->posts}.post_author = conv.post_author
                      AND {$wpdb->posts}.post_date_gmt = conv.latest
                    WHERE b.meta_key = 'agl_capabilities' and b.meta_value like '%contributor%'
                    ORDER BY post_date_gmt DESC";
            $users_count = $wpdb->get_var( $query );
            
            $users = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT {$wpdb->users}.*
                    FROM {$wpdb->users}
                    JOIN {$wpdb->usermeta} b
                    ON {$wpdb->users}.id = b.user_id
                    LEFT OUTER JOIN (
                        SELECT post_author, MAX(post_date_gmt) AS latest
                        FROM {$wpdb->posts} WHERE post_type='%s' AND post_status = 'publish'
                        GROUP BY post_author
                    ) conv ON {$wpdb->users}.id = conv.post_author 
                    LEFT OUTER JOIN {$wpdb->posts}
                      ON {$wpdb->posts}.post_author = conv.post_author
                      AND {$wpdb->posts}.post_date_gmt = conv.latest
                    WHERE b.meta_key = 'agl_capabilities' and b.meta_value like '%contributor%'
                    GROUP BY ID
                    ORDER BY ${orderby} ${order}
                    LIMIT ${offset}, ${perpage} ",
                    $post_type
                )
            );

            return array($users,$users_count);
        }

        public static function get_custom_post_type_template( $archive_template ) {
             global $post;

             if ( is_post_type_archive ( 'cliente' ) ) {
                  $archive_template = dirname( __FILE__ ) . '/archive.php';
             }
             return $archive_template;
        }
        
        public static function load_author_template($template) {
            global $post;
            $author = get_user_by( 'slug', get_query_var( 'author_name' ) );
            $rol = in_array('contributor', $author->roles) ? 'contributor' : '';

            // if ($post->post_type == "post" && $rol === 'contributor'){
            if ($rol === 'contributor'){

                $plugin_path = plugin_dir_path( __FILE__ );
                $template_name = 'author.php';

                if($template === get_stylesheet_directory() . '/' . $template_name
                    || !file_exists($plugin_path . $template_name)) {

                    return $template;
                }
                return $plugin_path . $template_name;
            }

            return $template;
        }
        
        public static function load_single_template($template) {
            global $post;

            $author = get_userdata($post->post_author);
            $rol = in_array('contributor', $author->roles) ? 'contributor' : '';

            if ($post->post_type == "post" && $rol === 'contributor'){

                $plugin_path = plugin_dir_path( __FILE__ );
                $template_name = 'single.php';

                // A specific single template for my custom post type exists in theme folder? Or it also doesn't exist in my plugin?
                if($template === get_stylesheet_directory() . '/' . $template_name
                    || !file_exists($plugin_path . $template_name)) {

                    //Then return "single.php" or "single-my-custom-post-type.php" from theme directory.
                    return $template;
                }

                // If not, return my plugin custom post type template.
                return $plugin_path . $template_name;
            }

            return $template;
        }
        
        public static function load_cpt_template($template) {
            global $post;

            if ($post->post_type == "cliente"){
                $plugin_path = plugin_dir_path( __FILE__ );
                $template_name = 'singular.php';

                if($template === get_stylesheet_directory() . '/' . $template_name
                    || !file_exists($plugin_path . $template_name)) {

                    return $template;
                }
                return $plugin_path . $template_name;
            }
            return $template;
        }
        
        public function ds8cliente_shortcode_fn($atts) {
          
          if (is_admin()) return;
          
          extract( shortcode_atts( array(
              'type' => 'cliente',
              'perpage' => 4
          ), $atts ) );
          
          if (get_query_var('orderc') == 'fecha'){
              $params['orderby'] ='post_date_gmt';
              $params['order'] = 'DESC';
          }else{
            $params['orderby'] = 'display_name';
            $params['order'] = 'ASC';
          }

          $result = self::_prefix_get_users_by_post_date_v2($perpage, $params['orderby'], $params['order']);
          $users = $result[0];
          $total_users = $result[1];
          ob_start();
          include('template-parts/lista-clientes.php');
          return ob_get_clean();
        }
        
        /**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0
	 */
	private static function set_locale() {
		load_plugin_textdomain( 'ds8cliente', false, plugin_dir_path( dirname( __FILE__ ) ) . '/languages/' );

	}
        
        public static function ds8cliente_textdomain( $mofile, $domain ) {
                if ( 'ds8cliente' === $domain && false !== strpos( $mofile, WP_LANG_DIR . '/plugins/' ) ) {
                        $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
                        $mofile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $domain . '-' . $locale . '.mo';
                }
                return $mofile;
        }
        
        
        /**
	 * Check if plugin is active
	 *
	 * @since    1.0
	 */
	private static function is_plugin_active( $plugin_file ) {
		return in_array( $plugin_file, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}

        public function ds8_clientes_javascript(){
          
            wp_enqueue_style('cliente-css', plugin_dir_url( __FILE__ ) . 'assets/css/clientes.css', array(), DS8CLIENTES_VERSION);
            //wp_enqueue_style('bootstrap-css', plugin_dir_url( __FILE__ ) . 'assets/css/bootstrap.min.css', array(), DS8CLIENTES_VERSION);
            //wp_enqueue_style('bootstrap-theme-css', plugin_dir_url( __FILE__ ) . 'assets/css/bootstrap-theme.css', array(), DS8CLIENTES_VERSION);
            
            //wp_register_script( 'tabs.js', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap.js', array('jquery'), DS8CLIENTES_VERSION, true );
            //wp_enqueue_script( 'tabs.js' );
            //wp_enqueue_script( 'popper', 'https://unpkg.com/@popperjs/core@2', array('jquery'), DS8CLIENTES_VERSION, true );
            wp_enqueue_script( 'popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array('jquery'), DS8CLIENTES_VERSION, true );
            
            wp_register_script( 'clientes.js', plugin_dir_url( __FILE__ ) . 'assets/js/clientes.js', array('bootstrap-js'), DS8CLIENTES_VERSION, true );
            $localize_script_args = array(
                'ajaxurl'         => admin_url('admin-ajax.php'),
                'security'        => wp_create_nonce( 'gl_authors_security_nonce' )
            );
            wp_localize_script('clientes.js', 'clientes', $localize_script_args );
            wp_enqueue_script('clientes.js');

        }

        public static function view( $name, array $args = array() ) {
                $args = apply_filters( 'ds8cliente_view_arguments', $args, $name );

                foreach ( $args AS $key => $val ) {
                        $$key = $val;
                }

                load_plugin_textdomain( 'ds8cliente' );

                $file = DS8CLIENTES_PLUGIN_DIR . 'views/'. $name . '.php';

                include( $file );
	}
        
        public static function plugin_deactivation( ) {
            unregister_post_type( 'calendar' );
            flush_rewrite_rules();
        }

        /**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {
		if ( version_compare( $GLOBALS['wp_version'], DS8CLIENTES_MINIMUM_WP_VERSION, '<' ) ) {
			load_plugin_textdomain( 'ds8cliente' );
                        
			$message = '<strong>'.sprintf(esc_html__( 'FD Estadisticas %s requires WordPress %s or higher.' , 'ds8cliente'), DS8CLIENTES_VERSION, DS8CLIENTES_MINIMUM_WP_VERSION ).'</strong> '.sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Akismet plugin</a>.', 'ds8cliente'), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/ds8cliente/download/');

			DS8Clientes::bail_on_activation( $message );
		} elseif ( ! empty( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( $_SERVER['SCRIPT_NAME'], '/wp-admin/plugins.php' ) ) {
                        flush_rewrite_rules();
			add_option( 'Activated_DS8Clientes', true );
		}
	}

        private static function bail_on_activation( $message, $deactivate = true ) {
?>
<!doctype html>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<style>
* {
	text-align: center;
	margin: 0;
	padding: 0;
	font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
}
p {
	margin-top: 1em;
	font-size: 18px;
}
</style>
</head>
<body>
<p><?php echo esc_html( $message ); ?></p>
</body>
</html>
<?php
		if ( $deactivate ) {
			$plugins = get_option( 'active_plugins' );
			$ds8cliente = plugin_basename( DS8CALENDAR__PLUGIN_DIR . 'ds8cliente.php' );
			$update  = false;
			foreach ( $plugins as $i => $plugin ) {
				if ( $plugin === $ds8cliente ) {
					$plugins[$i] = false;
					$update = true;
				}
			}

			if ( $update ) {
				update_option( 'active_plugins', array_filter( $plugins ) );
			}
		}
		exit;
	}

}