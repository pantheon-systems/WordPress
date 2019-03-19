<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils;
use AffWP\Utils\Batch_Process as Batch;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements an upgrade routine for creatingg customer records.
 *
 * @since 2.2
 * @see \AffWP\Utils\Batch_Process\Recount_Affiliate_Stats
 */
class Upgrade_Create_Customers extends Utils\Batch_Process implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @since  2.2
	 * @var    string
	 */
	public $batch_id = 'create-customers-upgrade';

	/**
	 * Capability needed to perform the current batch process.
	 *
	 * @since  2.2
	 * @var    string
	 */
	public $capability = 'manage_affiliates';

	/**
	 * Number of referrals to process per step.
	 *
	 * @since  2.2
	 * @var    int
	 */
	public $per_step = 1;

	/**
	 * Initializes the batch process.
	 *
	 * @since  2.2
	 */
	public function init( $data = null ) {

		if( $this->step <= 1 ) {

			affiliate_wp()->customers->create_table();
			affiliate_wp()->utils->log( 'Upgrade: The customers table has been created.' );

			affiliate_wp()->customer_meta->create_table();
			affiliate_wp()->utils->log( 'Upgrade: The customer meta table has been created.' );

		}

	}

	/**
	 * Handles pre-fetching user IDs for accounts in migration.
	 *
	 * @since  2.2
	 */
	public function pre_fetch() {

		$total_to_process = $this->get_total_count();

		if ( false === $total_to_process ) {

			$total_to_process = affiliate_wp()->referrals->count( array(
				'number' => -1,
			) );

			$this->set_total_count( $total_to_process );
		}
	}

	/**
	 * Executes a single step in the batch process.
	 *
	 * @since  2.2
	 *
	 * @return int|string|\WP_Error Next step number, 'done', or a WP_Error object.
	 */
	public function process_step() {

		$current_count = $this->get_current_count();

		$args = array(
			'number'     => $this->per_step,
			'offset'     => $this->get_offset(),
			'orderby'    => 'referral_id',
			'order'      => 'ASC',
		);

		$referrals = affiliate_wp()->referrals->get_referrals( $args );

		if ( empty( $referrals ) ) {
			return 'done';
		}

		$inserted = array();

		foreach ( $referrals as $referral ) {

			$class_name = affiliate_wp()->integrations->get_integration_class( $referral->context );

			if( class_exists( $class_name ) ) {

				try {

					$integration = new $class_name;
					$customer = $integration->get_customer( $referral->reference );

					if ( ! empty( $customer ) ) {

						$customer['affiliate_id'] = $referral->affiliate_id;

						$customer_id = affwp_add_customer( $customer );
						$inserted[]  = $customer_id;

						$referral->set( 'customer_id', $customer_id, true );

					}

				} catch ( Exception $e ) {

					// Something happened here

				}

			}


		}

		$this->set_current_count( absint( $current_count ) + count( $inserted ) );

		return ++$this->step;
	}

	/**
	 * Retrieves a message based on the given message code.
	 *
	 * @since  2.2
	 *
	 * @param string $code Message code.
	 * @return string Message.
	 */
	public function get_message( $code ) {
		switch( $code ) {

			case 'done':
				$message = sprintf( __( 'Your database has been successfully upgraded. %s', 'affiliate-wp' ),
					sprintf( '<a href="">%s</a>', __( 'Dismiss Notice', 'affiliate-wp' ) )
				);
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
	 * @since  2.2
	 *
	 * @param string $batch_id Batch process ID.
	 */
	public function finish( $batch_id ) {
		affwp_set_upgrade_complete( 'upgrade_v22_create_customer_records' );

		// Clean up.
		parent::finish( $batch_id );
	}
}
