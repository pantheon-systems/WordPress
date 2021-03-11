<?php
/**
 * Misc. helper functions
 *
 */

if ( ! function_exists( 'oe_get_array_value_deep' ) ) {
	/**
	 * Get value of a multidimensional array
	 *
	 */
	function oe_get_array_value_deep( array $array, array $keys ) {
		if ( empty( $array ) || empty( $keys ) ) {
			return $array;
		}

		foreach ( $keys as $idx => $key ) {
			unset( $keys[ $idx ] );

			if ( ! isset( $array[ $key ] ) ) {
				return null;
			}

			if ( ! empty( $keys ) ) {
				$array = $array[ $key ];
			}
		}

		if ( ! isset( $array[ $key ] ) ) {
			return null;
		}

		return $array[ $key ];
	}
}


if ( ! function_exists( 'oe_validate' ) ) {
	/**
	 * Validate settings values
	 *
	 */
	function oe_validate( $values, $sanitize_cb = 'wp_kses_data' ) {
		foreach ( $values as $key => $value ) {
			if ( is_array( $value ) ) {
				$values[ $key ] = oe_validate( $value );
			} else {
				$values[ $key ] = call_user_func_array(
					$sanitize_cb,
					array( $value )
				);
			}
		}

		return $values;
	}
}

if ( ! function_exists( 'oe_get_image_sizes' ) ) {
	/**
	 * Get image sizes
	 *
	 */
	function oe_get_image_sizes() {
		$_sizes = array(
			'thumbnail' => __( 'Thumbnail', 'ocean-extra' ),
			'medium'    => __( 'Medium', 'ocean-extra' ),
			'large'     => __( 'Large', 'ocean-extra' ),
			'full'      => __( 'Full Size', 'ocean-extra' ),
		);

		$_sizes = apply_filters( 'image_size_names_choose', $_sizes );

		$sizes = array();
		foreach ( $_sizes as $value => $label ) {
			$sizes[] = array(
				'value' => $value,
				'label' => $label,
			);
		}

		return $sizes;
	}
}

if ( ! function_exists( 'oe_get_script_suffix' ) ) {
	/**
	 * Get script & style suffix
	 *
	 */
	function oe_get_script_suffix() {
		return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	}
}