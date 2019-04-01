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
	 * Retrieve the email address of a customer from the subsccription key
	 *
	 * @access  public
	 * @since   2.0
	 * @return  string
	 */
	public function get_email( $reference = 0 ) {

		global $wpdb;

		$email   = '';
		$user_id = $wpdb->get_var( "SELECT user_id FROM $this->table_name WHERE subscription_key = '$reference' LIMIT 1;" );

		if ( $user_id ) {

			$user_info = get_userdata( $user_id );
			$email     = $user_info->user_email;

		} else if ( ! empty( $_POST['rcp_user_email'] ) ) {

			$email = sanitize_text_field( $_POST['rcp_user_email'] );
		
		}

		return $email;

	}

}
new Affiliate_WP_Lifetime_Commissions_RCP;
