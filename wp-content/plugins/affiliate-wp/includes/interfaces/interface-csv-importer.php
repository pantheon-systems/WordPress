<?php
namespace AffWP\Utils\Importer;

use AffWP\Utils\Importer;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Promise for structuring CSV importers.
 *
 * @since 2.1
 *
 * @see \AffWP\Utils\Importer\Base
 */
interface CSV extends Importer\Base {

	/**
	 * Maps CSV columns to their corresponding import fields.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param array $import_fields Import fields to map.
	 */
	public function map_fields( $import_fields = array() );

	/**
	 * Retrieves the CSV columns.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return array The columns in the CSV.
	 */
	public function get_columns();

	/**
	 * Maps a single CSV row to the data passed in via init().
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param array $csv_row CSV row data.
	 * @return array CSV row data mapped to form-defined arguments.
	 */
	public function map_row( $csv_row );

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
	public function get_first_row();

}
