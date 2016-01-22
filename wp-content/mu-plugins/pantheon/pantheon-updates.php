<?php
// Only in Test and Live Environments...
if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], Array('test', 'live') ) ) {
	//
	// Disable Core Updates EVERYWHERE (use git upstream)
	//
	function _pantheon_disable_wp_updates() {
		include ABSPATH . WPINC . '/version.php';
		return (object) array(
			'updates' => array(),
			'version_checked' => $wp_version,
			'last_checked' => time(),
		);
	}

	add_filter( 'pre_site_transient_update_core', '_pantheon_disable_wp_updates' );

	//
	// Disable Plugin Updates
	//
	add_action('admin_menu','_pantheon_hide_admin_notices');
	function _pantheon_hide_admin_notices() {
		remove_action( 'admin_notices', 'update_nag', 3 );
	}

	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	add_filter( 'pre_site_transient_update_plugins', '_pantheon_disable_wp_updates' );

	//
	// Disable Theme Updates
	//
	remove_action( 'load-update-core.php', 'wp_update_themes' );
	add_filter( 'pre_site_transient_update_themes', '_pantheon_disable_wp_updates' );
}

