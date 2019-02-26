<?php
/**
 * Determines if the specified user ID is an affiliate.
 *
 * If no user ID is given, it will check the currently logged in user
 *
 * @since 1.0
 * @return bool
 */
function affwp_is_affiliate( $user_id = 0 ) {
	return (bool) affwp_get_affiliate_id( $user_id );
}

/**
 * Retrieves the affiliate ID of the specified user
 *
 * If no user ID is given, it will check the currently logged in user
 *
 * @since 1.0
 * @since 2.0.3 The current user is no longer taken into consideration in the admin
 *              or if doing Ajax when `$user_id` is empty.
 *
 * @param int $user_id Optional. User ID. Default is the ID of the current user.
 * @return int|false Affiliate ID, or false if the current user isn't logged-in or `$user_id` is empty.
 */
function affwp_get_affiliate_id( $user_id = 0 ) {

	if ( empty( $user_id ) ) {
		$is_admin_doing_ajax = is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX );

		if ( ! $is_admin_doing_ajax && ! is_user_logged_in() ) {
			return false;
		} elseif ( ! $is_admin_doing_ajax && is_user_logged_in() ) {
			$user_id = get_current_user_id();
		}
	}

	$cache_key    = md5( 'affwp_get_affiliate_id' . $user_id );
	$affiliate_id = wp_cache_get( $cache_key, 'affiliates' );

	if( false === $affiliate_id ) {

		$affiliate_id = affiliate_wp()->affiliates->get_column_by( 'affiliate_id', 'user_id', $user_id );

	}

	return $affiliate_id;
}


/**
 * Retrieves the username of the specified affiliate
 *
 * If no affiliate ID is given, it will check the currently logged in affiliate
 *
 * @since 1.6
 * @since 1.9 The `$affiliate` parameter now accepts an affiliate object.
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return string|false username if affiliate exists, boolean false otherwise
 */
function affwp_get_affiliate_username( $affiliate = 0 ) {

	$affiliate = affwp_get_affiliate( $affiliate );

	if ( $affiliate ) {
		$user_info = get_userdata( $affiliate->user_id );

		if ( $user_info ) {
			$username  = $user_info->user_login;
			return esc_html( $username );
		}

	}

	return false;

}

/**
 * Retrieves the affiliate first name and/or last name, if set.
 *
 * If only one name (first_name or last_name) is provided, this function will return
 * only that name.
 *
 * @since 1.8
 * @since 1.9 The `$affiliate` parameter now accepts an affiliate object.
 *
 * @uses affwp_get_affiliate_id
 * @uses affwp_get_affiliate
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return string The affiliate user's first and/or last name  if set. An empty string if the affiliate ID
 *                is invalid or neither first nor last name are set.
 */
function affwp_get_affiliate_name( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return '';
	}

	if ( ! $user_info = get_userdata( $affiliate->user_id ) ) {
		return '';
	}

	$first_name   = esc_html( $user_info->first_name );
	$last_name    = esc_html( $user_info->last_name );

	// Check if both names are set first.
	if ( ! empty( $first_name ) && ! empty( $last_name ) ) {
		return $first_name . ' ' . $last_name;
	}

	// If neither are set, return an empty string.
	if ( empty( $first_name ) && empty( $last_name ) ) {
		return '';
	}

	// First name only
	if ( ! empty( $first_name ) && empty( $last_name ) ) {
		return $first_name;
	}

	// Last name only
	if ( empty( $first_name ) && ! empty( $last_name ) ) {
		return $last_name;
	}
}


/**
 * Retrieves the affiliate first name, if set.
 *
 * @since 2.1.7
 *
 * @uses affwp_get_affiliate_id
 * @uses get_userdata
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return string The affiliate user's first name  if set. An empty string if the affiliate ID
 *                is invalid or first name is not set.
 */
function affwp_get_affiliate_last_name( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return '';
	}

	if ( ! $user_info = get_userdata( $affiliate->user_id ) ) {
		return '';
	}

	return esc_html( $user_info->last_name );
	
}

/**
 * Retrieves the affiliate first name, if set.
 *
 * @since 2.1.7
 *
 * @uses affwp_get_affiliate_id
 * @uses get_userdata
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return string The affiliate user's first name  if set. An empty string if the affiliate ID
 *                is invalid or first name is not set.
 */
function affwp_get_affiliate_first_name( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return '';
	}

	if ( ! $user_info = get_userdata( $affiliate->user_id ) ) {
		return '';
	}

	return esc_html( $user_info->first_name );
	
}

/**
 * Determines whether or not the affiliate is active.
 *
 * If no affiliate ID is given, it will check the currently logged in affiliate
 *
 * @since 1.6
 * @since 1.9 The `$affiliate` parameter now accepts an affiliate object.
 *
 * @param int|AffWP\Affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return bool True if the affiliate has an 'active' status, false otherwise.
 */
function affwp_is_active_affiliate( $affiliate = 0 ) {

	if ( 'active' == affwp_get_affiliate_status( $affiliate ) ) {
		return true;
	}

	return false;
}

/**
 * Retrieves an affiliate's user ID.
 *
 * @since 1.0
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return int|false Affiliate user ID, otherwise false.
 */
function affwp_get_affiliate_user_id( $affiliate = 0 ) {

	$user_id = false;

	if ( $affiliate = affwp_get_affiliate( $affiliate ) ) {
		$user_id = $affiliate->user_id;
	}

	return $user_id;
}

/**
 * Retrieves the affiliate object.
 *
 * @since 1.0
 * @since 1.9 The `$affiliate` parameter was made optional. Affiliates can also now
 *            be retrieved by username.
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default null.
 * @return AffWP\Affiliate|false Affiliate object if found, otherwise false.
 */
