<?php

/**
 * Class WPML_Multilingual_Options_Array_Helper
 */
class WPML_Multilingual_Options_Array_Helper {

	/**
	 * @param array $value1
	 * @param array $value2
	 *
	 * @return array
	 */
	public function array_diff_recursive( array $value1, array $value2 ) {
		$diff = array();
		foreach ( $value1 as $k => $v ) {
			if ( $this->in_array( $value2, $v, $k ) ) {
				$temp_diff = $this->array_diff_recursive( $v, $value2[ $k ] );
				if ( $temp_diff ) {
					$diff[ $k ] = $temp_diff;
				}
			} elseif ( ! array_key_exists( $k, $value2 ) || $v !== $value2[ $k ] ) {
				$diff[ $k ] = $v;
			}
		}

		return $diff;
	}

	/**
	 * @param array $target
	 * @param array $source
	 *
	 * @return array
	 */
	public function recursive_merge( array $target, array $source ) {
		foreach ( $source as $k => $v ) {
			if ( $this->in_array( $target, $v, $k ) ) {
				$target[ $k ] = $this->recursive_merge( $target[ $k ], $v );
			} else {
				$target[ $k ] = $v;
			}
		}

		return $target;
	}

	/**
	 * @param array  $haystack
	 * @param mixed  $needle
	 * @param string $needle_key
	 *
	 * @return bool
	 */
	private function in_array( array $haystack, $needle, $needle_key ) {
		return is_array( $needle ) && array_key_exists( $needle_key, $haystack ) && is_array( $haystack[ $needle_key ] );
	}
}
