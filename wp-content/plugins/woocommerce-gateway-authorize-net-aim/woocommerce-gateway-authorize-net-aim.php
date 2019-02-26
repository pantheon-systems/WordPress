<?php
/**
 * Plugin Name: WooCommerce Authorize.Net AIM Gateway
 * Plugin URI: http://www.woocommerce.com/products/authorize-net-aim/
 * Description: Accept Credit Cards and eChecks via Authorize.Net AIM in your WooCommerce store
 * Author: SkyVerge
 * Author URI: http://www.woocommerce.com
 * Version: 3.13.0
 * Text Domain: woocommerce-gateway-authorize-net-aim
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2011-2018, SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Gateway-Authorize-Net-AIM
 * @author    SkyVerge
 * @category  Gateway
 * @copyright Copyright (c) 2011-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 18598:1a345d194a0d01e903f7a1363b6c86d2
 * WC requires at least: 2.6.14
 * WC tested up to: 3.3.0
 */

defined( 'ABSPATH' ) or exit;

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '1a345d194a0d01e903f7a1363b6c86d2', '18598' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.9.0', __( 'WooCommerce Authorize.Net AIM Gateway', 'woocommerce-gateway-authorize-net-aim' ), __FILE__, 'init_woocommerce_gateway_authorize_net_aim', array(
	'is_payment_gateway'   => true,
	'minimum_wc_version'   => '2.6.14',
	'minimum_wp_version'   => '4.4',
	'backwards_compatible' => '4.4',
) );

