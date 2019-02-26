<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils;
use AffWP\Utils\Batch_Process as Batch;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements a batch processor for migrating affiliates from WP Affiliate.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Batch_Process
 * @see \AffWP\Utils\Batch_Process\With_PreFetch
 */
class Migrate_WP_Affiliate extends Utils\Batch_Process implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id = 'migrate-wp-affiliate';

	/**
	 * Number of users to migrate per step.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int
	 */
	public $per_step = 100;

	/**
	 * Initializes values needed following instantiation (unused).
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param null|array $data Optional. Form data. Default null.
	 */
	public function init( $data = null ) {}

	/**
	 * Handles pre-fetching user IDs for accounts in migration.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function pre_fetch() {
		// Existing affiliate user IDs.
		$affiliate_user_ids = affiliate_wp()->utils->data->get( "{$this->batch_id}_user_ids" );

		if ( false === $affiliate_user_ids ) {
			$affiliate_user_ids = affiliate_wp()->affiliates->get_affiliates( array(
				'number' => -1,
				'fields' => 'user_id',
			) );

			affiliate_wp()->utils->data->write( "{$this->batch_id}_user_ids", $affiliate_user_ids );
		}

		// Existing user emails and IDs
		$existing_users = affiliate_wp()->utils->data->get( "{$this->batch_id}_user_email_ids" );

		if ( false === $existing_users ) {
			$existing_users = get_users( array(
				'number' => -1,
				'fields' => array( 'ID', 'user_email' ),
			) );
		}

		$emails_ids = array();

		foreach ( $existing_users as $user ) {

			if ( ! empty( $user->user_email ) ) {
				$emails_ids[ $user->user_email ] = array(
					'id'    => $user->ID,
					'email' => $user->user_email
				);
			}
		}

		affiliate_wp()->utils->data->write( "{$this->batch_id}_user_email_ids", $emails_ids );

		// Total number of WP Affiliate accounts to migrate.
		$total_to_migrate = $this->get_total_count();

		if ( false === $total_to_migrate ) {

			global $wpdb;

			$total_to_migrate = $wpdb->get_var( "SELECT COUNT(refid) FROM {$wpdb->prefix}affiliates_tbl;" );

			$this->set_total_count( absint( $total_to_migrate ) );
		}

	}

	/**
	 * Executes a single step in the batch process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return int|string|\WP_Error Next step number, 'done', or a WP_Error object.
	 */
	public function process_step() {
		global $wpdb;

		$affiliates = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}affiliates_tbl LIMIT %d, %d;",
				$this->get_offset(),
				$this->per_step
			)
		);

		$inserted = array();

		if ( empty( $affiliates ) ) {
			return 'done';
		}

		if ( $affiliates ) {

			$user_email_ids     = affiliate_wp()->utils->data->get( "{$this->batch_id}_user_email_ids", array() );
			$affiliate_user_ids = affiliate_wp()->utils->data->get( "{$this->batch_id}_user_ids", array() );

			foreach ( $affiliates as $affiliate ) {

				if ( empty( $affiliate->email ) ) {
					continue;
				}

				// If the email is in the user pool already, grab the ID.
				if ( array_key_exists( $affiliate->email, $user_email_ids ) ) {

					$user_id = absint( $user_email_ids[ $affiliate->email ]['id'] );

				} else {

					// Otherwise, create a new user account.
					$user_id = wp_insert_user( array(
						'user_email' => $affiliate->email,
						'first_name' => $affiliate->firstname,
						'last_name'  => $affiliate->lastname,
						'user_url'   => $affiliate->website,
						'user_pass'  => wp_generate_password( 20 ),
						'user_login' => $affiliate->email,
					) );

				}

				// Skip if the user is already affiliated or invalid.
				if ( in_array( $user_id, $affiliate_user_ids ) || is_wp_error( $user_id ) ) {
					continue;
				}

				$payment_email = ! empty( $affiliate->paypalemail ) ? $affiliate->paypalemail : $affiliate->email;
				$status        = 'approved' == $affiliate->account_status ? 'active' : 'pending';

				$args = array(
					'date_registered' => date( 'Y-n-d H:i:s', strtotime( $affiliate->date ) ),
					'user_id'         => $user_id,
					'payment_email'   => $payment_email,
					'rate'            => $affiliate->commissionlevel,
					'status'          => $status
				);

				// Insert a new affiliate - we need to always insert to make sure the affiliate_ids will match
				$new_affiliate = affiliate_wp()->affiliates->insert( $args, 'affiliate' );

				if ( $new_affiliate ) {
					$inserted[] = $new_affiliate;
				}
			}
		}
		$current_count = $this->get_current_count();

		$this->set_current_count( $current_count + count( $inserted ) );

		return ++$this->step;
	}

	/**
	 * Retrieves a message for the given code.
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

				$message = sprintf(
					_n(
						'%s affiliate was successfully converted.',
						'%s affiliates were successfully converted.',
						$final_count,
						'affiliate-wp'
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
	 * @access public
	 * @since  2.0
	 *
	 * @param string $batch_id Batch process ID.
	 */
	public function finish( $batch_id ) {
		// Clean up.
		delete_option( 'affwp_migrate_direct_affiliates' );

		parent::finish( $batch_id );
	}

}
