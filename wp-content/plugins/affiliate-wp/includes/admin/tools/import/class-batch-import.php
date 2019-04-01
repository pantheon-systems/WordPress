<?php
namespace AffWP\Utils\Batch_Process;

if ( ! class_exists( '\Affiliate_WP_Import' ) ) {
	require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/import/class-import.php';
}

/**
 * Implements the base batch importer as an intermediary between a batch process
 * and the base importer class.
 *
 * @since 2.1
 *
 * @see \Affiliate_WP_Import
 */
class Import extends \Affiliate_WP_Import {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.1
	 * @var    string
	 */
	public $batch_id;

	/**
	 * The file the import data will be stored in.
	 *
	 * @access public
	 * @since  2.1
	 * @var    resource
	 */
	public $file;

	/**
	 * The current step being processed.
	 *
	 * @access public
	 * @since  2.1
	 * @var    int
	 */
	public $step;

	/**
	 * The number of items to process per step.
	 *
	 * @access public
	 * @since  2.1
	 * @var    int
	 */
	public $per_step = 20;

	/**
	 * Map of CSV columns > database fields
	 *
	 * @access public
	 * @since  2.1
	 * @var    array
	 */
	public $field_mapping = array();

	/**
	 * Determines if the current user can perform the current export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool True if the current user has the needed capability, otherwise false.
	 */
	public function can_process() {
		return $this->can_import();
	}

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since  2.1
	 */
	public function process_step() {}

	/**
	 * Trims a column value for preview.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param $str Input string to trim down.
	 * @return string String trimmed for preview.
	 */
	public function trim_preview( $str = '' ) {

		if( ! is_numeric( $str ) ) {

			$long = strlen( $str ) >= 30;
			$str  = substr( $str, 0, 30 );
			$str  = $long ? $str . '...' : $str;

		}

		return $str;
	}

	/**
	 * Calculates and retrieves the offset for the current step.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return int Number of items to offset.
	 */
	public function get_offset() {
		return ( $this->step - 1 ) * $this->per_step;
	}

	/**
	 * Retrieves the calculated completion percentage.
	 *
	 * @access public
	 * @since  2.1
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
	 * Defines logic to execute once batch processing is complete.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $batch_id Batch ID.
	 */
	public function finish( $batch_id ) {
		affiliate_wp()->utils->data->delete_by_match( "^{$batch_id}[0-9a-z\_]+" );
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
