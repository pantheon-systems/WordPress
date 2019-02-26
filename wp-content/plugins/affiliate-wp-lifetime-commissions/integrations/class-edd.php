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
     * Retrieves the user's email or ID
     *
     * @param string $get what to retrieve
     * @param int $reference Payment reference number
     *
     * @since 1.1
     */
    public function get( $get = '', $reference = 0, $context ) {

        if ( ! $get ) {
            return false;
        }

        if ( 'email' === $get ) {
            return edd_get_payment_user_email( $reference );
        } elseif ( 'user_id' === $get ) {
            return edd_get_payment_user_id( $reference );
        }

        return false;

    }

}
new Affiliate_WP_Lifetime_Commissions_EDD;
