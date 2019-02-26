<?php

/**
 * Class Affiliate_WP_DB_Affiliates
 *
 * @property-read \AffWP\Affiliate\REST\v1\Endpoints $REST Affiliates REST endpoints.
 */
class Affiliate_WP_DB_Affiliates extends Affiliate_WP_DB {

	/**
	 * The payouts class instance variable.
	 *
	 * @access public
	 * @since  1.9
	 * @var    Affiliate_WP_Payouts_DB
	 */
	public $payouts;

	/**
	 * Cache group for queries.
	 *
	 * @internal DO NOT change. This is used externally both as a cache group and shortcut
	 *           for accessing db class instances via affiliate_wp()->{$cache_group}->*.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $cache_group = 'affiliates';

	/**
	 * Object type to query for.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $query_object_type = 'AffWP\Affiliate';

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  1.0
	*/
	public function __construct() {
		global $wpdb, $wp_version;

		if( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single affiliate table for the whole network
			$this->table_name  = 'affiliate_wp_affiliates';
		} else {
			$this->table_name  = $wpdb->prefix . 'affiliate_wp_affiliates';
		}
		$this->primary_key = 'affiliate_id';
		$this->version     = '1.1';

		$this->payouts = new Affiliate_WP_Payouts_DB;

		// REST endpoints.
		if ( version_compare( $wp_version, '4.4', '>=' ) ) {
			$this->REST = new \AffWP\Affiliate\REST\v1\Endpoints;
		}
	}

	/**
	 * Retrieves an affiliate object.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @see Affiliate_WP_DB::get_core_object()
	 *
	 * @param int|AffWP\Affiliate $affiliate Affiliate ID or object.
	 * @return AffWP\Affiliate|false Affiliate object, otherwise false.
	 */
	public function get_object( $affiliate ) {
		return $this->get_core_object( $affiliate, $this->query_object_type );
	}

	/**
	 * Get table columns and date types
	 *
	 * @access public
	 * @since  1.0
	*/
	public function get_columns() {
		return array(
			'affiliate_id'    => '%d',
			'user_id'         => '%d',
			'rate'            => '%s',
			'rate_type'       => '%s',
			'payment_email'   => '%s',
			'status'          => '%s',
			'earnings'        => '%s',
			'unpaid_earnings' => '%s',
			'referrals'       => '%d',
			'visits'          => '%d',
			'date_registered' => '%s',
		);
	}

	/**
	 * Get default column values
	 *
	 * @access public
	 * @since  1.0
	*/
	public function get_column_defaults() {
		return array(
			'user_id'         => get_current_user_id(),
			'date_registered' => gmdate( 'Y-m-d H:i:s' ),
		);
	}

