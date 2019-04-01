<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function affiliatewp_mlm_install() {

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;

	if( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
	
		// Set the global table name
		$affwp_mlm_table_name = 'affiliate_wp_mlm_connections';
	
	} else{

		// Set the single site table name
		$affwp_mlm_table_name = $wpdb->prefix . 'affiliate_wp_mlm_connections';
		
	}

	// Ad table sql
	$affwp_mlm_sql = "CREATE TABLE $affwp_mlm_table_name (
		affiliate_id bigint(20) NOT NULL,
		affiliate_parent_id bigint(20) NOT NULL,
		direct_affiliate_id bigint(20) NOT NULL,
		matrix_level bigint(20) NOT NULL,
		PRIMARY KEY  (affiliate_id),
		KEY direct_affiliate_id (direct_affiliate_id),
		KEY matrix_level (matrix_level)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

	// Run the query
	dbDelta( $affwp_mlm_sql );

}