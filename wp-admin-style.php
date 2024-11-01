<?php

/*
  Plugin Name: WP Admin Style
  Description: Adds styles to wp-admin pages. Most popular Black with Pink style included.
	Version: 0.1.2
  Author: Alex Egorov
	Author URI: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SLHFMF373Z9GG&source=url
	Plugin URI: https://wordpress.org/plugins/wp-admin-style/
  GitHub Plugin URI:
  License: GPLv2 or later (license.txt)
  Text Domain: wpas
  Domain Path: /languages
*/
global $wpas;
$wpas = get_option('wpas');
//error_reporting(E_ALL);
define('WPAS_URL', plugins_url( '/', __FILE__ ) );
define('WPAS_PATH', plugin_dir_path(__FILE__) );
define('WPAS_PREF', $wpdb->base_prefix.'n_' );

// Async load
if (!function_exists('async_scripts')){
  function async_scripts($url) {
      if ( strpos( $url, '#async') === false )
          return $url;
      else if ( is_admin() )
          return str_replace( '#async', '', $url );
      else
      return str_replace( '#async', '', $url )."' async='async";
  }
  add_filter( 'clean_url', 'async_scripts', 11, 1 );
}

require WPAS_PATH.'/includes/admin.php';
// include_once 'includes/shortcodes.php';
// include_once 'includes/widget.php';


  // wp_enqueue_style( 'snow' , wpas_URL.'includes/css/snow.min.css');
  // add_action( 'admin_enqueue_scripts', 'load_admin_styles' );
  // add_action('admin_footer','wpas_options');
  // add_action('admin_header','wpas_options');

  //   //Second solution : two or more files.
  //   add_action( 'admin_enqueue_scripts', 'load_admin_styles' );
  //   function load_admin_styles() {
  //     wp_enqueue_style( 'admin_css_foo', get_template_directory_uri() . '/admin-style-foo.css', false, '1.0.0' );
  //     wp_enqueue_style( 'admin_css_bar', get_template_directory_uri() . '/admin-style-bar.css', false, '1.0.0' );
  //   }

  // $mobile = wp_is_mobile() ? true : null;
  // if( !$mobile ) {
  //   wp_enqueue_script( 'wpas-'.$this->place.'-scripts', wpas_UPLOAD_URL.'js/'.$filename,$parents,VER_RCL,$in_footer);
  // }

  add_action('admin_enqueue_scripts', 'wpas_scripts');
  function wpas_scripts(){
    global $wpas;

    if( $wpas['style'] == 'yummi' ){
      wp_enqueue_style( 'yummi', WPAS_URL . '/includes/css/admin_style.min.css' );
      wp_enqueue_style( 'yummi-hint', WPAS_URL . '/includes/css/hint.min.css' );
    }
  }

  add_action('admin_footer','wpas_header');
  function wpas_header(){
    global $wpas;

    $mcss = '';
    if( is_array($wpas['mcss']) ){
      for ($i=0; $i < count($wpas['mcss']); $i++) {
        $mcss .= $wpas['mcss'];
      }
    }
    echo '<style>'.$mcss.$wpas['css'].'</style>'; // <script type="text/javascript">alert("yep!");</script>
  }

/* Multiplugin functions */
register_activation_hook(__FILE__, 'wpas_activation');
function wpas_activation() {}
register_deactivation_hook( __FILE__, 'wpas_deactivation' );
function wpas_deactivation() {}

register_uninstall_hook( __FILE__, 'wpas_uninstall' );
function wpas_uninstall() {}

add_filter('plugin_action_links', 'wpas_plugin_action_links', 10, 2);
function wpas_plugin_action_links($links, $file) {
    static $this_plugin;
    if (!$this_plugin)
        $this_plugin = plugin_basename(__FILE__);

    if ($file == $this_plugin) { // check to make sure we are on the correct plugin
			//$settings_link = '<a href="https://yummi.club/" target="_blank">' . __('Demo', 'wpas') . '</a> | ';
			$settings_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SLHFMF373Z9GG&source=url" target="_blank">❤ ' . __('Donate', 'wpas') . '</a> | <a href="admin.php?page=wpas">' . __('Settings') . '</a>'; // the anchor tag and href to the URL we want. For a "Settings" link, this needs to be the url of your settings page

      array_unshift($links, $settings_link); // add the link to the list
    }
    return $links;
}
/* /Multiplugin functions */
