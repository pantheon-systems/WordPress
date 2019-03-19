<?php
/**
 * Email actions
 *
 * @package AffiliateWP\Emails\Actions
 * @since 1.6
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Sends an admin email on affiliate registration
 *
 * @since 1.6
 * @param int $affiliate_id The ID of the registered affiliate
 * @param string $status
 * @param array $args
 * @return void
 */
function affwp_notify_on_registration( $affiliate_id = 0, $status = '', $args = array() ) {

	if ( ! affwp_email_notification_enabled( 'admin_affiliate_registration_email' ) ) {
		return;
	}

	if( empty( $affiliate_id ) || empty( $status ) ) {
		return;
	}

	$emails           = new Affiliate_WP_Emails;
	$emails->__set( 'affiliate_id', $affiliate_id );

	$email            = apply_filters( 'affwp_registration_admin_email', get_option( 'admin_email' ) );
	$user_info        = get_userdata( affwp_get_affiliate_user_id( $affiliate_id ) );
	$user_url         = $user_info->user_url;
	$promotion_method = get_user_meta( affwp_get_affiliate_user_id( $affiliate_id ), 'affwp_promotion_method', true );

	$subject          = affiliate_wp()->settings->get( 'registration_subject', __( 'New Affiliate Registration', 'affiliate-wp' ) );
	$message          = affiliate_wp()->settings->get( 'registration_email', '' );

	if( empty( $message ) ) {

		$message  = __( 'A new affiliate has registered on your site, ', 'affiliate-wp' ) . home_url() . "\n\n";
		$message .= sprintf( __( 'Name: %s', 'affiliate-wp' ), $args['display_name'] ) . "\n\n";

		if( $user_url ) {
			$message .= sprintf( __( 'Website URL: %s', 'affiliate-wp' ), esc_url( $user_url ) ) . "\n\n";
		}

		if( $promotion_method ) {
			$message .= sprintf( __( 'Promotion method: %s', 'affiliate-wp' ), esc_attr( $promotion_method ) ) . "\n\n";
		}

		if( affiliate_wp()->settings->get( 'require_approval' ) ) {
			$message .= sprintf( __( 'Review pending applications: %s', 'affiliate-wp' ), affwp_admin_url( 'affiliates', array( 'status' => 'pending' ) ) ) . "\n\n";
		}

	}

	// $args is setup for backwards compatibility with < 1.6
	$args    = array( 'affiliate_id' => $affiliate_id, 'name' => $args['display_name'] );
	$subject = apply_filters( 'affwp_registration_subject', $subject, $args );
	$message = apply_filters( 'affwp_registration_email', $message, $args );

	$emails->send( $email, $subject, $message );

}
add_action( 'affwp_register_user', 'affwp_notify_on_registration', 10, 3 );
add_action( 'affwp_auto_register_user', 'affwp_notify_on_registration', 10, 3 );


/**
 * Sends affiliate an email on affiliate approval
 *
 * @since 1.6
 * @param int $affiliate_id The ID of the registered affiliate
 * @param string $status
 * @param string $old_status
 */
