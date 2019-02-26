<?php
/**
 * Plugin Name: AffiliateWP MLM
 * Plugin URI: http://theperfectplugin.com/downloads/affiliatewp-mlm
 * Description: Turn your Affiliate Network into a full blown Multi-Level Marketing system, where your Affiliates can earn commissions on the referrals made by their Sub Affiliates on multiple levels.
 * Author: Christian Freeman
 * Author URI: http://theperfectplugin.com
 * Version: 1.1.2
 * Text Domain: affiliatewp-multi-level-marketing
 * Domain Path: languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AFFWP_MLM_License {

	static function init() {

		if ( ! class_exists( 'AFFWP_MLM_License_Menu' ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'am-license-menu.php' );
			
			AFFWP_MLM_License_Menu::instance( __FILE__, 'AffiliateWP MLM', '1.1.2', 'plugin', 'http://propluginmarketplace.com' );
		}
	}

}
AFFWP_MLM_License::init();

if ( ! class_exists( 'AffiliateWP_Multi_Level_Marketing' ) ) {

	final class AffiliateWP_Multi_Level_Marketing {

		/**
		 * Plugin instance.
		 *
		 * @see instance()
		 * @type object
		 */
		private static $instance;

		/**
		 * URL to this plugin's directory.
		 *
		 * @type string
		 */
		public static  $plugin_dir;
		public static  $plugin_url;
		private static $version;

		/**
		 * The settings instance variable
		 *
		 * @var AffiliateWP_MLM_Settings
		 * @since 1.0
		 */
		public $settings;

		/**
		 * Class Properties
		 *
		 * @var AffiliateWP_MLM_Emails
		 * @since 1.0.3
		 */
		// public $emails;
	
		/**
		 * The integrations handler instance variable
		 *
		 * @var AffiliateWP_MLM_Base
		 * @since 1.0
		 */
		public $integrations;

		/**
		 * Main AffiliateWP_Multi_Level_Marketing Instance
		 *
		 * Insures that only one instance of AffiliateWP_Multi_Level_Marketing exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 * @staticvar array $instance
		 * @return The one true AffiliateWP_Multi_Level_Marketing
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Multi_Level_Marketing ) ) {
				
				self::$instance = new AffiliateWP_Multi_Level_Marketing;
				self::$version  = '1.1.2';

				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->setup_objects();
				self::$instance->hooks();
				self::$instance->load_textdomain();

			}

			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0.3
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-multi-level-marketing' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 1.0.3
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-multi-level-marketing' ), '1.0' );
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0.3
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0.3
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
			if ( ! defined( 'AFFWP_MLM_VERSION' ) ) {
				define( 'AFFWP_MLM_VERSION', self::$version );
			}

			// Plugin Folder Path
			if ( ! defined( 'AFFWP_MLM_PLUGIN_DIR' ) ) {
				define( 'AFFWP_MLM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'AFFWP_MLM_PLUGIN_URL' ) ) {
				define( 'AFFWP_MLM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'AFFWP_MLM_PLUGIN_FILE' ) ) {
				define( 'AFFWP_MLM_PLUGIN_FILE', __FILE__ );
			}
			
			// API URL
			if ( ! defined( 'AFFWP_MLM_API_URL' ) ) {
				define( 'AFFWP_MLM_API_URL', 'http://propluginmarketplace.com' );
			}
			
			// Software Title
			if ( ! defined( 'AFFWP_MLM_SOFTWARE_TITLE' ) ) {
				define( 'AFFWP_MLM_SOFTWARE_TITLE', 'AffiliateWP MLM' );
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
			
			require_once AFFWP_MLM_PLUGIN_DIR . 'includes/admin/class-settings.php';

			require_once AFFWP_MLM_PLUGIN_DIR . 'includes/install.php';
			require_once AFFWP_MLM_PLUGIN_DIR . 'includes/db-functions.php';
			require_once AFFWP_MLM_PLUGIN_DIR . 'includes/functions.php';
			require_once AFFWP_MLM_PLUGIN_DIR . 'includes/scripts.php';
			require_once AFFWP_MLM_PLUGIN_DIR . 'includes/actions.php';
			require_once AFFWP_MLM_PLUGIN_DIR . 'includes/filters.php';
			require_once AFFWP_MLM_PLUGIN_DIR . 'includes/display-functions.php';
			require_once AFFWP_MLM_PLUGIN_DIR . 'includes/compatibility.php';
			require_once AFFWP_MLM_PLUGIN_DIR . 'includes/shortcodes.php';

			// require_once AFFWP_MLM_PLUGIN_DIR . 'includes/class-emails.php';
			require_once AFFWP_MLM_PLUGIN_DIR . 'integrations/class-base.php';


			// Load the class for each integration enabled
			foreach ( affiliate_wp()->integrations->get_enabled_integrations() as $filename => $integration ) {

				if ( file_exists( AFFWP_MLM_PLUGIN_DIR . 'integrations/class-' . $filename . '.php' ) ) {
					require_once AFFWP_MLM_PLUGIN_DIR . 'integrations/class-' . $filename . '.php';
				}

			}

		}

		/**
		 * Setup all objects
		 *
		 * @access public
		 * @since 1.0.5
		 * @return void
		 */
		public function setup_objects() {
			self::$instance->settings = new AffiliateWP_MLM_Settings;
			// self::$instance->emails = new AffiliateWP_MLM_Emails;
			self::$instance->integrations = new AffiliateWP_MLM_Base;
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0.3
		 *
		 * @return void
		 */
		private function hooks() {
		
			// Plugin meta
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );
			
			// Add sub affiliates tab
			add_action( 'affwp_affiliate_dashboard_tabs', array( $this, 'add_sub_affiliates_tab' ), 10, 2 );
			
			// Add template folder to hold the sub affiliates tab content
			add_filter( 'affwp_template_paths', array( $this, 'get_theme_template_paths' ) );

			// Add to the tabs list for 1.8.1 (fails silently if the hook doesn't exist).
			add_filter( 'affwp_affiliate_area_tabs', array( $this, 'register_tab' ), 10, 1 );
			
			// Add indirect referrals table to referrals tab
			add_action( 'affwp_referrals_dashboard_before_table', array( $this, 'display_indirect_referrals_in_tab' ) );

		}
		
		/**
		 * Modify plugin metalinks
		 *
		 * @access      public
		 * @since       1.0.3
		 * @param       array $links The current links array
		 * @param       string $file A specific plugin table entry
		 * @return      array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
		    if ( $file == plugin_basename( __FILE__ ) ) {
		        $plugins_link = array(
		            '<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliatewp-multi-level-marketing' ) . '" href="http://propluginmarketplace.com/product-category/add-ons/affiliatewp/" target="_blank">' . __( 'Get add-ons', 'affiliatewp-multi-level-marketing' ) . '</a>'
		        );

		        $links = array_merge( $links, $plugins_link );
		    }

		    return $links;
		}

		/**
		 * Whether or not we're on the sub affiliates tab of the dashboard
		 *
		 * @since 1.0.3
		 *
		 * @return boolean
		 */
		public function is_sub_affiliates_tab() {
			if ( isset( $_GET['tab']) && 'sub-affiliates' == $_GET['tab'] ) {
				return (bool) true;
			}
	
			return (bool) false;
		}

		/**
		 * Register the Sub Affiliates tab
		 * 
		 * @return array $tabs The list of tabs
		 */
		public function register_tab( $tabs ) {

			// Use the older method of registering the tab
			if ( ! $this->has_2_1_7() ) {
				return array_merge( $tabs, array( 'sub-affiliates' ) );
			}

			// Register the tab for AffiliateWP 2.1.7 +
			$tabs['sub-affiliates'] = __( 'Sub Affiliates', 'affiliatewp-multi-level-marketing' );

			return $tabs;
		}
		
		/**
		 * Add sub affiliates tab
		 *
		 * @since 1.0.3
		 *
		 * @return void
		 */
		public function add_sub_affiliates_tab( $affiliate_id, $active_tab ) {
			
			// Return early if user has AffiliateWP 2.1.7 or newer. This method is no longer needed.
			if ( $this->has_2_1_7() ) return;	
			
			?>
            <li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'sub-affiliates' ? ' active' : ''; ?>">
                <a href="<?php echo esc_url( add_query_arg( 'tab', 'sub-affiliates' ) ); ?>"><?php _e( 'Sub Affiliates', 'affiliatewp-multi-level-marketing' ); ?></a>
            </li>
		<?php	
		}

		/**
		 * Determine if the user has at least version 2.1.7 of AffiliateWP.
		 *
		 * @since 1.0
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
		 * Add template folder to hold the sub affiliates tab content
		 *
		 * @since 1.0.3
		 *
		 * @return void
		 */
		public function get_theme_template_paths( $file_paths ) {
			$file_paths[90] = plugin_dir_path( __FILE__ ) . '/templates';
	
			return $file_paths;
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
			$lang_dir = apply_filters( 'affiliatewp_multi_level_marketing_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-multi-level-marketing' );
			$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliatewp-multi-level-marketing', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-multi-level-marketing/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-multi-level-marketing/ folder
				load_textdomain( 'affiliatewp-multi-level-marketing', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-multi-level-marketing/languages/ folder
				load_textdomain( 'affiliatewp-multi-level-marketing', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affiliatewp-multi-level-marketing', false, $lang_dir );
			}
		}

		/**
		 * Get the referrals that were generated by a parent affiliate's sub affiliates
		 *
		 * @since  1.0
		 */
		public function get_sub_affiliate_referrals( $args = array() ) {

			$referral_type = 'indirect';
		
			$defaults = array(
				'number'       => -1,
				'offset'       => 0,
				'referrals_id' => 0,
				'affiliate_id' => affwp_get_affiliate_id(),
				'context'      => '',
				'status'       => array( 'paid', 'unpaid', 'rejected' )
			);

			$args  = wp_parse_args( $args, $defaults );

			// get the affiliate's referrals
			$referrals = affiliate_wp()->referrals->get_referrals(
				array(
					'number'       => $args['number'],
					'offset'       => $args['offset'],
					'referrals_id' => $args['referrals_id'],
					'affiliate_id' => $args['affiliate_id'],
					'context'      => $args['context'],
					'status'       => $args['status']
				)
			);

			// Only show referrals by type
			if ( $referrals ) {
				foreach ( $referrals as $key => $referral ) {
				
					$sub_affiliate_order = $referral->custom == $referral_type ? $referral->custom : '';
		
					if ( ! $sub_affiliate_order ) {
						unset( $referrals[$key] );
					}
		
				}
		
				return $referrals;
			}

		}

		/**
		 * Count sub affiliate referrals
		 *
		 * @since  1.0
		 */
		public function count_sub_affiliate_referrals( $affiliate_id = 0 ) {
	
			if( empty( $affiliate_id ) ) {
				// Get the specified affiliate's indirect referrals
				$referrals = $this->get_sub_affiliate_referrals( array( 'affiliate_id' => $affiliate_id ) );
			} else {
				// Get the current affiliate's indirect referrals
				$referrals = $this->get_sub_affiliate_referrals();
			}
		
			return count( $referrals );
		}

		/**
		 * Display indirect referrals in the referrals tab of the affiliate area
		 * 
		 * @since  1.0
		 * @param  integer $affiliate_id ID of the affiliate from the filter
		 */
		public function display_indirect_referrals_in_tab( $affiliate_id ) {
			if ( isset( $_GET['tab'] ) && 'referrals' != $_GET['tab'] ) {
				return;
			}
			
			show_indirect_referrals( $affiliate_id, '#affwp-affiliate-dashboard-referrals', 'referrals' );
		}

	}

	/**
	 * The main function responsible for returning the one true AffiliateWP_Multi_Level_Marketing
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affiliatewp_multi_level_marketing = affiliate_wp_mlm(); ?>
	 *
	 * @since 1.0
	 * @return object The one true AffiliateWP_Multi_Level_Marketing Instance
	 */
	function affiliate_wp_mlm() {

	    if ( ! class_exists( 'Affiliate_WP' ) ) {
	    	
	        if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
	            require_once 'includes/class-activation.php';
	        }

	        $activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
	        $activation = $activation->run();
	    } else {
	        return AffiliateWP_Multi_Level_Marketing::instance();
	    }
	}
	add_action( 'plugins_loaded', 'affiliate_wp_mlm', 100 );

}

/**
 * Installation
 * 
 * Registering the hook inside the 'plugins_loaded' hook will not work. 
 * You can't call register_activation_hook() inside a function hooked to the 'plugins_loaded' or 'init' hooks (or any other hook). 
 * These hooks are called before the plugin is loaded or activated.
 *
 * @since 1.0
*/
function affiliatewp_mlm_plugin_activate() {

	add_option( 'affwp_mlm_activated', true );

}
register_activation_hook( __FILE__, 'affiliatewp_mlm_plugin_activate' );

function affiliate_wp_mlm_load_plugin() {

	include_once dirname( __FILE__ ) . '/includes/install.php';

    if ( is_admin() && get_option( 'affwp_mlm_activated' ) == true ) {

        delete_option( 'affwp_mlm_activated' );

        // run install script
        affiliatewp_mlm_install();
    }

}
add_action( 'admin_init', 'affiliate_wp_mlm_load_plugin' );
