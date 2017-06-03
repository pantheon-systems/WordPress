<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) || ! WP_UNINSTALL_PLUGIN ||
	dirname( WP_UNINSTALL_PLUGIN ) != dirname( plugin_basename( __FILE__ ) ) ) {

	exit;
}

delete_option( 'tadv_settings' );
delete_option( 'tadv_admin_settings' );
delete_option( 'tadv_version' );

// Delete old options
delete_option('tadv_options');
delete_option('tadv_toolbars');
delete_option('tadv_plugins');
delete_option('tadv_btns1');
delete_option('tadv_btns2');
delete_option('tadv_btns3');
delete_option('tadv_btns4');
delete_option('tadv_allbtns');
