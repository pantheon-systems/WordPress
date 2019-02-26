<?php
namespace AffWP\Utils\Exporter;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Promise for structuring exporters.
 *
 * @since 2.0
 */
interface Base {

	/**
	 * Determines whether the current user can perform an export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool Whether the current user can perform an export.
	 */
	public function can_export();

	/**
	 * Handles sending appropriate headers depending on the type of export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return void
	 */
	public function headers();

	/**
	 * Retrieves the data for export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array[] Multi-dimensional array of data for export.
	 */
	public function get_data();

	/**
	 * Performs the export process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return void
	 */
	public function export();

}
