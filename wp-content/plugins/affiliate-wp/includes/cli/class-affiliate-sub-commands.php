<?php
namespace AffWP\Affiliate\CLI;

use \AffWP\CLI\Sub_Commands\Base;
use \WP_CLI\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP-CLI sub-commands for managing affiliates.
 *
 * @since 1.9
 *
 * @see \AffWP\CLI\Sub_Commands\Base
 */
class Sub_Commands extends Base {

	/**
	 * Affiliate display fields.
	 *
	 * @since 1.9
	 * @access protected
	 * @var array
	 */
	protected $obj_fields = array(
		'ID',
		'name',
		'user_login',
		'user_id',
		'rate',
		'rate_type',
		'status',
		'earnings',
		'unpaid_earnings',
		'referrals',
		'visits',
		'date_registered',
	);

	/**
	 * Sets up the fetcher for sanity-checking.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @see \AffWP\Affiliate\CLI\Fetcher
	 */
	public function __construct() {
		$this->fetcher = new Fetcher();
	}

	/**
	 * Retrieves an affiliate object or field(s) by ID.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The affiliate ID to retrieve.
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole affiliate object, returns the value of a single field.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields. Defaults to all fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, json, csv, yaml. Default: table
	 *
	 * ## EXAMPLES
	 *
	 *     # save the affiliate field value to a file
	 *     wp post get 12 --field=earnings > earnings.txt
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function get( $args, $assoc_args ) {
		parent::get( $args, $assoc_args );
	}

	/**
	 * Creates an affiliate account.
	 *
	 * ## OPTIONS
	 *
	 * <username|id>
	 * : Username or ID for an existing user.
	 *
	 * [--payment_email=<email>]
	 * : Affiliate payment email. If not specified, the user account email will be used.
	 * 
	 * [--rate=<float>]
	 * : Referral rate. If not specified, the default rate will be used.
	 * 
	 * [--rate_type=<type>]
	 * : Referral rate type. Accepts 'percentage', 'flat', or any custom rate type.
	 *
	 * If not specified, the default rate type will be used.
	 * 
	 * [--status=<status>]
	 * : Affiliate status. Accepts 'active', 'inactive', or 'pending'.
	 *
	 * If not specified, and new affiliates require approval per global settings, 'pending' will used. Otherwise, the default status of 'active' will be used.
	 *
	 * [--earnings=<number>]
	 * : Affiliate earnings. If not specified, 0 will be used.
	 *
	 * [--referrals=<number>]
	 * : Number of referrals. If not specified, 0 will be used.
	 *
	 * [--visits=<number>]
	 * : Number of visits. If not specified, 0 will be used.
	 *
	 * ## EXAMPLES
	 *
	 *     # Creates an affiliate with a 20% referral rate.
	 *     wp affwp affiliate create edduser1 --rate=0.2 --rate_type=percentage
	 *
	 *     # Creates an affiliate with the default referral rate, rate type, specified payment email, and 20 visits
	 *     wp affwp affiliate create woouser1 --payment_email=affwprocks@woo.dev --visits=20
	 *
	 *     # Creates an affiliate for user ID 142 with a status of 'pending'
	 *     wp affwp affiliate create 142 --status=pending
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function create( $args, $assoc_args ) {

		// Check validity of username or ID, retrieve the user object.
		if ( empty( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid username must be specified as the first argument.', 'affiliate-wp' ) );
		} else {
			$field = is_numeric( $args[0] ) ? 'id' : 'login';

			if ( ! $user = get_user_by( $field, $args[0] ) ) {
				\WP_CLI::error( sprintf( __( 'A user with the ID or username "%s" does not exist. See wp help user create for registering new users.', 'affiliate-wp' ), $args[0] ) );
			}
		}

		// Bail if this user already has an affiliate account.
		if ( affiliate_wp()->affiliates->get_by( 'user_id', $user->ID ) ) {
			\WP_CLI::error( __( 'An affiliate already exists for this user account.', 'affiliate-wp' ) );
		}

		// Grab flag values.
		$data['payment_email'] = Utils\get_flag_value( $assoc_args, 'payment_email', '' );
		$data['rate']          = Utils\get_flag_value( $assoc_args, 'rate'         , '' );
		$data['rate_type']     = Utils\get_flag_value( $assoc_args, 'rate_type'    , '' );
		$data['status']        = Utils\get_flag_value( $assoc_args, 'status'       , '' );
		$data['earnings']      = Utils\get_flag_value( $assoc_args, 'earnings'     , 0  );
		$data['referrals']     = Utils\get_flag_value( $assoc_args, 'referrals'    , 0  );
		$data['visits']        = Utils\get_flag_value( $assoc_args, 'visits'       , 0  );
		$data['user_id']       = $user->ID;

		// Add the affiliate.
		$affiliate = affwp_add_affiliate( $data );

		if ( $affiliate ) {
			\WP_CLI::success( sprintf( __( 'An affiliate with the username "%s" has been created.', 'affiliate-wp' ), $user->user_login ) );
 		} else {
			\WP_CLI::error( __( 'The affiliate account could not be added.', 'affiliate-wp' ) );
		}
	}

	/**
	 * Updates an existing affiliate.
	 *
	 * ## OPTIONS
	 *
	 * <username|id>
	 * : Username or affiliate ID.
	 *
	 * [--account_email=<email>]
	 * : Affiliate account email.
	 *
	 * [--payment_email=<email>]
	 * : Affiliate payment email.
	 *
	 * [--rate=<float>]
	 * : Referral rate.
	 *
	 * [--rate_type=<type>]
	 * : Referral rate type. Accepts 'percentage', 'flat', or any custom rate type.
	 *
	 * [--status=<status>]
	 * : Affiliate status. Accepts 'active', 'inactive', 'pending', or 'rejected'.
	 *
	 * ## EXAMPLES
	 *
	 *     # Updates affiliateuser20 to a 0.05 referral rate
	 *     wp affwp affiliate update affiliateuser20 --rate=0.05
	 *
	 *     # Updates the status for affiliate 64 to 'inactive'
	 *     wp affwp affiliate update 64 --status='inactive'
	 *
	 *     # Updates woouserftw's user account email to affwprocks@woo.dev
	 *     wp affwp affiliate update woouserftw --account_email=affwprocks@woo.dev
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments
	 * @param array $assoc_args Associated arguments.
	 */
	public function update( $args, $assoc_args ) {
		if ( empty( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid affiliate username or ID is required to proceed.', 'affiliate-wp' ) );
		}

		if ( ! $affiliate = affwp_get_affiliate( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid affiliate username or ID is required to proceed.', 'affiliate-wp' ) );
		}

		$data['affiliate_id']  = $affiliate->affiliate_id;
		$data['payment_email'] = Utils\get_flag_value( $assoc_args, 'payment_email', $affiliate->payment_email );
		$data['rate']          = Utils\get_flag_value( $assoc_args, 'rate',          $affiliate->rate          );
		$data['rate_type']     = Utils\get_flag_value( $assoc_args, 'rate_type',     $affiliate->rate_type     );
		$data['status']        = Utils\get_flag_value( $assoc_args, 'status',        $affiliate->status        );

		$update = affwp_update_affiliate( $data );

		if ( $update ) {
			\WP_CLI::success( __( 'The affiliate was updated successfully.', 'affiliate-wp' ) );
		} else {
			\WP_CLI::error( __( 'The affiliate account could not be updated.', 'affiliate-wp' ) );
		}
	}

	/**
	 * Deletes an affiliate.
	 *
	 * ## OPTIONS
	 *
	 * <username|affiliate_id>
	 * : Username or affiliate ID.
	 *
	 * [--delete_data]
	 * : Whether to delete affiliate data, such as referrals, visits, etc.
	 * Data will be retained by default.
	 *
	 * [--delete_user]
	 * : Whether to delete the user account associated with the affiliate account. Default false.
	 *
	 * [--network]
	 * : Whether to delete the user account network-wide (multisite only).
	 * Ignored if --delete_user is omitted. Default false.
	 *
	 * ## EXAMPLES
	 *
	 *     # Deletes the affiliateuser03 account, retaining its associated data and user account
	 *     wp affwp affiliate delete affiliateuser03
	 *
	 *     # Deletes the affiliate with ID 636 along with its associated data
	 *     wp affwp affiliate delete 636 --delete_data
	 *
	 *     # Deletes the networkuser25 affiliate and its associated user account
	 *     wp affwp affiliate delete networkuser25 --delete_user
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function delete( $args, $assoc_args ) {
		if ( empty( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid affiliate username or ID is required to proceed.', 'affiliate-wp' ) );
		}

		if ( ! $affiliate = affwp_get_affiliate( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid affiliate username or ID is required to proceed.', 'affiliate-wp' ) );
		}

		$delete_data = Utils\get_flag_value( $assoc_args, 'delete_data', false );
		$delete_user = Utils\get_flag_value( $assoc_args, 'delete_user', false );
		$network     = Utils\get_flag_value( $assoc_args, 'network',     false ) && is_multisite();

		if ( $delete_data ) {
			if ( $delete_user ) {
				$message = __( 'Are you sure you want to delete this affiliate, all its data and its associated user account?', 'affiliate-wp' );
			} else {
				$message = __( 'Are you sure you want to delete this affiliate and all its data?', 'affiliate-wp' );
			}
		} else {
			$message = __( 'Are you sure you want to delete this affiliate?', 'affiliate-wp' );
		}

		// Affiliate deletion.
		\WP_CLI::confirm( $message, $assoc_args );

		$affiliate_deleted = affwp_delete_affiliate( $affiliate, $delete_data );

		if ( $affiliate_deleted ) {
			if ( $delete_user ) {
				if ( $network ) {
					$user_deleted = wpmu_delete_user( $affiliate->user_id );
				} else {
					$user_deleted = wp_delete_user( $affiliate->user_id );
				}

				if ( $user_deleted ) {
					$success = __( 'The affiliate and its associated user account have been successfully deleted.', 'affiliate-wp' );
				}
			} else {
				$success = __( 'The affiliate account has been successfully deleted.', 'affiliate-wp' );
			}
		} else {
			\WP_CLI::error( __( 'The affiliate account could not be deleted.', 'affiliate-wp' ) );
		}

		\WP_CLI::success( $success );
	}

	/**
	 * Displays a list of affiliates.
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : One or more args to pass to get_affiliates(). 'user_login' and 'name' are not affiliate fields and will be ignored.
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each affiliate.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific affiliate fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count, ids, yaml. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each affiliate:
	 *
	 * * ID (alias for affiliate_id)
	 * * name
	 * * user_login
	 * * user_id
	 * * rate
	 * * rate_type
	 * * status
	 * * earnings
	 * * unpaid_earnings
	 * * referrals
	 * * visits
	 * * date_registered
	 *
	 * ## EXAMPLES
	 *
	 *     # Lists affiliates by affiliate_id
	 *     wp affwp affiliate list --field=affiliate_id
	 *
	 *     # Outputs a table with affiliate_id, rate, and earnings columns for affiliates set
	 *     # as the 'percentage' rate type
	 *
	 *     wp affwp affiliate list --rate_type=percentage --fields=affiliate_id,rate,earnings
	 *
	 *     # Outputs a table with standard user_login, affiliate_id, earnings, referrals, and
	 *     # visits columns, ordered by status ascending.
	 *
	 *     affwp affiliate list --orderby=status --order=ASC
	 *
	 * @subcommand list
	 */
	public function list_( $_, $assoc_args ) {
		$formatter = $this->get_formatter( $assoc_args );

		$fields = $this->get_fields( $assoc_args );

		$defaults = array(
			'order' => 'ASC',
		);

		// Handle ID alias.
		if ( isset( $assoc_args['ID'] ) ) {
			$assoc_args['affiliate_id'] = $assoc_args['ID'];
			unset( $assoc_args['ID'] );
		}

		$args = array_merge( $defaults, $assoc_args );

		if ( 'count' == $formatter->format ) {
			$affiliates = affiliate_wp()->affiliates->count( $args );

			\WP_CLI::line( sprintf( __( 'Number of affiliates: %d', 'affiliate-wp' ), $affiliates ) );
		} else {
			$affiliates = affiliate_wp()->affiliates->get_affiliates( $args );

			$affiliates = $this->process_extra_fields( $fields, $affiliates );

			if ( 'ids' == $formatter->format ) {
				$affiliates = wp_list_pluck( $affiliates, 'affiliate_id' );
			} else {
				$affiliates = array_map( function( $affiliate ) {
					$affiliate->ID = $affiliate->affiliate_id;

					return $affiliate;
				}, $affiliates );
			}

			$formatter->display_items( $affiliates );
		}
	}

	/**
	 * Handler for the 'payment_email' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Affiliate &$item Affiliate object (passed by reference).
	 */
	protected function payment_email_field( &$item ) {
		if ( empty( $item->payment_email ) ) {
			$item->payment_email = affwp_get_affiliate_payment_email( $item->affiliate_id );
		}
	}

	/**
	 * Handler for the 'user_login' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Affiliate &$item Affiliate object (passed by reference).
	 */
	protected function user_login_field( &$item ) {
		$user = get_user_by( 'id', $item->user_id );
		$item->user_login = $user->user_login;
	}

	/**
	 * Handler for the 'name' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Affiliate &$item Affiliate object (passed by reference).
	 */
	protected function name_field( &$item ) {
		$item->name = affwp_get_affiliate_name( $item );
	}

	/**
	 * Handler for the 'earnings' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Affiliate &$item Affiliate object (passed by reference).
	 */
	protected function earnings_field( &$item ) {
		if ( empty( $item->earnings ) ) {
			$item->earnings = 0;
		}
		$item->earnings = html_entity_decode( affwp_currency_filter( affwp_format_amount( $item->earnings ) ) );
	}

	/**
	 * Handler for the 'date_registered' field.
	 *
	 * Reformats the registration date for display.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Affiliate &$item Affiliate object (passed by reference).
	 */
	protected function date_registered_field( &$item ) {
		$item->date_registered = mysql2date( 'M j, Y', $item->date_registered, false );
	}

	/**
	 * Handler for the 'rate' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Affiliate &$item Affiliate object (passed by reference).
	 */
	protected function rate_field( &$item ) {
		$item->rate = html_entity_decode( affwp_get_affiliate_rate( $item, true ) );
	}

	/**
	 * Handler for the 'rate_type' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Affiliate &$item Affiliate object (passed by reference).
	 */
	protected function rate_type_field( &$item ) {
		if ( empty( $item->rate_type ) ) {
			$item->rate_type = affwp_get_affiliate_rate_type( $item );
		}
	}
}

\WP_CLI::add_command( 'affwp affiliate', 'AffWP\Affiliate\CLI\Sub_Commands' );
