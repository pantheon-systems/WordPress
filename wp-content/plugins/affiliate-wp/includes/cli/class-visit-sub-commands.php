<?php
namespace AffWP\Visit\CLI;

use \AffWP\CLI\Sub_Commands\Base;
use \WP_CLI\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP-CLI sub-commands for managing visits.
 *
 * @since 1.9
 *
 * @see \AffWP\CLI\Sub_Commands\Base
 */
class Sub_Commands extends Base {

	/**
	 * Visit display fields.
	 *
	 * @since 1.9
	 * @access protected
	 * @var array
	 */
	protected $obj_fields = array(
		'ID',
		'visit_url',
		'referrer',
		'affiliate_name',
		'affiliate_id',
		'referral_id',
		'context',
		'campaign',
		'ip',
		'converted',
		'date'
	);

	/**
	 * Sets up the fetcher for sanity-checking.
	 *
	 * @since 1.9
	 * @access public
	 * 
	 * @see \AffWP\Visit\CLI\Fetcher
	 */
	public function __construct() {
		$this->fetcher = new Fetcher();
	}

	/**
	 * Retrieves a visit object or field(s) by ID.
	 *
	 * ## OPTIONS
	 *
	 * <ID>
	 * : The visit ID to retrieve.
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole visit object, returns the value of a single field.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields. Defaults to all fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, json, csv, yaml. Default: table
	 *
	 * ## EXAMPLES
	 */
	public function get( $args, $assoc_args ) {
		parent::get( $args, $assoc_args );
	}

