<?php

/**
 * Base importer class.
 *
 * @since 2.1
 */
class Affiliate_WP_Import {

	/**
	 * Import type.
	 *
	 * Used for import-type specific filters/actions.
	 *
	 * @access public
	 * @since  2.1
	 * @var    string
	 */
	public $import_type = 'default';

	/**
	 * Capability needed to perform the current import.
	 *
	 * @access public
	 * @since  2.1
	 * @var    string
	 */
	public $capability = 'manage_affiliates';

	/**
	 * The file being imported.
	 *
	 * @access public
	 * @since  2.1
	 * @var    resource
	 */
	public $file;

	/**
	 * Whether the import file is empty.
	 *
	 * @access public
	 * @since  2.1
	 * @var    bool
	 */
	public $is_empty = false;

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
		$this->step = $_step;
		$this->file = $_file;
	}

	/**
	 * Determines whether the current user can perform an import.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return bool Whether the current use can import.
	 */
	public function can_import() {
		/**
		 * Filters the capability needed to perform an import.
		 *
		 * @since 2.0
		 *
		 * @param string $capability Capability needed to perform an export.
		 */
		return (bool) current_user_can( apply_filters( 'affwp_import_capability', $this->capability ) );
	}

	/**
	 * Retrieves the URL to the list table for the import data type.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return string List table URL.
	 */
	public function get_list_table_url() {}

	/**
	 * Retrieves the label for the import type.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return string Import type label.
	 */
	public function get_import_type_label() {}

	/**
	 * Converts a string containing delimiters to an array.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param string $str Optional. Input string to convert to an array. Default empty.
	 * @return array Derived array.
	 */
	public function str_to_array( $str = '' ) {

		$array = array();

		if( is_array( $str ) ) {
			return array_map( 'trim', $str );
		}

		// Look for standard delimiters
		if( false !== strpos( $str, '|' ) ) {

			$delimiter = '|';

		} elseif( false !== strpos( $str, ',' ) ) {

			$delimiter = ',';

		} elseif( false !== strpos( $str, ';' ) ) {

			$delimiter = ';';

		} elseif( false !== strpos( $str, '/' ) && ! filter_var( $str, FILTER_VALIDATE_URL ) ) {

			$delimiter = '/';

		}

		if( ! empty( $delimiter ) ) {

			$array = (array) explode( $delimiter, $str );

		} else {

			$array[] = $str;
		}

		return array_map( 'trim', $array );

	}

	/**
	 * Trims a column value for preview.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param string $str Optional. Input string to trim down. Default empty.
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
}
