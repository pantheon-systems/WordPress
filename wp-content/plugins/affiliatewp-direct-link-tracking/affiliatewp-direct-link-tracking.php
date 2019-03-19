<?php
/**
 * Plugin Name: AffiliateWP - Direct Link Tracking
 * Plugin URI: https://affiliatewp.com/add-ons/pro/direct-link-tracking/
 * Description: Allow affiliates to link directly to your site, from their site, without the need for an affiliate link
 * Author: AffiliateWP
 * Author URI: https://affiliatewp.com
 * Version: 1.1.3
 * Text Domain: affiliatewp-direct-link-tracking
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

if ( ! class_exists( 'AffiliateWP_Direct_Link_Tracking' ) ) {

	final class AffiliateWP_Direct_Link_Tracking {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of AffiliateWP_Direct_Link_Tracking exists in memory at any one
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
		 * The version number
		 *
		 * @since 1.0
		 */
		private static $version;

		/**
		 * The tracking class instance variable
		 *
		 * @var AffiliateWP_Direct_Link_Tracking_Base
		 * @since 1.0
		 */
		public $tracking;

		/**
		 * The direct links DB instance variable.
		 *
		 * @var Affiliate_WP_Direct_Links_DB
		 * @since 1.0.0
		 */
		public $direct_links;

		/**
		 * The frontend instance variable.
		 *
		 * @var AffiliateWP_Direct_Link_Tracking_Frontend
		 * @since 1.1
		 */
		public $frontend;

		/**
		 * The emails instance variable.
		 *
		 * @var AffiliateWP_Direct_Link_Tracking_Emails
		 * @since 1.1
		 */
		public $emails;

		/**
		 * Main AffiliateWP_Direct_Link_Tracking Instance
		 *
		 * Insures that only one instance of AffiliateWP_Direct_Link_Tracking exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 * @static var array $instance
		 * @return The one true AffiliateWP_Direct_Link_Tracking
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Direct_Link_Tracking ) ) {

				self::$instance = new AffiliateWP_Direct_Link_Tracking;
				self::$version  = '1.1.3';

				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->init();
				self::$instance->includes();
				self::$instance->hooks();

				self::$instance->tracking     = new AffiliateWP_Direct_Link_Tracking_Base;
				self::$instance->direct_links = new Affiliate_WP_Direct_Links_DB;
				self::$instance->frontend     = new AffiliateWP_Direct_Link_Tracking_Frontend;
				self::$instance->emails       = new AffiliateWP_Direct_Link_Tracking_Emails;
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
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-direct-link-tracking' ), '1.0' );
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
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-direct-link-tracking' ), '1.0' );
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
			if ( ! defined( 'AFFWP_DLT_VERSION' ) ) {
				define( 'AFFWP_DLT_VERSION', self::$version );
			}

			// Plugin Folder Path
			if ( ! defined( 'AFFWP_DLT_PLUGIN_DIR' ) ) {
				define( 'AFFWP_DLT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'AFFWP_DLT_PLUGIN_URL' ) ) {
				define( 'AFFWP_DLT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'AFFWP_DLT_PLUGIN_FILE' ) ) {
				define( 'AFFWP_DLT_PLUGIN_FILE', __FILE__ );
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
			$lang_dir = apply_filters( 'affwp_direct_link_tracking_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-direct-link-tracking' );
			$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliatewp-direct-link-tracking', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-direct-link-tracking/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-direct-link-tracking/ folder
				load_textdomain( 'affiliatewp-direct-link-tracking', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-plugin-template/languages/ folder
				load_textdomain( 'affiliatewp-direct-link-tracking', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affiliatewp-direct-link-tracking', false, $lang_dir );
			}
		}

		/**
		 * Init
		 *
		 * @access private
		 * @since  1.0.0
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
		 * @access private
		 * @since  1.0.0
		 * @return void
		 */
		private function includes() {

			if ( is_admin() ) {
				require_once AFFWP_DLT_PLUGIN_DIR . 'includes/class-admin.php';
				require_once AFFWP_DLT_PLUGIN_DIR . 'includes/class-list-table.php';
				require_once AFFWP_DLT_PLUGIN_DIR . 'includes/class-notices.php';
			}

			require_once AFFWP_DLT_PLUGIN_DIR . 'includes/class-shortcodes.php';
			require_once AFFWP_DLT_PLUGIN_DIR . 'includes/actions.php';
			require_once AFFWP_DLT_PLUGIN_DIR . 'includes/functions.php';
			require_once AFFWP_DLT_PLUGIN_DIR . 'includes/direct-link-functions.php';
			require_once AFFWP_DLT_PLUGIN_DIR . 'includes/url-functions.php';
			require_once AFFWP_DLT_PLUGIN_DIR . 'includes/domain-functions.php';
			require_once AFFWP_DLT_PLUGIN_DIR . 'includes/class-direct-links-db.php';
			require_once AFFWP_DLT_PLUGIN_DIR . 'includes/class-tracking.php';
			require_once AFFWP_DLT_PLUGIN_DIR . 'includes/class-frontend.php';
			require_once AFFWP_DLT_PLUGIN_DIR . 'includes/class-emails.php';

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

			// Add template folder
			add_filter( 'affwp_template_paths', array( $this, 'template' ) );

		}

		/**
		 * Add template folder
		 *
		 * @since 1.1
		 * @return void
		 */
		public function template( $file_paths ) {
			$file_paths[81] = plugin_dir_path( __FILE__ ) . '/templates';

			return $file_paths;
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
				$updater = new AffWP_AddOn_Updater( 100847, __FILE__, self::$version );
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
		            '<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliatewp-direct-link-tracking' ) . '" href="https://affiliatewp.com/addons/" target="_blank">' . __( 'More add-ons', 'affiliatewp-direct-link-tracking' ) . '</a>'
		        );

		        $links = array_merge( $links, $plugins_link );
		    }

		    return $links;
		}
	}

	/**
	 * The main function responsible for returning the one true AffiliateWP_Direct_Link_Tracking
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affiliatewp_plugin_template = affiliatewp_plugin_template(); ?>
	 *
	 * @since 1.0
	 * @return AffiliateWP_Direct_Link_Tracking The one true AffiliateWP_Direct_Link_Tracking Instance
	 */
	function affiliatewp_direct_link_tracking() {
	    if ( ! class_exists( 'Affiliate_WP' ) ) {
	        if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
	            require_once 'includes/class-activation.php';
	        }

	        $activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
	        $activation = $activation->run();
	    } else {
	        return AffiliateWP_Direct_Link_Tracking::instance();
	    }
	}
	add_action( 'plugins_loaded', 'affiliatewp_direct_link_tracking', 100 );

}

/**
 * Register activation hook
 *
 * Registering the hook inside the 'plugins_loaded' hook will not work.
 * You can't call register_activation_hook() inside a function hooked to the 'plugins_loaded' or 'init' hooks (or any other hook).
 * These hooks are called before the plugin is loaded or activated.
 *
 * @since 1.0
*/
function affiliatewp_dlt_plugin_activate() {
	add_option( 'affwp_dlt_activated', true );
}
register_activation_hook( __FILE__, 'affiliatewp_dlt_plugin_activate' );

/**
 * Installation script
 *
 * @since 1.0.0
 */
function affiliatewp_dlt_plugin_install() {

    if ( is_admin() && get_option( 'affwp_dlt_activated' ) == true ) {

		if ( ! class_exists( 'Affiliate_WP' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			delete_option( 'affwp_dlt_activated' );
			return;
		}

		include_once dirname( __FILE__ ) . '/includes/install.php';

        delete_option( 'affwp_dlt_activated' );

        // run install script
        affiliatewp_dlt_install();

    }

}
add_action( 'admin_init', 'affiliatewp_dlt_plugin_install' );
