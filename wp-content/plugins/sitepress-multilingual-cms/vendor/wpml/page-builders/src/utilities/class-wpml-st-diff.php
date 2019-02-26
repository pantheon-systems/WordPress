<?php

/**
 * Based on https://github.com/paulgb/simplediff/blob/master/php/simplediff.php
 */

class WPML_ST_Diff {

	/**
	 * @param string[] $old_words
	 * @param string[] $new_words
	 *
	 * @return array
	 */
	public static function diff( $old_words, $new_words ) {
		$matrix     = array();
		$max_length = 0;
		foreach ( $old_words as $old_index => $old_value ) {
			$new_keys = array_keys( $new_words, $old_value );
			foreach ( $new_keys as $new_index ) {
				$matrix[ $old_index ][ $new_index ] = isset( $matrix[ $old_index - 1 ][ $new_index - 1 ] ) ?
					$matrix[ $old_index - 1 ][ $new_index - 1 ] + 1 : 1;
				if ( $matrix[ $old_index ][ $new_index ] > $max_length ) {
					$max_length = $matrix[ $old_index ][ $new_index ];
					$old_max    = $old_index + 1 - $max_length;
					$new_max    = $new_index + 1 - $max_length;
				}
			}
		}
		if ( $max_length == 0 ) {
			return array( array( 'deleted' => $old_words, 'inserted' => $new_words ) );
		}

		return array_merge(
			self::diff( array_slice( $old_words, 0, $old_max ), array_slice( $new_words, 0, $new_max ) ),
			array_slice( $new_words, $new_max, $max_length ),
			self::diff( array_slice( $old_words, $old_max + $max_length ), array_slice( $new_words, $new_max + $max_length ) )
		);
	}

	/**
	 * @param string $old_text
	 * @param string $new_text
	 *
	 * @return float|int
	 */
	public static function get_sameness_percent( $old_text, $new_text ) {
		if ( $old_text ) {
			$diff = self::diff( preg_split( '/[\s]+/', $old_text ), preg_split( '/[\s]+/', $new_text ) );

			$common_length = 0;
			foreach ( $diff as $diff_data ) {
				if ( ! is_array( $diff_data ) ) {
					$common_length += strlen( $diff_data );
				}
			}

			return ( $common_length * 100 ) / strlen( $old_text );
		} else {
			return 0;
		}

	}

}