function affwp_get_affiliate( $affiliate = 0 ) {

	if ( empty( $affiliate ) ) {
		$affiliate = affwp_get_affiliate_id();
	}

	if ( is_object( $affiliate ) && isset( $affiliate->affiliate_id ) ) {
		$affiliate_id = $affiliate->affiliate_id;
	} elseif ( is_numeric( $affiliate ) ) {
		$affiliate_id = absint( $affiliate );
	} elseif ( is_string( $affiliate ) ) {
		if ( $user = get_user_by( 'login', $affiliate ) ) {
			if ( $affiliate = affiliate_wp()->affiliates->get_by( 'user_id', $user->ID ) ) {
				$affiliate_id = $affiliate->affiliate_id;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else {
		return false;
	}

	return affiliate_wp()->affiliates->get_object( $affiliate_id );
}

/**
 * Retrieves the affiliate's status.
 *
 * @since 1.0
 * @since 1.9 `$affiliate` was made optional.
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return string|false Affiliate status, false otherwise.
 */
function affwp_get_affiliate_status( $affiliate = 0 ) {

	$status = false;

	if ( $affiliate = affwp_get_affiliate( $affiliate ) ) {
		$status = $affiliate->status;
	}

	return $status;
}

/**
 * Sets the status for an affiliate
 *
 * @since 1.0
 *
 * @param int|AffWP\Affiliate $affiliate Affiliate ID or object.
 * @param string              $status    Optional. New affiliate status. Default empty.
 * @return bool True if the new status was set, false otherwise.
 */
function affwp_set_affiliate_status( $affiliate, $status = '' ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	$old_status = $affiliate->status;

	/**
	 * Fires just prior to update the affiliate status.
	 *
	 * @param  string $status     The new affiliate status. Optional.
	 * @param  string $old_status The old affiliate status.
	 */
	do_action( 'affwp_set_affiliate_status', $affiliate->ID, $status, $old_status );

	if ( affiliate_wp()->affiliates->update( $affiliate->ID, array( 'status' => $status ), '', 'affiliate' ) ) {

		return true;
	}

}

/**
 * Retrieves the affiliate's status and returns a translatable string
 *
 * @since 1.8
 * @since 1.9 The `$affiliate` parameter can now accept an affiliate object and was made optional.
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default current affiliate.
 * @return string $status_label A translatable, filterable label indicating affiliate status
 */
function affwp_get_affiliate_status_label( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return '';
	}

	$status       = '';
	$status_label = '';

	// Get current affiliate status.
	$status = $affiliate->status;

	// Return translatable string.
	switch( $status ) {

		case 'active':
			$status_label = __( 'Active','affiliate-wp' );
			break;
		case 'inactive':
			$status_label = __( 'Inactive','affiliate-wp' );
			break;
		case 'pending':
			$status_label = __( 'Pending','affiliate-wp' );
			break;
		case 'rejected':
			$status_label = __( 'Rejected','affiliate-wp' );
			break;
	}

	/**
	 * Filters the affiliate status label.
	 *
	 * @since 1.8
	 * @since 1.9 The `$affiliate` parameter was added.
	 *
	 * @param string          $status_label Localized status label string.
	 * @param AffWP\Affiliate $affiliate    Affiliate object.
	 */
	return apply_filters( 'affwp_get_affiliate_status_label', $status_label, $affiliate );
}

/**
 * Retrieves the referral rate for an affiliate.
 *
 * @since 1.0
 * @since 1.9 The `$affiliate` parameter now accepts an affiliate object.
 *
 * @param int|AffWP\Affiliate $affiliate    Optional. Affiliate ID or object. Default is the current affiliate.
 * @param bool                $formatted    Optional. Whether to return a formatted rate with %/currency.
 *                                          Default false.
 * @param string              $product_rate Optional. A custom product rate that overrides site/affiliate settings.
 *                                          Default empty.
 * @param string              $reference    Optional. Reference. Default empty.
 * @return string Affiliate rate, empty string otherwise.
 */
function affwp_get_affiliate_rate( $affiliate = 0, $formatted = false, $product_rate = '', $reference = '' ) {
	// Forward-compat with affiliate objects.
	if ( is_object( $affiliate ) ) {
		if ( isset( $affiliate->affiliate_id ) ) {
			$affiliate_id = $affiliate->affiliate_id;
		} else {
			$affiliate_id = 0;
		}
	} else {
		$affiliate_id = $affiliate;
	}

	// Global referral rate setting, fallback to 20
	$default_rate = affiliate_wp()->settings->get( 'referral_rate', 20 );
	$default_rate = affwp_abs_number_round( $default_rate );

	// Get product-specific referral rate, fallback to global rate
	$product_rate = affwp_abs_number_round( $product_rate );
	$product_rate = ( null !== $product_rate ) ? $product_rate : $default_rate;

	// Get affiliate-specific referral rate
	$affiliate_rate = affiliate_wp()->affiliates->get_column( 'rate', $affiliate_id );

	// Get rate in order of priority: Affiliate -> Product -> Global
	$rate = affwp_abs_number_round( $affiliate_rate );
	$rate = ( null !== $rate ) ? $rate : $product_rate;

	// Get the referral rate type
	$type = affwp_get_affiliate_rate_type( $affiliate_id );

	// Format percentage rates
	$rate = ( 'percentage' === $type ) ? $rate / 100 : $rate;

	/**
	 * Filter the affiliate rate
	 *
	 * @param  string  $rate
	 * @param  int     $affiliate_id
	 * @param  string  $type
	 */
	$rate = (string) apply_filters( 'affwp_get_affiliate_rate', $rate, $affiliate_id, $type, $reference );

	// Return rate now if formatting is not required
	if ( ! $formatted ) {
		return $rate;
	}

	// Format the rate based on the type
	$rate = affwp_format_rate( $rate, $type );

	return $rate;
}

/**
 * Determines if an affiliate has a custom rate.
 *
 * @since 1.5
 * @since 1.9 The `$affiliate` parameter can now accept an affiliate object and was made optional.
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return bool Whether the affiliate has a custom rate, false if the affiliate doesn't exist.
 */
function affwp_affiliate_has_custom_rate( $affiliate = 0 ) {

	if ( $affiliate = affwp_get_affiliate( $affiliate ) ) {
		$custom_rate = $affiliate->has_custom_rate();
		$affiliate_id = $affiliate->ID;
	} else {
		$custom_rate = false;
		$affiliate_id = 0;
	}

	/**
	 * Filters whether the affiliate has a custom rate.
	 *
	 * @since 1.5
	 *
	 * @param bool $custom_rate  Whether the affiliate has a custom rate.
	 * @param int  $affiliate_id Affiliate ID.
	 */
	return apply_filters( 'affwp_affiliate_has_custom_rate', $custom_rate, $affiliate_id );
}

/**
 * Retrieves the referral rate type for an affiliate.
 *
 * Either "flat" or "percentage"
 *
 * @since 1.1
 * @since 1.9 The `$affiliate` parameter can now accept an affiliate object and was made optional.
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return string Affiliate rate type.
 */
function affwp_get_affiliate_rate_type( $affiliate = 0 ) {

	// Default rate type.
	$type = affiliate_wp()->settings->get( 'referral_rate_type', 'percentage' );

	$affiliate_id = 0;

	if ( $affiliate = affwp_get_affiliate( $affiliate ) ) {
		$affiliate_id = $affiliate->ID;

		// Allowed types
		$types = affwp_get_affiliate_rate_types();

		$affiliate_rate_type = $affiliate->rate_type();

		if ( $affiliate_rate_type !== $type ) {
			$type = $affiliate_rate_type;
		}

		if ( ! array_key_exists( $type, $types ) ) {
			$type = 'percentage';
		}
	}

	/**
	 * Filters the affiliate rate type.
	 *
	 * @since 1.1
	 *
	 * @param string $type         Affiliate rate type. Default values will be 'percentage' or 'flat'.
	 * @param int    $affiliate_id Affiliate ID.
	 */
	return apply_filters( 'affwp_get_affiliate_rate_type', $type, $affiliate_id );

}

/**
 * Retrieves an array of allowed affiliate rate types.
 *
 * @since 1.1
 *
 * @return array Rate types.
 */
function affwp_get_affiliate_rate_types() {

	// Allowed types
	$types = array(
		'percentage' => __( 'Percentage (%)', 'affiliate-wp' ),
		'flat'       => sprintf( __( 'Flat %s', 'affiliate-wp' ), affwp_get_currency() )
	);

	/**
	 * Filters the available rate types.
	 *
	 * @since 1.1
	 *
	 * @param array $types Array of key/value pairs of rate types.
	 */
	return apply_filters( 'affwp_get_affiliate_rate_types', $types );

}

/**
 * Retrieves the affiliate's email address.
 *
 * @since 1.0
 *
 * @param int|AffWP\Affiliate $affiliate Affiliate ID or object.
 * @param string|false        $default   Optional. Default email address. Default false.
 * @return string|false Affiliate email, value of `$default`, or false.
 */
function affwp_get_affiliate_email( $affiliate, $default = false ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return $default;
	}

	$user = get_userdata( $affiliate->user_id );

	if ( empty( $user->user_email ) || ! is_email( $user->user_email ) ) {
		return $default;
	}

	return $user->user_email;

}

/**
 * Retrieves the affiliate's payment email address.
 *
 * @since 1.7
 * @since 1.9 The `$affiliate` parameter can now accept an affiliate object and was made optional.
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return string|false Affiliate payment email if set and valid, if not set, the user email. Otherwise false.
 */
function affwp_get_affiliate_payment_email( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	return $affiliate->payment_email();
}

/**
 * Retrieves the affiliate's user login (username).
 *
 * @since 1.6
 *
 * @param int|AffWP\Affiliate $affiliate Affiliate ID or object.
 * @param string|false        $default   Optional. Default username. Default false.
 * @return string|false Affiliate login, value of `$default`, or false.
 */
function affwp_get_affiliate_login( $affiliate, $default = false ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return $default;
	}

	$user = get_userdata( $affiliate->user_id );

	if ( empty( $user->user_login ) ) {
		return $default;
	}

	return $user->user_login;
}

