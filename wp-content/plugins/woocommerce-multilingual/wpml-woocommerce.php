<?php
/*
  Plugin Name: WooCommerce Multilingual
  Plugin URI: http://wpml.org/documentation/related-projects/woocommerce-multilingual/
  Description: Allows running fully multilingual e-Commerce sites with WooCommerce and WPML. <a href="http://wpml.org/documentation/related-projects/woocommerce-multilingual/">Documentation</a>.
  Author: OnTheGoSystems
  Author URI: http://www.onthegosystems.com/
  Text Domain: woocommerce-multilingual
  Requires at least: 3.9
  Tested up to: 4.9.8
  Version: 4.3.7
  WC requires at least: 2.1.0
  WC tested up to: 3.5
*/

if ( defined( 'WCML_VERSION' ) ) {
	return;
}

define( 'WCML_VERSION', '4.3.7' );
define( 'WCML_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'WCML_PLUGIN_FOLDER', basename( WCML_PLUGIN_PATH ) );
define( 'WCML_LOCALE_PATH', WCML_PLUGIN_PATH . '/locale' );
define( 'WPML_LOAD_API_SUPPORT', true );
define( 'WCML_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

include WCML_PLUGIN_PATH . '/inc/constants.php';
require WCML_PLUGIN_PATH . '/inc/missing-php-functions.php';
include WCML_PLUGIN_PATH . '/inc/installer-loader.php';
include WCML_PLUGIN_PATH . '/inc/wcml-core-functions.php';
include WCML_PLUGIN_PATH . '/inc/wcml-switch-lang-request.php';

if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
	require WCML_PLUGIN_PATH . '/vendor/autoload.php';
} else {
	require WCML_PLUGIN_PATH . '/vendor/autoload_52.php';
}

if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
	global $sitepress;
	//detecting language switching
	$wcml_switch_lang_request = new WCML_Switch_Lang_Request( new WPML_Cookie(), new WPML_WP_API(), $sitepress );
	$wcml_switch_lang_request->add_hooks();

	//cart related language switching functions
	$wcml_cart_switch_lang_functions = new WCML_Cart_Switch_Lang_Functions();
	$wcml_cart_switch_lang_functions->add_actions();
}

// Load WooCommerce Multilingual when WPML is active
global $woocommerce_wpml;
$woocommerce_wpml = new woocommerce_wpml();
$woocommerce_wpml->add_hooks();

add_action( 'wpml_loaded', 'wcml_loader' );
/**
 * Load WooCommerce Multilingual after WPML is loaded
 */
function wcml_loader(){
	$xdomain_data = new WCML_xDomain_Data( new WPML_Cookie() );
	$xdomain_data->add_hooks();

	$loaders = array(
		'WCML_Privacy_Content_Factory'
	);

	if (
		( defined( 'ICL_SITEPRESS_VERSION' ) && defined( 'WPML_MEDIA_VERSION' ) )
		|| ( defined( 'ICL_SITEPRESS_VERSION' )
		     && version_compare( ICL_SITEPRESS_VERSION, '4.0.0', '>=' )
		     && version_compare( ICL_SITEPRESS_VERSION, '4.0.4', '<' )
		     && ! defined( 'WPML_MEDIA_VERSION' )
		)
	) {
		$loaders[] = 'WCML_Product_Image_Filter_Factory';
		$loaders[] = 'WCML_Product_Gallery_Filter_Factory';
		$loaders[] = 'WCML_Update_Product_Gallery_Translation_Factory';
		$loaders[] = 'WCML_Append_Gallery_To_Post_Media_Ids_Factory';
	}

	if( version_compare( ICL_SITEPRESS_VERSION, '3.9.0', '>=' ) ){
		$action_filter_loader = new WPML_Action_Filter_Loader();
		$action_filter_loader->load( $loaders );
	}
}

$WCML_REST_API = new WCML_REST_API();
if( $WCML_REST_API->is_rest_api_request() ){
	add_action( 'wpml_before_init', array( $WCML_REST_API, 'remove_wpml_global_url_filters' ), 0 );
}

// Load WooCommerce Multilingual when WPML is NOT active
add_action( 'plugins_loaded', 'load_wcml_without_wpml', 10000 );
function load_wcml_without_wpml() {
	if ( ! did_action( 'wpml_loaded' ) ) {
		global $woocommerce_wpml;
		$woocommerce_wpml = new woocommerce_wpml();
	}
}

