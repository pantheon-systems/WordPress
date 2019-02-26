<?php

/**
 * Removes single-use query args derived from executed actions in the admin.
 *
 * @since  1.1
 *
 * @param  array $query_args Removable query arguments.
 * @return array Filtered list of removable query arguments.
 */
function affwp_dlt_remove_query_args( $query_args ) {

	// Prevent certain repeated Direct Link Tracking actions on refresh.
	if ( isset( $_GET['_wpnonce'] ) && isset( $_GET['url_id'] ) ) {
		$query_args[] = '_wpnonce';
	}

	return $query_args;
}
add_filter( 'removable_query_args', 'affwp_dlt_remove_query_args' );

/**
 * Process the add direct link request
 *
 * @since 1.1
 * @return void
 */
function affwp_process_add_direct_link( $data ) {

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_affiliates' ) ) {
		wp_die( __( 'You do not have permission to manage direct links', 'affiliatewp-direct-link-tracking' ), __( 'Error', 'affiliatewp-direct-link-tracking' ), array( 'response' => 403 ) );
	}

	if ( ! wp_verify_nonce( $data['affwp_add_direct_link_nonce'], 'affwp_add_direct_link_nonce' ) ) {
		wp_die( __( 'Security check failed', 'affiliatewp-direct-link-tracking' ), array( 'response' => 403 ) );
	}

    // Bail immediately if there is no user and URL.
    if ( ! ( $data['user_name'] && $data['url'] ) ) {
        return false;
    }

	$data = affiliate_wp()->utils->process_request_data( $data, 'user_name' );

	if ( empty( $data['user_id'] ) ) {
		return false;
	}

	// Get affiliate ID from the user ID.
	$affiliate_id = affwp_get_affiliate_id( absint( $data['user_id'] ) );

	// Response from validating the URL.
	$response = affwp_dlt_validate_url( $data['url'] );

	// URL failed validation.
	if ( is_array( $response ) && isset( $response['valid'] ) && false === $response['valid'] ) {

		// Generic notice ID.
		$notice_id = 'direct_link_add_failed';

		// Specific notice ID.
		if ( ! empty( $response['notice_id'] ) ) {
			$notice_id = $response['notice_id'];
		}

		wp_safe_redirect( admin_url( 'admin.php?page=affiliate-wp-direct-links&affwp_notice=' . $notice_id ) );
		exit;

	}

	// Validation passed, add the direct link.
	if ( affwp_dlt_add_direct_link( array( 'affiliate_id' => $affiliate_id, 'status' => 'active', 'url' => $response ) ) ) {

		// URL passed validation, add direct link.
		wp_safe_redirect( admin_url( 'admin.php?page=affiliate-wp-direct-links&affwp_notice=direct_link_added' ) );
		exit;

	}

}
add_action( 'affwp_add_direct_link', 'affwp_process_add_direct_link' );

/**
 * Process the update direct link request
 *
 * @since  1.1
 * @param  $data Array of data
 *
 * @return void
 */
function affwp_process_update_direct_link( $data ) {

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_affiliates' ) ) {
		wp_die( __( 'You do not have permission to manage direct links', 'affiliatewp-direct-link-tracking' ), __( 'Error', 'affiliatewp-direct-link-tracking' ), array( 'response' => 403 ) );
	}

	if ( ! wp_verify_nonce( $data['affwp_edit_direct_link_nonce'], 'affwp_edit_direct_link_nonce' ) ) {
		wp_die( __( 'Security check failed', 'affiliatewp-direct-link-tracking' ), array( 'response' => 403 ) );
	}

	if ( empty( $data['url_id'] ) ) {
		return false;
	}

	/**
	 * If the URL is different than the URL stored already it requires re-validation.
	 */
	if ( $data['url'] !== affwp_dlt_get_url( $data['url_id'] ) ) {

		// Response from validating the URL.
		$response = affwp_dlt_validate_url( $data['url'] );

		// URL failed validation.
		if ( is_array( $response ) && isset( $response['valid'] ) && false === $response['valid'] ) {

			// Generic notice ID.
			$notice_id = 'direct_link_update_failed';

			// Specific notice ID.
			if ( ! empty( $response['notice_id'] ) ) {
				$notice_id = $response['notice_id'];
			}

			wp_safe_redirect( admin_url( 'admin.php?page=affiliate-wp-direct-links&affwp_notice=' . $notice_id ) );
			exit;

		}

		// Set the URL to be the validated URL
		$data['url'] = $response;

	}

	/**
	 * Update the direct link with the (possibly new) data.
	 * Direct link will only be updated if the domain passes validation.
	 */
	if ( affwp_dlt_update_direct_link( $data['url_id'], $data ) ) {
		wp_safe_redirect( admin_url( 'admin.php?page=affiliate-wp-direct-links&affwp_notice=direct_link_updated' ) );
		exit;
	}

}
add_action( 'affwp_update_direct_link', 'affwp_process_update_direct_link' );

