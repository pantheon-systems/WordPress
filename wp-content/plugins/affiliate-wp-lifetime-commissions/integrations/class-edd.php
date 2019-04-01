<?php

class Affiliate_WP_Lifetime_Commissions_EDD extends Affiliate_WP_Lifetime_Commissions_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {
		$this->context = 'edd';
	}

	/**
	 * Retrieve the email address of a customer from the EDD_Payment
	 *
	 * @access  public
	 * @since   2.0
	 * @return  string
	 */
	public function get_email( $payment_id = 0 ) {
		return edd_get_payment_user_email( $payment_id );	
	}

}
new Affiliate_WP_Lifetime_Commissions_EDD;
