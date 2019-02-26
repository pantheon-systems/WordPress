<?php

/**
 * Process the add referral request
 *
 * @since 1.2
 * @return void|false
 */
function affwp_process_add_referral( $data ) {

	if ( ! is_admin() ) {
		return false;
	}

	$errors = array();

	if ( ! current_user_can( 'manage_referrals' ) ) {
		wp_die( __( 'You do not have permission to manage referrals', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( ! wp_verify_nonce( $data['affwp_add_referral_nonce'], 'affwp_add_referral_nonce' ) ) {
		wp_die( __( 'Security check failed', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if( ! affiliate_wp()->affiliates->affiliate_exists( $data['user_name'] ) ) {
		$errors[ 'invalid_affiliate'] = __( 'Referral not created because affiliate is invalid.', 'affiliate-wp' );
	}

	if ( empty( $errors ) ) {

		if ( affwp_add_referral( $data ) ) {
			wp_safe_redirect( affwp_admin_url( 'referrals', array( 'affwp_notice' => 'referral_added' ) ) );
			exit;
		} else {
			wp_safe_redirect( affwp_admin_url( 'referrals', array( 'affwp_notice' => 'referral_add_failed' ) ) );
			exit;
		}

	} else {

		if ( isset( $errors[ 'invalid_affiliate'] ) ) {

			wp_safe_redirect( affwp_admin_url( 'referrals', array( 'action' => 'add_referral', 'affwp_notice' => 'referral_add_invalid_affiliate' ) ) );
			exit;

		}

		wp_safe_redirect( affwp_admin_url( 'referrals', array( 'affwp_notice' => 'referral_add_failed' ) ) );
		exit;

	}

}
add_action( 'affwp_add_referral', 'affwp_process_add_referral' );

/**
 * Process the update referral request
 *
 * @since 1.2
 * @return void
 */
function affwp_process_update_referral( $data ) {

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_referrals' ) ) {
		wp_die( __( 'You do not have permission to manage referrals', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( ! wp_verify_nonce( $data['affwp_edit_referral_nonce'], 'affwp_edit_referral_nonce' ) ) {
		wp_die( __( 'Security check failed', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( affiliate_wp()->referrals->update_referral( $data['referral_id'], $data ) ) {
		wp_safe_redirect( affwp_admin_url( 'referrals', array( 'affwp_notice' => 'referral_updated' ) ) );
		exit;
	} else {
		wp_safe_redirect( affwp_admin_url( 'referrals', array( 'affwp_notice' => 'referral_update_failed' ) ) );
		exit;
	}

}
add_action( 'affwp_process_update_referral', 'affwp_process_update_referral' );

/**
 * Process the delete referral request
 *
 * @since 1.7
 * @return void
 */
function affwp_process_delete_referral( $data ) {

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_referrals' ) ) {
		wp_die( __( 'You do not have permission to manage referrals', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( ! wp_verify_nonce( $data['_wpnonce'], 'affwp_delete_referral_nonce' ) ) {
		wp_die( __( 'Security check failed', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( affwp_delete_referral( $data['referral_id'] ) ) {
		wp_safe_redirect( affwp_admin_url( 'referrals', array( 'affwp_notice' => 'referral_deleted' ) ) );
		exit;
	} else {
		wp_safe_redirect( affwp_admin_url( 'referrals', array( 'affwp_notice' => 'referral_delete_failed' ) ) );
		exit;
	}

}
add_action( 'affwp_process_delete_referral', 'affwp_process_delete_referral' );

/**
 * Process the delete payout request
 *
 * @since 2.1.12
 * @return void
 */
function affwp_process_delete_payout( $data ) {

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_payouts' ) ) {
		wp_die( __( 'You do not have permission to manage payouts', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( ! wp_verify_nonce( $data['_wpnonce'], 'affwp_delete_payout_nonce' ) ) {
		wp_die( __( 'Security check failed', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( affwp_delete_payout( $data['payout_id'] ) ) {
		wp_safe_redirect( affwp_admin_url( 'payouts', array( 'affwp_notice' => 'payout_deleted' ) ) );
		exit;
	} else {
		wp_safe_redirect( affwp_admin_url( 'payouts', array( 'affwp_notice' => 'payout_delete_failed' ) ) );
		exit;
	}

}
add_action( 'affwp_process_delete_payout', 'affwp_process_delete_payout' );

/**
 * Process the referral payout file generation
 *
 * @since 1.0
 * @return void
 */
function affwp_generate_referral_payout_file( $data ) {

	$export = new Affiliate_WP_Referral_Payout_Export;

	if ( ! empty( $data['user_name'] ) && $affiliate = affwp_get_affiliate( $data['user_name'] ) ) {
		$export->affiliate_id = $affiliate->ID;
	}

	$export->date = array(
		'start' => $data['from'],
		'end'   => $data['to'] . ' 23:59:59'
	);
	$export->export();

}
add_action( 'affwp_generate_referral_payout', 'affwp_generate_referral_payout_file' );