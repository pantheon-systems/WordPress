<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Force the frontend scripts to load on affiliate area shortcode tabs
 * 
 * @since  1.0
 */
if ( !function_exists( 'affwp_aas_force_frontend_scripts' ) && !function_exists( 'affwp_bp_force_frontend_scripts' ) ) {
	function affwp_mlm_force_frontend_scripts( $ret ) {
		global $post;
		
		if ( has_shortcode( $post->post_content, 'affiliate_area_sub_affiliates' ) ) {
			$ret = true;
		}
	}
	add_filter( 'affwp_force_frontend_scripts', 'affwp_mlm_force_frontend_scripts' );
}

/**
* [affiliate_area_sub_affiliates] shortcode
*
* @since  1.0.3
*/
function affwp_aas_affiliate_sub_affiliates_shortcode( $atts, $content = null ) {
	if ( ! ( is_user_logged_in() && affwp_is_affiliate() ) ) {
		return $content;
	}
	ob_start();
	echo '<div id="affwp-affiliate-dashboard">';
	affiliate_wp()->templates->get_template_part( 'dashboard-tab', 'sub-affiliates' );
	echo '</div>';
	$content = ob_get_clean();
	return $content;
}
add_shortcode( 'affiliate_area_sub_affiliates', 'affwp_aas_affiliate_sub_affiliates_shortcode' );

// Run if AffiliateWP Performance Bonuses is Active
if ( class_exists( 'AffiliateWP_Performance_Bonuses' ) ) {

	/**
	 * Adds Sub Affiliates to the Bonus Types List
	 *
	 * @since 1.0.4
	 * @return array
	 */
	function affwp_pb_add_sub_affiliates_type( $types = array() ) {
		
		$mlm_type = array(
			'sub_affiliate'  => __( 'Sub Affiliate', 'affiliatewp-performance-bonuses' )
		);
		$types = array_merge( $types, $mlm_type );
		
		return $types;
	}
	add_filter( 'affwp_pb_get_bonus_types', 'affwp_pb_add_sub_affiliates_type', 10, 1 );

	
	/**
	 * Adds Cycle Complete to the Bonus Types List
	 *
	 * @since 1.1.1
	 * @return array
	 */
	function affwp_pb_add_cycle_complete_type( $types = array() ) {
		
		$mlm_type = array(
			'cycle_complete'  => __( 'Cycle Complete', 'affiliatewp-performance-bonuses' )
		);
		$types = array_merge( $types, $mlm_type );
		
		return $types;
	}
	add_filter( 'affwp_pb_get_bonus_types', 'affwp_pb_add_cycle_complete_type', 10, 1 );
		
	/**
	 * Check to see if bonus requirements have been met when changing an affiliate's status
	 *
	 * @since 1.0
	 * @return array
	 */
	function affwp_pb_bonus_check_on_affiliate_status_change( $affiliate_id, $new_status, $old_status ) {
	
		if( 'active' == $new_status ) {
		
			$direct_affiliate_id = affwp_mlm_get_direct_affiliate( $affiliate_id );
			$mlm_data = array( 'direct_affiliate_id' => $direct_affiliate_id );
		
			affwp_pb_check_for_sub_affiliate_bonus( $affiliate_id, $mlm_data );
			
			if ( class_exists( 'AffiliateWP_Ranks' ) )
				affwp_ranks_check_for_sub_affiliate_rank( $affiliate_id, $mlm_data );
			
			do_action( 'affwp_pb_bonus_check_on_active_affiliate', $affiliate_id, $mlm_data, $new_status, $old_status, $direct_affiliate_id );
			
		} elseif ( 'inactive' == $new_status && 'active' == $old_status ) {
		
			// TODO - Subtract unpaid unqualified bonus
			
			do_action( 'affwp_pb_bonus_check_on_inactive_affiliate', $affiliate_id, $mlm_data, $new_status, $old_status, $direct_affiliate_id );
			
		} 

	}
	add_action( 'affwp_set_affiliate_status', 'affwp_pb_bonus_check_on_affiliate_status_change', 10, 3 );
	
	/**
	 * Check for bonuses based on sub affiliate signups
	 * 
	 * @since 1.0.4
	 */
	function affwp_pb_check_for_sub_affiliate_bonus( $affiliate_id, $mlm_data ) {
		
		// Use the direct referring affiliate
		$direct_affiliate_id = $mlm_data['direct_affiliate_id'];
	
		// Stop if the new affiliate was not directly referred
		if ( empty( $direct_affiliate_id ) ) return;
		
		// Exclude bonuses that have already been earned	
		$bonuses = apply_filters( 'affwp_pb_get_active_bonuses', get_active_bonuses(), $direct_affiliate_id );
		
		// Stop if all bonuses have been earned already
		if ( empty( $bonuses ) ) return;
		
		foreach( $bonuses as $key => $bonus ) {
			
			// Check for sub affiliate bonus
			if ( $bonus['type'] == 'sub_affiliate' ) {
				
				$sub_affiliates = affwp_mlm_filter_by_status( affwp_mlm_get_direct_sub_affiliates( $direct_affiliate_id ) );
				$sub_affiliate_count = count( $sub_affiliates );
			
				// Check if the affiliate has met the requirements
				if ( $sub_affiliate_count >= $bonus['requirement'] ) {
				
					$bonus_earned = affwp_pb_get_bonus_log( $direct_affiliate_id, $bonus['pre_bonus'] );
					
					// Check for prerequisite bonus
					if ( !empty( $bonus['pre_bonus'] ) && empty( $bonus_earned ) ) return;
				
					// Create the bonus
					affwp_pb_create_bonus_referral( $direct_affiliate_id, $bonus['id'], $bonus['title'], $bonus['amount'] );
				
				}
			}
		}
	}
	add_action( 'affwp_mlm_affiliates_connected', 'affwp_pb_check_for_sub_affiliate_bonus', 10, 2 );
	
	/**
	 * Check for bonuses based on completed cycles
	 * 
	 * @since 1.1.1
	 */
	function affwp_pb_check_for_cycle_complete_bonus( $affiliate_id, $new_cycles, $old_cycles ) {
		
		// Exclude bonuses that have already been earned	
		$bonuses = apply_filters( 'affwp_pb_get_active_bonuses', get_active_bonuses(), $affiliate_id );
		
		// Stop if all bonuses have been earned already
		if ( empty( $bonuses ) ) return;
		
		foreach( $bonuses as $key => $bonus ) {
			
			// Check for cycle complete bonus
			if ( $bonus['type'] == 'cycle_complete' ) {
			
				// Check if the affiliate has met the requirements
				if ( $new_cycles >= $bonus['requirement'] ) {
				
					$bonus_earned = affwp_pb_get_bonus_log( $affiliate_id, $bonus['pre_bonus'] );
					
					// Check for prerequisite bonus
					if ( !empty( $bonus['pre_bonus'] ) && empty( $bonus_earned ) ) return;
				
					// Create the bonus
					affwp_pb_create_bonus_referral( $affiliate_id, $bonus['id'], $bonus['title'], $bonus['amount'] );
				
				}
			}
		}
	}
	add_action( 'affwp_mlm_post_set_complete_cycles', 'affwp_pb_check_for_cycle_complete_bonus', 10, 3 );
	
}

