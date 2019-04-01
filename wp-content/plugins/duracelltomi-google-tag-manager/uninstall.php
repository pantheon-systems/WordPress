<?php
// if uninstall.php is not called by WordPress, die
if ( !defined( 'WP_UNINSTALL_PLUGIN' )) {
	die;
}

require_once( dirname( __FILE__ ) . "/common/readoptions.php" );

delete_option( GTM4WP_OPTIONS );