<?php
namespace AffWP\Utils\Referral_Types;

use AffWP\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements a referral type registry class.
 *
 * @since 2.2
 *
 * @see \AffWP\Utils\Registry
 */
class Registry extends Utils\Registry {

	/**
	 * Initializes the type registry.
	 *
	 * @access public
	 * @since  2.2
	 */
	public function init() {

		$this->register_core_types();

		/**
		 * Fires during instantiation of the referral type registry.
		 *
		 * @since 2.2
		 *
		 * @param \AffWP\Utils\Registry $this Registry instance.
		 */
		do_action( 'affwp_referral_type_init', $this );
	}

	/**
	 * Registers core referral types.
	 *
	 * @access protected
	 * @since  2.2
	 */
	protected function register_core_types() {

		// Sale type.
		$this->register_type( 'sale', array(
			'label' => __( 'Sale', 'affiliate-wp' ),
		) );

		// Opt-in type
		$this->register_type( 'opt-in', array(
			'label' => __( 'Opt-In', 'affiliate-wp' ),
		) );

		// Lead type
		$this->register_type( 'lead', array(
			'label' => __( 'Lead', 'affiliate-wp' ),
		) );

	}

	/**
	 * Registers a new referral type.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @param string $type_id Unique referral type ID.
	 * @param array  $args {
	 *     Arguments for registering a new referral type.
	 *
	 *     @type string $label The label for the referral type.
	 * }
	 * @return \WP_Error|true True on successful registration, otherwise a WP_Error object.
	 */
	public function register_type( $type_id, $args ) {
		$args = wp_parse_args( $args,  array_fill_keys( array( 'label' ), '' ) );

		if ( empty( $args['label'] ) ) {
			return new \WP_Error( 'invalid_label', __( 'A referral type label must be specified.', 'affiliate-wp' ) );
		}

		return $this->add_item( $type_id, $args );
	}

	/**
	 * Removes a referral type from the registry by ID.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @param string $type_id Referral type ID.
	 */
	public function remove_type( $type_id ) {
		$this->remove_item( $type_id );
	}

	/**
	 * Retrieves a type and its associated attributes.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @param string $type_id Type ID.
	 * @return array|false Array of attributes for the type if registered, otherwise false.
	 */
	public function get_type( $type_id ) {
		return $this->get( $type_id );
	}

	/**
	 * Retrieves registered referral types.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @return array The list of registered referral types.
	 */
	public function get_types() {
		return $this->get_items();
	}

}