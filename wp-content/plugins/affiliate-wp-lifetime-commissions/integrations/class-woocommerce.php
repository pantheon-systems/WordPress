<?php

class Affiliate_WP_Lifetime_Commissions_WooCommerce extends Affiliate_WP_Lifetime_Commissions_Base {
	
	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {
		$this->context = 'woocommerce';
	}

	/**
	 * Retrieve the email address of a customer from the WC_Order
	 *
	 * @access  public
	 * @since   2.0
	 * @return  string
	 */
	public function get_email( $order_id = 0 ) {
		
		$email = '';
		$order = wc_get_order( $order_id );

		if( $order ) {

			if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
				$email = $order->get_billing_email();
			} else {
				$email = $order->billing_email;
			}

		}

		return $email;
	}

}
new Affiliate_WP_Lifetime_Commissions_WooCommerce;
