<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_Direct_Link_Tracking_Emails {

	public function __construct() {

		// Email admin when a direct link has been submitted by an affiliate on the front-end.
		add_action( 'affwp_direct_link_tracking_set_direct_link_status', array( $this, 'notify_admin' ), 10, 3 );

		// Email affiliate when direct link has been approved or rejected
		add_action( 'affwp_direct_link_tracking_set_direct_link_status', array( $this, 'notify_affiliate' ), 10, 3 );

	}

    /**
     * Send email to admin when a direct link has been submitted by an affiliate
	 * Email is sent when the affiliate has submitted a direct link for the first-time,
	 * Or when they had a previously approved direct link and updated it with a new domain.
	 *
	 * @since  1.1
	 * @param  int $url_id The ID of the direct link
	 * @param  string $status The new status of the direct link
 	 * @param  string $old_status The old status of the direct link
	 *
	 * @return void
     */
    public function notify_admin( $url_id, $status, $old_status ) {

		// Only send email if option to notify admin is enabled.
		if ( ! affiliate_wp()->settings->get( 'direct_link_tracking_notify_admin' ) ) {
			return;
		}

		// Only send email from front-end.
		if ( is_admin() ) {
			return;
		}

		// Call AffiliateWP's emails class.
		$emails = affiliate_wp()->emails;

		// Set the email.
		$email = apply_filters( 'affwp_direct_link_tracking_email_admin', get_option( 'admin_email' ) );

		// Get the direct link.
		$direct_link = affwp_dlt_get_direct_link( $url_id );

		// Get the domain.
		$domain = $direct_link->url;

		// Set the subject.
		$subject = apply_filters( 'affwp_direct_link_tracking_email_admin_subject', sprintf( __( 'A new direct link, %s, has been submitted for approval', 'affiliatewp-direct-link-tracking' ), $domain ) );

		// Set the message.
		$message = $this->admin_message( $url_id, $status, $old_status );

		// Send email.
		$emails->send( $email, $subject, $message );

    }

	/**
	 * The message for the admin notification email
	 *
	 * @since  1.1
	 *
	 * @param  string $domain The direct link domain
	 * @param  int $affiliate_id The affiliate's ID
	 *
	 * @return string $message The email message to be sent to the admin
	 */
	public function admin_message( $url_id, $status, $old_status ) {

		// Get the direct link.
		$direct_link = affwp_dlt_get_direct_link( $url_id );

		// Get the domain.
		$domain = $direct_link->url;

		// Get the affiliate ID.
		$affiliate_id = $direct_link->affiliate_id;

		// Get the affiliate's name from their affiliate ID.
		$affiliate_name = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		/**
		 * If the $url_id exists in the "direct_link_tracking_urls" array then the domain has been updated.
		 * Set $domain to be the new posted domain.
		 * Set $old_domain to be the previous one.
		 */
		if ( isset( $_POST['direct_link_tracking_urls'] ) && array_key_exists( $url_id, $_POST['direct_link_tracking_urls'] ) ) {
			$domain     = $_POST['direct_link_tracking_urls'][$url_id];
			$old_domain = $direct_link->url;
		}

		// Message.
		$message = __( 'A direct link has been submitted for approval.', 'affiliatewp-direct-link-tracking' );
		$message .= "\n\n";
		$message .= sprintf( __( 'Domain: %s', 'affiliatewp-direct-link-tracking' ), $domain );
		$message .= "\n";

		// Affiliate had a previously active direct link but has submitted a new one.
		if ( 'active' === $old_status && 'pending' === $status ) {
			$message .= sprintf( __( 'Old domain: %s', 'affiliatewp-direct-link-tracking' ), $old_domain );
			$message .= "\n";
		}

		$message .= sprintf( __( 'Affiliate: %s', 'affiliatewp-direct-link-tracking' ), $affiliate_name );
		$message .= "\n\n";
		$message .= "\n\n";
		$message .= __( 'View direct links:', 'affiliatewp-direct-link-tracking' );
		$message .= "\n";
		$message .= admin_url( 'admin.php?page=affiliate-wp-direct-links' );
		// End Message

		$message = apply_filters( 'affwp_direct_link_tracking_email_admin_message', $message, $url_id, $status, $old_status );

		// Return the message.
		return $message;
	}

	/**
	 * Send email to affiliate when a direct link has been approved
	 *
	 * @since 1.1
	 *
	 * @param int $url_id The ID of the direct link
	 * @param  string $status The new status of the direct link
 	 * @param  string $old_status The old status of the direct link
	 *
	 * @return void
	 */
	public function notify_affiliate( $url_id, $status, $old_status ) {

		$notify_affiliate = affiliate_wp()->settings->get( 'direct_link_tracking_notify_affiliate' );

		$approval_email   = isset( $notify_affiliate['approval_email'] ) ? true : false;
		$rejection_email  = isset( $notify_affiliate['rejection_email'] ) ? true : false;

		if (
			( $approval_email && 'pending' === $old_status && 'active' === $status ) ||
			( $rejection_email && 'pending' === $old_status && 'rejected' === $status )
		) {

			// Get the direct link based on the URL ID.
			$direct_link = affwp_dlt_get_direct_link( $url_id );

			// Get the domain.
			$domain = untrailingslashit( $direct_link->url );

			// Get the affiliate ID.
			$affiliate_id = $direct_link->affiliate_id;

			// Get the affiliate's email.
			$email = affwp_get_affiliate_email( $affiliate_id );

			// Call AffiliateWP's emails class.
			$emails = affiliate_wp()->emails;

			// Direct link was approved.
			if ( 'pending' === $old_status && 'active' === $status ) {
				// Set the subject.
				$subject = sprintf( __( 'Your direct link, %s, has been approved', 'affiliatewp-direct-link-tracking'), $domain );
			}

			// Direct link was rejected.
			if ( 'pending' === $old_status && 'rejected' === $status ) {
				// Set the subject.
				$subject = sprintf( __( 'Your direct link, %s, has not been approved', 'affiliatewp-direct-link-tracking' ), $domain );
			}

			$subject = apply_filters( 'affwp_direct_link_tracking_email_affiliate_subject', $subject, $domain, $status, $old_status );

			// Set the message
			$message = $this->affiliate_message( $domain, $affiliate_id, $status, $old_status );

			// Send email
			$emails->send( $email, $subject, $message );

		}
	}

	/**
	 * The message for the affiliate notification email
	 *
	 * @since  1.1
	 *
	 * @param  string $domain The direct link domain
	 * @param  int $affiliate_id The affiliate's ID
	 * @param  string $status The new status of the direct link
	 * @param  string $old_status The old status of the direct link
	 *
	 * @return string $message The email message to be sent to the affiliate
	 */
	public function affiliate_message( $domain, $affiliate_id, $status, $old_status ) {

		// Get the affiliate's name from their affiliate ID.
		$affiliate_name = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );

		// Message.
		$message = sprintf( __( 'Hi %s,', 'affiliatewp-direct-link-tracking' ), $affiliate_name );
		$message .= "\n\n";

		// Direct link approved.
		if ( 'pending' === $old_status && 'active' === $status ) {
			$message .= sprintf( __( 'Your direct link, %s, has been approved.', 'affiliatewp-direct-link-tracking' ), $domain );
		}

		// Direct link rejected.
		if ( 'pending' === $old_status && 'rejected' === $status ) {
			$message .= sprintf( __( 'Your direct link, %s, has not been approved.', 'affiliatewp-direct-link-tracking' ), $domain );
		}
		// End Message.

		$message = apply_filters( 'affwp_direct_link_tracking_email_affiliate_message', $message, $domain, $affiliate_id, $status, $old_status );

		// Return the content.
		return $message;

	}

}
