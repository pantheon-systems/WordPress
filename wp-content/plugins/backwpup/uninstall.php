<?php
//if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}

global $wpdb;
/* @var wpdb $wpdb */

//only uninstall if no BackWPup Version active
if ( ! class_exists( 'BackWPup' ) ) {

	//delete plugin options
	if ( is_multisite() )
		$wpdb->query( "DELETE FROM " . $wpdb->sitemeta . " WHERE meta_key LIKE '%backwpup_%' " );
	else
		$wpdb->query( "DELETE FROM " . $wpdb->options . " WHERE option_name LIKE '%backwpup_%' " );
}
