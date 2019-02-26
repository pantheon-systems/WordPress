<?php
/**
 * URL functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the HTTP referer
 *
 * @since 1.0.0
 *
 * @return string $referrer, false otherwise
 */
function affwp_dlt_get_referrer() {

    if ( function_exists( 'wp_get_raw_referer' ) ) {
        $referrer = wp_get_raw_referer();
    } elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
        $referrer = wp_unslash( $_SERVER['HTTP_REFERER'] );
    } else {
        $referrer = '';
    }

    if ( $referrer ) {
        return $referrer;
    }

    return false;

}

/**
 * Determines if the given URL has a path
 * Example: https://mysite.com/pagename would have a path of /pagename
 *
 * @since  1.1
 * @param  string $url Full URL (including domain protocol) to check for path
 *
 * @return string $path path, boolean false otherwise
 */
function affwp_dlt_url_path( $url = '' ) {

	if ( empty( $url ) ) {
		return false;
	}

	$path = parse_url( $url, PHP_URL_PATH );

	if ( $path && '/' !== $path ) {
		return $path;
	}

	return false;

}

/**
 * Validate a given URL
 *
 * @param string $url The URL to validate
 * @since 1.0
 */
function affwp_dlt_validate_url( $url = '' ) {

	// We need a URL to validate!
	if ( empty( $url ) ) {
		return array(
			'valid'  => false,
			'reason' => __( 'No domain specified.', 'affiliatewp-direct-link-tracking' )
		);
	}

	/**
	 * Trim the URL to remove any whitespace.
	 */
	$url = trim( $url );

	/**
	 * Cleans the URL and adds a protocol if one does not exist
	 * A protocol is required for further validation below
	 *
	 * @see https://codex.wordpress.org/Function_Reference/esc_url_raw
	 */
	$url = esc_url_raw( $url );

	/**
	 * Checks that the URL starts with http:// or https://
	 * A URL cannot for example be ftp:// or any other protocol
	 */
	if ( 0 !== strpos( $url, 'http://' ) && 0 !== strpos( $url, 'https://' ) ) {
		return array(
			'valid'  => false,
			'reason' => __( 'Domain must start with http:// or https://', 'affiliatewp-direct-link-tracking' )
		);
	}

	/**
	 * Make sure URL has at least one . (period) since a URL can still validate correctly if a domain suffix is not added
	 * This loosely checks for a TLD, e.g. .com
	 */
	if ( strpos( $url, '.' ) < 1 ) {
		return array(
			'valid'     => false,
			'reason'    => __( 'Direct Link did not have a domain suffix (.com, .org etc).', 'affiliatewp-direct-link-tracking' ),
			'notice_id' => 'direct_link_missing_domain_suffix'
		);
	}

	/**
	 * Validate the URL according to the RFC 2396 standards
	 *
	 * @see http://www.faqs.org/rfcs/rfc2396
	 * @see http://php.net/manual/en/filter.filters.validate.php
	 */
	if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return array(
			'valid'     => false,
			'reason'    => __( 'Domain was entered incorrectly.', 'affiliatewp-direct-link-tracking' ),
			'notice_id' => 'direct_link_failed_validation'
		);
	}

	// Domain has passed validation

	/**
	 * Store a clean version of the URL/domain
	 * The clean URL does not include www or domain protocol. This is needed by some further validation tests
	 * This will remove whitespace, protocols etc
	 */
	$clean_url = affwp_dlt_clean_url( $url );

	/**
	 * Check to see if the domain is blacklisted
	 *
	 * @since 1.1
	 */
	if ( in_array( affwp_dlt_base_domain( $url, true ), affwp_dlt_blacklisted_domains() ) ) {
		return array(
			'valid'     => false,
			'reason'    => __( 'Domain is blacklisted.', 'affiliatewp-direct-link-tracking' ),
			'notice_id' => 'direct_link_blacklisted'
		);
	}

	/**
	 * Check to see if the domain's base URL is blacklisted
	 * E.g. If the domain site.com/page is submitted, but site.com is blacklisted, it should fail
	 */
	if ( in_array( affwp_dlt_base_domain( $url, false ), affwp_dlt_blacklisted_domains() ) ) {
		return array(
	 		'valid'     => false,
	 		'reason'    => __( 'Domain\'s base domain is blacklisted.', 'affiliatewp-direct-link-tracking' ),
	 		'notice_id' => 'direct_link_blacklisted_base_domain'
		);
	}

	// URL has path. The $url passed to affwp_dlt_url_path() must include a domain protocol (http:// or https:// ) in order for parse_url() to work correctly
	if ( affwp_dlt_url_path( $url ) ) {

		/**
		 * If URL has a path we need to check that no other affiliate already has the "base" domain, eg mysite.com
		 * If another affiliate has the base domain, the domain is not allowed to be added or updated
		 */

		// Get the affiliate ID that belongs to the base domain without the protocol.
		$affiliate_id_of_base_domain = affwp_dlt_get_affiliate_id_from_domain( affwp_dlt_base_domain( $url ) );

		// Domain is being added or updated from the admin.
		if ( is_admin() && isset( $_POST['affwp_action'] ) ) {

			switch ( $_POST['affwp_action'] ) {

				case 'update_direct_link':
					$action                         = 'updated';
					$affiliate_id_of_current_domain = isset( $_POST['affiliate_id'] ) ? (int) $_POST['affiliate_id'] : '';
					break;

				case 'add_direct_link':
					$action                         = 'added';
					$data                           = affiliate_wp()->utils->process_request_data( $_POST, 'user_name' );
					$affiliate_id_of_current_domain = (int) affwp_get_affiliate_id( absint( $data['user_id'] ) );
					break;

			}

			/**
			 * Domain is being submitted from the admin.
			 * Affiliate ID of base domain doesn't match the affiliate ID for the direct link being added or updated.
			 */
			if ( ! empty( $affiliate_id_of_base_domain ) && ( $affiliate_id_of_base_domain !== $affiliate_id_of_current_domain ) ) {

				return array(
					'valid'     => false,
					'reason'    => __( 'Base domain already exists in database, assigned to another affiliate.', 'affiliatewp-direct-link-tracking' ),
					'notice_id' => 'direct_link_' . $action . '_base_domain_exists'
				);

			}

		} else {

			/**
			 * Domain is being submitted from the front-end Affiliate Area.
			 * Affiliate ID of base domain doesn't match the current affiliate
			 */
			if ( $affiliate_id_of_base_domain && (int) affwp_get_affiliate_id() !== $affiliate_id_of_base_domain ) {
				return array(
					'valid' => false
				);
			}

		}

	}

	/**
	 * Check the clean URL (without domain protocol or www) against all URLs in database to see if it already exists
	 * This could be a domain with or without a path
	 */
	if ( in_array( affwp_dlt_base_domain( $clean_url ), affwp_dlt_get_domains() ) ) {
		// URL already exists
		return array(
			'valid'     => false,
			'reason'    => __( 'Domain already exists in database.', 'affiliatewp-direct-link-tracking' ),
			'notice_id' => 'direct_link_domain_exists'
		);
	}

	// Return the clean URL. This is what will be inserted into the database.
	return $clean_url;

}

/**
 * Retrieves the clean version of the URL. This is the URL that will be inserted into the database
 * Removes whitespace, trailing slashes, protocols etc
 *
 * @since 1.0.0
 *
 * @param string $url The URL to clean, including protocol (so parse_url() can process it)
 *
 * @return string $clean_url The cleaned URL, false otherwise.
 */
function affwp_dlt_clean_url( $url = '' ) {

	// Parse the URL
	$clean_url = parse_url( $url );

	if ( affwp_dlt_url_path( $url ) ) {

		// Make sure the URL with path has a trailing slash
		$clean_url = trailingslashit( $clean_url['host'] . affwp_dlt_url_path( $url ) );

	} else {
		// Use just the host if there is no path
		$clean_url = $clean_url['host'];
	}

	// Remove www. if present
	if ( substr( $clean_url, 0, 4 ) === 'www.' ) {
		$clean_url = substr( $clean_url, 4 );
	}

	// force to lowercase
	$clean_url = strtolower( $clean_url );

	if ( $clean_url ) {
		// return the clean URL if all checks out
		return $clean_url;
	}

	return false;
}
