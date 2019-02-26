<?php
namespace AffWP\Affiliate\Payout\Admin\Reports;

use AffWP\Admin\Reports;

/**
 * Implements an 'Payouts' tab for the Reports screen.
 *
 * @since 2.1
 *
 * @see \AffWP\Admin\Reports\Tab
 */
class Tab extends Reports\Tab {

	/**
	 * Affiliate link URL (reused by tiles when filtered by affiliate).
	 *
	 * @access public
	 * @since  2.1
	 * @var    string
	 *
	 * @see \AffWP\Affiliate\Payout\Admin\Reports\Tab::$affiliate_id
	 */
	public $affiliate_link = '';

	/**
	 * Affiliate name (reused by tiles when filtered by affiliate).
	 *
	 * @access public
	 * @since  2.1
	 * @var    string
	 *
	 * @see \AffWP\Affiliate\Payout\Admin\Reports\Tab::$affiliate_id
	 */
	public $affiliate_name = '';

	/**
	 * Sets up the Payouts tab for Reports.
	 *
	 * @access public
	 * @since  2.1
	 */
	public function __construct() {
		$this->tab_id   = 'payouts';
		$this->label    = __( 'Payouts', 'affiliate-wp' );
		$this->priority = 5;
		$this->graph    = new \Affiliate_WP_Payouts_Graph;

		$this->set_up_additional_filters();

		parent::__construct();
	}

