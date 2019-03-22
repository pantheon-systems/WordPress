<?php //phpcs:disable WordPress.Files.FileName
/**
 * Plugin Name: EBANX Payment Gateway for WooCommerce
 * Plugin URI: https://www.ebanx.com/business/en/developers/integrations/extensions-and-plugins/woocommerce-plugin
 * Description: Offer Latin American local payment methods & increase your conversion rates with the solution used by AliExpress, AirBnB and Spotify in Brazil.
 * Author: EBANX
 * Author URI: https://www.ebanx.com/business/en
 * Version: 1.38.0
 * License: MIT
 * Text Domain: woocommerce-gateway-ebanx
 * Domain Path: /languages
 *
 * @package WooCommerce_EBANX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_EBANX_MIN_PHP_VER', '5.6.0' );
define( 'WC_EBANX_MIN_WC_VER', '2.6.0' );
define( 'WC_EBANX_MIN_WP_VER', '4.0.0' );
define( 'WC_EBANX_DIR', __DIR__ . DIRECTORY_SEPARATOR );
define( 'WC_EBANX_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) . DIRECTORY_SEPARATOR );
define( 'WC_EBANX_PLUGIN_NAME', WC_EBANX_PLUGIN_DIR_URL . basename( __FILE__ ) );
define( 'WC_EBANX_GATEWAYS_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'gateways' . DIRECTORY_SEPARATOR );
define( 'WC_EBANX_SERVICES_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'services' . DIRECTORY_SEPARATOR );
define( 'WC_EBANX_EXCEPTIONS_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'exceptions' . DIRECTORY_SEPARATOR );
define( 'WC_EBANX_LANGUAGES_DIR', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
define( 'WC_EBANX_TEMPLATES_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR );
define( 'WC_EBANX_VENDOR_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR );
define( 'WC_EBANX_ASSETS_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR );
define( 'WC_EBANX_CONTROLLERS_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR );
define( 'WC_EBANX_DATABASE_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR );

if ( ! class_exists( 'WC_EBANX' ) ) {
	/**
	 * Hooks
	 */
	register_activation_hook( __FILE__, array( 'WC_EBANX', 'activate_plugin' ) );
	register_deactivation_hook( __FILE__, array( 'WC_EBANX', 'deactivate_plugin' ) );

	include_once WC_EBANX_DATABASE_DIR . 'class-wc-ebanx-database.php';
	register_activation_hook( __FILE__, array( 'WC_EBANX_Database', 'migrate' ) );

	/**
	 * WooCommerce WC_EBANX main class.
	 */
	class WC_EBANX {

		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		const DIR = __FILE__;

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 *
		 * @var $log
		 */
		private static $log;

		/**
		 *
		 * @var string $my_account_endpoint
		 */
		private static $my_account_endpoint = 'ebanx-saved-cards';

		/**
		 *
		 * @var string $my_account_name
		 */
		private static $my_account_menu_name = 'EBANX - Saved Cards';

		/**
		 *
		 * @var array $settings
		 */
		private $settings = [];

		/**
		 * Initialize the plugin public actions.
		 */
		private function __construct() {
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-notice.php';

			$this->notices = new WC_EBANX_Notice();

			if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
				$this->notices
					->with_view( 'missing-woocommerce' )
					->enqueue();
				return;
			}

			/**
			 * Includes.
			 */
			$this->includes();

			$configs = new WC_EBANX_Global_Gateway();

			/**
			 * Actions.
			 */
			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
			add_action( 'wp_loaded', array( $this, 'enable_i18n' ) );

			add_action( 'init', array( $this, 'ebanx_router' ) );
			add_action( 'init', array( 'WC_EBANX_Third_Party_Compability_Layer', 'check_and_solve' ) );
			add_action( 'admin_init', array( $this, 'ebanx_sidebar_shortcut' ) );
			add_action( 'admin_init', array( 'WC_EBANX_Flash', 'enqueue_admin_messages' ) );

			if ( WC_EBANX_Request::is_post_empty() ) {
				add_action( 'admin_init', array( $this, 'setup_configs' ), 10 );
				add_action( 'admin_init', array( $this, 'checker' ), 30 );
			}

			add_action( 'admin_head', array( 'WC_EBANX_Capture_Payment', 'add_order_capture_button_css' ) );

			add_action( 'woocommerce_order_actions', array( 'WC_EBANX_Capture_Payment', 'add_auto_capture_dropdown' ) );
			add_action( 'woocommerce_order_action_ebanx_capture_order', array( 'WC_EBANX_Capture_Payment', 'capture_from_order_dropdown' ) );

			add_action( 'admin_footer', array( 'WC_EBANX_Assets', 'render' ), 0 );

			add_action( 'woocommerce_settings_save_checkout', array( $this, 'on_before_save_settings' ), 10 );
			add_action( 'woocommerce_settings_saved', array( $this, 'setup_configs' ), 10 );
			add_action( 'woocommerce_settings_saved', array( $this, 'on_save_settings' ), 10 );
			add_action( 'woocommerce_settings_saved', array( $this, 'update_lead' ), 20 );
			add_action( 'woocommerce_settings_saved', array( $this, 'checker' ), 20 );

			add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'ebanx_admin_order_details' ), 10, 1 );

			add_action( 'upgrader_process_complete', array( $this, 'on_update' ), 10, 2 );

			add_action( 'woocommerce_checkout_process', array( 'WC_EBANX_Checker', 'validate_document' ), 10 );

			/**
			 * Payment by Link
			 */
			add_action( 'woocommerce_order_actions_end', array( $this, 'ebanx_metabox_save_post_render_button' ) );
			add_action( 'save_post', array( $this, 'ebanx_metabox_payment_link_save' ) );

			/**
			 * My account
			 */
			if ( $configs
				&& $configs->get_setting_or_default( 'save_card_data', 'no' ) === 'yes' ) {

				add_action( 'init', array( $this, 'my_account_endpoint' ) );
				add_action( 'woocommerce_account_' . self::$my_account_endpoint . '_endpoint', array( $this, 'my_account_template' ) );

				add_filter( 'query_vars', array( $this, 'my_account_query_vars' ), 0 );
				add_filter( 'woocommerce_account_menu_items', array( $this, 'my_account_menus' ) );
				add_filter( 'the_title', array( $this, 'my_account_menus_title' ) );
			}

			/**
			 * Filters
			 */
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
			add_filter( 'woocommerce_my_account_my_orders_actions', array( 'WC_EBANX_Cancel_Order', 'add_my_account_cancel_order_action' ), 10, 2 );
			add_filter( 'woocommerce_admin_order_actions', array( 'WC_EBANX_Capture_Payment', 'add_order_capture_button' ), 10, 2 );

			add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'get_instalments_admin_html' ) );

		}

		/**
		 * Sets up the configuration object
		 *
		 * @return void
		 */
		public function setup_configs() {
			/**
			 * Configs
			 */
			$this->configs         = new WC_EBANX_Global_Gateway();
			$this->is_sandbox_mode = 'yes' === $this->configs->settings['sandbox_mode_enabled'];
			$this->private_key     = $this->is_sandbox_mode ? $this->configs->settings['sandbox_private_key'] : $this->configs->settings['live_private_key'];
			$this->public_key      = $this->is_sandbox_mode ? $this->configs->settings['sandbox_public_key'] : $this->configs->settings['live_public_key'];
		}

		/**
		 * Extract some informations from the plugi.n
		 *
		 * @param  string $info The information that you want to extract, possible values: version, name, description, author, network.
		 * @return string       The value extracted.
		 */
		public static function get_plugin_info( $info = 'name' ) {
			$plugin = get_file_data( __FILE__, array( $info => $info ) );

			return $plugin[ $info ];
		}

		/**
		 * Extract the plugin version described on plugin's header.
		 *
		 * @return string The plugin version.
		 */
		public static function get_plugin_version() {
			return self::get_plugin_info( 'version' );
		}

		/**
		 * Performs checks on some system status.
		 *
		 * @return void
		 */
		public function checker() {
			WC_EBANX_Checker::check_sandbox_mode( $this );
			WC_EBANX_Checker::check_merchant_api_keys( $this );
			WC_EBANX_Checker::check_environment( $this );
			WC_EBANX_Checker::check_currency( $this );
			WC_EBANX_Checker::check_https_protocol( $this );
		}

		/**
		 * Call when the plugins are loaded.
		 *
		 * @return mixed
		 */
		public function plugins_loaded() {
			if ( $this->get_environment_warning() ) {
				return;
			}
		}

		/**
		 * Checks if we are receiving a third-party request and routes it
		 *
		 * @return void
		 */
		public function ebanx_router() {
			$ebanx_router = new WC_EBANX_Query_Router( 'ebanx' );

			$this->setup_configs();
			$api_controller = new WC_EBANX_Api_Controller( $this->configs );

			$ebanx_router->map( 'dashboard-check', array( $api_controller, 'dashboard_check' ) );
			$ebanx_router->map( 'order-received', array( $api_controller, 'order_received' ) );
			$ebanx_router->map( 'cancel-order', array( $api_controller, 'cancel_order' ) );
			$ebanx_router->map( 'capture-payment', array( $api_controller, 'capture_payment' ) );
			$ebanx_router->map( 'retrieve-logs', array( $api_controller, 'retrieve_logs' ) );
			$ebanx_router->map( 'plugin-check', array( $api_controller, 'plugin_check' ) );

			$ebanx_router->serve();
		}

		/**
		 * It enables the i18n of the plugin using the languages folders and the domain 'woocommerce-gateway-ebanx'
		 *
		 * @return void
		 */
		public function enable_i18n() {
			load_plugin_textdomain( 'woocommerce-gateway-ebanx', false, WC_EBANX_LANGUAGES_DIR );
		}

		/**
		 * It enables the my account page for logged users.
		 *
		 * @return void
		 */
		public function my_account_template() {
			if ( WC_EBANX_Request::has( 'credit-card-delete' )
				&& is_account_page() ) {
				// Find credit cards saved and delete the selected.
				$cards = get_user_meta( get_current_user_id(), '_ebanx_credit_card_token', true );

				foreach ( $cards as $k => $cd ) {
					if ( $cd && in_array( $cd->masked_number, WC_EBANX_Request::read( 'credit-card-delete' ) ) ) {
						unset( $cards[ $k ] );
					}
				}

				update_user_meta( get_current_user_id(), '_ebanx_credit_card_token', $cards );
			}

			$cards = array_filter(
				(array) get_user_meta( get_current_user_id(), '_ebanx_credit_card_token', true ), function ( $card ) {
					return ! empty( $card->brand ) && ! empty( $card->token ) && ! empty( $card->masked_number ); // TODO: Implement token due date.
				}
			);

			wc_get_template(
				'my-account/ebanx-credit-cards.php',
				array(
					'cards' => (array) $cards,
				),
				'woocommerce/ebanx/',
				WC_EBANX::get_templates_path()
			);
		}

		/**
		 * Mount query vars on my account for credit cards
		 *
		 * @param  array $vars
		 * @return array
		 */
		public function my_account_query_vars( $vars ) {
			$vars[] = self::$my_account_endpoint;

			return $vars;
		}

		/**
		 * It creates a endpoint to my account.
		 *
		 * @return void
		 */
		public function my_account_endpoint() {
			// My account endpoint.
			add_rewrite_endpoint( self::$my_account_endpoint, EP_ROOT | EP_PAGES );

			add_option( 'woocommerce_ebanx-global_settings', WC_EBANX_Global_Gateway::$defaults );

			flush_rewrite_rules();
		}

		/**
		 * Save some informations from merchant and send to EBANX servers.
		 *
		 * @return void
		 */
		public static function save_merchant_infos() {
			// Prevent fatal error if WooCommerce isn't installed.
			if ( ! defined( 'WC_VERSION' ) ) {
				return;
			}

			// Save merchant informations.
			$user = get_userdata( get_current_user_id() );
			if ( ! $user || is_wp_error( $user ) ) {
				return;
			}

			$url  = 'https://dashboard.ebanx.com/api/lead';
			$args = array(
				'body' => array(
					'lead' => array(
						'user_email'          => $user->user_email,
						'user_display_name'   => $user->display_name,
						'user_last_name'      => $user->last_name,
						'user_first_name'     => $user->first_name,
						'site_email'          => get_bloginfo( 'admin_email' ),
						'site_url'            => get_bloginfo( 'url' ),
						'site_name'           => get_bloginfo( 'name' ),
						'site_language'       => get_bloginfo( 'language' ),
						'wordpress_version'   => get_bloginfo( 'version' ),
						'woocommerce_version' => WC()->version,
						'type'                => 'Woocommerce',
					),
				),
			);

			// Call EBANX API to save a lead.
			$request = wp_remote_post( $url, $args );

			if ( ! is_wp_error( $request ) && isset( $request['body'] ) ) {
				$data = json_decode( $request['body'] );

				// Update merchant.
				update_option( '_ebanx_lead_id', $data->id, false );
			}
		}

		/**
		 * A method that will be called every time settings are saved.
		 *
		 * @return void
		 */
		public function on_before_save_settings() {
			$this->settings['before'] = $this->configs->settings;
		}

		/**
		 * Action triggered on save settings
		 */
		public function on_save_settings() {
			$this->settings['after'] = $this->configs->settings;

			delete_option( '_ebanx_api_was_checked' );

			// phpcs:disable
			do_action( 'ebanx_settings_saved', $_POST );
			// phpcs:enable

			WC_EBANX_Plugin_Settings_Change_Logger::persist( $this->settings );

			$this->settings = [];
		}

		/**
		 * Update and inegrate the lead to the merchant using the merchant's integration key.
		 *
		 * @return void
		 */
		public function update_lead() {
			$url     = 'https://dashboard.ebanx.com/api/lead';
			$lead_id = get_option( '_ebanx_lead_id' );

			$args = array(
				'body' => array(
					'lead' => array(
						'id'              => $lead_id,
						'integration_key' => $this->private_key,
						'site_url'        => get_bloginfo( 'url' ),
						'type'            => 'Woocommerce',
					),
				),
			);

			// Call EBANX API to save a lead.
			wp_remote_post( $url, $args );
		}

		/**
		 * Method that will be called when plugin is activated.
		 *
		 * @return void
		 */
		public static function activate_plugin() {
			self::save_merchant_infos();
			self::include_log_classes();

			WC_EBANX_Plugin_Activate_Logger::persist();

			flush_rewrite_rules();

			do_action( 'ebanx_activate_plugin' );
		}

		/**
		 * Method that will be called when plugin is deactivated.
		 *
		 * @return void
		 */
		public static function deactivate_plugin() {
			flush_rewrite_rules();

			self::include_log_classes();
			WC_EBANX_Plugin_Deactivate_Logger::persist();

			do_action( 'ebanx_deactivate_plugin' );
		}

		/**
		 * Method that will be called when plugin is updated.
		 *
		 * @param WP_Upgrader $plugin_upgrader
		 * @param array       $data
		 */
		public function on_update( $plugin_upgrader, $data ) {
			$ebanx_path     = plugin_basename( __FILE__ );
			$ebanx_database = new WC_EBANX_Database();

			if ( 'update' === $data['action'] && 'plugin' === $data['type'] ) {
				foreach ( $data['plugins'] as $plugin_path ) {
					if ( $plugin_path === $ebanx_path ) {
						self::save_merchant_infos();
						$ebanx_database->migrate();
					}
				}
			}
		}

		/**
		 * It enables a tab on WooCommerce My Account page.
		 *
		 * @param  string $title
		 * @return string Return the title to show on tab
		 */
		public function my_account_menus_title( $title ) {
			global $wp_query;

			$is_endpoint = isset( $wp_query->query_vars[ self::$my_account_endpoint ] );

			if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
				// phpcs:disable
				$title = __( self::$my_account_menu_name, 'woocommerce-gateway-ebanx' );
				// phpcs:enable
				remove_filter( 'the_title', array( $this, 'my_account_menus_title' ) );
			}

			return $title;
		}

		/**
		 * It enalbes the menu as a tab on My Account page.
		 *
		 * @param  array $menu The all menus supported by WooCoomerce.
		 * @return array       The new menu.
		 */
		public function my_account_menus( $menu ) {
			// Remove the logout menu item.
			$logout = $menu['customer-logout'];
			unset( $menu['customer-logout'] );

			// phpcs:disable
			$menu[ self::$my_account_endpoint ] = __( self::$my_account_menu_name, 'woocommerce-gateway-ebanx' );
			// phpcs:enable

			// Insert back the logout item.
			$menu['customer-logout'] = $logout;

			return $menu;
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Include log classes.
		 */
		private static function include_log_classes() {
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-environment.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-log.php';
			include_once WC_EBANX_SERVICES_DIR . 'loggers/class-wc-ebanx-logger.php';
			include_once WC_EBANX_SERVICES_DIR . 'loggers/class-wc-ebanx-plugin-activate-logger.php';
			include_once WC_EBANX_SERVICES_DIR . 'loggers/class-wc-ebanx-plugin-deactivate-logger.php';
			include_once WC_EBANX_SERVICES_DIR . 'loggers/class-wc-ebanx-plugin-settings-change-logger.php';
			include_once WC_EBANX_SERVICES_DIR . 'loggers/class-wc-ebanx-refund-logger.php';
			include_once WC_EBANX_SERVICES_DIR . 'loggers/class-wc-ebanx-notification-received-logger.php';
			include_once WC_EBANX_SERVICES_DIR . 'loggers/class-wc-ebanx-notification-query-logger.php';
			include_once WC_EBANX_SERVICES_DIR . 'loggers/class-wc-ebanx-payment-by-link-logger.php';
			include_once WC_EBANX_SERVICES_DIR . 'loggers/class-wc-ebanx-checkout-logger.php';
			include_once WC_EBANX_SERVICES_DIR . 'loggers/class-wc-ebanx-subscription-renewal-logger.php';
			include_once WC_EBANX_SERVICES_DIR . 'loggers/class-wc-ebanx-cancel-logger.php';
		}

		/**
		 * Include all plugin classes.
		 */
		private function includes() {
			// Utils.
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-constants.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-helper.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-notice.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-hooks.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-checker.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-flash.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-request.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-errors.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-assets.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-query-router.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-third-party-compability-layer.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-cancel-order.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-capture-payment.php';

			// Benjamin.
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-api.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-payment-adapter.php';

			// Load plugin log classes.
			self::include_log_classes();

			// Gateways.
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-new-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-redirect-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-flow-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-global-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-credit-card-gateway.php';

			// Chile Gateways.
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-servipag-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-sencillito-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-webpay-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-multicaja-gateway.php';

			// Brazil Gateways.
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-banking-ticket-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-credit-card-br-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-account-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-tef-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-bank-transfer-gateway.php';

			// Mexico Gateways.
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-credit-card-mx-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-debit-card-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-oxxo-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-spei-gateway.php';

			// Argentina Gateways.
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-credit-card-ar-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-efectivo-gateway.php';

			// Colombia Gateways.
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-baloto-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-eft-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-credit-card-co-gateway.php';

			// Peru Gateways.
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-pagoefectivo-gateway.php';
			include_once WC_EBANX_GATEWAYS_DIR . 'class-wc-ebanx-safetypay-gateway.php';

			// Hooks/Actions.
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-payment-by-link.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-payment-validator.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-my-account.php';
			include_once WC_EBANX_SERVICES_DIR . 'class-wc-ebanx-one-click.php';

			// Controllers.
			include_once WC_EBANX_CONTROLLERS_DIR . 'class-wc-ebanx-api-controller.php';

			// Exceptions.
			include_once WC_EBANX_EXCEPTIONS_DIR . 'class-wc-ebanx-payment-exception.php';
		}

		/**
		 * Get templates path.
		 *
		 * @return string
		 */
		public static function get_templates_path() {
			return WC_EBANX_TEMPLATES_DIR;
		}

		/**
		 * Add the gateways to WooCommerce.
		 *
		 * @param  array $methods WooCommerce payment methods.
		 *
		 * @return array
		 */
		public function add_gateway( $methods ) {
			// Global.
			$methods[] = 'WC_EBANX_Global_Gateway';

			// Brazil.
			$methods[] = 'WC_EBANX_Banking_Ticket_Gateway';
			$methods[] = 'WC_EBANX_Credit_Card_BR_Gateway';
			$methods[] = 'WC_EBANX_Tef_Gateway';
			$methods[] = 'WC_EBANX_Account_Gateway';
			$methods[] = 'WC_EBANX_Bank_Transfer_Gateway';

			// Mexico.
			$methods[] = 'WC_EBANX_Credit_Card_MX_Gateway';
			$methods[] = 'WC_EBANX_Debit_Card_Gateway';
			$methods[] = 'WC_EBANX_Oxxo_Gateway';
			$methods[] = 'WC_EBANX_Spei_Gateway';

			// Chile.
			$methods[] = 'WC_EBANX_Webpay_Gateway';
			$methods[] = 'WC_EBANX_Multicaja_Gateway';
			$methods[] = 'WC_EBANX_Sencillito_Gateway';
			$methods[] = 'WC_EBANX_Servipag_Gateway';

			// Colombia.
			$methods[] = 'WC_EBANX_Credit_Card_CO_Gateway';
			$methods[] = 'WC_EBANX_Baloto_Gateway';
			$methods[] = 'WC_EBANX_Eft_Gateway';

			// Peru.
			$methods[] = 'WC_EBANX_Pagoefectivo_Gateway';
			$methods[] = 'WC_EBANX_Safetypay_Gateway';

			// Argentina.
			$methods[] = 'WC_EBANX_Credit_Card_AR_Gateway';
			$methods[] = 'WC_EBANX_Efectivo_Gateway';

			return $methods;
		}

		/**
		 * Action links.
		 *
		 * @param  array $links Plugin links.
		 *
		 * @return array
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array();

			$ebanx_global = 'ebanx-global';

			$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $ebanx_global ) ) . '">' . __( 'Settings', 'woocommerce-gateway-ebanx' ) . '</a>';

			return array_merge( $plugin_links, $links );
		}

		/**
		 * WooCommerce fallback notice.
		 */
		public function woocommerce_missing_notice() {
			// TODO: Others notice here.
			include_once WC_EBANX_TEMPLATES_DIR . 'views/html-notice-missing-woocommerce.php';
		}

		/**
		 * Log messages.
		 *
		 * @param  string $message The log message.
		 * @return void
		 */
		public static function log( $message ) {
			$configs = new WC_EBANX_Global_Gateway();

			if ( 'yes' !== $configs->settings['debug_enabled'] ) {
				return;
			}

			if ( empty( self::$log ) ) {
				self::$log = new WC_Logger();
			}

			self::$log->add( 'woocommerce-gateway-ebanx', $message );

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( $message );
			}
		}

		/**
		 * It inserts a EBANX Settings shortcut on WordPress sidebar.
		 *
		 * @return void
		 */
		public function ebanx_sidebar_shortcut() {
			add_menu_page(
				'EBANX Settings',
				'EBANX Settings',
				'administrator',
				// TODO: Create a dynamic url.
				WC_EBANX_Constants::SETTINGS_URL,
				'',
				WC_EBANX_Assets::get_logo(),
				21
			);
		}

		/**
		 * Checks if this post is an EBANX Order and call WC_EBANX_Payment_By_Link.
		 *
		 * @param  int $post_id The post id.
		 * @return void
		 */
		public function ebanx_metabox_payment_link_save( $post_id ) {
			$order        = wc_get_order( $post_id );
			$checkout_url = get_post_meta( $order->id, '_ebanx_checkout_url', true );

			// Check if is an EBANX request.
			if ( WC_EBANX_Request::has( 'create_ebanx_payment_link' )
				&& WC_EBANX_Request::read( 'create_ebanx_payment_link' ) === __( 'Create EBANX Payment Link', 'woocommerce-gateway-ebanx' )
				&& ! $checkout_url ) {

				$this->setup_configs();

				update_post_meta( $order->id, '_ebanx_instalments', WC_EBANX_Request::read( 'ebanx_instalments', 1 ) );

				WC_EBANX_Payment_By_Link::create( $post_id );
			}
			return;
		}

		/**
		 * Checks if the button can be renderized and renders it.
		 *
		 * @param  int $post_id The post id.
		 * @return void
		 */
		public function ebanx_metabox_save_post_render_button( $post_id ) {
			$ebanx_currencies = array( 'BRL', 'USD', 'EUR', 'PEN', 'CLP', 'MXN', 'COP' );
			$order            = wc_get_order( $post_id );
			$checkout_url     = get_post_meta( $order->id, '_ebanx_checkout_url', true );

			if ( ! $checkout_url
				&& in_array( $order->status, array( 'auto-draft', 'pending' ) )
				&& in_array( strtoupper( get_woocommerce_currency() ), $ebanx_currencies ) ) {
				wc_get_template(
					'payment-by-link-action.php',
					array(),
					'woocommerce/ebanx/',
					WC_EBANX::get_templates_path()
				);
			}
		}

		/**
		 * It inserts informations about the order on admin order details.
		 *
		 * @param  WC_Object $order The WC order object.
		 * @return void
		 */
		public function ebanx_admin_order_details( $order ) {
			$payment_hash = get_post_meta( $order->id, '_ebanx_payment_hash', true );
			if ( $payment_hash ) {

				wc_get_template(
					'admin-order-details.php',
					array(
						'order'                => $order,
						'payment_hash'         => $payment_hash,
						'payment_checkout_url' => get_post_meta( $order->id, '_ebanx_checkout_url', true ),
						'is_sandbox_mode'      => $this->is_sandbox_mode,
						'dashboard_link'       => 'https://dashboard.ebanx.com/' . ( $this->is_sandbox_mode ? 'test/' : '' ) . "payments/?hash=$payment_hash",
					),
					'woocommerce/ebanx/',
					WC_EBANX::get_templates_path()
				);
			}
		}

		/**
		 * Get instalments select.
		 */
		public function get_instalments_admin_html() {
			echo '<div class="edit_address">
				<p class="form-field form-field-wide">
					<label>' . esc_html( __( 'Instalment for EBANX Credit Card:', 'woocommerce-gateway-ebanx' ) ) . '</label>
					<select name="ebanx_instalments" id="_payment_method_instalment" class="first">
						<option value="1" selected>1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
					</select>
				</p>
			</div>';
		}
	}

	add_action( 'plugins_loaded', array( 'WC_EBANX', 'get_instance' ) );
}
