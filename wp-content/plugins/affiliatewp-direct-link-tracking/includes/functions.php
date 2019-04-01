<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * This determines if a visit can be stored or not, based on the current referrer
 * Such factors may prevent the visit from being stored such as:
 *
 * The affiliate cannot be found.
 * Direct Link Tracking is not enabled for the affiliate.
 * The referring URL being blacklisted.
 * The base domain of the referring URL is blacklisted.
 * The base domain of the referrer URL belongs to another affiliate.
 *
 * @since 1.1
 *
 * @see  track_visit() in class-tracking.php
 * @see  fallback_track_visit() in class-tracking.php
 * @uses affwp_dlt_get_affiliate_id()
 *
 * @return boolean true, false otherwise
 */
function affwp_dlt_can_store_visit() {

    // Get the tracked affiliate ID.
    $affiliate_id = affwp_dlt_get_affiliate_id();

    // Bail if an affiliate can't be found.
    if ( empty( $affiliate_id ) ) {
    	return false;
    }

    // Bail if DLT is not enabled for the affiliate.
    if ( ! affwp_dlt_allow_direct_link_tracking( $affiliate_id ) ) {
        return false;
    }

    // Bail if domain is blacklisted.
    if ( in_array( affwp_dlt_get_referrer_base_domain(), affwp_dlt_blacklisted_domains() ) ) {
        return false;
    }

    // Get the referrer.
    $referrer = affwp_dlt_get_referrer();

    // Get the protocol relative URL.
    $protocol_relative_url = affwp_dlt_base_domain( $referrer );

    // Bail if the base URL of the referring URL is blacklisted.
    if (
        in_array( untrailingslashit( $protocol_relative_url ), affwp_dlt_blacklisted_domains() ) ||
        in_array( trailingslashit( $protocol_relative_url ), affwp_dlt_blacklisted_domains() ) )
    {
        return false;
    }

    /**
     * Bail if the base URL of the referring URL belongs to another affiliate
     */
    $base_url_affiliate_id = affwp_dlt_get_affiliate_id_from_domain( $protocol_relative_url );

    if ( $base_url_affiliate_id && (int) $base_url_affiliate_id !== $affiliate_id ) {
        return false;
    }

    // All set, a visit can be stored
    return true;

}

/**
 * Get the affiliate ID from the referrer
 *
 * @since  1.0.0
 * @uses   affwp_dlt_get_affiliate_id_from_referrer()
 *
 * @return int $affiliate_id The ID of the affiliate that is linked to the referring URL, boolean false otherwise
 */
function affwp_dlt_get_affiliate_id() {

	// Get the HTTP_REFERER.
	$referrer = affwp_dlt_get_referrer();

	// Try retrieving affiliate ID from referrer.
	if ( affwp_dlt_get_affiliate_id_from_referrer( $referrer ) ) {
 		$affiliate_id = affwp_dlt_get_affiliate_id_from_referrer( $referrer );
 	} else {
 		// Try with trailing slash.
 		$affiliate_id = affwp_dlt_get_affiliate_id_from_referrer( trailingslashit( $referrer ) );
 	}

    if ( $affiliate_id ) {
        return $affiliate_id;
    }

    return false;

}

/**
 * Get the affiliate ID based on the HTTP_REFERER
 *
 * @since  1.0.0
 * @param  string $url The referrer to search for. This URL may or may not include a domain path.
 *
 * @return int $affiliate_id The ID of the affiliate that is stored against the domain, boolean false otherwise
 */
