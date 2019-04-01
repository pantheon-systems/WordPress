<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils\Batch_Process as Batch;

/**
 * Implements a batch processor for exporting visits based on affiliate ID or a date range
 * to a CSV file.
 *
 * @since 2.1
 *
 * @see \AffWP\Utils\Batch_Process\Export\CSV
 * @see \AffWP\Utils\Batch_Process\With_PreFetch
 */
class Export_Visits extends Batch\Export\CSV implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id = 'export-visits';

	/**
	 * Export type.
	 *
	 * @access public
	 * @since  2.1
	 * @var    string
	 */
	public $export_type = 'visits';

	/**
	 * Capability needed to perform the current export.
	 *
	 * @access public
	 * @since  2.1
	 * @var    string
	 */
	public $capability = 'export_visit_data';

	/**
	 * ID of affiliate to export visits for.
	 *
	 * @access public
	 * @since  2.1
	 * @var    string
	 */
	public $affiliate_id = 0;

	/**
	 * Start and/or end dates to retrieve visits for.
	 *
	 * @access public
	 * @since  2.1
	 * @var    array
	 */
	public $date = array();

	/**
	 * Referral (conversion) status to export visits for.
	 *
	 * @access public
	 * @since  2.1
	 * @var    string
	 */
	public $referral_status = '';

	/**
	 * Initializes the batch process.
	 *
	 * This is the point where any relevant data should be initialized for use by the processor methods.
	 *
	 * @access public
	 * @since  2.1
	 */
	public function init( $data = null ) {

		if ( null !== $data ) {

			$data = affiliate_wp()->utils->process_request_data( $data, 'user_name' );

			// TODO: stop using affwp_get_affiliate_id()
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

			if ( ! empty( $data['referral_status'] ) ) {
				$this->referral_status = sanitize_text_field( $data['referral_status'] );

				if ( 0 === $this->referral_status ) {
					$this->referral_status = '';
				}
			}
		}

	}

	/**
	 * Pre-fetches data to speed up processing.
	 *
	 * @access public
	 * @since  2.1
	 */
	public function pre_fetch() {
		$total_to_export = $this->get_total_count();

		if ( false === $total_to_export  ) {
			$args = array(
				'number'          => -1,
				'fields'          => 'ids',
				'referral_status' => $this->referral_status,
				'date'            => $this->date,
				'affiliate_id'    => $this->affiliate_id,
			);

			$total_to_export = affiliate_wp()->visits->get_visits( $args, true );

			$this->set_total_count( $total_to_export );
		}
	}

	/**
	 * Retrieves the columns for the CSV export.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return array The list of CSV columns.
	 */
	public function csv_cols() {
		return array(
			'visit_id'        => __( 'Visit ID', 'affiliate-wp' ),
			'affiliate'       => __( 'Affiliate', 'affiliate-wp' ),
			'referral_status' => __( 'Converted', 'affiliate-wp' ),
			'campaign'        => __( 'Campaign', 'affiliate-wp' ),
			'url'             => __( 'URL', 'affiliate-wp' ),
			'referrer'        => __( 'Referrer', 'affiliate-wp' ),
			'context'         => __( 'Context', 'affiliate-wp' ),
			'date'            => _x( 'Date', 'visit', 'affiliate-wp' ),
		);
	}

	/**
	 * Retrieves the visit export data for a single step in the process.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return array Data for a single step of the export.
	 */
	public function get_data() {

		$args = array(
			'referral_status' => $this->referral_status,
			'date'            => $this->date,
			'affiliate_id'    => $this->affiliate_id,
			'number'          => $this->per_step,
			'offset'          => $this->get_offset(),
		);

		$data         = array();
		$affiliates   = array();
		$referral_ids = array();

		/** @var \AffWP\Visit[] $visits */
		$visits = affiliate_wp()->visits->get_visits( $args );

		if ( $visits ) {

			$date_format = get_option( 'date_format' );

			foreach( $visits as $visit ) {

				$affiliate = sprintf( '%s (#%d)', affwp_get_affiliate_name( $visit->affiliate_id ), $visit->affiliate_id );

				if ( $visit->referral_id ) {
					$referral_status = sprintf( __( 'Yes (Ref: #%s)', 'affiliate-wp' ), $visit->referral_id );
				} else {
					$referral_status = _x( 'No', 'visit not converted', 'affiliate-wp' );
				}

				/**
				 * Filters an individual line of visit data to be exported.
				 *
				 * @since 2.1
				 *
				 * @param array        $visit_data {
				 *     Single row of exported visit data
				 *
				 *     @type int    $visit_id        Visit ID.
				 *     @type int    $affiliate_id    Affiliate ID.
				 *     @type int    $referral_id     Referral ID.
				 *     @type string $referral_status Referral status, 'converted', 'unconverted', or empty (all).
				 *     @type string $campaign        Campaign.
				 *     @type string $url             URL.
				 *     @type string $referrer        Referrer.
				 *     @type string $context         Visit context.
				 *     @type string $date            Visit date.
				 * }
				 * @param \AffWP\Visit $visit Visit object.
				 */
				$visit_data = apply_filters( 'affwp_visit_export_get_data_line', array(
					'visit_id'        => $visit->ID,
					'affiliate'       => $affiliate,
					'referral_status' => $referral_status,
					'campaign'        => $visit->campaign,
					'url'             => $visit->url,
					'referrer'        => $visit->referrer,
					'context'         => $visit->context,
					'date'            => $visit->date_i18n(),
				), $visit );

				// Add slashing.
				$data[] = array_map( function( $column ) {
					return addslashes( preg_replace( "/\"/","'", $column ) );
				}, $visit_data );

				unset( $visit_data );
			}

		}

		return $this->prepare_data( $data );
	}

	/**
	 * Retrieves a message for the given code.
	 *
	 * @access public
	 * @since  2.1
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
						'%s visit was successfully exported.',
						'%s visits were successfully exported.',
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