function affwp_notify_on_approval( $affiliate_id = 0, $status = '', $old_status = '' ) {

	if ( ! affwp_email_notification_enabled( 'affiliate_application_accepted_email' ) ) {
		return;
	}

	if( empty( $affiliate_id ) || 'active' !== $status ) {
		return;
	}

	/*
	 * Skip sending the acceptance email for a now-'active' affiliate under
	 * certain conditions:
	 *
	 * 1. The affiliate was previously of 'inactive' or 'rejected' status.
	 * 2. The affiliate was previously of 'pending' status, where the status
	 *    transition wasn't triggered by a registration.
	 * 3. The affiliate's 'active' status didn't change, and the status
	 *    "transition" wasn't triggered by a registration, i.e. the affiliate
	 *    was updated in a bulk action and the 'active' status didn't change.
	 */
	if ( ! in_array( $old_status, array( 'active', 'pending' ), true )
		&& ! did_action( 'affwp_affiliate_register' )
	) {
		return;
	}

	if( doing_action( 'affwp_add_affiliate' ) && empty( $_POST['welcome_email'] ) ) {
		return;
	}

	$emails       = new Affiliate_WP_Emails;
	$emails->__set( 'affiliate_id', $affiliate_id );

	$email        = affwp_get_affiliate_email( $affiliate_id );
	$subject      = affiliate_wp()->settings->get( 'accepted_subject', __( 'Affiliate Application Accepted', 'affiliate-wp' ) );
	$message      = affiliate_wp()->settings->get( 'accepted_email', '' );

	if( empty( $message ) ) {
		$message  = sprintf( __( 'Congratulations %s!', 'affiliate-wp' ), affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id ) ) . "\n\n";
		$message .= sprintf( __( 'Your affiliate application on %s has been accepted!', 'affiliate-wp' ), home_url() ) . "\n\n";
		$message .= sprintf( __( 'Log into your affiliate area at %s', 'affiliate-wp' ), affiliate_wp()->login->get_login_url() ) . "\n\n";
	}

	// $args is setup for backwards compatibility with < 1.6
	$args        = array( 'affiliate_id' => $affiliate_id );
	$subject     = apply_filters( 'affwp_application_accepted_subject', $subject, $args );
	$message     = apply_filters( 'affwp_application_accepted_email', $message, $args );
	$user_id     = affwp_get_affiliate_user_id( $affiliate_id );

	if ( doing_action( 'affwp_add_affiliate' ) && ! empty( $_POST['user_email'] ) ) {

		$key        = get_password_reset_key( get_user_by( 'id', $user_id ) );
		$user_login = affwp_get_affiliate_username( $affiliate_id );

		if ( ! is_wp_error( $key ) ) {
			$message .= "\r\n\r\n" . __( 'To set your password, visit the following address:', 'affiliate-wp' ) . "\r\n\r\n";
			$message .= network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n";
		}

	}

	if ( affiliate_wp()->settings->get( 'allow_affiliate_registration' ) && doing_action( 'affwp_affiliate_register' ) ) {

		$key                          = get_password_reset_key( get_user_by( 'id', $user_id ) );
		$user_login                   = affwp_get_affiliate_username( $affiliate_id );
		$required_registration_fields = affiliate_wp()->settings->get( 'required_registration_fields' );

		if ( ! is_wp_error( $key ) && ! isset( $required_registration_fields['password'] ) ) {
			$message .= "\r\n\r\n" . __( 'To set your password, visit the following address:', 'affiliate-wp' ) . "\r\n\r\n";
			$message .= network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n";
		}

	}

	/**
	 * Filters whether to notify an affiliate upon approval of their application.
	 *
	 * @since 1.6
	 *
	 * @param bool $notify Whether to notify the affiliate upon approval. Default true.
	 */
	if ( apply_filters( 'affwp_notify_on_approval', true ) && ! get_user_meta( $user_id, 'affwp_disable_affiliate_email', true ) ) {
		$emails->send( $email, $subject, $message );
	}

}
add_action( 'affwp_set_affiliate_status', 'affwp_notify_on_approval', 10, 3 );

/**
 * Sends affiliate an email on pending affiliate registration
 *
 * @since 1.6.1
 * @param int $affiliate_id The ID of the registered affiliate
 * @param string $status
 * @param array $args
 */
