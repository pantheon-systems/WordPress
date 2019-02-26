<?php
/**
 * Export Class
 *
 * This is the base class for all export methods. Each data export type (referrals, affiliates, visits) extends this class.
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Export
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

use AffWP\Utils\Exporter;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Affiliate_WP_Export Class
 *
 * @since 1.0
 */
class Affiliate_WP_Referral_Payout_Export extends Affiliate_WP_Referral_Export implements Exporter\CSV {

	/**
	 * Our export type. Used for export-type specific filters/actions
	 * @var string
	 * @since 1.0
	 */
	public $export_type = 'referrals_payout';

	/**
	 * Array of referrals to export.
	 *
	 * @access public
	 * @since  1.9
	 * @var    array
	 */
	public $referrals = array();

	/**
	 * ID of the specific affiliate to include referrals for.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int
	 */
	public $affiliate_id = 0;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function __construct() {
		$this->referrals = $this->get_referrals_for_export();

		add_action( 'affwp_export_referrals_payout_end', array( $this, 'generate_payouts' ) );
	}

	/**
	 * Set the CSV columns
	 *
	 * @access public
	 * @since 1.0
	 * @return array $cols All the columns
	 */
	public function csv_cols() {
		$cols = array(
			'email'         => __( 'Email', 'affiliate-wp' ),
			'amount'        => __( 'Amount', 'affiliate-wp' ),
			'currency'      => __( 'Currency', 'affiliate-wp' ),
		);
		return $cols;
	}

	/**
	 * Retrieves the data being exported.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return array $data Data for Export
	 */
	public function get_data() {
		// Final data to be exported
		$data         = array();

		// The affiliates that have earnings to be paid
		$affiliates   = array();

		// The list of referrals that are possibly getting marked as paid
		$to_maybe_pay = array();

		// Retrieve the referrals from the database
		$referrals = $this->get_referrals_for_export();

		// The minimum payout amount
		$minimum      = ! empty( $_POST['minimum'] ) ? sanitize_text_field( affwp_sanitize_amount( $_POST['minimum'] ) ) : 0;

		if( $referrals ) {

			foreach( $referrals as $referral ) {

				if( in_array( $referral->affiliate_id, $affiliates ) ) {

					// Add the amount to an affiliate that already has a referral in the export

					$amount = $data[ $referral->affiliate_id ]['amount'] + $referral->amount;

					$data[ $referral->affiliate_id ]['amount'] = $amount;

				} else {

					$data[ $referral->affiliate_id ] = array(
						'email'    => affwp_get_affiliate_payment_email( $referral->affiliate_id ),
						'amount'   => $referral->amount,
						'currency' => ! empty( $referral->currency ) ? $referral->currency : affwp_get_currency()
					);

					$affiliates[] = $referral->affiliate_id;

				}

				// Add the referral to the list of referrals to maybe payout
				if( ! array_key_exists( $referral->affiliate_id, $to_maybe_pay ) ) {

					$to_maybe_pay[ $referral->affiliate_id ] = array();

				}

				$to_maybe_pay[ $referral->affiliate_id ][] = $referral->referral_id;

			}

			// Now determine which affiliates are above the minimum payout amount
			if( $minimum > 0 ) {
				foreach( $data as $affiliate_id => $payout ) {

					if( $payout['amount'] < $minimum ) {
						unset( $data[ $affiliate_id ] );
						unset( $to_maybe_pay[ $affiliate_id ] );
					}

				}
			}

			// We now know which referrals should be marked as paid
			foreach( $to_maybe_pay as $referral_list ) {
				foreach( $referral_list as $referral_id ) {
					affwp_set_referral_status( $referral_id, 'paid' );
				}
			}

		}

		/** This filter is documented in includes/admin/tools/export/class-export.php */
		$data = apply_filters( 'affwp_export_get_data', $data );

		/** This filter is documented in includes/admin/tools/export/class-export.php */
		$data = apply_filters( 'affwp_export_get_data_' . $this->export_type, $data );

		return $data;
	}

	/**
	 * Retrieves referrals for export.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @return array Array of referrals for export.
	 */
	public function get_referrals_for_export( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'status'       => 'unpaid',
			'date'         => ! empty( $this->date ) ? $this->date : '',
			'number'       => -1,
			'affiliate_id' => $this->affiliate_id,
		) );

		return affiliate_wp()->referrals->get_referrals( $args );
	}

	/**
	 * Generates payout objects batched by affiliate.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function generate_payouts() {
		$referrals = wp_list_pluck( $this->referrals, 'referral_id' );

		if ( ! empty( $referrals ) ) {
			$batches = affiliate_wp()->affiliates->payouts->get_affiliate_ids_by_referrals( $referrals );

			foreach ( $batches as $affiliate_id => $referrals ) {
				affwp_add_payout( array(
					'affiliate_id'  => $affiliate_id,
					'referrals'     => $referrals,
					'payout_method' => 'manual',
				) );
			}
		}
	}

}