/**
 * Deletes an affiliate.
 *
 * @since 1.0
 *
 * @param int|AffWP\Affiliate $affiliate   Affiliate ID or object.
 * @param bool                $delete_data Whether to also delete referral and visit data. Default false.
 * @return bool True if the affiliate (and optionally data) was deleted, false otherwise.
 */
function affwp_delete_affiliate( $affiliate, $delete_data = false ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	$affiliate_id = $affiliate->ID;

	if( $delete_data ) {

		$referrals = affiliate_wp()->referrals->get_referrals( array(
			'affiliate_id' => $affiliate_id,
			'number'       => -1
		) );

		$visits = affiliate_wp()->visits->get_visits( array(
			'affiliate_id' => $affiliate_id,
			'number'       => -1
		) );

		foreach( $referrals as $referral ) {
			affiliate_wp()->referrals->delete( $referral->referral_id );
		}

		foreach( $visits as $visit ) {
			affiliate_wp()->visits->delete( $visit->visit_id );
		}

		delete_user_meta( $affiliate->user_id, 'affwp_referral_notifications' );
		delete_user_meta( $affiliate->user_id, 'affwp_promotion_method' );

	}

	$deleted = affiliate_wp()->affiliates->delete( $affiliate_id, 'affiliate' );

	if( $deleted ) {

		/**
		 * Fires immediately after an affiliate is deleted.
		 *
		 * @param int             $affiliate_id The affiliate ID.
		 * @param bool            $delete_data  Whether the user data was also flagged for deletion.
		 * @param AffWP\Affiliate $affiliate    Affiliate object.
		 */
		do_action( 'affwp_affiliate_deleted', $affiliate_id, $delete_data, $affiliate );

	}

	return $deleted;

}

