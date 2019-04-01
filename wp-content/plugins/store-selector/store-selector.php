<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://elixinol.com/
 * @since             1.0.0
 * @package           Store_Selector
 *
 * @wordpress-plugin
 * Plugin Name:       Store Selector
 * Plugin URI:        https://elixinol.com/
 * Description:       Displays a store selector dialog to visitors for which a closer local Elixinol website exists.
 * Version:           1.0.0
 * Author:            Christopher Cook
 * Author URI:        https://elixinol.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       store-selector
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'STORE_SELECTOR_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-store-selector-activator.php
 */
function activate_store_selector() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-store-selector-activator.php';
  Store_Selector_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-store-selector-deactivator.php
 */
function deactivate_store_selector() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-store-selector-deactivator.php';
  Store_Selector_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_location_helper' );
register_deactivation_hook( __FILE__, 'deactivate_location_helper' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-store-selector.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_store_selector() {

  $plugin = new Store_Selector();
  $plugin->run();

}
run_store_selector();
