<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add per page screen option to the Direct Links list table
 *
 * @since 1.0.0
 */
function affwp_dlt_screen_options() {

	$screen = get_current_screen();

	if ( $screen->id !== 'affiliates_page_affiliate-wp-direct-links' ) {
		return;
	}

	add_screen_option(
		'per_page',
		array(
			'label'   => __( 'Number of direct links per page:', 'affiliatewp-direct-link-tracking' ),
			'option'  => 'affwp_edit_direct_links_per_page',
			'default' => 30,
		)
	);

	do_action( 'affwp_direct_link_tracking_direct_links_screen_options', $screen );

}
add_action( 'load-affiliates_page_affiliate-wp-direct-links', 'affwp_dlt_screen_options' );

/**
 * Per page screen option value for the Direct Links list table
 *
 * @since  1.0.0
 * @param  bool|int $status
 * @param  string   $option
 * @param  mixed    $value
 * @return mixed
 */
function affwp_dlt_set_screen_option( $status, $option, $value ) {

	if ( 'affwp_edit_direct_links_per_page' === $option ) {
		return $value;
	}

	return $status;

}
add_filter( 'set-screen-option', 'affwp_dlt_set_screen_option', 10, 3 );
