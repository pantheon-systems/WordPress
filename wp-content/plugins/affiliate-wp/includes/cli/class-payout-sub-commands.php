<?php
namespace AffWP\Affiliate\Payout\CLI;

use \AffWP\CLI\Sub_Commands\Base;
use \WP_CLI\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP-CLI sub-commands for managing payouts.
 *
 * @since 1.9
 *
 * @see \AffWP\CLI\Sub_Commands\Base
 */
class Sub_Commands extends Base {

	/**
	 * Payout display fields.
	 *
	 * @since 1.9
	 * @access protected
	 * @var array
	 */
	protected $obj_fields = array(
		'ID',
		'amount',
		'affiliate_id',
		'affiliate_email',
		'referrals',
		'owner',
		'payout_method',
		'status',
		'date'
	);

	/**
	 * Sets up the fetcher for sanity-checking.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @see \AffWP\Affiliate\Payout\CLI\Fetcher
	 */
	public function __construct() {
		$this->fetcher = new Fetcher();
	}

	/**
	 * Retrieves a payout object or field(s) by ID.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The payout ID to retrieve.
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole payout object, returns the value of a single field.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields. Defaults to all fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, json, csv, yaml. Default: table
	 *
	 * ## EXAMPLES
	 *
	 *     # save the payout field value to a file
	 *     wp payout get 12 --field=amount > amounts.txt
	 */
	public function get( $args, $assoc_args ) {
		parent::get( $args, $assoc_args );
	}

