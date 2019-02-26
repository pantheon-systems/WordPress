<?php
/**
 * Uninstall Lifetime Commissions
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( function_exists( 'affiliate_wp' ) ) {
	if ( affiliate_wp()->settings->get( 'lifetime_commissions_uninstall_on_delete' ) ) {
		// Remove all meta keys
		delete_metadata( 'user', 0, 'affwp_lc_email', '', true );
		delete_metadata( 'user', 0, 'affwp_lc_customer_email', '', true );
		delete_metadata( 'user', 0, 'affwp_lc_customer_id', '', true );
		delete_metadata( 'user', 0, 'affwp_lc_affiliate_id', '', true );
		delete_metadata( 'user', 0, 'affwp_lc_enabled', '', true );
	}
}
