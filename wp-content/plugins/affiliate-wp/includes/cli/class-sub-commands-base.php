<?php
namespace AffWP\CLI\Sub_Commands;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Foundation superclass for AffWP CLI commands.
 *
 * @since 1.9
 * @abstract
 *
 * @see \WP_CLI\CommandWithDBObject
 */
abstract class Base extends \WP_CLI\CommandWithDBObject {

	/**
	 * Object fields.
	 *
	 * Should be defined in sub-classes as the default fields to retrieve/display.
	 *
	 * Supports "custom" fields if method handlers are added.
	 *
	 * @since 1.9
	 * @access protected
	 * @var array
	 */
	protected $obj_fields = array();

	/**
	 * Retrieves an object of a given type or ID.
	 *
	 * Should be extended by sub-classes with a call to the parent. This is to satisfy
	 * command-specific documentation WP-CLI pulls from the DocBlock.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function get( $args, $assoc_args ) {
		$object_id = $this->fetcher->get_check( $args[0] );

		$fields_array = get_object_vars( $object_id );
		unset( $fields_array['filled'] );

		if ( empty( $assoc_args['filled'] ) ) {
			$assoc_args['fields'] = array_keys( $fields_array );
		}

		$formatter = $this->get_formatter( $assoc_args );
		$formatter->display_item( $fields_array );
	}

	/**
	 * Creates an [x].
	 *
	 * Should be extended by sub-classes.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args Arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	abstract public function create( $args, $assoc_args );

	/**
	 * Updates an existing [x].
	 *
	 * Should be extended by sub-classes.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments
	 * @param array $assoc_args Associated arguments.
	 */
	abstract public function update( $args, $assoc_args );

	/**
	 * Deletes an [x].
	 *
	 * Should be extended by sub-classes.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments
	 * @param array $assoc_args Associated arguments.
	 */
	abstract public function delete( $args, $assoc_args );

	/**
	 * Displays a list of [x].
	 *
	 * Should be extended by sub-classes.
	 *
	 * @subcommand list
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments (unused).
	 * @param array $assoc_args Associated arguments.
	 */
	abstract public function list_( $_, $assoc_args );

	/**
	 * Processes extra fields that can't be derived from a simple db lookup.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param array $fields Array of fields to process for.
	 * @param array $items  Array of items to process `$fields` for.
	 * @return array Processed array of items.
	 */
	protected function process_extra_fields( $fields, $items ) {
		$processed = array();

		foreach ( $items as $item ) {
			foreach ( $fields as $field ) {
				// Handle field special cases. Methods should follow the pattern: {$field}_field( &$item ).
				$method = "{$field}_field";
				if ( method_exists( $this, $method ) ) {
					$this->$method( $item );
				}
			}

			$processed[] = $item;
		}

		return $processed;
	}

	/**
	 * Retrieves fields from the associated arguments if present, falls back to $obj_fields.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param array $assoc_args Associated arguments (flags).
	 * @return array Fields.
	 */
	protected function get_fields( $assoc_args ) {
		$fields = array();

		foreach ( array( 'fields', 'field' ) as $key ) {
			if ( isset( $assoc_args[ $key ] ) ) {
				$fields[ $key ] = $assoc_args[ $key ];
			}
		}

		if ( empty( $fields ) ) {
			$fields = $this->obj_fields;
		}
		return $fields;
	}

}