<?php
/**
 * Retrieves a visit object.
 *
 * @since 1.9
 *
 * @param int|AffWP\Visit $visit Visit ID or object.
 * @return AffWP\Visit|false Visit object, otherwise false.
 */
function affwp_get_visit( $visit = null ) {

	if ( is_object( $visit ) && isset( $visit->visit_id ) ) {
		$visit_id = $visit->visit_id;
	} elseif( is_numeric( $visit ) ) {
		$visit_id = absint( $visit );
	} else {
		return false;
	}

	return affiliate_wp()->visits->get_object( $visit_id );
}

/**
 * Counts the number of visits logged for a given affiliate.
 *
 * @since
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @param array|string        $date      Optional. Array of date data with 'start' and 'end' key/value pairs,
 *                                       or a timestamp. Default empty array.
 * @return int|false Number of visits, otherwise 0|false.
 */
function affwp_count_visits( $affiliate = 0, $date = array() ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return 0;
	}

	$args = array(
		'affiliate_id' => $affiliate->ID,
	);

	if( ! empty( $date ) ) {
		$args['date'] = $date;
	}

	return affiliate_wp()->visits->count( $args );

}

/**
 * Deletes a visit record.
 *
 * @since 1.2
 *
 * @param int|AffWP\Visit $visit Visit ID or object.
 * @return bool True if the visit was successfully deleted, otherwise false.
 */
function affwp_delete_visit( $visit ) {

	if ( ! $visit = affwp_get_visit( $visit ) ) {
		return false;
	}

	if ( affiliate_wp()->visits->delete( $visit->ID, 'visit' ) ) {
		// Decrease the visit count
		affwp_decrease_affiliate_visit_count( $visit->affiliate_id );

		/**
		 * Fires immediately after a visit has been deleted.
		 *
		 * @since 1.2
		 *
		 * @param int $visit_id Visit ID.
		 */
		do_action( 'affwp_delete_visit', $visit->ID );

		return true;

	}

	return false;
}

/**
 * Sanitizes visit a URL.
 *
 * @since 1.7.5
 *
 * @param string $url The URL to sanitize.
 * @return string $url The sanitized URL.
 */
function affwp_sanitize_visit_url( $url ) {
	$original_url = $url;
	$referral_var = affiliate_wp()->tracking->get_referral_var();

	// Remove the referral var
	$url = remove_query_arg( $referral_var, $url );

	// Fallback for pretty permalinks
	if( $original_url === $url ) {
		if( strpos( $url, $referral_var ) ) {
			$url = preg_replace( '/(\/' . $referral_var . ')[\/](\w\-*)+/', '', $url );
		}
	}

	return $url;
}
