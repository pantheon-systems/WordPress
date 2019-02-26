<?php
/**
 * WooCommerce Authorize.Net AIM Gateway
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net AIM Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net AIM Gateway for your
 * needs please refer to http://docs.woocommerce.com/document/authorize-net-aim/
 *
 * @package   WC-Gateway-Authorize-Net-AIM/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Authorize.Net AIM Emulation Gateway class (credit cards only)
 *
 * @since 3.8.0
 */
class WC_Gateway_Authorize_Net_AIM_Emulation extends SV_WC_Payment_Gateway_Direct {


	/** @var string authorize.net API login ID */
	protected $api_login_id;

	/** @var string authorize.net API transaction key */
	protected $api_transaction_key;

	/** @var string payment gateway URL */
	protected $gateway_url;

	/** @var string authorize.net test API login ID */
	protected $test_api_login_id;

	/** @var string authorize.net test API transaction key */
	protected $test_api_transaction_key;

	/** @var string test payment gateway URL */
	protected $test_gateway_url;

	/** @var WC_Authorize_Net_AIM_Emulation_API instance */
	protected $api;


	/**
	 * Initialize the gateway
	 *
	 * @since 3.8.0
	 */
	public function __construct() {

		parent::__construct(
			WC_Authorize_Net_AIM::EMULATION_GATEWAY_ID,
			wc_authorize_net_aim(),
			array(
				'method_title'       => __( 'Authorize.Net AIM Emulation', 'woocommerce-gateway-authorize-net-aim' ),
				'method_description' => __( 'Allow customers to securely pay using their credit cards via a payment processor that supports Authorize.Net AIM Emulation.', 'woocommerce-gateway-authorize-net-aim' ),
				'supports'           => array(
					self::FEATURE_PRODUCTS,
					self::FEATURE_CARD_TYPES,
					self::FEATURE_PAYMENT_FORM,
					self::FEATURE_CREDIT_CARD_CHARGE,
					self::FEATURE_CREDIT_CARD_AUTHORIZATION,
					self::FEATURE_CREDIT_CARD_CAPTURE,
				),
				'payment_type'       => 'credit-card',
				'environments'       => array(
					self::ENVIRONMENT_PRODUCTION => __( 'Production', 'woocommerce-gateway-authorize-net-aim' ),
					self::ENVIRONMENT_TEST       => __( 'Test', 'woocommerce-gateway-authorize-net-aim' ),
				),
			)
		);
	}


	/**
	 * Returns an array of form fields specific for this method
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway::get_method_form_fields()
	 * @return array of form fields
	 */
	protected function get_method_form_fields() {

		return array(

			'gateway_url' => array(
				'title'    => __( 'Payment Gateway URL', 'woocommerce-gateway-authorize-net-aim' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'The URL to post transaction requests to. Your payment processor should provide this to you.', 'woocommerce-gateway-authorize-net-aim' ),
				'default' => 'https://secure2.authorize.net/gateway/transact.dll',
			),

			'api_login_id' => array(
				'title'    => __( 'API Login ID', 'woocommerce-gateway-authorize-net-aim' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your Authorize.Net API Login ID', 'woocommerce-gateway-authorize-net-aim' ),
			),

			'api_transaction_key' => array(
				'title'    => __( 'API Transaction Key', 'woocommerce-gateway-authorize-net-aim' ),
				'type'     => 'password',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'Your Authorize.Net API Transaction Key', 'woocommerce-gateway-authorize-net-aim' ),
			),

			'test_gateway_url' => array(
				'title'    => __( 'Test Payment Gateway URL', 'woocommerce-gateway-authorize-net-aim' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'The test URL to post transaction requests to. Your payment processor should provide this to you.', 'woocommerce-gateway-authorize-net-aim' ),
				'default'  => 'https://test.authorize.net/gateway/transact.dll',
			),

			'test_api_login_id' => array(
				'title'    => __( 'Test API Login ID', 'woocommerce-gateway-authorize-net-aim' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Your test Authorize.Net API Login ID', 'woocommerce-gateway-authorize-net-aim' ),
			),

			'test_api_transaction_key' => array(
				'title'    => __( 'Test API Transaction Key', 'woocommerce-gateway-authorize-net-aim' ),
				'type'     => 'password',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'Your test Authorize.Net API Transaction Key', 'woocommerce-gateway-authorize-net-aim' ),
			),
		);
	}