	/**
	 * Registers the 'Total Earnings Paid' (all time) tile.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @see register_tile()
	 */
	public function total_paid_all_time_tile() {

		$affiliate_id = $this->affiliate_id ? $this->affiliate_id : 0;

		$payouts = affiliate_wp()->affiliates->payouts->get_payouts( array(
			'number'       => -1,
			'fields'       => 'amount',
			'affiliate_id' => $affiliate_id
		) );

		if ( $this->affiliate_id ) {

			$this->register_tile( 'affiliate_total_paid_all_time', array(
				'label'           => __( 'Total Earnings Paid (All Time)', 'affiliate-wp' ),
				'type'            => 'amount',
				'context'         => 'primary',
				'data'            => ! empty( $payouts ) ? array_sum( $payouts ) : 0,
				'comparison_data' => sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a>', 'affiliate-wp' ),
					esc_url( $this->affiliate_link ),
					$this->affiliate_name
				),
			) );

		} else {

			$this->register_tile( 'total_paid_all_time', array(
				'label'           => __( 'Total Earnings Paid', 'affiliate-wp' ),
				'type'            => 'amount',
				'context'         => 'primary',
				'data'            => array_sum( $payouts ),
				'comparison_data' => __( 'All Time', 'affiliate-wp' ),
			) );
		}
	}

	/**
	 * Registers the 'Total Earnings Paid' date-based tile.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @see register_tile()
	 */
	public function total_earnings_paid_tile() {

		$affiliate_id = $this->affiliate_id ? $this->affiliate_id : 0;

		$payouts = affiliate_wp()->affiliates->payouts->get_payouts( array(
			'number'       => -1,
			'fields'       => 'amount',
			'date'         => $this->date_query,
			'affiliate_id' => $affiliate_id,
		) );

		if ( $this->affiliate_id ) {

			$this->register_tile( 'affiliate_total_earnings_paid', array(
				'label'           => __( 'Total Earnings Paid', 'affiliate-wp' ),
				'type'            => 'amount',
				'context'         => 'secondary',
				'data'            => ! empty( $payouts ) ? array_sum( $payouts ) : 0,
				'comparison_data' => sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a> | %3$s', 'affiliate-wp' ),
					esc_url( $this->affiliate_link ),
					$this->affiliate_name,
					$this->get_date_comparison_label()
				),
			) );

		} else {

			$this->register_tile( 'total_earnings_paid', array(
				'label'   => __( 'Total Earnings Paid', 'affiliate-wp' ),
				'type'    => 'amount',
				'context' => 'secondary',
				'data'    => ! empty( $payouts ) ? array_sum( $payouts ) : 0,
				'comparison_data' => $this->get_date_comparison_label(),
			) );
		}
	}

	/**
	 * Registers the 'Total Earnings Generated' date-based tile.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @see register_tile()
	 */
	public function total_earnings_generated_tile() {

		$affiliate_id = $this->affiliate_id ? $this->affiliate_id : 0;

		$referrals = affiliate_wp()->referrals->get_referrals( array(
			'number'       => -1,
			'fields'       => 'amount',
			'date'         => $this->date_query,
			'status'       => array( 'paid', 'unpaid', 'pending' ),
			'affiliate_id' => $affiliate_id,
		) );

		if ( $this->affiliate_id ) {

			$this->register_tile( 'affiliate_total_earnings_generated', array(
				'label'           => __( 'Total Earnings Generated', 'affiliate-wp' ),
				'type'            => 'amount',
				'context'         => 'tertiary',
				'data'            => ! empty( $referrals ) ? array_sum( $referrals ) : 0,
				'comparison_data' => sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a> | %3$s', 'affiliate-wp' ),
					esc_url( $this->affiliate_link ),
					$this->affiliate_name,
					$this->get_date_comparison_label()
				),
			) );

		} else {

			$this->register_tile( 'total_earnings_generated', array(
				'label' => __( 'Total Earnings Generated', 'affiliate-wp' ),
				'type'  => 'amount',
				'context' => 'tertiary',
				'data'    => ! empty( $referrals ) ? array_sum( $referrals ) : 0,
				'comparison_data' => $this->get_date_comparison_label(),
			) );
		}
	}

	/**
	 * Registers the 'Total Payouts Count' (all time) tile.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @see register_tile()
	 */
	public function total_payouts_count_tile() {
		if ( $this->affiliate_id ) {

			$this->register_tile( 'affiliate_total_payouts_count', array(
				'label'           => __( 'Total Payouts Count (All Time)', 'affiliate-wp' ),
				'type'            => 'number',
				'context'         => 'primary',
				'data'            => affiliate_wp()->affiliates->payouts->count( array( 'affiliate_id' => $this->affiliate_id ) ),
				'comparison_data' => sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a>', 'affiliate-wp' ),
					esc_url( $this->affiliate_link ),
					$this->affiliate_name
				),
			) );

		} else {

			$this->register_tile( 'total_payouts_count', array(
				'label'           => __( 'Total Payouts Count', 'affiliate-wp' ),
				'type'            => 'number',
				'context'         => 'primary',
				'data'            => affiliate_wp()->affiliates->payouts->count(),
				'comparison_data' => __( 'All Time', 'affiliate-wp' ),
			) );

		}
	}

	/**
	 * Registers the 'Average Payout' date-based tile.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @see register_tile()
	 */
	public function average_payout_tile() {

		$affiliate_id = $this->affiliate_id ? $this->affiliate_id : 0;

		$payouts = affiliate_wp()->affiliates->payouts->get_payouts( array(
			'number'       => -1,
			'fields'       => 'amount',
			'date'         => $this->date_query,
			'affiliate_id' => $affiliate_id
		) );

		if ( ! empty( $payouts ) ) {

			if ( $this->affiliate_id ) {

				$this->register_tile( 'affiliate_average_payout_amount', array(
					'label'           => __( 'Average Payout', 'affiliate-wp' ),
					'type'            => 'amount',
					'context'         => 'secondary',
					'data'            => array_sum( $payouts ) / count( $payouts ),
					'comparison_data' => sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a> | %3$s', 'affiliate-wp' ),
						esc_url( $this->affiliate_link ),
						$this->affiliate_name,
						$this->get_date_comparison_label()
					),
				) );


			} else {

				$this->register_tile( 'average_payout_amount', array(
					'label'           => __( 'Average Payout', 'affiliate-wp' ),
					'type'            => 'amount',
					'context'         => 'secondary',
					'data'            => array_sum( $payouts ) / count( $payouts ),
					'comparison_data' => $this->get_date_comparison_label(),
				) );

			}

		}
	}

	/**
	 * Registers the 'Average Payout' date-based tile.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @see register_tile()
	 */
	public function average_referrals_per_payout_tile() {

		$affiliate_id = $this->affiliate_id ? $this->affiliate_id : 0;

		$payout_referrals = affiliate_wp()->affiliates->payouts->get_payouts( array(
			'number'       => -1,
			'fields'       => 'referrals',
			'affiliate_id' => $affiliate_id
		) );

		if ( ! empty( $payout_referrals ) ) {

			$counts = array();

			foreach ( $payout_referrals as $referrals ) {
				$counts[] = count( explode( ',', $referrals ) );
			}

			if ( $this->affiliate_id ) {

				$this->register_tile( 'affiliate_average_referrals_per_payout', array(
					'label'           => __( 'Average Referrals Per Payout (All Time)', 'affiliate-wp' ),
					'type'            => 'number',
					'context'         => 'tertiary',
					'data'            => array_sum( $counts ) / count( $payout_referrals ),
					'comparison_data' => sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a>', 'affiliate-wp' ),
						esc_url( $this->affiliate_link ),
						$this->affiliate_name
					),
				) );

			} else {

				$this->register_tile( 'average_referrals_per_payout', array(
					'label'           => __( 'Average Referrals Per Payout', 'affiliate-wp' ),
					'type'            => 'number',
					'context'         => 'tertiary',
					'data'            => array_sum( $counts ) / count( $payout_referrals ),
					'comparison_data' => __( 'All Time', 'affiliate-wp' ),
				) );

			}

		}
	}

	/**
	 * Registers the Payouts tab tiles.
	 *
	 * @access public
	 * @since  2.1
	 */
	public function register_tiles() {
		if ( $this->affiliate_id ) {
			$this->affiliate_link = affwp_admin_url( 'visits', array(
				'affiliate' => $this->affiliate_id,
			) );

			$this->affiliate_name = affwp_get_affiliate_name( $this->affiliate_id );

			if ( empty( $this->affiliate_name ) ) {
				$this->affiliate_name = affwp_get_affiliate_username( $this->affiliate_id );
			}
		}

		$this->total_paid_all_time_tile();
		$this->total_earnings_paid_tile();
		$this->total_earnings_generated_tile();
		$this->total_payouts_count_tile();
		$this->average_payout_tile();
		$this->average_referrals_per_payout_tile();
	}

	/**
	 * Handles displaying the 'Trends' graph.
	 *
	 * @access public
	 * @since  2.1
	 */
	public function display_trends() {
		$this->graph->set( 'show_controls', false );
		$this->graph->set( 'x_mode',   'time' );
		$this->graph->set( 'currency', false  );
		$this->graph->display();
	}

}
