<?php
/**
 * Export processing
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Tools/Export
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/
use \AffWP\Utils\Exporter;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Process an affiliates export
 *
 * @since       1.0
 * @return      void
 */
function affwp_process_affiliates_export() {

	if( empty( $_POST['affwp_export_affiliates_nonce'] ) ) {
		return;
	}

	if( ! wp_verify_nonce( $_POST['affwp_export_affiliates_nonce'], 'affwp_export_affiliates_nonce' ) ) {
		return;
	}

	$status  = ! empty( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : false;

	$export = new Affiliate_WP_Affiliate_Export;
	$export->status    = $status;
	$export->export();

}
add_action( 'affwp_export_affiliates', 'affwp_process_affiliates_export' );

/**
 * Process a referrals export
 *
 * @since       1.0
 * @return      void
 */
function affwp_process_referrals_export() {

	if( empty( $_POST['affwp_export_referrals_nonce'] ) ) {
		return;
	}

	if( ! wp_verify_nonce( $_POST['affwp_export_referrals_nonce'], 'affwp_export_referrals_nonce' ) ) {
		return;
	}

	$can_export = false;

	$can_export = ( current_user_can( 'manage_options' ) || current_user_can( 'export_referral_data' ) ) ? true : false;

	if( ! $can_export ) {
		return;
	}

	$start   = ! empty( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : false;
	$end     = ! empty( $_POST['end_date'] )   ? sanitize_text_field( $_POST['end_date'] )   : false;
	$status  = ! empty( $_POST['status'] )     ? sanitize_text_field( $_POST['status'] )     : false;
	$user_id = ! empty( $_POST['user_id'] )    ? absint( $_POST['user_id'] )                 : false;

	$export = new Affiliate_WP_Referral_Export;

	$export->date = array(
		'start' => $start,
		'end'   => $end
	);

	$export->status = $status;

	if( ! empty( $user_id ) ) {

		$export->affiliate = affwp_get_affiliate_id( $user_id );

	}

	$export->export();

}
add_action( 'affwp_export_referrals', 'affwp_process_referrals_export' );

/**
 * Process a settings export that generates a .json file of the shop settings.
 *
 * @since       1.0
 * @return      void
 */
function affwp_process_settings_export() {

	if( empty( $_POST['affwp_export_nonce'] ) )
		return;

	if( ! wp_verify_nonce( $_POST['affwp_export_nonce'], 'affwp_export_nonce' ) )
		return;

	if( ! current_user_can( 'manage_affiliate_options' ) )
		return;

	$settings = new Exporter\Settings();
	$settings->export();

	exit;
}
add_action( 'affwp_export_settings', 'affwp_process_settings_export' );

/**
 * Converts the character encoding for the export data
 *
 * @since 2.1.17
 *
 * @param array $data Single row of exported data.
 * @return array Single row of exported data.
 */
function affwp_export_set_data_charset( $data ) {

	if ( defined( 'AFFILIATE_WP_EXPORT_CHARSET' ) ) {

		$charset = AFFILIATE_WP_EXPORT_CHARSET;

		foreach ( $data as $key => $value ) {

			$data[ $key ] = mb_convert_encoding( $value , $charset, get_option( 'blog_charset' ));

		}

	}

	return $data;

}
add_filter( 'affwp_visit_export_get_data_line', 'affwp_export_set_data_charset' );
add_filter( 'affwp_payout_export_get_data_line', 'affwp_export_set_data_charset' );
add_filter( 'affwp_referral_export_get_data_line', 'affwp_export_set_data_charset' );
add_filter( 'affwp_affiliate_export_get_data_line', 'affwp_export_set_data_charset' );

/**
 * Converts the character encoding for the export CSV columns (headers)
 *
 * @since 2.1.17
 *
 * @param array $cols The export CSV columns (headers)
 * @return array The export CSV columns (headers)
 */
function affwp_export_set_header_charset( $cols ) {

	if ( defined( 'AFFILIATE_WP_EXPORT_CHARSET' ) ) {

		$charset = AFFILIATE_WP_EXPORT_CHARSET;

		foreach ( $cols as $key => $value ) {

			$cols[ $key ] = mb_convert_encoding( $value , $charset, get_option( 'blog_charset' ));

		}

	}

	return $cols;

}
add_filter( 'affwp_export_csv_cols_visits', 'affwp_export_set_data_charset' );
add_filter( 'affwp_export_csv_cols_payouts', 'affwp_export_set_data_charset' );
add_filter( 'affwp_export_csv_cols_referrals', 'affwp_export_set_data_charset' );
add_filter( 'affwp_export_csv_cols_affiliates', 'affwp_export_set_data_charset' );
add_filter( 'affwp_export_csv_cols_payout-logs', 'affwp_export_set_data_charset' );
add_filter( 'affwp_export_csv_cols_referrals_payout', 'affwp_export_set_data_charset' );