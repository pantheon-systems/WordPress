<?php

namespace Pantheon\NetworkSetup;

/**
 * Alter network setup pages to include Pantheon-specific instructions.
 */

/**
 * Replace the WordPress core Network Setup page from the Settings menu.
 */
function pantheon_remove_network_setup() {
	global $submenu;
    if ( isset( $submenu['tools.php'][50] ) ) {
	    unset( $submenu['tools.php'][50] );
    }
}

/**
 * Register the Pantheon network setup submenu page.
 */
function pantheon_add_network_setup() {
	add_management_page(
		__( 'Create a Network of WordPress  Sites', 'network-setup' ),
		__( 'Network Setup', 'network-setup' ),
		'setup_network',
		'setup_network',
		 __NAMESPACE__ . '\\pantheon_render_network_setup_page'
	);
}

/**
 * Render the Pantheon network setup page.
 */
function pantheon_render_network_setup_page() {
    global $wpdb;
    require_once __DIR__ . '/network/network.php';
}

add_action( 'admin_menu', __NAMESPACE__ . '\\pantheon_remove_network_setup' );
add_action( 'admin_menu', __NAMESPACE__ . '\\pantheon_add_network_setup' );
