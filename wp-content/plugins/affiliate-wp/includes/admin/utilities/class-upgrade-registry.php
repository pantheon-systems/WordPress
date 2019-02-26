<?php
namespace AffWP\Utils\Upgrades;

use AffWP\Utils;

/**
 * Implements a registry for core upgrade routines.
 *
 * @since 2.0.5
 *
 * @see \AffWP\Utils\Registry
 */
class Registry extends Utils\Registry {

	/**
	 * Initialize the registry.
	 *
	 * Each sub-class will need to do various initialization operations in this method.
	 *
	 * @access public
	 * @since  2.0.5
	 */
	public function init() {}

	/**
	 * Adds an upgrade to the registry.
	 *
	 * @access public
	 * @since  2.0.5
	 *
	 * @param int    $upgrade_id   upgrade ID.
	 * @param array  $attributes {
	 *     Upgrade attributes.
	 *
	 *     @type string $class upgrade handler class.
	 *     @type string $file  upgrade handler class file.
	 * }
	 * @return true Always true.
	 */
	public function add_upgrade( $upgrade_id, $attributes ) {
		return parent::add_item( $upgrade_id, $attributes );
	}

	/**
	 * Removes an upgrade from the registry by ID.
	 *
	 * @access public
	 * @since  2.0.5
	 *
	 * @param string $upgrade_id upgrade ID.
	 */
	public function remove_upgrade( $upgrade_id ) {
		parent::remove_item( $upgrade_id );
	}

	/**
	 * Retrieves registered upgrades.
	 *
	 * @access public
	 * @since  2.0.5
	 *
	 * @return array The list of registered upgrades.
	 */
	public function get_upgrades() {
		return parent::get_items();
	}

}
