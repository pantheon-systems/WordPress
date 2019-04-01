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
 * @package   WC-Gateway-Authorize-Net-AIM/API
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Authorize.Net AIM Emulation API Class
 *
 * Handles sending/receiving/parsing of Authorize.Net AIM name/value pair API.
 * Some payment processors offer emulation for their service which matches how
 * Authorize.Net handles their legacy NVP.
 *
 * @since 3.8.0
 */
class WC_Authorize_Net_AIM_Emulation_API extends SV_WC_API_Base implements SV_WC_Payment_Gateway_API {

	/** @var string gateway ID, used for logging */
	protected $gateway_id;

	/** @var \WC_Order|null order associated with the request, if any */
	protected $order;

	/** @var string API login ID value */
	protected $api_login_id;

	/** @var string API transaction key value */
	protected $api_transaction_key;


	/**
	 * Constructor - setup request object and set endpoint
	 *
	 * @since 3.8.0
	 * @param \WC_Gateway_Authorize_Net_AIM_Emulation $gateway instance
	 */
	public function __construct( $gateway ) {

		$this->gateway_id = $gateway->get_id();

		// request URI does not vary in between requests
		$this->request_uri = $gateway->get_gateway_url();

		// set response handler class
		$this->response_handler = 'WC_Authorize_Net_AIM_Emulation_API_Response';

		// set auth creds
		$this->api_login_id        = $gateway->get_api_login_id();
		$this->api_transaction_key = $gateway->get_api_transaction_key();
	}


	/**
	 * Create a new credit card charge transaction
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API::credit_card_charge()
	 * @param WC_Order $order order
	 * @return \WC_Authorize_Net_AIM_Emulation_API_Response Authorize.Net API response object
	 * @throws Exception network timeouts, etc
	 */
	public function credit_card_charge( WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->create_credit_card_charge( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Create a new credit card auth transaction
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API::credit_card_authorization()
	 * @param WC_Order $order order
	 * @return \WC_Authorize_Net_AIM_Emulation_API_Response Authorize.Net API response object
	 * @throws Exception network timeouts, etc
	 */
	public function credit_card_authorization( WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->create_credit_card_auth( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Capture funds for a credit card authorization
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API::credit_card_capture()
	 * @param WC_Order $order order
	 * @return \WC_Authorize_Net_AIM_Emulation_API_Response Authorize.Net API response object
	 * @throws Exception network timeouts, etc
	 */
	public function credit_card_capture( WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->create_credit_card_capture( $order );

		return $this->perform_request( $request );
	}


	/** Non-supported check debit, void, and refund methods *******************/

	/**
	 * No-op, as emulation does not support refund transactions
	 *
	 * @since 3.8.0
	 * @param \WC_Order $order
	 * @return null
	 */
	public function refund( WC_Order $order ) { }


	/**
	 * No-op, as emulation does not support void transactions
	 *
	 * @since 3.8.0
	 * @param \WC_Order $order
	 * @return null
	 */
	public function void( WC_Order $order ) { }


	/**
	 * no-op, as emulation does not support eCheck transactions
	 *
	 * @since 3.8.0
	 * @param WC_Order $order order
	 * @return null
	 */
	public function check_debit( WC_Order $order ) { }


	/** Tokenization methods - Authorize.Net AIM Emulation does not support tokenization **************/


	/**
	 * Authorize.Net AIM Emulation does not support getting tokenized payment methods.
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API::supports_get_tokenized_payment_methods()
	 * @return boolean false
	 */
	public function supports_get_tokenized_payment_methods() { return false; }


	/**
	 * Authorize.Net AIM Emulation does not support removing tokenized payment methods.
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API::supports_remove_tokenized_payment_method()
	 * @return boolean false
	 */
	public function supports_remove_tokenized_payment_method() { return false; }


	/**
	 * Authorize.Net AIM Emulation does not support tokenizing payment methods.
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API::tokenize_payment_method()
	 * @param WC_Order $order the order with associated payment and customer info
	 * @return null
	 */
	public function tokenize_payment_method( WC_Order $order ) { }


	/**
	 * Authorize.Net AIM  Emulation does not support removing tokenized payment methods.
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API::remove_tokenized_payment_method()
	 * @param string $token the payment method token
	 * @param string $customer_id unique
	 * @return null
	 */
	public function remove_tokenized_payment_method( $token, $customer_id ) { }


	/**
	 * Authorize.Net AIM Emulation does not support getting tokenized payment methods.
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API::get_tokenized_payment_methods()
	 * @param string $customer_id unique
	 * @return null
	 */
	public function get_tokenized_payment_methods( $customer_id ) { }


	/** Validation methods ****************************************************/


	/**
	 * Check if the response has any status code errors
	 *
	 * @since 3.8.0
	 * @see \SV_WC_API_Base::do_pre_parse_response_validation()
	 * @throws \SV_WC_API_Exception non HTTP 200 status
	 */
	protected function do_pre_parse_response_validation() {

		// authorize.net should rarely return a non-200 status
		if ( 200 != $this->get_response_code() ) {

			throw new SV_WC_API_Exception( sprintf( __( 'HTTP %s: %s', 'woocommerce-gateway-authorize-net-aim' ), $this->get_response_code(), $this->get_response_message() ) );
		}
	}


	/** Helper methods ********************************************************/


	/**
	 * Builds and returns a new API request object
	 *
	 * @since 3.8.0
	 * @param array $type
	 * @return \WC_Authorize_Net_AIM_API_Request API request object
	 */
	protected function get_new_request( $type = array() ) {

		return new WC_Authorize_Net_AIM_Emulation_API_Request( $this->api_login_id, $this->api_transaction_key );
	}


	/**
	 * Return the order associated with the request, if any
	 *
	 * @since 3.8.0
	 * @return \WC_Order|null
	 */
	public function get_order() {

		return $this->order;
	}


	/**
	 * Get the ID for the API, used primarily to namespace the action name
	 * for broadcasting requests
	 *
	 * @since 3.8.0
	 * @see \SV_WC_API_Base::get_api_id()
	 * @return string
	 */
	protected function get_api_id() {

		return $this->gateway_id;
	}


	/**
	 * Returns the main plugin class
	 *
	 * @since 3.8.0
	 * @see \SV_WC_API_Base::get_plugin()
	 * @return object
	 */
	protected function get_plugin() {
		return wc_authorize_net_aim();
	}


}