/**
 * Retrieves the total paid earnings for an affiliate.
 *
 * @since 1.0
 *
 * @param int|AffWP\Affiliate $affiliate Affiliate ID or object.
 * @param bool                $formatted Optional. Whether to retrieve formatted earnings.
 *                                       Default false.
 * @return float|false Affiliate earnings, otherwise false.
 */
function affwp_get_affiliate_earnings( $affiliate, $formatted = false ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	$earnings = $affiliate->earnings;

	if ( empty( $earnings ) ) {

		$earnings = 0;

	}

	if ( $formatted ) {

		$earnings = affwp_currency_filter( affwp_format_amount( $earnings ) );

	}

	return $earnings;
}

/**
 * Retrieves the total unpaid earnings for an affiliate.
 *
 * @since 1.0
 *
 * @param int|AffWP\Affiliate $affiliate Affiliate ID or object.
 * @param bool                $formatted Optional. Whether to retrieve formatted unpaid earnings.
 *                                       Default false.
 * @return float|false Unpaid affiliate earnings, otherwise false.
 */
function affwp_get_affiliate_unpaid_earnings( $affiliate, $formatted = false ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	$earnings = $affiliate->unpaid_earnings;

	if ( $formatted ) {

		$earnings = affwp_currency_filter( affwp_format_amount( $earnings ) );

	}

	return $earnings;
}

/**
 * Increases an affiliate's total paid earnings by the specified amount.
 *
 * @since 1.0
 *
 * @param int|AffWP\Affiliate $affiliate Affiliate ID or object.
 * @param string|float        $amount    Optional. Amount to increase the affiliate's earnings. Default empty.
 * @return float|false The affiliate's updated earnings, false otherwise.
 */
function affwp_increase_affiliate_earnings( $affiliate, $amount = '' ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	if ( empty( $amount ) || floatval( $amount ) <= 0 ) {
		return false;
	}

	$earnings = affwp_get_affiliate_earnings( $affiliate->ID );
	$earnings += $amount;
	$earnings = round( $earnings, affwp_get_decimal_count() );

	if ( affiliate_wp()->affiliates->update( $affiliate->ID, array( 'earnings' => $earnings ), '', 'affiliate' ) ) {
		$alltime = get_option( 'affwp_alltime_earnings' );
		$alltime += $amount;
		update_option( 'affwp_alltime_earnings', $alltime );

		return $earnings;

	} else {

		return false;

	}

}

/**
 * Decreases an affiliate's total paid earnings by the specified amount.
 *
 * @since 1.0
 *
 * @param int|AffWP\Affiliate $affiliate Affiliate ID or object.
 * @param string|float        $amount    Optional. Amount to decrease the affiliate's earnings. Default empty.
 * @return float|false The affiliate's updated earnings, false otherwise.
 */
function affwp_decrease_affiliate_earnings( $affiliate, $amount = '' ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	if ( empty( $amount ) || floatval( $amount ) <= 0 ) {
		return false;
	}

	$earnings = affwp_get_affiliate_earnings( $affiliate->ID );
	$earnings -= $amount;
	$earnings = round( $earnings, affwp_get_decimal_count() );

	if ( $earnings < 0 ) {
		$earnings = 0;
	}

	if ( affiliate_wp()->affiliates->update( $affiliate->ID, array( 'earnings' => $earnings ), '', 'affiliate' ) ) {

		$alltime = get_option( 'affwp_alltime_earnings' );
		$alltime -= $amount;
		if ( $alltime < 0 ) {
			$alltime = 0;
		}
		update_option( 'affwp_alltime_earnings', $alltime );

		return $earnings;

	} else {

		return false;

	}

}

/**
 * Increases an affiliate's unpaid earnings.
 *
 * @since 2.0
 *
 * @param \AffWP\Affiliate|int $affiliate Affiliate object or ID.
 * @param float                $amount    Amount to increase unpaid earnings by.
 * @param bool                 $replace   Optional. Whether to replace the current unpaid earnings count.
 *                                        Default false.
 * @return float|false New unpaid earnings value upon successful update, otherwise false.
 */
function affwp_increase_affiliate_unpaid_earnings( $affiliate, $amount, $replace = false ) {
	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	if ( empty( $amount ) || floatval( $amount ) <= 0 ) {
		return false;
	}

	if ( false === $replace ) {
		$unpaid_earnings = affwp_get_affiliate_unpaid_earnings( $affiliate );
	} else {
		$unpaid_earnings = 0;
	}

	$unpaid_earnings += $amount;
	$unpaid_earnings = round( $unpaid_earnings, affwp_get_decimal_count() );

	if ( affiliate_wp()->affiliates->update( $affiliate->ID, array( 'unpaid_earnings' => $unpaid_earnings ), '', 'affiliate' ) ) {

		return $unpaid_earnings;

	} else {

		return false;

	}
}

/**
 * Decreases an affiliate's unpaid earnings.
 *
 * @since 2.0
 *
 * @param \AffWP\Affiliate|int $affiliate Affiliate object or ID.
 * @param float                $amount    Amount to decrease unpaid earnings by.
 * @return float|false New unpaid earnings value upon successful update, otherwise false.
 */
function affwp_decrease_affiliate_unpaid_earnings( $affiliate, $amount ) {
	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	if ( empty( $amount ) || floatval( $amount ) <= 0 ) {
		return false;
	}

	$unpaid_earnings = affwp_get_affiliate_unpaid_earnings( $affiliate );
	$unpaid_earnings -= $amount;
	$unpaid_earnings = round( $unpaid_earnings, affwp_get_decimal_count() );

	if ( $unpaid_earnings < 0 ) {
		$unpaid_earnings = 0;
	}

	if ( affiliate_wp()->affiliates->update( $affiliate->ID, array( 'unpaid_earnings' => $unpaid_earnings ), '', 'affiliate' ) ) {

		return $unpaid_earnings;

	} else {

		return false;

	}

}

/**
 * Retrieves the number of paid referrals for an affiliate.
 *
 * @since 1.0
 * @since 1.9 The `$affiliate` parameter can now accept an affiliate object.
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return int|false The affiliate's referral count, false otherwise.
 */
