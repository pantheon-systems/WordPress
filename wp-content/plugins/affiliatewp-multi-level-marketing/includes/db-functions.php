<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the downline of an affiliate
 *
 * @since 1.0
 */
function affwp_mlm_get_connections_table() {

	global $wpdb;

	if( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
	
		// Set the global table name
		$affwp_mlm_table_name = 'affiliate_wp_mlm_connections';
	
	} else{

		// Set the single site table name
		$affwp_mlm_table_name = $wpdb->prefix . 'affiliate_wp_mlm_connections';
		
	}

	return $affwp_mlm_table_name;

}

/**
 * Get an affiliate's sub-affiliates
 *
 * @since 1.0
 * @return object
 */
function affwp_mlm_get_sub_affiliates( $affiliate_id ) {

	if ( empty( $affiliate_id ) ) return;

	global $wpdb;

	$affiliate_connection_table = affwp_mlm_get_connections_table();

	// Get sub affiliates for several parents
	if ( is_array( $affiliate_id ) ) {
		
		$affiliate_ids = implode( ',', array_map( 'intval', $affiliate_id ) );
		
		$sub_affiliates = $wpdb->get_results(
				"
				SELECT		*
				FROM		$affiliate_connection_table
				WHERE		affiliate_parent_id 
				IN			( {$affiliate_ids} )
				ORDER BY    affiliate_id ASC
				"
		);
		
	} else {

		// Get sub affiliates for 1 parent
		$sub_affiliates = $wpdb->get_results( $wpdb->prepare(
				"
				SELECT     *
				FROM       $affiliate_connection_table
				WHERE      affiliate_parent_id = %d
				",
				$affiliate_id
			)
		);
	}

	if ( null === $sub_affiliates ) {
		return;
	}

	return $sub_affiliates;

}

/**
 * Get an affiliate's directly referred sub-affiliates
 *
 * @since 1.0.4
 * @return object
 */
function affwp_mlm_get_direct_sub_affiliates( $affiliate_id ) {

	global $wpdb;

	$affiliate_connection_table = affwp_mlm_get_connections_table();

	// Get sub affiliates for several parents
	if ( is_array( $affiliate_id ) ) {
		
		$affiliate_ids = implode( ',', array_map( 'intval', $affiliate_id ) );
		
		$sub_affiliates = $wpdb->get_results(
				"
				SELECT		*
				FROM		$affiliate_connection_table
				WHERE		direct_affiliate_id 
				IN			( {$affiliate_ids} )
				ORDER BY    affiliate_id ASC
				"
		);
		
	} else {
		
		$sub_affiliates = $wpdb->get_results( $wpdb->prepare(
				"
				SELECT     *
				FROM       $affiliate_connection_table
				WHERE      direct_affiliate_id = %d
				",
				$affiliate_id
			)
		);
	}
	
	if ( null ===  $sub_affiliates ) {
		return;
	}

	return $sub_affiliates;

}

/**
 * Get affiliate connections data
 * 
 * @since 1.0
 * @return object
 */
function affwp_mlm_get_affiliate_connections( $affiliate_id = 0 ) {

	global $wpdb;

	$affiliate_connection_table = affwp_mlm_get_connections_table();

	$affiliate_data = $wpdb->get_row( $wpdb->prepare(
			"
			SELECT     *
			FROM       $affiliate_connection_table
			WHERE      affiliate_id = %d
			",
			$affiliate_id
		)
	);

	// Return false if nothing returned
	if ( null ===  $affiliate_data ) {
		return false;
	}

	return $affiliate_data;

}

/**
 * Get the parent affiliate of a given affiliate
 *
 * @since 1.0.4
 */
function affwp_mlm_get_parent_affiliate( $affiliate_id = 0 ) {

	// Get the affiliate's direct affiliate
	$affiliate_connections = affwp_mlm_get_affiliate_connections( $affiliate_id );
	$parent_affiliate_id = $affiliate_connections ? $affiliate_connections->affiliate_parent_id : '';
	
	return $parent_affiliate_id;
}

/**
 * Get the direct referring affiliate of a given affiliate
 *
 * @since 1.0.4
 */
