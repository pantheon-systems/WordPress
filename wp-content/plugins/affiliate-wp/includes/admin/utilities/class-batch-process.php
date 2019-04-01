<?php
namespace AffWP\Utils;

use AffWP\Utils\Batch_Process as Batch;

/**
 * Implements a basic batch process.
 *
 * Export processes should instead extend \AffWP\Utils\Batch_Process\Export.
 *
 * @since 2.0
 * @abstract
 */
abstract class Batch_Process implements Batch\Base {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id;

	/**
	 * The current step being processed.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int|string Step number or 'done'.
	 */
	public $step;

	/**
	 * Number of items to process per step.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int
	 */
	public $per_step = 100;

	/**
	 * Capability needed to perform the current batch process.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $capability = 'manage_affiliates';

	/**
	 * Sets up the batch process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|string $step Step number or 'done'.
	 */
	public function __construct( $step = 1 ) {

		$this->step = $step;

		if ( has_filter( "affwp_batch_per_step_{$this->batch_id}" ) ) {
			/**
			 * Filters the number of items to process per step for the given batch process.
			 *
			 * The dynamic portion of the hook name, `$this->export_type` refers to the export
			 * type defined in each sub-class.
			 *
			 * @since 2.0
			 *
			 * @param int                                     $per_step The number of items to process
			 *                                                          for each step. Default 100.
			 * @param \AffWP\Utils\Batch_Process\Base_Process $this     Batch process instance.
			 */
			$this->per_step = apply_filters( "affwp_batch_per_step_{$this->batch_id}", $this->per_step, $this );
		}
	}

	/**
	 * Determines if the current user can perform the current batch process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool True if the current user has the needed capability, otherwise false.
	 */
	public function can_process() {
		return current_user_can( $this->capability );
	}

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since  2.0
	 */
	public function process_step() {}

	/**
	 * Retrieves the calculated completion percentage.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 *
	 * @return int Percentage completed.
	 */
	public function get_percentage_complete() {
		$percentage = 0;

		$current_count = $this->get_current_count();
		$total_count   = $this->get_total_count();

		if ( $total_count > 0 ) {
			$percentage = ( $current_count / $total_count ) * 100;
		}

		if ( $percentage > 100 ) {
			$percentage = 100;
		}

		return $percentage;
	}

	/**
	 * Retrieves a message based on the given message code.
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
						'%s item was successfully processed.',
						'%s items were successfully processed.',
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

	/**
	 * Defines logic to execute once batch processing is complete.
	 *
	 * @access public
	 * @since  2.1.4
	 *
	 * @param string $batch_id Batch process ID.
	 */
	public function finish( $batch_id ) {
		affiliate_wp()->utils->data->delete_by_match( "^{$batch_id}[0-9a-z\_]+" );
	}

	/**
	 * Calculates and retrieves the offset for the current step.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return int Number of items to offset.
	 */
	public function get_offset() {
		return ( $this->step - 1 ) * $this->per_step;
	}

	/**
	 * Retrieves the current, stored count of processed items.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @see get_percentage_complete()
	 *
	 * @return int Current number of processed items. Default 0.
	 */
	protected function get_current_count() {
		return affiliate_wp()->utils->data->get( "{$this->batch_id}_current_count", 0 );
	}

	/**
	 * Sets the current count of processed items.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @param int $count Number of processed items.
	 */
	protected function set_current_count( $count ) {
		affiliate_wp()->utils->data->write( "{$this->batch_id}_current_count", $count );
	}

	/**
	 * Retrieves the total, stored count of items to process.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @see get_percentage_complete()
	 *
	 * @return int Current number of processed items. Default 0.
	 */
	protected function get_total_count() {
		return affiliate_wp()->utils->data->get( "{$this->batch_id}_total_count", 0 );
	}

	/**
	 * Sets the total count of items to process.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @param int $count Number of items to process.
	 */
	protected function set_total_count( $count ) {
		affiliate_wp()->utils->data->write( "{$this->batch_id}_total_count", $count );
	}

	/**
	 * Deletes the stored current and total counts of processed items.
	 *
	 * @access protected
	 * @since  2.0
	 */
	protected function delete_counts() {
		affiliate_wp()->utils->data->delete( "{$this->batch_id}_current_count" );
		affiliate_wp()->utils->data->delete( "{$this->batch_id}_total_count" );
	}

}
