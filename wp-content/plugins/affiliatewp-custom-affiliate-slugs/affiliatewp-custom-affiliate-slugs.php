<?php
/**
 * Plugin Name: AffiliateWP - Custom Affiliate Slugs
 * Plugin URI: https://affiliatewp.com/
 * Description: Automatically generate custom slugs for your affiliates, or let your affiliates create their own
 * Author: Pippin Williamson and Andrew Munro
 * Author URI: https://affiliatewp.com
 * Version: 1.0.2
 * Text Domain: affiliatewp-custom-affiliate-slugs
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

if ( ! class_exists( 'AffiliateWP_Custom_Affiliate_Slugs' ) ) {

	final class AffiliateWP_Custom_Affiliate_Slugs {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of AffiliateWP_Custom_Affiliate_Slugs exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0.0
		 */
		private static $instance;

		private static $version;

		/**
		 * Main AffiliateWP_Custom_Affiliate_Slugs Instance
		 *
		 * Insures that only one instance of AffiliateWP_Custom_Affiliate_Slugs exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @static
		 * @static var array $instance
		 * @return The one true AffiliateWP_Custom_Affiliate_Slugs
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Custom_Affiliate_Slugs ) ) {

				self::$instance = new AffiliateWP_Custom_Affiliate_Slugs;
				self::$version = '1.0.2';

				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->init();
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
		 * @since 1.0.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-custom-affiliate-slugs' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 1.0.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-custom-affiliate-slugs' ), '1.0' );
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0.0
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0.0
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
		 * @since 1.0.0
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version
			if ( ! defined( 'AFFWP_CAS_VERSION' ) ) {
				define( 'AFFWP_CAS_VERSION', self::$version );
			}

			// Plugin Folder Path
			if ( ! defined( 'AFFWP_CAS_PLUGIN_DIR' ) ) {
				define( 'AFFWP_CAS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'AFFWP_CAS_PLUGIN_URL' ) ) {
				define( 'AFFWP_CAS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'AFFWP_CAS_PLUGIN_FILE' ) ) {
				define( 'AFFWP_CAS_PLUGIN_FILE', __FILE__ );
			}
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {

			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'affiliatewp_custom_affiliate_slugs_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-custom-affiliate-slugs' );
			$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliatewp-custom-affiliate-slugs', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-custom-affiliate-slugs/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-custom-affiliate-slugs/ folder
				load_textdomain( 'affiliatewp-custom-affiliate-slugs', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-custom-affiliate-slugs/languages/ folder
				load_textdomain( 'affiliatewp-custom-affiliate-slugs', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affiliatewp-custom-affiliate-slugs', false, $lang_dir );
			}
		}

		/**
		 * Init
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function init() {

			if ( is_admin() ) {
				self::$instance->updater();
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

			require_once AFFWP_CAS_PLUGIN_DIR . 'includes/functions.php';
			require_once AFFWP_CAS_PLUGIN_DIR . 'includes/class-base.php';

			if ( is_admin() ) {
				require_once AFFWP_CAS_PLUGIN_DIR . 'includes/class-admin.php';
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

			// plugin meta
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

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
				$updater = new AffWP_AddOn_Updater( 73907, __FILE__, self::$version );
			}
		}

		/**
		 * Modify plugin metalinks
		 *
		 * @access      public
		 * @since       1.0.0
		 * @param       array $links The current links array
		 * @param       string $file A specific plugin table entry
		 * @return      array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
		    if ( $file == plugin_basename( __FILE__ ) ) {
		        $plugins_link = array(
		            '<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliatewp-custom-affiliate-slugs' ) . '" href="http://affiliatewp.com/addons/" target="_blank">' . __( 'More add-ons', 'affiliatewp-custom-affiliate-slugs' ) . '</a>'
		        );

		        $links = array_merge( $links, $plugins_link );
		    }

		    return $links;
		}
	}

	/**
	 * The main function responsible for returning the one true AffiliateWP_Custom_Affiliate_Slugs
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affiliatewp_custom_affiliate_slugs = affiliatewp_custom_affiliate_slugs(); ?>
	 *
	 * @since 1.0
	 * @return object The one true AffiliateWP_Custom_Affiliate_Slugs Instance
	 */
	function affiliatewp_custom_affiliate_slugs() {
	    if ( ! class_exists( 'Affiliate_WP' ) ) {
	        if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
	            require_once 'includes/class-activation.php';
	        }

	        $activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
	        $activation = $activation->run();
	    } else {
	        return AffiliateWP_Custom_Affiliate_Slugs::instance();
	    }
	}
	add_action( 'plugins_loaded', 'affiliatewp_custom_affiliate_slugs', 100 );

}
