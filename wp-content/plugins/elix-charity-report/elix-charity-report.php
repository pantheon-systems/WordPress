<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://elixinol.com
 * @since             1.0.0
 * @package           Elix_Charity_Report
 *
 * @wordpress-plugin
 * Plugin Name:       Elix Charity Report
 * Plugin URI:        https://elixinol.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Zvi Epner
 * Author URI:        https://elixinol.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       elix-charity-report
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ELIX_CHARITY_REPORT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-elix-charity-report-activator.php
 */
function activate_elix_charity_report() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-elix-charity-report-activator.php';
	Elix_Charity_Report_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-elix-charity-report-deactivator.php
 */
function deactivate_elix_charity_report() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-elix-charity-report-deactivator.php';
	Elix_Charity_Report_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_elix_charity_report' );
register_deactivation_hook( __FILE__, 'deactivate_elix_charity_report' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-elix-charity-report.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_elix_charity_report() {

	$plugin = new Elix_Charity_Report();
	$plugin->run();

}
run_elix_charity_report();
