<?php
namespace AffWP\Utils\Batch_Process\Export;

use AffWP\Utils\Batch_Process as Batch;
use AffWP\Utils\Exporter;

/**
 * Implements a base CSV batch exporter.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Batch_Process\Export
 * @see \AffWP\Utils\Exporter\CSV
 */
class CSV extends Batch\Export implements Exporter\CSV {

	/**
	 * The export file type, e.g. '.csv'.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $filetype = '.csv';

	/**
	 * Number of processed rows per step.
	 *
	 * @access protected
	 * @since  2.0
	 * @var    int
	 */
	protected $rows = 0;

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since  2.0
	 */
	public function process_step() {

		$current_count = $this->get_current_count();

		if ( $this->step < 2 ) {

			// Make sure we start with a fresh file on step 1.
			@unlink( $this->file );
			$this->csv_cols_out();
		}

		$rows = $this->csv_rows_out();

		if ( empty( $rows ) ) {
			// If empty and the first step, it's an empty export.
			if ( $this->step < 2 ) {
				$this->is_empty = true;
			}

			return 'done';
		}

		if ( false !== $current_count ) {
			$this->set_current_count( $current_count + $this->rows );
		} else {
			$this->set_current_count( $this->rows );
		}

		return ++$this->step;
	}

	/**
	 * Retrieves and stores the CSV columns for the current step.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return string Column data.
	 */
	public function csv_cols_out() {
		$col_data = '';
		$cols = $this->get_csv_cols();
		$i = 1;
		foreach( $cols as $col_id => $column ) {
			$col_data .= '"' . addslashes( $column ) . '"';
			$col_data .= $i == count( $cols ) ? '' : ',';
			$i++;
		}
		$col_data .= "\r\n";

		$this->stash_step_data( $col_data );

		return $col_data;
	}

	/**
	 * Retrieves and stores the CSV rows for the current step.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return string Rows data.
	 */
	public function csv_rows_out() {
		$row_data = '';
		$data     = $this->get_data();
		$cols     = $this->get_csv_cols();

		if( $data ) {

			// Output each row
			foreach ( $data as $row ) {
				$i = 1;
				foreach ( $row as $col_id => $column ) {
					// Make sure the column is valid
					if ( array_key_exists( $col_id, $cols ) ) {
						$row_data .= '"' . addslashes( preg_replace( "/\"/","'", $column ) ) . '"';
						$row_data .= $i == count( $cols ) ? '' : ',';
						$i++;
					}
				}
				$row_data .= "\r\n";
			}

			$this->stash_step_data( $row_data );
			$this->rows = count( $data );

			return $row_data;
		}

		return false;
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

		$file = $this->get_file();
		$file .= $data;
		@file_put_contents( $this->file, $file );

		// If we have no rows after this step, mark it as an empty export
		$file_rows    = file( $this->file, FILE_SKIP_EMPTY_LINES);
		$default_cols = $this->get_csv_cols();
		$default_cols = empty( $default_cols ) ? 0 : 1;

		$this->is_empty = count( $file_rows ) == $default_cols ? true : false;

	}

}