function affwp_dlt_get_affiliate_id_from_referrer( $url = '' ) {

	/**
	 * First we determine if the URL has a path.
	 *
	 * If the URL contains a dommain with a path then we need to check if the base domain is assigned to an affiliate, and if so, return the affiliate ID immediately.
	 * This is because any affiliate who has the base domain assigned to them as a direct link will always be tracked.
	 */

	// Check if the URL contains a domain with a path.
	if ( affwp_dlt_url_path( $url ) ) {

		// Get the base domain of the URL.
		// Example: https://site.com/some-page would have a base domain of site.com
		$base_domain = affwp_dlt_base_domain( $url );

		// Try and retrieve the affiliate ID from the old domain.
		// Old domains are domains which are pending approval because the affiliate has changed it.
		$affiliate_id_from_old_base_domain = affwp_dlt_get_affiliate_id_from_domain( $base_domain, 'url_old' );

		if ( $affiliate_id_from_old_base_domain ) {

			// If old domain has "pending" status, return the affiliate ID.
			if ( 'pending' === affwp_dlt_get_domain_status( $base_domain, 'url_old' ) ) {
				return $affiliate_id_from_old_base_domain;
			}

		}

		// Try and retrieve the affiliate ID from the domain.
		$affiliate_id_from_base_domain = affwp_dlt_get_affiliate_id_from_domain( $base_domain );

		if ( $affiliate_id_from_base_domain ) {

			// If domain has "active" status, return the affiliate ID.
			if ( 'active' === affwp_dlt_get_domain_status( $base_domain ) ) {
				return $affiliate_id_from_base_domain;
			}

		}

		// If we got this far, no base domain is assigned to any affiliate and the domain with path can now be looked up.

		// Get the base domain of the URL with path.
		$base_domain_with_path = affwp_dlt_base_domain( $url, true );

		// Try and retrieve the affiliate ID from the old domain with path.
		$affiliate_id_from_old_base_domain_with_path = affwp_dlt_get_affiliate_id_from_domain( $base_domain_with_path, 'url_old' );

		if ( $affiliate_id_from_old_base_domain_with_path ) {

			// If old domain has "pending" status, return the affiliate ID.
			if ( 'pending' === affwp_dlt_get_domain_status( $base_domain_with_path, 'url_old' ) ) {
				return $affiliate_id_from_old_base_domain_with_path;
			}

		}

		// Try and retrieve the affiliate ID from the domain with path.
		$affiliate_id_from_base_domain_with_path = affwp_dlt_get_affiliate_id_from_domain( $base_domain_with_path );

		if ( $affiliate_id_from_base_domain_with_path ) {

			// If domain has "active" status, return the affiliate ID.
			if ( 'active' === affwp_dlt_get_domain_status( $base_domain_with_path ) ) {
				return $affiliate_id_from_base_domain_with_path;
			}

		}

	} else {
		/**
		 * The domain does not have a path
		 */

		// Get the base domain of the URL.
		$base_domain = affwp_dlt_base_domain( $url );

		// Try and retrieve the affiliate ID from the old domain.
		$affiliate_id_from_old_base_domain = affwp_dlt_get_affiliate_id_from_domain( $base_domain, 'url_old' );

		if ( $affiliate_id_from_old_base_domain ) {

			// If old domain has "pending" status, return the affiliate ID.
			if ( 'pending' === affwp_dlt_get_domain_status( $base_domain, 'url_old' ) ) {
				return $affiliate_id_from_old_base_domain;
			}

		}

		// Try and retrieve the affiliate ID from the domain.
		$affiliate_id_from_base_domain = affwp_dlt_get_affiliate_id_from_domain( $base_domain );

		if ( $affiliate_id_from_base_domain ) {

			// If domain has "active" status, return the affiliate ID.
			if ( 'active' === affwp_dlt_get_domain_status( $base_domain ) ) {
				return $affiliate_id_from_base_domain;
			}

		}

	}

    return false;

}

/**
 * Are affiliates allowed to use direct link tracking?
 *
 * @since  1.0.0
 *
 * @return boolean true is Direct Link Tracking is enabled, false otherwise
 */
function affwp_dlt_allow_direct_link_tracking( $affiliate_id = 0 ) {

    $all_affiliates   = affiliate_wp()->settings->get( 'direct_link_tracking' );
    $single_affiliate = affwp_get_affiliate_meta( $affiliate_id, 'direct_link_tracking_enabled', true );

    if ( $all_affiliates || $single_affiliate ) {
        return true;
    }

    return false;

}