function affwp_get_affiliate_referral_count( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	return $affiliate->referrals;
}

/**
 * Increases an affiliate's total paid referrals by 1.
 *
 * @since 1.0
 * @since 1.9 The `$affiliate` parameter can now accept an affiliate object.
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return float|false The updated referral count, otherwise false.
 */
function affwp_increase_affiliate_referral_count( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	$referrals = affwp_get_affiliate_referral_count( $affiliate->ID );
	$referrals += 1;

	if ( affiliate_wp()->affiliates->update( $affiliate->ID, array( 'referrals' => $referrals ), '', 'affiliate' ) ) {

		return $referrals;

	} else {

		return false;

	}

}

/**
 * Decreases an affiliate's total paid referrals by 1.
 *
 * @since 1.0
 * @since 1.9 The `$affiliate` parameter can now accept an affiliate object.
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return float|false The updated referral count, otherwise false.
 */
function affwp_decrease_affiliate_referral_count( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	$referrals = affwp_get_affiliate_referral_count( $affiliate->ID );
	$referrals -= 1;
	if ( $referrals < 0 ) {
		$referrals = 0;
	}

	if ( affiliate_wp()->affiliates->update( $affiliate->ID, array( 'referrals' => $referrals ), '', 'affiliate' ) ) {

		return $referrals;

	} else {

		return false;

	}

}

/**
 * Retrieves an affiliate's total visit count.
 *
 * @since 1.0
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return int|false The new affiliate visit count, otherwise false.
 */
function affwp_get_affiliate_visit_count( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	$visits = $affiliate->visits;

	if ( $visits < 0 ) {
		$visits = 0;
	}

	return absint( $visits );
}

/**
 * Increases an affiliate's total visit count by 1.
 *
 * @since 1.0
 * @since 1.9 The `$affiliate` parameter can now accept an affiliate object.
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return int|false The new affiliate visit count, otherwise false.
 */
function affwp_increase_affiliate_visit_count( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	$visits = affwp_get_affiliate_visit_count( $affiliate->ID );
	$visits += 1;

	if ( affiliate_wp()->affiliates->update( $affiliate->ID, array( 'visits' => $visits ), '', 'affiliate' ) ) {

		return $visits;

	} else {

		return false;

	}

}

/**
 * Decreases an affiliate's total visit count by 1.
 *
 * @since 1.0
 * @since 1.9 The `$affiliate` parameter can now accept an affiliate object.
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return float|false The affiliate's updated visit count, otherwise false.
 */
function affwp_decrease_affiliate_visit_count( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	$visits = affwp_get_affiliate_visit_count( $affiliate->ID );
	$visits -= 1;

	if ( $visits < 0 ) {
		$visits = 0;
	}

	if ( affiliate_wp()->affiliates->update( $affiliate->ID, array( 'visits' => $visits ), '', 'affiliate' ) ) {

		return $visits;

	} else {

		return false;

	}

}

/**
 * Retrieves the affiliate's conversion rate.
 *
 * @since 1.0
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return string|false The affiliate's conversion rate, otherwise false.
 */
function affwp_get_affiliate_conversion_rate( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	$rate = 0;

	$referrals = affiliate_wp()->referrals->count( array(
		'affiliate_id' => $affiliate->ID,
		'status'       => array( 'paid', 'unpaid' )
	) );

	$visits = affwp_get_affiliate_visit_count( $affiliate->ID );

	if ( $visits > 0 ) {
		$rate = $referrals / $visits;
	}

	$rate = affwp_format_rate( $rate );

	/**
	 * Filters the conversion rate.
	 *
	 * @since 1.0
	 *
	 * @param string $rate         Formatted conversion rate.
	 * @param int    $affiliate_id Affiliate ID.
	 */
	return apply_filters( 'affwp_get_affiliate_conversion_rate', $rate, $affiliate->ID );

}

/**
 * Retrieves the affiliate's tracked campaigns.
 *
 * @since 1.7
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return array|false The affiliate's campaigns, otherwise false.
 */
function affwp_get_affiliate_campaigns( $affiliate = 0 ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	$campaigns = affiliate_wp()->campaigns->get_campaigns( array(
		'affiliate_id' => $affiliate->ID,
		'number'       => 100,
	) );

	/**
	 * Filters the list of campaigns associated with an affiliate.
	 *
	 * @since 1.7
	 *
	 * @param array $campaigns    The affiliate's campaigns.
	 * @param int   $affiliate_id Affiliate ID.
	 */
	return apply_filters( 'affwp_get_affiliate_campaigns', $campaigns, $affiliate->ID );
}

/**
 * Adds a new affiliate to the database.
 *
 * @since 1.0
 *
 * @see Affiliate_WP_DB_Affiliates::add()
 *
 * @param array $data {
 *     Optional. Array of arguments for adding a new affiliate. Default empty array.
 *
 *     @type string $status          Affiliate status. Default 'active'.
 *     @type string $date_registered Date the affiliate was registered. Default is the current time.
 *     @type string $rate            Affiliate-specific referral rate.
 *     @type string $rate_type       Rate type. Accepts 'percentage' or 'flat'.
 *     @type string $payment_email   Affiliate payment email.
 *     @type int    $earnings        Affiliate earnings. Default 0.
 *     @type int    $referrals       Number of affiliate referrals.
 *     @type int    $visits          Number of visits.
 *     @type int    $user_id         User ID used to correspond to the affiliate.
 *     @type string $user_name       User login. Used to retrieve the affiliate ID if `affiliate_id` and
 *                                   `user_id` not given.
 *     @type string $notes           Notes about the affiliate for use by administrators.
 *     @type string $website_url     The affiliate's website URL.
 * }
 * @return int|false The ID for the newly-added affiliate, otherwise false.
 */
