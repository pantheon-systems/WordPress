<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/class-list-table.php';

/**
 * Add per page screen option to the Affiliates list table
 *
 * @since 1.7
 */
function affwp_affiliates_screen_options() {

	$screen = affwp_get_current_screen();

	if ( $screen !== 'affiliate-wp-affiliates' ) {
		return;
	}

	add_screen_option(
		'per_page',
		array(
			'label'   => __( 'Number of affiliates per page:', 'affiliate-wp' ),
			'option'  => 'affwp_edit_affiliates_per_page',
			'default' => 30,
		)
	);

	/*
	 * Instantiate the list table to make the columns array available to screen options.
	 *
	 * If the 'view_affiliate' action is set, don't instantiate. Instantiating in sub-views
	 * creates conflicts in the screen option column controls if another list table is being
	 * displayed.
	 */
	if ( empty( $_REQUEST['action'] )
		|| ( ! empty( $_REQUEST['action'] ) && 'view_affiliate' !== $_REQUEST['action'] )
	) {
		new AffWP_Affiliates_Table();
	}

	/**
	 * Fires in the affiliates screen options area.
	 *
	 * @param string $screen The current screen.
	 */
	do_action( 'affwp_affiliates_screen_options', $screen );

}

/**
 * Per page screen option value for the Affiliates list table
 *
 * @since  1.7
 * @param  bool|int $status
 * @param  string   $option
 * @param  mixed    $value
 * @return mixed
 */
function affwp_affiliates_set_screen_option( $status, $option, $value ) {

	if ( 'affwp_edit_affiliates_per_page' === $option ) {
		return $value;
	}

	return $status;

}
add_filter( 'set-screen-option', 'affwp_affiliates_set_screen_option', 10, 3 );