/**
 * Updates the direct links from the front-end Affiliate Area
 * Fires on the update_direct_links affwp_action from dashboard-tab-direct-links.php
 *
 * @since 1.1
 *
 * @return bool
 */
function affwp_update_direct_links( $data = array() ) {

	if ( ! is_user_logged_in() ) {
		return false;
	}

	if ( empty( $data['affiliate_id'] ) ) {
		return false;
	}

	if ( affwp_get_affiliate_id() != $data['affiliate_id'] && ! current_user_can( 'manage_affiliates' ) ) {
		return false;
	}

	/**
	 * Fires immediately after an affiliate's direct links have been updated.
	 *
	 * @since 1.1
	 *
	 * @param array $data Affiliate direct link data.
	 */
	do_action( 'affwp_direct_link_tracking_update_direct_links', $data );

}
add_action( 'affwp_update_direct_links', 'affwp_update_direct_links' );


/**
 * Update direct links.
 * Handles adding, updating and deleting domains from the frontend
 *
 * @since 1.1
 *
 * @return bool true if updated, bool false if errors.
 */
function affwp_dlt_update_direct_links( $data ) {

	// The affiliate ID
	$affiliate_id = $data['affiliate_id'];

	$errors = false;

	// Get affiliate's direct links from the database
	$direct_links = affwp_dlt_get_direct_links( array( 'affiliate_id' => $affiliate_id ) );

	// Return if affiliate isn't allowed to enter direct link tracking URLs
	if ( ! affwp_dlt_allow_direct_link_tracking( $affiliate_id ) ) {
		return;
	}

	/**
	 * Build out an array of saved domains
	 *
	 * For example: url_id => domain
	 *
	 * 4  => domain.com
	 * 10 => domain2.com
	 */
	$saved_domains = array();

	if ( $direct_links ) {
		foreach ( $direct_links as $direct_link ) {
			$saved_domains[$direct_link->url_id] = $direct_link->url;
		}
	}

	/**
	 * Update a domain
	 */

	// An array containing the posted URLs from the affiliate
	$posted_domains = isset( $data['direct_link_tracking_urls'] ) ? $data['direct_link_tracking_urls'] : array();

	if ( ! empty( $posted_domains ) ) {

		foreach ( $posted_domains as $url_id => $posted_domain ) {

			/**
			 * Delete an existing domain.
			 */
			if ( empty( $posted_domain ) ) {

				// Domain must already exist in the current affiliate's array of domains ($saved_domains) before it can be deleted.
				if ( array_key_exists( $url_id, $saved_domains ) ) {
					affwp_dlt_delete_direct_link( $url_id );
				}

			}

			/**
			 * Update an existing domain
			 * The URL ID must exist in the affiliate's saved domains
			 */
			if ( array_key_exists( $url_id, $saved_domains ) ) {

				// The domain doesn't match the domain in the database with the same URL ID so the domain has been changed (and isn't empty).
				if ( $posted_domain !== affwp_dlt_get_url( $url_id ) && ! empty( $posted_domain ) ) {

					// Validate the domain.
					$validated_url = affwp_dlt_validate_url( $posted_domain );

					if ( ! is_array( $validated_url ) ) {

						// Get the current domain from the database.
						$current_domain = affwp_dlt_get_url( $url_id );

						// Set the args.
						$args = array(
							'url'     => $validated_url,
							'url_old' => $current_domain,
							'status'  => 'pending'
						);

						// Update the domain.
						affwp_dlt_update_direct_link( $url_id, $args );

					} else {
						// There were errors.
					 	$errors = true;
					}

				}

			}

		}

	}

	/**
	 * Submit new domain.
	 * Domains are set to "pending" for first-time submissions.
	 */

	// New domains that will be added.
	$new_domains = isset( $data['direct_link_tracking_urls_new'] ) ? $data['direct_link_tracking_urls_new'] : array();

	// Remove any blank values.
	$new_domains = array_filter( $new_domains );

	if ( ! empty( $new_domains ) ) {

		foreach ( $new_domains as $new_domain ) {

			// Validate the domain.
 			$validated_url = affwp_dlt_validate_url( $new_domain );

			// affwp_dlt_validate_url() returns an array on failure, string if the domain is valid.
			if ( ! is_array( $validated_url ) ) {
				affwp_dlt_add_direct_link( array( 'url' => $validated_url, 'status' => 'pending' ) );
			} else {
				// There were errors.
				$errors = true;
			}

		}

	}

	// There were errors.
	if ( $errors ) {
		return false;
	}

	// No errors.
	return true;

}
