<?php
/**
 * Robot Ninja Stock Manager class
 *
 * @author 	Prospress
 * @since 	1.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RN_Stock_Manager {

	/**
	 * Initialise Robot Ninja Helper Stock Manager class
	 *
	 * @since 1.5.2
	 */
	public static function init() {
		if ( defined( 'RN_REDUCE_STOCK' ) && ! RN_REDUCE_STOCK ) {
			add_action( 'woocommerce_can_reduce_order_stock', __CLASS__ . '::maybe_not_reduce_stock', 10, 2 );
		}
	}

	/**
	 * Make sure we don't reduce the stock levels of products for test orders.
	 *
	 * @since 1.5.2
	 * @param bool $reduce_stock
	 * @param WP_Order $order
	 * @return bool
	 */
	public static function maybe_not_reduce_stock( $reduce_stock, $order ) {
		if ( $reduce_stock && is_object( $order ) && $order->get_billing_email() ) {
			$billing_email = $order->get_billing_email();

			if ( preg_match( '/store[\+]guest[\-](\d+)[\@]robotninja.com/', $billing_email ) || preg_match( '/store[\+](\d+)[\@]robotninja.com/', $billing_email ) ) {
				$reduce_stock = false;
			}
		}

		return $reduce_stock;
	}
}
RN_Stock_Manager::init();
