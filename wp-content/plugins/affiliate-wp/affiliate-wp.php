<?php
/**
 * Plugin Name: AffiliateWP
 * Plugin URI: https://affiliatewp.com
 * Description: Affiliate Plugin for WordPress
 * Author: AffiliateWP, LLC
 * Author URI: https://affiliatewp.com
 * Version: 2.2.14
 * Text Domain: affiliate-wp
 * Domain Path: languages
 * GitHub Plugin URI: affiliatewp/affiliatewp
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
 * @package AffiliateWP
 * @category Core
 * @author Pippin Williamson
 * @version 2.2.14
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Affiliate_WP' ) ) :

/**
 * Main Affiliate_WP Class
 *
 * @since 1.0
 */
final class Affiliate_WP {
	/** Singleton *************************************************************/

	/**
	 * AffiliateWP instance.
	 *
	 * @access private
	 * @since  1.0
	 * @var    Affiliate_WP The one true Affiliate_WP
	 */
	private static $instance;

	/**
	 * The version number of AffiliateWP.
	 *
	 * @access private
	 * @since  1.0
	 * @var    string
	 */
	private $version = '2.2.14';

	/**
	 * The affiliates DB instance variable.
	 *
	 * @access public
	 * @since  1.0
	 * @var    Affiliate_WP_DB_Affiliates
	 */
	public $affiliates;

	/**
	 * The affiliate meta DB instance variable.
	 *
	 * @access public
	 * @since  1.6
	 * @var    Affiliate_WP_Affiliate_Meta_DB
	 */
	public $affiliate_meta;

	/**
	 * The customers DB instance variable.
	 *
	 * @access public
	 * @since  2.2
	 * @var    Affiliate_WP_Customers_DB
	 */
	public $customers;

	/**
	 * The customer meta DB instance variable.
	 *
	 * @access public
	 * @since  2.2
	 * @var    Affiliate_WP_Customer_Meta_DB
	 */
	public $customer_meta;

	/**
	 * The referrals instance variable.
	 *
	 * @access public
	 * @since  1.0
	 * @var    Affiliate_WP_Referrals_DB
	 */
	public $referrals;

	/**
	 * The campaigns instance variable.
	 *
	 * @access public
	 * @since  1.7
	 * @var    Affiliate_WP_Campaigns_DB
	 */
	public $campaigns;

	/**
	 * The visits DB instance variable
	 *
	 * @access public
	 * @since  1.0
	 * @var    Affiliate_WP_Visits_DB
	 */
	public $visits;

	/**
	 * The settings instance variable
	 *
	 * @access public
	 * @since  1.0
	 * @var    Affiliate_WP_Settings
	 */
	public $settings;

	/**
	 * The affiliate tracking handler instance variable
	 *
	 * @access public
	 * @since  1.0
	 * @var    Affiliate_WP_Tracking
	 */
	public $tracking;

	/**
	 * The template loader instance variable
	 *
	 * @access public
	 * @since  1.0
	 * @var    Affiliate_WP_Templates
	 */
	public $templates;

	/**
	 * The affiliate login handler instance variable
	 *
	 * @access public
	 * @since  1.0
	 * @var    Affiliate_WP_Login
	 */
	public $login;

	/**
	 * The opt in form handler instance variable
	 *
	 * @access public
	 * @since  2.2
	 * @var    Affiliate_WP_Opt_In
	 */
	public $opt_in;

	/**
	 * The affiliate registration handler instance variable
	 *
	 * @access public
	 * @since  1.0
	 * @var    Affiliate_WP_Register
	 */
	public $register;

	/**
	 * The integrations handler instance variable
	 *
	 * @access public
	 * @since  1.0
	 * @var    Affiliate_WP_Integrations
	 */
	public $integrations;

	/**
	 * The email notification handler instance variable
	 *
	 * @access public
	 * @since  1.0
	 * @var    Affiliate_WP_Emails
	 */
	public $emails;

	/**
	 * The creatives instance variable
	 *
	 * @access public
	 * @since  1.2
	 * @var    Affiliate_WP_Creatives_DB
	 */
	public $creatives;

	/**
	 * The creative class instance variable
	 *
	 * @access public
	 * @since  1.3
	 * @var    Affiliate_WP_Creatives
	 */
	public $creative;

	/**
	 * The rewrite class instance variable
	 *
	 * @access public
	 * @since  1.7.8
	 * @var    Affiliate_WP_Rewrites
	 */
	public $rewrites;

	/**
	 * REST API bootstrap.
	 *
	 * @access public
	 * @since  1.9
	 * @var    Affiliate_WP_REST
	 */
	public $REST;