function init_woocommerce_gateway_authorize_net_aim() {

/**
 * # WooCommerce Authorize.Net AIM Gateway Main Plugin Class
 *
 * ## Plugin Overview
 *
 * This plugin adds Authorize.Net AIM as a payment gateway.  This class handles all the
 * non-gateway tasks such as verifying dependencies are met, loading the text
 * domain, etc.
 *
 * ## Features
 *
 * + Credit Card Authorization
 * + Credit Card Charge
 * + Credit Card Auth Capture
 *
 * ## Admin Considerations
 *
 * + An additional plugin action link is added that allows the admin to activate the legacy SIM gateway for use
 * with non-Authorize.Net processors (emulation)
 *
 * + A 'Capture Charge' order action link is added that allows the admin to capture a previously authorized charge for
 * an order
 *
 * ## Frontend Considerations
 *
 * Both the payment fields on checkout (and checkout->pay) and the My cards section on the My Account page are template
 * files for easy customization.
 *
 * ## Database
 *
 * ### Global Settings
 *
 * + `woocommerce_authorize_net_aim_settings` - the serialized gateway settings array
 * + `woocommerce_authorize_net_aim_echeck_settings` - the serialized eCheck gateway settings array
 *
 * ### Options table
 *
 * + `wc_authorize_net_aim_version` - the current plugin version, set on install/upgrade
 *
 * ### Credit Card Order Meta
 *
 * + `_wc_authorize_net_aim_environment` - the environment the transaction was created in, one of 'test' or 'production'
 * + `_wc_authorize_net_aim_trans_id` - the credit card transaction ID returned by Authorize.Net
 * + `_wc_authorize_net_aim_trans_date` - the credit card transaction date
 * + `_wc_authorize_net_aim_account_four` - the last four digits of the card used for the order
 * + `_wc_authorize_net_aim_card_type` - the card type used for the transaction, if known
 * + `_wc_authorize_net_aim_card_expiry_date` - the expiration date for the card used for the order
 * + `_wc_authorize_net_aim_authorization_code` - the authorization code returned by Authorize.Net
 * + `_wc_authorize_net_aim_charge_captured` - indicates if the transaction was captured, either `yes` or `no`
 *
 * ### eCheck Order Meta
 * + `_wc_authorize_net_aim_echeck_environment` - the environment the transaction was created in, one of 'test' or 'production'
 * + `_wc_authorize_net_aim_echeck_trans_id` - the credit card transaction ID returned by Authorize.Net
 * + `_wc_authorize_net_aim_echeck_trans_date` - the credit card transaction date
 * + `_wc_authorize_net_aim_echeck_account_four` - the last four digits of the card used for the order
 * + `_wc_authorize_net_aim_echeck_account_type` - the bank account type used for the transaction, if known, either `checking` or `savings`
 *
 * @since 3.0
 */
class WC_Authorize_Net_AIM extends SV_WC_Payment_Gateway_Plugin {


	/** string version number */
	const VERSION = '3.13.0';

	/** @var WC_Authorize_Net_AIM single instance of this plugin */
	protected static $instance;

	/** string the plugin id */
	const PLUGIN_ID = 'authorize_net_aim';

	/** string credit card gateway class name */
	const CREDIT_CARD_GATEWAY_CLASS_NAME = 'WC_Gateway_Authorize_Net_AIM_Credit_Card';

	/** string credit card gateway id */
	const CREDIT_CARD_GATEWAY_ID = 'authorize_net_aim';

	/** string eCheck gateway class name */
	const ECHECK_GATEWAY_CLASS_NAME = 'WC_Gateway_Authorize_Net_AIM_eCheck';

	/** string eCheck gateway id */
	const ECHECK_GATEWAY_ID = 'authorize_net_aim_echeck';

	/** string emulation gateway class name */
	const EMULATION_GATEWAY_CLASS_NAME = 'WC_Gateway_Authorize_Net_AIM_Emulation';

	/** string emulation gateway ID */
	const EMULATION_GATEWAY_ID = 'authorize_net_aim_emulation';


	/**
	 * Setup main plugin class
	 *
	 * @since 3.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'  => 'woocommerce-gateway-authorize-net-aim',
				'gateways'     => $this->get_enabled_gateways(),
				'dependencies' => array( 'SimpleXML', 'xmlwriter', 'dom' ),
				'require_ssl'  => true,
				'supports'     => array(
					self::FEATURE_CAPTURE_CHARGE,
				),
				'display_php_notice' => true,
			)
		);

		// load gateway files
		add_action( 'sv_wc_framework_plugins_loaded', array( $this, 'includes' ), 11 );

		if ( is_admin() && ! is_ajax() ) {

			// handle activating/deactivating emulation gateway
			add_action( 'admin_action_wc_authorize_net_aim_emulation', array( $this, 'toggle_emulation' ) );
		}
	}


	/**
	 * Loads any required files
	 *
	 * @since 3.0
	 */
	public function includes() {

		$plugin_path = $this->get_plugin_path();

		// gateway classes
		require_once( $plugin_path . '/includes/class-wc-gateway-authorize-net-aim.php' );
		require_once( $plugin_path . '/includes/class-wc-gateway-authorize-net-aim-credit-card.php' );
		require_once( $plugin_path . '/includes/class-wc-gateway-authorize-net-aim-echeck.php' );

		// require checkout billing fields for non-US stores, as all European card processors require the billing fields
		// in order to successfully process transactions
		if ( ! is_admin() && ! strncmp( get_option( 'woocommerce_default_country' ), 'US:', 3 ) ) {

			// remove blank arrays from the state fields, otherwise it's hidden
			add_action( 'woocommerce_states', array( $this, 'tweak_states' ), 1 );

			//  require the billing fields
			add_filter( 'woocommerce_get_country_locale', array( $this, 'require_billing_fields' ), 100 );
		}

		// load the emulation gateway if enabled
		if ( $this->is_emulation_enabled() ) {

			require_once( $plugin_path . '/includes/class-wc-gateway-authorize-net-aim-emulation.php' );
		}
	}


	/**
	 * Return the enabled gateways, AIM credit card/eCheck by default, with
	 * AIM emulation included when enabled
	 *
	 * @since 3.8.0
	 * @return array
	 */
	protected function get_enabled_gateways() {

		// default gateways
		$gateways = array(
			self::CREDIT_CARD_GATEWAY_ID => self::CREDIT_CARD_GATEWAY_CLASS_NAME,
			self::ECHECK_GATEWAY_ID      => self::ECHECK_GATEWAY_CLASS_NAME,
		);

		// add emulation gateway if enabled
		if ( $this->is_emulation_enabled() ) {
			$gateways[ self::EMULATION_GATEWAY_ID ] = self::EMULATION_GATEWAY_CLASS_NAME;
		}

		return $gateways;
	}


	/** Frontend methods ******************************************************/


	/**
	 * Before requiring all billing fields, the state array has to be removed of blank arrays, otherwise
	 * the field is hidden
	 *
	 * @see WC_Countries::__construct()
	 *
	 * @since 3.0
	 * @param array $countries the available countries
	 * @return array the available countries
	 */
	public function tweak_states( $countries ) {

		foreach ( $countries as $country_code => $states ) {

			if ( is_array( $countries[ $country_code ] ) && empty( $countries[ $country_code ] ) ) {
				$countries[ $country_code ] = null;
			}
		}

		return $countries;
	}


	/**
	 * Require all billing fields to be entered when the merchant is using a European payment processor
	 *
	 * @since 3.0
	 * @param array $locales array of countries and locale-specific address field info
	 * @return array the locales array with billing info required
	 */
	public function require_billing_fields( $locales ) {

		foreach ( $locales as $country_code => $fields ) {

			if ( isset( $locales[ $country_code ]['state']['required'] ) ) {
				$locales[ $country_code ]['state']['required'] = true;
				$locales[ $country_code ]['state']['label']    = $this->get_state_label( $country_code );
			}
		}

		return $locales;
	}


	/**
	 * Gets a label for states that don't have one set by WooCommerce.
	 *
	 * @since 3.11.1
	 *
	 * @param string $country_code the 2-letter country code for the billing country
	 * @return string the label for the "billing state" field at checkout
	 */
	protected function get_state_label( $country_code ) {

		switch( $country_code ) {

			case 'AF':
			case 'AT':
			case 'BI':
			case 'KR':
			case 'PL':
			case 'PT':
			case 'LK':
			case 'SE':
			case 'VN':
				$label = __( 'Province', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'AX':
			case 'YT':
				$label = __( 'Island', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'DE':
				$label = __( 'State', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'EE':
			case 'NO':
				$label = __( 'County', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'FI':
			case 'IL':
			case 'LB':
				$label = __( 'District', 'woocommerce-gateway-authorize-net-cim' );
			break;

			default:
				$label = __( 'Region', 'woocommerce-gateway-authorize-net-cim' );
		}

		return $label;
	}


	/** Admin methods ******************************************************/


	/**
	 * Return the plugin action links.  This will only be called if the plugin
	 * is active.
	 *
	 * @since 3.0
	 * @param array $actions associative array of action names to anchor tags
	 * @return array associative array of plugin action links
	 */
	public function plugin_action_links( $actions ) {

		// get the standard action links
		$actions = parent::plugin_action_links( $actions );

		// enable/disable emulation link
		$params = array(
			'action' => 'wc_authorize_net_aim_emulation',
			'toggle' => $this->is_emulation_enabled() ? 'disable' : 'enable'
		);

		$url = wp_nonce_url( add_query_arg( $params, 'admin.php' ), $this->get_file() );
		$title  = $this->is_emulation_enabled()
			? esc_html__( 'Disable Emulation Gateway', 'woocommerce-gateway-authorize-net-aim' )
			: esc_html__( 'Enable Emulation Gateway', 'woocommerce-gateway-authorize-net-aim' );

		$actions['emulation'] = sprintf( '<a href="%1$s" title="%2$s">%2$s</a>', esc_url( $url ), $title );

		return $actions;
	}


	/**
	 * Returns the "Configure Credit Cards" or "Configure eCheck" plugin action links that go
	 * directly to the gateway settings page
	 *
	 * @since 3.4.0
	 * @see SV_WC_Payment_Gateway_Plugin::get_settings_url()
	 * @param string $gateway_id the gateway identifier
	 * @return string plugin configure link
	 */
	public function get_settings_link( $gateway_id = null ) {

		switch ( $gateway_id ) {

			case self::EMULATION_GATEWAY_ID:
				$label = __( 'Configure Emulator', 'woocommerce-gateway-authorize-net-aim' );
			break;

			case self::ECHECK_GATEWAY_ID:
				$label = __( 'Configure eChecks', 'woocommerce-gateway-authorize-net-aim' );
			break;

			default:
				$label = __( 'Configure Credit Cards', 'woocommerce-gateway-authorize-net-aim' );
		}

		return sprintf( '<a href="%s">%s</a>',
			$this->get_settings_url( $gateway_id ),
			$label
		);
	}


	/**
	 * Handles enabling/disabling the emulation gateway
	 *
	 * @since 3.8.0
	 */
	public function toggle_emulation() {

		// security check
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], $this->get_file() ) || ! current_user_can( 'manage_woocommerce' ) ) {
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// sanity check
		if ( empty( $_GET['toggle'] ) || ! in_array( $_GET['toggle'], array( 'enable', 'disable' ), true ) ) {
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		// enable/disable the emulation gateway
		update_option( 'wc_authorize_net_aim_emulation_enabled', 'enable' === $_GET['toggle'] );

		$return_url = add_query_arg( array( 'wc_authorize_net_aim_emulation' => $_GET['toggle'] ), 'plugins.php' );

		// back to whence we came
		wp_safe_redirect( $return_url );
		exit;
	}


	/**
	 * Renders an admin notices, along with displaying a message on the plugins list table
	 * when activating/deactivating legacy SIM gateway
	 *
	 * @since 3.2.0
	 * @see SV_WC_Plugin::add_admin_notices()
	 */
	public function add_admin_notices() {

		parent::add_admin_notices();

		$credit_card_gateway = $this->get_gateway( self::CREDIT_CARD_GATEWAY_ID );

		if ( $credit_card_gateway->is_enabled() && $credit_card_gateway->is_accept_js_enabled() && isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] ) {

			$message = '';

			if ( ! $credit_card_gateway->get_client_key() ) {
				$message = sprintf( __( "%s: A valid Client Key is required to use Accept.js at checkout.", 'woocommerce-gateway-authorize-net-aim' ), '<strong>' . $this->get_plugin_name() . '</strong>' );
			} elseif ( ! wc_checkout_is_https() ) {
				$message = sprintf( __( "%s: SSL is required to use Accept.js at checkout.", 'woocommerce-gateway-authorize-net-aim' ), '<strong>' . $this->get_plugin_name() . '</strong>' );
			}

			if ( $message ) {
				$this->get_admin_notice_handler()->add_admin_notice( $message, 'accept-js-status', array(
					'dismissible'  => false,
					'notice_class' => 'error',
				) );
			}
		}

		// emulation enabled/disabled notice
		if ( ! empty( $_GET['wc_authorize_net_aim_emulation'] ) ) {

			$message = ( 'enable' === $_GET['wc_authorize_net_aim_emulation'] )
				? __( 'Authorize.Net AIM Emulation Gateway is now enabled.', 'woocommerce-gateway-authorize-net-aim' )
				: __( 'Authorize.Net AIM Emulation Gateway is now disabled.', 'woocommerce-gateway-authorize-net-aim');

			$this->get_admin_notice_handler()->add_admin_notice( $message, 'emulation-status', array( 'dismissible' => false, ) );
		}
	}


	/**
	 * Returns true if emulation is enabled
	 *
	 * @since 3.8.0
	 * @return bool
	 */
	private function is_emulation_enabled() {

		return (bool) get_option( 'wc_authorize_net_aim_emulation_enabled' );
	}


	/**
	 * Return the gateway settings for the given gateway ID. Overridden to mark
	 * the emulation gateway as inheriting settings (even though it does not) to
	 * prevent the credit card/eCheck gateways from attempting to inherit it's settings
	 *
	 * TODO: this can be removed once is https://github.com/skyverge/wc-plugin-framework/issues/157
	 * is merged and it's FW version required {MR 2016-06-28}
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_Plugin::get_gateway_settings()
	 * @param string $gateway_id gateway identifier
	 * @return array settings array
	 */
	public function get_gateway_settings( $gateway_id ) {

		$settings = parent::get_gateway_settings( $gateway_id );

		if ( $gateway_id === self::EMULATION_GATEWAY_ID ) {
			$settings['inherit_settings'] = 'yes';
		}

		return $settings;
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Authorize.Net AIM Instance, ensures only one instance is/can be loaded
	 *
	 * @since 3.3.0
	 * @see wc_authorize_net_aim()
	 * @return WC_Authorize_Net_AIM
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Authorize.Net AIM Gateway', 'woocommerce-gateway-authorize-net-aim' );
	}


	/**
	 * Gets the plugin documentation URL
	 *
	 * @since 3.0
	 * @see SV_WC_Plugin::get_documentation_url()
	 * @return string
	 */
	public function get_documentation_url() {
		return 'http://docs.woocommerce.com/document/authorize-net-aim/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since 3.4.0
	 * @see SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 3.0
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Install default settings
	 *
	 * @since 3.0
	 */
	protected function install() {

		// versions prior to 3.0 did not set a version option, so the upgrade method needs to be called manually
		if ( get_option( 'woocommerce_authorize_net_settings' ) ) {

			$this->upgrade( '2.1' );
		}
	}


	/**
	 * Perform any version-related changes.
	 *
	 * @since 3.0
	 * @param int $installed_version the currently installed version of the plugin
	 */
	protected function upgrade( $installed_version ) {

		// upgrade to 3.0
		if ( version_compare( $installed_version, '3.0', '<' ) ) {

			if ( $old_settings = get_option( 'woocommerce_authorize_net_settings' ) ) {

				$new_settings = array();

				// migrate from old settings
				$new_settings['enabled']                  = isset( $old_settings['enabled'] ) ? $old_settings['enabled'] : 'no';
				$new_settings['title']                    = isset( $old_settings['title'] ) ? $old_settings['title'] : '';
				$new_settings['description']              = isset( $old_settings['description'] ) ? $old_settings['description'] : '';
				$new_settings['enable_csc']               = isset( $old_settings['cvv'] ) ? $old_settings['cvv'] : 'yes';
				$new_settings['transaction_type']         = isset( $old_settings['salemethod'] ) && 'AUTH_ONLY' == $old_settings['salemethod'] ? 'authorization' : 'charge';
				$new_settings['environment']              = isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' == $old_settings['gatewayurl'] ? 'test' : 'production';
				$new_settings['api_login_id']             = isset( $old_settings['apilogin'] ) ? $old_settings['apilogin'] : '';
				$new_settings['debug_mode']               = isset( $old_settings['debugon'] ) && 'yes' == $old_settings['debugon'] ? 'log' : 'off';
				$new_settings['api_transaction_key']      = isset( $old_settings['transkey'] ) ? $old_settings['transkey'] : '';
				$new_settings['test_api_login_id']        = isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' == $old_settings['gatewayurl'] ? $new_settings['api_login_id'] : '';
				$new_settings['test_api_transaction_key'] = isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' == $old_settings['gatewayurl'] ? $new_settings['api_transaction_key'] : '';

				// automatically activate legacy SIM gateway if the gateway URL is non-standard
				if ( isset( $old_settings['gatewayurl'] ) &&
					'https://test.authorize.net/gateway/transact.dll' != $old_settings['gatewayurl'] &&
					'https://secure.authorize.net/gateway/transact.dll' != $old_settings['gatewayurl'] ) {

					update_option( 'wc_authorize_net_aim_sim_active', true );
				}

				if ( isset( $old_settings['cardtypes'] ) && is_array( $old_settings['cardtypes'] ) ) {

					$new_settings['card_types'] = array();

					// map old to new
					foreach ( $old_settings['cardtypes'] as $card_type ) {

						switch ( $card_type ) {

							case 'MasterCard':
								$new_settings['card_types'][] = 'MC';
								break;

							case 'Visa':
								$new_settings['card_types'][] = 'VISA';
								break;

							case 'Discover':
								$new_settings['card_types'][] = 'DISC';
								break;

							case 'American Express':
								$new_settings['card_types'][] = 'AMEX';
								break;
						}
					}
				}

				// update to new settings
				update_option( 'woocommerce_authorize_net_aim_settings', $new_settings );

				// change option name for old settings
				update_option( 'woocommerce_authorize_net_sim_settings', $old_settings );
			}
		}

		// upgrade to 3.8.0
		if ( version_compare( $installed_version, '3.8.0', '<' ) ) {

			// update emulation gateway enabled option
			if ( get_option( 'wc_authorize_net_aim_sim_active', false ) ) {

				update_option( 'wc_authorize_net_aim_emulation_enabled', true );
				delete_option( 'wc_authorize_net_aim_sim_active' );
			}

			// migrate settings from legacy emulation gateway
			if ( $old_settings = get_option( 'woocommerce_authorize_net_sim_settings' ) ) {

				// base settings
				$new_settings = array(
					'enabled'                  => isset( $old_settings['enabled'] ) ? $old_settings['enabled'] : 'no',
					'title'                    => isset( $old_settings['title'] ) ? $old_settings['title'] : 'Credit Card',
					'description'              => isset( $old_settings['description'] ) ? $old_settings['description'] : 'Pay securely using your credit card.',
					'enable_csc'               => isset( $old_settings['cvv'] ) ? $old_settings['cvv'] : 'yes',
					'transaction_type'         => isset( $old_settings['salemethod'] ) && 'AUTH_ONLY' === $old_settings['salemethod'] ? 'authorization' : 'charge',
					'environment'              => isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' === $old_settings['gatewayurl'] ? 'test' : 'production',
					'debug_mode'               => isset( $old_settings['debugon'] ) && 'yes' === $old_settings['debugon'] ? 'log' : 'off',
					'gateway_url'              => isset( $old_settings['gatewayurl'] ) ? $old_settings['gatewayurl'] : 'https://secure2.authorize.net/gateway/transact.dll',
					'api_login_id'             => isset( $old_settings['apilogin'] ) ? $old_settings['apilogin'] : '',
					'api_transaction_key'      => isset( $old_settings['transkey'] ) ? $old_settings['transkey'] : '',
					'test_gateway_url'         => isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' === $old_settings['gatewayurl'] ? $old_settings['gatewayurl'] : 'https://test.authorize.net/gateway/transact.dll',
					'test_api_login_id'        => isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' === $old_settings['gatewayurl'] ? $old_settings['apilogin'] : '',
					'test_api_transaction_key' => isset( $old_settings['gatewayurl'] ) && 'https://test.authorize.net/gateway/transact.dll' === $old_settings['gatewayurl'] ? $old_settings['transkey'] : '',
				);

				// card types
				if ( isset( $old_settings['cardtypes'] ) && is_array( $old_settings['cardtypes'] ) ) {

					$new_settings['card_types'] = array();

					// map old to new
					foreach ( $old_settings['cardtypes'] as $card_type ) {

						switch ( $card_type ) {

							case 'MasterCard':
								$new_settings['card_types'][] = 'MC';
								break;

							case 'Visa':
								$new_settings['card_types'][] = 'VISA';
								break;

							case 'Discover':
								$new_settings['card_types'][] = 'DISC';
								break;

							case 'American Express':
								$new_settings['card_types'][] = 'AMEX';
								break;
						}
					}
				}

				// set new settings
				update_option( 'woocommerce_authorize_net_aim_emulation_settings', $new_settings );

				// remove old settings
				delete_option( 'woocommerce_authorize_net_sim_settings' );
			}
		}
	}


} // end \WC_Authorize_Net_AIM


/**
 * Returns the One True Instance of Authorize.Net AIM
 *
 * @since 3.3.0
 * @return WC_Authorize_Net_AIM
 */
function wc_authorize_net_aim() {
	return WC_Authorize_Net_AIM::instance();
}

// fire it up!
wc_authorize_net_aim();

} // init_woocommerce_gateway_authorize_net_aim
