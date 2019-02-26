<?php
/**
 * WooCommerce Payment Gateway Framework
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
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Payment-Gateway/API
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! interface_exists( 'SV_WC_Payment_Gateway_API' ) ) :

/**
 * WooCommerce Direct Payment Gateway API
 */
interface SV_WC_Payment_Gateway_API {


	/**
	 * Perform a credit card authorization for the given order
	 *
	 * If the gateway does not support credit card authorizations, this method can be a no-op.
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order
	 * @return SV_WC_Payment_Gateway_API_Response credit card charge response
	 * @throws SV_WC_Payment_Gateway_Exception network timeouts, etc
	 */
	public function credit_card_authorization( WC_Order $order );


	/**
	 * Perform a credit card charge for the given order
	 *
	 * If the gateway does not support credit card charges, this method can be a no-op.
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order
	 * @return SV_WC_Payment_Gateway_API_Response credit card charge response
	 * @throws SV_WC_Payment_Gateway_Exception network timeouts, etc
	 */
	public function credit_card_charge( WC_Order $order );


	/**
	 * Perform a credit card capture for a given authorized order
	 *
	 * If the gateway does not support credit card capture, this method can be a no-op.
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order
	 * @return SV_WC_Payment_Gateway_API_Response credit card capture response
	 * @throws SV_WC_Payment_Gateway_Exception network timeouts, etc
	 */
	public function credit_card_capture( WC_Order $order );


	/**
	 * Perform an eCheck debit (ACH transaction) for the given order
	 *
	 * If the gateway does not support check debits, this method can be a no-op.
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order
	 * @return SV_WC_Payment_Gateway_API_Response check debit response
	 * @throws SV_WC_Payment_Gateway_Exception network timeouts, etc
	 */
	public function check_debit( WC_Order $order );


	/**
	 * Perform a refund for the given order
	 *
	 * If the gateway does not support refunds, this method can be a no-op.
	 *
	 * @since 3.1.0
	 * @param WC_Order $order order object
	 * @return SV_WC_Payment_Gateway_API_Response refund response
	 * @throws SV_WC_Payment_Gateway_Exception network timeouts, etc
	 */
	public function refund( WC_Order $order );


	/**
	 * Perform a void for the given order
	 *
	 * If the gateway does not support voids, this method can be a no-op.
	 *
	 * @since 3.1.0
	 * @param WC_Order $order order object
	 * @return SV_WC_Payment_Gateway_API_Response void response
	 * @throws SV_WC_Payment_Gateway_Exception network timeouts, etc
	 */
	public function void( WC_Order $order );


	/**
	 * Creates a payment token for the given order
	 *
	 * If the gateway does not support tokenization, this method can be a no-op.
	 *
	 * @since 1.0.0
	 * @param WC_Order $order the order
	 * @return SV_WC_Payment_Gateway_API_Create_Payment_Token_Response payment method tokenization response
	 * @throws SV_WC_Payment_Gateway_Exception network timeouts, etc
	 */
	public function tokenize_payment_method( WC_Order $order );


	/**
	 * Removes the tokenized payment method.  This method should not be invoked
	 * unless supports_remove_tokenized_payment_method() returns true, otherwise
	 * the results are undefined.
	 *
	 * @since 1.0.0
	 * @see SV_WC_Payment_Gateway_API::supports_remove_tokenized_payment_method()
	 * @param string $token the payment method token
	 * @param string $customer_id unique customer id for gateways that support it
	 * @return SV_WC_Payment_Gateway_API_Response remove tokenized payment method response
	 * @throws SV_WC_Payment_Gateway_Exception network timeouts, etc
	 */
	public function remove_tokenized_payment_method( $token, $customer_id );


	/**
	 * Returns true if this API supports a "remove tokenized payment method"
	 * request.  If this method returns true, then remove_tokenized_payment_method()
	 * is considered safe to call.
	 *
	 * @since 1.0.0
	 * @see SV_WC_Payment_Gateway_API::remove_tokenized_payment_method()
	 * @return boolean true if this API supports a "remove tokenized payment method" request, false otherwise
	 */
	public function supports_remove_tokenized_payment_method();


	/**
	 * Returns all tokenized payment methods for the customer.  This method
	 * should not be invoked unless supports_get_tokenized_payment_methods()
	 * return true, otherwise the results are undefined
	 *
	 * @since 1.0.0
	 * @see SV_WC_Payment_Gateway_API::supports_get_tokenized_payment_methods()
	 * @param string $customer_id unique customer id
	 * @return SV_WC_API_Get_Tokenized_Payment_Methods_Response response containing any payment tokens for the customer
	 * @throws SV_WC_Payment_Gateway_Exception network timeouts, etc
	 */
	public function get_tokenized_payment_methods( $customer_id );


	/**
	 * Returns true if this API supports a "get tokenized payment methods"
	 * request.  If this method returns true, then get_tokenized_payment_methods()
	 * is considered safe to call.
	 *
	 * @since 1.0.0
	 * @see SV_WC_Payment_Gateway_API::get_tokenized_payment_methods()
	 * @return boolean true if this API supports a "get tokenized payment methods" request, false otherwise
	 */
	public function supports_get_tokenized_payment_methods();


	/**
	 * Returns the most recent request object
	 *
	 * @since 1.0.0
	 * @return \SV_WC_Payment_Gateway_API_Request the most recent request object
	 */
	public function get_request();


	/**
	 * Returns the most recent response object
	 *
	 * @since 1.0.0
	 * @return \SV_WC_Payment_Gateway_API_Response the most recent response object
	 */
	public function get_response();


	/**
	 * Returns the WC_Order object associated with the request, if any
	 *
	 * @since 4.1.0
	 * @return \WC_Order
	 */
	public function get_order();


}

endif;  // interface exists check
