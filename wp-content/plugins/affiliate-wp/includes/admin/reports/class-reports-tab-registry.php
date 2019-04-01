<?php
namespace AffWP\Admin\Reports;

use AffWP\Utils;

/**
 * Implements a registry for Reports tabs tiles.
 *
 * @since 2.1
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
	 * @since  2.1
	 */
	public function init() {}

	/**
	 * Adds a tile to a given reports tab (collection).
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param string $collection Collection (tab) ID.
	 * @param string $tile_id    Tile ID.
	 * @param array  $attributes {
	 *     Tile attributes.
	 *
	 *     @type string   $label            Tile label. Default 'Meta Box'.
	 *     @type string   $context          Tile context. Maps to the corresponding meta box `$context` value.
	 *                                      Accepts 'primary', 'secondary', and 'tertiary'. Default 'primary'.
	 *     @type string   $type             Tile type (used for formatting purposes). Accepts 'number', 'amount',
	 *                                      'rate', or empty. Default 'number'.
	 *     @type mixed    $data             The data value to supply to the tile. Default empty.
	 *     @type mixed    $comparison_data  Comparison data to pair with `$data`. Default empty.
	 *     @type callable $display_callback Display callback to use for the tile. Default is 'default_tile',
	 *                                      which leverages `$type`.
	 * }
	 * @return true Always true.
	 */
	public function add_tile( $collection, $tile_id, $attributes ) {
		return parent::add_item( "{$collection}:{$tile_id}", $attributes );
	}

	/**
	 * Removes a tile from the registry by ID.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param string $collection Collection (tab) ID.
	 * @param string $tile_id    Tile ID.
	 */
	public function remove_tile( $collection, $tile_id ) {
		parent::remove_item( "{$collection}:{$tile_id}" );
	}

	/**
	 * Retrieves registered tiles.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param int|string $collection Optional. Collection to retrieve tiles for. Default all collections.
	 * @return array The list of registered tiles.
	 */
	public function get_tiles( $collection = '' ) {
		$all_items = parent::get_items();

		$items = array();

		if ( ! empty( $collection ) ) {
			foreach ( $all_items as $item => $attributes ) {
				if ( ! preg_match( '/^' . $collection . ':/', $item ) ) {
					continue;
				}

				$parts = explode( ':', $item );

				if ( ! empty( $parts[1] ) ) {
					$item_id = $parts[1];

					$items[ $parts[1] ] = $attributes;
				}
			}
		} else {
			$items = $all_items;
		}

		return $items;
	}

}
