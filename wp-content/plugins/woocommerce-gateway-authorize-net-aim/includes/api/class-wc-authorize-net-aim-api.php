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
 * Authorize.Net AIM API Class
 *
 * Handles sending/receiving/parsing of Authorize.Net AIM XML, this is the main API
 * class responsible for communication with the Authorize.Net AIM API
 *
 * @since 3.0
 */
class WC_Authorize_Net_AIM_API extends SV_WC_API_Base implements SV_WC_Payment_Gateway_API {


	/** the production endpoint */
	const PRODUCTION_ENDPOINT = 'https://api2.authorize.net/xml/v1/request.api';

	/** the test endpoint */
	const TEST_ENDPOINT = 'https://apitest.authorize.net/xml/v1/request.api';

	/** @var string request URI */
	protected $request_uri;

	/** @var \WC_Order|null order associated with the request, if any */
	protected $order;

	/** @var string gateway ID */
	private $gateway_id;

	/** @var string API login ID value */
	private $api_login_id;

	/** @var string API transaction key value */
	private $api_transaction_key;


	/**
	 * Constructor - setup request object and set endpoint
	 *
	 * @since 3.0
	 * @param string $gateway_id gateway id
	 * @param string $environment current API environment, either `production` or `test`
	 * @param string $api_login_id API login ID
	 * @param string $api_transaction_key API transaction key
	 * @return \WC_Authorize_Net_AIM_API
	 */
	public function __construct( $gateway_id, $environment, $api_login_id, $api_transaction_key ) {

		$this->gateway_id = $gateway_id;

		// request URI does not vary in between requests
		$this->request_uri = ( 'production' === $environment ) ? self::PRODUCTION_ENDPOINT : self::TEST_ENDPOINT;

		$this->set_request_content_type_header( 'application/xml' );
		$this->set_request_accept_header( 'application/xml' );

		// set response handler class
		$this->response_handler = 'WC_Authorize_Net_AIM_API_Response';

		// set auth creds
		$this->api_login_id        = $api_login_id;
		$this->api_transaction_key = $api_transaction_key;
	}


