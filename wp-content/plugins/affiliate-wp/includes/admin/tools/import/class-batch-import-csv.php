<?php
namespace AffWP\Utils\Batch_Process\Import;

use AffWP\Utils\Batch_Process as Batch;
use AffWP\Utils\Importer;

/**
 * CSV importer base class.
 *
 * @since 2.1
 *
 * @see \AffWP\Utils\Batch_Process\Import
 * @see \AffWP\Utils\Importer\CSV
 */
class CSV extends Batch\Import implements Importer\CSV {

	/**
	 * The parsed CSV file being imported.
	 *
	 * @access public
	 * @since  2.1
	 * @var    \parseCSV
	 */
	public $csv;

	/**
	 * Total rows in the CSV file.
	 *
	 * @access public
	 * @since  2.1
	 * @var    int
	 */
	public $total;

	/**
	 * Map of CSV columns > database fields
	 *
	 * @access public
	 * @since  2.1
	 * @var    array
	 */
	public $field_mapping = array();

	/**
	 * Form data passed via Ajax.
	 *
	 * @access public
	 * @since  2.1
	 * @var    array
	 */
	public $data = array();

	/**
	 * Instantiates the importer.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param resource $_file File to import.
	 * @param int      $_step Current step.
	 */
	public function __construct( $_file = '', $_step = 1 ) {

		if( ! class_exists( 'parseCSV' ) ) {
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/libraries/parsecsv.lib.php';
		}

		$this->step = $_step;
		$this->file = $_file;
		$this->done = false;
		$this->csv = new \parseCSV();
		$this->csv->auto( $this->file );

		$this->total = count( $this->csv->data );

		parent::__construct( $_file, $_step );
	}

	/**
	 * Maps CSV columns to their corresponding import fields.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param array $import_fields Import fields to map.
	 */
	public function map_fields( $import_fields = array() ) {
		$this->field_mapping = $import_fields;
	}

	/**
	 * Retrieves the CSV columns.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return array The columns in the CSV.
	 */
	public function get_columns() {
		return array_values( array_filter( $this->csv->titles ) );
	}

	/**
	 * Maps a single CSV row to the data passed in via init().
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param array $csv_row CSV row data.
	 * @return array CSV row data mapped to form-defined arguments.
	 */
	public function map_row( $csv_row ) {
		$mapped_row = array();

		foreach ( $this->data as $key => $field ) {
			if ( ! empty( $this->data[ $key ] ) && ! empty( $csv_row[ $this->data[ $key ] ] ) ) {
				$mapped_row[ $key ] = $csv_row[ $this->data[ $key ] ];
			}
		}

		return $mapped_row;
	}

	/**
	 * Retrieves the first row of the CSV.
	 *
	 * This is used for showing an example of what the import will look like.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return array The first row after the header of the CSV.
	 */
	public function get_first_row() {
		return array_map( array( $this, 'trim_preview' ), current( $this->csv->data ) );
	}

	/**
	 * Retrieves the CSV data for processing.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return array[] Multi-dimensional array of data for import.
	 */
	public function get_data() {
		/**
		 * Filters the data supplied to the batch importer process.
		 *
		 * @since 2.1
		 *
		 * @param array                                 $data CSV data.
		 * @param \AffWP\Utils\Batch_Process\Import\CSV $this Batch process instance.
		 */
		return apply_filters( "affwp_batch_{$this->batch_id}_data", $this->csv->data, $this );
	}

	/**
	 * Sets the running count in the temporary data store.
	 *
	 * The "running" count differs from the "current" count because the current count is used
	 * to calculate the percentage and keep the progress bar moving, whereas the running count
	 * is the actual number of items processed.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param int $count Running count.
	 */
	public function set_running_count( $count ) {
		affiliate_wp()->utils->data->write( "{$this->batch_id}_running_count", $count );
	}

	/**
	 * Retrieves the running count from the temporary data store.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @see set_running_count()
	 *
	 * @return int Running count of processed affiliates.
	 */
	public function get_running_count() {
		return affiliate_wp()->utils->data->get( "{$this->batch_id}_running_count", 0 );
	}

	/**
	 * Performs the import process.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return void
	 */
	public function import() {}

}
