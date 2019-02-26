<?php
/**
 * Class Affiliate_WP_Creatives_DB
 *
 * @see Affiliate_WP_DB
 *
 * @property-read \AffWP\Creative\REST\v1\Endpoints $REST Creatives REST endpoints.
 */
class Affiliate_WP_Creatives_DB extends Affiliate_WP_DB {

	/**
	 * Cache group for queries.
	 *
	 * @internal DO NOT change. This is used externally both as a cache group and shortcut
	 *           for accessing db class instances via affiliate_wp()->{$cache_group}->*.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $cache_group = 'creatives';

	/**
	 * Object type to query for.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $query_object_type = 'AffWP\Creative';

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.2
	*/
	public function __construct() {
		global $wpdb, $wp_version;

		if ( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single creatives table for the whole network
			$this->table_name  = 'affiliate_wp_creatives';
		} else {
			$this->table_name  = $wpdb->prefix . 'affiliate_wp_creatives';
		}
		$this->primary_key = 'creative_id';
		$this->version     = '1.0';

		// REST endpoints.
		if ( version_compare( $wp_version, '4.4', '>=' ) ) {
			$this->REST = new \AffWP\Creative\REST\v1\Endpoints;
		}
	}

	/**
	 * Retrieves a creative object.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @see Affiliate_WP_DB::get_core_object()
	 *
	 * @param int|AffWP\Creative $creative Creative ID or object.
	 * @return AffWP\Creative|false Creative object, otherwise false.
	 */
	public function get_object( $creative ) {
		return $this->get_core_object( $creative, $this->query_object_type );
	}

	/**
	 * Database columns
	 *
	 * @access  public
	 * @since   1.2
	*/
	public function get_columns() {
		return array(
			'creative_id'  => '%d',
			'name'         => '%s',
			'description'  => '%s',
			'url'          => '%s',
			'text'         => '%s',
			'image'        => '%s',
			'status'       => '%s',
			'date'         => '%s',
		);
	}

	/**
	 * Default column values
	 *
	 * @access  public
	 * @since   1.2
	*/
	public function get_column_defaults() {
		return array(
			'date' => gmdate( 'Y-m-d H:i:s' ),
		);
	}