	/**
	 * Adds a payout.
	 *
	 * ## OPTIONS
	 *
	 * [--start_date=<date>]
	 * : Starting date to pay out referrals for. Can be used without --date_end to pay out referrals
	 * on or after this date.
	 *
	 * [--end_date=<date>]
	 * : Starting date to pay out referrals for. Can be used without --date_start to pay out referrals
	 * on or before this date.
	 *
	 * [--min_earnings=<amount>]
	 * : Minimum total earnings required to generate a payout for an affiliate. Compared as greater than or equal to.
	 *
	 * If omitted, minimum earnings required will be 0.
	 *
	 * [--owner=<user_id>]
	 * : User ID to set as the payout owner. If ommitted, the current user will be used.
	 *
	 * [--payout_method=<method>]
	 * : Payout method. Default 'cli'.
	 *
	 * [--referral_status=<status>]
	 * : Status to retrieve referrals for. Accepts any valid referral status.
	 *
	 * If omitted, 'unpaid' will be used.
	 *
	 * ## EXAMPLES
	 *
	 *     # Creates a payout for affiliate edduser1 and referrals 4, 5, and 6
	 *     wp affwp payout create edduser1 4,5,6
	 *
	 *     # Creates a payout for affiliate woouser1, for all of their unpaid referrals, for a total amount of 50
	 *     wp affwp payout create woouser1 all --amount=10
	 *
	 *     # Creates a payout for affiliate ID 142, for all of their unpaid referrals, with a payout method of 'manual'
	 *     wp affwp payout create 142 --method='manual'
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function create( $args, $assoc_args ) {

		$data = array();

		$referral_args = array(
			'number' => - 1,
			'fields' => 'ids',
		);

		$start_date = Utils\get_flag_value( $assoc_args, 'start_date',   '' );
		$end_date   = Utils\get_flag_value( $assoc_args, 'end_date',     '' );
		$minimum    = Utils\get_flag_value( $assoc_args, 'min_earnings', '' );

		if ( ! empty( $start_date ) ) {
			$referral_args['date']['start'] = sanitize_text_field( $start_date );
		}

		if ( ! empty( $end_date ) ) {
			$referral_args['date']['end'] = sanitize_text_field( $end_date );
		}

		if ( ! empty( $minimum ) ) {
			$minimum = absint( $minimum );
		}

		$referral_args['status'] = Utils\get_flag_value( $assoc_args, 'referral_status', 'unpaid' );

		$referrals = affiliate_wp()->referrals->get_referrals( $referral_args );

		if ( empty( $referrals ) ) {
			\WP_CLI::warning( __( 'No referrals were found matching your criteria. Please try again.', 'affiliate-wp' ) );
		}

		$maps = affiliate_wp()->affiliates->payouts->get_affiliate_ids_by_referrals( $referrals, $referral_args['status'] );

		$to_pay = array();

		foreach ( $maps as $affiliate_id => $referrals ) {
			$amount = 0;

			foreach( $referrals as $referral_id ) {
				if ( $referral = affwp_get_referral( $referral_id ) ) {
					$amount += $referral->amount;
				}
			}

			if ( $amount >= $minimum ) {
				$to_pay[ $affiliate_id ] = array(
					'referrals' => $maps[ $affiliate_id ],
					'amount'    => $amount,
				);
			}
		}

		// Grab flag values.
		$data['owner']         = Utils\get_flag_value( $assoc_args, 'owner', get_current_user_id() );
		$data['payout_method'] = Utils\get_flag_value( $assoc_args, 'payout_method', 'cli' );

		if ( empty( $to_pay ) ) {
			\WP_CLI::warning( __( 'No affiliates matched the minimum earnings amount in order to generate a payout.', 'affiliate-wp' ) );
		} else {
			foreach ( $to_pay as $affiliate_id => $payout_data ) {
				if ( false !== $payout_id = affwp_add_payout( array(
					'affiliate_id'  => $affiliate_id,
					'referrals'     => $payout_data['referrals'],
					'payout_method' => $data['payout_method'],
				) ) ) {
					\WP_CLI::success( sprintf( __( 'A payout has been created for Affiliate #%1$d for %2$s.', 'affiliate-wp' ),
						$affiliate_id,
						html_entity_decode( affwp_currency_filter( affwp_format_amount( $payout_data['amount'] ) ) )
					) );
				} else {
					\WP_CLI::warning( sprintf( __( 'There was a problem generating a payout for Affiliate #%1$d for %2$s.', 'affiliate-wp' ),
						$affiliate_id,
						html_entity_decode( affwp_currency_filter( affwp_format_amount( $payout_data['amount'] ) ) )
					) );
				}
			}
		}
	}

	/**
	 * Updates a payout.
	 *
	 * ## OPTIONS
	 *
	 * <payout_id>
	 * : ID of the payout to update.
	 *
	 * [--owner=<user_id>]
	 * : New payout owner (user ID).
	 *
	 * [--payout_method=<method>]
	 * : New payout method.
	 *
	 * [--status=<status>]
	 * : New payout status. Accepts 'paid' or 'failed'. Will not be changed if invalid.
	 *
	 * [--amount=<number>]
	 * : New payout amount.
	 *
	 * Note: care should be taken when updating this value as payouts are typically tied to
	 * the sum total of the paid-out referrals attached to it.
	 *
	 * [--referrals=<referrals>]
	 * : Updated referrals to associate with the payout. New referrals will be merged with currently associated ones.
	 *
	 * Note: care should be taken when updating this value as payouts referrals are typically tied with the affiliate
	 * ID of and total amount.
	 *
	 * [--affiliate=<ID|username>]
	 * : Affiliate to associate with the payout. Accepts an affiliate ID or user_login.
	 *
	 * Note: care should be taken when updating this value as referrals
	 * tied to a payout are also typicalyl tied to the affiliate_id of record.
	 *
	 */
	public function update( $args, $assoc_args ) {
		if ( empty( $args[0] ) ) {
			try {

				\WP_CLI::error( __( 'A valid payout ID must be supplied to update a payout', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}

		if ( ! $payout = affwp_get_payout( absint( $args[0] ) ) ) {
			try {

				\WP_CLI::error( __( 'A valid payout ID must be supplied to update a payout', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}

		$owner         = Utils\get_flag_value( $assoc_args, 'owner',         0  );
		$payout_method = Utils\get_flag_value( $assoc_args, 'payout_method', '' );
		$status        = Utils\get_flag_value( $assoc_args, 'status',        '' );
		$amount        = Utils\get_flag_value( $assoc_args, 'amount',        '' );
		$referrals     = Utils\get_flag_value( $assoc_args, 'referrals',     '' );
		$affiliate     = Utils\get_flag_value( $assoc_args, 'affiliate',     '' );

		$data = array();

		if ( ! empty( $owner ) ) {
			$data['owner'] = get_user_by( 'id', $owner ) ? absint( $owner ) : $payout->owner;
		}

		if ( ! empty( $payout_method ) ) {
			$data['payout_method'] = sanitize_text_field( $payout_method );
		}

		if ( ! empty( $status ) && in_array( $status, array( 'paid', 'failed' ), true ) ) {
			$data['status'] = sanitize_text_field( $status );
		}

		if ( ! empty( $amount ) ) {
			$data['amount'] = affwp_sanitize_amount( $amount );
		}

		if ( ! empty( $referrals ) ) {
			if ( false !== strpos( $referrals, ',' ) ) {
				$referrals = wp_parse_id_list( $referrals );
			} else {
				$referrals = (array) absint( $referrals );
 			}

 			$confirmed = array();

			foreach ( $referrals as $referral_id ) {
				if ( $referral = affwp_get_referral( $referral_id ) ) {
					if ( empty( $referral->payout_id ) ) {
						$confirmed[] = $referral_id;
					} else {
						\WP_CLI::warning( sprintf( __( "Referral #%d is already associated with payout #%d and has been skipped.", 'affiliate-wp' ),
							$referral_id,
							$referral->payout_id
						) );
					}
				} else {
					\WP_CLI::warning( sprintf( __( "Referral #%d is not valid and has been skipped.", 'affiliate-wp' ), $referral_id ) );
				}
			}

			if ( ! empty( $confirmed ) ) {
				\WP_CLI::confirm( __( 'Are you sure you want to overwrite this payout\'s referrals?', 'affiliate-wp' ), $assoc_args );

				$payout_referrals = empty( $payout->referrals ) ? array() : wp_parse_id_list( $payout->referrals );

				$referrals = array_unique( array_merge( $payout_referrals, $confirmed ) );

				$data['referrals'] = implode( ',', $referrals );
			} else {
				try {

					\WP_CLI::error( __( 'All values passed via the --referrals argument are invalid.', 'affiliate-wp' ) );

				} catch( \Exception $exception ) {}
			}
		}

		if ( ! empty( $affiliate ) ) {
			if ( $affiliate = affwp_get_affiliate( $affiliate ) ) {
				\WP_CLI::confirm( __( 'Are you sure you want to overwrite the affiliate associated with this payout?', 'affiliate-wp' ), $assoc_args );

				$data['affiliate_id'] = $affiliate->ID;
			} else {
				try {

					\WP_CLI::error( __( 'The supplied affiliate ID or username is invalid and has been ignored.', 'affiliate-wp' ) );

				} catch( \Exception $exception ) {}
			}
		}

		if ( ! empty( $data ) ) {
			$updated = affiliate_wp()->affiliates->payouts->update( $payout->ID, $data, '', 'payout' );
		} else {
			\WP_CLI::warning( __( 'No fields were specified for updating. For more information, see wp help affwp payout update.', 'affiliate-wp' ) );
		}

		if ( $updated ) {
			\WP_CLI::success( sprintf( __( "Payout #%d has been updated successfully.", 'affiliate-wp' ), $payout->ID ) );
		} else {
			try {

				\WP_CLI::error( sprintf( __( "Payout #%d could not be updated due to an error.", 'affiliate-wp' ), $payout->ID ) );

			} catch( \Exception $exception ) {}
		}
	}

	/**
	 * Deletes a payout.
	 *
	 * ## OPTIONS
	 *
	 * <payout_id>
	 * : Payout ID.
	 *
	 * ## EXAMPLES
	 *
	 *     # Deletes the payout with ID 20
	 *     wp affwp payout delete 20
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags, unused).
	 */
	public function delete( $args, $assoc_args ) {
		if ( empty( $args[0] ) || ! is_numeric( $args[0] ) ) {
			try {

				\WP_CLI::error( __( 'A valid payout ID is required to proceed.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}

		if ( ! $payout = affwp_get_payout( $args[0] ) ) {
			try {

				\WP_CLI::error( __( 'A valid payout ID is required to proceed.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}

		\WP_CLI::confirm( __( 'Are you sure you want to delete this payout?', 'affiliate-wp' ), $assoc_args );

		$deleted = affwp_delete_payout( $payout );

		if ( $deleted ) {
			\WP_CLI::success( __( 'The payout has been successfully deleted.', 'affiliate-wp' ) );
		} else {
			try {

				\WP_CLI::error( __( 'The payout could not be deleted.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}
	}

	/**
	 * Displays a list of payouts.
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : One or more args to pass to get_payouts().
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each payout.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific payout fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count, ids, yaml. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each payout:
	 *
	 * * ID (alias for payout_id)
	 * * amount
	 * * affiliate_id
	 * * affiliate_email
	 * * referrals
	 * * owner (user_id)
	 * * payout_method
	 * * status
	 * * date
	 *
	 * ## EXAMPLES
	 *
	 *     affwp payout list --field=date
	 *
	 *     affwp payout list --amount_min=0 --amount_max=20 --fields=affiliate_id,amount,date
	 *
	 *     affwp payout list --fields=affiliate_id,amount,date --format=json
	 *
	 * @subcommand list
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function list_( $_, $assoc_args ) {
		$formatter = $this->get_formatter( $assoc_args );

		$fields = $this->get_fields( $assoc_args );

		// Handle ID alias.
		if ( isset( $assoc_args['ID'] ) ) {
			$assoc_args['payout_id'] = $assoc_args['ID'];
			unset( $assoc_args['ID'] );
		}

		$args = $assoc_args;

		if ( 'count' == $formatter->format ) {
			$payouts = affiliate_wp()->affiliates->payouts->count( $args );

			\WP_CLI::line( sprintf( __( 'Number of payouts: %d', 'affiliate-wp' ), $payouts ) );
		} else {
			$payouts = affiliate_wp()->affiliates->payouts->get_payouts( $args );
			$payouts = $this->process_extra_fields( $fields, $payouts );

			if ( 'ids' == $formatter->format ) {
				$payouts = wp_list_pluck( $payouts, 'payout_id' );
			} else {
				$payouts = array_map( function( $payout ) {
					$payout->ID = $payout->payout_id;

					return $payout;
				}, $payouts );
			}

			$formatter->display_items( $payouts );
		}
	}

	/**
	 * Handler for the 'amount' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Affiliate\Payout $item Payout object (passed by reference).
	 */
	protected function amount_field( &$item ) {
		$amount = affwp_currency_filter( affwp_format_amount( $item->amount ) );

		/** This filter is documented in includes/admin/payouts/payouts.php */
		$amount = apply_filters( 'affwp_payout_table_amount', $amount, $item );

		$item->amount = html_entity_decode( $amount );
	}

	/**
	 * Handler for the 'affiliate_email' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Affiliate\Payout $item Payout object (passed by reference).
	 */
	protected function affiliate_email_field( &$item ) {
		$item->affiliate_email = affwp_get_affiliate_email( $item->affiliate_id );
	}

	/**
	 * Handler for the 'date' field.
	 *
	 * Reformats the date for display.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Affiliate\Payout $item Payout object (passed by reference).
	 */
	protected function date_field( &$item ) {
		$item->date = mysql2date( 'M j, Y', $item->date, false );
	}

}

try {

	\WP_CLI::add_command( 'affwp payout', 'AffWP\Affiliate\Payout\CLI\Sub_Commands' );

} catch( \Exception $exception ) {

	affiliate_wp()->utils->log( $exception->getCode() . ' - ' . $exception->getMessage() );

}