function affwp_notify_on_pending_affiliate_registration( $affiliate_id = 0, $status = '', $args ) {

	if ( ! affwp_email_notification_enabled( 'affiliate_application_pending_email' ) ) {
		return;
	}

	if ( empty( $affiliate_id ) ) {
		return;
	}

	if ( 'pending' != $status ) {
		return;
	}

	$emails       = new Affiliate_WP_Emails;
	$emails->__set( 'affiliate_id', $affiliate_id );

	$email        = affwp_get_affiliate_email( $affiliate_id );
	$subject      = affiliate_wp()->settings->get( 'pending_subject', __( 'Your Affiliate Application Is Being Reviewed', 'affiliate-wp' ) );
	$message      = affiliate_wp()->settings->get( 'pending_email', '' );

	if ( empty( $message ) ) {
		$message  = sprintf( __( 'Hi %s!', 'affiliate-wp' ), affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id ) ) . "\n\n";
		$message .= __( 'Thanks for your recent affiliate registration on {site_name}.', 'affiliate-wp' ) . "\n\n";
		$message .= __( 'We&#8217;re currently reviewing your affiliate application and will be in touch soon!', 'affiliate-wp' ) . "\n\n";
	}

	$required_registration_fields = affiliate_wp()->settings->get( 'required_registration_fields' );

	$user_id     = affwp_get_affiliate_user_id( $affiliate_id );
	$key         = get_password_reset_key( get_user_by( 'id', $user_id ) );
	$user_login  = affwp_get_affiliate_username( $affiliate_id );

	if ( ! is_wp_error( $key ) && ! isset( $required_registration_fields['password'] ) ) {
		$message .= "\r\n\r\n" . __( 'To set your password, visit the following address:', 'affiliate-wp' ) . "\r\n\r\n";
		$message .= network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n";
	}

	if ( apply_filters( 'affwp_notify_on_pending_affiliate_registration', true ) ) {
		$emails->send( $email, $subject, $message );
	}

}
add_action( 'affwp_register_user', 'affwp_notify_on_pending_affiliate_registration', 10, 3 );
add_action( 'affwp_auto_register_user', 'affwp_notify_on_pending_affiliate_registration', 10, 3 );

/**
 * Sends affiliate an email on rejected affiliate registration
 *
 * @since 1.6.1
 * @param int $affiliate_id The ID of the registered affiliate
 * @param string $status
 * @param string $old_status
 */
function affwp_notify_on_rejected_affiliate_registration( $affiliate_id = 0, $status = '', $old_status = '' ) {

	if ( ! affwp_email_notification_enabled( 'affiliate_application_rejected_email' ) ) {
		return;
	}

	if ( empty( $affiliate_id ) ) {
		return;
	}

	if ( 'rejected' != $status || 'pending' != $old_status ) {
		return;
	}

	$emails       = new Affiliate_WP_Emails;
	$emails->__set( 'affiliate_id', $affiliate_id );

	$email        = affwp_get_affiliate_email( $affiliate_id );
	$subject      = affiliate_wp()->settings->get( 'rejection_subject', __( 'Your Affiliate Application Has Been Rejected', 'affiliate-wp' ) );
	$message      = affiliate_wp()->settings->get( 'rejection_email', '' );

	if ( empty( $message ) ) {
		$message  = sprintf( __( 'Hi %s,', 'affiliate-wp' ), affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id ) ) . "\n\n";
		$message .= __( 'We regret to inform you that your recent affiliate registration on {site_name} was rejected.', 'affiliate-wp' ) . "\n\n";
	}

	if ( apply_filters( 'affwp_notify_on_rejected_affiliate_registration', true ) ) {
		$emails->send( $email, $subject, $message );
	}

}
add_action( 'affwp_set_affiliate_status', 'affwp_notify_on_rejected_affiliate_registration', 10, 3 );

/**
 * Sends affiliate an email on new referrals
 *
 * @since 1.6
 * @param int $affiliate_id The ID of the registered affiliate
 * @param array $referral
 */
