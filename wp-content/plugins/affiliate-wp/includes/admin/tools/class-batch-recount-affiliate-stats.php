<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils;
use AffWP\Utils\Batch_Process as Batch;

/**
 * Implements a batch process to recount all affiliate stats.
 *
 * @see \AffWP\Utils\Batch_Process\Base
 * @see \AffWP\Utils\Batch_Process
 * @package AffWP\Utils\Batch_Process
 */
class Recount_Affiliate_Stats extends Utils\Batch_Process implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id = 'recount-affiliate-stats';

	/**
	 * Capability needed to perform the current batch process.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $capability = 'manage_affiliates';

	/**
	 * Number of affiliates to process per step.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int
	 */
	public $per_step = 1;

	/**
	 * Recount type.
	 *
	 * @access public
	 * @since  2.0.5
	 * @var    string
	 */
	public $type = '';

	/**
	 * ID of the affiliate to recount stats for.
	 *
	 * @access public
	 * @since  2.0.5
	 * @var    int
	 */
	public $affiliate_id = 0;

	/**
	 * Whether the affiliate filter is set.
	 *
	 * Used for the case where an affiliate is filtered but there are no matches.
	 *
	 * @access public
	 * @since  2.0.5
	 * @var    bool
	 */
	public $affiliate_filter = false;

	/**
	 * Initializes the batch process.
	 *
	 * This is the point where any relevant data should be initialized for use by the processor methods.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function init( $data = null ) {
		if ( null !== $data ) {

			$this->affiliate_filter = ! empty( $data['user_name'] );

			$data = affiliate_wp()->utils->process_request_data( $data, 'user_name' );

			if ( ! empty( $data['user_id'] ) ) {
				$this->affiliate_id = affiliate_wp()->affiliates->get_column_by( 'affiliate_id', 'user_id', $data['user_id'] );
			}

			$this->type = sanitize_text_field( $data['recount_type'] );
		}
	}

	/**
	 * Pre-fetches data to speed up processing.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function pre_fetch() {

		// If an invalid affiliate is set, go no further.
		if ( ! $this->affiliate_id && $this->affiliate_filter ) {
			affiliate_wp()->utils->data->write( "{$this->batch_id}_affiliate_totals", array() );

			$this->set_total_count( 0 );

			return;
		}

		if ( false === $this->get_total_count() ) {
			if ( in_array( $this->type, array( 'earnings', 'unpaid-earnings' ), true ) ) {

				$this->compile_affiliate_totals();

			} else {

				$this->compile_totals();

			}
		}

	}

	/**
	 * Compiles and stores amount totals for all affiliates with unpaid referrals.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function compile_affiliate_totals() {
		$affiliate_totals = affiliate_wp()->utils->data->get( "{$this->batch_id}_affiliate_totals", array() );

		if ( false === $affiliate_totals ) {
			if ( 'earnings' === $this->type ) {
				$status = 'paid';
			} elseif ( 'unpaid-earnings' === $this->type ) {
				$status = 'unpaid';
			} else {
				$status = '';
			}

			if ( empty( $status ) ) {
				// Bail if no status.
				return;
			}

			$args = array(
				'number'       => -1,
				'status'       => $status,
				'affiliate_id' => $this->affiliate_id,
			);

			$referrals = affiliate_wp()->referrals->get_referrals( $args );

			$data_sets = array();

			foreach ( $referrals as $referral ) {
				$data_sets[ $referral->affiliate_id ][] = $referral;
			}

			$affiliate_totals = array();

			if ( ! empty( $data_sets ) ) {
				foreach ( $data_sets as $affiliate_id => $referrals ) {
					foreach ( $referrals as $referral ) {
						if ( isset( $affiliate_totals[ $referral->affiliate_id ] ) ) {
							$affiliate_totals[ $referral->affiliate_id ] += $referral->amount;
						} else {
							$affiliate_totals[ $referral->affiliate_id ] = $referral->amount;
						}
					}
				}
			}

			affiliate_wp()->utils->data->write( "{$this->batch_id}_affiliate_totals", $affiliate_totals );

			$this->set_total_count( count( $affiliate_totals ) );
		}

	}

	/**
	 * Compiles totals for referrals and visits.
	 *
	 * @access public
	 * @since  2.0.5
	 */
	public function compile_totals() {
		$count = 0;

		$affiliate_totals = array();

		if ( 'referrals' === $this->type ) {

			$referrals = affiliate_wp()->referrals->get_referrals( array(
				'affiliate_id' => $this->affiliate_id,
				'number'       => -1,
				'fields'       => 'affiliate_id'
			) );

			$referrals = array_map( 'absint', $referrals );

			$affiliate_totals = array_count_values( $referrals );

		} elseif ( 'visits' === $this->type ) {

			$visits = affiliate_wp()->visits->get_visits( array(
				'affiliate_id' => $this->affiliate_id,
				'number'       => -1,
				'fields'       => 'affiliate_id'
			) );

			$visits = array_map( 'absint', $visits );

			$affiliate_totals = array_count_values( $visits );
		}

		affiliate_wp()->utils->data->write( "{$this->batch_id}_affiliate_totals", $affiliate_totals );

		$this->set_total_count( count( $affiliate_totals ) );
	}

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since  2.0
	 */
	public function process_step() {
		$offset        = $this->get_offset();
		$current_count = $this->get_current_count();

		$affiliate_totals = affiliate_wp()->utils->data->get( "{$this->batch_id}_affiliate_totals", array() );
		$affiliate_ids    = array_keys( $affiliate_totals );

		if ( isset( $affiliate_ids[ $offset ] ) ) {
			$affiliate_id = $affiliate_ids[ $offset ];
		} else {
			return 'done';
		}

		$total = $affiliate_totals[ $affiliate_id ];

		if ( 'earnings' === $this->type ) {

			affiliate_wp()->affiliates->update( $affiliate_id, array( 'earnings' => floatval( $total ), '', 'affiliate' ) );

		} elseif ( 'unpaid-earnings' === $this->type ) {

			affiliate_wp()->affiliates->update( $affiliate_id, array( 'unpaid_earnings' => floatval( $total ), '', 'affiliate' ) );

		} elseif ( 'referrals' === $this->type ) {

			affiliate_wp()->affiliates->update( $affiliate_id, array( 'referrals' => $total ), '', 'affiliate' );

		} elseif ( 'visits' === $this->type ) {

			affiliate_wp()->affiliates->update( $affiliate_id, array( 'visits' => $total ), '', 'affiliate' );
		}

		$this->set_current_count( absint( $current_count ) + 1 );

		return ++$this->step;
	}

	/**
	 * Retrieves a message based on the given message code.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $code Message code.
	 * @return string Message.
	 */
	public function get_message( $code ) {
		switch( $code ) {

			case 'done':
				$final_count = $this->get_current_count();

				if ( 0 == $final_count ) {

					$message = __( 'No affiliates were found to be recounted for the current filters.', 'affiliate-wp' );

				} else {

					$filtered = 1 == $final_count && $this->affiliate_filter;
					$username = affwp_get_affiliate_username( $this->affiliate_id );

					switch( $this->type ) {
						case 'earnings':
							if ( $filtered ) {
								$message = sprintf( __( 'Earnings have been successfully recounted for %s.', 'affiliate-wp' ), $username );
							} else {
								$message = __( 'Earnings have been successfully recounted for all matching affiliates.', 'affiliate-wp' );
							}
							break;

						case 'unpaid-earnings':
							if ( $filtered ) {
								$message = sprintf( __( 'Unpaid earnings have been successfully recounted for %s.', 'affiliate-wp' ), $username );
							} else {
								$message = __( 'Unpaid earnings have been successfully recounted for all matching affiliates.', 'affiliate-wp' );
							}
							break;

						case 'referrals':
							if ( $filtered ) {
								$message = sprintf( __( 'Referrals have been successfully recounted for %s.', 'affiliate-wp' ), $username );
							} else {
								$message = __( 'Referrals have been successfully recounted for all matching affiliates.', 'affiliate-wp' );
							}
							break;

						case 'visits':
							if ( $filtered ) {
								$message = sprintf( __( 'Visits have been successfully recounted for %s.', 'affiliate-wp' ), $username );
							} else {
								$message = __( 'Visits have been successfully recounted for all matching affiliates.', 'affiliate-wp' );
							}
							break;

						default: break;
					}

				}
				break;

			default:
				$message = '';
				break;
		}

		return $message;
	}

	/**
	 * Defines logic to execute after the batch processing is complete.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $batch_id Batch process ID.
	 */
	public function finish( $batch_id ) {
		// Clean up.
		parent::finish( $batch_id );

		// Invalidate the affiliates cache.
		wp_cache_set( 'last_changed', microtime(), 'affiliates' );
	}

}
