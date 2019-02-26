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
 * Authorize.Net AIM Response Class
 *
 * Parses XML received from the Authorize.Net AIM API, the general response body looks like:
 *
 * <?xml version="1.0" encoding="utf-8"?>
 * <createTransactionResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
 * 	<refId>123456</refId>
 * 	<messages>
 * 		<resultCode>Ok</resultCode>
 * 		<message>
 * 			<code>I00001</code>
 * 			<text>Successful.</text>
 * 		</message>
 * 	</messages>
 * 	<transactionResponse>
 * 		<responseCode>1</responseCode>
 * 		<authCode>UGELQC</authCode>
 * 		<avsResultCode>E</avsResultCode>
 * 		<cavvResultCode />
 * 		<transId>2148061808</transId>
 * 		<refTransID />
 *		<transHash>0B428D8A928AAC61121AF2F6EAC5FF3F</transHash>
 * 		<testRequest>0</testRequest>
 * 		<accountNumber>XXXX0015</accountNumber>
 * 		<accountType>MasterCard</accountType>
 * 		<message>
 * 			<code>1</code>
 * 			<description>This transaction has been approved.</description>
 * 		</message>
 * 		<userFields>
 * 			<userField>
 * 				<name>MerchantDefinedFieldName1</name>
 * 				<value>MerchantDefinedFieldValue1</value>
 * 			</userField>
 * 		</userFields>
 * 	</transactionResponse>
 * </createTransactionResponse>
 *
 * Note: the (string) casts here are critical, without these you'll tend to get untraceable
 * errors like "Serialization of 'SimpleXMLElement' is not allowed"
 *
 * @link http://www.authorize.net/support/AIM_guide_XML.pdf
 *
 * @since 3.0
 * @see SV_WC_Payment_Gateway_API_Response
 */
class WC_Authorize_Net_AIM_API_Response extends SV_WC_API_XML_Response implements SV_WC_Payment_Gateway_API_Response, SV_WC_Payment_Gateway_API_Authorization_Response {


	/** approved transaction response code */
	const TRANSACTION_APPROVED = '1';

	/** declined transaction response code */
	const TRANSACTION_DECLINED = '2';

	/** error with transaction response code */
	const TRANSACTION_ERROR = '3';

	/** held for review transaction response code */
	const TRANSACTION_HELD = '4';

	/** CSC match code */
	const CSC_MATCH = 'M';


	/**
	 * Build a response object from the raw response xml
	 *
	 * @since 3.0
	 * @param string $raw_response_xml the raw response XML
	 */
	public function __construct( $raw_response_xml ) {

		// Remove namespace as SimpleXML throws warnings with invalid namespace URI provided by Authorize.Net
		 $raw_response_xml = preg_replace( '/[[:space:]]xmlns[^=]*="[^"]*"/i', '', $raw_response_xml );

		parent::__construct( $raw_response_xml );
	}


	/**
	 * Checks if response contains an API error code
	 *
	 * @since 3.0
	 * @return bool true if has API error, false otherwise
	 */
	public function has_api_error() {

		if ( ! isset( $this->response_xml->messages->resultCode ) ) {
			return true;
		}

		return 'error' == strtolower( (string) $this->response_xml->messages->resultCode );
	}


	/**
	 * Gets the API error code
	 *
	 * @since 3.0
	 * @return string
	 */
	public function get_api_error_code() {

		if ( ! isset( $this->response_xml->messages->message->code ) ) {
			return __( 'N/A', 'woocommerce-gateway-authorize-net-aim' );
		}

		return (string) $this->response_xml->messages->message->code;
	}


	/**
	 * Gets the API error message
	 *
	 * @since 3.0
	 * @return string
	 */
	public function get_api_error_message() {

		if ( ! isset( $this->response_xml->messages->message->text ) ) {
			return __( 'N/A', 'woocommerce-gateway-authorize-net-aim' );
		}

		$message = (string) $this->response_xml->messages->message->text;

		// E00027 is a generic decline error that is returned as an API error but includes additional error messages
		// that are valuable to include
		if ( 'E00027' == $this->get_api_error_code() ) {
			$message .= ' ' . $this->get_transaction_status_message();
		}

		return $message;
	}


