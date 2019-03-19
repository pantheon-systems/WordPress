<?php
namespace AffWP\Creative\CLI;

use \AffWP\CLI\Sub_Commands\Base;
use \WP_CLI\Utils as Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP-CLI sub-commands for managing creatives.
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
		'name',
		'status',
		'url',
		'image',
		'date'
	);

	/**
	 * Sets up the fetcher for sanity-checking.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @see \AffWP\Creative\CLI\Fetcher
	 */
	public function __construct() {
		$this->fetcher = new Fetcher();
	}

	/**
	 * Retrieves a creative object or field(s) by ID.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The creative ID to retrieve.
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole creative object, returns the value of a single field.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields. Defaults to all fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, json, csv, yaml. Default: table
	 *
	 * ## EXAMPLES
	 *
	 *     # Retrieve creative ID 12.
	 *     wp affwp creative get 12
	 */
	public function get( $args, $assoc_args ) {
		parent::get( $args, $assoc_args );
	}

	/**
	 * Adds a creative.
	 *
	 * ## OPTIONS
	 *
	 * [--name=<name>]
	 * : Required. Name identifier for the creative.
	 *
	 * [--description=<description>]
	 * : Description for the creative.
	 *
	 * [--link=<URL>]
	 * : URL the creative should link to.
	 *
	 * [--text=<text>]
	 * : Text for the creative.
	 *
	 * [--image=<URL>]
	 * : Image URL (local or external) to use for the creative.
	 *
	 * [--status=<status>]
	 * : Status for the creative. Accepts 'active' or 'inactive'. Default 'active'.
	 *
	 * ## EXAMPLES
	 *
	 *     # Creates a creative linking to http://affiliatewp.com
	 *     wp affwp creative create --name=AffiliateWP --link=http://affiliatewp.com
	 *
	 *     # Creates a creative using a locally-hosted image.
	 *     wp affwp creative create --name='Special Case' --image=https://example.org/my-image.jpg
	 *
	 *     # Create a creative with a status of 'inactive'
	 *     wp affwp creative create --name='My Creative' --status=inactive
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @internal The --link flag maps to 'url' because --url is a global WP CLI flag.
	 *
	 * @param array $_          Top-level arguments (unused).
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function create( $_, $assoc_args ) {
		$name = Utils\get_flag_value(  $assoc_args, 'name', '' );

		if ( empty( $name ) ) {
			try {

				\WP_CLI::error( __( 'A --name value must be specified to add a new creative.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}

		$data['name']        = $name;
		$data['description'] = Utils\get_flag_value(  $assoc_args, 'description', ''       );
		$data['url']         = Utils\get_flag_value(  $assoc_args, 'link',        ''       );
		$data['text']        = Utils\get_flag_value(  $assoc_args, 'text',        ''       );
		$data['image']       = Utils\get_flag_value(  $assoc_args, 'image',       ''       );
		$data['status']      = Utils\get_flag_value(  $assoc_args, 'status',      'active' );

		$created = affwp_add_creative( $data );

		if ( $created ) {
			$creative = affiliate_wp()->creatives->get_by( 'name', $data['name'] );
			\WP_CLI::success( sprintf( __( 'A creative with the ID %d has been successfully created.', 'affiliate-wp' ), $creative->creative_id ) );
		} else {
			try {

				\WP_CLI::error( __( 'The creative could not be created.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}
	}

	/**
	 * Updates a creative.
	 *
	 * ## OPTIONS
	 *
	 * [--name=<name>]
	 * : Name identifier for the creative.
	 *
	 * [--description=<description>]
	 * : Description for the creative.
	 *
	 * [--link=<URL>]
	 * : URL the creative should link to.
	 *
	 * [--text=<text>]
	 * : Text for the creative.
	 *
	 * [--image=<URL>]
	 * : Image URL (local or external) to use for the creative.
	 *
	 * [--status=<status>]
	 * : Status for the creative. Accepts 'active' or 'inactive'. Default 'active'.
	 *
	 * ## EXAMPLES
	 *
	 *     # Updates creative ID 300 with a new name 'New Name'
	 *     wp affwp creative update 300 --name='New Name'
	 *
	 *     # Updates creative ID 53 with a new image.
	 *     wp affwp creative update 53 --image=https://example.org/my-other-image.jpg
	 *
	 *     # Updates creative ID 199's status to inactive
	 *     wp affwp creative update 199 --status=inactive
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function update( $args, $assoc_args ) {
		if ( empty( $args[0] ) ) {
			try {

				\WP_CLI::error( __( 'A valid creative ID is required to proceed.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}

		if ( ! $creative = affwp_get_creative( $args[0] ) ) {
			try {

				\WP_CLI::error( __( 'A valid creative ID is required to proceed.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}

		$data['name']        = Utils\get_flag_value( $assoc_args, 'name', $creative->name        );
		$data['description'] = Utils\get_flag_value( $assoc_args, 'name', $creative->description );
		$data['url']         = Utils\get_flag_value( $assoc_args, 'name', $creative->link        );
		$data['text']        = Utils\get_flag_value( $assoc_args, 'name', $creative->text        );
		$data['image']       = Utils\get_flag_value( $assoc_args, 'name', $creative->image       );
		$data['status']      = Utils\get_flag_value( $assoc_args, 'name', $creative->status      );
		$data['creative_id'] = $creative->creative_id;

		$updated = affwp_update_creative( $data );

		if ( $updated ) {
			\WP_CLI::success( __( 'The creative was successfully updated.', 'affiliate-wp' ) );
		} else {
			try {

				\WP_CLI::error( __( 'The creative could not be updated.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}
	}

	/**
	 * Deletes a creative.
	 *
	 * ## OPTIONS
	 *
	 * <creative_id>
	 * : Creative ID.
	 *
	 * ## EXAMPLES
	 *
	 *     # Deletes the creative with ID 20
	 *     wp affwp creative delete 20
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function delete( $args, $assoc_args ) {
		if ( empty( $args[0] ) ) {
			try {

				\WP_CLI::error( __( 'A valid creative ID is required to proceed.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}

		if ( ! $creative = affwp_get_creative( $args[0] ) ) {
			try {

				\WP_CLI::error( __( 'A valid creative ID is required to proceed.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}

		$deleted = affwp_delete_creative( $creative );

		if ( $deleted ) {
			\WP_CLI::success( __( 'The creative was successfully deleted.', 'affiliate-wp' ) );
		} else {
			try {

				\WP_CLI::error( __( 'The creative could not be deleted.', 'affiliate-wp' ) );

			} catch( \Exception $exception ) {}
		}
	}

	/**
	 * Displays a list of creatives.
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : One or more args to pass to get_creatives().
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each creative.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific creative fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count, ids, yaml. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each creative:
	 *
	 * * ID (alias for creative_id)
	 * * name
	 * * url
	 * * image
	 * * status
	 * * date
	 *
	 * These fields are optionally available:
	 *
	 * * description
	 * * text
	 *
	 * ## EXAMPLES
	 *
	 *     # List all creatives by name.
	 *     wp affwp creative list --field=name
	 *
	 *     # List all creative IDs with an 'inactive' status.
	 *     wp affwp creative list --status=inactive --format=ids
	 *
	 *     # List all creatives and display only the ID and image fields.
	 *     wp affwp creative list --fields=ID,image
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
			$assoc_args['creative_id'] = $assoc_args['ID'];
			unset( $assoc_args['ID'] );
		}

		$args = array_merge( $defaults, $assoc_args );

		if ( 'count' == $formatter->format ) {
			$creatives = affiliate_wp()->creatives->count( $args );

			\WP_CLI::line( sprintf( __( 'Number of creatives: %d', 'affiliate-wp' ), $creatives ) );
		} else {
			$creatives = affiliate_wp()->creatives->get_creatives( $args );
			$creatives = $this->process_extra_fields( $fields, $creatives );

			if ( 'ids' == $formatter->format ) {
				$creatives = wp_list_pluck( $creatives, 'creative_id' );
			} else {
				$creatives = array_map( function( $creative ) {
					$creative->ID = $creative->creative_id;

					return $creative;
				}, $creatives );
			}

			$formatter->display_items( $creatives );
		}
	}

	/**
	 * Handler for the 'date' field.
	 *
	 * Reformats the date for display.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param \AffWP\Creative $item Affiliate object (passed by reference).
	 */
	protected function date_field( &$item ) {
		$item->date = mysql2date( 'M j, Y', $item->date, false );
	}

}

try {

	\WP_CLI::add_command( 'affwp creative', 'AffWP\Creative\CLI\Sub_Commands' );

} catch( \Exception $exception ) {

	affiliate_wp()->utils->log( $exception->getCode() . ' - ' . $exception->getMessage() );

}
