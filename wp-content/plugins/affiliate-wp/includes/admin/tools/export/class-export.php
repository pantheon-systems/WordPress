<?php
/**
 * Export Class
 *
 * This is the base class for all export methods. Each data export type (referrals, affiliates, visits) extends this class.
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Export
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Affiliate_WP_Export Class
 *
 * @since 1.0
 */
class Affiliate_WP_Export {
	/**
	 * Our export type. Used for export-type specific filters/actions.
	 * @var string
	 * @since 1.0
	 */
	public $export_type = 'default';

	/**
	 * Capability needed to perform the current export.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $capability = 'export_affiliate_data';

	/**
	 * Can we export?
	 *
	 * @access public
	 * @since 1.0
	 * @return bool Whether we can export or not
	 */
	public function can_export() {
		/**
		 * Filters the capability needed to perform an export.
		 *
		 * @param string $capability Capability needed to perform an export.
		 */
		return (bool) current_user_can( apply_filters( 'affwp_export_capability', $this->capability ) );
	}

	/**
	 * Set the export headers
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function headers() {
		ignore_user_abort( true );

		if ( ! affwp_is_func_disabled( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=affiliate-wp-export-' . $this->export_type . '-' . date( 'm-d-Y' ) . '.csv' );
		header( "Expires: 0" );
	}

	/**
	 * Set the CSV columns
	 *
	 * @access public
	 * @since 1.0
	 * @return array $cols All the columns
	 */
	public function csv_cols() {
		$cols = array(
			'id'   => __( 'ID',   'affiliate-wp' ),
			'date' => __( 'Date', 'affiliate-wp' )
		);
		return $cols;
	}

	/**
	 * Retrieve the CSV columns
	 *
	 * @access public
	 * @since 1.0
	 * @return array $cols Array of the columns
	 */
	public function get_csv_cols() {
		$cols = $this->csv_cols();

		/**
		 * Filters the available CSV export columns for this export.
		 *
		 * This dynamic filter is appended with he export type string, for example:
		 *
		 *     `affwp_export_csv_cols_affiliates`
		 *
		 * @param $cols The export columns available.
		 */
		return apply_filters( 'affwp_export_csv_cols_' . $this->export_type, $cols );
	}

	/**
	 * Output the CSV columns
	 *
	 * @access public
	 * @since 1.0
	 * @uses Affiliate_WP_Export::get_csv_cols()
	 * @return void
	 */
	public function csv_cols_out() {
		$cols = $this->get_csv_cols();
		$i = 1;
		foreach( $cols as $col_id => $column ) {
			echo '"' . $column . '"';
			echo $i == count( $cols ) ? '' : ',';
			$i++;
		}
		echo "\r\n";
	}

	/**
	 * Retrieves the data being exported.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return array $data Data for Export
	 */
	public function get_data() {
		// Just a sample data array
		$data = array(
			0 => array(
				'id'   => '',
				'data' => date( 'F j, Y' )
			),
			1 => array(
				'id'   => '',
				'data' => date( 'F j, Y' )
			)
		);

		return $data;
	}

	/**
	 * Prepares a batch of data for export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param array $data Export data.
	 * @return array Filtered export data.
	 */
	public function prepare_data( $data ) {
		/**
		 * Filters the export data.
		 *
		 * The data set will differ depending on which exporter is currently in use.
		 *
		 * @param array $data Export data.
		 */
		$data = apply_filters( 'affwp_export_get_data', $data );

		/**
		 * Filters the export data for a given export type.
		 *
		 * The dynamic portion of the hook name, `$this->export_type`, refers to the export type.
		 *
		 * @param array $data Export data.
		 */
		$data = apply_filters( 'affwp_export_get_data_' . $this->export_type, $data );

		return $data;
	}

	/**
	 * Output the CSV rows
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function csv_rows_out() {
		$data = $this->prepare_data( $this->get_data() );

		$cols = $this->get_csv_cols();

		// Output each row
		foreach ( $data as $row ) {
			$i = 1;
			foreach ( $row as $col_id => $column ) {
				// Make sure the column is valid
				if ( array_key_exists( $col_id, $cols ) ) {
					echo '"' . $column . '"';
					echo $i == count( $cols ) + 1 ? '' : ',';
				}

				$i++;
			}
			echo "\r\n";
		}
	}

	/**
	 * Perform the export
	 *
	 * @access public
	 * @since 1.0
	 * @uses Affiliate_WP_Export::can_export()
	 * @uses Affiliate_WP_Export::headers()
	 * @uses Affiliate_WP_Export::csv_cols_out()
	 * @uses Affiliate_WP_Export::csv_rows_out()
	 * @return void
	 */
	public function export() {
		if ( ! $this->can_export() ) {
			wp_die( __( 'You do not have permission to export data.', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
		}
		// Set headers
		$this->headers();

		// Output CSV columns (headers)
		$this->csv_cols_out();

		// Output CSV rows
		$this->csv_rows_out();

		/**
		 * Fires at the end of an export.
		 *
		 * The dynamic portion of the hook name, `$this->export_type`, refers to
		 * the export type set by the extending sub-class.
		 *
		 * @since 1.9
		 * @since 1.9.2 Renamed to 'affwp_export_type_end' to prevent a conflict with another
		 *              dynamic hook.
		 *
		 * @param Affiliate_WP_Export $this Affiliate_WP_Export instance.
		 */
		do_action( "affwp_export_{$this->export_type}_end", $this );
		exit;
	}
}
