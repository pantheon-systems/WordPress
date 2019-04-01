<?php
/**
 * Affiliate_WP_DB base class.
 *
 * The base class for all core objects.
 *
 * @since  1.9
 */
abstract class Affiliate_WP_DB {

	/**
	 * Database table name.
	 *
	 * @access public
	 * @var    string
	 */
	public $table_name;

	/**
	 * Database version.
	 *
	 * @access public
	 * @var    string
	 */
	public $version;

	/**
	 * Primary key (unique field) for the database table.
	 *
	 * @since public
	 * @var   string
	 */
	public $primary_key;

	/**
	 * Object type to query for.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $query_object_type = 'stdClass';

	/**
	 * Constructor.
	 *
	 * Sub-classes should define $table_name, $version, and $primary_key here.
	 *
	 * @access public
	 */
	public function __construct() {}

	/**
	 * Retrieves the list of columns for the database table.
	 *
	 * Sub-classes should define an array of columns here.
	 *
	 * @access public
	 * @return array List of columns.
	 */
	public function get_columns() {
		return array();
	}

	/**
	 * Retrieves column defaults.
	 *
	 * Sub-classes can define default for any/all of columns defined in the get_columns() method.
	 *
	 * @access public
	 * @return array All defined column defaults.
	 */
	public function get_column_defaults() {
		return array();
	}

	/**
	 * Retrieves a row from the database based on a given row ID.
	 *
	 * Corresponds to the value of $primary_key.
	 *
	 * @param  int                    $row_id Row ID.
	 * @return array|null|object|void
	 */
	public function get( $row_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $this->primary_key = %s LIMIT 1;", $row_id ) );
	}

	/**
	 * Retrieves a row based on column and row ID.
	 *
	 * @access public
	 *
	 * @param  string       $column Column name. See get_columns().
	 * @param  int|string   $row_id Row ID.
	 * @return object|false Database query result object or false on failure.
	 */
	public function get_by( $column, $row_id ) {
		global $wpdb;

		if ( ! array_key_exists( $column, $this->get_columns() ) || empty( $row_id ) ) {
			return false;
		}

		if( empty( $column ) || empty( $row_id ) ) {
			return false;
		}

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $column = '%s' LIMIT 1;", $row_id ) );
	}

	/**
	 * Retrieves a value based on column name and row ID.
	 *
	 * @access public
	 *
	 * @param  string      $column Column name. See get_columns().
	 * @param  int|string  $row_id Row ID.
	 * @return string|null         Database query result (as string), or null on failure
	 */
	public function get_column( $column, $row_id ) {
		global $wpdb;

		if ( ! array_key_exists( $column, $this->get_columns() ) || empty( $row_id ) ) {
			return false;
		}

		return $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM $this->table_name WHERE $this->primary_key = '%s' LIMIT 1;", $row_id ) );
	}

	/**
	 * Retrieves one column value based on another given column and matching value.
	 *
	 * @access public
	 *
	 * @param  string $column       Column name. See get_columns().
	 * @param  string $column_where Column to match against in the WHERE clause.
	 * @param  $column_value        Value to match to the column in the WHERE clause.
	 * @return string|null          Database query result (as string), or null on failure
	 */
	public function get_column_by( $column, $column_where, $column_value ) {
		global $wpdb;

		if ( empty( $column ) || empty( $column_where ) || empty( $column_value )
			|| ! array_key_exists( $column, $this->get_columns() )
		) {
			return false;
		}

		return $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM $this->table_name WHERE $column_where = %s LIMIT 1;", $column_value ) );
	}

	/**
	 * Retrieves results for a variety of query types.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param array    $clauses  Compacted array of query clauses.
	 * @param array    $args     Query arguments.
	 * @param callable $callback Optional. Callback to run against results in the generic results case.
	 *                           Default empty.
	 * @return array|int|null|object Query results.
	 */
	public function get_results( $clauses, $args, $callback = '' ) {
		global $wpdb;


		if ( true === $clauses['count'] ) {

			$results = $wpdb->get_var(
				"SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$clauses['join']} {$clauses['where']};"
			);

			$results = absint( $results );

		} else {

			$fields = $clauses['fields'];

			// Run the query.
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT {$fields} FROM {$this->table_name} {$clauses['join']} {$clauses['where']} ORDER BY {$clauses['orderby']} {$clauses['order']} LIMIT %d, %d;",
					absint( $args['offset'] ),
					absint( $args['number'] )
				)
			);

			/*
			 * If the query is for a single field, pluck the field into an array.
			 *
			 * Note that only the single field was selected in the query, but get_results()
			 * returns an array of objects, thus the pluck.
			 */
			if ( '*' !== $fields && false === strpos( $fields, ',' ) ) {
				$results = wp_list_pluck( $results, $fields );
			}

