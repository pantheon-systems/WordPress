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
 * @package   SkyVerge/WooCommerce/Payment-Gateway/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_Payment_Gateway_Integration_Pre_Orders' ) ) :

/**
 * Pre-Orders Integration
 *
 * @since 4.1.0
 */
class SV_WC_Payment_Gateway_Integration_Pre_Orders extends SV_WC_Payment_Gateway_Integration {


	/**
	 * Bootstrap class
	 *
	 * @since 4.1.0
	 * @param \SV_WC_Payment_Gateway_Direct $gateway
	 */
	public function __construct( SV_WC_Payment_Gateway_Direct $gateway ) {

		parent::__construct( $gateway );

		// add hooks
		$this->add_support();
	}


	/**
	 * Adds support for pre-orders by hooking in some necessary actions
	 *
	 * @since 4.1.0
	 */
	public function add_support() {

		$this->get_gateway()->add_support( array( 'pre-orders' ) );

		// force tokenization when needed
		add_filter( 'wc_payment_gateway_' . $this->get_gateway()->get_id() . '_tokenization_forced', array( $this, 'maybe_force_tokenization' ) );

		// add pre-orders data to the order object
		add_filter( 'wc_payment_gateway_' . $this->get_gateway()->get_id() . '_get_order', array( $this, 'get_order' ) );

		// process pre-order initial payment as needed
		add_filter( 'wc_payment_gateway_' . $this->get_gateway()->get_id() . '_process_payment', array( $this, 'process_payment' ), 10, 2 );

		// process batch pre-order payments
		add_action( 'wc_pre_orders_process_pre_order_completion_payment_' . $this->get_gateway()->get_id(), array( $this, 'process_release_payment' ) );
	}


	/**
	 * Force tokenization for pre-orders
	 *
	 * @since 4.1.0
	 * @see SV_WC_Payment_Gateway::tokenization_forced()
	 * @param boolean $force_tokenization whether tokenization should be forced
	 * @return boolean true if tokenization should be forced, false otherwise
	 */
	public function maybe_force_tokenization( $force_tokenization ) {

		// pay page with pre-order?
		$pay_page_pre_order = false;
		if ( $this->get_gateway()->is_pay_page_gateway() ) {

			$order_id  = $this->get_gateway()->get_checkout_pay_page_order_id();

			if ( $order_id ) {
				$pay_page_pre_order = WC_Pre_Orders_Order::order_contains_pre_order( $order_id ) && WC_Pre_Orders_Product::product_is_charged_upon_release( WC_Pre_Orders_Order::get_pre_order_product( $order_id ) );
			}
		}

		if ( ( WC_Pre_Orders_Cart::cart_contains_pre_order() && WC_Pre_Orders_Product::product_is_charged_upon_release( WC_Pre_Orders_Cart::get_pre_order_product() ) ) ||
			 $pay_page_pre_order ) {

			// always tokenize the card for pre-orders that are charged upon release
			$force_tokenization = true;
		}

		return $force_tokenization;
	}


	/**
	 * Adds pre-orders data to the order object.  Filtered onto SV_WC_Payment_Gateway::get_order()
	 *
	 * @since 4.1.0
	 * @see SV_WC_Payment_Gateway::get_order()
	 * @param WC_Order $order the order
	 * @return WC_Order the orders
	 */
	public function get_order( $order ) {

		// bail if order doesn't contain a pre-order
		if ( ! WC_Pre_Orders_Order::order_contains_pre_order( $order ) ) {
			return $order;
		}

		if ( WC_Pre_Orders_Order::order_requires_payment_tokenization( $order ) ) {

			// normally a guest user wouldn't be assigned a customer id, but for a pre-order requiring tokenization, it might be
			if ( 0 == $order->get_user_id() && false !== ( $customer_id = $this->get_gateway()->get_guest_customer_id( $order ) ) )
				$order->customer_id = $customer_id;

		} elseif ( WC_Pre_Orders_Order::order_has_payment_token( $order ) && ! is_checkout_pay_page() ) {

			// if this is a pre-order release payment with a tokenized payment method, get the payment token to complete the order

			// retrieve the payment token
			$order->payment->token = $this->get_gateway()->get_order_meta( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), 'payment_token' );

			// retrieve the optional customer id
			$order->customer_id = $this->get_gateway()->get_order_meta( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), 'customer_id' );