	/**
	 * Checks if the response is from a test request which means all the response
	 * data is bogus.
	 *
	 * @since 3.2.1
	 * @return bool true if testRequest element is present, false otherwise
	 */
	public function is_test_request() {

		return isset( $this->response_xml->transactionResponse->testRequest ) && '1' === (string) $this->response_xml->transactionResponse->testRequest;
	}


	/**
	 * Checks if the transaction was successful
	 *
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API_Response::transaction_approved()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_approved() {

		return ! $this->has_api_error() && self::TRANSACTION_APPROVED === $this->get_transaction_response_code();
	}


	/**
	 * Returns true if the transaction was held, for instance due to AVS/CSC
	 * Fraud Settings.  This indicates that the transaction was successful, but
	 * did not pass a fraud check and should be reviewed
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API_Response::transaction_held()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_held() {

		return ! $this->has_api_error() && self::TRANSACTION_HELD === $this->get_transaction_response_code();
	}


	/**
	 * Determines if the transaction was held for Fraud Filter reasons.
	 *
	 * This checks for both AVS & CSC result codes that indicate fraud, but only
	 * if the merchant's account is configured to hold them.
	 *
	 * @since 3.12.2
	 *
	 * @return bool
	 */
	public function transaction_held_for_fraud() {

		$avs_codes = array(
			'E',
			'R',
			'G',
			'U',
			'S',
			'N',
			'A',
			'Z',
			'W',
			'X',
		);

		$csc_codes = array(
			'N',
			'P',
			'S',
			'U',
		);

		return $this->transaction_held() && ( in_array( $this->get_avs_result(), $avs_codes, true ) || in_array( $this->get_csc_result(), $csc_codes, true ) );
	}


	/**
	 * Gets the response transaction id, or null if there is no transaction id
	 * associated with this transaction
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API_Response::get_transaction_id()
	 * @return string transaction id
	 */
	public function get_transaction_id() {

		return isset( $this->response_xml->transactionResponse->transId ) ? (string) $this->response_xml->transactionResponse->transId : null;
	}


	/**
	 * Gets the transaction status message: API error message if there was an
	 * API error, otherwise the transaction status message
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API_Response::get_status_message()
	 * @return string status message
	 */
	public function get_status_message() {

		if ( $this->has_api_error() ) {

			// E00027 is a generic decline error that is returned as an API error but includes additional error messages
			// that are valuable to include
			if ( 'E00027' == $this->get_api_error_code() ) {

				return $this->get_api_error_message() . ' ' . $this->get_transaction_status_message();

			} else {

				return $this->get_api_error_message();
			}
		}

		return $this->get_transaction_status_message();
	}


	/**
	 * Gets the transaction status code: API error code if there was an
	 * API error, otherwise the transaction status code
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API_Response::get_status_code()
	 * @return string status code
	 */
	public function get_status_code() {

		if ( $this->has_api_error() ) return $this->get_api_error_code();

		return $this->get_transaction_response_code();
	}


	/**
	 * Gets the transaction status code
	 *
	 * @since 3.0
	 * @return string transaction status code or null if none was found
	 */
	public function get_transaction_response_code() {

		return isset( $this->response_xml->transactionResponse->responseCode ) ? (string) $this->response_xml->transactionResponse->responseCode : null;
	}


	/**
	 * Gets the transaction status message
	 *
	 * @since 3.0
	 * @return string transaction status message
	 */
	public function get_transaction_status_message() {

		$messages = array();

		// messages
		if ( isset( $this->response_xml->transactionResponse->messages->message ) ) {

			foreach ( $this->response_xml->transactionResponse->messages->message as $message ) {

				$messages[] = sprintf( __( 'Message Code: %s - %s', 'woocommerce-gateway-authorize-net-aim' ), (string) $message->code, (string) $message->description );
			}
		}

		// errors
		if ( isset( $this->response_xml->transactionResponse->errors->error ) ) {

			foreach ( $this->response_xml->transactionResponse->errors->error as $error ) {

				$messages[] = sprintf( __( 'Error Code: %s - %s', 'woocommerce-gateway-authorize-net-aim' ), (string) $error->errorCode, (string) $error->errorText );
			}
		}

		return implode( ',', $messages );
	}


