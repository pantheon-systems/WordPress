<?php
/*
 Plugin Name: N-Media WooCommerce PPOM
Plugin URI: http://najeebmedia.com/wordpress-plugin/woocommerce-personalized-product-option/
Description: PPOM (Personalized Product Meta Manager) plugin allow WooCommerce Store Admin to create unlimited input fields and files to attach with Product Page
Version: 17.0
Author: Najeeb Ahmad
Text Domain: ppom
Domain Path: /languages
WC requires at least: 3.0.0
WC tested up to: 3.5.0
Author URI: http://www.najeebmedia.com/
*/

// @since 6.1
if( ! defined('ABSPATH' ) ){
	exit;
}

define('PPOM_PATH', untrailingslashit(plugin_dir_path( __FILE__ )) );
define('PPOM_URL', untrailingslashit(plugin_dir_url( __FILE__ )) );
define('PPOM_WP_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __DIR__ ) ));
define('PPOM_VERSION', '16.9');
define('PPOM_DB_VERSION', '16.9');
define("PPOM_PRODUCT_META_KEY", '_product_meta_id');
define('PPOM_TABLE_META', 'nm_personalized');
define('PPOM_UPLOAD_DIR_NAME', 'ppom_files');

/*
 * plugin localization being initiated here
 */
add_action ( 'init', 'ppom_i18n_setup');
function ppom_i18n_setup() {
	
	$loadedok = load_plugin_textdomain('ppom', false, basename( dirname( __FILE__ ) ) . '/languages');
}


include_once PPOM_PATH . "/inc/functions.php";
include_once PPOM_PATH . "/inc/deprecated.php";
include_once PPOM_PATH . "/inc/arrays.php";
include_once PPOM_PATH . "/inc/hooks.php";
include_once PPOM_PATH . "/inc/woocommerce.php";
include_once PPOM_PATH . "/inc/admin.php";
include_once PPOM_PATH . "/inc/files.php";
include_once PPOM_PATH . "/inc/nmInput.class.php";
include_once PPOM_PATH . "/inc/rest.class.php";


/* ======= For now we are including class file, we will replace  =========== */
// include_once PPOM_PATH . "/classes/nm-framework.php";
include_once PPOM_PATH . "/classes/input.class.php";
include_once PPOM_PATH . "/classes/fields.class.php";
include_once PPOM_PATH . "/classes/ppom.class.php";
include_once PPOM_PATH . "/classes/plugin.class.php";

if( is_admin() ){

	include_once PPOM_PATH . "/classes/admin.class.php";

	$ppom_admin = new NM_PersonalizedProduct_Admin();
	
	$ppom_basename = plugin_basename( __FILE__ );
	add_filter( "plugin_action_links_{$ppom_basename}", 'ppom_settings_link');
}



// ==================== INITIALIZE PLUGIN CLASS =======================
//
add_action('woocommerce_init', 'PPOM');
//
// ==================== INITIALIZE PLUGIN CLASS =======================

function PPOM(){
	return NM_PersonalizedProduct::get_instance();
}

/*
 * activation/install the plugin data
*/
register_activation_hook( __FILE__, array('NM_PersonalizedProduct', 'activate_plugin'));
register_deactivation_hook( __FILE__, array('NM_PersonalizedProduct', 'deactivate_plugin'));