	/**
	 * Adds a visit.
	 *
	 * ## OPTIONS
	 *
	 * <username|affiliate_id>
	 * : Affiliate ID or username.
	 *
	 * <URL>
	 * : URL to associate the visit with.
	 *
	 * [--referral_id=<referral_id>]
	 * : Referral ID.
	 *
	 * [--referrer=<URL>]
	 * : The referring URL. Left empty, the referrer will be considered 'Direct Traffic'.
	 *
	 * [--context=<slug>]
	 * : The context under which the visit is generated. Default empty.
	 *
	 * [--campaign=<campaign>]
	 * : Campaign to associate the visit with.
	 *
	 * [--ip=<IP>]
	 * : IP address of the visitor.
	 *
	 * [--date=<date_string>]
	 * : Date the visit was generated.
	 *
	 * ## EXAMPLES
	 *
	 *     # Adds a new visit associated with the jigouser1 affiliate for the URL https://affiliatewp.com
	 *     wp affwp visit create jigouser1 --visit_url=https://affiliatewp.com
	 *
	 *     # Adds a new visit associated with affiliate ID 17 for the URL
	 *     wp affwp visit create
	 *
	 *     #
	 *     wp affwp visit create
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function create( $args, $assoc_args ) {
		// Affiliate ID or username.
		if ( empty( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid affiliate username or ID must be specified as the first argument.', 'affiliate-wp' ) );
		}

		if ( ! $affiliate = affwp_get_affiliate( $args[0] ) ) {
			\WP_CLI::error( sprintf( __( 'An affiliate with the ID or username "%s" does not exist. See wp affwp affiliate create for adding affiliates.', 'affiliate-wp' ), $args[0] ) );
		} else {
			$data['affiliate_id'] = $affiliate->affiliate_id;
		}

		// URL.
		if ( empty( $args[1] ) ) {
			\WP_CLI::error( __( 'A URL must be specified as the second argument to proceed.', 'affiliate-wp' ) );
		}
		$data['url'] = affwp_sanitize_visit_url( $args[1] );

		// Referral ID.
		$referral_id = Utils\get_flag_value( $assoc_args, 'referral_id' );

		if ( $referral_id ) {
			if ( ! $referral = affwp_get_referral( $referral_id ) ) {
				// If the referral_id is invalid, fall back to the column default.
				\WP_CLI::warning( __( 'An invalid referral ID was specified. Using 0 (none) instead.', 'affiliate-wp' ) );
			} else {
				$data['referral_id'] = $referral->referral_id;
			}
		}

		// Context.
		$context = Utils\get_flag_value( $assoc_args, 'context' );

		if ( $context ) {
			$data['context'] = sanitize_key( substr( $context, 0, 50 ) );
		}

		// Date.
		$_date = Utils\get_flag_value( $assoc_args, 'date' );

		if ( is_string( $_date ) ) {

			if ( ! preg_match( '/^([0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2})$/', $_date ) ) {
				// If the supplied date string is invalid, fall back to the column default.
				\WP_CLI::warning( __( 'A valid date string must be supplied. Using the current time.', 'affiliate-wp' ) );
			} else {
				$data['date'] = $_date;
			}
		}

		$data['referrer'] = Utils\get_flag_value( $assoc_args, 'referrer', '' );
		$data['campaign'] = Utils\get_flag_value( $assoc_args, 'campaign', '' );
		$data['ip']       = Utils\get_flag_value( $assoc_args, 'ip'      , '' );


		$visit_id = affiliate_wp()->visits->add( $data );

		if ( $visit_id ) {
			\WP_CLI::success( __( 'The visit was successfully created.', 'affiliate-wp' ) );
		} else {
			\WP_CLI::error( __( 'The visit could not be created.', 'affiliate-wp' ) );
		}
	}

	/**
	 * Updates a visit.
	 *
	 * ## OPTIONS
	 *
	 * <visit_id>
	 * : Visit ID.
	 *
	 * [--affiliate=<username|affiliate_id>]
	 * : Affiliate ID or username.
	 *
	 * [--referral_id=<referral_id>]
	 * : Referral ID.
	 *
	 * [--context=<slug>]
	 * : Visit context.
	 *
	 * [--visit_url=<URL>]
	 * : The URL that generated the visit.
	 *
	 * [--referrer=<URL>]
	 * : The referring URL. Left empty, the referrer will be considered 'Direct Traffic'.
	 *
	 * [--campaign=<campaign>]
	 * : Campaign to associate the visit with.
	 *
	 * [--ip=<IP>]
	 * : IP address of the visitor.
	 *
	 * [--date=<date_string>]
	 * : Date the visit was generated.
	 *
	 * ## EXAMPLES
	 *
	 *     # Updates the affiliate and referral associated with visit ID 20
	 *     wp affwp visit update 20 --affiliate=woouser1 --referral_id=33
	 *
	 *     # Updates the campaign associated with visit ID 199 to 'Spring Sale'
	 *     wp affwp visit update 199 --campaign='Spring Sale'
	 *
	 *     # Updates the creation date for visit ID 15
	 *     wp affwp visit update 15 --date='2016-04-06 18:07:35'
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function update( $args, $assoc_args ) {
		if ( empty( $args[0] ) || ! is_numeric( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid visit ID is required to proceed.', 'affiliate-wp' ) );
		} else {
			if ( ! $visit = affiliate_wp()->visits->get( $args[0] ) ) {
				\WP_CLI::error( __( 'A valid visit ID is required to proceed.', 'affiliate-wp' ) );
			}
		}

		$data = array();
		// Affiliate by username or ID.
		$_affiliate = Utils\get_flag_value( $assoc_args, 'affiliate', $visit->affiliate_id );

		if ( ! $_affiliate = affwp_get_affiliate( $_affiliate ) ) {
			\WP_CLI::error( __( 'A valid affiliate username or ID is required to proceed.', 'affiliate-wp' ) );
		} else {
			$data['affiliate_id'] = $_affiliate->affiliate_id;
		}

		// Date. Expecting YYYY-MM-DD HH:MM:SS format.
		$_date = Utils\get_flag_value( $assoc_args, 'date', $visit->date );

		if ( is_string( $_date ) ) {

			if ( ! preg_match( '/^([0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2})$/', $_date ) ) {
				\WP_CLI::warning( __( 'A valid new date string must be supplied. Using the original date.', 'affiliate-wp' ) );

				$data['date'] = $visit->date;
			} else {
				$data['date'] = $_date;
			}
		}

		$data['referral_id'] = Utils\get_flag_value( $assoc_args, 'referral_id', $visit->referral_id );
		$data['url']         = Utils\get_flag_value( $assoc_args, 'visit_url',   $visit->url         );
		$data['referrer']    = Utils\get_flag_value( $assoc_args, 'referrer',    $visit->referrer    );
		$data['campaign']    = Utils\get_flag_value( $assoc_args, 'campaign',    $visit->campaign    );
		$data['context']     = Utils\get_flag_value( $assoc_args, 'context',     $visit->context     );
		$data['ip']          = Utils\get_flag_value( $assoc_args, 'ip',          $visit->ip          );

		$updated = affiliate_wp()->visits->update_visit( $visit->visit_id, $data );

		if ( $updated ) {
			\WP_CLI::success( __( 'The visit was updated successfully.', 'affiliate-wp' ) );
		} else {
			\WP_CLI::error( __( 'The visit could not be updated.', 'affiliate-wp' ) );
		}
	}

	/**
	 * Deletes a visit.
	 *
	 * ## OPTIONS
	 *
	 * <visit_id>
	 * : Visit ID.
	 *
	 * ## EXAMPLES
	 *
	 *     # Deletes the visit with ID 20
	 *     wp affwp visit delete 20
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

				\WP_CLI::error( __( 'A valid visit ID is required to proceed.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		} else {
			if ( ! $visit = affiliate_wp()->visits->get( $args[0] ) ) {
				try {

					\WP_CLI::error( __( 'A valid visit ID is required to proceed.', 'affiliate-wp' ) );

				} catch( \Exception $exception ) {}
			}
		}

		\WP_CLI::confirm( __( 'Are you sure you want to delete this visit?', 'affiliate-wp' ), $assoc_args );

		$deleted = affwp_delete_visit( $visit );

		if ( $deleted ) {
			\WP_CLI::success( __( 'The visit has been successfully deleted.', 'affiliate-wp' ) );
		} else {
			try {

				\WP_CLI::error( __( 'The visit could not be deleted.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}
	}

	/**
	 * Displays a list of visits.
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : One or more args to pass to get_visits().
	 *
	 * 'converted' is not a visit field and will be ignored. Use 'visit_url' for 'url'.
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each visit.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific visit fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count, ids, yaml. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each referral:
	 *
	 * * ID (alias for visit_id)
	 * * visit_url (alias for 'url')
	 * * referrer
	 * * affiliate_name
	 * * affiliate_id
	 * * referral_id
	 * * context
	 * * campaign
	 * * ip (IP address)
	 * * converted
	 * * date
	 *
	 * ## EXAMPLES
	 *
	 *     #
	 *     wp affwp referral list --field=affiliate_id
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

		// Handle 'visit_id' alias.
		if ( isset( $assoc_args['ID'] ) ) {
			$assoc_args['visit_id'] = $assoc_args['ID'];
			unset( $assoc_args['ID'] );
		}

		// Handle 'url' alias.
		if ( isset( $assoc_args['visit_url'] ) ) {
			$assoc_args['url'] = $assoc_args['visit_url'];
			unset( $assoc_args['visit_url'] );
		}

		$args = array_merge( $defaults, $assoc_args );

		if ( 'count' == $formatter->format ) {
			$visits = affiliate_wp()->visits->count( $args );

			\WP_CLI::line( sprintf( __( 'Number of visits: %d', 'affiliate-wp' ), $visits ) );
		} else {
			$visits = affiliate_wp()->visits->get_visits( $args );
			$visits = $this->process_extra_fields( $fields, $visits );

			if ( 'ids' == $formatter->format ) {
				$visits = wp_list_pluck( $visits, 'visit_id' );
			} else {
				$visits = array_map( function( $visit ) {
					$visit->ID = $visit->visit_id;

					return $visit;
				}, $visits );
			}

			$formatter->display_items( $visits );
		}
	}

	/**
	 * Handler for the 'visit_url' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Visit $item Visit object (passed by reference).
	 */
	protected function visit_url_field( &$item ) {
		$item->visit_url = $item->url;
	}