	/**
	 * Retrieves creatives from the database.
	 *
	 * @access public
	 * @since  1.2
	 *
	 * @param array $args {
	 *     Optional. Arguments for querying creatives. Default empty array.
	 *
	 *     @type int          $number      Number of creatives to query for. Default 20.
	 *     @type int          $offset      Number of creatives to offset the query for. Default 0.
	 *     @type int|array    $creative_id Creative ID or array of creative IDs to explicitly retrieve. Default 0.
	 *     @type string       $status      Creative status. Default empty (all).
	 *     @type string       $order       How to order returned creative results. Accepts 'ASC' or 'DESC'.
	 *                                     Default 'DESC'.
	 *     @type string       $orderby     Creatives table column to order results by. Accepts any AffWP\Creative
	 *                                     field. Default 'creative_id'.
	 *     @type string|array $fields      Specific fields to retrieve. Accepts 'ids', a single creative field, or an
	 *                                     array of fields. Default '*' (all).
	 * }
	 * @param bool $count Whether to retrieve only the total number of results found. Default false.
	 * @return array|int Array of creative objects or field(s) (if found), int if `$count` is true.
	 */
	public function get_creatives( $args = array(), $count = false ) {
		global $wpdb;

		$defaults = array(
			'number'      => 20,
			'offset'      => 0,
			'creative_id' => 0,
			'status'      => '',
			'orderby'     => $this->primary_key,
			'order'       => 'ASC',
			'fields'      => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$where = $join = '';

		// Specific creative ID or IDs.
		if ( ! empty( $args['creative_id'] ) ) {

			$where .= empty( $where ) ? "WHERE " : "AND ";

			if( is_array( $args['creative_id'] ) ) {
				$creatives = implode( ',', array_map( 'intval', $args['creative_id'] ) );
			} else {
				$creatives = intval( $args['creative_id'] );
			}

			$where .= "`creative_id` IN( {$creatives} ) ";
		}

		// Status.
		if ( ! empty( $args['status'] ) ) {

			$where .= empty( $where ) ? "WHERE " : "AND ";

			$status = esc_sql( $args['status'] );

			if ( ! empty( $where ) ) {
				$where .= "`status` = '" . $status . "' ";
			} else {
				$where .= "`status` = '" . $status . "' ";
			}
		}

		// Creatives for a date or date range.
		if( ! empty( $args['date'] ) ) {
			$where = $this->prepare_date_query( $where, $args['date'] );
		}

		// There can be only two orders.
		if ( 'ASC' === strtoupper( $args['order'] ) ) {
			$order = 'ASC';
		} else {
			$order = 'DESC';
		}

		$orderby = array_key_exists( $args['orderby'], $this->get_columns() ) ? $args['orderby'] : $this->primary_key;

		// Overload args values for the benefit of the cache.
		$args['orderby'] = $orderby;
		$args['order']   = $order;

		// Fields.
		$callback = '';

		if ( 'ids' === $args['fields'] ) {
			$fields   = "$this->primary_key";
			$callback = 'intval';
		} else {
			$fields = $this->parse_fields( $args['fields'] );

			if ( '*' === $fields ) {
				$callback = 'affwp_get_creative';
			}
		}

		$key = ( true === $count ) ? md5( 'affwp_creatives_count' . serialize( $args ) ) : md5( 'affwp_creatives_' . serialize( $args ) );

		$last_changed = wp_cache_get( 'last_changed', $this->cache_group );
		if ( ! $last_changed ) {
			$last_changed = microtime();
			wp_cache_set( 'last_changed', $last_changed, $this->cache_group );
		}

		$cache_key = "{$key}:{$last_changed}";

		$results = wp_cache_get( $cache_key, $this->cache_group );

		if ( false === $results ) {

			$clauses = compact( 'fields', 'join', 'where', 'orderby', 'order', 'count' );

			$results = $this->get_results( $clauses, $args, $callback );
		}

		wp_cache_add( $cache_key, $results, $this->cache_group, HOUR_IN_SECONDS );

		return $results;

	}

	/**
	 * Return the number of results found for a given query
	 *
	 * @param  array  $args
	 * @return int
	 */
	public function count( $args = array() ) {
		return $this->get_creatives( $args, true );
	}

	/**
	 * Add a new creative
	 *
	 * @access  public
	 * @since   1.2
	*/
	public function add( $data = array() ) {

		$defaults = array(
			'status' => 'active',
			'url'	 => '',
			'image'  => '',
		);

		$args = wp_parse_args( $data, $defaults );

		if ( empty( $args['date'] ) ) {
			unset( $args['date'] );
		} else {
			$time = strtotime( $args['date'] );

			$args['date'] = gmdate( 'Y-m-d H:i:s', $time - affiliate_wp()->utils->wp_offset );
		}

		$add = $this->insert( $args, 'creative' );

		if ( $add ) {
			/**
			 * Fires immediately after a creative has been added to the database.
			 *
			 * @param array $add The creative data being added.
			 */
			do_action( 'affwp_insert_creative', $add );
			return $add;
		}

		return false;

	}

	public function create_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE {$this->table_name} (
			creative_id bigint(20) NOT NULL AUTO_INCREMENT,
			name tinytext NOT NULL,
			description longtext NOT NULL,
			url varchar(255) NOT NULL,
			text tinytext NOT NULL,
			image varchar(255) NOT NULL,
			status tinytext NOT NULL,
			date datetime NOT NULL,
			PRIMARY KEY  (creative_id),
			KEY creative_id (creative_id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}
}