	/**
	 * The capabilities class instance variable.
	 *
	 * @access public
	 * @since  2.0
	 * @var    Affiliate_WP_Capabilities
	 */
	public $capabilities;

	/**
	 * The utilities class instance variable.
	 *
	 * @access public
	 * @since  2.0
	 * @var    Affiliate_WP_Utilities
	 */
	public $utils;

	/**
	 * Main Affiliate_WP Instance
	 *
	 * Insures that only one instance of Affiliate_WP exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @uses Affiliate_WP::setup_globals() Setup the globals needed
	 * @uses Affiliate_WP::includes() Include the required files
	 * @uses Affiliate_WP::setup_actions() Setup the hooks and actions
	 * @uses Affiliate_WP::updater() Setup the plugin updater
	 * @return Affiliate_WP
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Affiliate_WP ) ) {
			self::$instance = new Affiliate_WP;

			if( version_compare( PHP_VERSION, '5.3', '<' ) ) {

				add_action( 'admin_notices', array( 'Affiliate_WP', 'below_php_version_notice' ) );

				return self::$instance;

			}

			self::$instance->setup_constants();
			self::$instance->includes();

			add_action( 'plugins_loaded', array( self::$instance, 'setup_objects' ), -1 );
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp' ), '1.0' );
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp' ), '1.0' );
	}

	/**
	 * Show a warning to sites running PHP < 5.3
	 *
	 * @static
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	public static function below_php_version_notice() {
		echo '<div class="error"><p>' . __( 'Your version of PHP is below the minimum version of PHP required by AffiliateWP. Please contact your host and request that your version be upgraded to 5.3 or later.', 'affiliate-wp' ) . '</p></div>';
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
		if ( ! defined( 'AFFILIATEWP_VERSION' ) ) {
			define( 'AFFILIATEWP_VERSION', $this->version );
		}

		// Plugin Folder Path
		if ( ! defined( 'AFFILIATEWP_PLUGIN_DIR' ) ) {
			define( 'AFFILIATEWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'AFFILIATEWP_PLUGIN_URL' ) ) {
			define( 'AFFILIATEWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin directory name only.
		if ( ! defined( 'AFFILIATEWP_PLUGIN_DIR_NAME' ) ) {
			define( 'AFFILIATEWP_PLUGIN_DIR_NAME', basename( __DIR__ ) );
		}

		// Plugin Root File
		if ( ! defined( 'AFFILIATEWP_PLUGIN_FILE' ) ) {
			define( 'AFFILIATEWP_PLUGIN_FILE', __FILE__ );
		}

		// Make sure CAL_GREGORIAN is defined.
		if ( ! defined( 'CAL_GREGORIAN' ) ) {
			define( 'CAL_GREGORIAN', 1 );
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

		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/abstracts/class-affwp-object.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-affwp-affiliate.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-affwp-customer.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-affwp-creative.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-affwp-payout.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-affwp-referral.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-affwp-visit.php';

		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/actions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/abstracts/class-affwp-registry.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/settings/class-settings.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/abstracts/class-db.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-affiliates-db.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-payouts-db.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-capabilities.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-utilities.php';

		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

			// Bootstrap.
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/AFFWP_Plugin_Updater.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/abstracts/class-affwp-list-table.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-meta-box-base.php';

			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/actions.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/ajax-actions.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/class-addon-updater.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/class-menu.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/affiliates.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/class-notices.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/creatives/actions.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/creatives/creatives.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/class-meta-box-base.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/overview/overview.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/referrals/actions.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/referrals/referrals.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/payouts/payouts.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/reports.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/settings/display-settings.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/visits/visits.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/tools.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/plugins.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-migrate.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/add-ons.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/user-profile.php';

		}

		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-shortcodes.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/emails/class-affwp-emails.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/emails/functions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/emails/actions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/date-functions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-graph.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-referrals-graph.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-visits-graph.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-payouts-graph.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/abstracts/class-affwp-opt-in-platform.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-integrations.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-login.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-referrals-db.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-referral-type-registry.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-register.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-templates.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-tracking.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-rewrites.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-visits-db.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-campaigns-db.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-customers-db.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-customer-meta-db.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-creatives-db.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-creatives.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-affiliate-meta-db.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/affiliate-functions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/affiliate-meta-functions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/customer-meta-functions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-registrations-graph.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/misc-functions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/payout-functions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/referral-functions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/visit-functions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/customer-functions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/creative-functions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/install.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/plugin-compatibility.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/scripts.php';

		// REST bootstrap.
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/REST/rest-functions.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/REST/class-rest-consumer.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/REST/class-rest.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/REST/class-rest-authentication.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/REST/class-rest-consumers-db.php';

		// REST endpoints.
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/REST/v1/class-rest-controller.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/REST/v1/class-affiliates-endpoints.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/REST/v1/class-creatives-endpoints.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/REST/v1/class-payouts-endpoints.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/REST/v1/class-referrals-endpoints.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/REST/v1/class-visits-endpoints.php';

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/class-command.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/class-sub-commands-base.php';

			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/utils/class-affiliate-fetcher.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/utils/class-creative-fetcher.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/utils/class-customer-fetcher.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/utils/class-payout-fetcher.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/utils/class-referral-fetcher.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/utils/class-visit-fetcher.php';

			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/class-affiliate-sub-commands.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/class-creative-sub-commands.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/class-customer-sub-commands.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/class-payout-sub-commands.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/class-referral-sub-commands.php';
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/cli/class-visit-sub-commands.php';
		}
	}

	/**
	 * Setup all objects
	 *
	 * @access public
	 * @since 1.6.2
	 * @return void
	 */
	public function setup_objects() {

		self::$instance->affiliates     = new Affiliate_WP_DB_Affiliates;
		self::$instance->affiliate_meta = new Affiliate_WP_Affiliate_Meta_DB;
		self::$instance->referrals      = new Affiliate_WP_Referrals_DB;
		self::$instance->visits         = new Affiliate_WP_Visits_DB;
		self::$instance->customers      = new Affiliate_WP_Customers_DB;
		self::$instance->customer_meta  = new Affiliate_WP_Customer_Meta_DB;
		self::$instance->campaigns      = new Affiliate_WP_Campaigns_DB;
		self::$instance->settings       = new Affiliate_WP_Settings;
		self::$instance->REST           = new Affiliate_WP_REST;
		self::$instance->tracking       = new Affiliate_WP_Tracking;
		self::$instance->templates      = new Affiliate_WP_Templates;
		self::$instance->login          = new Affiliate_WP_Login;
		self::$instance->register       = new Affiliate_WP_Register;
		self::$instance->integrations   = new Affiliate_WP_Integrations;
		self::$instance->emails         = new Affiliate_WP_Emails;
		self::$instance->creatives      = new Affiliate_WP_Creatives_DB;
		self::$instance->creative       = new Affiliate_WP_Creatives;
		self::$instance->rewrites       = new Affiliate_WP_Rewrites;
		self::$instance->capabilities   = new Affiliate_WP_Capabilities;
		self::$instance->utils          = new Affiliate_WP_Utilities;

		self::$instance->updater();
	}

