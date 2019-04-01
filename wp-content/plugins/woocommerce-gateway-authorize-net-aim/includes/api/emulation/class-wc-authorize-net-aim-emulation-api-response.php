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
 * @package   WC-Gateway-Authorize-Net-AIM/API/Response
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Authorize.Net AIM Emulation Response Class
 *
 * Parses XML received from the Authorize.Net AIM NVP API
 *
 * @link http://www.authorize.net/support/AIM_guide.pdf
 *
 * @since 3.8.0
 * @see SV_WC_Payment_Gateway_API_Response
 */
class WC_Authorize_Net_AIM_Emulation_API_Response implements SV_WC_Payment_Gateway_API_Response, SV_WC_Payment_Gateway_API_Authorization_Response {


	/** approved transaction response code */
	const TRANSACTION_APPROVED = '1';

	/** held for review transaction response code */
	const TRANSACTION_HELD = '4';

	/** CSC match code */
	const CSC_MATCH = 'M';

	/** @var string raw response string */
	protected $raw_response;

	/** @var stdClass parsed response object */
	protected $response;


	/**
	 * Setup class
	 *
	 * @since 3.8.0
	 * @param string $raw_response the raw response
	 * @throws \SV_WC_Payment_Gateway_Exception
	 */
	public function __construct( $raw_response ) {

		$this->raw_response = $raw_response;

		$this->parse_response();
	}


	/**
	 * Parse the response string and set the parsed response object
	 *
	 * @since 3.8.0
	 * @throws \SV_WC_Payment_Gateway_Exception
	 */
	protected function parse_response() {

		// adjust response based on our hybrid delimiter :|: (delimiter = | encapsulation = :)
		// remove the leading encap character and add a trailing delimiter/encap character
		// so explode works correctly -- response string starts and ends with an encapsulation
		// character)
		$this->raw_response = ltrim( $this->raw_response, ':' ) . '|:';

		// parse response
		$response = explode( ':|:', $this->raw_response );

		if ( empty( $response ) ) {
			throw new SV_WC_Payment_Gateway_Exception( __( 'Could not parse direct response.', 'woocommerce-gateway-authorize-net-aim' ) );
		}

		// offset array by 1 to match Authorize.Net's order, mainly for readability
		array_unshift( $response, null );

		$this->response = new stdClass();

		// response fields are URL encoded, but we currently do not use any fields
		// (e.g. billing/shipping details) that would be affected by that
		$response_fields = array(
			'response_code'        => 1,
			'response_subcode'     => 2,
			'response_reason_code' => 3,
			'response_reason_text' => 4,
			'authorization_code'   => 5,
			'avs_response'         => 6,
			'transaction_id'       => 7,
			'amount'               => 10,
			'account_type'         => 11,
			'transaction_type'     => 12,
			'csc_response'         => 39,
			'cavv_response'        => 40,
			'account_last_four'    => 51,
			'card_type'            => 52,
		);

		foreach ( $response_fields as $field => $order ) {

			$this->response->$field = ( isset( $response[ $order ] ) ) ? $response[ $order ] : '';
		}
	}


	/**
	 * Checks if the transaction was successful
	 *
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Response::transaction_approved()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_approved() {


		return self::TRANSACTION_APPROVED === $this->get_status_code();
	}


	/**
	 * Returns true if the transaction was held, for instance due to AVS/CSC
	 * Fraud Settings.  This indicates that the transaction was successful, but
	 * did not pass a fraud check and should be reviewed
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Response::transaction_held()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_held() {

		return self::TRANSACTION_HELD === $this->get_status_code();
	}


	/**
	 * Gets the response transaction id, or null if there is no transaction id
	 * associated with this transaction
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Response::get_transaction_id()
	 * @return string transaction id
	 */
	public function get_transaction_id() {

		return $this->response->transaction_id;
	}


	/**
	 * Gets the transaction status message: API error message if there was an
	 * API error, otherwise the transaction status message
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Response::get_status_message()
	 * @return string status message
	 */
	public function get_status_message() {

		return $this->response->response_reason_text;
	}


	/**
	 * Gets the transaction status code
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Response::get_status_code()
	 * @return string status code
	 */
	public function get_status_code() {

		return $this->response->response_code;
	}


	/**
	 * The authorization code is returned from the credit card processor to
	 * indicate that the charge will be paid by the card issuer
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Authorization_Response::get_authorization_code()
	 * @return string 6 character credit card authorization code
	 */
	public function get_authorization_code() {

		return $this->response->authorization_code;
	}


	/**
	 * Returns the result of the AVS check
	 *
	 * see page 49 of the AIM XML developer documentation for explanations
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Authorization_Response::get_avs_result()
	 * @return string result of the AVS check, if any
	 */
	public function get_avs_result() {

		return $this->response->avs_response;
	}


	/**
	 * Returns the result of the CSC check
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Authorization_Response::get_csc_result()
	 * @return string result of CSC check
	 */
	public function get_csc_result() {

		return $this->response->csc_response;
	}


	/**
	 * Returns true if the CSC check was successful
	 *
	 * see page 50 of the AIM XML developer documentation for CSC response code explanations
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Authorization_Response::csc_match()
	 * @return boolean true if the CSC check was successful
	 */
	public function csc_match() {

		return self::CSC_MATCH == $this->get_csc_result();
	}


	/**
	 * Returns the result of the CAVV (Cardholder authentication verification) check
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Authorization_Response::get_csc_result()
	 * @return string result of CAVV check
	 */
	public function get_cavv_result() {

		return $this->response->cavv_response;
	}


	/**
	 * Returns a message appropriate for a frontend user.  This should be used
	 * to provide enough information to a user to allow them to resolve an
	 * issue on their own, but not enough to help nefarious folks fishing for
	 * info.
	 *
	 * @since 3.1.1-1
	 * @see SV_WC_Payment_Gateway_API_Response_Message_Helper
	 * @see SV_WC_Payment_Gateway_API_Response::get_user_message()
	 * @return string user message, if there is one
	 */
	public function get_user_message() {

		return '';
	}


	/**
	 * Get the payment type: 'credit-card', 'echeck', etc
	 *
	 * @since 3.8.0
	 * @return string payment type or null if not available
	 */
	public function get_payment_type() {

		return 'credit-card';
	}


	/**
	 * Return the string representation of the response
	 *
	 * @since 3.8.0
	 * @return string
	 */
	public function to_string() {
		return $this->raw_response;
	}


	/**
	 * Return the string representation of the response, stripped of any sensitive
	 * or confidential information to make it suitable for logging
	 *
	 * @since 3.8.0
	 * @return string
	 */
	public function to_string_safe() {

		// no sensitive data to mask in response
		return $this->raw_response;
	}


}
