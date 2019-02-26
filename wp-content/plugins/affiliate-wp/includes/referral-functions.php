<?php
/**
 * Retrieves a referral object.
 *
 * @param int|AffWP\Referral $referral Referral ID or object.
 * @return AffWP\Referral|false Referral object, otherwise false.
 */
function affwp_get_referral( $referral = null ) {

	if ( is_object( $referral ) && isset( $referral->referral_id ) ) {
		$referral_id = $referral->referral_id;
	} elseif ( is_numeric( $referral ) ) {
		$referral_id = absint( $referral );
	} else {
		return false;
	}

	$referral = affiliate_wp()->referrals->get_object( $referral_id );

	if ( ! empty( $referral->products ) ) {
		// products is a multidimensional array. Double unserialize is not a typo
		$referral->products = maybe_unserialize( maybe_unserialize( $referral->products ) );
	}

	return $referral;
}

/**
 * Retrieves a referral's status.
 *
 * @since 1.6
 *
 * @param int|AffWP\Referral $referral Referral ID or object.
 * @return string|false Referral status, otherwise false.
 */
function affwp_get_referral_status( $referral ) {

	if ( ! $referral = affwp_get_referral( $referral ) ) {
		return false;
	}

	return $referral->status;
}

/**
 * Retrieves the status label for a referral.
 *
 * @since 1.6
 *
 * @param int|AffWP\Referral $referral Referral ID or object.
 * @return string|false $label The localized version of the referral status, otherwise false. If the status
 *                             isn't registered and the referral is valid, the default 'pending' status will
 *                             be returned
 */
function affwp_get_referral_status_label( $referral ) {

	if ( ! $referral = affwp_get_referral( $referral ) ) {
		return false;
	}

	$statuses = array(
		'paid'     => __( 'Paid', 'affiliate-wp' ),
		'unpaid'   => __( 'Unpaid', 'affiliate-wp' ),
		'rejected' => __( 'Rejected', 'affiliate-wp' ),
		'pending'  => __( 'Pending', 'affiliate-wp' ),
	);

	$label = array_key_exists( $referral->status, $statuses ) ? $statuses[ $referral->status ] : 'pending';

	/**
	 * Filters the referral status label.
	 *
	 * @since 1.6
	 *
	 * @param string         $label    A localized version of the referral status label.
	 * @param AffWP\Referral $referral Referral object.
	 */
	return apply_filters( 'affwp_referral_status_label', $label, $referral );

}

/**
 * Sets a referral's status.
 *
 * @since
 *
 * @param int|AffWP\Referral $referral   Referral ID or object.
 * @param string             $new_status Optional. New referral status to set. Default empty.
 * @return bool True if the referral status was successfully changed from the old status to the
 *              new one, otherwise false.
 */
function affwp_set_referral_status( $referral, $new_status = '' ) {

	if ( ! $referral = affwp_get_referral( $referral ) ) {
		return false;
	}

	$old_status = $referral->status;

	if( $old_status == $new_status ) {
		return false;
	}

	if( empty( $new_status ) ) {
		return false;
	}

	if( affiliate_wp()->referrals->update( $referral->ID, array( 'status' => $new_status ), '', 'referral' ) ) {

		// Old status cleanup.
		if ( 'paid' === $old_status ) {

			// Reverse the effect of a paid referral.
			affwp_decrease_affiliate_earnings( $referral->affiliate_id, $referral->amount );
			affwp_decrease_affiliate_referral_count( $referral->affiliate_id );

		} elseif ( 'unpaid' === $old_status ) {

			affwp_decrease_affiliate_unpaid_earnings( $referral->affiliate_id, $referral->amount );

		}

		// New status.
		if( 'paid' === $new_status ) {

			affwp_increase_affiliate_earnings( $referral->affiliate_id, $referral->amount );
			affwp_increase_affiliate_referral_count( $referral->affiliate_id );

		} elseif ( 'unpaid' === $new_status ) {

			affwp_increase_affiliate_unpaid_earnings( $referral->affiliate_id, $referral->amount );

			if ( 'pending' === $old_status || 'rejected' === $old_status ) {
				// Update the visit ID that spawned this referral
				affiliate_wp()->visits->update( $referral->visit_id, array( 'referral_id' => $referral->ID ), '', 'visit' );

				/**
				 * Fires when a referral is marked as accepted.
				 *
				 * @param int             $affiliate_id Referral affiliate ID.
				 * @param \AffWP\Referral $referral     The referral object.
				 */
				do_action( 'affwp_referral_accepted', $referral->affiliate_id, $referral );
			}
		}

		/**
		 * Fires immediately after a referral's status has been successfully updated.
		 *
		 * Will not fire if the new status matches the old one, or if `$new_status` is empty.
		 *
		 * @since
		 *
		 * @param int    $referral_id Referral ID.
		 * @param string $new_status  New referral status.
		 * @param string $old_status  Old referral status.
		 */
		do_action( 'affwp_set_referral_status', $referral->ID, $new_status, $old_status );

		return true;
	}

	return false;

}

