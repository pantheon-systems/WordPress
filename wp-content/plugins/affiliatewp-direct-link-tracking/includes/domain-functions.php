<?php
/**
 * Domain functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the referrer's base domain
 *
 * @since  1.1
 * @uses   affwp_dlt_base_domain()
 * @uses   affwp_dlt_get_referrer()
 *
 * @return string $url referrer base domain
 */
function affwp_dlt_get_referrer_base_domain() {
	// Return the domain, including any domain paths.
	return affwp_dlt_base_domain( affwp_dlt_get_referrer(), true );
}

/**
 * Gets the base domain (optional: domain path) from a URL.
 *
 * E.g. https://site.com would return site.com
 * E.g. https://site.com/path/name/ would return site.com (default)
 * E.g. https://site.com/path/name/ would return site.com/path/name/ with $keep_path enabled
 *
 * @since  1.1
 * @param  string  $url Full URL with protocol
 * @param  boolean $keep_path Whether or not to keep the domain path (if any)
 *
 * @return string  $domain The base domain of the URL
 */
function affwp_dlt_base_domain( $url = '', $keep_path = false ) {

	// No URL provided, return false.
	if ( empty( $url ) ) {
		return false;
	}

	// Parse the URL.
	$parsed_url = parse_url( $url );

	// URL doesn't have protocol (http, https etc), return false.
	if ( empty( $parsed_url['scheme'] ) ) {
		return false;
	}

	// Keep domain path.
	if ( true === $keep_path ) {

		// We need to keep the paths, just remove the schemes.
		if ( $path = affwp_dlt_url_path( $url ) ) {
			// URL has path
			$domain = $parsed_url['host'] . $path;

		} else {
			$domain = $parsed_url['host'];
		}

	} else {
		$domain = $parsed_url['host'];
	}

	// Remove www. if present in the domain.
	if ( substr( $domain, 0, 4 ) === 'www.' ) {
		$domain = substr( $domain, 4 );
	}

	// Return the base domain.
	return $domain;

}

/**
 * Returns a version of the domain prefixed with www.
 * This is primararily for backwards compatibility with versions less than v1.1
 *
 * @since  1.1
 * @param  string $domain The domain to prefix
 *
 * @return string $domain
 */
function affwp_dlt_domain_with_www( $domain = '' ) {

	if ( empty( $domain ) ) {
		return false;
	}

	return 'www.' . $domain;

}


/**
 * Get the status of a domain
 *
 * @since  1.1
 * @param  string $domain The domain to get the status for
 * @param  string $url_column The url column in the database to search. Either url (default) or url_old
 *
 * @return string $domain_status Status of the domain
 */
function affwp_dlt_get_domain_status( $domain = '', $url_column = 'url' ) {

	// A Domain is required.
	if ( empty( $domain ) ) {
		return false;
	}

	if ( $domain_status = affiliatewp_direct_link_tracking()->direct_links->get_column_by( 'status', $url_column, $domain ) ) {

		// Get the domain status.
		return $domain_status;

	} elseif ( $domain_status = affiliatewp_direct_link_tracking()->direct_links->get_column_by( 'status', $url_column, affwp_dlt_domain_with_www( $domain ) ) ) {
		// Try and get the domain with www for backwards compatibility.
		return $domain_status;

	}

	return false;

}


/**
 * Get affiliate ID from domain
 *
 * @since  1.1
 * @param  string $domain The domain used to find the affiliate ID
 * @param  string $url_column The url column in the database to search. Either url (default) or url_old
 *
 * @return int $affiliate_id Affiliate ID that belongs to the domain
 */
function affwp_dlt_get_affiliate_id_from_domain( $domain = '', $url_column = 'url' ) {

	if ( empty( $domain ) ) {
		return false;
	}

	if ( $affiliate_id = affiliatewp_direct_link_tracking()->direct_links->get_column_by( 'affiliate_id', $url_column, $domain ) ) {

		// Get the afffiliate ID from the domain.
		return (int) $affiliate_id;

	} elseif ( $affiliate_id = affiliatewp_direct_link_tracking()->direct_links->get_column_by( 'affiliate_id', $url_column, affwp_dlt_domain_with_www( $domain ) ) ) {

		// Try and get the affiliate Id from the domain with www for backwards compatibility.
		return (int) $affiliate_id;

	}

	return false;

}

/**
 * Retrieve all domains from the database, or for a specific affiliate.
 *
 * @since  1.1
 * @param  int $affiliate_id (optional) The affiliate ID to retrieve the domains for.
 *
 * @return array $domains All domains from database, empty array otherwise.
 */
function affwp_dlt_get_domains( $affiliate_id = 0 ) {

	// Create an empty array to hold the arguments.
	$args = array();

	// Filter direct links by affiliate ID
	if ( ! empty( $affiliate_id ) ) {
		$args['affiliate_id'] = $affiliate_id;
	}

	// Get all direct links
	$direct_links = affwp_dlt_get_direct_links( $args );

	if ( $direct_links ) {

		// Pluck the url column
		$domains = wp_list_pluck( $direct_links, 'url' );

		if ( $domains ) {
			return $domains;
		}

	}

	return array();

}

/**
 * Get domain limit (global or per-affiliate level)
 *
 * @param int $affiliate_id (optional) The affiliate ID to retrieve the domain limit for.
 *
 * @since 1.0.0
 */
function affwp_dlt_get_domain_limit( $affiliate_id = 0 ) {

	// Get the global limit.
	$limit = affiliate_wp()->settings->get( 'direct_link_tracking_url_limit' );

	// Get the limit for a specific affiliate
	if ( $affiliate_id ) {

		// Get the affiliate's domain limit.
		$affiliate_limit = affwp_get_affiliate_meta( $affiliate_id, 'direct_link_tracking_url_limit', true );

		if ( $affiliate_limit ) {
			$limit = $affiliate_limit;
		}

	}

	// Return the limit.
	return (int) $limit;

}

/**
 * Get domain for a direct link, given its url_id
 *
 * @since  1.1
 * @param  int $url_id The url_id of the direct link in the database table.
 *
 * @return string domain of the direct link
 */
function affwp_dlt_get_url( $url_id = 0 ) {

	if ( is_numeric( $url_id ) ) {
		$url_id = absint( $url_id );
	} else {
		return false;
	}

	return affiliatewp_direct_link_tracking()->direct_links->get_column( 'url', $url_id );
}

/**
 * Blacklisted domains
 *
 * @since  1.1
 *
 * @return array $urls The array of blacklisted URLs
 */
function affwp_dlt_blacklisted_domains() {

	// Retrieve the URLs.
	// These could also be domains (no protocols).
	$urls = affiliate_wp()->settings->get( 'direct_link_tracking_url_blacklist', array() );

	if ( ! empty( $urls ) ) {
		$urls = trim( $urls );
		$urls = explode( "\n", $urls );

		$urls = array_map( 'sanitize_text_field', $urls );
		$urls = array_map( 'esc_url_raw', $urls );
		$urls = array_map( 'trailingslashit', $urls );
	}

	// Blacklist these URLs by default
	$urls[] = home_url();
	$urls[] = site_url();

	foreach ( $urls as $key => $url ) {
		// strip the protocol, keep any paths
		$urls[$key] = affwp_dlt_base_domain( $url, true );
	}

	/**
	 * Filters the list of blacklisted URLs.
	 *
	 * @since 1.1
	 *
	 * @param array $urls blacklisted URLs.
	 */
	$urls = apply_filters( 'affwp_direct_link_tracking_blacklisted_urls', $urls );

	// Remove any duplicate domains.
	$urls = array_unique( $urls );

	return $urls;

}
