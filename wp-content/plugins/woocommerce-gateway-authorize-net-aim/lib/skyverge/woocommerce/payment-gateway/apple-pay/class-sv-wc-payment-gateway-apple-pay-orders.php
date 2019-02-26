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
 * @package   SkyVerge/WooCommerce/Payment-Gateway/Apple-Pay
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2016, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_Payment_Gateway_Apple_Pay_Orders' ) ) :

/**
 * The Apple Pay order handler.
 *
 * @since 4.7.0
 */
class SV_WC_Payment_Gateway_Apple_Pay_Orders {


	/**
	 * Creates an order from a cart.
	 *
	 * @since 4.7.0
	 *
	 * @param \WC_Cart $cart cart object
	 *
	 * @throws \SV_WC_Payment_Gateway_Exception
	 */
	public static function create_order( WC_Cart $cart ) {

		// ensure totals are fully calculated by simulating checkout in WC 3.1 or lower
		// TODO: remove this when WC 3.2+ can be required {CW 2017-11-17}
		if ( SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.2' ) && ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}

		$cart->calculate_totals();

		try {

			wc_transaction_query( 'start' );

			$order_data = array(
				'status'      => apply_filters( 'woocommerce_default_order_status', 'pending' ),
				'customer_id' => get_current_user_id(),
				'cart_hash'   => md5( json_encode( wc_clean( $cart->get_cart_for_session() ) ) . $cart->total ),
				'created_via' => 'apple_pay',
			);

			$order = self::get_order_object( $order_data );

			foreach ( $cart->get_cart() as $cart_item_key => $item ) {

				$args = array(
					'variation' => $item['variation'],
					'totals'    => array(
						'subtotal'     => $item['line_subtotal'],
						'subtotal_tax' => $item['line_subtotal_tax'],
						'total'        => $item['line_total'],
						'tax'          => $item['line_tax'],
						'tax_data'     => $item['line_tax_data']
					),
				);

				if ( ! $order->add_product( $item['data'], $item['quantity'], $args ) ) {
					throw new SV_WC_Payment_Gateway_Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce-plugin-framework' ), 525 ) );
				}
			}

			foreach ( $cart->get_coupons() as $code => $coupon ) {

				if ( ! SV_WC_Order_Compatibility::add_coupon( $order, $code, $cart->get_coupon_discount_amount( $code ), $cart->get_coupon_discount_tax_amount( $code ) ) ) {
					throw new SV_WC_Payment_Gateway_Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce-plugin-framework' ), 529 ) );
				}
			}

			$chosen_methods = WC()->session->get( 'chosen_shipping_methods', array() );

			foreach ( WC()->shipping->get_packages() as $key => $package ) {

				if ( isset( $package['rates'][ $chosen_methods[ $key ] ] ) ) {

					$method = $package['rates'][ $chosen_methods[ $key ] ];

					if ( ! SV_WC_Order_Compatibility::add_shipping( $order, $method ) ) {
						throw new SV_WC_Payment_Gateway_Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce-plugin-framework' ), 527 ) );
					}
				}
			}

			// add fees
			foreach ( $cart->get_fees() as $key => $fee ) {

				if ( ! SV_WC_Order_Compatibility::add_fee( $order, $fee ) ) {
					throw new SV_WC_Payment_Gateway_Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce-plugin-framework' ), 526 ) );
				}
			}

			$cart_taxes     = SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.2' ) ? $cart->get_cart_contents_taxes() : $cart->taxes;
			$shipping_taxes = SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.2' ) ? $cart->get_shipping_taxes() : $cart->shipping_taxes;

			foreach ( array_keys( $cart_taxes + $shipping_taxes ) as $rate_id ) {

				if ( $rate_id && apply_filters( 'woocommerce_cart_remove_taxes_zero_rate_id', 'zero-rated' ) !== $rate_id ) {

					if ( ! SV_WC_Order_Compatibility::add_tax( $order, $rate_id, $cart->get_tax_amount( $rate_id ), $cart->get_shipping_tax_amount( $rate_id ) ) ) {
						throw new SV_WC_Payment_Gateway_Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce-plugin-framework' ), 526 ) );
					}
				}
			}

			wc_transaction_query( 'commit' );

			$order->update_taxes();

			$order->calculate_totals( false ); // false to skip recalculating taxes

			do_action( 'woocommerce_checkout_update_order_meta', SV_WC_Order_Compatibility::get_prop( $order, 'id' ), array() );

			return $order;

		} catch ( SV_WC_Payment_Gateway_Exception $e ) {

			wc_transaction_query( 'rollback' );

			throw $e;
		}
	}


	/**
	 * Gets an order object for payment.
	 *
	 * @since 4.7.0
	 *
	 * @param array $order_data the order data
	 * @return \WC_Order
	 *
	 * @throws \SV_WC_Payment_Gateway_Exception
	 */
	public static function get_order_object( $order_data ) {

		$order_id = (int) WC()->session->get( 'order_awaiting_payment', 0 );

		if ( $order_id && $order_data['cart_hash'] === get_post_meta( $order_id, '_cart_hash', true ) && ( $order = wc_get_order( $order_id ) ) && $order->has_status( array( 'pending', 'failed' ) ) ) {

			$order_data['order_id'] = $order_id;

			$order = wc_update_order( $order_data );

			if ( is_wp_error( $order ) ) {
				throw new SV_WC_Payment_Gateway_Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce-plugin-framework' ), 522 ) );
			} else {
				$order->remove_order_items();
			}

		} else {

			$order = wc_create_order( $order_data );

			if ( is_wp_error( $order ) ) {
				throw new SV_WC_Payment_Gateway_Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce-plugin-framework' ), 520 ) );
			} elseif ( false === $order ) {
				throw new SV_WC_Payment_Gateway_Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce-plugin-framework' ), 521 ) );
			}

			// set the new order ID so it can be resumed in case of failure
			WC()->session->set( 'order_awaiting_payment', SV_WC_Order_Compatibility::get_prop( $order, 'id' ) );
		}

		return $order;
	}


}

endif;
