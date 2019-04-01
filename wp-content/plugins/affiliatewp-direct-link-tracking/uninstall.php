<?php
/**
 * Uninstall Direct Link Tracking
 *
 * @since 1.1
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

if ( function_exists( 'affiliate_wp' ) ) {
	if ( affiliate_wp()->settings->get( 'direct_link_tracking_uninstall' ) ) {

		// Remove Direct Links database table.
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "affiliate_wp_direct_links" );

		// Remove Affiliate Meta.
		$wpdb->delete( $wpdb->prefix . 'affiliate_wp_affiliatemeta', array( 'meta_key' => 'direct_link_tracking_url_limit' ) );
		$wpdb->delete( $wpdb->prefix . 'affiliate_wp_affiliatemeta', array( 'meta_key' => 'direct_link_tracking_enabled' ) );

	}
}
