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
 * Authorize.Net AIM API Response Message Handler
 *
 * Builds customer-friendly response messages by mapping the various Authorize.Net
 * error codes to standardized messages
 *
 * @link http://www.authorize.net/support/AIM_guide_XML.pdf for listing of error codes
 *
 * @since 3.8.0
 * @see SV_WC_Payment_Gateway_API_Response_Message_Helper
 */
class WC_Authorize_Net_AIM_API_Response_Message_Handler extends SV_WC_Payment_Gateway_API_Response_Message_Helper {


	/** @var \WC_Authorize_Net_AIM_API_Response transaction response */
	protected $response;

	/** @var array decline reasons */
	protected $reasons = array(

		// declined
		'2' => array(
			'2'   => 'card_declined',
			'3'   => 'card_declined',
			'4'   => 'card_declined',
			'27'  => 'avs_mismatch',
			'28'  => 'card_type_not_accepted',
			'35'  => 'error',
			'37'  => 'card_number_invalid',
			'44'  => 'csc_mismatch',
			'45'  => 'card_declined',
			'65'  => 'csc_mismatch',
			'127' => 'avs_mismatch',
			'165' => 'csc_mismatch',
			'250' => 'decline',
			'251' => 'decline',
			'254' => 'decline',
			'315' => 'card_number_invalid',
			'316' => 'card_expiry_invalid',
			'317' => 'card_expired',
		),

		// processing error
		'3' => array(
			'8'   => 'card_expired',
			'9'   => 'bank_aba_invalid',
			'10'  => 'bank_account_number_invalid',
			'17'  => 'card_type_not_accepted',
			'19'  => 'authorize_net_error_try_later',
			'20'  => 'authorize_net_error_try_later',
			'21'  => 'authorize_net_error_try_later',
			'22'  => 'authorize_net_error_try_later',
			'23'  => 'authorize_net_error_try_later',
			'36'  => 'authorize_net_authorized_but_not_settled',
			'52'  => 'authorize_net_authorized_but_not_settled',
			'57'  => 'authorize_net_error_try_later',
			'58'  => 'authorize_net_error_try_later',
			'59'  => 'authorize_net_error_try_later',
			'60'  => 'authorize_net_error_try_later',
			'61'  => 'authorize_net_error_try_later',
			'62'  => 'authorize_net_error_try_later',
			'63'  => 'authorize_net_error_try_later',
			'66'  => 'decline',
			'101' => 'authorize_net_echeck_mismatch',
			'128' => 'decline',
		),

		// held for review
		'4' => array(
			'193' => 'held_for_review',
			'252' => 'held_for_review',
			'253' => 'held_for_review',
		)
	);


	/**
	 * Initialize the API response message handler
	 *
	 * @since 3.8.0
	 * @param \WC_Authorize_Net_AIM_API_Response $response
	 */
	public function __construct( $response ) {

		$this->response = $response;
	}


	/**
	 * Get the user-facing error/decline message. Used in place of the get_user_message()
	 * method because this class is instantiated with the response class and handles
	 * generating the message ID internally
	 *
	 * Note that API errors (e.g. E00039) cannot be parsed here as they are considered
	 * exceptions and thus do not use the response class at all.
	 *
	 * @since 3.8.0
	 * @return string
	 */
	public function get_message() {

		$response_code        = $this->get_response()->get_transaction_response_code();
		$response_reason_code = $this->get_response()->get_transaction_response_reason_code();

		$message_id = isset( $this->reasons[ $response_code ][ $response_reason_code ] ) ? $this->reasons[ $response_code ][ $response_reason_code ] : null;

		return $this->get_user_message( $message_id );
	}


	/**
	 * Returns a message appropriate for a frontend user.  This should be used
	 * to provide enough information to a user to allow them to resolve an
	 * issue on their own, but not enough to help nefarious folks fishing for
	 * info. Adds a few custom authorize.net-specific user error messages.
	 *
	 * @since 3.8.0
	 * @see SV_WC_Payment_Gateway_API_Response_Message_Helper::get_user_message()
	 * @param string $message_id identifies the message to return
	 * @return string a user message
	 */
	public function get_user_message( $message_id ) {

		switch ( $message_id ) {

			case 'authorize_net_error_try_later':
				$message = __( 'Oops, sorry! A temporary error occurred. Please try again in 5 minutes.', 'woocommerce-gateway-authorize-net-aim' );
				break;

			case 'authorize_net_authorized_but_not_settled':
				$message = __( 'This transaction was authorized successfully, but could not be settled. Please contact us.', 'woocommerce-gateway-authorize-net-aim' );
				break;

			case 'authorize_net_echeck_mismatch':
				$message = __( 'The name and/or bank account type does not match. Please re-enter and try again.', 'woocommerce-gateway-authorize-net-aim' );
				break;

			default:
				$message = parent::get_user_message( $message_id );
		}

		return apply_filters( 'wc_authorize_net_aim_api_response_user_message', $message, $message_id, $this );
	}


	/**
	 * Return the response object for this user message
	 *
	 * @since 3.8.0
	 * @return \WC_Authorize_Net_AIM_API_Response
	 */
	public function get_response() {

		return $this->response;
	}


}
