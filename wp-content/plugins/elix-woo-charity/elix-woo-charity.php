<?php

/**
 * The woo-charity plugin.
 *
 * @link              https://elixinol.com
 * @since             1.0.0
 * @package           Elix_woo_charity
 *
 * @wordpress-plugin
 * Plugin Name:       Elix Woo Charity
 * Plugin URI:        https://elixinol.com
 * Description:       Add charity field to orders.
 * Version:           1.0.0
 * Author:            Zvi Epner
 * Author URI:        https://elixinol.com
 * Text Domain:       elix_woo_charity
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'EW_CHARITY_VERSION', '1.0.0' );

require plugin_dir_path( __FILE__ ) . 'includes/class-elix-woo-charity.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_elix_woo_charity() {

	$plugin = new Elix_woo_charity();
	$plugin->run();

}
run_elix_woo_charity();