/**
 * Adds a new referral to the database.
 *
 * Referral status cannot be updated here, use affwp_set_referral_status().
 *
 * @since 1.0
 *
 * @param array $data {
 *     Optional. Arguments for adding a new referral. Default empty array.
 *
 *     @type int          $user_id      User ID. Used to retrieve the affiliate ID if `affiliate_id` not given.
 *     @type int          $affiliate_id Affiliate ID.
 *     @type string       $user_name    User login. Used to retrieve the affiliate ID if `affiliate_id` not given.
 *     @type float        $amount       Referral amount. Default empty.
 *     @type string       $description  Description. Default empty.
 *     @type string       $reference    Referral reference (usually product information). Default empty.
 *     @type string       $context      Referral context (usually the integration it was generated from).
 *                                      Default empty.
 *     @type string|array $custom       Any custom data that can be passed to and stored with the referral. Accepts
 *                                      an array or string, and will be serialized when stored. Default empty.
 *     @type string       $status       Status to update the referral too. Default 'pending'.
 * }
 * @return int|bool 0|false if no referral was added, referral ID if it was successfully added.
 */
function affwp_add_referral( $data = array() ) {

	if ( empty( $data['user_id'] ) && empty( $data['affiliate_id'] ) && empty( $data['user_name'] ) ) {
		return 0;
	}

	$data = affiliate_wp()->utils->process_request_data( $data, 'user_name' );

	if ( empty( $data['affiliate_id'] ) ) {

		$user_id      = absint( $data['user_id'] );
		$affiliate_id = affiliate_wp()->affiliates->get_column_by( 'affiliate_id', 'user_id', $user_id );

		if ( ! empty( $affiliate_id ) ) {

			$data['affiliate_id'] = $affiliate_id;

		} else {

			return 0;

		}

	}

	if ( ! empty( $data['custom'] ) ) {
		if ( is_array( $data['custom'] ) ) {
			$data['custom'] = array_map( 'sanitize_text_field', $data['custom'] );
		} else {
			$data['custom'] = sanitize_text_field( $data['custom'] );
		}
	}

	$args = array(
		'affiliate_id' => absint( $data['affiliate_id'] ),
		'amount'       => ! empty( $data['amount'] )      ? sanitize_text_field( $data['amount'] )      : '',
		'description'  => ! empty( $data['description'] ) ? sanitize_text_field( $data['description'] ) : '',
		'reference'    => ! empty( $data['reference'] )   ? sanitize_text_field( $data['reference'] )   : '',
		'context'      => ! empty( $data['context'] )     ? sanitize_text_field( $data['context'] )     : '',
		'custom'       => ! empty( $data['custom'] )      ? $data['custom']                             : '',
		'date'         => ! empty( $data['date'] )        ? $data['date']                               : '',
		'status'       => 'pending',
	);

	if ( ! empty( $data['visit_id'] ) && ! affiliate_wp()->referrals->get_by( 'visit_id', $data['visit_id'] ) ) {
		$args['visit_id'] = absint( $data['visit_id'] );
	}

	$referral_id = affiliate_wp()->referrals->add( $args );

	if ( $referral_id ) {

		$status = ! empty( $data['status'] ) ? sanitize_text_field( $data['status'] ) : 'pending';

		affwp_set_referral_status( $referral_id, $status );

		return $referral_id;
	}

	return 0;

}

/**
 * Deletes a referral.
 *
 * If the referral has a status of 'paid', the affiliate's earnings and referral count will decrease.
 *
 * @since
 *
 * @param int|AffWP\Referral $referral Referral ID or object.
 * @return bool True if the referral was successfully deleted, otherwise false.
 */
