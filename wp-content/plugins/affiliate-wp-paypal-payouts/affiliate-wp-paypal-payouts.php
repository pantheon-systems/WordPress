<?php
/**
 * Plugin Name: AffiliateWP - PayPal Payouts
 * Plugin URI: http://affiliatewp.com/addons/paypal-payouts/
 * Description: Instantly pay your affiliates via PayPal
 * Author: Pippin Williamson and Andrew Munro
 * Author URI: http://affiliatewp.com
 * Version: 1.1.11
 * Text Domain: affwp-paypal-payouts
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
 * @package AffiliateWP PayPal Payouts
 * @category Core
 * @author Pippin Williamson
 * @version 1.1.11
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

final class AffiliateWP_PayPal_Payouts {

	/** Singleton *************************************************************/

	/**
	 * @var AffiliateWP_PayPal_Payouts The one true AffiliateWP_PayPal_Payouts
	 * @since 1.0
	 */
	private static $instance;

	public static $plugin_dir;
	public static $plugin_url;
	private static $version;

	/**
	 * Main AffiliateWP_PayPal_Payouts Instance
	 *
	 * Insures that only one instance of AffiliateWP_PayPal_Payouts exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @return The one true AffiliateWP_PayPal_Payouts
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_PayPal_Payouts ) ) {
			self::$instance = new AffiliateWP_PayPal_Payouts;

			self::$plugin_dir = plugin_dir_path( __FILE__ );
			self::$plugin_url = plugin_dir_url( __FILE__ );
			self::$version    = '1.1.11';

			self::$instance->load_textdomain();
			self::$instance->includes();
			self::$instance->init();

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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affwp-paypal-payouts' ), '1.0' );
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affwp-paypal-payouts' ), '1.0' );
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
		$lang_dir = apply_filters( 'affwp_stripe_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affwp-paypal-payouts' );
		$mofile   = sprintf( '%1$s-%2$s.mo', 'affwp-paypal-payouts', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/affwp-paypal-payouts/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/affwp-paypal-payouts/ folder
			load_textdomain( 'affwp-paypal-payouts', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/affwp-paypal-payouts/languages/ folder
			load_textdomain( 'affwp-paypal-payouts', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'affwp-paypal-payouts', false, $lang_dir );
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

		if( is_admin() ) {

			require_once self::$plugin_dir . 'admin/class-paypal-api.php';
			require_once self::$plugin_dir . 'admin/class-paypal-masspay.php';
			require_once self::$plugin_dir . 'admin/referrals.php';
			require_once self::$plugin_dir . 'admin/settings.php';

		}

	}

	/**
	 * Add in our filters to affect affiliate rates
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function init() {

		if( is_admin() ) {
			self::$instance->updater();
		}

	}


	/**
	 * Gets the Stripe API keys
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function get_api_credentials() {

		$payout_mode = affiliate_wp()->settings->get( 'paypal_payout_mode', 'masspay' );
		$mode        = $this->is_test_mode() ? 'test' : 'live';

		if( 'api' == $payout_mode ) {

			$creds = array(
				'client_id' => affiliate_wp()->settings->get( 'paypal_' . $mode . '_client_id', '' ),
				'secret'    => affiliate_wp()->settings->get( 'paypal_' . $mode . '_secret', '' ),
			);

		} else {

			$creds = array(
				'username'  => affiliate_wp()->settings->get( 'paypal_' . $mode . '_username', '' ),
				'password'  => affiliate_wp()->settings->get( 'paypal_' . $mode . '_password', '' ),
				'signature' => affiliate_wp()->settings->get( 'paypal_' . $mode . '_signature', '' )
			);

		}

		return $creds;
	}

	/**
	 * Checks if we have API credentails
	 *
	 * @access public
	 * @since 1.0
	 * @return bool
	 */
	public function has_api_credentials() {

		$ret         = true;
		$payout_mode = affiliate_wp()->settings->get( 'paypal_payout_mode', 'masspay' );
		$creds       = $this->get_api_credentials();

		if( 'api' == $payout_mode ) {

			if( empty( $creds['client_id'] ) ) {
				$ret = false;
			}

			if( empty( $creds['secret'] ) ) {
				$ret = false;
			}

		} else {

			if( empty( $creds['username'] ) ) {
				$ret = false;
			}

			if( empty( $creds['password'] ) ) {
				$ret = false;
			}

			if( empty( $creds['signature'] ) ) {
				$ret = false;
			}

		}

		return $ret;
	}

	/**
	 * Determines if we are in test mode
	 *
	 * @access public
	 * @since 1.0
	 * @return bool
	 */
	public function is_test_mode() {

		return (bool) affiliate_wp()->settings->get( 'paypal_test_mode', false );
	}


	/**
	 * Sets up the plugin updater class
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	public function updater() {

		if( class_exists( 'AffWP_AddOn_Updater' ) ) {
			$updater = new AffWP_AddOn_Updater( 345, __FILE__, self::$version );
		}
	}

	/**
	 * Displays an error message if PHP version is below 5.3
	 *
	 * @access private
	 * @since 1.1
	 * @return void
	 */
	public function php_version_notice() {
		echo '<div class="error"><p>' . __( 'Your version of PHP is below the minimum version of PHP required by AffiliateWP - PayPal Payouts. Version 5.3 or later is required.', 'affwp-paypal-payouts' ) . '</p></div>';
	}

}

/**
 * The main function responsible for returning the one true AffiliateWP_PayPal_Payouts
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $AffiliateWP_PayPal_Payouts = affiliate_wp_paypal(; ?>
 *
 * @since 1.0
 * @return object The one true AffiliateWP_PayPal_Payouts Instance
 */
function affiliate_wp_paypal() {

	if ( ! class_exists( 'Affiliate_WP' ) ) {

		if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
			require_once 'includes/class-activation.php';
		}

		$activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation = $activation->run();

	} else {
		return AffiliateWP_PayPal_Payouts::instance();
	}

}
add_action( 'plugins_loaded', 'affiliate_wp_paypal', 100 );