			// Run the results through the fields-dictated callback.
			if ( ! empty( $callback ) && is_callable( $callback ) ) {
				$results = array_map( $callback, $results );
			}

		}

		return $results;
	}

	/**
	 * Inserts a new record into the database.
	 *
	 * Please note: inserting a record flushes the cache.
	 *
	 * @access public
	 *
	 * @param  array  $data Column data. See get_column_defaults().
	 * @param  string $type Optional. Data type context, e.g. 'affiliate', 'creative', etc. Default empty.
	 * @return int          ID for the newly inserted record.
	 */
	public function insert( $data, $type = '' ) {
		global $wpdb;

		// Set default values
		$data = wp_parse_args( $data, $this->get_column_defaults() );

		/**
		 * Filters the data array to be used for inserting a new object of a given type.
		 *
		 * The dynamic portion of the hook, `$type`, refers to the data type, such as
		 * 'affiliate', 'creative', 'payout', etc.
		 *
		 * Passing a falsey value back via a filter callback will effectively allow
		 * insertion of the new object to be short-circuited. Example:
		 *
		 *     add_filter( 'affwp_pre_insert_payout_data', '__return_empty_array' );
		 *
		 * @since 2.1.9
		 *
		 * @param array $data Data to be inserted for the new object.
		 */
		$data = apply_filters( "affwp_pre_insert_{$type}_data", $data );

		if ( empty( $data ) ) {
			return false;
		}

		/**
		 * Fires immediately before an item has been created in the database.
		 *
		 * The dynamic portion of the hook name, `$type`, refers to the object type.
		 *
		 * @param array $data Array of object data.
		 */
		do_action( 'affwp_pre_insert_' . $type, $data );

		// Initialise column format array
		$column_formats = $this->get_columns();

		// Force fields to lower case
		$data = array_change_key_case( $data );

		// White list columns
		$data = array_intersect_key( $data, $column_formats );

		// Unslash data.
		$data = wp_unslash( $data );

		// Reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		$inserted = $wpdb->insert( $this->table_name, $data, $column_formats );

		if ( ! $inserted ) {
			return false;
		}

		$object = $this->get_core_object( $wpdb->insert_id, $this->query_object_type );

		// Prime the item cache, and invalidate related query caches.
		affwp_clean_item_cache( $object );

		/**
		 * Fires immediately after an item has been created in the database.
		 *
		 * @param int   $object_id Object ID.
		 * @param array $data      Array of object data.
		 */
		do_action( 'affwp_post_insert_' . $type, $object->{$this->primary_key}, $data );

		return $object->{$this->primary_key};
	}

	/**
	 * Updates an existing record in the database.
	 *
	 * @access public
	 *
	 * @param  int    $row_id Row ID for the record being updated.
	 * @param  array  $data   Optional. Array of columns and associated data to update. Default empty array.
	 * @param  string $where  Optional. Column to match against in the WHERE clause. If empty, $primary_key
	 *                        will be used. Default empty.
	 * @param  string $type   Optional. Data type context, e.g. 'affiliate', 'creative', etc. Default empty.
	 * @return bool           False if the record could not be updated, true otherwise.
	 */
	public function update( $row_id, $data = array(), $where = '', $type = '' ) {
		global $wpdb;

		// Row ID must be positive integer
		$row_id = absint( $row_id );

		$object = $this->get_core_object( $row_id, $this->query_object_type );

		if ( ! $object || empty( $data ) ) {
			return false;
		}

		if( empty( $where ) ) {
			$where = $this->primary_key;
		}

		// Initialise column format array
		$column_formats = $this->get_columns();

		// Force fields to lower case
		$data = array_change_key_case ( $data );

		// White list columns
		$data = array_intersect_key( $data, $column_formats );

		// Unslash data.
		$data = wp_unslash( $data );

		// Ensure primary key is not included in the $data array
		if( isset( $data[ $this->primary_key ] ) ) {
			unset( $data[ $this->primary_key ] );
		}

		// Reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		if ( empty( $data ) ) {
			return false;
		}

		if ( false === $wpdb->update( $this->table_name, $data, array( $where => $object->{$this->primary_key} ), $column_formats ) ) {
			return false;
		}

		// Invalidate and prime the item cache, and invalidate related query caches.
		affwp_clean_item_cache( $object );

		/**
		 * Fires immediately after an item has been successfully updated.
		 *
		 * @param array $data   Array of item data.
		 * @param int   $row_id Current item ID.
		 */
		do_action( 'affwp_post_update_' . $type, $data, $row_id );

		return true;
	}

	/**
	 * Deletes a record from the database.
	 *
	 * Please note: successfully deleting a record flushes the cache.
	 *
	 * @access public
	 *
	 * @param  int|string $row_id Row ID.
	 * @return bool               False if the record could not be deleted, true otherwise.
	 */
	public function delete( $row_id = 0, $type = '' ) {
		global $wpdb;

		// Row ID must be positive integer
		$row_id = absint( $row_id );
		$object = $this->get_core_object( $row_id, $this->query_object_type );

		if ( ! $object ) {
			return false;
		}

		/**
		 * Fires immediately before an item deletion has been attempted.
		 *
		 * @param string     $object Core object type.
		 * @param int|string $row_id Row ID.
		 */
		do_action( 'affwp_pre_delete_' . $type, $row_id );

		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM $this->table_name WHERE $this->primary_key = %d", $object->{$this->primary_key} ) ) ) {
			return false;
		}

		/**
		 * Fires immediately after an item has been successfully deleted.
		 *
		 * In the case of deletion, this must fire prior
		 * to the cache being invalidated below.
		 *
		 * @param string     $object Core object type.
		 * @param int|string $row_id Row ID.
		 */
		do_action( 'affwp_post_delete_' . $type, $row_id );

		// Invalidate the item cache along with related query caches.
		affwp_clean_item_cache( $object );

		return true;
	}

	/**
	 * Retrieves a core object instance based on the given type.
	 *
	 * @since  1.9
	 * @access protected
	 *
	 * @param  object|int   $instance Instance or object ID.
	 * @param  string       $class    Object class name.
	 * @return object|false           Object instance, otherwise false.
	 */
	protected function get_core_object( $instance, $object_class ) {
		// Back-compat for non-core objects.
		if ( 'stdClass' === $object_class ) {
			return $this->get( $instance );
		}

		if ( ! class_exists( $object_class ) ) {
			return false;
		}

		if ( $instance instanceof $object_class ) {
			$_object = $instance;
		} elseif ( is_object( $instance ) ) {
			if ( isset( $instance->{$this->primary_key} ) ) {
				$_object = new $object_class( $instance );
			} else {
				$_object = $object_class::get_instance( $instance );
			}
		} else {
			$_object = $object_class::get_instance( $instance );
		}

		if ( ! $_object ) {
			return false;
		}

		return $_object;
	}

	/**
	 * Parses a string of one or more valid object fields into a SQL-friendly format.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param string|array $fields Object fields.
	 * @return string SQL-ready fields list. If empty, default is '*'.
	 */
	public function parse_fields( $fields ) {

		$fields_sql = '';

		if ( ! is_array( $fields ) ) {
			$fields = array( $fields );
		}

		$count     = count( $fields );
		$whitelist = array_keys( $this->get_columns() );

		foreach ( $fields as $index => $field ) {
			if ( ! in_array( $field, $whitelist, true ) ) {
				unset( $fields[ $index ] );
			}
		}

		$fields_sql = implode( ', ', $fields );

		if ( empty ( $fields_sql ) ) {
			$fields_sql = '*';
		}

		return $fields_sql;
	}

	/**
	 * Prepares the date query section of the WHERE clause if set.
	 *
	 * @since 2.1.9
	 *
	 * @param string       $where WHERE clause for the query up to this point.
	 * @param string|array $date {
	 *     Date string or array of start and end dates to query by.
	 *
	 *     @type string $start Starting date string.
	 *     @type string $end   Ending date string.
	 * }
	 * @param string       $field Optional. Field to query by (this will be 'date' for all
	 *                            except affiliate queries). Default 'date'.
	 * @return string WHERE clause string for date conditions.
	 */
	public function prepare_date_query( $where, $date, $field = 'date' ) {

		if ( empty( $field ) ) {
			$field = 'date';
		} else {
			sanitize_key( $field );
		}

		$gmt_offset = affiliate_wp()->utils->wp_offset;

		if ( is_array( $date ) ) {

			if( ! empty( $date['start'] ) ) {

				$where .= empty( $where ) ? "WHERE " : "AND ";

				if ( false !== strpos( $date['start'], ':' ) ) {
					$format = 'Y-m-d H:i:s';
				} else {
					$format = 'Y-m-d 00:00:00';
				}

				$start = esc_sql( gmdate( $format, strtotime( $date['start'] ) - $gmt_offset ) );

				$where .= "`{$field}` >= '{$start}' ";
			}

			if ( ! empty( $date['end'] ) ) {

				$where .= empty( $where ) ? "WHERE " : "AND ";

				if ( false !== strpos( $date['end'], ':' ) ) {
					$format = 'Y-m-d H:i:s';
				} else {
					$format = 'Y-m-d 23:59:59';
				}

				$end = esc_sql( gmdate( $format, strtotime( $date['end'] ) - $gmt_offset ) );

				$where .= "`{$field}` <= '{$end}' ";
			}

		} else {

			$year  = gmdate( 'Y', strtotime( $date ) - $gmt_offset );
			$month = gmdate( 'm', strtotime( $date ) - $gmt_offset );
			$day   = gmdate( 'd', strtotime( $date ) - $gmt_offset );

			$where .= empty( $where ) ? "WHERE " : "AND ";
			$where .= "$year = YEAR ( {$field} ) AND $month = MONTH ( {$field} ) AND $day = DAY ( {$field} ) ";
		}

		return $where;
	}
}