// Run if AffiliateWP Ranks is Active
if ( class_exists( 'AffiliateWP_Ranks' ) ) {
	
	// Run if AffiliateWP Performance Bonuses is Active
	if ( class_exists( 'AffiliateWP_Performance_Bonuses' ) ) {
		
		/**
		 * Check for ranks based on sub affiliate signups
		 * 
		 * @since 1.0.6
		 */
		function affwp_ranks_check_for_sub_affiliate_rank( $affiliate_id, $mlm_data ) {
			
			// Use the direct referring affiliate
			$direct_affiliate_id = $mlm_data['direct_affiliate_id'];
		
			// Stop if the new affiliate was not directly referred
			if ( empty( $direct_affiliate_id ) ) return;
			
			$ranks = get_ranks();
			
			// Make sure there are ranks setup 
			if ( empty( $ranks ) ) return;
			
			foreach( $ranks as $key => $rank ) {
				
				// Skip this rank if it's the affiliate's current rank
				if ( affwp_ranks_has_rank( $affiliate_id, $rank['id'] ) ) continue;
				
				// Check for sub affiliate based rank
				if ( $rank['type'] == 'sub_affiliate' ) {
					
					$sub_affiliates = affwp_mlm_filter_by_status( affwp_mlm_get_direct_sub_affiliates( $direct_affiliate_id ) );
					$sub_affiliate_count = count( $sub_affiliates );
	
					// Check if the affiliate has met the requirements
					if ( $sub_affiliate_count >= $rank['requirement'] ) {
																						
						$rank_id = $rank['id'];
					
						// Set the affiliate's rank
						affwp_ranks_set_affiliate_rank( $direct_affiliate_id, $rank_id );

					}
				}
			}
		}
		add_action( 'affwp_mlm_affiliates_connected', 'affwp_ranks_check_for_sub_affiliate_rank', 10, 2 );
		
		/**
		 * Check for ranks based on completed cycles
		 * 
		 * @since 1.1.1
		 */
		function affwp_ranks_check_for_cycle_complete_rank( $affiliate_id, $new_cycles, $old_cycles ) {

			$ranks = get_ranks();
			
			// Make sure there are ranks setup 
			if ( empty( $ranks ) ) return;
			
			foreach( $ranks as $key => $rank ) {
				
				// Skip this rank if it's the affiliate's current rank
				if ( affwp_ranks_has_rank( $affiliate_id, $rank['id'] ) ) continue;
				
				// Check for cycle based rank
				if ( $rank['type'] == 'cycle_complete' ) {
	
					// Check if the affiliate has met the requirements
					if ( $new_cycles >= $rank['requirement'] ) {
																						
						$rank_id = $rank['id'];
					
						// Set the affiliate's rank
						affwp_ranks_set_affiliate_rank( $affiliate_id, $rank_id );

					}
				}
			}
		}
		add_action( 'affwp_mlm_post_set_complete_cycles', 'affwp_ranks_check_for_cycle_complete_rank', 10, 3 );		
		
		
	}
}


// Run if Lifetime Commissions is Active
//if ( class_exists( 'Affiliate_WP_Lifetime_Commissions' ) ) {
	
	/**
	 * Set the Direct or Parent Affiliate as the Lifetime Affiliate
	 * 
	 * @since 1.1.1
	 */
	function affwp_mlm_sync_lifetime_affiliate( $affiliate_id, $mlm_data ) {

		$sync = affiliate_wp()->settings->get( 'affwp_mlm_lc_sync_lifetime_affiliate' );
		
		// Make sure syncing is enabled in the settings
		if ( empty( $sync ) ) return;	

		// Use the direct referring affiliate
		if ( $sync == 'direct' ) $sync_affiliate_id = $mlm_data['direct_affiliate_id'];
		
		// Use the parent affiliate
		if ( $sync == 'parent' ) $sync_affiliate_id = $mlm_data['parent_affiliate_id'];

		$user_id = affwp_get_affiliate_user_id( $affiliate_id );
		
		update_user_meta( $user_id, 'affwp_lc_affiliate_id', $sync_affiliate_id );
		
	}
	add_action( 'affwp_mlm_affiliates_connected', 'affwp_mlm_sync_lifetime_affiliate', 10, 2 );
//}