function affwp_mlm_get_direct_affiliate( $affiliate_id = 0 ) {

	// Get the affiliate's direct affiliate
	$affiliate_connections = affwp_mlm_get_affiliate_connections( $affiliate_id );
	$direct_affiliate_id = $affiliate_connections ? $affiliate_connections->direct_affiliate_id : '';
	
	return $direct_affiliate_id;
}

/**
 * Get the given affiliate's level in the matrix
 *
 * @since 1.0.5
 */
function affwp_mlm_get_matrix_level( $affiliate_id = 0 ) {

	// Get the affiliate's direct affiliate
	$affiliate_connections = affwp_mlm_get_affiliate_connections( $affiliate_id );
	$matrix_level = $affiliate_connections ? $affiliate_connections->matrix_level : '';
	
	return $matrix_level;
}

/**
 * Returns the number of completed cycles for a given affiliate
 *
 * If no affiliate ID is given, it will check the currently logged in affiliate
 *
 * @since 1.1.1
 * @return int
 */
function affwp_mlm_get_complete_cycles( $affiliate_id = 0 ) {
	
	if ( empty( $affiliate_id ) ) {
		$affiliate_id = affwp_get_affiliate_id();
	}
	
	$affiliate = affwp_get_affiliate( $affiliate_id );
	
	if ( ! $affiliate ) return;
	
	// Returns an array
	$complete_cycles = affwp_get_affiliate_meta( $affiliate_id, 'complete_cycles' );
	
	$cycles = (int)$complete_cycles[0] ? (int)$complete_cycles[0] : 0;
	
	return $cycles;

}

/**
 * Get an affiliate's spillover affiliates
 *
 * @since 1.1
 * @return array
 */
function affwp_mlm_get_spillover_affiliates( $affiliate_id = 0 ) {
	
	$sub_affiliates = affwp_mlm_get_direct_sub_affiliates( $affiliate_id );
	$sub_ids = wp_list_pluck( $sub_affiliates, 'affiliate_id' );
	$spillover_ids = array();
	
	foreach ( $sub_ids as $sub_id ) {
		
		$parent_id = affwp_mlm_get_parent_affiliate( $sub_id );
		
		// Check for spillover affiliate
		if ( $parent_id != $affiliate_id ) {
			
			$spillover_ids[] = $sub_id;
			
		}
	}
	
	return $spillover_ids;	
}

/**
 * Get the upline of an affiliate
 *
 * @since 1.0
 */
function affwp_mlm_get_upline( $affiliate_id = 0, $max = 0, $basis = '' ) {

	// Stop no affiliate is given
	if ( empty( $affiliate_id ) ) return $upline;
	
	$matrix_depth = affiliate_wp()->settings->get( 'affwp_mlm_matrix_depth' );
	$upline = array();
	
	// Get 15 levels if no max or matrix depth is set
	if ( empty( $max ) )
		$max = !empty( $matrix_depth ) ? $matrix_depth : apply_filters( 'affwp_mlm_upline_level_max', 15, $affiliate_id, $upline );
	
	$max--; // Offset the max value to return the correct amount of levels
	
	if ( empty( $basis ) )
		$basis = 'parent';
	
	if ( affwp_mlm_is_sub_affiliate( $affiliate_id ) ) {
		
		$upline[0] = ( $basis == 'direct' ) ? affwp_mlm_get_direct_affiliate( $affiliate_id ) : affwp_mlm_get_parent_affiliate( $affiliate_id );
		
		// Loop through levels and add parents
		for ( $level = 1; $level <= $max; $level++ ) {
			
			$sub_level = $level - 1;			
			$parent_id = $upline[$sub_level];
			$parent_id = ( $basis == 'direct' ) ? affwp_mlm_get_direct_affiliate( $parent_id ) : affwp_mlm_get_parent_affiliate( $parent_id );
			
			if ( empty( $parent_id ) ) {
				break;
			} else{
				$upline[$level] = $parent_id;
			}	
		}
	}

	return $upline;
}

/**
 * Get the downline of an affiliate
 *
 * @since 1.1
 */