function affwp_add_affiliate( $data = array() ) {

	if ( ! empty( $data['status'] ) ) {
		$status = $data['status'];
	} elseif ( affiliate_wp()->settings->get( 'require_approval' ) ) {
		$status = 'pending';
	} else {
		$status = 'active';
	}

	$data = affiliate_wp()->utils->process_request_data( $data, 'user_name' );

	// If a user email is passed, then attempt to also create a new user.
	if ( ! empty( $data['user_email'] ) ) {

		if ( ! empty( $data['user_name'] ) ) {
			$username = sanitize_text_field(( $data['user_name'] ) );
		} else {
			$username = sanitize_user( $data['user_email'] );
		}

		$user_args = array(
			'user_email' => sanitize_text_field( $data['user_email'] ),
			'user_login' => $username,
			'user_pass'  => wp_generate_password( 24 ),
		);

		/**
		 * Filters the arguments used for creating new users when adding an affiliate.
		 *
		 * @since 2.1.8
		 *
		 * @param array $user_args Arguments passed to wp_insert_user().
		 * @param array $data      Arguments passed to affwp_add_affiliate().
		 */
		$user_args = apply_filters( 'affwp_add_affiliate_user_args', $user_args, $data );

		// Create the user account.
		$user_id = wp_insert_user( $user_args );

		if ( is_wp_error( $user_id ) ) {
			return false;
		}

		$data['user_id'] = $user_id;

	}

	if ( empty( $data['user_id'] ) ) {
		return false;
	} else {
		$user_id = absint( $data['user_id'] );
	}

	$args = array(
		'user_id'         => $user_id,
		'status'          => $status,
		'rate'            => ! empty( $data['rate'] ) ? sanitize_text_field( $data['rate'] ) : '',
		'rate_type'       => ! empty( $data['rate_type' ] ) ? sanitize_text_field( $data['rate_type'] ) : '',
		'payment_email'   => ! empty( $data['payment_email'] ) ? sanitize_text_field( $data['payment_email'] ) : '',
		'notes'           => ! empty( $data['notes' ] ) ? wp_kses_post( $data['notes'] ) : '',
		'website_url'     => ! empty( $data['website_url'] ) ? sanitize_text_field( $data['website_url'] ) : '',
		'date_registered' => ! empty( $data['date_registered'] ) ? $data['date_registered'] : '',
	);

	$affiliate_id = affiliate_wp()->affiliates->add( $args );

	if ( $affiliate_id ) {

		affwp_set_affiliate_status( $affiliate_id, $status );

		return $affiliate_id;
	}

	return false;

}

/**
 * Updates an affiliate.
 *
 * @since 1.0
 * @since 1.9 Support was added for updating an affiliate's status.
 *
 * @todo Document `$data` as a hash notation
 *
 * @param array $data Optional. Affiliate data array. Default empty array.
 * @return bool True if the affiliate was updated, false otherwise.
 */
function affwp_update_affiliate( $data = array() ) {
	if ( empty( $data['affiliate_id'] ) ) {
		return false;
	}

	$args         = array();
	$affiliate_id = absint( $data['affiliate_id'] );
	$affiliate    = affwp_get_affiliate( $affiliate_id );
	$user_id      = empty( $affiliate->user_id ) ? absint( $data['user_id'] ) : $affiliate->user_id;

	$args['account_email'] = ! empty( $data['account_email' ] ) && is_email( $data['account_email' ] ) ? sanitize_text_field( $data['account_email'] ) : '';
	$args['payment_email'] = ! empty( $data['payment_email' ] ) && is_email( $data['payment_email' ] ) ? sanitize_text_field( $data['payment_email'] ) : '';
	$args['rate']          = ( isset( $data['rate' ] ) && '' !== $data['rate' ] )                      ? sanitize_text_field( $data['rate'] )          : '';
	$args['rate_type']     = ! empty( $data['rate_type' ] ) ? sanitize_text_field( $data['rate_type'] ) : '';
	$args['status']        = ! empty( $data['status'] ) ? sanitize_text_field( $data['status'] ) : $affiliate->status;
	$args['user_id']       = $user_id;
	$args['notes']         = ! empty( $data['notes' ] ) ? wp_kses_post( $data['notes'] ) : '';

	if ( ! empty( $data['date_registered'] ) && $data['date_registered'] !== $affiliate->date_registered ) {
		$timestamp = strtotime( $data['date_registered'] ) - affiliate_wp()->utils->wp_offset;

		$args['date_registered'] = gmdate( 'Y-m-d H:i:s', $timestamp );
	}

	/**
	 * Fires immediately before data for the current affiliate is updated.
	 *
	 * @since 1.8
	 *
	 * @param stdClass $affiliate Affiliate object.
	 * @param array    $args      Prepared affiliate data.
	 * @param array    $data      Raw affiliate data.
	 */
	do_action( 'affwp_pre_update_affiliate', $affiliate, $args, $data );

	// Change the affiliate's status if different from their old status
	if ( $args['status'] !== $affiliate->status ) {
		$status = affwp_set_affiliate_status( $affiliate_id, $args['status'] );
	}

	$updated = affiliate_wp()->affiliates->update( $affiliate_id, $args, '', 'affiliate' );

	/**
	 * Fires immediately after an affiliate has been updated.
	 *
	 * @since 1.8
	 *
	 * @param stdClass $affiliate Updated affiliate object.
	 * @param bool     $updated   Whether the update was successful.
	 */
	do_action( 'affwp_updated_affiliate', affwp_get_affiliate( $affiliate ), $updated );

	if ( $updated ) {

		// Update affiliate's account email
		if ( wp_update_user( array( 'ID' => $user_id, 'user_email' => $args['account_email'] ) ) ) {

			// Add or delete affiliate notes
			if ( $args['notes'] ) {
				affwp_update_affiliate_meta( $affiliate_id, 'notes', $args['notes'] );
			} else {
				affwp_delete_affiliate_meta( $affiliate_id, 'notes' );
			}

			return true;
		}

	}
	return false;
}