	/**
	 * Handler for the 'referrer' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Visit $item Visit object (passed by reference).
	 */
	protected function referrer_field( &$item ) {
		if ( empty( $item->referrer ) ) {
			$item->referrer = _x( 'Direct', 'direct traffic', 'affiliate-wp' );
		}
	}

	/**
	 * Handler for the 'affiliate_name' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Visit $item Visit object (passed by reference).
	 */
	protected function affiliate_name_field( &$item ) {
		$item->affiliate_name = affwp_get_affiliate_name( $item->affiliate_id );
	}

	/**
	 * Handler for the 'converted' field.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Visit $item Visit object (passed by reference).
	 */
	protected function converted_field( &$item ) {
		$item->converted = ! empty( $item->referral_id ) ? 'yes' : 'no';
	}

	/**
	 * Handler for the 'date' field.
	 *
	 * Re-formats the date for display.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Visit $item Visit object (passed by reference).
	 */
	protected function date_field( &$item ) {
		$item->date = mysql2date( 'M j, Y', $item->date, false );
	}

}

try {

	\WP_CLI::add_command( 'affwp visit', 'AffWP\Visit\CLI\Sub_Commands' );

} catch( \Exception $exception ) {

	affiliate_wp()->utils->log( $exception->getCode() . ' - ' . $exception->getMessage() );

}
