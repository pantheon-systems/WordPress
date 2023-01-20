<?php
/**
 * List table for displaying all sessions.
 *
 * @package WPNPS
 */

namespace Pantheon_Sessions;

/**
 * List table for displaying all sessions.
 */
class List_Table extends \WP_List_Table {

	/**
	 * Prepare the items for the list table
	 */
	public function prepare_items() {
		global $wpdb;

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$per_page = 20;
		$paged    = ( isset( $_GET['paged'] ) ) ? (int) $_GET['paged'] : 1;
		$offset   = $per_page * ( $paged - 1 );

		$this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->pantheon_sessions ORDER BY datetime DESC LIMIT %d,%d", $offset, $per_page ) );
		$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->pantheon_sessions" );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

	}

	/**
	 * Message for no items found
	 */
	public function no_items() {
		_e( 'No sessions found.', 'wp-native-php-sessions' );
	}

	/**
	 * Get the columns in the list table
	 */
	public function get_columns() {
		return array(
			'session_id' => __( 'Session ID', 'wp-native-php-sessions' ),
			'user_id'    => __( 'User ID', 'wp-native-php-sessions' ),
			'ip_address' => __( 'IP Address', 'wp-native-php-sessions' ),
			'datetime'   => __( 'Last Active', 'wp-native-php-sessions' ),
			'data'       => __( 'Data', 'wp-native-php-sessions' ),
		);
	}

	/**
	 * Render a column value
	 *
	 * @param object $item        Session to display.
	 * @param string $column_name Name of the column.
	 */
	public function column_default( $item, $column_name ) {
		if ( 'data' === $column_name ) {
			return '<code>' . esc_html( $item->data ) . '</code>';
		} elseif ( 'session_id' === $column_name ) {
			$query_args = array(
				'action'  => 'pantheon_clear_session',
				'nonce'   => wp_create_nonce( 'pantheon_clear_session' ),
				'session' => $item->session_id,
			);
			$actions    = array(
				'clear' => '<a href="' . esc_url( add_query_arg( $query_args, admin_url( 'admin-ajax.php' ) ) ) . '">' . esc_html__( 'Clear', 'wp-native-php-sessions' ) . '</a>',
			);
			return esc_html( $item->session_id ) . $this->row_actions( $actions );
		} elseif ( 'datetime' === $column_name ) {
			// translators: Time ago.
			return esc_html( sprintf( esc_html__( '%s ago', 'wp-native-php-sessions' ), human_time_diff( strtotime( $item->datetime ) ) ) );
		} else {
			return esc_html( $item->$column_name );
		}
	}

}
