<?php
/**
 * Plugin Name: WP Security Audit Log
 * Plugin URI: http://www.wpsecurityauditlog.com/
 * Description: Identify WordPress security issues before they become a problem. Keep track of everything happening on your WordPress including WordPress users activity. Similar to Windows Event Log and Linux Syslog, WP Security Audit Log generates a security alert for everything that happens on your WordPress blogs and websites. Use the Audit Log Viewer included in the plugin to see all the security alerts.
 * Author: WP White Security
 * Version: 3.1.6
 * Text Domain: wp-security-audit-log
 * Author URI: http://www.wpsecurityauditlog.com/
 * License: GPL2
 *
 * @package Wsal
 * @fs_premium_only /extensions/
 */

/*
	WP Security Audit Log
	Copyright(c) 2014  Robert Abela  (email : robert@wpwhitesecurity.com)

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

if ( ! function_exists( 'wsal_freemius' ) ) {

	/**
	 * Freemius SDK.
	 *
	 * @since 2.7.0
	 */
	if ( file_exists( plugin_dir_path( __FILE__ ) . '/sdk/wsal-freemius.php' ) ) {
		require_once( plugin_dir_path( __FILE__ ) . '/sdk/wsal-freemius.php' );
	}

	/**
	 * WSAL Main Class.
	 *
	 * @package Wsal
	 */
	class WpSecurityAuditLog {

		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		public $version = '3.1.6';

		// Plugin constants.
		const PLG_CLS_PRFX = 'WSAL_';
		const MIN_PHP_VERSION = '5.3.0';
		const OPT_PRFX = 'wsal-';

		/**
		 * Views supervisor.
		 *
		 * @var WSAL_ViewManager
		 */
		public $views;

		/**
		 * Logger supervisor.
		 *
		 * @var WSAL_AlertManager
		 */
		public $alerts;

		/**
		 * Sensors supervisor.
		 *
		 * @var WSAL_SensorManager
		 */
		public $sensors;

		/**
		 * Settings manager.
		 *
		 * @var WSAL_Settings
		 */
		public $settings;

		/**
		 * Class loading manager.
		 *
		 * @var WSAL_Autoloader
		 */
		public $autoloader;

		/**
		 * Constants manager.
		 *
		 * @var WSAL_ConstantManager
		 */
		public $constants;

		/**
		 * Licenses manager.
		 *
		 * @var WSAL_LicenseManage
		 */
		public $licensing;

		/**
		 * Simple profiler.
		 *
		 * @var WSAL_SimpleProfiler
		 */
		public $profiler;

		/**
		 * Options.
		 *
		 * @var WSAL_DB_Option
		 */
		public $options;

		/**
		 * Contains a list of cleanup callbacks.
		 *
		 * @var callable[]
		 */
		protected $_cleanup_hooks = array();

		/**
		 * Add-ons Manager.
		 *
		 * @var object
		 */
		public $extensions;

		/**
		 * Allowed HTML Tags for strings.
		 *
		 * @var array
		 */
		public $allowed_html_tags = array();

		/**
		 * Standard singleton pattern.
		 * WARNING! To ensure the system always works as expected, AVOID using this method.
		 * Instead, make use of the plugin instance provided by 'wsal_init' action.
		 *
		 * @return WpSecurityAuditLog Returns the current plugin instance.
		 */
		public static function GetInstance() {
			static $instance = null;
			if ( ! $instance ) {
				$instance = new self();
			}
			return $instance;
		}

		/**
		 * Initialize plugin.
		 */
		public function __construct() {
			// Define important plugin constants.
			$this->define_constants();

			// Define allowed HTML tags.
			$this->set_allowed_html_tags();

			require_once( 'classes/Helpers/DataHelper.php' );

			// Profiler has to be loaded manually.
			require_once( 'classes/SimpleProfiler.php' );
			$this->profiler = new WSAL_SimpleProfiler();
			require_once( 'classes/Models/ActiveRecord.php' );
			require_once( 'classes/Models/Query.php' );
			require_once( 'classes/Models/OccurrenceQuery.php' );
			require_once( 'classes/Models/Option.php' );
			require_once( 'classes/Models/TmpUser.php' );

			// Load autoloader and register base paths.
			require_once( 'classes/Autoloader.php' );
			$this->autoloader = new WSAL_Autoloader( $this );
			$this->autoloader->Register( self::PLG_CLS_PRFX, $this->GetBaseDir() . 'classes' . DIRECTORY_SEPARATOR );

			// Load dependencies.
			$this->views = new WSAL_ViewManager( $this );
			$this->alerts = new WSAL_AlertManager( $this );
			$this->sensors = new WSAL_SensorManager( $this );
			$this->settings = new WSAL_Settings( $this );
			$this->constants = new WSAL_ConstantManager( $this );
			$this->licensing = new WSAL_LicenseManager( $this );
			$this->widgets = new WSAL_WidgetManager( $this );

			// Listen for installation event.
			register_activation_hook( __FILE__, array( $this, 'Install' ) );

			// Listen for init event.
			add_action( 'init', array( $this, 'Init' ) );

			// Listen for cleanup event.
			add_action( 'wsal_cleanup', array( $this, 'CleanUp' ) );

			// Render wsal header.
			add_action( 'admin_enqueue_scripts', array( $this, 'RenderHeader' ) );

			// Render wsal footer.
			add_action( 'admin_footer', array( $this, 'RenderFooter' ) );

			// Handle admin Disable Custom Field.
			add_action( 'wp_ajax_AjaxDisableCustomField', array( $this, 'AjaxDisableCustomField' ) );

			// Handle admin Disable Alerts.
			add_action( 'wp_ajax_AjaxDisableByCode', array( $this, 'AjaxDisableByCode' ) );

			// Render Login Page Notification.
			add_filter( 'login_message', array( $this, 'render_login_page_message' ), 10, 1 );

			// Cron job to delete alert 1003 for the last day.
			add_action( 'wsal_delete_logins', array( $this, 'delete_failed_logins' ) );
			if ( ! wp_next_scheduled( 'wsal_delete_logins' ) ) {
				wp_schedule_event( time(), 'daily', 'wsal_delete_logins' );
			}

			// Register freemius uninstall event.
			wsal_freemius()->add_action( 'after_uninstall', array( $this, 'wsal_freemius_uninstall_cleanup' ) );

			// Add filters to customize freemius welcome message.
			wsal_freemius()->add_filter( 'connect_message', array( $this, 'wsal_freemius_connect_message' ), 10, 6 );
			wsal_freemius()->add_filter( 'connect_message_on_update', array( $this, 'wsal_freemius_update_connect_message' ), 10, 6 );
		}

		/**
		 * Method: Set allowed  HTML tags.
		 *
		 * @since 3.0.0
		 */
		public function set_allowed_html_tags() {
			// Set allowed HTML tags.
			$this->allowed_html_tags = array(
				'a' => array(
					'href' => array(),
					'title' => array(),
					'target' => array(),
				),
				'br' => array(),
				'em' => array(),
				'strong' => array(),
				'p' => array(
					'class' => array(),
				),
			);
		}

		/**
		 * Method: Define constants.
		 *
		 * @since 2.6.6
		 */
		public function define_constants() {
			// Plugin version.
			if ( ! defined( 'WSAL_VERSION' ) ) {
				define( 'WSAL_VERSION', $this->version );
			}
			// Plugin Name.
			if ( ! defined( 'WSAL_BASE_NAME' ) ) {
				define( 'WSAL_BASE_NAME', plugin_basename( __FILE__ ) );
			}
			// Plugin Directory URL.
			if ( ! defined( 'WSAL_BASE_URL' ) ) {
				define( 'WSAL_BASE_URL', plugin_dir_url( __FILE__ ) );
			}
			// Plugin Directory Path.
			if ( ! defined( 'WSAL_BASE_DIR' ) ) {
				define( 'WSAL_BASE_DIR', plugin_dir_path( __FILE__ ) );
			}
			// Plugin Docs URL.
			if ( ! defined( 'WSAL_DOCS_URL' ) ) {
				define( 'WSAL_DOCS_URL', 'https://www.wpsecurityauditlog.com/support-documentation/' );
			}
			// Plugin Issue Reporting URL.
			if ( ! defined( 'WSAL_ISSUE_URL' ) ) {
				define( 'WSAL_ISSUE_URL', 'https://wordpress.org/support/plugin/wp-security-audit-log' );
			}
		}

		/**
		 * Customize Freemius connect message for new users.
		 *
		 * @param string $message - Connect message.
		 * @param string $user_first_name - User first name.
		 * @param string $plugin_title - Plugin title.
		 * @param string $user_login - Username.
		 * @param string $site_link - Site link.
		 * @param string $freemius_link - Freemius link.
		 * @return string
		 */
		function wsal_freemius_connect_message( $message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link ) {
			$freemius_link = '<a href="https://www.wpsecurityauditlog.com/support-documentation/what-is-freemius/" target="_blank" tabindex="1">freemius.com</a>';
			return sprintf(
				esc_html__( 'Hey %1$s', 'wp-security-audit-log' ) . ',<br>' .
				esc_html__( 'Never miss an important update! Opt-in to our security and feature updates notifications, and non-sensitive diagnostic tracking with freemius.com.', 'wp-security-audit-log' ) .
				'<br /><br /><strong>' . esc_html__( 'Note: ', 'wp-security-audit-log' ) . '</strong>' .
				esc_html__( 'NO AUDIT LOG ACTIVITY & DATA IS SENT BACK TO OUR SERVERS.', 'wp-security-audit-log' ),
				$user_first_name,
				'<b>' . $plugin_title . '</b>',
				'<b>' . $user_login . '</b>',
				$site_link,
				$freemius_link
			);
		}

		/**
		 * Customize Freemius connect message on update.
		 *
		 * @param string $message - Connect message.
		 * @param string $user_first_name - User first name.
		 * @param string $plugin_title - Plugin title.
		 * @param string $user_login - Username.
		 * @param string $site_link - Site link.
		 * @param string $freemius_link - Freemius link.
		 * @return string
		 */
		function wsal_freemius_update_connect_message( $message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link ) {
			$freemius_link = '<a href="https://www.wpsecurityauditlog.com/support-documentation/what-is-freemius/" target="_blank" tabindex="1">freemius.com</a>';
			return sprintf(
				esc_html__( 'Hey %1$s', 'wp-security-audit-log' ) . ',<br>' .
				esc_html__( 'Please help us improve %2$s! If you opt-in, some non-sensitive data about your usage of %2$s will be sent to %5$s, a diagnostic tracking service we use. If you skip this, that\'s okay! %2$s will still work just fine.', 'wp-security-audit-log' ) .
				'<br /><br /><strong>' . esc_html__( 'Note: ', 'wp-security-audit-log' ) . '</strong>' .
				esc_html__( 'NO AUDIT LOG ACTIVITY & DATA IS SENT BACK TO OUR SERVERS.', 'wp-security-audit-log' ),
				$user_first_name,
				'<b>' . $plugin_title . '</b>',
				'<b>' . $user_login . '</b>',
				$site_link,
				$freemius_link
			);
		}

		/**
		 * Start to trigger the events after installation.
		 *
		 * @internal
		 */
		public function Init() {
			// Start listening to events.
			WpSecurityAuditLog::GetInstance()->sensors->HookEvents();

			if ( $this->settings->IsArchivingEnabled() ) {
				// Check the current page.
				$get_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
				if ( ( ! isset( $get_page ) || 'wsal-auditlog' !== $get_page ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
					$selected_db = get_transient( 'wsal_wp_selected_db' );
					if ( $selected_db ) {
						// Delete the transient.
						delete_transient( 'wsal_wp_selected_db' );
					}
				}
			}
		}


		/**
		 * Render plugin stuff in page header.
		 *
		 * @internal
		 */
		public function RenderHeader() {
			// common.css?
		}

		/**
		 * Disable Custom Field through ajax.
		 *
		 * @internal
		 */
		public function AjaxDisableCustomField() {
			// Die if user does not have permission to disable.
			if ( ! $this->settings->CurrentUserCan( 'edit' ) ) {
				echo '<p>' . esc_html__( 'Error: You do not have sufficient permissions to disable this custom field.', 'wp-security-audit-log' ) . '</p>';
				die();
			}

			// Set filter input args.
			$filter_input_args = array(
				'disable_nonce' => FILTER_SANITIZE_STRING,
				'notice' => FILTER_SANITIZE_STRING,
			);

			// Filter $_POST array for security.
			$post_array = filter_input_array( INPUT_POST );

			if ( isset( $post_array['disable_nonce'] ) && ! wp_verify_nonce( $post_array['disable_nonce'], 'disable-custom-nonce' . $post_array['notice'] ) ) {
				die();
			}

			$fields = $this->GetGlobalOption( 'excluded-custom' );
			if ( isset( $fields ) && '' != $fields ) {
				$fields .= ',' . esc_html( $post_array['notice'] );
			} else {
				$fields = esc_html( $post_array['notice'] );
			}
			$this->SetGlobalOption( 'excluded-custom', $fields );
			echo '<p>Custom Field ' . esc_html( $post_array['notice'] ) . ' is no longer being monitored.<br />Enable the monitoring of this custom field again from the <a href="admin.php?page=wsal-settings#tab-exclude">Excluded Objects</a> tab in the plugin settings</p>';
			die;
		}

		/**
		 * Disable Alert through ajax.
		 *
		 * @internal
		 */
		public function AjaxDisableByCode() {
			// Die if user does not have permission to disable.
			if ( ! $this->settings->CurrentUserCan( 'edit' ) ) {
				echo '<p>' . esc_html__( 'Error: You do not have sufficient permissions to disable this alert.', 'wp-security-audit-log' ) . '</p>';
				die();
			}

			// Set filter input args.
			$filter_input_args = array(
				'disable_nonce' => FILTER_SANITIZE_STRING,
				'code' => FILTER_SANITIZE_STRING,
			);

			// Filter $_POST array for security.
			$post_array = filter_input_array( INPUT_POST );

			if ( isset( $post_array['disable_nonce'] ) && ! wp_verify_nonce( $post_array['disable_nonce'], 'disable-alert-nonce' . $post_array['code'] ) ) {
				die();
			}

			$s_alerts = $this->GetGlobalOption( 'disabled-alerts' );
			if ( isset( $s_alerts ) && '' != $s_alerts ) {
				$s_alerts .= ',' . esc_html( $post_array['code'] );
			} else {
				$s_alerts = esc_html( $post_array['code'] );
			}
			$this->SetGlobalOption( 'disabled-alerts', $s_alerts );
			echo '<p>Alert ' . esc_html( $post_array['code'] ) . ' is no longer being monitored.<br />';
			echo 'You can enable this alert again from the Enable/Disable Alerts node in the plugin menu.</p>';
			die;
		}

		/**
		 * Render plugin stuff in page footer.
		 *
		 * @internal
		 */
		public function RenderFooter() {
			wp_enqueue_script(
				'wsal-common',
				$this->GetBaseUrl() . '/js/common.js',
				array( 'jquery' ),
				filemtime( $this->GetBaseDir() . '/js/common.js' )
			);
		}

		/**
		 * Load the rest of the system.
		 *
		 * @internal
		 */
		public function Load() {
			$options_table = new WSAL_Models_Option();
			if ( ! $options_table->IsInstalled() ) {
				$options_table->Install();
				// Setting the prunig date with the old value or the default value.
				$pruning_date = $this->settings->GetPruningDate();
				$this->settings->SetPruningDate( $pruning_date );

				$pruning_enabled = $this->settings->IsPruningLimitEnabled();
				$this->settings->SetPruningLimitEnabled( $pruning_enabled );
				// Setting the prunig limit with the old value or the default value.
				$pruning_limit = $this->settings->GetPruningLimit();
				$this->settings->SetPruningLimit( $pruning_limit );
			}
			$log_404 = $this->GetGlobalOption( 'log-404' );
			// If old setting is empty enable 404 logging by default.
			if ( false === $log_404 ) {
				$this->SetGlobalOption( 'log-404', 'on' );
			}

			$purge_log_404 = $this->GetGlobalOption( 'purge-404-log' );
			// If old setting is empty enable 404 purge log by default.
			if ( false === $purge_log_404 ) {
				$this->SetGlobalOption( 'purge-404-log', 'on' );
			}
			// Load translations.
			load_plugin_textdomain( 'wp-security-audit-log', false, basename( dirname( __FILE__ ) ) . '/languages/' );

			// Tell the world we've just finished loading.
			$s = $this->profiler->Start( 'WSAL Init Hook' );
			do_action( 'wsal_init', $this );
			$s->Stop();

			// Hide plugin.
			if ( $this->settings->IsIncognito() ) {
				add_action( 'admin_head', array( $this, 'HidePlugin' ) );
			}

			// Update routine.
			$old_version = $this->GetOldVersion();
			$new_version = $this->GetNewVersion();
			if ( $old_version !== $new_version ) {
				$this->Update( $old_version, $new_version );
			}

			// Generate index.php for uploads directory.
			$this->settings->generate_index_files();
		}

		/**
		 * Install all assets required for a useable system.
		 */
		public function Install() {
			if ( version_compare( PHP_VERSION, self::MIN_PHP_VERSION ) < 0 ) {
				?>
				<html>
					<head>
						<link rel="stylesheet"
							href="<?php echo esc_attr( $this->GetBaseUrl() . '/css/install-error.css?v=' . filemtime( $this->GetBaseDir() . '/css/install-error.css' ) ); ?>"
							type="text/css" media="all"/>
					</head>
					<body>
						<div class="warn-wrap">
							<div class="warn-icon-tri"></div><div class="warn-icon-chr">!</div><div class="warn-icon-cir"></div>
							<?php echo sprintf( esc_html__( 'You are using a version of PHP that is older than %s, which is no longer supported.', 'wp-security-audit-log' ), self::MIN_PHP_VERSION ); ?><br />
							<?php echo wp_kses( __( 'Contact us on <a href="mailto:plugins@wpwhitesecurity.com">plugins@wpwhitesecurity.com</a> to help you switch the version of PHP you are using.', 'wp-security-audit-log' ), $this->allowed_html_tags ); ?>
						</div>
					</body>
				</html>
				<?php
				die( 1 );
			}

			// Ensure that the system is installed and schema is correct.
			self::getConnector()->installAll();

			$pre_installed = $this->IsInstalled();

			// If system already installed, do updates now (if any).
			$old_version = $this->GetOldVersion();
			$new_version = $this->GetNewVersion();

			if ( $pre_installed && $old_version != $new_version ) {
				$this->Update( $old_version, $new_version );
			}

			// Load options from wp_options table or wp_sitemeta in multisite enviroment.
			$data = $this->read_options_prefixed( 'wsal-' );
			if ( ! empty( $data ) ) {
				$this->SetOptions( $data );
			}
			$this->deleteAllOptions();

			// If system wasn't installed, try migration now.
			if ( ! $pre_installed && $this->CanMigrate() ) {
				$this->Migrate();
			}

			// Setting the prunig date with the old value or the default value.
			$pruning_date = $this->settings->GetPruningDate();
			$this->settings->SetPruningDate( $pruning_date );

			// $pruning_enabled = $this->settings->IsPruningLimitEnabled();
			$this->settings->SetPruningLimitEnabled( true );
			// Setting the prunig limit with the old value or the default value.
			$pruning_limit = $this->settings->GetPruningLimit();
			$this->settings->SetPruningLimit( $pruning_limit );

			$old_disabled = $this->GetGlobalOption( 'disabled-alerts' );
			// If old setting is empty disable alert 2099 by default.
			if ( empty( $old_disabled ) ) {
				$this->settings->SetDisabledAlerts( array( 2099, 2126 ) );
			}

			$log_404 = $this->GetGlobalOption( 'log-404' );
			// If old setting is empty enable 404 logging by default.
			if ( false === $log_404 ) {
				$this->SetGlobalOption( 'log-404', 'on' );
			}

			$purge_log_404 = $this->GetGlobalOption( 'purge-404-log' );
			// If old setting is empty enable 404 purge log by default.
			if ( false === $purge_log_404 ) {
				$this->SetGlobalOption( 'purge-404-log', 'on' );
			}

			// Install cleanup hook (remove older one if it exists).
			wp_clear_scheduled_hook( 'wsal_cleanup' );
			wp_schedule_event( current_time( 'timestamp' ) + 600, 'hourly', 'wsal_cleanup' );
		}

		/**
		 * Run some code that updates critical components required for a newwer version.
		 *
		 * @param string $old_version The old version.
		 * @param string $new_version The new version.
		 */
		public function Update( $old_version, $new_version ) {
			// Update version in db.
			$this->SetGlobalOption( 'version', $new_version );

			// Disable all developer options.
			// $this->settings->ClearDevOptions();
			// Do version-to-version specific changes.
			if ( -1 === version_compare( $old_version, $new_version ) ) {
				// Update External DB password on plugin update.
				$this->update_external_db_password();

				// Update pruning alerts option.
				$this->settings->SetPruningDate( '12 months' );
			}
		}

		/**
		 * Method: Update external DB password.
		 *
		 * @since 2.6.3
		 */
		public function update_external_db_password() {
			// Get the passwords.
			$external_password  = $this->settings->GetAdapterConfig( 'adapter-password' );
			$mirror_password    = $this->settings->GetAdapterConfig( 'mirror-password' );
			$archive_password   = $this->settings->GetAdapterConfig( 'archive-password' );

			// Update external db password.
			if ( ! empty( $external_password ) ) {
				// Decrypt the password using fallback method.
				$password   = $this->getConnector()->decryptString_fallback( $external_password );

				// Encrypt the password with latest encryption method.
				$encrypted_password   = $this->getConnector()->encryptString( $password );

				// Store the new password.
				$this->settings->SetAdapterConfig( 'adapter-password', $encrypted_password );
			}

			// Update mirror db password.
			if ( ! empty( $mirror_password ) ) {
				// Decrypt the password using fallback method.
				$password   = $this->getConnector()->decryptString_fallback( $mirror_password );

				// Encrypt the password with latest encryption method.
				$encrypted_password   = $this->getConnector()->encryptString( $password );

				// Store the new password.
				$this->settings->SetAdapterConfig( 'mirror-password', $encrypted_password );
			}

			// Update archive db password.
			if ( ! empty( $archive_password ) ) {
				// Decrypt the password using fallback method.
				$password   = $this->getConnector()->decryptString_fallback( $archive_password );

				// Encrypt the password with latest encryption method.
				$encrypted_password   = $this->getConnector()->encryptString( $password );

				// Store the new password.
				$this->settings->SetAdapterConfig( 'archive-password', $encrypted_password );
			}
		}

		/**
		 * Method: Freemius method to run after uninstall event.
		 *
		 * @since 2.7.0
		 */
		public function wsal_freemius_uninstall_cleanup() {
			// Call the uninstall routine of the plugin.
			$this->Uninstall();
		}

		/**
		 * Uninstall plugin.
		 */
		public function Uninstall() {
			if ( $this->GetGlobalOption( 'delete-data' ) == 1 ) {
				self::getConnector()->uninstallAll();
				$this->deleteAllOptions();
			}
			wp_clear_scheduled_hook( 'wsal_cleanup' );
		}

		/**
		 * Delete from the options table of WP.
		 *
		 * @param string $prefix - Table prefix.
		 * @return boolean - Query result.
		 */
		public function delete_options_prefixed( $prefix ) {
			global $wpdb;
			if ( $this->IsMultisite() ) {
				$table_name = $wpdb->prefix . 'sitemeta';
				$result = $wpdb->query( "DELETE FROM {$table_name} WHERE meta_key LIKE '{$prefix}%'" );
			} else {
				$result = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'" );
			}
			return ($result) ? true : false;
		}

		/**
		 * Delete all the Wsal options from the options table of WP.
		 */
		private function deleteAllOptions() {
			$flag = true;
			while ( $flag ) {
				$flag = $this->delete_options_prefixed( self::OPT_PRFX );
			}
		}

		/**
		 * Read options from the options table of WP.
		 *
		 * @param string $prefix - Table prefix.
		 * @return boolean - Query result.
		 */
		public function read_options_prefixed( $prefix ) {
			global $wpdb;
			if ( $this->IsMultisite() ) {
				$table_name = $wpdb->prefix . 'sitemeta';
				$results = $wpdb->get_results( "SELECT site_id,meta_key,meta_value FROM {$table_name} WHERE meta_key LIKE '{$prefix}%'", ARRAY_A );
			} else {
				$results = $wpdb->get_results( "SELECT option_name,option_value FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'", ARRAY_A );
			}
			return $results;
		}

		/**
		 * Set options in the Wsal options table.
		 *
		 * @param array $data - Table prefix.
		 */
		public function SetOptions( $data ) {
			foreach ( $data as $key => $option ) {
				$this->options = new WSAL_Models_Option();
				if ( $this->IsMultisite() ) {
					$this->options->SetOptionValue( $option['meta_key'], $option['meta_value'] );
				} else {
					$this->options->SetOptionValue( $option['option_name'], $option['option_value'] );
				}
			}
		}

		/**
		 * Migrate data from old plugin.
		 */
		public function Migrate() {
			global $wpdb;
			static $mig_types = array(
				3000 => 5006,
			);

			// Load data.
			$sql = 'SELECT * FROM ' . $wpdb->base_prefix . 'wordpress_auditlog_events';
			$events = array();
			foreach ( $wpdb->get_results( $sql, ARRAY_A ) as $item ) {
				$events[ $item['EventID'] ] = $item;
			}
			$sql = 'SELECT * FROM ' . $wpdb->base_prefix . 'wordpress_auditlog';
			$auditlog = $wpdb->get_results( $sql, ARRAY_A );

			// Migrate using db logger.
			foreach ( $auditlog as $entry ) {
				$data = array(
					'ClientIP' => $entry['UserIP'],
					'UserAgent' => '',
					'CurrentUserID' => $entry['UserID'],
				);
				if ( $entry['UserName'] ) {
					$data['Username'] = base64_decode( $entry['UserName'] );
				}
				$mesg = $events[ $entry['EventID'] ]['EventDescription'];
				$date = strtotime( $entry['EventDate'] );
				$type = $entry['EventID'];
				if ( isset( $mig_types[ $type ] ) ) {
					$type = $mig_types[ $type ];
				}
				// Convert message from '<strong>%s</strong>' to '%Arg1%' format.
				$c = 0;
				$n = '<strong>%s</strong>';
				$l = strlen( $n );
				while ( ($pos = strpos( $mesg, $n )) !== false ) {
					$mesg = substr_replace( $mesg, '%MigratedArg' . ($c++) . '%', $pos, $l );
				}
				$data['MigratedMesg'] = $mesg;
				// Generate new meta data args.
				$temp = unserialize( base64_decode( $entry['EventData'] ) );
				foreach ( (array) $temp as $i => $item ) {
					$data[ 'MigratedArg' . $i ] = $item;
				}
				// send event data to logger!
				foreach ( $this->alerts->GetLoggers() as $logger ) {
					$logger->Log( $type, $data, $date, $entry['BlogId'], true );
				}
			}

			// Migrate settings.
			$this->settings->SetAllowedPluginEditors(
				get_option( 'WPPH_PLUGIN_ALLOW_CHANGE' )
			);
			$this->settings->SetAllowedPluginViewers(
				get_option( 'WPPH_PLUGIN_ALLOW_ACCESS' )
			);
			$s = get_option( 'wpph_plugin_settings' );
			// $this->settings->SetPruningDate(($s->daysToKeep ? $s->daysToKeep : 30) . ' days');
			// $this->settings->SetPruningLimit(min($s->eventsToKeep, 1));
			$this->settings->SetViewPerPage( max( $s->showEventsViewList, 5 ) );
			$this->settings->SetWidgetsEnabled( ! ! $s->showDW );
		}

		/**
		 * The current plugin version (according to plugin file metadata).
		 *
		 * @return string
		 */
		public function GetNewVersion() {
			$version = get_plugin_data( __FILE__, false, false );
			return isset( $version['Version'] ) ? $version['Version'] : '0.0.0';
		}

		/**
		 * The plugin version as stored in DB (will be the old version during an update/install).
		 *
		 * @return string
		 */
		public function GetOldVersion() {
			return $this->GetGlobalOption( 'version', '0.0.0' );
		}

		/**
		 * To be called in admin header for hiding plugin form Plugins list.
		 *
		 * @internal
		 */
		public function HidePlugin() {
			$selectr = '';
			$plugins = array( 'wp-security-audit-log' );
			foreach ( $plugins as $value ) {
				$selectr .= '.wp-list-table.plugins tr[data-slug="' . $value . '"], ';
			}
			?>
			<style type="text/css">
				<?php echo rtrim( $selectr, ', ' ); ?> { display: none; }
			</style>
			<?php
		}

		/**
		 * Returns the class name of a particular file that contains the class.
		 *
		 * @param string $file - File name.
		 * @return string - Class name.
		 * @deprecated since 1.2.5 Use autoloader->GetClassFileClassName() instead.
		 */
		public function GetClassFileClassName( $file ) {
			return $this->autoloader->GetClassFileClassName( $file );
		}

		/**
		 * Return whether we are running on multisite or not.
		 *
		 * @return boolean
		 */
		public function IsMultisite() {
			return function_exists( 'is_multisite' ) && is_multisite();
		}

		/**
		 * Get a global option.
		 *
		 * @param string $option - Option name.
		 * @param mixed  $default - (Optional) Value returned when option is not set (defaults to false).
		 * @param string $prefix - (Optional) A prefix used before option name.
		 * @return mixed - Option's value or $default if option not set.
		 */
		public function GetGlobalOption( $option, $default = false, $prefix = self::OPT_PRFX ) {
			$this->options = new WSAL_Models_Option();
			return $this->options->GetOptionValue( $prefix . $option, $default );
		}

		/**
		 * Set a global option.
		 *
		 * @param string $option - Option name.
		 * @param mixed  $value - New value for option.
		 * @param string $prefix - (Optional) A prefix used before option name.
		 */
		public function SetGlobalOption( $option, $value, $prefix = self::OPT_PRFX ) {
			$this->options = new WSAL_Models_Option();
			$this->options->SetOptionValue( $prefix . $option, $value );
		}

		/**
		 * Get a user-specific option.
		 *
		 * @param string $option - Option name.
		 * @param mixed  $default - (Optional) Value returned when option is not set (defaults to false).
		 * @param string $prefix - (Optional) A prefix used before option name.
		 * @return mixed - Option's value or $default if option not set.
		 */
		public function GetUserOption( $option, $default = false, $prefix = self::OPT_PRFX ) {
			$result = get_user_option( $prefix . $option, get_current_user_id() );
			return false === $result ? $default : $result;
		}

		/**
		 * Set a user-specific option.
		 *
		 * @param string $option - Option name.
		 * @param mixed  $value - New value for option.
		 * @param string $prefix - (Optional) A prefix used before option name.
		 */
		public function SetUserOption( $option, $value, $prefix = self::OPT_PRFX ) {
			update_user_option( get_current_user_id(), $prefix . $option, $value, false );
		}

		/**
		 * Run cleanup routines.
		 */
		public function CleanUp() {
			$s = $this->profiler->Start( 'Clean Up' );
			foreach ( $this->_cleanup_hooks as $hook ) {
				call_user_func( $hook );
			}
			$s->Stop();
		}

		/**
		 * Clear last day's failed login alert.
		 */
		public function delete_failed_logins() {
			// Set the dates.
			list( $y, $m, $d ) = explode( '-', date( 'Y-m-d' ) );

			// Site id.
			$site_id = (function_exists( 'get_current_blog_id' ) ? get_current_blog_id() : 0);

			// New occurrence object.
			$occurrence = new WSAL_Models_Occurrence();
			$alerts = $occurrence->check_alert_1003(
				array(
					1003,
					$site_id,
					mktime( 0, 0, 0, $m, $d - 1, $y ) + 1,
					mktime( 0, 0, 0, $m, $d, $y ),
				)
			);

			// Alerts exists then continue.
			if ( ! empty( $alerts ) ) {
				foreach ( $alerts as $alert ) {
					// Flush the usernames meta data.
					$alert->UpdateMetaValue( 'Users', array() );
				}
			}
		}

		/**
		 * Add callback to be called when a cleanup operation is required.
		 *
		 * @param callable $hook - Hook name.
		 */
		public function AddCleanupHook( $hook ) {
			$this->_cleanup_hooks[] = $hook;
		}

		/**
		 * Remove a callback from the cleanup callbacks list.
		 *
		 * @param callable $hook - Hook name.
		 */
		public function RemoveCleanupHook( $hook ) {
			while ( ($pos = array_search( $hook, $this->_cleanup_hooks )) !== false ) {
				unset( $this->_cleanup_hooks[ $pos ] );
			}
		}

		/**
		 * DB connection.
		 *
		 * @param mixed $config DB configuration.
		 * @param bool  $reset - True if reset.
		 * @return WSAL_Connector_ConnectorInterface
		 */
		public static function getConnector( $config = null, $reset = false ) {
			return WSAL_Connector_ConnectorFactory::getConnector( $config, $reset );
		}

		/**
		 * Do we have an existing installation? This only applies for version 1.0 onwards.
		 *
		 * @return boolean
		 */
		public function IsInstalled() {
			return self::getConnector()->isInstalled();
		}

		/**
		 * Whether the old plugin was present or not.
		 *
		 * @return boolean
		 */
		public function CanMigrate() {
			return self::getConnector()->canMigrate();
		}

		/**
		 * Absolute URL to plugin directory WITHOUT final slash.
		 *
		 * @return string
		 */
		public function GetBaseUrl() {
			return plugins_url( '', __FILE__ );
		}

		/**
		 * Full path to plugin directory WITH final slash.
		 *
		 * @return string
		 */
		public function GetBaseDir() {
			return plugin_dir_path( __FILE__ );
		}

		/**
		 * Plugin directory name.
		 *
		 * @return string
		 */
		public function GetBaseName() {
			return plugin_basename( __FILE__ );
		}

		/**
		 * Load default configuration / data.
		 */
		public function LoadDefaults() {
			$s = $this->profiler->Start( 'Load Defaults' );
			require_once( 'defaults.php' );
			$s->Stop();
		}

		/**
		 * WSAL-Notifications-Extension Functions.
		 *
		 * @param string $opt_prefix - Option prefix.
		 */
		public function GetNotificationsSetting( $opt_prefix ) {
			$this->options = new WSAL_Models_Option();
			return $this->options->GetNotificationsSetting( self::OPT_PRFX . $opt_prefix );
		}

		/**
		 * Get notification.
		 *
		 * @param int $id - Option ID.
		 */
		public function GetNotification( $id ) {
			$this->options = new WSAL_Models_Option();
			return $this->options->GetNotification( $id );
		}

		/**
		 * Delete option by name.
		 *
		 * @param string $name - Option name.
		 */
		public function DeleteByName( $name ) {
			$this->options = new WSAL_Models_Option();
			return $this->options->DeleteByName( $name );
		}

		/**
		 * Delete option by prefix.
		 *
		 * @param string $opt_prefix - Option prefix.
		 */
		public function DeleteByPrefix( $opt_prefix ) {
			$this->options = new WSAL_Models_Option();
			return $this->options->DeleteByPrefix( self::OPT_PRFX . $opt_prefix );
		}

		/**
		 * Count notifications.
		 *
		 * @param string $opt_prefix - Option prefix.
		 */
		public function CountNotifications( $opt_prefix ) {
			$this->options = new WSAL_Models_Option();
			return $this->options->CountNotifications( self::OPT_PRFX . $opt_prefix );
		}

		/**
		 * Update global option.
		 *
		 * @param string $option - Option name.
		 * @param mix    $value - Option value.
		 */
		public function UpdateGlobalOption( $option, $value ) {
			$this->options = new WSAL_Models_Option();
			return $this->options->SetOptionValue( $option, $value );
		}

		/**
		 * Method: Render login page message.
		 *
		 * @param string $message - Login message.
		 */
		public function render_login_page_message( $message ) {
			// Check if the option is enabled.
			$login_message_enabled = $this->settings->is_login_page_notification();
			if ( 'true' === $login_message_enabled
				|| ( ! $login_message_enabled && 'false' !== $login_message_enabled ) ) {
				// Get login message.
				$message = $this->settings->get_login_page_notification_text();

				// Default message.
				if ( ! $message ) {
					$message = wp_kses( __( '<p class="message">For security and auditing purposes, a record of all of your logged-in actions and changes within the WordPress dashboard will be recorded in an audit log with the <a href="https://www.wpsecurityauditlog.com/" target="_blank">WP Security Audit Log plugin</a>. The audit log also includes the IP address where you accessed this site from.</p>', 'wp-security-audit-log' ), $this->allowed_html_tags );
				} else {
					$message = '<p class="message">' . $message . '</p>';
				}
			}

			// Return message.
			return $message;
		}

		/**
		 * Error Logger
		 *
		 * Logs given input into debug.log file in debug mode.
		 *
		 * @param mix $message - Error message.
		 */
		function wsal_log( $message ) {
			if ( WP_DEBUG === true ) {
				if ( is_array( $message ) || is_object( $message ) ) {
					error_log( print_r( $message, true ) );
				} else {
					error_log( $message );
				}
			}
		}
	}

	// Profile WSAL load time.
	$s = WpSecurityAuditLog::GetInstance()->profiler->Start( 'WSAL Init' );

	// Begin load sequence.
	add_action( 'plugins_loaded', array( WpSecurityAuditLog::GetInstance(), 'Load' ) );

	// Load extra files.
	WpSecurityAuditLog::GetInstance()->LoadDefaults();

	// End profile snapshot.
	$s->Stop();

	// Create & Run the plugin.
	return WpSecurityAuditLog::GetInstance();

}
