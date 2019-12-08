<?php
/**
 * Plugin initialization file
 *
 * @package   Google\Site_Kit_Dev_Settings
 * @copyright 2019 Google LLC
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://sitekit.withgoogle.com
 *
 * @wordpress-plugin
 * Plugin Name: Site Kit by Google Dev Settings
 * Plugin URI:  https://sitekit.withgoogle.com
 * Description: Development utility adding an advanced Settings screen for Site Kit plugin configuration.
 * Version:     0.3.0
 * Author:      Google
 * Author URI:  https://opensource.google.com/
 * License:     Apache License 2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0
 * Text Domain: google-site-kit-dev-settings
 */

/* This file must be parsable by PHP 5.2. */

/**
 * Loads the plugin.
 *
 * @since 0.1.0
 * @access private
 */
function googlesitekit_dev_settings_load() {
	if ( ! defined( 'GOOGLESITEKIT_PLUGIN_BASENAME' ) ) {
		return;
	}

	define( 'GOOGLESITEKITDEVSETTINGS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
	define( 'GOOGLESITEKITDEVSETTINGS_VERSION', '0.3.0' );

	$src_dir = plugin_dir_path( __FILE__ ) . 'src/';

	require_once $src_dir . 'Plugin.php';
	require_once $src_dir . 'Setting.php';
	require_once $src_dir . 'Hooks.php';
	require_once $src_dir . 'Updater.php';
	require_once $src_dir . 'Admin/Settings_Screen.php';

	call_user_func( array( 'Google\\Site_Kit_Dev_Settings\\Plugin', 'load' ), __FILE__ );
}
add_action( 'plugins_loaded', 'googlesitekit_dev_settings_load' );