			// set token data on order
			if ( $this->get_gateway()->get_payment_tokens_handler()->user_has_token( $order->get_user_id(), $order->payment->token ) ) {

				// an existing registered user with a saved payment token
				$token = $this->get_gateway()->get_payment_tokens_handler()->get_token( $order->get_user_id(), $order->payment->token );

				// account last four
				$order->payment->account_number = $token->get_last_four();

				if ( $this->get_gateway()->is_credit_card_gateway() ) {

					// card type
					$order->payment->card_type = $token->get_card_type();

					// exp month/year
					$order->payment->exp_month  = $token->get_exp_month();
					$order->payment->exp_year   = $token->get_exp_year();

				} elseif ( $this->get_gateway()->is_echeck_gateway() ) {

					// account type (checking/savings)
					$order->payment->account_type = $token->get_account_type();
				}

			} else {

				// a guest user means that token data must be set from the original order

				// account number
				$order->payment->account_number = $this->get_gateway()->get_order_meta( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), 'account_four' );

				if ( $this->get_gateway()->is_credit_card_gateway() ) {

					// card type
					$order->payment->card_type = $this->get_gateway()->get_order_meta( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), 'card_type' );

					// expiry date
					if ( $expiry_date = $this->get_gateway()->get_order_meta( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), 'card_expiry_date' ) ) {
						list( $exp_year, $exp_month ) = explode( '-', $expiry_date );
						$order->payment->exp_month  = $exp_month;
						$order->payment->exp_year   = $exp_year;
					}

				} elseif ( $this->get_gateway()->is_echeck_gateway() ) {

					// account type
					$order->payment->account_type = $this->get_gateway()->get_order_meta( SV_WC_Order_Compatibility::get_prop( $order, 'id' ), 'account_type' );
				}
			}
		}

		return $order;
	}


	/**
	 * Handle the pre-order initial payment/tokenization, or defer back to the normal payment
	 * processing flow
	 *
	 * @since 4.1.0
	 * @see SV_WC_Payment_Gateway::process_payment()
	 * @param boolean $result the result of this pre-order payment process
	 * @param int $order_id the order identifier
	 * @return true|array true to process this payment as a regular transaction, otherwise
	 *         return an array containing keys 'result' and 'redirect'
	 */
	public function process_payment( $result, $order_id ) {

		// processing pre-order
		if ( WC_Pre_Orders_Order::order_contains_pre_order( $order_id ) &&
			 WC_Pre_Orders_Order::order_requires_payment_tokenization( $order_id ) ) {

			$order = $this->get_gateway()->get_order( $order_id );

			try {

				// using an existing tokenized payment method
				if ( isset( $order->payment->token ) && $order->payment->token ) {

					// save the tokenized card info for completing the pre-order in the future
					$this->get_gateway()->add_transaction_data( $order );

				} else {

					// otherwise tokenize the payment method
					$order = $this->get_gateway()->get_payment_tokens_handler()->create_token( $order );
				}

				// mark order as pre-ordered / reduce order stock
				WC_Pre_Orders_Order::mark_order_as_pre_ordered( $order );

				// empty cart
				WC()->cart->empty_cart();

				// redirect to thank you page
				$result = array(
						'result'   => 'success',
						'redirect' => $this->get_gateway()->get_return_url( $order ),
				);

			} catch( SV_WC_Payment_Gateway_Exception $e ) {

				$this->get_gateway()->mark_order_as_failed( $order, sprintf( __( 'Pre-Order Tokenization attempt failed (%s)', 'woocommerce-plugin-framework' ), $this->get_gateway()->get_method_title(), $e->getMessage() ) );

				$result = array(
						'result'  => 'failure',
						'message' => $e->getMessage(),
				);
			}
		}

		return $result;
	}


	/**
	 * Process a pre-order payment when the pre-order is released
	 *
	 * @since 4.1.0
	 * @param \WC_Order $order original order containing the pre-order
	 */
	public function process_release_payment( $order ) {

		try {

			// set order defaults
			$order = $this->get_gateway()->get_order( SV_WC_Order_Compatibility::get_prop( $order, 'id' ) );

			// order description
			$order->description = sprintf( __( '%s - Pre-Order Release Payment for Order %s', 'woocommerce-plugin-framework' ), esc_html( SV_WC_Helper::get_site_name() ), $order->get_order_number() );

			// token is required
			if ( ! $order->payment->token ) {
				throw new SV_WC_Payment_Gateway_Exception( __( 'Payment token missing/invalid.', 'woocommerce-plugin-framework' ) );
			}

			// perform the transaction
			if ( $this->get_gateway()->is_credit_card_gateway() ) {

				if ( $this->get_gateway()->perform_credit_card_charge( $order ) ) {
					$response = $this->get_gateway()->get_api()->credit_card_charge( $order );
				} else {
					$response = $this->get_gateway()->get_api()->credit_card_authorization( $order );
				}

			} elseif ( $this->get_gateway()->is_echeck_gateway() ) {
				$response = $this->get_gateway()->get_api()->check_debit( $order );
			}

			// success! update order record
			if ( $response->transaction_approved() ) {

				$last_four = substr( $order->payment->account_number, -4 );

				// order note based on gateway type
				if ( $this->get_gateway()->is_credit_card_gateway() ) {

					$message = sprintf(
							__( '%s %s Pre-Order Release Payment Approved: %s ending in %s (expires %s)', 'woocommerce-plugin-framework' ),
							$this->get_gateway()->get_method_title(),
							$this->get_gateway()->perform_credit_card_authorization( $order ) ? 'Authorization' : 'Charge',
							SV_WC_Payment_Gateway_Helper::payment_type_to_name( ( ! empty( $order->payment->card_type ) ? $order->payment->card_type : 'card' ) ),
							$last_four,
							( ! empty( $order->payment->exp_month) && ! empty( $order->payment->exp_year ) ? $order->payment->exp_month . '/' . substr( $order->payment->exp_year, -2 ) : 'n/a' )
					);

				} elseif ( $this->get_gateway()->is_echeck_gateway() ) {

					// account type (checking/savings) may or may not be available, which is fine
					$message = sprintf( __( '%s eCheck Pre-Order Release Payment Approved: %s ending in %s', 'woocommerce-plugin-framework' ), $this->get_gateway()->get_method_title(), SV_WC_Payment_Gateway_Helper::payment_type_to_name( ( ! empty( $order->payment->account_type ) ? $order->payment->account_type : 'bank' ) ), $last_four );

				}

				// adds the transaction id (if any) to the order note
				if ( $response->get_transaction_id() ) {
					$message .= ' ' . sprintf( __( '(Transaction ID %s)', 'woocommerce-plugin-framework' ), $response->get_transaction_id() );
				}

				$order->add_order_note( $message );
			}

			if ( $response->transaction_approved() || $response->transaction_held() ) {

				// add the standard transaction data
				$this->get_gateway()->add_transaction_data( $order, $response );

				// allow the concrete class to add any gateway-specific transaction data to the order
				$this->get_gateway()->add_payment_gateway_transaction_data( $order, $response );

				// if the transaction was held (ie fraud validation failure) mark it as such
				if ( $response->transaction_held() || ( $this->get_gateway()->supports( SV_WC_Payment_Gateway::FEATURE_CREDIT_CARD_AUTHORIZATION ) && $this->get_gateway()->perform_credit_card_authorization( $order ) ) ) {

					$this->get_gateway()->mark_order_as_held( $order, $this->get_gateway()->supports( SV_WC_Payment_Gateway::FEATURE_CREDIT_CARD_AUTHORIZATION ) && $this->get_gateway()->perform_credit_card_authorization( $order ) ? __( 'Authorization only transaction', 'woocommerce-plugin-framework' ) : $response->get_status_message(), $response );

					SV_WC_Order_Compatibility::reduce_stock_levels( $order ); // reduce stock for held orders, but don't complete payment

				} else {
					// otherwise complete the order
					$order->payment_complete();
				}

			} else {

				// failure
				throw new SV_WC_Payment_Gateway_Exception( sprintf( '%s: %s', $response->get_status_code(), $response->get_status_message() ) );

			}

		} catch ( SV_WC_Plugin_Exception $e ) {

			// Mark order as failed
			$this->get_gateway()->mark_order_as_failed( $order, sprintf( __( 'Pre-Order Release Payment Failed: %s', 'woocommerce-plugin-framework' ), $e->getMessage() ) );

		}
	}
}


endif;  // class exists check