	/**
	 * Plugin Updater
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function updater() {

		if( ! is_admin() || ! class_exists( 'AFFWP_Plugin_Updater' ) ) {
			return;
		}

		$license_key = $this->settings->get( 'license_key' );

		// setup the updater
		$affwp_updater = new AFFWP_Plugin_Updater( 'https://affiliatewp.com', __FILE__, array(
				'version'   => AFFILIATEWP_VERSION,
				'license'   => $license_key,
				'item_name' => 'AffiliateWP',
				'item_id'   => 17,
				'author'    => 'Pippin Williamson',
				'beta'      => $this->settings->get( 'betas', false )
			)
		);

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
		$lang_dir = dirname( plugin_basename( AFFILIATEWP_PLUGIN_FILE ) ) . '/languages/';

		/**
		 * Filters the languages directory path to use for AffiliateWP.
		 *
		 * @param string $lang_dir The languages directory path.
		 */
		$lang_dir = apply_filters( 'aff_wp_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter

		global $wp_version;

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {
			$get_locale = get_user_locale();
		}

		/**
		 * Defines the plugin language locale used in AffiliateWP.
		 *
		 * @var $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
		 *                  otherwise uses `get_locale()`.
		 */
		$locale = apply_filters( 'plugin_locale', $get_locale, 'affiliate-wp' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'affiliate-wp', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/affiliate-wp/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/affiliate-wp/ folder
			load_textdomain( 'affiliate-wp', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/affiliate-wp/languages/ folder
			load_textdomain( 'affiliate-wp', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'affiliate-wp', false, $lang_dir );
		}
	}
}

endif; // End if class_exists check


/**
 * The main function responsible for returning the one true Affiliate_WP
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $affiliate_wp = affiliate_wp(); ?>
 *
 * @since 1.0
 * @return Affiliate_WP The one true Affiliate_WP Instance
 */
function affiliate_wp() {
	return Affiliate_WP::instance();
}
affiliate_wp();