/**
 * Updates an affiliate's profile settings.
 *
 * @since 1.0
 *
 * @todo Document `$data` as a hash notation
 *
 * @return bool
 */
function affwp_update_profile_settings( $data = array() ) {

	if ( ! is_user_logged_in() ) {
		return false;
	}

	if ( empty( $data['affiliate_id'] ) ) {
		return false;
	}

	$affiliate_id = absint( $data['affiliate_id'] );
	$user_id      = affwp_get_affiliate_user_id( $affiliate_id );

	if ( $user_id !== get_current_user_id() && ! current_user_can( 'manage_affiliate' ) ) {
		return false;
	}

	if ( ! empty( $data['referral_notifications'] ) ) {

		update_user_meta( $user_id, 'affwp_referral_notifications', '1' );

	} else {

		delete_user_meta( $user_id, 'affwp_referral_notifications' );

	}

	if ( ! empty( $data['payment_email'] ) && is_email( $data['payment_email'] ) ) {
		affiliate_wp()->affiliates->update( $affiliate_id, array( 'payment_email' => $data['payment_email'] ), '', 'affiliate' );
	}

	/**
	 * Fires immediately after an affiliate's profile settings have been updated.
	 *
	 * @since 1.0
	 *
	 * @param array $data Affiliate profile data.
	 */
	do_action( 'affwp_update_affiliate_profile_settings', $data );

	if ( ! empty( $_POST['affwp_action'] ) ) {
		wp_redirect( add_query_arg( 'affwp_notice', 'profile-updated' ) ); exit;
	}
}

/**
 * Builds an affiliate's referral URL.
 *
 * Used by creatives, referral URL generator and [affiliate_referral_url] shortcode
 *
 * @since 1.6
 *
 * @param array $args {
 *     Optional. Array of arguments for building an affiliate referral URL. Default empty array.
 *
 *     @type int          $affiliate_id Affiliate ID. Default is the current user's affiliate ID.
 *     @type string|false $pretty       Whether to build a pretty referral URL. Accepts 'yes' or 'no'. False
 *                                      disables pretty URLs. Default empty, see affwp_is_pretty_referral_urls().
 *     @type string       $format       Referral format. Accepts 'id' or 'username'. Default empty,
 *                                      see affwp_get_referral_format().
 *     @type string       $base_url     Base URL to use for building a referral URL. If specified, should contain
 *                                      'query' and 'fragment' query vars. 'scheme', 'host', and 'path' query vars
 *                                      can also be passed as part of the base URL. Default empty.
 * }
 * @return string Trailing-slashed value of home_url() when `$args` is empty, built referral URL otherwise.
 */
