<?php
/**
 * Core class that implements a database layer for payouts.
 *
 * @since 1.9
 *
 * @see \Affiliate_WP_DB
 *
 * @property-read \AffWP\Affiliate\Payout\REST\v1\Endpoints $REST Affiliates REST endpoints.
 */
class Affiliate_WP_Payouts_DB extends Affiliate_WP_DB {

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
	public $cache_group = 'payouts';

	/**
	 * Object type to query for.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $query_object_type = 'AffWP\Affiliate\Payout';

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since  1.9
	*/
	public function __construct() {
		global $wpdb, $wp_version;

		if( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single payouts table for the whole network.
			$this->table_name  = 'affiliate_wp_payouts';
		} else {
			$this->table_name  = $wpdb->prefix . 'affiliate_wp_payouts';
		}
		$this->primary_key = 'payout_id';
		$this->version     = '1.0';

		// REST endpoints.
		if ( version_compare( $wp_version, '4.4', '>=' ) ) {
			$this->REST = new \AffWP\Affiliate\Payout\REST\v1\Endpoints;
		}
	}

	/**
	 * Retrieves a payout object.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @see Affiliate_WP_DB::get_core_object()
	 *
	 * @param int $payout Payout ID or object.
	 * @return AffWP\Affiliate\Payout|false Payout object, null otherwise.
	 */
	public function get_object( $payout ) {
		return $this->get_core_object( $payout, $this->query_object_type );
	}

	/**
	 * Retrieves table columns and date types.
	 *
	 * @access public
	 * @since  1.9
	*/
	public function get_columns() {
		return array(
			'payout_id'     => '%d',
			'affiliate_id'  => '%d',
			'referrals'     => '%s',
			'amount'        => '%s',
			'owner'         => '%d',
			'payout_method' => '%s',
			'status'        => '%s',
			'date'          => '%s',
		);
	}

