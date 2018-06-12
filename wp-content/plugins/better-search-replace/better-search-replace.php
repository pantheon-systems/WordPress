<?php
/**
 * Better Search Replace
 *
 * This plugin improves upon the database search/replace functionality offered
 * by some other plugins- offering serialization support, the ability to
 * select specific tables, and the ability to run a dry run.
 *
 * @since             1.0.0
 * @package           Better_Search_Replace
 *
 * @wordpress-plugin
 * Plugin Name:       Better Search Replace
 * Plugin URI:        https://bettersearchreplace.com
 * Description:       A small plugin for running a search/replace on your WordPress database.
 * Version:           1.3.2
 * Author:            Delicious Brains
 * Author URI:        https://bettersearchreplace.com
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       better-search-replace
 * Domain Path:       /languages
 * Network:			  true
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// If this file was called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_better_search_replace() {

	// Allows for overriding the capability required to run the plugin.
	$cap = apply_filters( 'bsr_capability', 'install_plugins' );

	// Only load for admins.
	if ( current_user_can( $cap ) ) {

		// Defines the path to the main plugin file.
		define( 'BSR_FILE', __FILE__ );

		// Defines the path to be used for includes.
		define( 'BSR_PATH', plugin_dir_path( BSR_FILE ) );

		// Defines the URL to the plugin.
		define( 'BSR_URL', plugin_dir_url( BSR_FILE ) );

		// Defines the current version of the plugin.
		define( 'BSR_VERSION', '1.3.2' );

		/**
		 * The core plugin class that is used to define internationalization,
		 * dashboard-specific hooks, and public-facing site hooks.
		 */
		require BSR_PATH . 'includes/class-bsr-main.php';
		$plugin = new Better_Search_Replace();
		$plugin->run();

	}

}
add_action( 'after_setup_theme', 'run_better_search_replace' );