function affwp_get_affiliate_referral_url( $args = array() ) {

	$defaults = array(
		'pretty' => '',
		'format' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( isset( $args['affiliate_id'] ) ) {
		$affiliate = affwp_get_affiliate( $args['affiliate_id'] );
	} else {
		$affiliate = affwp_get_affiliate();
	}

	$affiliate_id = $affiliate ? $affiliate->ID : 0;

	// get format, username or id
	$format = isset( $args['format'] ) ? $args['format'] : affwp_get_referral_format();

	// pretty URLs
	if ( ! empty( $args['pretty'] ) && 'yes' == $args['pretty'] ) {
		// pretty URLS explicitly turned on
		$pretty = true;
	} elseif ( ( ! empty( $args['pretty'] ) && 'no' == $args['pretty'] ) || false === $args['pretty'] ) {
		// pretty URLS explicitly turned off
		$pretty = false;
	} else {
		// pretty URLs set from admin
		$pretty = affwp_is_pretty_referral_urls();
	}

	// get base URL
	if ( isset( $args['base_url'] ) ) {
		$base_url = $args['base_url'];
	} else {
		$base_url = affwp_get_affiliate_base_url();
	}

	// add trailing slash only if no query string exists and there's no fragment identifier
	if ( isset( $args['base_url'] ) && ! array_key_exists( 'query', parse_url( $base_url ) ) && ! array_key_exists( 'fragment', parse_url( $base_url ) ) ) {
		$base_url = trailingslashit( $args['base_url'] );
	}

	// the format value, either affiliate's ID or username
	$format_value = affwp_get_referral_format_value( $format, $affiliate_id );

	$url_parts = parse_url( $base_url );

	// if fragment identifier exists in base URL, strip it and store in variable so we can append it later
	$fragment        = array_key_exists( 'fragment', $url_parts ) ? '#' . $url_parts['fragment'] : '';

	// if query exists in base URL, strip it and store in variable so we can append to the end of the URL
	$query_string    = array_key_exists( 'query', $url_parts ) ? '?' . $url_parts['query'] : '';

	$url_scheme      = isset( $url_parts['scheme'] ) ? $url_parts['scheme'] : 'http';
	$url_host        = isset( $url_parts['host'] ) ? $url_parts['host'] : '';
	$url_path        = isset( $url_parts['path'] ) ? $url_parts['path'] : '';
	$constructed_url = $url_scheme . '://' . $url_host . $url_path;
	$base_url        = $constructed_url;

	// set up URLs
	$pretty_urls     = trailingslashit( $base_url ) . trailingslashit( affiliate_wp()->tracking->get_referral_var() ) . trailingslashit( $format_value ) . $query_string . $fragment;
	$non_pretty_urls = esc_url( add_query_arg( affiliate_wp()->tracking->get_referral_var(), $format_value, $base_url . $query_string . $fragment ) );

	if ( $pretty ) {
		$referral_url = $pretty_urls;
	} else {
		$referral_url = $non_pretty_urls;
	}

	return $referral_url;

}

/**
 * Retrieves the base URL that is then displayed in the Page URL input field of the affiliate area.
 *
 * @since 1.6
 *
 * @return string Base URL.
 */
function affwp_get_affiliate_base_url() {

	if ( isset( $_GET['url'] ) && ! empty( $_GET['url'] ) ) {
		$base_url = urldecode( $_GET['url'] );
	} else {
		$base_url = home_url( '/' );
	}

	$default_referral_url = affiliate_wp()->settings->get( 'default_referral_url' );

	if ( $default_referral_url ) {
		$base_url = $default_referral_url;
	}

	/**
	 * Filters the referral base URL.
	 *
	 * @since 1.6
	 *
	 * @param string Base URL.
	 */
	return apply_filters( 'affwp_affiliate_referral_url_base', $base_url );

}

/**
 * Retrieves the page ID for the Affiliate Area page.
 *
 * @since 1.8
 *
 * @return int Affiliate Area page ID.
 */
function affwp_get_affiliate_area_page_id() {
	$affiliate_page_id = affiliate_wp()->settings->get( 'affiliates_page' );

	/**
	 * Filters the Affiliate Area page ID.
	 *
	 * @since 1.8
	 *
	 * @param int $affiliate_page_id Affiliate Area page ID.
	 */
	return apply_filters( 'affwp_affiliate_area_page_id', $affiliate_page_id );
}

/**
 * Retrieves the Affiliates Area page URL.
 *
 * @since 1.8
 *
 * @param string $tab Optional. Tab ID. Default empty.
 * @return string If `$tab` is specified and valid, the URL for the given tab within the Affiliate Area page.
 *                Otherwise the Affiliate Area page URL.
 */
function affwp_get_affiliate_area_page_url( $tab = '' ) {
	$affiliate_area_page_id = affwp_get_affiliate_area_page_id();

	$affiliate_area_page_url = get_permalink( $affiliate_area_page_id );

	if ( ! empty( $tab ) && array_key_exists( $tab, affwp_get_affiliate_area_tabs() ) ) {
		$affiliate_area_page_url = add_query_arg( array( 'tab' => $tab ), $affiliate_area_page_url );
	}

	/**
	 * Filters the Affiliate Area page URL.
	 *
	 * @since 1.8
	 *
	 * @param string $affiliate_area_page_url Page URL.
	 * @param int    $affiliate_area_page_id  Page ID.
	 * @param string $tab                     Page tab (if specified).
	 */
	return apply_filters( 'affwp_affiliate_area_page_url', $affiliate_area_page_url, $affiliate_area_page_id, $tab );
}

/**
 * Retrieves an array of tabs for the affiliate area
 *
 * @since 2.1.7
 *
 * @return array $tabs Array of tabs.
 */
function affwp_get_affiliate_area_tabs() {

	/**
	 * Filters the Affiliate Area tabs list.
	 *
	 * @since 2.1.7
	 *
	 * @param array $tabs Array of tabs.
	 */
	$tabs = apply_filters( 'affwp_affiliate_area_tabs',
		array(
			'urls'      => __( 'Affiliate URLs', 'affiliate-wp' ),
			'stats'     => __( 'Statistics', 'affiliate-wp' ),
			'graphs'    => __( 'Graphs', 'affiliate-wp' ),
			'referrals' => __( 'Referrals', 'affiliate-wp' ),
			'payouts'   => __( 'Payouts', 'affiliate-wp' ),
			'visits'    => __( 'Visits', 'affiliate-wp' ),
			'creatives' => __( 'Creatives', 'affiliate-wp' ),
			'settings'  => __( 'Settings', 'affiliate-wp' ),
		)
	);

	return $tabs;
}

/**
 * Retrieves the active Affiliate Area tab slug.
 *
 * @since 1.8.1
 *
 * @return string Active tab if valid, empty string otherwise.
 */
function affwp_get_active_affiliate_area_tab() {
	$active_tab = ! empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';
	$tabs = affwp_get_affiliate_area_tabs();

	foreach ( $tabs as $tab_slug => $tab_title ) {

		// This ensures that tabs registered prior to 2.1.7 (when tab titles were added to the array) continue to function
		if( is_int( $tab_slug ) ) {
			$tabs[ sanitize_key( $tab_title ) ] = $tab_title;
		}

		if ( false === affwp_affiliate_area_show_tab( $tab_slug ) ) {
			unset( $tabs[ $tab_slug ] );
		}
	}

	if ( $active_tab && array_key_exists( $active_tab, $tabs ) ) {
		$active_tab = $active_tab;
	} elseif ( ! empty( $tabs ) ) {
		$active_tab = reset( $tabs );
		$active_tab = key( $tabs );
	} else {
		$active_tab = '';
	}

	return $active_tab;
}

/**
 * Show a tab in the Affiliate Area
 *
 * @since  1.8
 * @return boolean
 */
function affwp_affiliate_area_show_tab( $tab = '' ) {
	return apply_filters( 'affwp_affiliate_area_show_tab', true, $tab );
}

/**
 * Render a specified tab of the affiliate area
 *
 * @since  2.1.7
 * @return void
 */
function affwp_render_affiliate_dashboard_tab( $tab = '' ) {

	ob_start();
	affiliate_wp()->templates->get_template_part( 'dashboard-tab', $tab );
	$content = ob_get_clean();

	$content = apply_filters( 'affwp_render_affiliate_dashboard_tab_' . $tab, $content, $tab );
	echo apply_filters( 'affwp_render_affiliate_dashboard_tab', $content, $tab );

}

/**
 * Retrieves an array of payouts for the given affiliate.
 *
 * @since 1.9
 *
 * @param int|\AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @return array|false Array of payout objects for the given affiliate, otherwise false.
 */
function affwp_get_affiliate_payouts( $affiliate = 0 ) {
	if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
		return false;
	}

	$payouts = affiliate_wp()->affiliates->payouts->get_payouts( array(
		'affilate_id' => $affiliate->ID,
	) );

	/**
	 * Filters the list of payouts associated with an affiliate.
	 *
	 * @since 1.9
	 *
	 * @param array $payouts      The affiliate's payouts.
	 * @param int   $affiliate_id Affiliate ID.
	 */
	return apply_filters( 'affwp_get_affiliate_payouts', $payouts, $affiliate->ID );
}
