<?php

/**
 * The woo-charity plugin.
 *
 * @link              https://elixinol.com
 * @since             1.0.0
 * @package           Elix_Woo_Charity
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Charity Field
 * Plugin URI:        https://elixinol.com
 * Description:       Add charity field to orders.
 * Version:           1.0.2
 * Author:            Zvi Epner
 * Author URI:        https://elixinol.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       elix-woo-charity
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'ELIX_WOO_CHARITY_VERSION', '1.0.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-elix-woo-charity-activator.php
 */
function activate_elix_woo_charity() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-elix-woo-charity-activator.php';
	Elix_Woo_Charity_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-elix-woo-charity-deactivator.php
 */
function deactivate_elix_woo_charity() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-elix-woo-charity-deactivator.php';
	Elix_Woo_Charity_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_elix_woo_charity' );
register_deactivation_hook( __FILE__, 'deactivate_elix_woo_charity' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-elix-woo-charity.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_elix_woo_charity() {

	$plugin = new Elix_Woo_Charity();
	$plugin->run();

}
run_elix_woo_charity();
