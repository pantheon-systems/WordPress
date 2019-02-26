<?php
/**
 * Plugin Name: AffiliateWP - Show Affiliate Coupons
 * Plugin URI: https://affiliatewp.com/add-ons/official-free/show-affiliate-coupons/
 * Description: Shows an affiliate their available coupon codes in the affiliate area
 * Author: AffiliateWP
 * Author URI: https://affiliatewp.com
 * Version: 1.0.7
 * Text Domain: affiliatewp-show-affiliate-coupons
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
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AffiliateWP_Show_Affiliate_Coupons' ) ) {

	final class AffiliateWP_Show_Affiliate_Coupons {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of AffiliateWP_Show_Affiliate_Coupons exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;


		/**
		 * The version number of AffiliateWP
		 *
		 * @since 1.0
		 */
		private $version = '1.0.7';

		/**
		 * Main AffiliateWP_Show_Affiliate_Coupons Instance
		 *
		 * Insures that only one instance of AffiliateWP_Show_Affiliate_Coupons exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 * @static var array $instance
		 * @return The one true AffiliateWP_Show_Affiliate_Coupons
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Show_Affiliate_Coupons ) ) {

				self::$instance = new AffiliateWP_Show_Affiliate_Coupons;
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->includes();
				self::$instance->hooks();

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
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-show-affiliate-coupons' ), '1.0' );
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
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-show-affiliate-coupons' ), '1.0' );
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version
			if ( ! defined( 'AFFWP_SAC_VERSION' ) ) {
				define( 'AFFWP_SAC_VERSION', $this->version );
			}

			// Plugin Folder Path
			if ( ! defined( 'AFFWP_SAC_PLUGIN_DIR' ) ) {
				define( 'AFFWP_SAC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'AFFWP_SAC_PLUGIN_URL' ) ) {
				define( 'AFFWP_SAC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'AFFWP_SAC_PLUGIN_FILE' ) ) {
				define( 'AFFWP_SAC_PLUGIN_FILE', __FILE__ );
			}
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
			$lang_dir = apply_filters( 'affiliatewp_show_affiliate_coupons_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-show-affiliate-coupons' );
			$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliatewp-show-affiliate-coupons', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-show-affiliate-coupons/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-show-affiliate-coupons/ folder
				load_textdomain( 'affiliatewp-show-affiliate-coupons', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-show-affiliate-coupons/languages/ folder
				load_textdomain( 'affiliatewp-show-affiliate-coupons', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affiliatewp-show-affiliate-coupons', false, $lang_dir );
			}
		}

		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function includes() {

		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {

			// add new tab to affiliate area
			add_action( 'affwp_affiliate_dashboard_tabs', array( $this, 'add_tab' ), 10, 2 );

			// prevent access to tab
			add_action( 'template_redirect', array( $this, 'no_access_redirect' ) );

			// Add template folder
			add_filter( 'affwp_template_paths', array( $this, 'template_paths' ) );

			// shortcode
			add_shortcode( 'affiliate_coupons', array( $this, 'affiliate_coupons_shortcode' ) );

			// plugin meta
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

			// Add to the tabs list for 1.8.1 (fails silently if the hook doesn't exist).
			add_filter( 'affwp_affiliate_area_tabs', array( $this, 'register_tab' ), 10, 1 );

		}

		/**
		 * Check to see if a supported integration is enabled
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function integration_supported() {

			$supported_integrations = $this->supported_integrations();
			$enabled_integrations = affiliate_wp()->integrations->get_enabled_integrations();

			foreach ( $enabled_integrations as $integration_key => $integration ) {
				// integration supported
				if ( in_array( $integration_key, $supported_integrations ) ) {
					return true;
				}
			}

			return false;

		}

		/**
		 * Currently supported integrations
		 * @since  1.0
		 * @return array supported integrations
		 */
		public function supported_integrations() {

			$supported_integrations = array(
				'edd',
				'woocommerce',
				'rcp',
				'exchange',
				'memberpress'
			);

			return $supported_integrations;
		}

		/**
		 * Register the "Coupons" tab.
		 * 
		 * @since  1.8.1
		 * @since  2.1.7 The tab being registered requires both a slug and title.
		 * 
		 * @return array $tabs The list of tabs
		 */
		public function register_tab( $tabs ) {

			/**
			 * User is on older version of AffiliateWP, use the older method of
			 * registering the tab. 
			 * 
			 * The previous method was to register the slug, and add the tab
			 * separately, @see add_tab()
			 * 
			 * @since 1.0.6
			 */
			if ( ! $this->has_2_1_7() ) {
				return array_merge( $tabs, array( 'coupons' ) );
			}

			/**
			 * Don't show tab to affiliate if they don't have access.
			 * Also makes sure tab is properly outputted in Affiliate Area Tabs.
			 * 
			 * @since 1.0.6
			 */
			if ( ! is_admin() ) {
				if ( $this->no_access() ) {
					return $tabs;
				}
			}
			 
			// Register the "Coupons" tab.
			$tabs['coupons'] = __( 'Coupons', 'affiliatewp-show-affiliate-coupons' );
			
			// Return the tabs.
			return $tabs;
		}

		/**
		 * Determine if the user has at least version 2.1.7 of AffiliateWP.
		 *
		 * @since 1.0.6
		 * 
		 * @return boolean True if AffiliateWP v2.1.7 or newer, false otherwise.
		 */
		public function has_2_1_7() {

			$return = true;

			if ( version_compare( AFFILIATEWP_VERSION, '2.1.7', '<' ) ) {
				$return = false;
			}

			return $return;
		}

		/**
		 * Add tab
		 *
		 * @since 1.0
		 * @since 1.0.6 Only kept for backwards compatibility.
		 *
		 * @return void
		 */
		public function add_tab( $affiliate_id, $active_tab ) {

			// Return early if user has AffiliateWP 2.1.7 or newer. This method is no longer needed.
			if ( $this->has_2_1_7() ) {
				return;
			}

			if ( $this->no_access() ) {
				return;
			}

			?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'coupons' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'coupons' ) ); ?>"><?php _e( 'Coupons', 'affiliatewp-show-affiliate-coupons' ); ?></a>
			</li>

		<?php
		}

		/**
		 * Whether or not we're on the customer's tab of the dashboard
		 *
		 * @since 1.0
		 *
		 * @return boolean
		 */
		public function is_tab() {
			if ( isset( $_GET['tab']) && 'coupons' == $_GET['tab'] ) {
				return (bool) true;
			}

			return (bool) false;
		}

		/**
		 * No access
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function no_access() {

			// no access if integration isn't supported or affiliate does not have any active coupons
			if ( ! $this->integration_supported() || ! $this->get_coupons() ) {
				return true;
			}

			return false;
		}

		/**
		 * Redirect affiliate to main dashboard page if they cannot access tab
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function no_access_redirect() {

			// cannot access tab
			if ( $this->is_tab() && ( ! $this->integration_supported() || ! $this->get_coupons() ) ) {
				wp_redirect( affiliate_wp()->login->get_login_url() );
				exit;
			}
		}

		/**
		 * Add template folder to hold the customer table
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function template_paths( $file_paths ) {
			$file_paths[82] = plugin_dir_path( __FILE__ ) . '/templates';

			return $file_paths;
		}

		/**
		 * List the affiliate's coupon codes
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function get_coupons() {

			global $wpdb;

			$affiliate_id = affwp_get_affiliate_id();

			if ( ! $affiliate_id ) {
				return false;
			}

			$post_ids = $wpdb->get_results(
				"
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE ( meta_key = 'affwp_discount_affiliate' OR meta_key = 'affwp_coupon_affiliate' )
				AND meta_value = $affiliate_id
				"
			);

			$ids = wp_list_pluck( $post_ids, 'post_id' );

			$coupons = array();

			// get enabled integrations
			$enabled_integrations = affiliate_wp()->integrations->get_enabled_integrations();

			if ( $ids ) {
				foreach ( $ids as $id ) {

					switch ( get_post_type( $id ) ) {
						// EDD
						case 'edd_discount':

						if ( array_key_exists( 'edd', $enabled_integrations ) && edd_is_discount_active( $id ) ) {
							$coupons[$id]['code'] = edd_get_discount_code( $id );
							$coupons[$id]['amount'] = edd_format_discount_rate( edd_get_discount_type( $id ), edd_get_discount_amount( $id ) );

						}

							break;

						// WooCommerce
						case 'shop_coupon':

						if ( array_key_exists( 'woocommerce', $enabled_integrations ) && 'publish' == get_post_status( $id ) ) {

							$coupons[$id]['code']   = get_the_title( $id );
							$coupons[$id]['amount'] = esc_html( get_post_meta( $id, 'coupon_amount', true ) ) . ' (' . esc_html( wc_get_coupon_type( get_post_meta( $id, 'discount_type', true ) ) ) . ')';
						}

							break;

						// iThemes Exchange
						case 'it_exchange_coupon':

						if ( array_key_exists( 'exchange', $enabled_integrations ) ) {

							$coupons[$id]['code']   = get_post_meta( $id, '_it-basic-code', true );
							$coupons[$id]['amount'] = esc_attr( it_exchange_get_coupon_discount_label( $id ) );
						}

							break;

						// MemberPress
						case 'memberpresscoupon':

						if ( array_key_exists( 'memberpress', $enabled_integrations ) && 'publish' == get_post_status( $id ) ) {

							$coupons[$id]['code']   = get_the_title( $id );
							$coupons[$id]['amount'] = esc_html( get_post_meta( $id, '_mepr_coupons_discount_amount', true ) ) . ' (' . esc_html( get_post_meta( $id, '_mepr_coupons_discount_type', true ) ) . ')';
						}

							break;

						 default:
							break;
					}


				}
			}

			if ( ! empty( $coupons ) ) {
				return $coupons;
			}

			return false;
		}

		/**
		* [affiliate_coupons] shortcode
		*
		* @since  1.0
		*/
		public function affiliate_coupons_shortcode( $atts, $content = null ) {

			if ( ! ( affwp_is_affiliate() && affwp_is_active_affiliate() ) ) {
				return;
			}

			ob_start();

			affiliate_wp()->templates->get_template_part( 'dashboard-tab', 'coupons' );

			$content = ob_get_clean();

			return do_shortcode( $content );
		}

		/**
		 * Modify plugin metalinks
		 *
		 * @access      public
		 * @since       1.0
		 * @param       array $links The current links array
		 * @param       string $file A specific plugin table entry
		 * @return      array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
			if ( $file == plugin_basename( __FILE__ ) ) {
				$plugins_link = array(
					'<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliatewp-show-affiliate-coupons' ) . '" href="http://affiliatewp.com/addons/" target="_blank">' . __( 'Get add-ons', 'affiliatewp-show-affiliate-coupons' ) . '</a>'
				);

				$links = array_merge( $links, $plugins_link );
			}

			return $links;
		}
	}

	/**
	 * The main function responsible for returning the one true AffiliateWP_Show_Affiliate_Coupons
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affiliatewp_show_affiliate_coupons = affiliatewp_show_affiliate_coupons(); ?>
	 *
	 * @since 1.0
	 * @return object The one true AffiliateWP_Show_Affiliate_Coupons Instance
	 */
	function affiliatewp_show_affiliate_coupons() {
		if ( ! class_exists( 'Affiliate_WP' ) ) {
			if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
				require_once 'includes/class-activation.php';
			}

			$activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
			$activation = $activation->run();
		} else {
			return AffiliateWP_Show_Affiliate_Coupons::instance();
		}
	}
	add_action( 'plugins_loaded', 'affiliatewp_show_affiliate_coupons', 100 );

}