function affwp_notify_on_new_referral( $affiliate_id = 0, $referral ) {

	if ( ! affwp_email_notification_enabled( 'affiliate_new_referral_email', $affiliate_id ) ) {
		return;
	}

	$user_id = affwp_get_affiliate_user_id( $affiliate_id );

	if( ! get_user_meta( $user_id, 'affwp_referral_notifications', true ) ) {
		return;
	}

	if( empty( $affiliate_id ) ) {
		return;
	}

	if( empty( $referral ) ) {
		return;
	}

	$emails  = new Affiliate_WP_Emails;
	$emails->__set( 'affiliate_id', $affiliate_id );
	$emails->__set( 'referral', $referral );

	$email   = affwp_get_affiliate_email( $affiliate_id );
	$subject = affiliate_wp()->settings->get( 'referral_subject', __( 'Referral Awarded!', 'affiliate-wp' ) );
	$message = affiliate_wp()->settings->get( 'referral_email', false );
	$amount  = html_entity_decode( affwp_currency_filter( $referral->amount ), ENT_COMPAT, 'UTF-8' );

	if( ! $message ) {
		$message  = sprintf( __( 'Congratulations %s!', 'affiliate-wp' ), affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id ) ) . "\n\n";
		$message .= sprintf( __( 'You have been awarded a new referral of %s on %s!', 'affiliate-wp' ), $amount, home_url() ) . "\n\n";
		$message .= sprintf( __( 'log into your affiliate area to view your earnings or disable these notifications: %s', 'affiliate-wp' ), affiliate_wp()->login->get_login_url() ) . "\n\n";
	}

	// $args is setup for backwards compatibility with < 1.6
	$args    = array( 'affiliate_id' => $affiliate_id, 'amount' => $referral->amount, 'referral' => $referral );
	$subject = apply_filters( 'affwp_new_referral_subject', $subject, $args );
	$message = apply_filters( 'affwp_new_referral_email', $message, $args );

	if ( apply_filters( 'affwp_notify_on_new_referral', true, $referral ) ) {
		$emails->send( $email, $subject, $message );
	}


}
add_action( 'affwp_referral_accepted', 'affwp_notify_on_new_referral', 10, 2 );

/**
 * Sends an email to admins on when a new referral is generated.
 *
 * @since 2.1.7
 *
 * @param int             $affiliate_id The ID of the registered affiliate
 * @param \AffWP\Referral $referral     Referral object.
 */
function affwp_notify_admin_on_new_referral( $affiliate_id = 0, $referral ) {

	if( empty( $affiliate_id ) ) {
		return;
	}

	if( empty( $referral ) ) {
		return;
	}

	$send = affwp_email_notification_enabled( 'admin_new_referral_email', $affiliate_id );

	/**
	 * Filters whether to notify admins when a new referral is generated.
	 *
	 * @since 2.1.7
	 *
	 * @param bool            $send     Whether to send the email. Default false.
	 * @param \AffWP\Referral $referral Referral object.
	 */
	if( true !== apply_filters( 'affwp_notify_admin_on_new_referral', $send, $referral ) ) {
		return;
	}

	$emails  = new Affiliate_WP_Emails;
	$emails->__set( 'affiliate_id', $affiliate_id );
	$emails->__set( 'referral', $referral );

	$subject = affiliate_wp()->settings->get( 'new_admin_referral_subject', __( 'Referral Earned!', 'affiliate-wp' ) );
	$message = affiliate_wp()->settings->get( 'new_admin_referral_email', false );

	if( ! $message ) {
		$message = '{name} has been awarded a new referral of {amount} on {site_name}.';
	}

	/**
	 * Filters the subject field for the email sent to admins when a new referral is generated.
	 *
	 * @since 2.1.7
	 *
	 * @param string          $subject      Email subject.
	 * @param int             $affiliate_id Affiliate ID.
	 * @param \AffWP\Referral $referral     Referral object.
	 */
	$subject = apply_filters( 'affwp_new_admin_referral_subject', $subject, $affiliate_id, $referral );

	/**
	 * Filters the message body for the email sent to admins when a new referral is generated.
	 *
	 * @since 2.1.7
	 *
	 * @param string          $message      Email message body.
	 * @param int             $affiliate_id Affiliate ID.
	 * @param \AffWP\Referral $referral     Referral object.
	 */
	$message = apply_filters( 'affwp_new_admin_referral_email', $message, $affiliate_id, $referral );

	/**
	 * Filters the recipient email address for the email sent to admins when a new referral is generated.
	 *
	 * @since 2.1.7
	 *
	 * @param string          $email        Recipient email. Default is the value of the 'admin_email' option.
	 * @param int             $affiliate_id Affiliate ID.
	 * @param \AffWP\Referral $referral     Referral object.
	 */
	$to_email = apply_filters( 'affwp_new_admin_referral_email_to', get_option( 'admin_email' ), $affiliate_id, $referral );

	$emails->send( $to_email, $subject, $message );

}
add_action( 'affwp_referral_accepted', 'affwp_notify_admin_on_new_referral', 10, 2 );
