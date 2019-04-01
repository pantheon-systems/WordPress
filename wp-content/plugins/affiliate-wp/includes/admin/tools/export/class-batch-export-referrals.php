<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils\Batch_Process as Batch;

/**
 * Implements a batch processor for exporting referrals based on status to a CSV file.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Batch_Process\Export\CSV
 * @see \AffWP\Utils\Batch_Process\With_PreFetch
 */
class Export_Referrals extends Batch\Export\CSV implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id = 'export-referrals';

	/**
	 * Export type.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $export_type = 'referrals';

	/**
	 * Capability needed to perform the current export.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $capability = 'export_referral_data';

	/**
	 * ID of affiliate to export referrals for.
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
	 * Status to export referrals for.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $status = '';

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

			$data = affiliate_wp()->utils->process_request_data( $data, 'user_name' );

			if ( ! empty( $data['user_id'] ) ) {
				if ( $affiliate_id = affwp_get_affiliate_id( absint( $data['user_id'] ) ) ) {
					$this->affiliate_id = $affiliate_id;
				}
			}

			if ( ! empty( $data['start_date' ] ) ) {
				$this->date['start'] = sanitize_text_field( $data['start_date' ] );
			}

			if ( ! empty( $data['end_date'] ) ) {
				$this->date['end'] = sanitize_text_field( $data['end_date'] );
			}

			if ( ! empty( $data['status'] ) ) {
				$this->status = sanitize_text_field( $data['status'] );

				if ( 0 === $this->status ) {
					$this->status = '';
				}
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
		$total_to_export = $this->get_total_count();

		if ( false === $total_to_export  ) {
			$args = array(
				'number'       => -1,
				'fields'       => 'ids',
				'status'       => $this->status,
				'date'         => $this->date,
				'affiliate_id' => $this->affiliate_id,
			);

			$total_to_export = affiliate_wp()->referrals->get_referrals( $args, true );

			$this->set_total_count( $total_to_export );
		}
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
		return array(
			'affiliate_id'  => __( 'Affiliate ID', 'affiliate-wp' ),
			'email'         => __( 'Email', 'affiliate-wp' ),
			'name'          => __( 'Name', 'affiliate-wp' ),
			'payment_email' => __( 'Payment Email', 'affiliate-wp' ),
			'username'      => __( 'Username', 'affiliate-wp' ),
			'amount'        => __( 'Amount', 'affiliate-wp' ),
			'currency'      => __( 'Currency', 'affiliate-wp' ),
			'description'   => __( 'Description', 'affiliate-wp' ),
			'campaign'      => __( 'Campaign', 'affiliate-wp' ),
			'reference'     => __( 'Reference', 'affiliate-wp' ),
			'context'       => __( 'Context', 'affiliate-wp' ),
			'status'        => __( 'Status', 'affiliate-wp' ),
			'date'          => __( 'Date', 'affiliate-wp' ),
		);
	}

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since  2.0
	 */
	public function process_step() {
		if ( is_null( $this->status ) ) {
			return new \WP_Error( 'no_status_found', __( 'No valid referral status was selected for export.', 'affiliate-wp' ) );
		}

		return parent::process_step();
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

		$args = array(
			'status'       => $this->status,
			'date'         => $this->date,
			'affiliate_id' => $this->affiliate_id,
			'number'       => $this->per_step,
			'offset'       => $this->get_offset(),
		);

		$data         = array();
		$affiliates   = array();
		$referral_ids = array();
		$referrals    = affiliate_wp()->referrals->get_referrals( $args );

		if( $referrals ) {

			foreach( $referrals as $referral ) {

				/** This filter is documented in includes/admin/tools/export/class-export-referrals.php */
				$referral_data = apply_filters( 'affwp_referral_export_get_data_line', array(
					'affiliate_id'  => $referral->affiliate_id,
					'email'         => affwp_get_affiliate_email( $referral->affiliate_id ),
					'name'          => affwp_get_affiliate_name( $referral->affiliate_id ),
					'payment_email' => affwp_get_affiliate_payment_email( $referral->affiliate_id ),
					'username'      => affwp_get_affiliate_login( $referral->affiliate_id ),
					'amount'        => $referral->amount,
					'currency'      => $referral->currency,
					'description'   => $referral->description,
					'campaign'      => $referral->campaign,
					'reference'     => $referral->reference,
					'context'       => $referral->context,
					'status'        => $referral->status,
					'date'          => $referral->date_i18n( 'datetime' ),
				), $referral );

				// Add slashing.
				$data[] = array_map( function( $column ) {
					return addslashes( preg_replace( "/\"/","'", $column ) );
				}, $referral_data );

				unset( $referral_data );
			}

		}

		return $this->prepare_data( $data );
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

				$message = sprintf(
					_n(
						'%s referral was successfully exported.',
						'%s referrals were successfully exported.',
						$final_count,
						'affiliate-wp'
					), number_format_i18n( $final_count )
				);
				break;

			default:
				$message = '';
				break;
		}

		return $message;
	}

}
