<?php
namespace AffWP\Utils\Batch_Process;

if ( ! class_exists( '\Affiliate_WP_Export' ) ) {
	require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-export.php';
}

/**
 * Implements the base batch exporter as an intermediary between a batch process
 * and the base exporter class.
 *
 * @since 2.0
 *
 * @see \Affiliate_WP_Export
 */
class Export extends \Affiliate_WP_Export {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id;

	/**
	 * The file the export data will be stored in.
	 *
	 * @access protected
	 * @since  2.0
	 * @var    resource
	 */
	protected $file;

	/**
	 * The name of the file the export data will be stored in.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $filename;

	/**
	 * The export file type, e.g. '.csv'.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $filetype;

	/**
	 * The current step being processed.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int|string Step number or 'done'.
	 */
	public $step;

	/**
	 * Whether the the export file is writable.
	 *
	 * @access public
	 * @since  2.0
	 * @var    bool
	 */
	public $is_writable = true;

	/**
	 * Whether the export file is empty.
	 *
	 * @access public
	 * @since  2.0
	 * @var    bool
	 */
	public $is_empty = false;

	/**
	 * Number of items to process per step.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int
	 */
	public $per_step = 100;

	/**
	 * Sets up the batch export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|string $step Step number or 'done'.
	 */
	public function __construct( $step ) {

		$upload_dir     = wp_upload_dir();
		$this->filename = 'affiliate-wp-export-' . $this->export_type . '-' . date( 'm-d-Y' ) . $this->filetype;
		$this->file     = trailingslashit( $upload_dir['basedir'] ) . $this->filename;

		if ( ! is_writeable( $upload_dir['basedir'] ) ) {
			$this->is_writable = false;
		}

		$this->step = $step;
		$this->done = false;

		if ( has_filter( "affwp_export_per_step_{$this->export_type}" ) ) {
			/**
			 * Filters the number of items to process per step for the given export type.
			 *
			 * The dynamic portion of the hook name, `$this->export_type` refers to the export
			 * type defined in each sub-class.
			 *
			 * @since 2.0
			 *
			 * @param int                               $per_step The number of items to process
			 *                                                    for each step. Default 100.
			 * @param \AffWP\Utils\Batch_Process\Export $this     Exporter instance.
			 */
			$this->per_step = apply_filters( "affwp_export_per_step_{$this->export_type}", $this->per_step, $this );
		}
	}

	/**
	 * Determines if the current user can perform the current export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool True if the current user has the needed capability, otherwise false.
	 */
	public function can_process() {
		return $this->can_export();
	}

	/**
	 * Sets the export headers.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function headers() {
		ignore_user_abort( true );

		if ( ! affwp_is_func_disabled( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $this->filename );
		header( "Expires: 0" );
	}

	/**
	 * Retrieves the file that data will be written to.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @return string File data.
	 */
	protected function get_file() {

		$file = '';

		if ( @file_exists( $this->file ) ) {

			if ( ! is_writeable( $this->file ) ) {
				$this->is_writable = false;
			}

			$file = @file_get_contents( $this->file );

		} else {

			@file_put_contents( $this->file, '' );
			@chmod( $this->file, 0664 );

		}

		return $file;
	}

	/**
	 * Initiate the export file download.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function export() {
		if ( ! $this->can_export() ) {
			wp_die(
				__( 'You do not have permission to export data.', 'affiliate-wp' ),
				__( 'Error', 'affiliate-wp' ),
				array( 'response' => 403 )
			);
		}

		// Set headers.
		$this->headers();

		$file = $this->get_file();

		@unlink( $this->file );

		echo $file;

		die();
	}

	/**
	 * Appends data to the export file.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @param string $data Optional. Data to append to the export file. Default empty.
	 */
	protected function stash_step_data( $data = '' ) {
		$file  = $this->get_file();
		$file .= $data;

		@file_put_contents( $this->file, $file );
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
	 * Retrieves the calculated completion percentage.
	 *
	 * @access public
	 * @since  2.0
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
	 * @param string $batch_id Batch process ID.
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
