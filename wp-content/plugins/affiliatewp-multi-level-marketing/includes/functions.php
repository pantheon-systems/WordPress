<?php

/**
 * Check whether the current affiliate is a sub-affiliate
 *
 * @since  1.0
 * @return boolean
 */
function affwp_mlm_is_sub_affiliate( $affiliate_id = 0 ) {
	
	if ( empty( $affiliate_id ) ) {
		$affiliate_id = affwp_get_affiliate_id();
	}

	if ( affwp_mlm_get_affiliate_connections( absint( $affiliate_id ) ) ) {
		return true;
	}

	return false;

}

/**
 * Check whether the current affiliate is a parent affiliate
 *
 * @since 1.0
 * @return boolean
 */
function affwp_mlm_is_parent_affiliate( $affiliate_id = 0 ) {
	
	if ( empty( $affiliate_id ) ) {
		$affiliate_id = affwp_get_affiliate_id();
	}

	// Parent affiliates must have sub-affiliates
	if ( affwp_mlm_get_sub_affiliates( absint( $affiliate_id ) ) ) {
		return true;
	}

	return false;
}

/**
 * Check whether the current affiliate can have a new sub affiliate
 *
 * @since 1.0
 * @return boolean
 */
function affwp_mlm_sub_affiliate_allowed( $affiliate_id = 0 ) {

	if ( ! $affiliate_id ) return false;
	
	$allowed = false;
	
	// Make sure affiliate is active
	if ( 'active' !== affwp_get_affiliate_status( $affiliate_id ) ) {
		$allowed = false;
	}
	
	$matrix_depth = affiliate_wp()->settings->get( 'affwp_mlm_matrix_depth' );
	
	// Check if total depth is enabled (Unilevel)
	if ( affiliate_wp()->settings->get( 'affwp_mlm_total_depth' ) ) {
			
		$matrix_level = affwp_mlm_get_matrix_level( $affiliate_id );
		$sub_level = $matrix_level + 1;
			
		// Check if matrix depth limit has been reached
		if ( $sub_level > $matrix_depth ) {
			 $allowed = false;
		}
		
	}
	
	// Check if forced matrix is enabled (Forced Matrix)
	if ( affiliate_wp()->settings->get( 'affwp_mlm_forced_matrix' ) ) {

		$matrix_width = affiliate_wp()->settings->get( 'affwp_mlm_matrix_width' );
		$matrix_width = ! empty( $matrix_width ) ? $matrix_width : 1;
		$extra_width = affiliate_wp()->settings->get( 'affwp_mlm_matrix_width_extra' );
		$cycles = affiliate_wp()->settings->get( 'affwp_mlm_matrix_cycles' );
		$cycles = ! empty( $cycles ) ? $cycles : 1;
		$level_1_count = count( affwp_mlm_get_sub_affiliates( absint( $affiliate_id ) ) );	
		
		// Allow if 1st level isn't full
		if ( $level_1_count < $matrix_width ) {
			$allowed = true;
		} else {
			
			$downline = affwp_mlm_get_downline( $affiliate_id );

			// Loop through cycles and see if they are complete
			for ( $cycle_count = 1; $cycle_count <= $cycles; $cycle_count++ ) {

				$cycle_max_width = $matrix_width * $cycle_count;
				$cycle_min_width = $cycle_max_width > 1 ? $cycle_max_width - $matrix_width + 1 : 1;

				// See if they've started this cycle
				if ( $level_1_count >= $cycle_min_width ) {				
					
					// See if the 1st level for this cycle is full
					if ( $level_1_count == $cycle_max_width ) {
					
						// Allow if a new cycle is allowed
						if ( affwp_mlm_new_cycle_allowed( $affiliate_id, $downline, $cycle_count ) ) {

							$allowed = true;
							
							do_action( 'affwp_mlm_new_cycle_started', $affiliate_id, $downline, $cycle_count );
							break;
						}
						
					} else {
						
						// Allow a sub affiliate, but not a new cycle
						if ( $level_1_count < $cycle_max_width ) $allowed = true;

					}
					
				} else {
					break;
				}
			}	
		}
	} else {
		$allowed = true;
	}
	
	return apply_filters( 'affwp_mlm_sub_affiliate_allowed', $allowed, $affiliate_id );
}

/**
 * Check whether the current affiliate has completed a level
 *
 * @since 1.1.1
 * @return boolean
 */
