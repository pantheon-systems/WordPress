<?php
namespace AffWP\Campaign\Admin\Reports;

use AffWP\Admin\Reports;

/**
 * Implements a core 'Campaigns' tab for the Reports screen.
 *
 * @since 1.9
 *
 * @see \AffWP\Admin\Reports\Tab
 */
class Tab extends Reports\Tab {

	/**
	 * Sets up the Campaigns tab for Reports.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function __construct() {
		$this->tab_id   = 'campaigns';
		$this->label    = __( 'Campaigns', 'affiliate-wp' );
		$this->priority = 0;
		$this->graph    = new \Affiliate_WP_Visits_Graph;

		$this->set_up_additional_filters();

		parent::__construct();
	}

	/**
	 * Registers the 'Best Converting Campaign' (all time) tile.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function best_converting_campaign_tile() {
		$affiliate_id = $this->affiliate_id ? $this->affiliate_id : 0;

		$top_campaign = affiliate_wp()->campaigns->get_campaigns( array(
			'orderby'          => 'conversion_rate',
			'campaign_compare' => 'NOT EMPTY',
			'affiliate_id'     => $affiliate_id,
			'number'           => 1,
		) );

		if ( ! empty( $top_campaign[0] ) ) {
			$campaign = $top_campaign[0];

			$affiliate_name = affwp_get_affiliate_name( $campaign->affiliate_id );

			if ( empty( $affiliate_name ) ) {
				$affiliate_name = affwp_get_affiliate_username( $campaign->affiliate_id );
			}

			$affiliate_link = affwp_admin_url( 'referrals', array(
				'affiliate_id' => $campaign->affiliate_id,
				'orderby'      => 'status',
				'order'        => 'ASC',
			) );

			$this->register_tile( 'best_converting_campaign', array(
				'label'           => __( 'Best Converting Campaign (All Time)', 'affiliate-wp' ),
				'data'            => empty( $campaign->campaign ) ? __( 'n/a', 'affiliate-wp' ) : $campaign->campaign,
				'comparison_data' => sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a> | Visits: %3$d', 'affiliate-wp' ),
					esc_url( $affiliate_link ),
					$affiliate_name,
					$campaign->visits
				)
			) );
		} else {

			if ( $this->affiliate_id ) {

				$affiliate_name = affwp_get_affiliate_name( $this->affiliate_id );

				if ( empty( $affiliate_name ) ) {
					$affiliate_name = affwp_get_affiliate_username( $this->affiliate_id );
				}

				$affiliate_link = affwp_admin_url( 'referrals', array(
					'affiliate_id' => $this->affiliate_id,
					'orderby'      => 'status',
					'order'        => 'ASC',
				) );

				$comparison_data = sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a>', 'affiliate-wp' ),
					esc_url( $affiliate_link ),
					$affiliate_name
				);

			} else {

				$comparison_data = '';

			}


			$this->register_tile( 'best_converting_campaign', array(
				'label'           => __( 'Best Converting Campaign (All Time)', 'affiliate-wp' ),
				'data'            => '',
				'comparison_data' => $comparison_data
			) );
		}
	}

	/**
	 * Registers the 'Best Converting Campaign' date-based tile.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function best_converting_campaign_date_tile() {

		$affiliate_id = $this->affiliate_id ? $this->affiliate_id : 0;

		$top_campaign_visits = affiliate_wp()->visits->get_visits( array(
			'date'             => $this->date_query,
			'referral_status'  => 'converted',
			'campaign_compare' => 'NOT EMPTY',
			'affiliate_id'     => $affiliate_id,
			'number'           => -1,
			'orderby'          => 'date',
		) );

		$top_campaign_date = $this->get_campaign_by_highest_visits( $top_campaign_visits );


		if ( ! empty( $top_campaign_date ) ) {
			$campaign = $top_campaign_date;

			$affiliate_name = affwp_get_affiliate_name( $campaign->affiliate_id );

			if ( empty( $affiliate_name ) ) {
				$affiliate_name = affwp_get_affiliate_username( $campaign->affiliate_id );
			}

			$affiliate_link = affwp_admin_url( 'referrals', array(
				'affiliate_id' => $campaign->affiliate_id,
				'orderby'      => 'status',
				'order'        => 'ASC',
			) );

			$this->register_tile( 'best_converting_campaign_date', array(
				'label'           => sprintf( __( 'Best Converting Campaign (%s)', 'affiliate-wp' ),
					$this->get_date_comparison_label( __( 'Custom', 'affiliate-wp' ) )
				),
				'context'         => 'tertiary',
				'data'            => empty( $campaign->campaign ) ? __( 'n/a', 'affiliate-wp' ) : $campaign->campaign,
				'comparison_data' => sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a> | Visits: %3$d', 'affiliate-wp' ),
					esc_url( $affiliate_link ),
					$affiliate_name,
					$campaign->visits
				)
			) );
		} else {

			if ( $this->affiliate_id ) {

				$affiliate_name = affwp_get_affiliate_name( $this->affiliate_id );

				if ( empty( $affiliate_name ) ) {
					$affiliate_name = affwp_get_affiliate_username( $this->affiliate_id );
				}

				$affiliate_link = affwp_admin_url( 'referrals', array(
					'affiliate_id' => $this->affiliate_id,
					'orderby'      => 'status',
					'order'        => 'ASC',
				) );

				$comparison_data = sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a>', 'affiliate-wp' ),
					esc_url( $affiliate_link ),
					$affiliate_name
				);

			} else {

				$comparison_data = '';

			}

			$this->register_tile( 'best_converting_campaign_date', array(
				'label'           => sprintf( __( 'Best Converting Campaign (%s)', 'affiliate-wp' ),
					$this->get_date_comparison_label( __( 'Custom', 'affiliate-wp' ) )
				),
				'context'         => 'tertiary',
				'data'            => '',
				'comparison_data' => $comparison_data,
			) );
		}
	}

	/**
	 * Registers the 'Most Active Campaign' date-based tile.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function most_active_campaign_tile() {

		$affiliate_id = $this->affiliate_id ? $this->affiliate_id : 0;

		$active_campaign_visits = affiliate_wp()->visits->get_visits( array(
			'date'             => $this->date_query,
			'campaign_compare' => 'NOT EMPTY',
			'affiliate_id'     => $affiliate_id,
			'number'           => -1,
			'orderby'          => 'date',
		) );

		$most_active_campaign_date = $this->get_campaign_by_highest_visits( $active_campaign_visits );

		if ( ! empty( $most_active_campaign_date ) ) {
			$campaign = $most_active_campaign_date;

			$affiliate_name = affwp_get_affiliate_name( $campaign->affiliate_id );

			if ( empty( $affiliate_name ) ) {
				$affiliate_name = affwp_get_affiliate_username( $campaign->affiliate_id );
			}

			$affiliate_link = affwp_admin_url( 'referrals', array(
				'affiliate_id' => $campaign->affiliate_id,
				'orderby'      => 'status',
				'order'        => 'ASC',
			) );

			$this->register_tile( 'most_active_campaign', array(
				'label'           => sprintf( __( 'Most Active Campaign (%s)', 'affiliate-wp' ),
					$this->get_date_comparison_label( __( 'Custom', 'affiliate-wp' ) )
				),
				'context'         => 'secondary',
				'data'            => empty( $campaign->campaign ) ? __( 'n/a', 'affiliate-wp' ) : $campaign->campaign,
				'comparison_data' => sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a> | Visits: %3$d', 'affiliate-wp' ),
					esc_url( $affiliate_link ),
					$affiliate_name,
					$campaign->visits
				),
			) );
		} else {

			if ( $this->affiliate_id ) {

				$affiliate_name = affwp_get_affiliate_name( $this->affiliate_id );

				if ( empty( $affiliate_name ) ) {
					$affiliate_name = affwp_get_affiliate_username( $this->affiliate_id );
				}

				$affiliate_link = affwp_admin_url( 'referrals', array(
					'affiliate_id' => $this->affiliate_id,
					'orderby'      => 'status',
					'order'        => 'ASC',
				) );

				$comparison_data = sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a>', 'affiliate-wp' ),
					esc_url( $affiliate_link ),
					$affiliate_name
				);

			} else {

				$comparison_data = '';

			}


			$this->register_tile( 'most_active_campaign', array(
				'label'           => sprintf( __( 'Most Active Campaign (%s)', 'affiliate-wp' ),
					$this->get_date_comparison_label( __( 'Custom', 'affiliate-wp' ) )
				),
				'context'         => 'secondary',
				'data'            => '',
				'comparison_data' => $comparison_data
			) );
		}

	}

	/**
	 * Retrieves a pseudo campaign object with the highest visits by campaign.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param array $visits Array of visits results.
	 * @return object Pseudo-campaign object with the highest visits by campaign.
	 */
	public function get_campaign_by_highest_visits( $visits ) {
		$pairs = array();

		// Campaigns by affiliate.
		foreach ( $visits as $visit ) {
			/** @var \AffWP\Visit $visit */
			$pairs[ $visit->affiliate_id ][] = $visit->campaign;
		}

		$counts = array();

		// Campaign an visit counts.
		foreach ( $pairs as $affiliate_id => $campaigns ) {
			$campaign_counts = array_count_values( $campaigns );

			arsort( $campaign_counts );

			foreach ( $campaign_counts as $campaign => $count ) {
				$counts[] = (object) array(
					'affiliate_id' => $affiliate_id,
					'campaign'     => $campaign,
					'visits'       => $count
				);
			}
		}

		usort( $counts, function( $a, $b ) {
			return $a->visits - $b->visits;
		} );

		$counts = array_reverse( $counts );

		return reset( $counts );
	}

	/**
	 * Registers the Campaigns tab tiles.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function register_tiles() {
		$this->best_converting_campaign_tile();
		$this->best_converting_campaign_date_tile();
		$this->most_active_campaign_tile();
	}

	/**
	 * Handles displaying the 'Trends' graph.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function display_trends() {
		$this->graph->set( 'query_args', array(
			'campaign_compare' => 'NOT EMPTY'
		) );

		$this->graph->set( 'show_controls', false );
		$this->graph->set( 'x_mode', 'time' );
		$this->graph->display();
	}

}
