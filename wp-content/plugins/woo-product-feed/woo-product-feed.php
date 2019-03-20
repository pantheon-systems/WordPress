<?php

/**
 * The plugin bootstrap file
 *
 * Produces an XML feed for WooCommerce products at /feed/products/
 *
 * @link              https://elixinol.com/
 * @since             1.0.0
 * @package           Woo_Product_Feed
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Product Feed
 * Plugin URI:        https://elixinol.com/
 * Description:       Produces an XML feed for WooCommerce products.
 * Version:           1.0.3
 * Author:            Christopher Cook
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * Current plugin version.
 */
define( 'WOO_PRODUCT_FEED_VERSION', '1.0.3' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-product-feed.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_product_feed() {

  $plugin = new Woo_Product_Feed();
  $plugin->run();

}
run_woo_product_feed();
