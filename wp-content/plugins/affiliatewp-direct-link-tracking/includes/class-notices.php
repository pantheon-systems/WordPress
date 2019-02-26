<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AffiliateWP Direct Link Tracking Admin Notices class
 *
 * @since 1.0
 */
class AffiliateWP_Direct_Link_Tracking_Admin_Notices {

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'admin_notices', array( $this, 'show_notices' ) );
		add_action( 'affwp_dismiss_notices', array( $this, 'dismiss_notices' ) );
	}

	/**
	 * Displays admin notices.
	 *
	 * @since 1.0
	 * @since 1.8.3 Notices are hidden for users lacking the 'manage_affiliates' capability
	 * @access public
	 */
	public function show_notices() {

		// Don't display notices for users who can't manage affiliates.
		if ( ! current_user_can( 'manage_affiliates' ) ) {
			return;
		}

		$class = 'updated';

		if ( isset( $_GET['affwp_notice'] ) && $_GET['affwp_notice'] ) {

			switch ( $_GET['affwp_notice'] ) {

				case 'direct_link_activated' :
					$message = __( 'Direct Link activated', 'affiliatewp-direct-link-tracking' );
					break;

				case 'direct_link_deactivated' :
					$message = __( 'Direct Link deactivated', 'affiliatewp-direct-link-tracking' );
					break;

				case 'direct_link_accepted' :
					$message = __( 'Direct Link accepted', 'affiliatewp-direct-link-tracking' );
					break;

				case 'direct_link_rejected' :
					$message = __( 'Direct Link rejected', 'affiliatewp-direct-link-tracking' );
					break;

				case 'direct_link_deleted' :
					$message = __( 'Direct Link deleted', 'affiliatewp-direct-link-tracking' );
					break;

				case 'direct_link_added' :
					$message = __( 'Direct Link added successfully', 'affiliatewp-direct-link-tracking' );
					break;

				case 'direct_link_add_failed' :
					$message = __( 'Direct Link wasn&#8217;t added, please try again', 'affiliatewp-direct-link-tracking' );
					$class   = 'error';
					break;

				case 'direct_link_updated' :
					$message = __( 'Direct Link updated', 'affiliatewp-direct-link-tracking' );
					break;

				case 'direct_link_update_failed' :
					$message = __( 'Direct Link wasn&#8217;t updated, please try again', 'affiliatewp-direct-link-tracking' );
					$class   = 'error';
					break;

				/**
				 * Validation error messages
				 */
				case 'direct_link_blacklisted' :
					$message = __( 'Direct Link wasn&#8217;t added. Reason: Direct Link is blacklisted.', 'affiliatewp-direct-link-tracking' );
					$class   = 'error';
					break;

				case 'direct_link_missing_domain_suffix' :
					$message = __( 'Direct Link wasn&#8217;t added. Reason: Direct Link did not have a domain suffix (.com, .org etc).', 'affiliatewp-direct-link-tracking' );
					$class   = 'error';
					break;

				case 'direct_link_added_base_domain_exists' :
					$message = __( 'Direct Link wasn&#8217;t added. Reason: Base domain already exists and is assigned to another affiliate.', 'affiliatewp-direct-link-tracking' );
					$class   = 'error';
					break;

				case 'direct_link_updated_base_domain_exists' :
					$message = __( 'Direct Link wasn&#8217;t updated. Reason: Base domain already exists and is assigned to another affiliate.', 'affiliatewp-direct-link-tracking' );
					$class   = 'error';
					break;

				case 'direct_link_domain_exists' :
					$message = __( 'Direct Link wasn&#8217;t added. Reason: Domain already exists.', 'affiliatewp-direct-link-tracking' );
					$class   = 'error';
					break;

				case 'direct_link_failed_validation' :
					$message = __( 'Direct Link wasn&#8217;t added. Reason: Domain was entered incorrectly.', 'affiliatewp-direct-link-tracking' );
					$class   = 'error';
					break;

				case 'direct_link_blacklisted_base_domain' :
					$message = __( 'Direct Link wasn&#8217;t added. Reason: Domain\'s base domain is blacklisted.', 'affiliatewp-direct-link-tracking' );
					$class   = 'error';
					break;

			}
		}

		if ( ! empty( $message ) ) {
			echo '<div class="' . esc_attr( $class ) . '"><p><strong>' .  $message  . '</strong></p></div>';
		}

	}

	/**
	 * Dismisses admin notices when Dismiss links are clicked.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function dismiss_notices() {
		if ( ! isset( $_GET['affwp_dismiss_notice_nonce'] ) || ! wp_verify_nonce( $_GET['affwp_dismiss_notice_nonce'], 'affwp_dismiss_notice') ) {
			wp_die( __( 'Security check failed', 'affiliatewp-direct-link-tracking' ), __( 'Error', 'affiliatewp-direct-link-tracking' ), array( 'response' => 403 ) );
		}

		if ( isset( $_GET['affwp_notice'] ) ) {

			$notice = sanitize_key( $_GET['affwp_notice'] );

			switch ( $notice ) {

				default:
					/**
					 * Fires once a notice has been flagged for dismissal.
					 *
					 * @since 1.0.0
					 *
					 * @param string $notice Notice value via $_GET['affwp_notice'].
					 */
					do_action( 'affwp_direct_link_tracking_dismiss_notices', $notice );
					break;
			}

			wp_redirect( remove_query_arg( array( 'affwp_action', 'affwp_notice' ) ) );
			exit;
		}
	}
}

new AffiliateWP_Direct_Link_Tracking_Admin_Notices;
