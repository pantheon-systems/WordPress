<?php
/**
 * Plugin Name: Advanced Order Export For WooCommerce
 * Plugin URI:
 * Description: Export orders from WooCommerce with ease (Excel/CSV/XML/JSON supported)
 * Author: AlgolPlus
 * Author URI: https://algolplus.com/
 * Version: 2.1.1
 * Text Domain: woo-order-export-lite
 * Domain Path: /i18n/languages/
 * WC requires at least: 2.6.0
 * WC tested up to: 3.5.0
 *
 * Copyright: (c) 2015 AlgolPlus LLC. (algol.plus@gmail.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     woo-order-export-lite
 * @author      AlgolPlus LLC
 * @Category    Plugin
 * @copyright   Copyright (c) 2015 AlgolPlus LLC
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// a small function to check startup conditions 
if ( ! function_exists( "woe_check_running_options" ) ) {
	function woe_check_running_options() {

		$is_backend           = is_admin();
		return $is_backend;
	}
}

if ( ! woe_check_running_options() ) {
	return;
} //don't load for frontend !

//Stop if another version is active!
if ( class_exists( 'WC_Order_Export_Admin' ) ) {
	if ( ! function_exists( 'woe_warn_free_admin' ) ) {
		function woe_warn_free_admin() {
			?>
            <div class="notice notice-warning is-dismissible">
                <p><?php _e( 'Please, <a href="plugins.php">deactivate</a> Free version of Advanced Order Export For WooCommerce!',
						'woo-order-export-lite' ); ?></p>
            </div>
			<?php
		}
	}
	add_action( 'admin_notices', 'woe_warn_free_admin' );

	return;
}


include 'classes/class-wc-order-export-admin.php';
include 'classes/admin/class-wc-order-export-ajax.php';
include 'classes/admin/class-wc-order-export-manage.php';
include 'classes/admin/class-wc-order-export-labels.php';
include 'classes/core/class-wc-order-export-engine.php';
include 'classes/core/class-wc-order-export-data-extractor.php';
include 'classes/core/class-wc-order-export-data-extractor-ui.php';

define( 'WOE_VERSION', '2.1.1' );
define( 'WOE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WOE_PLUGIN_BASEPATH', dirname( __FILE__ ) );
$wc_order_export = new WC_Order_Export_Admin();
register_activation_hook( __FILE__, array( $wc_order_export, 'install' ) );
register_deactivation_hook( __FILE__, array( $wc_order_export, 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'WC_Order_Export_Admin', 'uninstall' ) );

// fight with ugly themes which add empty lines
if ( $wc_order_export->must_run_ajax_methods() AND ! ob_get_level() ) {
	ob_start();
}
//Done