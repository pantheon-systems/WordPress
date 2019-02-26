<?php

class Affiliate_WP_Lifetime_Commissions_RCP extends Affiliate_WP_Lifetime_Commissions_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
    public function init() {
        $this->context = 'rcp';
        $this->table_name = function_exists( 'rcp_get_payments_db_name' ) ? rcp_get_payments_db_name() : '';
    }

    /**
     * Retrieves the user's email or ID by payment ID
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

        global $wpdb;

        $user_id = $wpdb->get_var( "SELECT user_id FROM $this->table_name WHERE subscription_key = '$reference' LIMIT 1;" );

        if ( 'email' === $get ) {

            if ( $user_id ) {

                $user_info = get_userdata( $user_id );
                $email     = $user_info->user_email;

            } else {
                $email = $_POST['rcp_user_email'];
            }

            return $email;

        } elseif ( 'user_id' === $get ) {
            return $user_id;
        }

        return false;

    }

}
new Affiliate_WP_Lifetime_Commissions_RCP;
