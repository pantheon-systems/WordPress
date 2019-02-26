<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Affiliate_WP_Direct_Links_DB extends Affiliate_WP_DB {

	/**
	 * Cache group for queries.
	 *
	 * @internal DO NOT change. This is used externally both as a cache group and shortcut
	 *           for accessing db class instances via affiliate_wp()->{$cache_group}->*.
	 *
	 * @access public
	 * @since  1.1
	 * @var    string
	 */
	public $cache_group = 'direct_links';

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0.0
	*/
	public function __construct() {
		global $wpdb;

		if ( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single direct links table for the whole network
			$this->table_name  = 'affiliate_wp_direct_links';
		} else {
			$this->table_name  = $wpdb->prefix . 'affiliate_wp_direct_links';
		}

		$this->primary_key = 'url_id';
		$this->version     = '1.0';

		add_action( 'plugins_loaded', array( $this, 'register_table' ), 11 );

	}

	/**
	 * Get table columns and data types
	 *
	 * @access  public
	 * @since   1.0.0
	*/
	public function get_columns() {
		return array(
			'url_id'       => '%d',
			'affiliate_id' => '%d',
			'url'          => '%s',
			'url_old'      => '%s',
			'url_code'     => '%d',
			'status'       => '%s',
			'date'         => '%s',
		);
	}

    /**
	 * Default column values
	 *
	 * @access  public
	 * @since   1.0.0
	*/
	public function get_column_defaults() {
		return array(
			'date' => date( 'Y-m-d H:i:s' ),
		);
	}

	/**
	 * Register the table with $wpdb so the metadata api can find it
	 *
	 * @access  public
	 * @since   1.0.0
	*/
	public function register_table() {
		global $wpdb;
		$wpdb->direct_links = $this->table_name;
	}

    /**
     * Add a new Direct Link URL
     *
     * @access  public
     * @since   1.0.0
    */
    public function add( $data = array() ) {

        $defaults = array(
            'date' => current_time( 'mysql' ),
        );

        $args = wp_parse_args( $data, $defaults );
        $add  = $this->insert( $args, 'direct_link' );

        if ( $add ) {
	        // Clean the query cache.
	        wp_cache_set( 'last_changed', microtime(), $this->cache_group );

	        /**
	         * Fires immediately after a direct link has been added.
	         *
	         * @since 1.0
	         *
	         * @param int ID of the newly-added direct link.
	         */
	        do_action( 'affwp_direct_link_tracking_insert_direct_link', $add );

            return $add;
        }

        return false;

    }

	/**
	 * Retrieve direct links from the database
	 *
	 * @access  public
	 * @since   1.0.0
	 * @param   array $args
	 * @param   bool  $count  Return only the total number of results found (optional)
	 */
	public function get_direct_links( $args = array(), $count = false ) {

		global $wpdb;

		$defaults = array(
			'number'       => 20,
			'offset'       => 0,
			'status'       => '',
			'affiliate_id' => 0,
			'orderby'      => $this->primary_key,
			'order'        => 'ASC',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$where = '';

		// Direct Links for specific affiliates
		if( ! empty( $args['affiliate_id'] ) ) {

			if( is_array( $args['affiliate_id'] ) ) {
				$affiliate_ids = implode( ',', array_map( 'intval', $args['affiliate_id'] ) );
			} else {
				$affiliate_ids = intval( $args['affiliate_id'] );
			}

			$where .= "WHERE `affiliate_id` IN( {$affiliate_ids} ) ";

		}

		// status
		if ( ! empty( $args['status'] ) ) {
			$status = esc_sql( $args['status'] );

			if ( ! empty( $where ) ) {
				$where .= "AND `status` = '" . $status . "' ";
			} else {
				$where .= "WHERE `status` = '" . $status . "' ";
			}
		}

		// order
		if ( 'ASC' === strtoupper( $args['order'] ) ) {
			$order = 'ASC';
		} else {
			$order = 'DESC';
		}

		// orderby
		$orderby = array_key_exists( $args['orderby'], $this->get_columns() ) ? $args['orderby'] : $this->primary_key;

		// Overload args values for the benefit of the cache.
		$args['orderby'] = $orderby;
		$args['order']   = $order;

		$key = ( true === $count ) ? md5( 'affwp_direct_links_count' . serialize( $args ) ) : md5( 'affwp_direct_links_' . serialize( $args ) );

		$last_changed = wp_cache_get( 'last_changed', $this->cache_group );
		if ( ! $last_changed ) {
			$last_changed = microtime();
			wp_cache_set( 'last_changed', $last_changed, $this->cache_group );
		}

		$cache_key = "{$key}:{$last_changed}";

		$results = wp_cache_get( $cache_key, $this->cache_group );

		if ( false === $results ) {

			if ( true === $count ) {

				$results = absint( $wpdb->get_var( "SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$where};" ) );

			} else {

				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT * FROM {$this->table_name} {$where} ORDER BY {$orderby} {$order} LIMIT %d, %d;",
						absint( $args['offset'] ),
						absint( $args['number'] )
					)
				);

			}

		}

		wp_cache_add( $cache_key, $results, $this->cache_group, HOUR_IN_SECONDS );

		return $results;

	}

	/**
	 * Updates a direct link.
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param  int    $row_id Row ID for the record being updated.
	 * @param  array  $data   Optional. Array of columns and associated data to update. Default empty array.
	 * @param  string $where  Optional. Column to match against in the WHERE clause. If empty, $primary_key
	 *                        will be used. Default empty.
	 * @param  string $type   Optional. Data type context, e.g. 'affiliate', 'creative', etc. Default empty.
	 * @return bool           False if the record could not be updated, true otherwise.
	 */
	public function update( $row_id, $data = array(), $where = '', $type = '' ) {
		$updated = parent::update( $row_id, $data, $where, $type );

		// Clean the query cache.
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );

		return $updated;
	}

	/**
	 * Deletes a record from the database.
	 *
	 * Please note: successfully deleting a record flushes the cache.
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param  int|string $row_id Row ID.
	 * @return bool               False if the record could not be deleted, true otherwise.
	 */
	public function delete( $row_id = 0, $type = '' ) {
		$deleted = parent::delete( $row_id, $type );

		if ( $deleted ) {
			// Clean the query cache.
			wp_cache_set( 'last_changed', microtime(), $this->cache_group );
		}

		return $deleted;
	}

	/**
	 * Return the number of results found for a given query
	 *
	 * @since 1.0.0
	 * @param  array  $args
	 * @return int
	 */
	public function count( $args = array() ) {
		return $this->get_direct_links( $args, true );
	}

	/**
	 * Create the table
	 *
	 * @access  public
	 * @since   1.0.0
	*/
	public function create_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE {$this->table_name} (
			url_id bigint(20) NOT NULL AUTO_INCREMENT,
			affiliate_id bigint(20) NOT NULL DEFAULT '0',
			url varchar(255) NOT NULL,
			url_old varchar(255) NOT NULL,
			url_code tinyint(20) NOT NULL,
			status tinytext NOT NULL,
			date datetime NOT NULL,
			PRIMARY KEY  (url_id),
			KEY affiliate_id (affiliate_id),
			KEY url (url)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}
