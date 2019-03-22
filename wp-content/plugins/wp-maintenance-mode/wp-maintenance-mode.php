<?php

/**
 * WP Maintenance Mode
 *
 * Plugin Name: WP Maintenance Mode
 * Plugin URI: https://designmodo.com/
 * Description: Adds a splash page to your site that lets visitors know your site is down for maintenance. It's perfect for a coming soon page.
 * Version: 2.2.3
 * Author: Designmodo
 * Author URI: https://designmodo.com/
 * Twitter: designmodo
 * GitHub Plugin URI: https://github.com/Designmodocom/WP-Maintenance-Mode
 * GitHub Branch: master
 * Text Domain: wp-maintenance-mode
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
	exit;

/**
 * DEFINE PATHS
 */
define('WPMM_PATH', plugin_dir_path(__FILE__));
define('WPMM_CLASSES_PATH', WPMM_PATH . 'includes/classes/');
define('WPMM_FUNCTIONS_PATH', WPMM_PATH . 'includes/functions/');
define('WPMM_LANGUAGES_PATH', basename(WPMM_PATH) . '/languages/');
define('WPMM_VIEWS_PATH', WPMM_PATH . 'views/');
define('WPMM_CSS_PATH', WPMM_PATH . 'assets/css/');

/**
 * DEFINE URLS
 */
define('WPMM_URL', plugin_dir_url(__FILE__));
define('WPMM_JS_URL', WPMM_URL . 'assets/js/');
define('WPMM_CSS_URL', WPMM_URL . 'assets/css/');
define('WPMM_IMAGES_URL', WPMM_URL . 'assets/images/');
define('WPMM_AUTHOR_UTM', '?utm_source=wpplugin&utm_medium=wpmaintenance');

/**
 * OTHER DEFINES
 */
define('WPMM_ASSETS_SUFFIX', (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min');

/**
 * FUNCTIONS
 */
require_once(WPMM_FUNCTIONS_PATH . 'hooks.php');
require_once(WPMM_FUNCTIONS_PATH . 'helpers.php');
if (is_multisite() && !function_exists('is_plugin_active_for_network')) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

/**
 * FRONTEND
 */
require_once(WPMM_CLASSES_PATH . 'wp-maintenance-mode-shortcodes.php');
require_once(WPMM_CLASSES_PATH . 'wp-maintenance-mode.php');
register_activation_hook(__FILE__, array('WP_Maintenance_Mode', 'activate'));
register_deactivation_hook(__FILE__, array('WP_Maintenance_Mode', 'deactivate'));

add_action('plugins_loaded', array('WP_Maintenance_Mode', 'get_instance'));

/**
 * DASHBOARD
 */
if (is_admin()) {
	require_once(WPMM_CLASSES_PATH . 'wp-maintenance-mode-admin.php');
	add_action('plugins_loaded', array('WP_Maintenance_Mode_Admin', 'get_instance'));
}