	/**
	 * Retrieve affiliates from the database
	 *
	 * @access public
	 * @since  1.0
	 * @since  1.8 The `$affiliate_id` argument was added. `$orderby` now accepts referral statuses.
	 *             and 'username'.
	 *
	 * @param array $args {
	 *     Optional. Arguments for querying affiliates. Default empty array.
	 *
	 *     @type int          $number       Number of affiliates to query for. Default 20.
	 *     @type int          $offset       Number of affiliates to offset the query for. Default 0.
	 *     @type int|array    $exclude      Affiliate ID or array of IDs to explicitly exclude.
	 *     @type int|array    $user_id      User ID or array of user IDs that correspond to the affiliate user.
	 *     @type int|array    $affiliate_id Affiliate ID or array of affiliate IDs to retrieve.
	 *     @type string       $status       Affiliate status. Default empty.
	 *     @type string       $order        How to order returned affiliate results. Accepts 'ASC' or 'DESC'.
	 *                                      Default 'DESC'.
	 *     @type string       $orderby      Affiliates table column to order results by. Also accepts 'paid',
	 *                                      'unpaid', 'rejected', or 'pending' referral statuses, 'name'
	 *                                      (user display_name), or 'username' (user user_login). Default 'affiliate_id'.
	 *     @type string|array $fields       Specific fields to retrieve. Accepts 'ids', a single affiliate field, or an
	 *                                      array of fields. Default '*' (all).
	 * }
	 * @param bool  $count Optional. Whether to return only the total number of results found. Default false.
	 * @return array|int Array of affiliate objects or field(s) (if found), int if `$count` is true.
	 */
	public function get_affiliates( $args = array(), $count = false ) {
		global $wpdb;

		$defaults = array(
			'number'       => 20,
			'offset'       => 0,
			'exclude'      => array(),
			'user_id'      => 0,
			'affiliate_id' => 0,
			'status'       => '',
			'order'        => 'DESC',
			'orderby'      => 'affiliate_id',
			'fields'       => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if( ! empty( $args['date_registered'] ) ) {
			$args['date'] = $args['date_registered'];
			unset( $args['date_registered'] );
		}

		if( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$where = '';

		if ( ! empty( $args['exclude'] ) ) {
			$where .= empty( $where ) ? "WHERE " : "AND ";

			if ( is_array( $args['exclude'] ) ) {
				$exclude = implode( ',', array_map( 'intval', $args['exclude'] ) );
			} else {
				$exclude = intval( $args['exclude'] );
			}

			$where .= "`affiliate_id` NOT IN( {$exclude} )";
		}

		// affiliates for specific users
		if ( ! empty( $args['user_id'] ) ) {

			if ( is_array( $args['user_id'] ) ) {
				$user_ids = implode( ',', array_map( 'intval', $args['user_id'] ) );
			} else {
				$user_ids = intval( $args['user_id'] );
			}

			$where .= "WHERE `user_id` IN( {$user_ids} ) ";

		}

		// Specific affiliates.
		if ( ! empty( $args['affiliate_id'] ) ) {
			if ( is_array( $args['affiliate_id'] ) ) {
				$affiliates = implode( ',', array_map( 'intval', $args['affiliate_id'] ) );
			} else {
				$affiliates = intval( $args['affiliate_id'] );
			}

			if ( empty( $args['user_id'] ) ) {
				$where .= "WHERE `affiliate_id` IN( {$affiliates} )";
			} else {
				$where .= "AND `affiliate_id` IN( {$affiliates} )";
			}
		}

		if ( ! empty( $args['status'] ) ) {
			$status = esc_sql( $args['status'] );

			if ( ! empty( $where ) ) {
				$where .= "AND `status` = '" . $status . "' ";
			} else {
				$where .= "WHERE `status` = '" . $status . "' ";
			}
		}

		if ( ! empty( $args['search'] ) ) {
			$search_value = $args['search'];

			if ( is_numeric( $search_value ) ) {
				$search = "`affiliate_id` IN( {$search_value} )";
			} elseif ( is_string( $search_value ) ) {

				// Searching by an affiliate's name or email
				if ( is_email( $search_value ) ) {

					$user    = get_user_by( 'email', $search_value );
					$user_id = $user ? absint( $user->ID ) : 0;
					$search  = "`user_id` = '" . $user_id . "' OR `payment_email` = '" . esc_sql( $search_value ) . "' ";

				} else {

					$users = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->users} WHERE display_name LIKE '%s' OR user_login LIKE '%s'", "%{$search_value}%", "%{$search_value}%" ) );
					$users = ! empty( $users ) ? implode( ',', array_map( 'intval', $users ) ) : 0;
					$search = "`user_id` IN( {$users} )";

				}
			}

			if ( ! empty( $search ) ) {

				if( ! empty( $where ) ) {
					$search = "AND " . $search;
				} else {
					$search = "WHERE " . $search;
				}

				$where .= $search;
			}

		}

		// Affiliates registered on a date or date range
		if( ! empty( $args['date'] ) ) {
			$where = $this->prepare_date_query( $where, $args['date'], 'date_registered' );
		}

		if ( 'DESC' === strtoupper( $args['order'] ) ) {
			$order = 'DESC';
		} else {
			$order = 'ASC';
		}

		$join = '';

		// Orderby.
		switch( $args['orderby'] ) {
			case 'date':
				// Registered date.
				$orderby = 'date_registered';
				break;

			case 'name':
				// User display_name.
				$orderby = 'u.display_name';
				$join = "a INNER JOIN {$wpdb->users} u ON a.user_id = u.ID";
				break;

			case 'username':
				// Username.
				$orderby = 'u.user_login';
				$join = "a INNER JOIN {$wpdb->users} u ON a.user_id = u.ID";
				break;

			case 'earnings':
				// Earnings.
				$orderby = 'earnings+0';
				break;

			case 'unpaid_earnings':
				// Unpaid earnings.
				$orderby = 'unpaid_earnings+0';
				break;

			case 'referrals':
				// Referrals.
				$orderby = 'referrals+0';
				break;

			case 'paid':
			case 'unpaid':
			case 'rejected':
			case 'pending':
				// If ordering by a referral status, do a sub-query to order by count.
				$status    = esc_sql( $args['orderby'] );
				$referrals = affiliate_wp()->referrals->table_name;

				$orderby  = "( SELECT COUNT(*) FROM {$referrals}";
				$orderby .= " WHERE ( {$this->table_name}.affiliate_id = {$referrals}.affiliate_id";
				$orderby .= " AND {$referrals}.status = '{$status}' ) )";
				break;

			default:
				// Check against the columns whitelist. If no match, default to $primary_key.
				$orderby = array_key_exists( $args['orderby'], $this->get_columns() ) ? $args['orderby'] : $this->primary_key;
				break;
		}

		// Overload args values for the benefit of the cache.
		$args['orderby'] = $orderby;
		$args['order']   = $order;

		$callback = '';

		if ( 'ids' === $args['fields'] ) {
			$fields   = "$this->primary_key";
			$callback = 'intval';
		} else {
			$fields = $this->parse_fields( $args['fields'] );

			if ( '*' === $fields ) {
				$callback = 'affwp_get_affiliate';
			}
		}

		$key = ( true === $count ) ? md5( 'affwp_affiliates_count' . serialize( $args ) ) : md5( 'affwp_affiliates_' . serialize( $args ) );

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
	 * @since  1.0
	 *
	 * @param array $args Optional. Any valid get_affiliates() arguments. Default empty array.
	 * @return int Number of affiliates found for the given arguments.
	 */
	public function count( $args = array() ) {
		return $this->get_affiliates( $args, true );
	}

	/**
	 * Retrieve the name of the affiliate
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param int|\AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
	 * @return string|null Affiliate name on success, otherwise null.
	*/
	public function get_affiliate_name( $affiliate = 0 ) {
		global $wpdb;

		if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
			return;
		}

		$cache_key = "affwp_affiliate_name_{$affiliate->ID}";

		$name = wp_cache_get( $cache_key, 'affiliates' );

		if( false === $name ) {
			$name = $wpdb->get_var( $wpdb->prepare( "SELECT u.display_name FROM {$wpdb->users} u INNER JOIN {$this->table_name} a ON u.ID = a.user_id WHERE a.affiliate_id = %d;", $affiliate->ID ) );
			wp_cache_set( $cache_key, $name, 'affiliates', HOUR_IN_SECONDS );
		}

		return $name;
	}

	/**
	 * Checks if an affiliate exists.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @param int|\AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
	 * @return bool True if the affiliate exists, otherwise false.
	*/
	public function affiliate_exists( $affiliate = 0 ) {
		global $wpdb;

		if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
			return false;
		}

		$exists = $wpdb->query( $wpdb->prepare( "SELECT 1 FROM {$this->table_name} WHERE {$this->primary_key} = %d;", $affiliate->ID ) );

		return ! empty( $exists );
	}