	/**
	 * Create a new credit card charge transaction
	 *
	 * This request, if successful, causes a charge to be incurred by the
	 * specified credit card. Notice that the authorization for the charge is
	 * obtained when the card issuer receives this request. The resulting
	 * authorization code is returned in the response to this request.
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API::credit_card_charge()
	 * @param WC_Order $order order
	 * @return \WC_Authorize_Net_AIM_API_Response Authorize.Net API response object
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
	 * This request is used for a transaction in which the merchant needs
	 * authorization of a charge, but does not wish to actually make the charge
	 * at this point in time. For example, if a customer orders merchandise to
	 * be shipped, you could issue this request at the time of the order to
	 * make sure the merchandise will be paid for by the card issuer. Then at
	 * the time of actual merchandise shipment, you can capture the charge.
	 *
	 * It is very important to save the transaction ID from the response to
	 * this request, because this is required for the subsequent capture request.
	 *
	 * Note: The authorization is valid only for a fixed amount of time, which
	 * may vary by card issuer, but which is usually several days. Authorize.Net imposes
	 * its own maximum of 30 days after the date of the original authorization,
	 * but most issuers are expected to have a validity period significantly
	 * less than this.
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API::credit_card_authorization()
	 * @param WC_Order $order order
	 * @return \WC_Authorize_Net_AIM_API_Response Authorize.Net API response object
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
	 * This request can be made only after a previous and successful
	 * authorization request, where the card issuer has authorized a
	 * charge to be made against the specified credit card in the future. The
	 * transaction ID from that prior transaction must be used in this
	 * subsequent and related transaction. This request actually causes that
	 * authorized charge to be incurred against the customer's credit card.
	 *
	 * Notice that you cannot have multiple capture requests against a single
	 * authorization request. Each authorization request must
	 * have one and only one capture request.
	 *
	 * Note: The authorization to be captured is valid only for a fixed amount
	 * of time, which may vary by card issuer, but which is usually several
	 * days. Authorize.Net imposes its own maximum of 30 days after the date of the
	 * original authorization, but most issuers are expected to have a validity
	 * period significantly less than this.
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API::credit_card_capture()
	 * @param WC_Order $order order
	 * @return \WC_Authorize_Net_AIM_API_Response Authorize.Net API response object
	 * @throws Exception network timeouts, etc
	 */
	public function credit_card_capture( WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->create_credit_card_capture( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Perform a customer check debit transaction
	 *
	 * An amount will be debited from the customer's account to the merchant's account.
	 *
	 * @since 3.0
	 * @param WC_Order $order order
	 * @return \WC_Authorize_Net_AIM_API_Response Authorize.Net API response object
	 * @throws Exception network timeouts, etc
	 */
	public function check_debit( WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->create_echeck_debit( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Perform a refund for the order
	 *
	 * Note that only transactions settled in the past 120 days are eligible for
	 * refunds
	 *
	 * @since 3.3.0
	 * @param WC_Order $order the order
	 * @return \WC_Authorize_Net_AIM_API_Response Authorize.Net API response object
	 * @throws SV_WC_API_Exception network timeouts, etc
	 */
	public function refund( WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->create_refund( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Perform a void for the order
	 *
	 * Note that a void is only performed in for a transaction that has a valid
	 * authorization that has not been captured. Authorized & captured transactions
	 * that have not yet been settled are not eligible for voiding as we don't
	 * know if they've been settled or not.
	 *
	 * @since 3.3.0
	 * @param WC_Order $order the order
	 * @return \WC_Authorize_Net_AIM_API_Response Authorize.Net API response object
	 * @throws SV_WC_API_Exception network timeouts, etc
	 */
	public function void( WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_request();

		$request->create_void( $order );

		return $this->perform_request( $request );
	}


	/** Tokenization methods - all no-op as Authorize.Net AIM does not support tokenization ***************************/


	/**
	 * Returns false, as Authorize.Net AIM does not support tokenization.
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API::supports_get_tokenized_payment_methods()
	 * @return boolean true
	 */
	public function supports_get_tokenized_payment_methods() {

		return false;
	}


	/**
	 * Returns false, as Authorize.Net AIM does not support tokenization.
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API::supports_remove_tokenized_payment_method()
	 * @return boolean true
	 */
	public function supports_remove_tokenized_payment_method() {

		return false;
	}


	/**
	 * Authorize.Net AIM does not support tokenization.
	 *
	 * no-op
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API::tokenize_payment_method()
	 * @param WC_Order $order the order with associated payment and customer info
	 * @return \SV_WC_Payment_Gateway_API_Create_Payment_Token_Response|void
	 */
	public function tokenize_payment_method( WC_Order $order ) { }


	/**
	 * Authorize.Net AIM does not support tokenization.
	 *
	 * no-op
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API::remove_tokenized_payment_method()
	 * @param string $token the payment method token
	 * @param string $customer_id unique
	 * @return \SV_WC_Payment_Gateway_API_Response|void
	 */
	public function remove_tokenized_payment_method( $token, $customer_id ) { }


	/**
	 * Authorize.Net AIM does not support tokenization.
	 *
	 * no-op
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API::get_tokenized_payment_methods()
	 * @param string $customer_id unique
	 * @return \SV_WC_API_Get_Tokenized_Payment_Methods_Response|void
	 */
	public function get_tokenized_payment_methods( $customer_id ) { }


	/** Validation methods ****************************************************/


	/**
	 * Check if the response has any status code errors
	 *
	 * @since 3.2.0
	 * @see \SV_WC_API_Base::do_pre_parse_response_validation()
	 * @throws \SV_WC_API_Exception non HTTP 200 status
	 */
	protected function do_pre_parse_response_validation() {

		// authorize.net should rarely return a non-200 status
		if ( 200 != $this->get_response_code() ) {

			throw new SV_WC_API_Exception( sprintf( __( 'HTTP %s: %s', 'woocommerce-gateway-authorize-net-aim' ), $this->get_response_code(), $this->get_response_message() ) );
		}
	}


	/**
	 * Check if the response has any errors
	 *
	 * @since 3.2.0
	 * @see \SV_WC_API_Base::do_post_parse_response_validation()
	 * @throws \SV_WC_API_Exception if response has API error
	 */
	protected function do_post_parse_response_validation() {

		// E00027 is a processing error that almost always includes additional transaction info, like status codes and a transaction ID so it's treated like a general transaction decline than API error
		if ( $this->get_response()->has_api_error() && 'E00027' !== $this->get_response()->get_api_error_code() ) {

			$exception_code = intval( str_ireplace( array( 'E', 'I' ), '', $this->get_response()->get_api_error_code() ) );

			throw new SV_WC_API_Exception( sprintf( __( 'Code: %s, Message: %s', 'woocommerce-gateway-authorize-net-aim' ), $this->get_response()->get_api_error_code(), $this->get_response()->get_api_error_message() ), $exception_code );

		} elseif ( $this->get_response()->is_test_request() ) {

			throw new SV_WC_API_Exception( __( 'Test request detected -- please disable test mode in your Authorize.Net control panel and use a separate Authorize.Net test account for testing.' ) );
		}
	}


	/** Helper methods ********************************************************/


	/**
	 * Builds and returns a new API request object
	 *
	 * @since 3.0
	 * @param array $type
	 * @return \WC_Authorize_Net_AIM_API_Request API request object
	 */
	protected function get_new_request( $type = array() ) {

		return new WC_Authorize_Net_AIM_API_Request( $this->api_login_id, $this->api_transaction_key );
	}


	/**
	 * Return the order associated with the request, if any
	 *
	 * @since 3.4.3
	 * @return \WC_Order|null
	 */
	public function get_order() {

		return $this->order;
	}


	/**
	 * Get the ID for the API, used primarily to namespace the action name
	 * for broadcasting requests
	 *
	 * @since 3.2.0
	 * @see \SV_WC_API_Base::get_api_id()
	 * @return string
	 */
	protected function get_api_id() {

		return $this->gateway_id;
	}


	/**
	 * Returns the main plugin class
	 *
	 * @since 3.2.0
	 * @see \SV_WC_API_Base::get_plugin()
	 * @return object
	 */
	protected function get_plugin() {
		return wc_authorize_net_aim();
	}


	/**
	 * Determine if TLS v1.2 is required for this API's requests.
	 *
	 * @since 3.11.3
	 * @return bool
	 */
	public function require_tls_1_2() {
		return true;
	}


}
