<?php

class Affiliate_WP_Campaigns_DB extends Affiliate_WP_DB {

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
	public $cache_group = 'campaigns';

	/**
	 * Setup our table name, primary key, and version
	 *
	 * This is a read-only VIEW of the visits table
	 *
	 * @param  int  $affiliate_id The ID of the affiliate to retrieve campaigns for
	 * @since  1.7
	 */
	public function __construct() {
		global $wpdb;

		if( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single visits table for the whole network
			$this->table_name  = 'affiliate_wp_campaigns';
		} else {
			$this->table_name  = $wpdb->prefix . 'affiliate_wp_campaigns';
		}
		$this->primary_key = 'affiliate_id';
		$this->version     = '1.0';
	}

	/**
	 * Retrieve campaigns and associated stats
	 *
	 * @param  int  $affiliate_id The ID of the affiliate to retrieve campaigns for
	 * @since  1.7
	 *
	 * @param array $args {
	 *     Optional. Arguments to retrieve campaigns.
	 *
	 *     @type int          $number           Number of campaigns to query for. Default 20.
	 *     @type int          $offset           Number of campaigns to offset the query for. Default 0.
	 *     @type int|array    $affiliate_id     Affiliate ID or array of IDs. Default 0.
	 *     @type string|array $campaign         Campaign or array of campaigns. Default empty.
	 *     @type string       $campaign_compare Comparison operator to use when querying for visits by campaign.
	 *                                          Accepts '=', '!=' or 'NOT EMPTY'. If 'EMPTY' or 'NOT EMPTY', `$campaign`
	 *                                          will be ignored and campaigns will simply be queried based on whether
	 *                                          the `campaign` column is empty or not. Default '='.
	 *     @type float|array  $conversion_rate  {
	 *         Specific conversion rate to query for or min/max range. If float, can be used with `$rate_compare`.
	 *         If array, `BETWEEN` is used.
	 *
	 *         @type float $min Minimum conversion rate to query for.
	 *         @type float $max Maximum conversion rate to query for.
	 *     }
	 *     @type string       $rate_compare     Comparison operator to use with `$conversion_rate`. Accepts '>', '<',
	 *                                          '>=', '<=', '=', or '!='. Default '='.
	 *     @type string       $order            How to order returned campaign results. Accepts 'ASC' or 'DESC'.
	 *                                          Default 'DESC'.
	 *     @type string       $orderby          Campaigns table column to order results by. Default 'affiliate_id'.
	 *     @type string       $fields           Specific fields to retrieve. Accepts 'ids' or '*' (all). Default '*'.
	 * }
	 * @param bool  $count Optional. Whether to return only the total number of results found. Default false.
	 * @return array|int Array of campaign objects or field(s) (if found) or integer if `$count` is true.
	 */
	public function get_campaigns( $args = array(), $count = false ) {
		global $wpdb;

		// Back-compat for the old $affiliate_id parameter.
		if ( is_numeric( $args ) ) {
			$affiliate_id = $args;
			$args = array(
				'affiliate_id' => $affiliate_id
			);
			unset( $affiliate_id );
		}

		$defaults = array(
			'number'           => 20,
			'offset'           => 0,
			'affiliate_id'     => 0,
			'campaign'         => '',
			'campaign_compare' => '=',
			'conversion_rate'  => 0,
			'rate_compare'     => '',
			'orderby'          => 'affiliate_id',
			'order'            => 'DESC',
			'fields'           => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$where = $join = '';

		// Specific affiliate(s).
		if( ! empty( $args['affiliate_id'] ) ) {

			$where .= empty( $where ) ? "WHERE " : "AND ";

			if( is_array( $args['affiliate_id'] ) ) {
				$affiliate_ids = implode( ',', array_map( 'intval', $args['affiliate_id'] ) );
			} else {
				$affiliate_ids = intval( $args['affiliate_id'] );
			}

			$where .= "`affiliate_id` IN( {$affiliate_ids} ) ";

		}

		// Specific campaign(s).
		if ( empty( $args['campaign_compare'] ) ) {
			$campaign_compare = '=';
		} else {
			if ( 'NOT EMPTY' === $args['campaign_compare'] ) {
				$campaign_compare = '!=';

				// Cancel out campaign value for comparison purposes.
				$args['campaign'] = '';
			} elseif ( 'EMPTY' === $args['campaign_compare'] ) {
				$campaign_compare = '=';

				// Cancel out campaign value for comparison purposes.
				$args['campaign'] = '';
			} else {
				$campaign_compare = $args['campaign_compare'];
			}
		}

		// visits for specific campaign
		if( ! empty( $args['campaign'] )
		    || ( empty( $args['campaign'] ) && '=' !== $campaign_compare )
		) {

			$where .= empty( $where ) ? "WHERE " : "AND ";

			if( is_array( $args['campaign'] ) ) {

				if ( '!=' === $campaign_compare ) {
					$where .= "`campaign` NOT IN(" . implode( ',', array_map( 'esc_sql', $args['campaign'] ) ) . ") ";
				} else {
					$where .= "`campaign` IN(" . implode( ',', array_map( 'esc_sql', $args['campaign'] ) ) . ") ";
				}

			} else {

				if ( empty( $args['campaign'] ) ) {
					$where .= "`campaign` {$campaign_compare} '' ";
				} else {
					$where .= "`campaign` {$campaign_compare} '{$args['campaign']}' ";
				}
			}

		}

		// Conversion rate.
		if ( ! empty( $args['conversion_rate'] ) ) {

			$rate = $args['conversion_rate'];

			$where .= empty( $where ) ? "WHERE " : "AND ";

			if ( is_array( $rate ) && ! empty( $rate['min'] ) && ! empty( $rate['max'] ) ) {

				$minimum = absint( $rate['min'] );
				$maximum = absint( $rate['max'] );

				$where .= "`conversion_rate` BETWEEN {$minimum} AND {$maximum} ";

			} else {

				$rate  = absint( $rate );
				$compare = '=';

				if ( ! empty( $args['rate_compare'] ) ) {
					$compare = $args['rate_compare'];

					if ( ! in_array( $compare, array( '>', '<', '>=', '<=', '=', '!=' ) ) ) {
						$compare = '=';
					}
				}

				$where .= " `conversion_rate` {$compare} {$rate}";
			}
		}

		// Orderby.
		switch( $args['orderby'] ) {
			case 'conversion_rate':
				$orderby = 'conversion_rate+0';
				break;

			case 'visits':
				$orderby = 'visits+0';
				break;

			case 'unique_visits':
				$orderby = 'unique_visits+0';
				break;

			case 'referrals':
				$orderby = 'referrals+0';
				break;

			default:
				$orderby = array_key_exists( $args['orderby'], $this->get_columns() ) ? $args['orderby'] : $this->primary_key;
				break;
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
		}

		$key = ( true === $count ) ? md5( 'affwp_campaigns_count' . serialize( $args ) ) : md5( 'affwp_campaigns_' . serialize( $args ) );

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
	 * @since  2.0.2
	 *
	 * @param array $args get_campaigns() arguments.
	 * @return int Number of campaigns for the given arguments.
	 */
	public function count( $args = array() ) {
		return $this->get_campaigns( $args, true );
	}

	/**
	 * Ensure insert method cannot be called
	 *
	 * @since  1.7
	 */
	public function insert( $data, $type = '' ) {
		_doing_it_wrong( 'insert', 'The AffiliateWP Campaigns table is a read-only VIEW. Data cannot be inserted.', '1.7' );
	}

	/**
	 * Ensure update method cannot be called
	 *
	 * @since  1.7
	 */
	public function update( $row_id, $data = array(), $where = '', $type = '' ) {
		_doing_it_wrong( 'update', 'The AffiliateWP Campaigns table is a read-only VIEW. Data cannot be updated.', '1.7' );
	}

	/**
	 * Ensure delete method cannot be called
	 *
	 * @since  1.7
	 */
	public function delete( $row_id = 0, $type = '' ) {
		_doing_it_wrong( 'delete', 'The AffiliateWP Campaigns table is a read-only VIEW. Data cannot be deleted.', '1.7' );
	}

	/**
	 * Create the view
	 *
	 * @since  1.7
	 */
	public function create_view() {

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		if( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single visits table for the whole network
			$visits_db  = 'affiliate_wp_visits';
		} else {
			$visits_db  = $wpdb->prefix . 'affiliate_wp_visits';
		}

		$sql = "CREATE OR REPLACE VIEW $this->table_name AS
				SELECT affiliate_id,
					campaign,
					COUNT(url) as visits,
					COUNT(DISTINCT url) as unique_visits,
					SUM(IF(referral_id<>0,1,0)) as referrals,
					ROUND((SUM(IF(referral_id<>0,1,0))/COUNT(url)) * 100, 2) as conversion_rate
				FROM $visits_db GROUP BY affiliate_id, campaign;";

		$wpdb->query( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}
}
