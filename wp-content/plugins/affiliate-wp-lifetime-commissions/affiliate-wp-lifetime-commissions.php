<?php
/**
 * Plugin Name: AffiliateWP - Lifetime Commissions
 * Plugin URI: http://affiliatewp.com/addons/lifetime-commissions/
 * Description: Allow your affiliates to receive a commission on all future purchases by the customer
 * Author: Pippin Williamson and Andrew Munro
 * Author URI: http://affiliatewp.com
 * Version: 1.2.5
 * Text Domain: affiliate-wp-lifetime-commissions
 * Domain Path: languages
 *
 * AffiliateWP is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * AffiliateWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AffiliateWP. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package AffiliateWP Lifetime Commissions
 * @category Core
 * @author Andrew Munro
 * @version 1.2.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

final class AffiliateWP_Lifetime_Commissions {

	/** Singleton *************************************************************/

	/**
	 * @var AffiliateWP_Lifetime_Commissions The one true AffiliateWP_Lifetime_Commissions
	 * @since 1.0
	 */
	private static $instance;

	private static $plugin_dir;
	private static $version;

	/**
	 * The integrations handler instance variable
	 *
	 * @var Affiliate_WP_Lifetime_Commissions_Base
	 * @since 1.0
	 */
	public $integrations;

	/**
	 * Main AffiliateWP_Lifetime_Commissions Instance
	 *
	 * Insures that only one instance of AffiliateWP_Lifetime_Commissions exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @return AffiliateWP_Lifetime_Commissions The one true AffiliateWP_Lifetime_Commissions
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Lifetime_Commissions ) ) {
			self::$instance = new AffiliateWP_Lifetime_Commissions;

			self::$plugin_dir = plugin_dir_path( __FILE__ );
			self::$version    = '1.2.5';

			self::$instance->load_textdomain();
			self::$instance->includes();
			self::$instance->init();
			self::$instance->hooks();

			self::$instance->integrations = new Affiliate_WP_Lifetime_Commissions_Base;

		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp-lifetime-commissions' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp-lifetime-commissions' ), '1.0' );
	}

	/**
	 * Loads the plugin language files
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$lang_dir = apply_filters( 'aff_wp_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliate-wp-lifetime-commissions' );
		$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliate-wp-lifetime-commissions', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/affiliate-wp-lifetime-commissions/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/affiliate-wp-lifetime-commissions/ folder
			load_textdomain( 'affiliate-wp-lifetime-commissions', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/affiliate-wp-lifetime-commissions/languages/ folder
			load_textdomain( 'affiliate-wp-lifetime-commissions', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'affiliate-wp-lifetime-commissions', false, $lang_dir );
		}
	}

	/**
	 * Include required files
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {

		if ( is_admin() ) {
			require_once self::$plugin_dir . 'includes/class-admin.php';
		}

		require_once self::$plugin_dir . 'integrations/class-base.php';

		// Load the class for each integration enabled
		foreach ( affiliate_wp()->integrations->get_enabled_integrations() as $filename => $integration ) {

			if ( file_exists( self::$plugin_dir . 'integrations/class-' . $filename . '.php' ) ) {
				require_once self::$plugin_dir . 'integrations/class-' . $filename . '.php';
			}

		}

	}

	/**
	 * Init
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function init() {

		if ( is_admin() ) {
			self::$instance->updater();
		}

	}

	/**
	 * Setup the default hooks and actions
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function hooks() {
		// Forces the was_referred() function to return true so our referral can still be created
		add_filter( 'affwp_was_referred', '__return_true' );
	}

	/**
	 * Load the custom plugin updater
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	public function updater() {

		if ( class_exists( 'AffWP_AddOn_Updater' ) ) {
			$updater = new AffWP_AddOn_Updater( 6956, __FILE__, self::$version );
		}
	}

	/**
	 * Forces the was_referred() function to return true so our referral can still be created
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @deprecated 1.2.3 Use __return_true() instead.
	 * @see __return_true()
	 */
	public function force_was_referred( $bool ) {
		_deprecated_function( __METHOD__, '1.3', '__return_true' );

		return true;
	}

}

/**
 * The main function responsible for returning the one true AffiliateWP_Lifetime_Commissions
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $affiliatewp_lifetime_commissions = affiliate_wp_lifetime_commissions(); ?>
 *
 * @since 1.0
 * @return object The one true AffiliateWP_Lifetime_Commissions Instance
 * @since  1.0
 */
function affiliate_wp_lifetime_commissions() {

	if ( ! function_exists( 'affiliate_wp' ) ) {
		return;
	}

	return AffiliateWP_Lifetime_Commissions::instance();
}
add_action( 'plugins_loaded', 'affiliate_wp_lifetime_commissions', 100 );