	/**
	 * Add a new affiliate
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @param array $args {
	 *     Optional. Array of arguments for adding a new affiliate. Default empty array.
	 *
	 *     @type string $status          Affiliate status. Default 'active'.
	 *     @type string $date_registered Date the affiliate was registered. Default is the current time.
	 *     @type string $rate            Affiliate-specific referral rate.
	 *     @type string $rate_type       Rate type. Accepts 'percentage' or 'flat'.
	 *     @type string $payment_email   Affiliate payment email.
	 *     @type int    $earnings        Affiliate earnings. Default 0.
	 *     @type int    $referrals       Number of affiliate referrals.
	 *     @type int    $visits          Number of visits.
	 *     @type int    $user_id         User ID used to correspond to the affiliate.
	 *     @type string $website_url     The affiliate's website URL.
	 * }
	 * @return int|false Affiliate ID if successfully added, otherwise false.
	*/
	public function add( $data = array() ) {

		$defaults = array(
			'status'          => 'active',
			'earnings'        => 0,
			'referrals'       => 0,
			'visits'          => 0
		);

		$args = wp_parse_args( $data, $defaults );

		if(  ! empty( $args['user_id'] ) && affiliate_wp()->affiliates->get_by( 'user_id', $args['user_id'] ) ) {
			return false;
		}

		if ( isset( $args['date_registered'] ) ) {

			if ( empty( $args['date_registered'] ) ) {
				unset( $args['date_registered'] );
			} else {
				$time = strtotime( $args['date_registered'] );

				$args['date_registered'] = gmdate( 'Y-m-d H:i:s', $time - affiliate_wp()->utils->wp_offset );
			}
		}

		if ( ! empty( $data['website_url'] ) ) {
			$args['website_url'] = sanitize_text_field( $data['website_url'] );
		}

		$add = $this->insert( $args, 'affiliate' );

		if ( $add ) {

			/**
			 * Fires immediately after an affiliate has been added to the database.
			 *
			 * @param int   $add  The new affiliate ID.
			 * @param array $args The arguments passed to the insert method.
			 */
			do_action( 'affwp_insert_affiliate', $add, $args );

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
			affiliate_id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			rate tinytext NOT NULL,
			rate_type tinytext NOT NULL,
			payment_email mediumtext NOT NULL,
			status tinytext NOT NULL,
			earnings mediumtext NOT NULL,
			unpaid_earnings mediumtext NOT NULL,
			referrals bigint(20) NOT NULL,
			visits bigint(20) NOT NULL,
			date_registered datetime NOT NULL,
			PRIMARY KEY  (affiliate_id),
			KEY user_id (user_id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}
