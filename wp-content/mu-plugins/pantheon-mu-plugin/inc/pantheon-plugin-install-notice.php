<?php
/**
 * If a Pantheon site is in Git mode, hide the Plugin installation functionality and show a notice.
 */

if ( ! wp_is_writable( WP_PLUGIN_DIR ) ) {
	if ( ! defined( 'DISALLOW_FILE_MODS' ) ) {
		define( 'DISALLOW_FILE_MODS', true );
	}

	add_action( 'admin_notices', '_pantheon_plugin_install_notice' );
	add_action( 'network_admin_notices', '_pantheon_plugin_install_notice' );
}

function _pantheon_plugin_install_notice() {
	$screen = get_current_screen();
	// Only show this notice on the plugins page.
	if ( 'plugins' === $screen->id || 'plugins-network' === $screen->id ) {
		$dashboard_url = Pantheon\_pantheon_get_dashboard_url();
		$message = __( 'If you wish to update or add plugins using the WordPress UI, switch your site to SFTP mode from your Pantheon dashboard.', 'pantheon-systems' );

		Pantheon\_pantheon_render_notice(
			[
				'type'        => 'warning',
				'heading'     => __( 'Your Site is in Git Mode', 'pantheon-systems' ),
				'message'     => $message,
				'button_text' => __( 'Go to the Dashboard', 'pantheon-systems' ),
				'button_url'  => $dashboard_url,
				'dismissible' => true,
			]
		);
	}
}
