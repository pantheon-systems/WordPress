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

        $order = new MemberOrder( $reference );

        if ( 'email' === $get ) {
            return $order->Email;
        } elseif ( 'user_id' === $get ) {
            return $order->user_id;
        }

        return false;

    }

}
new Affiliate_WP_Lifetime_Commissions_PMP;
