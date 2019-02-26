<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils\Batch_Process as Batch;

/**
 * Implements a batch processor for exporting affiliate accounts based on status to a CSV file.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Batch_Process\Export\CSV
 * @see \AffWP\Utils\Batch_Process\With_PreFetch
 */
class Export_Affiliates extends Batch\Export\CSV implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id = 'export-affiliates';

	/**
	 * Export type.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $export_type = 'affiliates';

	/**
	 * Affiliates status to export.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $status;

	/**
	 * Initializes the batch process.
	 *
	 * This is the point where any relevant data should be initialized for use by the processor methods.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function init( $data = null ) {
		if ( null !== $data && isset( $data['status'] ) ) {
			$this->status = sanitize_text_field( $data['status'] );

			if ( 0 === $this->status ) {
				$this->status = '';
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
				'fields' => 'ids',
				'status' => $this->status,
			);

			$total_to_export = affiliate_wp()->affiliates->get_affiliates( $args, true );

			$this->set_total_count( absint( $total_to_export ) );
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
			'affiliate_id'    => __( 'Affiliate ID', 'affiliate-wp' ),
			'email'           => __( 'Email', 'affiliate-wp' ),
			'first_name'      => __( 'First Name', 'affiliate-wp' ),
			'last_name'       => __( 'Last Name', 'affiliate-wp' ),
			'payment_email'   => __( 'Payment Email', 'affiliate-wp' ),
			'username'        => __( 'Username', 'affiliate-wp' ),
			'rate'            => __( 'Rate', 'affiliate-wp' ),
			'rate_type'       => __( 'Rate Type', 'affiliate-wp' ),
			'earnings'        => __( 'Earnings', 'affiliate-wp' ),
			'unpaid_earnings' => __( 'Unpaid Earnings', 'affiliate-wp' ),
			'referrals'       => __( 'Referrals', 'affiliate-wp' ),
			'visits'          => __( 'Visits', 'affiliate-wp' ),
			'conversion_rate' => __( 'Conversion Rate', 'affiliate-wp' ),
			'status'          => __( 'Status', 'affiliate-wp' ),
			'date_registered' => __( 'Date Registered', 'affiliate-wp' )
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
			return new \WP_Error( 'no_status_found', __( 'No valid affiliate status was selected for export.', 'affiliate-wp' ) );
		}

		return parent::process_step();
	}

	/**
	 * Retrieves the affiliate export data for a single step in the process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array Data for a single step of the export.
	 */
	public function get_data() {

		$args = array(
			'status' => $this->status,
			'number' => $this->per_step,
			'offset' => $this->get_offset(),
		);

		$data       = array();
		$affiliates = affiliate_wp()->affiliates->get_affiliates( $args );

		if( $affiliates ) {

			foreach( $affiliates as $affiliate ) {

				$data[] = array(
					'affiliate_id'    => $affiliate->ID,
					'email'           => affwp_get_affiliate_email( $affiliate->ID ),
					'first_name'      => affwp_get_affiliate_first_name( $affiliate->ID ),
					'last_name'       => affwp_get_affiliate_last_name( $affiliate->ID ),
					'payment_email'   => affwp_get_affiliate_payment_email( $affiliate->ID ),
					'username'        => affwp_get_affiliate_login( $affiliate->ID ),
					'rate'            => affwp_get_affiliate_rate( $affiliate->ID ),
					'rate_type'       => affwp_get_affiliate_rate_type( $affiliate->ID ),
					'earnings'        => $affiliate->earnings,
					'unpaid_earnings' => $affiliate->unpaid_earnings,
					'referrals'       => $affiliate->referrals,
					'visits'          => $affiliate->visits,
					'conversion_rate' => affwp_get_affiliate_conversion_rate( $affiliate->ID ),
					'status'          => $affiliate->status,
					'date_registered' => $affiliate->date_i18n( 'datetime' ),
				);

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
						'%s affiliate was successfully exported.',
						'%s affiliates were successfully exported.',
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