function affwp_mlm_get_downline( $affiliate_id = 0, $max_depth = 0 ) {
	
	if ( empty( $affiliate_id ) ) return;
	
	$matrix_depth = affiliate_wp()->settings->get( 'affwp_mlm_matrix_depth' );
	$downline = array();
	
	// Get 15 levels if no max or matrix depth is set
	if ( empty( $max_depth ) )
		$max_depth = !empty( $matrix_depth ) ? $matrix_depth : 15;
	
	if ( affwp_mlm_is_parent_affiliate( $affiliate_id ) ) {
		
		$downline[0] = array( $affiliate_id );
		
		// Loop through levels and add sub affiliates
		for ( $level = 1; $level <= $max_depth; $level++ ) {
			
			$parent_level = $level - 1;			
			$parent_ids = $downline[$parent_level];
			$sub_ids = affwp_mlm_get_sub_affiliates( $parent_ids );
			$sub_ids = wp_list_pluck( $sub_ids, 'affiliate_id' );
			
			if ( empty( $sub_ids ) ) {
				break;
			} else{
				$downline[$level] = $sub_ids;
			}	
		}
	}

	return apply_filters( 'affwp_mlm_downline', $downline, $affiliate_id, $max_depth );
}

/**
 * Get the downline of an affiliate (In a one-dimensional array)
 *
 * @since 1.1
 */
function affwp_mlm_get_downline_array( $affiliate_id = 0, $max_depth = 0 ) {
	
	if ( empty( $affiliate_id ) ) return;
	
	$downline = array();
	$downline_lvls = affwp_mlm_get_downline( $affiliate_id, $max_depth );
	
	foreach ( $downline_lvls as $lvl ) {
	
		foreach ( $lvl as $sub_id ) {
		
			$downline[] = $sub_id;
		
		}
	
	}

	return apply_filters( 'affwp_mlm_downline_array', $downline, $affiliate_id, $max_depth );
}

/**
 * Find an affiliate that can have a new sub affiliate
 *
 * @since 1.0
 */
function affwp_mlm_find_open_affiliate( $affiliate_id = 0 ) {

	if ( empty( $affiliate_id ) ) return;

	$downline = affwp_mlm_get_downline( $affiliate_id );
	$level_count = 0;
	
	foreach ( $downline as $lvl ) {
		
		$level_count++;

		foreach( $lvl as $sub_id ) {

			// Check for open and active sub affiliates
			if ( affwp_mlm_sub_affiliate_allowed( $sub_id ) ) {
				
				return $sub_id;
				break;
			}
		}
	}
}

/**
 * Get referrals for order
 *
 * @since 1.0
 */
function affwp_mlm_get_referrals_for_order( $reference, $context ) {

	global $wpdb;

	if( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
	
		$referral_table = 'affiliate_wp_referrals';
	
	} else{

		$referral_table = $wpdb->prefix . 'affiliate_wp_referrals';
		
	}

	$referrals = $wpdb->get_results( $wpdb->prepare(
		"
		SELECT *
		FROM {$referral_table}
		WHERE reference = %d
		AND context = %s
		",
	$reference,
	$context
	) );

	return $referrals;

}

/**
 * Get referrals by type
 *
 * @since  1.0
 */
function affwp_mlm_get_referrals_by_type( $args = array(), $referral_type = '' ) {

	$defaults = array(
		'number'       => -1,
		'offset'       => 0,
		'referrals_id' => 0,
		'affiliate_id' => 0,
		'context'      => '',
		'status'       => array( 'paid', 'unpaid', 'rejected' )
	);

	$args  = wp_parse_args( $args, $defaults );

	// get the affiliate's referrals
	$referrals = affiliate_wp()->referrals->get_referrals(
		array(
			'number'       => $args['number'],
			'offset'       => $args['offset'],
			'referrals_id' => $args['referrals_id'],
			'affiliate_id' => $args['affiliate_id'],
			'context'      => $args['context'],
			'status'       => $args['status']
		)
	);

	// Only show referrals by type
	if ( $referrals ) {
		foreach ( $referrals as $key => $referral ) {
		
			$sub_affiliate_order = $referral->custom == $referral_type ? $referral->custom : '';

			if ( ! $sub_affiliate_order ) {
				unset( $referrals[$key] );
				// unset( $referrals );
			}

		}

		return $referrals;
	}

}