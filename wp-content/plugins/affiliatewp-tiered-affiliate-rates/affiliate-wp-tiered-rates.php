<?php
/**
 * Plugin Name: AffiliateWP - Tiered Rates
 * Plugin URI: http://affiliatewp.com/addons/tiered-affiliate-rates/
 * Description: Tiered affiliate rates for AffiliateWP
 * Author: AffiliateWP
 * Author URI: http://affiliatewp.com
 * Version: 1.1
 * Text Domain: affiliate-wp-tiered
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
 * @package AffiliateWP Tiered Rates
 * @category Core
 * @author Pippin Williamson
 * @version 1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

final class AffiliateWP_Tiered_Rates {

	/** Singleton *************************************************************/

	/**
	 * @var AffiliateWP_Tiered_Rates The one true AffiliateWP_Tiered_Rates
	 * @since 1.0
	 */
	private static $instance;

	private static $plugin_dir;
	private static $version;

	/**
	 * Main AffiliateWP_Tiered_Rates Instance
	 *
	 * Insures that only one instance of AffiliateWP_Tiered_Rates exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @return The one true AffiliateWP_Tiered_Rates
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Tiered_Rates ) ) {
			self::$instance = new AffiliateWP_Tiered_Rates;

			self::$plugin_dir = plugin_dir_path( __FILE__ );
			self::$version    = '1.1';

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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp-tiered' ), '1.0' );
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp-tiered' ), '1.0' );
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
		$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliate-wp-tiered' );
		$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliate-wp-tiered', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/affiliate-wp-tiered/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/affiliate-wp-tiered/ folder
			load_textdomain( 'affiliate-wp-tiered', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/affiliate-wp-tiered/languages/ folder
			load_textdomain( 'affiliate-wp-tiered', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'affiliate-wp-tiered', false, $lang_dir );
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

			require_once self::$plugin_dir . 'admin/rates.php';

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

		add_filter( 'affwp_get_affiliate_rate', array( $this, 'get_affiliate_rate' ), 10, 3 );

		if( is_admin() ) {
			self::$instance->updater();
		}

		add_filter( 'affwp_tiered_rates', array( $this, 'remove_disabled_rates' ) );
	}

	/**
	 * Retrieve the tiered rates
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function get_rates() {
		$rates = affiliate_wp()->settings->get( 'rates', array() );

		/**
		 * Filters tiered rate values.
		 *
		 * @since 1.0
		 *
		 * @param array $rate_values Rate values.
		 */
		return apply_filters( 'affwp_tiered_rates', array_values( $rates ) );
	}

	/**
	 * Removes disabled rates from consideration.
	 *
	 * @since 1.1
	 * @access public
	 *
	 * @param array $rates Rate values.
	 * @return array Filtered rates
	 */
	public function remove_disabled_rates( $rates ) {

		// Bail if on rates edit screen.
		if ( is_admin() && ( ! empty( $_GET['page'] ) || ! empty( $_GET['tab'] ) || 'affiliate-wp-settings' != $_GET['tab'] || 'rates' != $_GET['tab'] ) ) {
			return $rates;
		}

		foreach ( $rates as $index => $rate ) {
			if ( isset( $rate['disabled'] ) && 'on' === $rate['disabled'] ) {
				unset( $rates[ $index ] );
			}
		}

		return $rates;
	}

	/**
	 * Retrieve the rate for a specific affiliate
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function get_affiliate_rate( $rate, $affiliate_id, $type ) {

		$rates          = $this->get_rates();
		$affiliate_rate = affiliate_wp()->affiliates->get_column( 'rate', $affiliate_id );

		$tiers_expire = affiliate_wp()->settings->get( 'rate-expiration', null );
		$tiers_expire = isset( $tiers_expire );

		if ( ! empty( $rates ) && empty( $affiliate_rate ) ) {
			// Start with highest tiers
			$rates = array_reverse( $rates );

			if ( $tiers_expire ) {
				$earnings  = affiliate_wp()->referrals->paid_earnings( 'month', $affiliate_id, false );
				$referrals = $this->paid_count( 'month', $affiliate_id );
			} else {
				$earnings  = affwp_get_affiliate_earnings( $affiliate_id, false );
				$referrals = affwp_get_affiliate_referral_count( $affiliate_id );
			}

			// Loop through the rates to see which applies to this affiliate
			foreach( $rates as $tiered_rate ) {

				if( empty( $tiered_rate['threshold'] ) || empty( $tiered_rate['rate'] ) ) {
					continue;
				}

				if( 'earnings' == $tiered_rate['type'] ) {

					if( $earnings >= affwp_sanitize_amount( $tiered_rate['threshold'] ) ) {
						$rate = $tiered_rate['rate'];
						break;

					}

				} else {

					if( $referrals >= $tiered_rate['threshold'] ) {

						$rate = $tiered_rate['rate'];
						break;

					}

				}

			}

			if ( 'percentage' == $type ) {
				// Sanitize the rate and ensure it's in the proper format
				if ( $rate > 1 ) {
					$rate = $rate / 100;
				}
			}
		
		}

		return $rate;
	}

	public function updater() {

		if( class_exists( 'AffWP_AddOn_Updater' ) ) {
			$updater = new AffWP_AddOn_Updater( 368, __FILE__, self::$version );
		}
	}

	/**
	 * Retrieves the paid referrals count for the given affiliate.
	 *
	 * @since 1.1
	 * @access public
	 *
	 * @param string $date         Date period to retrieve the referral count for.
	 * @param int    $affiliate_id Affiliate ID.
	 * @return int Number of paid referrals for the time period (based on now).
	 */
	public function paid_count( $date = '', $affiliate_id = 0 ) {
		$args = array(
			'affiliate_id' => absint( $affiliate_id ),
			'status'       => 'paid',
		);

		if ( ! empty( $date ) ) {
			switch ( $date ) {
				case 'month' :
					$date = array(
						'start' => date( 'Y-m-d H:i:s', strtotime( 'first day of', current_time( 'timestamp' ) ) ),
						'end'   => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
					);
					break;
			}
			$args['date'] = $date;
		}

		return affiliate_wp()->referrals->count( $args );
	}

}

/**
 * The main function responsible for returning the one true AffiliateWP_Tiered_Rates
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $AffiliateWP_Tiered_Rates = affiliate_wp_tiers(); ?>
 *
 * @since 1.0
 * @return object The one true AffiliateWP_Tiered_Rates Instance
 */
function affiliate_wp_tiers() {
	return AffiliateWP_Tiered_Rates::instance();
}
add_action( 'plugins_loaded', 'affiliate_wp_tiers', 100 );