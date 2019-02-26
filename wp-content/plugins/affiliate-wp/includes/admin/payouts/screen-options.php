<?php
/**
 * 'Payouts' Screen Options
 *
 * @package    AffiliateWP\Admin\Payouts
 * @copyright  Copyright (c) 2014, Pippin Williamson
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/payouts/class-list-table.php';

/**
 * Adds per-page screen option to the Payouts list table.
 *
 * @since 1.9
 */
function affwp_payouts_screen_options() {

	$screen = affwp_get_current_screen();

	if ( $screen !== 'affiliate-wp-payouts' ) {
		return;
	}

	add_screen_option(
		'per_page',
		array(
			'label'   => __( 'Number of payouts per page:', 'affiliate-wp' ),
			'option'  => 'affwp_edit_payouts_per_page',
			'default' => 30,
		)
	);

	/*
	 * Instantiate the list table to make the columns array available to screen options.
	 *
	 * If the 'view_payout' action is set, don't instantiate. Instantiating in sub-views
	 * creates conflicts in the screen option column controls if another list table is being
	 * displayed.
	 */
	if ( empty( $_REQUEST['action'] )
	     || ( ! empty( $_REQUEST['action'] ) && 'view_payout' !== $_REQUEST['action'] )
	) {
		new AffWP_Payouts_Table;
	}

	/**
	 * Fires at the end of the Payouts screen options callback.
	 *
	 * @since 1.9
	 *
	 * @param \WP_Screen $screen Current screen.
	 */
	do_action( 'affwp_payouts_screen_options', $screen );

}
add_action( 'load-affiliates_page_affiliate-wp-payouts', 'affwp_payouts_screen_options' );

/**
 * Renders per-page screen option value for the Payouts list table.
 *
 * @since 1.9
 *
 * @param bool|int $status Screen option value.
 * @param string   $option The option name.
 * @param int      $value  The number of rows to use.
 * @return bool|int Screen option value.
 */
function affwp_payouts_set_screen_option( $status, $option, $value ) {

	if ( 'affwp_edit_payouts_per_page' === $option ) {
		return $value;
	}

	return $status;

}
add_filter( 'set-screen-option', 'affwp_payouts_set_screen_option', 10, 3 );