function affwp_delete_referral( $referral ) {

	if ( ! $referral = affwp_get_referral( $referral ) ) {
		return false;
	}

	if ( $referral ) {
		if ( 'paid' === $referral->status ) {
			// This referral has already been paid, so decrease the affiliate's earnings
			affwp_decrease_affiliate_earnings( $referral->affiliate_id, $referral->amount );

			// Decrease the referral count
			affwp_decrease_affiliate_referral_count( $referral->affiliate_id );

		} elseif ( 'unpaid' === $referral->status ) {

			// Decrease the unpaid earnings.
			affwp_decrease_affiliate_unpaid_earnings( $referral->affiliate_id, $referral->amount );

		}
	}

	if( affiliate_wp()->referrals->delete( $referral->ID, 'referral' ) ) {

		/**
		 * Fires immediately after a referral has been deleted.
		 *
		 * @since
		 *
		 * @param int $referral_id Referral ID.
		 */
		do_action( 'affwp_delete_referral', $referral->ID );

		return true;

	}

	return false;
}

/**
 * Calculate the referral amount
 *
 * @param  string  $amount
 * @param  int     $affiliate_id
 * @param  int     $reference
 * @param  string  $rate
 * @param  int     $product_id
 * @return float
 */
function affwp_calc_referral_amount( $amount = '', $affiliate_id = 0, $reference = 0, $rate = '', $product_id = 0 ) {

	$rate     = affwp_get_affiliate_rate( $affiliate_id, false, $rate, $reference );
	$type     = affwp_get_affiliate_rate_type( $affiliate_id );
	$decimals = affwp_get_decimal_count();

	$referral_amount = ( 'percentage' === $type ) ? round( $amount * $rate, $decimals ) : $rate;

	if ( $referral_amount < 0 ) {
		$referral_amount = 0;
	}

	return (string) apply_filters( 'affwp_calc_referral_amount', $referral_amount, $affiliate_id, $amount, $reference, $product_id );

}

/**
 * Retrieves the number of referrals for the given affiliate.
 *
 * @since
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @param string|array        $status    Optional. Referral status or array of statuses. Default empty array.
 * @param array|string        $date      Optional. Array of date data with 'start' and 'end' key/value pairs,
 *                                       or a timestamp. Default empty array.
 * @return int Zero if the affiliate is invalid, or the number of referrals for the given arguments.
 */
function affwp_count_referrals( $affiliate_id = 0, $status = array(), $date = array() ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate_id ) ) {
		return 0;
	}

	$args = array(
		'affiliate_id' => $affiliate->ID,
		'status'       => $status
	);

	if( ! empty( $date ) ) {
		$args['date'] = $date;
	}

	return affiliate_wp()->referrals->count( $args );
}

/**
 * Retrieves an array of banned URLs.
 *
 * @since 2.0
 *
 * @return array The array of banned URLs
 */
function affwp_get_banned_urls() {
	$urls = affiliate_wp()->settings->get( 'referral_url_blacklist', array() );

	if ( ! empty( $urls ) ) {
		$urls = array_map( 'trim', explode( "\n", $urls ) );
		$urls = array_unique( $urls );
		$urls = array_map( 'sanitize_text_field', $urls );
	}

	/**
	 * Filters the list of banned URLs.
	 *
	 * @since 2.0
	 *
	 * @param array $url Banned URLs.
	 */
	return apply_filters( 'affwp_get_banned_urls', $urls );
}

/**
 * Determines if a URL is banned.
 *
 * @since 2.0
 *
 * @param string $url The URL to check against the black list.
 * @return bool True if banned, otherwise false.
 */
function affwp_is_url_banned( $url ) {
	$banned_urls = affwp_get_banned_urls();

	if( ! is_array( $banned_urls ) || empty( $banned_urls ) ) {
		$banned = false;
	}

	foreach( $banned_urls as $banned_url ) {

		$banned = ( stristr( trim( $url ), $banned_url ) ? true : false );

		if ( true === $banned ) {
			break;
		}
	}

	/**
	 * Filters whether the given URL is considered 'banned'.
	 *
	 * @since 2.0
	 *
	 * @param bool   $banned Whether the given URL is banned.
	 * @param string $url    The URL check for ban status.
	 */
	return apply_filters( 'affwp_is_url_banned', $banned, $url );
}