function affwp_mlm_is_level_complete( $affiliate_id = 0, $level_subs = array(), $level = 0, $current_cycle = 0 ) {
	
	if ( ! isset( $level_subs ) ) return false;
	
	if ( empty( $level ) ) $level = 1;
	
	if ( empty( $current_cycle ) ) $current_cycle = 1;
	
	$forced_matrix = affiliate_wp()->settings->get( 'affwp_mlm_forced_matrix' );
	$matrix_width = affiliate_wp()->settings->get( 'affwp_mlm_matrix_width' );
	
	if ( ! $forced_matrix || empty( $matrix_width ) ) return false;
	
	$complete = false;
	$level_sub_count = count( $level_subs );
	$max_level_subs = pow( $matrix_width, $level ) * $current_cycle;

	if ( $level_sub_count >= $max_level_subs ) {
		$complete = true;
	}

	return apply_filters( 'affwp_mlm_level_complete', $complete, $affiliate_id, $level_subs, $level, $current_cycle, $max_level_subs );
}

/**
 * Check whether the current affiliate has completed a cycle
 *
 * @since 1.1.1
 * @return boolean
 */
function affwp_mlm_is_cycle_complete( $affiliate_id = 0, $downline = array(), $current_cycle = 0 ) {
	
	if ( empty( $affiliate_id ) || !isset( $downline ) ) return false;
	
	if ( empty( $current_cycle ) ) $current_cycle = 1;
	
	$matrix_width = affiliate_wp()->settings->get( 'affwp_mlm_matrix_width' );
	$matrix_depth = affiliate_wp()->settings->get( 'affwp_mlm_matrix_depth' );
	
	if ( empty( $matrix_width ) || empty( $matrix_depth ) ) return false;
				
	// Remove current affiliate (Level 0)
	if ( isset( $downline[0] ) ) unset( $downline[0] );
	
	$level_count = 0;
	$complete_levels = 0;

	foreach ( $downline as $lvl ) {

		$level_count++;
		$level_subs = $lvl;

		if ( affwp_mlm_is_level_complete( $affiliate_id, $level_subs, $level_count, $current_cycle ) ) {
			$complete_levels++;
		}

	}
	
	// Check if all levels are complete in this cycle
	if ( $complete_levels >= $matrix_depth ) {
		return true;
	}
	
	return false;
}

/**
 * Check whether the current affiliate can start a new cycle
 *
 * @since 1.1.1
 * @return boolean
 */
function affwp_mlm_new_cycle_allowed( $affiliate_id = 0, $downline = array(), $current_cycle = 0 ) {
	
	if ( empty( $affiliate_id ) || !isset( $downline ) ) return false;
	
	$cycles = affiliate_wp()->settings->get( 'affwp_mlm_matrix_cycles' );
	
	if ( empty( $cycles ) ) return false;

	if ( empty( $current_cycle ) ) $current_cycle = 1;
	
	$allowed = false;
	$matrix_width = affiliate_wp()->settings->get( 'affwp_mlm_matrix_width' );
	$matrix_depth = affiliate_wp()->settings->get( 'affwp_mlm_matrix_depth' );
	$level_1_count = count( $downline[1] );

	// Allow new cycle if this cycle is full
	if ( affwp_mlm_is_cycle_complete( $affiliate_id, $downline, $current_cycle ) ) {

		if ( $current_cycle < $cycles ) $allowed = true;

	}

	return apply_filters( 'affwp_mlm_new_cycle_allowed', $allowed, $affiliate_id, $downline, $current_cycle, $cycles );
}

/**
 * Filter an array of Affiliate IDs by each affiliate's level in the matrix
 *
 * @since 1.0
 * @return array
 */
function affwp_mlm_filter_by_level( $affiliate_ids = array(), $levels = 0 ) {

	if ( !empty( $affiliate_ids ) ) {
		
		if ( empty( $levels ) ) {
			
			$matrix_depth = affiliate_wp()->settings->get( 'affwp_mlm_matrix_depth' );
			$levels = $matrix_depth ? $matrix_depth : 15;
			
		}
		
		$level_count = 0;
		
		foreach( $affiliate_ids as $affiliate_id ) {
			
			$level_count++;
			
			if( $level_count > $levels ) {
				break;
			}
			
			$filtered_affiliates[] = $affiliate_id;
		
		}
		
		return $filtered_affiliates;
	
	} else{
		return;
	}
}

/**
 * Filter an array of Affiliate IDs by each affiliate's status
 *
 * @since 1.0.4
 */
function affwp_mlm_filter_by_status( $affiliate_ids = array(), $status = '' ) {
	
	// Stop if the affiliate has no upline
	if ( empty( $affiliate_ids ) ) {
		return $affiliate_ids;
	}

	if ( empty( $status ) ) {
		$status = 'active';
	}
	
	$filtered_affiliates = array();
	
	foreach( $affiliate_ids as $affiliate_id ) {
		
		// Skip affiliates that don't have the given status
		if ( $status != affwp_get_affiliate_status( $affiliate_id ) ) {
			continue;
		}
		
		$filtered_affiliates[] = $affiliate_id;
	
	}
		
		return $filtered_affiliates;
}