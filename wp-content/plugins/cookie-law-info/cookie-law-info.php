<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.webtoffee.com/product/gdpr-cookie-consent/
 * @since             1.6.6
 * @package           Cookie_Law_Info
 *
 * @wordpress-plugin
 * Plugin Name:       GDPR Cookie Consent
 * Plugin URI:        https://www.webtoffee.com/product/gdpr-cookie-consent/
 * Description:       A simple way to show your website complies with the EU Cookie Law / GDPR.
 * Version:           1.7.6
 * Author:            WebToffee
 * Author URI:        http://cookielawinfo.com/
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       cookie-law-info
 * Domain Path:       /languages
 */

/*	
    Copyright 2018  WebToffee

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define ( 'CLI_PLUGIN_DEVELOPMENT_MODE', false );
define ( 'CLI_PLUGIN_BASENAME', plugin_basename(__FILE__) );
define ( 'CLI_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define ( 'CLI_PLUGIN_URL', plugin_dir_url(__FILE__));
define ( 'CLI_DB_KEY_PREFIX', 'CookieLawInfo-' );
define ( 'CLI_LATEST_VERSION_NUMBER', '0.9' );
define ( 'CLI_SETTINGS_FIELD', CLI_DB_KEY_PREFIX . CLI_LATEST_VERSION_NUMBER );
define ( 'CLI_MIGRATED_VERSION', CLI_DB_KEY_PREFIX . 'MigratedVersion' );
// Previous version settings (depreciated from 0.9 onwards):
define ( 'CLI_ADMIN_OPTIONS_NAME', 'CookieLawInfo-0.8.3' );
define ( 'CLI_PLUGIN_FILENAME',__FILE__);
define ( 'CLI_POST_TYPE','cookielawinfo');
/**
 * Currently plugin version.
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CLI_VERSION', '1.7.6' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cookie-law-info-activator.php
 */
function activate_cookie_law_info() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cookie-law-info-activator.php';
	Cookie_Law_Info_Activator::activate();
    register_uninstall_hook( __FILE__, 'uninstall_cookie_law_info' );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cookie-law-info-deactivator.php
 */
function deactivate_cookie_law_info() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cookie-law-info-deactivator.php';
	Cookie_Law_Info_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_cookie_law_info' );
register_deactivation_hook( __FILE__, 'deactivate_cookie_law_info' );


function uninstall_cookie_law_info()
{
    // Bye bye settings:
    delete_option( CLI_ADMIN_OPTIONS_NAME );
    delete_option( CLI_MIGRATED_VERSION );
    delete_option( CLI_SETTINGS_FIELD );
    
    // Bye bye custom meta:
    $args = array('post_type' => 'cookielawinfo');
    $posts = get_posts($args);    
    if (!$posts) 
    {
        return;
    }       
    if($posts)
    {
        foreach($posts as $post)
        {
            $custom = get_post_custom( $post->ID );
            // Look for old values. If they exist, move them to new values then delete old values:
            if ( isset ( $custom["cookie_type"][0] ) ) 
            {
                delete_post_meta( $post->ID, "cookie_type", $custom["cookie_type"][0] );
            }
            if ( isset ( $custom["cookie_duration"][0] ) ) 
            {
                delete_post_meta( $post->ID, "cookie_duration", $custom["cookie_duration"][0] );
            }
            if ( isset ( $custom["_cli_cookie_type"][0] ) ) 
            {
                delete_post_meta( $post->ID, "_cli_cookie_type", $custom["_cli_cookie_type"][0] );
            }
            if(isset( $custom["_cli_cookie_duration"][0] ) ) 
            {
                delete_post_meta( $post->ID, "_cli_cookie_duration", $custom["_cli_cookie_duration"][0] );
            }               
        }
    }
}



/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cookie-law-info.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.6.6
 */
function run_cookie_law_info() {

	$plugin = new Cookie_Law_Info();
	$plugin->run();
}
run_cookie_law_info();
