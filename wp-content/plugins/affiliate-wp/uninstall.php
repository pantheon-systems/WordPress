<?php
/**
 * Uninstall AffiliateWP
 *
 * @package     AffiliateWP
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load AffiliateWP file
include_once( 'affiliate-wp.php' );

global $wp_roles;

$affiliate_wp_settings = new Affiliate_WP_Settings;

if( $affiliate_wp_settings->get( 'uninstall_on_delete' ) ) {

	// Remove the affiliate area page
	wp_delete_post( $affiliate_wp_settings->get( 'affiliates_page' ) );

	// Remove all capabilities and roles
	$caps = new Affiliate_WP_Capabilities;
	$caps->remove_caps();

	if ( is_multisite() ) {

		if ( true === version_compare( $GLOBALS['wp_version'], '4.6', '<' ) ) {
			$sites = wp_list_pluck( 'blog_id', wp_get_sites() );
		} else {
			$sites = get_sites( array( 'fields' => 'ids' ) );
		}

		// Remove all database tables
		foreach ( $sites as $site_id ) {

			switch_to_blog( $site_id );

			affiliate_wp_uninstall_tables();

			restore_current_blog();

		}

	} else {

		affiliate_wp_uninstall_tables();

	}
}

/**
 * Uninstalls all database tables created by AffiliateWP.
 *
 * @since 2.1.1
 *
 * @global \wpdb $wpdb WordPress database abstraction layer.
 */
function affiliate_wp_uninstall_tables() {
	global $wpdb;

	$db_segments = array(
		'affiliate_wp_affiliates',
		'affiliate_wp_affiliatemeta',
		'affiliate_wp_campaigns',
		'affiliate_wp_creatives',
		'affiliate_wp_payouts',
		'affiliate_wp_referrals',
		'affiliate_wp_rest_consumers',
		'affiliate_wp_visits'
	);

	// Remove all affwp_ options.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'affwp\_%';" );

	foreach ( $db_segments as $segment ) {
		// Table.
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . $segment );

		// Options.
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%$segment%'" );
	}

	$wpdb->query( "DROP VIEW " . $wpdb->prefix . "affiliate_wp_campaigns" );

}
