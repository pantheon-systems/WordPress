<?php
namespace AffWP\Utils;

/**
 * Defines the construct for building an item registry.
 *
 * @since 2.0.5
 * @abstract
 */
abstract class Registry {

	/**
	 * Array of registry items.
	 *
	 * @access private
	 * @since  2.0.5
	 * @var    array
	 */
	private $items = array();

	/**
	 * Initialize the registry.
	 *
	 * Each sub-class will need to do various initialization operations in this method.
	 *
	 * @access public
	 * @since  2.0.5
	 */
	abstract public function init();

	/**
	 * Adds an item to the registry.
	 *
	 * @access public
	 * @since  2.0.5
	 *
	 * @param int    $item_id   Item ID.
	 * @param array  $attributes {
	 *     Item attributes.
	 *
	 *     @type string $class Item handler class.
	 *     @type string $file  Item handler class file.
	 * }
	 * @return true Always true.
	 */
	public function add_item( $item_id, $attributes ) {
		foreach ( $attributes as $attribute => $value ) {
			$this->items[ $item_id ][ $attribute ] = $value;
		}

		return true;
	}

	/**
	 * Removes an item from the registry by ID.
	 *
	 * @access public
	 * @since  2.0.5
	 *
	 * @param string $item_id Item ID.
	 */
	public function remove_item( $item_id ) {
		unset( $this->items[ $item_id ] );
	}

	/**
	 * Retrieves an item and its associated attributes.
	 *
	 * @access public
	 * @since  2.0.5
	 *
	 * @param string $item_id Item ID.
	 * @return array|false Array of attributes for the item if registered, otherwise false.
	 */
	public function get( $item_id ) {
		if ( array_key_exists( $item_id, $this->items ) ) {
			return $this->items[ $item_id ];
		}
		return false;
	}

	/**
	 * Retrieves registered items.
	 *
	 * @access public
	 * @since  2.0.5
	 *
	 * @return array The list of registered items.
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Only intended for use by tests.
	 *
	 * @access public
	 * @since  2.0.5
	 */
	public function _reset_items() {
		if ( ! defined( 'WP_TESTS_DOMAIN' ) ) {
			_doing_it_wrong( 'This method is only intended for use in phpunit tests', '2.0.5' );
		} else {
			$this->items = array();
		}
	}
}
