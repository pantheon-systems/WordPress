<?php

/**
 * Menu item metadata
 *
 */
final class OE_Menu_Icons_Meta {

	const KEY = 'oe-icons';

	/**
	 * Default meta value
	 *
	 */
	protected static $defaults = array(
		'type' => '',
		'icon' => '',
		'url'  => '',
	);

	/**
	 * Initialize metadata functionalities
	 *
	 */
	public static function init() {
		add_filter( 'is_protected_meta', array( __CLASS__, '_protect_meta_key' ), 10, 3 );
	}

	/**
	 * Protect meta key
	 *
	 */
	public static function _protect_meta_key( $protected, $meta_key, $meta_type ) {
		if ( self::KEY === $meta_key ) {
			$protected = true;
		}

		return $protected;
	}

	/**
	 * Get menu item meta value
	 *
	 */
	public static function get( $id, $defaults = array() ) {
		$defaults = wp_parse_args( $defaults, self::$defaults );
		$value    = get_post_meta( $id, self::KEY, true );
		$value    = wp_parse_args( (array) $value, $defaults );

		// Backward-compatibility.
		if ( empty( $value['icon'] ) &&
			! empty( $value['type'] ) &&
			! empty( $value[ "{$value['type']}-icon" ] )
		) {
			$value['icon'] = $value[ "{$value['type']}-icon" ];
		}

		if ( ! empty( $value['width'] ) ) {
			$value['svg_width'] = $value['width'];
		}
		unset( $value['width'] );

		if ( isset( $value['position'] ) &&
			! in_array( $value['position'], array( 'before', 'after', 'below' ), true )
		) {
			$value['position'] = $defaults['position'];
		}

		if ( isset( $value['size'] ) && ! isset( $value['font_size'] ) ) {
			$value['font_size'] = $value['size'];
			unset( $value['size'] );
		}

		// The values below will NOT be saved
		if ( ! empty( $value['icon'] ) &&
			in_array( $value['type'], array( 'image', 'svg' ), true )
		) {
			$value['url'] = wp_get_attachment_image_url( $value['icon'], 'thumbnail', false );
		}

		return $value;
	}

	/**
	 * Update menu item metadata
	 *
	 */
	public static function update( $id, $value ) {
		/**
		 * Add filter to allow to filter the values
		 */
		$value = apply_filters( 'oe_menu_icons_item_meta_values', $value, $id );

		// Don't bother saving if `type` or `icon` is not set.
		if ( empty( $value['type'] ) || empty( $value['icon'] ) ) {
			$value = false;
		}

		// Update
		if ( ! empty( $value ) ) {
			update_post_meta( $id, self::KEY, $value );
		} else {
			delete_post_meta( $id, self::KEY );
		}
	}
}