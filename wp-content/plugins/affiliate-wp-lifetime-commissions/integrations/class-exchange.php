<?php

class Affiliate_WP_Lifetime_Commissions_Exchange extends Affiliate_WP_Lifetime_Commissions_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {
		$this->context = 'it-exchange';
	}

	/**
	 * Retrieve the email address of a customer from the order data
	 *
	 * @access  public
	 * @since   2.0
	 * @return  string
	 */
	public function get_email( $reference = 0 ) {

		// return if no payment ID
		if ( ! $reference ) {
			return false;
		}

		// get exchange cart object
		$payment_meta   = get_post_meta( $reference, '_it_exchange_cart_object', true );

		$guest_checkout = isset( $payment_meta->is_guest_checkout ) ? $payment_meta->is_guest_checkout : false;

		// if logged in, get email from payment meta
		if ( ! $guest_checkout ) {
			return $payment_meta->shipping_address['email'];
		} else {
			// get it from the ID field for guest purchases
			return get_post_meta( $reference, '_it_exchange_customer_id', true );
		}

		return false;

	}

}
new Affiliate_WP_Lifetime_Commissions_Exchange;