	/**
	 * Returns true if the gateway is properly configured to perform transactions.
	 * Authorize.Net AIM Emulation requires: API Login ID, API Transaction Key,
	 * and Payment Gateway URL
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway::is_configured()
	 * @return boolean true if the gateway is properly configured
	 */
	protected function is_configured() {

		$is_configured = parent::is_configured();

		// missing configuration
		if ( ! $this->get_api_login_id() || ! $this->get_api_transaction_key() || ! $this->get_gateway_url() ) {
			$is_configured = false;
		}

		return $is_configured;
	}


	/**
	 * Return the default values for this payment method, used to pre-fill
	 * an authorize.net valid test account number when in testing mode
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway::get_payment_method_defaults()
	 * @return array
	 */
	public function get_payment_method_defaults() {

		$defaults = parent::get_payment_method_defaults();

		if ( $this->is_test_environment() ) {

			$defaults['account-number'] = '4007000000027';
		}

		return $defaults;
	}


	/** Getter methods ******************************************************/


	/**
	 * Get the API object
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway::get_api()
	 * @return WC_Authorize_Net_AIM_Emulation_API API instance
	 */
	public function get_api() {

		if ( is_object( $this->api ) ) {
			return $this->api;
		}

		$path = $this->get_plugin()->get_plugin_path() . '/includes/api/emulation/';

		// main API class responsible for communication with AIM API
		require_once( $path . 'class-wc-authorize-net-aim-emulation-api.php' );

		// API request
		require_once( $path. 'class-wc-authorize-net-aim-emulation-api-request.php' );

		// API response
		require_once( $path . 'class-wc-authorize-net-aim-emulation-api-response.php' );

		return $this->api = new WC_Authorize_Net_AIM_Emulation_API( $this );
	}


	/**
	 * Returns the API Login ID based on the current environment
	 *
	 * @since 3.8.0
	 * @param string|null $environment_id either 'production' or 'test'
	 * @return string the API login ID to use
	 */
	public function get_api_login_id( $environment_id = null ) {

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		return $this->is_production_environment( $environment_id ) ? $this->api_login_id : $this->test_api_login_id;
	}


	/**
	 * Returns the API Transaction Key based on the current environment
	 *
	 * @since 3.8.0
	 * @param string|null $environment_id either 'production' or 'test'
	 * @return string the API transaction key to use
	 */
	public function get_api_transaction_key( $environment_id = null ) {

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		return $this->is_production_environment( $environment_id ) ? $this->api_transaction_key : $this->test_api_transaction_key;
	}


	/**
	 * Return the payment gateway URL based on the current environment
	 *
	 * @since 3.8.0
	 * @param string|null $environment_id either 'production' or 'test'
	 * @return string payment gateway URL
	 */
	public function get_gateway_url( $environment_id = null ) {

		if ( is_null( $environment_id ) ) {
			$environment_id = $this->get_environment();
		}

		return $this->is_production_environment( $environment_id ) ? $this->gateway_url : $this->test_gateway_url;
	}


	/**
	 * Authorize.Net AIM does not support customer IDs
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway::get_customer_id_user_meta_name()
	 * @param $environment_id
	 * @return bool false
	 */
	public function get_customer_id_user_meta_name( $environment_id = null ) {

		return false;
	}


	/**
	 * Authorize.Net AIM does not support customer IDs
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway::get_guest_customer_id()
	 * @param WC_Order $order
	 * @return bool false
	 */
	public function get_guest_customer_id( WC_Order $order ) {

		return false;
	}


	/**
	 * Authorize.Net AIM does not support customer IDs
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway::get_customer_id()
	 * @param int $user_id wordpress user identifier
	 * @param array $args optional additional arguments which can include: environment_id, autocreate (true/false), and order
	 * @return false
	 */
	public function get_customer_id( $user_id, $args = array() ) {

		return false;
	}

	public function inherit_settings() {
		return false;
	}


}