	/**
	 * Returns the response reason code
	 *
	 * @since 3.4.0
	 * @return string response reason code
	 */
	public function get_transaction_response_reason_code() {

		return isset( $this->response_xml->transactionResponse->errors->error->errorCode ) ? (string) $this->response_xml->transactionResponse->errors->error->errorCode : null;
	}


	/**
	 * Returns the response reason code
	 *
	 * @since 3.4.0
	 * @return string response reason code
	 */
	public function get_transaction_response_reason_text() {
		return isset( $this->response_xml->transactionResponse->errors->error->errorText ) ? $this->response_xml->transactionResponse->errors->error->errorText : null;
	}


	/**
	 * The authorization code is returned from the credit card processor to
	 * indicate that the charge will be paid by the card issuer
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API_Authorization_Response::get_authorization_code()
	 * @return string 6 character credit card authorization code
	 */
	public function get_authorization_code() {

		return isset( $this->response_xml->transactionResponse->authCode ) ? (string) $this->response_xml->transactionResponse->authCode : null;
	}


	/**
	 * Returns the result of the AVS check
	 *
	 * see page 49 of the AIM XML developer documentation for explanations
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API_Authorization_Response::get_avs_result()
	 * @return string result of the AVS check, if any
	 */
	public function get_avs_result() {

		return isset( $this->response_xml->transactionResponse->avsResultCode ) ? (string) $this->response_xml->transactionResponse->avsResultCode : null;
	}


	/**
	 * Returns the result of the CSC check
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API_Authorization_Response::get_csc_result()
	 * @return string result of CSC check
	 */
	public function get_csc_result() {

		return isset( $this->response_xml->transactionResponse->cvvResultCode ) ? (string) $this->response_xml->transactionResponse->cvvResultCode : null;
	}


	/**
	 * Returns true if the CSC check was successful
	 *
	 * see page 50 of the AIM XML developer documentation for CSC response code explanations
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API_Authorization_Response::csc_match()
	 * @return boolean true if the CSC check was successful
	 */
	public function csc_match() {

		return self::CSC_MATCH == $this->get_csc_result();
	}


	/**
	 * Returns the result of the CAVV (Cardholder authentication verification) check
	 *
	 * @since 3.0
	 * @see SV_WC_Payment_Gateway_API_Authorization_Response::get_csc_result()
	 * @return string result of CAVV check
	 */
	public function get_cavv_result() {

		return isset( $this->response_xml->transactionResponse->cavvResultCode ) ? (string) $this->response_xml->transactionResponse->cavvResultCode : null;
	}


	/**
	 * Gets any user-defined fields associated with the transaction response
	 *
	 * @since 3.0
	 * @return array transaction user-defined fields
	 */
	public function get_user_defined_fields() {

		$fields = array();

		if ( isset( $this->response_xml->transactionResponse->userFields->userField ) ) {

			foreach ( $this->response_xml->transactionResponse->userFields->userField as $user_field ) {

				$fields[ (string) $user_field->name ] = (string) $user_field->value;
			}
		}

		return $fields;
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

		$helper = new WC_Authorize_Net_AIM_API_Response_Message_Handler( $this );

		return $helper->get_message();
	}


	/**
	 * Get the payment type: 'credit-card', 'echeck', etc
	 *
	 * @since 3.6.0
	 * @return string payment type or null if not available
	 */
	public function get_payment_type() {

		if ( ! isset( $this->response_xml->transactionResponse->accountType ) ) {
			return null;
		}

		return ( 'eCheck' === $this->response_xml->transactionResponse->accountType ) ? 'echeck' : 'credit-card';
	}


}
