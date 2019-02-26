<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_action( 'vc_activation_hook', 'vc_bc_multisite_options', 9 );

function vc_bc_multisite_options( $networkWide ) {
	global $current_site;
	if ( ! is_multisite() || empty( $current_site ) || ! $networkWide || get_site_option( 'vc_bc_options_called', false ) || get_site_option( 'wpb_js_js_composer_purchase_code', false ) ) {
		return;
	}
	// Now we need to check BC with license keys
	$is_main_blog_activated = get_blog_option( (int) $current_site->id, 'wpb_js_js_composer_purchase_code' );
	if ( $is_main_blog_activated ) {
		update_site_option( 'wpb_js_js_composer_purchase_code', $is_main_blog_activated );
	}
	update_site_option( 'vc_bc_options_called', true );
}
