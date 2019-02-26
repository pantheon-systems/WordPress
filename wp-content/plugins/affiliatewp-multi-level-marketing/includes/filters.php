<?php

/**
 * Remove Indirect Referrals from the Direct Referrals List in the Affiliate Area
 *
 * @since  1.1.2
 */
function affwp_mlm_remove_indirect_referrals_from_referrals_tab ( $referrals ) {
	
	if ( $referrals ) {

		foreach ( $referrals as $referral ) {
			// Remove Indirect Referrals from Direct Referrals Table
			if ( $referral->custom == 'indirect' ) {
				continue;
			}	
		}
	}
}
add_filter( 'affwp_dashboard_referrals', 'affwp_mlm_remove_indirect_referrals_from_referrals_tab', 10, 1 );
