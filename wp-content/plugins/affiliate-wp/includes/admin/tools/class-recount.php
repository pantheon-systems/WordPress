<?php

class Affiliate_WP_Recount {

	/**
	 * Get things started
	 *
	 * @since       1.0
	 * @return      void
	 */
	public function __construct() {
		add_action( 'affwp_recount_stats', array( $this, 'process_recount' ) );
	}

	/**
	 * Process the recount
	 *
	 * @since       1.0
	 * @return      void
	 */
	public function process_recount( $data ) {

		if( ! is_admin() || ! current_user_can( 'manage_affiliates' ) ) {
			return;
		}

		$data = affiliate_wp()->utils->process_request_data( $data, 'user_name' );

		$user_id      = ! empty( $data['user_id'] ) ? absint( $data['user_id'] ) : false;
		$affiliate_id = affwp_get_affiliate_id( $user_id );

		if( empty( $user_id ) || empty( $affiliate_id ) ) {
			return;
		}

		$type = ! empty( $data['recount_type'] ) ? $data['recount_type'] : false;

		if( empty( $type ) ) {
			return;
		}

		switch( $type ) {

			case 'earnings' :
				$this->recount_earnings( $affiliate_id );
				break;

			case 'referrals' :
				$this->recount_referrals( $affiliate_id );
				break;

			case 'visits' :
				$this->recount_visits( $affiliate_id );
				break;

		}

		wp_redirect( affwp_admin_url( 'tools', array( 'tab' => 'recount', 'affwp_notice' => 'stats_recounted' ) ) );
		exit;
	}

	/**
	 * Recount earnings
	 *
	 * @since       1.0
	 * @return      void
	 */
	public function recount_earnings( $affiliate_id = 0 ) {

		if( empty( $affiliate_id ) ) {
			return;
		}

		$earnings = affiliate_wp()->referrals->paid_earnings( '', $affiliate_id, false );
		affiliate_wp()->affiliates->update( $affiliate_id, array( 'earnings' => $earnings ), '', 'affiliate' );
	}

	/**
	 * Recount referrals
	 *
	 * @since       1.0
	 * @return      void
	 */
	public function recount_referrals( $affiliate_id = 0 ) {

		if( empty( $affiliate_id ) ) {
			return;
		}

		$earnings = affiliate_wp()->referrals->count( array( 'affiliate_id' => $affiliate_id, 'status' => 'paid' ) );
		affiliate_wp()->affiliates->update( $affiliate_id, array( 'referrals' => $earnings ), '', 'affiliate' );

	}

	/**
	 * Recount visits
	 *
	 * @since       1.0
	 * @return      void
	 */
	public function recount_visits( $affiliate_id = 0  ) {

		if( empty( $affiliate_id ) ) {
			return;
		}

		$earnings = affiliate_wp()->visits->count( array( 'affiliate_id' => $affiliate_id ) );
		affiliate_wp()->affiliates->update( $affiliate_id, array( 'visits' => $earnings ), '', 'affiliate' );

	}


}
new Affiliate_WP_Recount;
