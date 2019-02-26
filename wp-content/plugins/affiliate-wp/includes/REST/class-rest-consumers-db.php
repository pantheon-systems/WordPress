<?php
namespace AffWP\REST\Consumer;

/**
 * Database class for managing and interacting with REST consumers.
 *
 * @since 1.9
 *
 * @see Affiliate_WP_DB
 */
class Database extends \Affiliate_WP_DB {

	/**
	 * Cache group for queries.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $cache_group = 'consumers';

	/**
	 * Object type to query for.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $query_object_type = 'AffWP\REST\Consumer';

	/**
	 * Constructor.
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct() {
		global $wpdb;

		// Site-level table.
		$this->table_name  = $wpdb->prefix . 'affiliate_wp_rest_consumers';

		$this->primary_key = 'consumer_id';
		$this->version     = '1.0';
	}

	/**
	 * Retrieves a REST consumer object.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @see Affiliate_WP_DB::get_core_object()
	 *
	 * @param int|object|AffWP\REST\Consumer $consumer Consumer user ID or consumer object.
	 * @return AffWP\REST\Consumer|null Consumer object, otherwise null.
	 */
	public function get_object( $consumer ) {
		return $this->get_core_object( $consumer, $this->query_object_type );
	}

	/**
	 * Retrieves table columns and data types.
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function get_columns() {
		return array(
			'consumer_id' => '%d',
			'user_id'     => '%d',
			'token'       => '%s',
			'public_key'  => '%s',
			'secret_key'  => '%s',
		);
	}

	/**
	 * Retrieves default column values.
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function get_column_defaults() {
		return array(
			'user_id' => get_current_user_id()
		);
	}

	/**
	 * Retrieves consumers from the database.
	 *
	 * @access  public
	 * @since   1.9
	 *
	 * @param array $args {
	 *     Optional. Arguments for querying consumers. Default empty array.
	 *
	 *     @type int          $number      Maximum number of consumers to query for. Default 20.
	 *     @type int          $offset      Number of consumers to offset the query for. Default 0.
	 *     @type int|array    $consumer_id Specific consumer ID or array of IDs to query for. Default 0 (ignored).
	 *     @type int|array    $user_id     User ID or array of IDs to query consumers for. Default 0 (ignored).
	 *     @type string       $token       Token to retrieve a specific consumer for.
	 *     @type string       $public_key  Public key to retrieve a specific consumer for.
	 *     @type string       $secret_key  Secret key to retrieve a specific consumer for.
	 *     @type string       $order       How to order returned consumer results. Accepts 'ASC' or 'DESC'.
	 *                                     Default 'DESC'.
	 *     @type string       $orderby     Consumers table column to order results by. Default 'consumer_id'.
	 *     @type string|array $fields      Specific fields to retrieve. Accepts 'ids', a single consumer field, or an
	 *                                     array of fields. Default '*' (all).
	 * }
	 * @param bool  $count Optional. Whether to return only the total number of results found. Default false.
	 * @return array|int Array of consumer objects or field(s) (if found) or integer if `$count` is true.
	 */
	public function get_consumers( $args = array(), $count = false ) {
		global $wpdb;

		$defaults = array(
			'number'       => 20,
			'offset'       => 0,
			'consumer_id'  => 0,
			'user_id'      => 0,
			'token'        => '',
			'public_key'   => '',
			'secret_key'   => '',
			'order'        => 'DESC',
			'orderby'      => 'consumer_id',
			'fields'       => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$where = $join = '';

		// Specific consumers.
		if ( ! empty( $args['consumer_id'] ) ) {

			$where .= empty( $where ) ? "WHERE " : "AND ";

			if ( is_array( $args['consumer_id'] ) ) {
				$consumers = implode( ',', array_map( 'intval', $args['consumer_id'] ) );
			} else {
				$consumers = intval( $args['consumer_id'] );
			}

			$where .= "`consumer_id` IN ( {$consumers} ) ";
		}

		// Specific consumer user IDs.
		if ( ! empty( $args['user_id'] ) ) {

			$where .= empty( $where ) ? "WHERE " : "AND ";

			if ( is_array( $args['user_id'] ) ) {
				$user_ids = implode( ',', array_map( 'intval', $args['user_id'] ) );
			} else {
				$user_ids = intval( $args['user_id'] );
			}

			$where .= "`user_id` IN( {$user_ids} ) ";

		}

		$orderby = array_key_exists( $args['orderby'], $this->get_columns() ) ? $args['orderby'] : $this->primary_key;

		// There can be only two orders.
		if ( 'DESC' === strtoupper( $args['order'] ) ) {
			$order = 'DESC';
		} else {
			$order = 'ASC';
		}

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
				$callback = 'affwp_get_rest_consumer';
			}
		}

		$key = ( true === $count ) ? md5( 'affwp_consumers_count' . serialize( $args ) ) : md5( 'affwp_consumers_' . serialize( $args ) );

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
	 * Retrieves the number of results found for a given query.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param array $args Arguments for the get_consumers() method.
	 * @return int Number of results.
	 */
	public function count( $args = array() ) {
		return $this->get_consumers( $args, true );
	}

	/**
	 * Retrieves the username associated with the consumer user ID.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function get_consumer_username( $user_id = 0 ) {
		if ( ! $user = get_user_by( 'id', $user_id ) ) {
			return false;
		}

		return $user->data->user_login;
	}

	/**
	 * Checks if a REST consumer exists.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param int $consumer_id Consumer ID or object.
	 * @return bool Whether the consumer exists.
	 */
	public function consumer_exists( $consumer_id ) {
		return (bool) affwp_get_rest_consumer( $consumer_id );
	}

	/**
	 * Adds a new REST consumer.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param array $args {
	 *     Data arguments for adding a new REST consumer. All arguments are required.
	 *
	 *     @type int    $user_id    Required. User ID used to correspond to the consumer.
	 *     @type string $token      Consumer token.
	 *     @type string $public_key Consumer public key.
	 *     @type string $secret_key Consumer secret key.
	 * }
	 * @return int|false Consumer ID if successfully added, otherwise false.
	 */
	public function add( $args ) {

		foreach ( $args as $argument ) {
			if ( empty( $argument ) ) {
				return false;
			}
		}

		$args['user_id'] = absint( $args['user_id'] );
		$args['token']   = sanitize_text_field( $args['token'] );
		$args['public_key'] = sanitize_text_field( $args['public_key'] );
		$args['secret_key'] = sanitize_text_field( $args['secret_key'] );

		$add = $this->insert( $args, 'consumer' );

		if ( $add ) {
			/**
			 * Fires immediately after a new REST consumer has been added.
			 *
			 * @since 1.9
			 *
			 * @param int $add ID for the newly-created REST consumer.
			 */
			do_action( 'affwp_insert_consumer', $add );

			return $add;
		}

		return false;

	}

	/**
	 * Create the table
	 *
	 * @access public
	 * @since  1.0
	 */
	public function create_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE {$this->table_name} (
			consumer_id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			token varchar(32) NOT NULL,
			public_key varchar(32) NOT NULL,
			secret_key varchar(32) NOT NULL,
			PRIMARY KEY  (consumer_id),
			KEY user_id (user_id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}
