<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils\Batch_Process as Batch;

/**
 * Implements a batch processor for generating payouts logs and exporting them to a CSV file.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Batch_Process\Export\CSV
 * @see \AffWP\Utils\Batch_Process\With_PreFetch
 */
class Generate_Payouts extends Batch\Export\CSV implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id = 'generate-payouts';

	/**
	 * Export type.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $export_type = 'payouts';

	/**
	 * Capability needed to perform the current export.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $capability = 'export_affiliate_data';

	/**
	 * The number of affiliates to process payouts for in each step.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int
	 */
	public $per_step = 1;

	/**
	 * Array of referrals to export.
	 *
	 * @access public
	 * @since  1.9
	 * @var    \AffWP\Referral[]
	 */
	public $referrals = array();

	/**
	 * ID of affiliate to generate a payout for.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $affiliate_id = 0;

	/**
	 * Start and/or end dates to retrieve referrals for.
	 *
	 * @access public
	 * @since  2.0
	 * @var    array
	 */
	public $date = array();

	/**
	 * Minimum total payout amount for each affiliate to qualify for inclusion.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $min_amount = 0;

	/**
	 * Initializes the batch process.
	 *
	 * This is the point where any relevant data should be initialized for use by the processor methods.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function init( $data = null ) {

		if ( null !== $data ) {

			if ( ! empty( $data['user_name'] ) && $affiliate = affwp_get_affiliate( $data['user_name'] ) ) {
				$this->affiliate_id = $affiliate->ID;
			}

			if ( ! empty( $data['minimum'] ) ) {
				$this->min_amount = sanitize_text_field( affwp_sanitize_amount( $data['minimum'] ) );
			}

			if ( ! empty( $data['from' ] ) ) {
				$this->date['start'] = sanitize_text_field( $data['from' ] );
			}

			if ( ! empty( $data['to'] ) ) {
				$this->date['end'] = sanitize_text_field( $data['to'] );
			}

		}

	}

	/**
	 * Pre-fetches data to speed up processing.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function pre_fetch() {
		// Referrals to export.
		$compiled_data = affiliate_wp()->utils->data->get( "{$this->batch_id}_compiled_data", array() );

		if ( false === $compiled_data ) {
			$args = array(
				'status'       => 'unpaid',
				'number'       => -1,
				'date'         => $this->date,
				'affiliate_id' => $this->affiliate_id,
			);

			$referrals_for_export = affiliate_wp()->referrals->get_referrals( $args );

			$this->compile_potential_payouts( $referrals_for_export );
		}
	}

	/**
	 * Retrieves processed affiliate and referral data based on the minimum amount (if any).
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @param \AffWP\Referral[] $referrals List of referrals to compile possible payouts for.
	 * @return array Processed affiliate and referral data.
	 */
	protected function compile_potential_payouts( $referrals ) {
		$data = $affiliates = array();

		if ( $referrals ) {

			$global_currency = affwp_get_currency();

			foreach ( $referrals as $referral ) {

				if ( array_key_exists( $referral->affiliate_id, $data ) ) {
					// Add the amount to an affiliate that already has a referral in the export
					$amount = $data[ $referral->affiliate_id ]['amount'] + $referral->amount;

					$data[ $referral->affiliate_id ]['amount']      = $amount;
					$data[ $referral->affiliate_id ]['referrals'][] = $referral->ID;

				} else {

					$data[ $referral->affiliate_id ] = array(
						'amount'    => $referral->amount,
						'currency'  => ! empty( $referral->currency ) ? $referral->currency : $global_currency,
						'referrals' => array( $referral->ID )
					);

				}
			}

			// Now determine which affiliates are above the minimum payout amount.
			if ( $this->min_amount > 0 ) {
				foreach ( $data as $affiliate_id => $payout ) {

					if ( $payout['amount'] < $this->min_amount ) {
						unset( $data[ $affiliate_id ] );
					}

				}
			}
		}

		affiliate_wp()->utils->data->write( "{$this->batch_id}_compiled_data", $data );

		// Set the total count based on the number of affiliate ids (keys).
		$this->set_total_count( count( $data ) );
	}

	/**
	 * Retrieves the columns for the CSV export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array The list of CSV columns.
	 */
	public function csv_cols() {
		/**
		 * Filters the list of CSV columns used when generating payout logs.
		 *
		 * @since 2.0.2
		 *
		 * @param array $columns CSV columns. Default 'email', 'amount', and 'currency'.
		 */
		return apply_filters( 'affwp_batch_generate_payouts_csv_cols', array(
			'email'    => __( 'Email', 'affiliate-wp' ),
			'amount'   => __( 'Amount', 'affiliate-wp' ),
			'currency' => __( 'Currency', 'affiliate-wp' ),
		) );
	}

	/**
	 * Retrieves the referral export data for a single step in the process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array Data for a single step of the export.
	 */
	public function get_data() {
		$offset = $this->get_offset();

		$payouts       = affiliate_wp()->utils->data->get( "{$this->batch_id}_compiled_data", array() );
		$affiliate_ids = array_keys( $payouts );

		if ( isset( $affiliate_ids[ $offset ] ) ) {
			$affiliate_id = $affiliate_ids[ $offset ];
		} else {
			$affiliate_id = false;
		}

		// Grab the next affiliate in the list.
		$data = array();

		if ( $affiliate_id && array_key_exists( $affiliate_id, $payouts ) ) {
			$current_payout = $payouts[ $affiliate_id ];

			/**
			 * Filters the data retrieved for a single generated payout during batch processing.
			 *
			 * @since 2.0.2
			 *
			 * @param array $data {
			 *     Payout data.
			 *
			 *     @type string $email    Affiliate payment email.
			 *     @type float  $amount   Payout amount.
			 *     @type string $currency Payout currency.
			 * }
			 * @param int   $affiliate_id Current affiliate ID.
			 * @param array $payouts      Compiled payouts and referrals data where the keys are affiliate
			 *                            IDs and values arrays of referral data.
			 */
			$data[] = apply_filters( 'affwp_batch_generate_payouts_get_data', array(
				'email'    => affwp_get_affiliate_payment_email( $affiliate_id ),
				'amount'   => affwp_format_amount( $payouts[ $affiliate_id ]['amount'] ),
				'currency' => $payouts[ $affiliate_id ]['currency'],
			), $affiliate_id, $payouts );

			affwp_add_payout( array(
				'affiliate_id'  => $affiliate_id,
				'referrals'     => $payouts[ $affiliate_id ]['referrals'],
				'payout_method' => 'manual',
			) );
		}

		return $data;
	}

	/**
	 * Retrieves a message for the given code.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $code Message code.
	 * @return string Message.
	 */
	public function get_message( $code ) {

		switch( $code ) {

			case 'done':
				$final_count = $this->get_current_count();

				if ( ! $final_count ) {
					$message = __( 'No unpaid referrals were found matching your criteria.', 'affiliate-wp' );
				} else {
					$message = sprintf(
						_n(
							'A payout log for %s affiliate was successfully generated.',
							'A payout log for %s affiliates was successfully generated.',
							$final_count,
							'affiliate-wp'
						), number_format_i18n( $final_count )
					);
				}
				break;

			default:
				$message = '';
				break;
		}

		return $message;
	}

}