	/**
	 * Retrieves default column values.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function get_column_defaults() {
		return array(
			'affiliate_id' => 0,
			'owner'        => 0,
			'status'       => 'paid',
			'date'         => gmdate( 'Y-m-d H:i:s' ),
		);
	}

	/**
	 * Adds a new single payout.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param array $args {
	 *     Optional. Array of arguments for adding a new payout. Default empty array.
	 *
	 *     @type int        $affiliate_id  Affiliate ID the payout should be associated with.
	 *     @type array      $referrals     Referral ID or array of IDs to associate the payout with.
	 *     @type float      $amount        Payout amount.
	 *     @type int        $owner         ID of the user who generated the payout. Default is the ID
	 *                                     of the current user.
	 *     @type string     $payout_method Payout method.
	 *     @type string     $status        Payout status. Will be 'paid' unless there's a problem.
	 *     @type int|string $date          Date string or timestamp for when the payout was created.
	 * }
	 * @return int|false Payout ID if successfully added, otherwise false.
	 */
	public function add( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'affiliate_id'  => 0,
			'referrals'     => array(),
			'amount'        => 0,
			'payout_method' => '',
			'owner'         => get_current_user_id(),
			'status'        => 'paid',
		) );

		$args['affiliate_id'] = absint( $args['affiliate_id'] );

		if ( ! affiliate_wp()->affiliates->affiliate_exists( $args['affiliate_id'] ) ) {
			return false;
		}

		if ( ! empty( $args['payout_method'] ) ) {
			$args['payout_method'] = sanitize_text_field( $args['payout_method'] );
		}

		/**
		 * Filters the payout method when adding a payout.
		 *
		 * @since 1.9
		 *
		 * @param string $payout_method Payout method.
		 * @param array  $args          Data for adding a payout.
		 */
		$args['payout_method'] = apply_filters( 'affwp_add_payout_method', $args['payout_method'], $args );

		if ( ! empty( $args['status'] ) ) {
			$args['status'] = sanitize_key( $args['status'] );
		}

		if ( is_array( $args['referrals'] ) ) {
			$args['referrals'] = array_map( 'absint', $args['referrals'] );
		} else {
			$args['referrals'] = (array) absint( $args['referrals'] );
		}

		$referrals = array();

		foreach ( $args['referrals'] as $referral_id ) {
			if ( $referral = affwp_get_referral( $referral_id ) ) {
				// Only keep it if the referral is real and the affiliate IDs match.
				if ( $args['affiliate_id'] === $referral->affiliate_id ) {
					$referrals[] = $referral;
				}
			}
		}

		if ( ! empty( $args['amount'] ) ) {
			$args['amount'] = affwp_sanitize_amount( $args['amount'] );
		} else {
			$amount = 0;

			foreach ( $referrals as $referral ) {
				$amount += $referral->amount;
			}
			$args['amount'] = $amount;
		}

		if ( empty( $args['date'] ) ) {
			unset( $args['date'] );
		} else {
			$time = strtotime( $args['date'] );

			$args['date'] = gmdate( 'Y-m-d H:i:s', $time - affiliate_wp()->utils->wp_offset );
		}

		if ( empty( $referrals ) ) {
			$add = false;
		} else {
			$args['referrals'] = implode( ',', wp_list_pluck( $referrals, 'referral_id' ) );

			$add = $this->insert( $args, 'payout' );
		}

		if ( $add ) {
			/**
			 * Fires immediately after a payout has been successfully inserted.
			 *
			 * @since 1.9
			 *
			 * @param int $add New payout ID.
			 */
			do_action( 'affwp_insert_payout', $add );

			// Add the payout IDs to the referral records.
			foreach ( $referrals as $referral ) {
				if ( affiliate_wp()->referrals->update( $referral->ID, array( 'payout_id' => $add ), '', 'referral' ) ) {
					affwp_set_referral_status( $referral, 'paid' );
				}
			}

			return $add;
		}

		return false;
	}

	/**
	 * Builds an associative array of affiliate IDs to their corresponding referrals.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param array  $referrals Array of referral IDs.
	 * @param string $status    Optional. Required referral status. Pass an empty string to disable.
	 *                          Default 'paid'.
	 * @return array Associative array of affiliates to referral IDs where affiliate IDs
	 *               are the index with a sub-array of corresponding referral IDs. Referrals
	 *               with a status other than 'paid' will be skipped.
	 */
	public function get_affiliate_ids_by_referrals( $referrals, $status = 'paid' ) {
		$referrals = array_map( 'affwp_get_referral', $referrals );

		$affiliates = array();

		foreach ( $referrals as $referral ) {
			if ( ! $referral || ( ! empty( $status ) && $status !== $referral->status ) ) {
				continue;
			}

			$affiliates[ $referral->affiliate_id ][] = $referral->ID;
		}

		return $affiliates;
	}

	/**
	 * Builds an array of payout IDs given an associative array of affiliate IDS to their
	 * corresponding referral IDs.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param array $affiliates Associative array of affiliate IDs to their corresponding
	 *                          referral IDs.
	 * @return array List of payout IDs for all referrals.
	 */
	public function get_payout_ids_by_affiliates( $affiliates ) {
		$payout_ids = array();

		if ( ! empty( $affiliates ) ) {
			foreach ( $affiliates as $affiliate => $referrals ) {
				foreach ( $referrals as $referral ) {
					$payout_ids[] = (int) affiliate_wp()->referrals->get_column( 'payout_id', $referral );
				}
			}
		}

		return array_unique( $payout_ids );
	}

	/**
	 * Retrieves all payout IDs for a set of referrals, regardless of affiliate association.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param array  $referrals Array of referral IDs.
	 * @param string $status    Optional. Required referral status. Pass an empty string to disable.
	 *                          Default 'paid'.
	 * @return array Array of payout IDs.
	 */
	public function get_payout_ids_by_referrals( $referrals, $status = 'paid' ) {
		return $this->get_payout_ids_by_affiliates( $this->get_affiliate_ids_by_referrals( $referrals, $status ) );
	}

	/**
	 * Retrieve payouts from the database
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param array $args {
	 *     Optional. Arguments for querying affiliates. Default empty array.
	 *
	 *     @type int          $number         Number of payouts to query for. Default 20.
	 *     @type int          $offset         Number of payouts to offset the query for. Default 0.
	 *     @type int|array    $payout_id      Payout ID or array of payout IDs to explicitly retrieve. Default 0.
	 *     @type int|array    $affiliate_id   Affiliate ID or array of affiliate IDs to retrieve payouts for. Default 0.
	 *     @type int|array    $referrals      Referral ID or array of referral IDs to retrieve payouts for.
	 *                                        Default empty array.
	 *     @type float|array  $amount {
	 *         Payout amount to retrieve payouts for or min/max range to retrieve payouts for.
	 *         Default 0.
	 *
	 *         @type float $min Minimum payout amount.
	 *         @type float $max Maximum payout amount. Use -1 for no limit.
	 *     }
	 *     @type string       $amount_compare Comparison operator to use in coordination with with $amount when passed
	 *                                        as a float or string. Accepts '>', '<', '>=', '<=', '=', or '!='.
	 *                                        Default '='.
	 *     @type string       $payout_method  Payout method to retrieve payouts for. Default empty (all).
	 *     @type string|array $date {
	 *         Date string or start/end range to retrieve payouts for.
	 *
	 *         @type string $start Start date to retrieve payouts for.
	 *         @type string $end   End date to retrieve payouts for.
	 *     }
	 *     @type int|array    $owner          ID or array of IDs for users who generated payouts. Default empty.
	 *     @type string       $status         Payout status. Default is 'paid' unless there's a problem.
	 *     @type string       $order          How to order returned payout results. Accepts 'ASC' or 'DESC'.
	 *                                        Default 'DESC'.
	 *     @type string       $orderby        Payouts table column to order results by. Accepts any AffWP\Affiliate\Payout
	 *                                        field. Default 'payout_id'.
	 *     @type string|array $fields         Specific fields to retrieve. Accepts 'ids', a single payout field, or an
	 *                                        array of fields. Default '*' (all).
	 * }
	 * @param bool  $count Optional. Whether to return only the total number of results found. Default false.
	 * @return array|int Array of payout objects or field(s) (if found), int if `$count` is true.
	 */
	public function get_payouts( $args = array(), $count = false ) {
		global $wpdb;

		$defaults = array(
			'number'         => 20,
			'offset'         => 0,
			'payout_id'      => 0,
			'affiliate_id'   => 0,
			'referrals'      => 0,
			'amount'         => 0,
			'amount_compare' => '=',
			'payout_method'  => '',
			'owner'          => '',
			'status'         => 'paid',
			'date'           => '',
			'order'          => 'DESC',
			'orderby'        => 'payout_id',
			'fields'         => '',
			'search'         => false,
		);

		$args = wp_parse_args( $args, $defaults );

		if( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$where = $join = '';

		// Specific payouts.
		if( ! empty( $args['payout_id'] ) ) {

			$where .= empty( $where ) ? "WHERE " : "AND ";

			if( is_array( $args['payout_id'] ) ) {
				$payout_ids = implode( ',', array_map( 'intval', $args['payout_id'] ) );
			} else {
				$payout_ids = intval( $args['payout_id'] );
			}

			$payout_ids = esc_sql( $payout_ids );

			$where .= "`payout_id` IN( {$payout_ids} ) ";

			unset( $payout_ids );
		}

		// Affiliate(s).
		if ( ! empty( $args['affiliate_id'] ) ) {

			$where .= empty( $where ) ? "WHERE " : "AND ";

			if ( is_array( $args['affiliate_id'] ) ) {
				$affiliates = implode( ',', array_map( 'intval', $args['affiliate_id'] ) );
			} else {
				$affiliates = intval( $args['affiliate_id'] );
			}

			$affiliates = esc_sql( $affiliates );

			$where .= "`affiliate_id` IN( {$affiliates} ) ";
		}

		// Referral ID(s).
		if ( ! empty( $args['referrals'] ) ) {

			if ( ! is_array( $args['referrals'] ) ) {
				$args['referrals'] = (array) $args['referrals'];
			}

			$payout_ids = $this->get_payout_ids_by_referrals( $args['referrals'] );

			if ( ! empty( $payout_ids ) ) {
				$where .= empty( $where ) ? "WHERE " : "AND ";

				$payout_ids = esc_sql( implode( ',', $payout_ids ) );

				if ( ! empty( $args['search'] ) ) {
					$where .= "`payout_id` LIKE '%%" . $payout_ids . "%%' ";
				} else {
					$where .= "`payout_id` IN( {$payout_ids} ) ";
				}
			}

			unset( $payout_ids );
		}

		// Amount.
		if ( ! empty( $args['amount'] ) ) {

			$amount = $args['amount'];

			$where .= empty( $where ) ? "WHERE " : "AND ";

			if ( is_array( $amount ) && ! empty( $amount['min'] ) && ! empty( $amount['max'] ) ) {

				$minimum = absint( $amount['min'] );
				$maximum = absint( $amount['max'] );

				$where .= "`amount` BETWEEN {$minimum} AND {$maximum} ";
			} else {

				$amount  = absint( $amount );
				$compare = '=';

				if ( ! empty( $args['amount_compare'] ) ) {
					$compare = $args['amount_compare'];

					if ( ! in_array( $compare, array( '>', '<', '>=', '<=', '=', '!=' ) ) ) {
						$compare = '=';
					}
				}

				$where .= "`amount` {$compare} {$amount} ";
			}

		}

		// Payout method.
		if ( ! empty( $args['payout_method'] ) ) {

			$where .= empty( $where ) ? "WHERE " : "AND ";

			$payment_method = esc_sql( $args['payout_method'] );

			$where .= "`payout_method` = '" . $payout_method . "' ";
		}

		// Owners.
		if ( ! empty( $args['owner'] ) ) {
			$where .= empty( $where ) ? "WHERE " : "AND ";

			if ( is_array( $args['owner'] ) ) {
				$owners = implode( ',', array_map( 'intval', $args['owner'] ) );
			} else {
				$owners = intval( $args['owner'] );
			}

			$owners = esc_sql( $owners );

			$where .= "`owner` IN( {$owners} ) ";
		}

		// Status.
		if ( ! empty( $args['status'] ) ) {

			$where .= empty( $where ) ? "WHERE " : "AND ";

			if ( ! in_array( $args['status'], array( 'paid', 'failed' ), true ) ) {
				$args['status'] = 'paid';
			}

			$status = esc_sql( $args['status'] );

			$where .= "`status` = '" . $status . "' ";
		}

		// Visits for a date or date range
		if( ! empty( $args['date'] ) ) {
			$where = $this->prepare_date_query( $where, $args['date'] );
		}

		$orderby = array_key_exists( $args['orderby'], $this->get_columns() ) ? $args['orderby'] : $this->primary_key;

		// Non-column orderby exception;
		if ( 'amount' === $args['orderby'] ) {
			$orderby = 'amount+0';
		}

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
				$callback = 'affwp_get_payout';
			}
		}

		$key = ( true === $count ) ? md5( 'affwp_payouts_count' . serialize( $args ) ) : md5( 'affwp_payouts_' . serialize( $args ) );

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
	 * @param array $args Arguments to pass to get_payouts().
	 * @return int Number of payouts.
	 */
	public function count( $args = array() ) {
		return $this->get_payouts( $args, true );
	}

	/**
	 * Checks if a payout exists.
	 *
	 * @access public
	 * @since  1.9
	*/
	public function payout_exists( $payout_id = 0 ) {

		global $wpdb;

		if ( empty( $payout_id ) ) {
			return false;
		}

		$payout = $wpdb->query( $wpdb->prepare( "SELECT 1 FROM {$this->table_name} WHERE {$this->primary_key} = %d;", $payout_id ) );

		return ! empty( $payout );
	}

	/**
	 * Retrieves an array of referral IDs stored for the payout.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param AffWP\Affiliate\Payout|int $payout Payout object or ID.
	 * @return array List of referral IDs.
	 */
	public function get_referral_ids( $payout ) {
		if ( ! $payout = affwp_get_payout( $payout ) ) {
			$referral_ids = array();
		} else {
			$referral_ids = array_map( 'intval', explode( ',', $payout->referrals ) );
		}
		return $referral_ids;
	}

	/**
	 * Creates the table.
	 *
	 * @access public
	 * @since  1.9
	*/
	public function create_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE " . $this->table_name . " (
			payout_id bigint(20) NOT NULL AUTO_INCREMENT,
			affiliate_id bigint(20) NOT NULL,
			referrals mediumtext NOT NULL,
			amount mediumtext NOT NULL,
			owner bigint(20) NOT NULL,
			payout_method tinytext NOT NULL,
			status tinytext NOT NULL,
			date datetime NOT NULL,
			PRIMARY KEY  (payout_id),
			KEY affiliate_id (affiliate_id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}
