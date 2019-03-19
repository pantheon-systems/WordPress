<?php

class Affiliate_WP_Lifetime_Commissions_PMP extends Affiliate_WP_Lifetime_Commissions_Base {
	
	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {
		$this->context = 'pmp';
	}

	/**
	 * Retrieve the email address of a customer from the MemberOrder
	 *
	 * @access  public
	 * @since   2.0
	 * @return  string
	 */
	public function get_email( $reference = 0 ) {

		$email = '';

		$order = new MemberOrder( $reference );

		if ( isset( $order->Email ) ) {

			$email = $order->Email;

		}

		return $email;

	}

}
new Affiliate_WP_Lifetime_Commissions_PMP;
