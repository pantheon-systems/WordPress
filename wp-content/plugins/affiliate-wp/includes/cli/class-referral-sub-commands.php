<?php
namespace AffWP\Referral\CLI;

use \AffWP\CLI\Sub_Commands\Base;
use \WP_CLI\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP-CLI sub-commands for managing referrals.
 *
 * @since 1.9
 *
 * @see \AffWP\CLI\Sub_Commands\Base
 */
class Sub_Commands extends Base {

	/**
	 * Referral display fields.
	 *
	 * @since 1.9
	 * @access protected
	 * @var array
	 */
	protected $obj_fields = array(
		'ID',
		'amount',
		'affiliate_name',
		'affiliate_id',
		'visit_id',
		'reference',
		'description',
		'status',
		'date'
	);

	/**
	 * Sets up the fetcher for sanity-checking.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @see \AffWP\Referral\CLI\Fetcher
	 */
	public function __construct() {
		$this->fetcher = new Fetcher();
	}

	/**
	 * Retrieves a referral object or field(s) by ID.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The referral ID to retrieve.
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole referral object, returns the value of a single field.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields. Defaults to all fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, json, csv, yaml. Default: table
	 *
	 * ## EXAMPLES
	 *
	 *     # save the referral field value to a file
	 *     wp post get 12 --field=earnings > earnings.txt
	 */
	public function get( $args, $assoc_args ) {
		parent::get( $args, $assoc_args );
	}

