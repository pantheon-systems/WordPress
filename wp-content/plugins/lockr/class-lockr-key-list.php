<?php
/**
 * Create a table to display all keys currently stored in Lockr.
 *
 * @package Lockr
 */

// Admin Table for Lockr Key Management.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Create a table to display all keys currently stored in Lockr.
 */
class Lockr_Key_List extends WP_List_Table {

	/**
	 *  Get things started with the table.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Key', 'lockr' ),
				'plural'   => __( 'Keys', 'lockr' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 *  Text displayed when no key data is available.
	 */
	public function no_items() {
		esc_attr_e( 'No keys stored yet.', 'sp' );
	}

	/**
	 *  Format the data for the row item
	 *
	 * @param array $item The row item to display.
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s" value="%2$s" />',
			$this->_args['plural'] . '[]',
			$item->key_name
		);
	}

	/**
	 * Get columns and their names.
	 */
	public function get_columns() {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'key_label'    => __( 'Key Name' ),
			'key_abstract' => __( 'Key Value' ),
			'dev_abstract' => __( 'Dev Value' ),
			'time'         => __( 'Created' ),
			'edit'         => '',
		);

		return $columns;
	}

	/**
	 * Get sortable columns and their config.
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'key_label' => array( 'key_label', true ),
			'time'      => array( 'time', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Set the content for each row's column.
	 *
	 * @param array  $item The row item to be displayed.
	 * @param string $column_name The name of the column to put the data in.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'key_label':
				return $item->key_label;
			case 'key_abstract':
				return $item->key_abstract;
			case 'dev_abstract':
				return $item->dev_abstract;
			case 'time':
				return $item->time;
			case 'edit':
				if ( ! $item->auto_created ) {
					$url  = admin_url( 'admin.php?page=lockr-edit-key' );
					$url .= '&key=' . $item->key_name;
					return "<a href='$url' >edit</a>";
				}
		}
	}

	/**
	 * Get the data from the database to put into the table.
	 */
	public function prepare_items() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'lockr_keys';
		$order      = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
		$orderby    = ! empty( $_GET['orderby'] ) ? sanitize_sql_orderby( wp_unslash( $_GET['orderby'] ) . ' ' . $order ) : 'ASC';

		// Process any bulk actions first.
		$this->process_bulk_action();

		$query = "SELECT * FROM $table_name";

		if ( ! empty( $orderby ) & ! empty( $order ) ) {
			$query .= $wpdb->prepare( ' ORDER BY %s ', array( $orderby ) );
		}

		$totalitems = $wpdb->query( $query ); // WPCS: unprepared SQL OK.

		// First, lets decide how many records per page to show.
		$perpage = 20;

		// Which page is this?
		$paged = ! empty( $_GET['paged'] ) ? intval( $_GET['paged'] ) : '';
		// Page Number.
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}

		// How many pages do we have in total?
		$totalpages = ceil( $totalitems / $perpage );
		// Adjust the query to take pagination into account.
		if ( ! empty( $paged ) && ! empty( $perpage ) ) {
			$offset = ( $paged - 1 ) * $perpage;
			$query .= $wpdb->prepare( ' LIMIT %d,%d', array( (int) $offset, (int) $perpage ) );
		}

		// Register the pagination.
		$this->set_pagination_args(
			array(
				'total_items' => $totalitems,
				'total_pages' => $totalpages,
				'per_page'    => $perpage,
			)
		);

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $wpdb->get_results( $query ); // WPCS: unprepared SQL OK.
	}

	/**
	 * Delete a Lockr key.
	 *
	 * @param string $key_name machine name of the key.
	 */
	public static function delete_key( $key_name ) {
		lockr_delete_key( $key_name );
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => 'Delete',
		);

		return $actions;
	}

	/**
	 * Do the bulk action submitted.
	 */
	public function process_bulk_action() {
		if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
			$nonce        = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
			$nonce_action = 'bulk-' . $this->_args['plural'];
			if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
				wp_die( 'Lock it up!' );
			}
		}

		// If the delete bulk action is triggered.
		if ( ( isset( $_POST['action'] ) && 'bulk-delete' === $_POST['action'] )
			|| ( isset( $_POST['action2'] ) && 'bulk-delete' === $_POST['action2'] )
		) {
			if ( isset( $_POST['keys'] ) ) {
				$keys = array_map( 'sanitize_text_field', wp_unslash( $_POST['keys'] ) );
			} else {
				$keys = array();
			}

			if ( is_array( $keys ) ) {

				foreach ( $keys as $name ) {
					self::delete_key( $name );
					if ( 'lockr_default_key' === $name ) {
						update_option( 'lockr_default_deleted', true );
					}
					$message = esc_html( "You successfully deleted the $name key from Lockr." );
					echo "<div id='message' class='updated fade'><p><strong>" . esc_html( "You successfully deleted the $name key from Lockr." ) . '</strong></p></div>';
				}
			}
		}
	}
}
