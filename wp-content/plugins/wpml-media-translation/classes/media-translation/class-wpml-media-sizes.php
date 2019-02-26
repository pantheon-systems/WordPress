<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Media_Sizes {
	/**
	 * @param array $img
	 *
	 * @return null|string
	 */
	public function get_size_from_class( array $img ) {
		if ( array_key_exists( 'attributes', $img ) && array_key_exists( 'class', $img['attributes'] ) ) {

			$classes = explode( ' ', $img['attributes']['class'] );
			foreach ( $classes as $class ) {
				if ( strpos( $class, 'size-' ) === 0 ) {
					$class_parts = explode( '-', $class );
					if ( count( $class_parts ) >= 2 ) {
						unset( $class_parts[0] );

						return implode( '-', $class_parts );
					}
				}
			}

		}

		return null;
	}

	/**
	 * @param array $img
	 *
	 * @return null|string
	 */
	public function get_size_from_attributes( array $img ) {
		if (
			array_key_exists( 'attributes', $img )
			&& array_key_exists( 'width', $img['attributes'] )
			&& array_key_exists( 'height', $img['attributes'] )
		) {

			$width  = $img['attributes']['width'];
			$height = $img['attributes']['height'];

			$size_name = $this->get_image_size_name( $width, $height );

			if ( $size_name ) {
				return $size_name;
			}
		}

		return null;
	}

	/**
	 * @param array $img
	 *
	 * @return null|string
	 */
	public function get_attachment_size( array $img ) {
		$size = null;
		if ( array_key_exists( 'size', $img ) ) {
			$size = $img['size'];
		}
		if ( ! $size ) {
			$size = $this->get_size_from_class( $img );
		}
		if ( ! $size ) {
			$size = $this->get_size_from_attributes( $img );
		}
		if ( ! $size ) {
			$size = $this->get_size_from_url( $img );
		}

		return $size;
	}

	/**
	 * @param string $width
	 * @param string $height
	 *
	 * @return null|string
	 */
	private function get_image_size_name( $width, $height ) {
		global $_wp_additional_image_sizes;

		foreach ( get_intermediate_image_sizes() as $size ) {
			if ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
				if ( $width == $_wp_additional_image_sizes[ $size ]['width'] && $height == $_wp_additional_image_sizes[ $size ]['height'] ) {
					return $size;
				}
			} elseif ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				if ( $width == get_option( "{$size}_size_w" ) && $height == get_option( "{$size}_size_h" ) ) {
					return $size;
				}
			}
		}

		return null;
	}

	/**
	 * @param array $img
	 *
	 * @return null|string
	 */
	private function get_size_from_url( array $img ) {
		$size = null;

		if ( isset( $img['attributes']['src'], $img['attachment_id'] ) ) {
			$size = $this->get_image_size_from_url( $img['attributes']['src'], $img['attachment_id'] );
		}

		return $size;
	}

	/**
	 * @param $url
	 * @param $attachment_id
	 *
	 * @return null|string
	 */
	public function get_image_size_from_url( $url, $attachment_id ) {
		$size = null;

		$thumb_file_name      = basename( $url );
		$attachment_meta_data = wp_get_attachment_metadata( $attachment_id );
		foreach ( $attachment_meta_data['sizes'] as $key => $size_array ) {
			if ( $thumb_file_name === $size_array['file'] ) {
				$size = $key;
				break;
			}
		}

		return $size;
	}
}