	/**
	 * Adds a referral.
	 *
	 * ## OPTIONS
	 *
	 * <username|ID>
	 * : Affiliate username or ID
	 *
	 * [--amount=<number>]
	 * : Referral amount.
	 *
	 * [--description=<description>]
	 * : Referral description.
	 *
	 * [--reference=<reference>]
	 * : Referral reference (usually product information).
	 *
	 * [--context=<context>]
	 * : Referral context (usually related to the integration, e.g. woocommerce)
	 *
	 * [--status=<status>]
	 * : Referral status. Accepts 'unpaid', 'paid', 'pending', or 'rejected'.
	 *
	 * If not specified, 'pending' will be used.
	 *
	 * ## EXAMPLES
	 *
	 *     # Creates a referral for affiliate edduser1 with an amount of $2 and 'unpaid' status
	 *     wp affwp referral create edduser1 --amount=2 --status=unpaid
	 *
	 *     # Creates a referral for affiliate woouser1 with a context of woocommerce and 'pending' status
	 *     wp affwp referral create woouser1 --context=woocommerce --status=pending
	 *
	 *     # Creates a referral for affiliate ID 142 with description of "For services rendered."
	 *     wp affwp referral create 142 --description='For services rendered.'
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function create( $args, $assoc_args ) {
		if ( empty( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid affiliate username or ID must be specified as the first argument.', 'affiliate-wp' ) );
		}

		if ( ! $affiliate = affwp_get_affiliate( $args[0] ) ) {
			\WP_CLI::error( sprintf( __( 'An affiliate with the ID or username "%s" does not exist. See wp affwp affiliate create for adding affiliates.', 'affiliate-wp' ), $args[0] ) );
		}

		// Grab flag values.
		$data['amount']       = Utils\get_flag_value( $assoc_args, 'amount'     , '' );
		$data['description']  = Utils\get_flag_value( $assoc_args, 'description', '' );
		$data['reference']    = Utils\get_flag_value( $assoc_args, 'reference'  , '' );
		$data['context']      = Utils\get_flag_value( $assoc_args, 'context'    , '' );
		$data['status']       = Utils\get_flag_value( $assoc_args, 'status'     , '' );
		$data['affiliate_id'] = $affiliate->affiliate_id;
		$data['user_id']      = $affiliate->user_id;

		if ( ! in_array( $status, array( 'unpaid', 'paid', 'pending', 'rejected' ) ) ) {
			$status = 'pending';
		}

		$referral_id = affwp_add_referral( $data );

		if ( $referral_id ) {
			$referral = affwp_get_referral( $referral_id );
			\WP_CLI::success( sprintf( __( 'A referral with the ID "%d" has been created.', 'affiliate-wp' ), $referral->referral_id ) );
		} else {
			\WP_CLI::error( __( 'The referral could not be added.', 'affiliate-wp' ) );
		}
	}

	/**
	 * Updates a referral.
	 *
	 * ## OPTIONS
	 *
	 * <referral_id>
	 * : Referral ID.
	 *
	 * [--affiliate=<username|affiliate_id>]
	 * : Affiliate ID or username.
	 *
	 * [--amount=<number>]
	 * : Referral amount.
	 *
	 * [--description=<description>]
	 * : Referral description.
	 *
	 * [--reference=<reference>]
	 * : Referral reference (usually product information).
	 *
	 * [--context=<context>]
	 * : Referral context (usually related to the integration, e.g. woocommerce)
	 *
	 * [--status=<status>]
	 * : Referral status. Accepts 'unpaid', 'paid', 'pending', or 'rejected'.
	 *
	 * ## EXAMPLES
	 *
	 *     # Updates referral ID 120 with an amount of $1
	 *     wp affwp referral update 120 --amount=1
	 *
	 *     # Updates referral ID 33 with a status of 'paid'
	 *     wp affwp referral update 33 --status=paid
	 *
	 *     # Updates referral ID 50 to belong to affiliate username woouser1
	 *     wp affwp referral update 50 --affiliate=woouser1
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function update( $args, $assoc_args ) {
		if ( empty( $args[0] ) || ! is_numeric( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid referral ID is required to proceed.', 'affiliate-wp' ) );
		}

		if ( ! $referral = affwp_get_referral( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid referral ID is required to proceed.', 'affiliate-wp' ) );
		}

		$affiliate = Utils\get_flag_value( $assoc_args, 'affiliate', $referral->affiliate_id );

		if ( ! $affiliate = affwp_get_affiliate( $affiliate ) ) {
			\WP_CLI::error( __( 'A valid affiliate username or ID is required to proceed.', 'affiliate-wp' ) );
		} else {
			$data['affiliate_id'] = $affiliate->affiliate_id;
		}

		$data['amount']       = Utils\get_flag_value( $assoc_args, 'amount',       $referral->amount       );
		$data['description']  = Utils\get_flag_value( $assoc_args, 'description',  $referral->description  );
		$data['reference']    = Utils\get_flag_value( $assoc_args, 'reference',    $referral->reference    );
		$data['context']      = Utils\get_flag_value( $assoc_args, 'context',      $referral->context      );
		$data['status']       = Utils\get_flag_value( $assoc_args, 'status',       $referral->status       );

		$update = affiliate_wp()->referrals->update( $referral->referral_id, $data );

		if ( $update ) {
			\WP_CLI::success( __( 'The referral was updated successfully.', 'affiliate-wp' ) );
		} else {
			\WP_CLI::error( __( 'The referral could not be updated', 'affiliate-wp' ) );
		}

	}

	/**
	 * Deletes a referral.
	 *
	 * ## OPTIONS
	 *
	 * <referral_id>
	 * : Referral ID.
	 *
	 * ## EXAMPLES
	 *
	 *     # Deletes the referral with ID 20
	 *     wp affwp referral delete 20
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags, unused).
	 */
	public function delete( $args, $assoc_args ) {
		if ( empty( $args[0] ) || ! is_numeric( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid referral ID is required to proceed.', 'affiliate-wp' ) );
		}

		if ( ! $referral = affwp_get_referral( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid referral ID is required to proceed.', 'affiliate-wp' ) );
		}

		\WP_CLI::confirm( __( 'Are you sure you want to delete this referral?', 'affiliate-wp' ), $assoc_args );

		$deleted = affwp_delete_referral( $referral );

		if ( $deleted ) {
			\WP_CLI::success( __( 'The referral has been successfully deleted.', 'affiliate-wp' ) );
		} else {
			\WP_CLI::error( __( 'The referral could not be deleted.', 'affiliate-wp' ) );
		}
	}

	/**
	 * Displays a list of referrals.
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : One or more args to pass to get_referrals().
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each referral.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific referral fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count, ids, yaml. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each referral:
	 *
	 * * ID (alias for referral_id)
	 * * amount
	 * * affiliate_name
	 * * affiliate_id
	 * * visit_id
	 * * reference
	 * * description
	 * * status
	 * * date
	 *
	 * These fields are optionally available:
	 *
	 * * currency
	 * * custom
	 * * campaign
	 *
	 * ## EXAMPLES
	 *
	 * affwp referral list --field=affiliate_name
	 *
	 * affwp referral list --rate_type=percentage --fields=affiliate_id,rate,earnings
	 *
	 * affwp referral list --field=earnings --format=json
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

		$defaults = array(
			'order' => 'ASC',
		);

		// Handle ID alias.
		if ( isset( $assoc_args['ID'] ) ) {
			$assoc_args['referral_id'] = $assoc_args['ID'];
			unset( $assoc_args['ID'] );
		}

		$args = array_merge( $defaults, $assoc_args );

		if ( 'count' == $formatter->format ) {
			$referrals = affiliate_wp()->referrals->count( $args );

			\WP_CLI::line( sprintf( __( 'Number of referrals: %d', 'affiliate-wp' ), $referrals ) );
		} else {
			$referrals = affiliate_wp()->referrals->get_referrals( $args );
			$referrals = $this->process_extra_fields( $fields, $referrals );

			if ( 'ids' == $formatter->format ) {
				$referrals = wp_list_pluck( $referrals, 'referral_id' );
			} else {
				$referrals = array_map( function( $referral ) {
					$referral->ID = $referral->referral_id;

					return $referral;
				}, $referrals );
			}

			$formatter->display_items( $referrals );
		}
	}

	/**
	 * Handler for the 'amount' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Referral $item Referral object (passed by reference).
	 */
	protected function amount_field( &$item ) {
		$amount = affwp_currency_filter( affwp_format_amount( $item->amount ) );

		/** This filter is documented in includes/admin/referrals/referrals.php */
		$amount = apply_filters( 'affwp_referral_table_amount', $amount, $item );

		$item->amount = html_entity_decode( $amount );
	}

	/**
	 * Handler for the 'affiliate_name' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Referral $item Referral object (passed by reference).
	 */
	protected function affiliate_name_field( &$item ) {
		$item->affiliate_name = affwp_get_affiliate_name( $item->affiliate_id );
	}

	/**
	 * Handler for the 'date' field.
	 *
	 * Reformats the date for display.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Referral $item Referral object (passed by reference).
	 */
	protected function date_field( &$item ) {
		$item->date = mysql2date( 'M j, Y', $item->date, false );
	}

}

\WP_CLI::add_command( 'affwp referral', 'AffWP\Referral\CLI\Sub_Commands' );
