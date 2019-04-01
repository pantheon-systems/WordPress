<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils;
use AffWP\Utils\Batch_Process as Batch;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements an batch processor for migrating lifetime meta from the user meta to affiliatewp meta.
 *
 * @see \AffWP\Utils\Batch_Process\Base
 * @see \AffWP\Utils\Batch_Process
 * @package AffWP\Utils\Batch_Process
 */
class Migrate_Lifetime_Commissions_Meta extends Utils\Batch_Process implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @since  1.3
	 * @var    string
	 */
	public $batch_id = 'migrate-lc-meta';

	/**
	 * Capability needed to perform the current batch process.
	 *
	 * @since  1.3
	 * @var    string
	 */
	public $capability = 'manage_affiliates';

	/**
	 * Number of affiliates to process per step.
	 *
	 * @since  1.3
	 * @var    int
	 */
	public $per_step = 1;

	/**
	 * Initializes the batch process.
	 *
	 * @since  1.3
	 */
	public function init( $data = null ) {}

	/**
	 * Handles pre-fetching user IDs for accounts in migration.
	 *
	 * @since  1.3
	 */
	public function pre_fetch() {

		$total_to_process = $this->get_total_count();

		if ( false === $total_to_process ) {

			$total_to_process = affiliate_wp()->affiliates->count( array(
				'number' => -1,
			) );

			$this->set_total_count( $total_to_process );
		}
	}

	/**
	 * Executes a single step in the batch process.
	 *
	 * @since  1.3
	 *
	 * @return int|string|\WP_Error Next step number, 'done', or a WP_Error object.
	 */
	public function process_step() {

		$current_count = $this->get_current_count();

		$args = array(
			'number'     => $this->per_step,
			'offset'     => $this->get_offset(),
			'orderby'    => 'affiliate_id',
			'order'      => 'ASC',
		);

		$affiliates = affiliate_wp()->affiliates->get_affiliates( $args );

		if ( empty( $affiliates ) ) {
			return 'done';
		}

		$migrated = array();

		foreach ( $affiliates as $affiliate ) {

			$affwp_lc_enabled         = get_user_meta( $affiliate->user_id, 'affwp_lc_enabled', true );
			$affwp_lc_customer_ids    = get_user_meta( $affiliate->user_id, 'affwp_lc_customer_id', false );
			$affwp_lc_customer_emails = get_user_meta( $affiliate->user_id, 'affwp_lc_customer_email', false );

			if ( $affwp_lc_enabled ) {
				affwp_update_affiliate_meta( $affiliate->affiliate_id, 'affwp_lc_enabled', $affwp_lc_enabled );
			}

			if ( ! empty( $affwp_lc_customer_emails ) ) {

				foreach ( $affwp_lc_customer_emails as $customer_email ) {

					if ( ! empty( $customer_email ) && is_email( $customer_email ) ) {

						$customer = affiliate_wp()->customers->get_by( 'email', $customer_email );

						if ( ! $customer ) {

							$args = array(
								'email'        => $customer_email,
								'affiliate_id' => $affiliate->affiliate_id
							);

							$user = get_user_by( 'email', $customer_email );

							if ( $user ) {
								$args['user_id']    = $user->ID;
								$args['first_name'] = $user->first_name;
								$args['last_name']  = $user->last_name;
							}

							// Delete the lc meta saved in the user meta
							if ( $user ) {
								delete_user_meta( $user->ID, 'affwp_lc_email' );
								delete_user_meta( $user->ID, 'affwp_lc_affiliate_id' );
							}

							affwp_add_customer( $args );

						} else {

							affwp_add_customer_meta( $customer->customer_id, 'affiliate_id', $affiliate->affiliate_id, true );

							// Delete the lc meta saved in the user meta.
							if ( $customer->user_id ) {
								delete_user_meta( $customer->user_id, 'affwp_lc_email' );
								delete_user_meta( $customer->user_id, 'affwp_lc_affiliate_id' );
							}

						}

					}

				}
			}

			if ( ! empty( $affwp_lc_customer_ids ) ) {

				foreach ( $affwp_lc_customer_ids as $user_id ) {

					if ( ! empty( $user_id ) && $user = get_userdata( $user_id ) ) {

						$customer = affiliate_wp()->customers->get_by( 'user_id', $user_id );

						if ( ! $customer ) {

							$args = array(
								'email'        => $user->user_email,
								'affiliate_id' => $affiliate->affiliate_id,
								'user_id'      => $user_id,
								'first_name'   => $user->first_name,
								'last_name'    => $user->last_name
							);

							affwp_add_customer( $args );

						}

						// Delete the lc meta saved in the user meta.
						delete_user_meta( $user->ID, 'affwp_lc_email' );
						delete_user_meta( $user->ID, 'affwp_lc_affiliate_id' );

					}
				}
			}

			delete_user_meta( $affiliate->user_id, 'affwp_lc_enabled' );
			delete_user_meta( $affiliate->user_id, 'affwp_lc_customer_id' );
			delete_user_meta( $affiliate->user_id, 'affwp_lc_customer_email' );

			$migrated[] = $affiliate->affiliate_id;
		}

		$this->set_current_count( absint( $current_count ) + count( $migrated ) );

		return ++ $this->step;
	}

	/**
	 * Retrieves a message based on the given message code.
	 *
	 * @since  1.3
	 *
	 * @param string $code Message code.
	 * @return string Message.
	 */
	public function get_message( $code ) {

		switch( $code ) {

			case 'done':
				$final_count = $this->get_current_count();

				$message = sprintf(
					_n(
						'%s affiliate was updated successfully.',
						'%s affiliates were updated successfully.',
						$final_count,
						'affiliate-wp-lifetime-commissions'
					), number_format_i18n( $final_count )
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
	 * @since  1.3
	 *
	 * @param string $batch_id Batch process ID.
	 */
	public function finish( $batch_id ) {

		update_option( 'affwp_lc_migrate_meta', 1 );

		// Clean up.
		parent::finish( $batch_id );
	}
}
