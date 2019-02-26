<?php

/**
 * Class WPML_TM_Field_Type_Encoding
 */
class WPML_TM_Field_Type_Encoding {

	const CUSTOM_FIELD_KEY_SEPARATOR = ':::';

	/**
	 * @param string $custom_field_name
	 * @param array $attributes
	 *
	 * @return array
	 */
	public static function encode( $custom_field_name, $attributes ) {
		$encoded_index = $custom_field_name;
		foreach ( $attributes as $index ) {
			$encoded_index .= '-' . self::encode_hyphen( $index );
		}

		return array( 'field-' . $encoded_index, $encoded_index );
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public static function encode_hyphen( $string ) {
		return str_replace( '-', self::CUSTOM_FIELD_KEY_SEPARATOR, $string );
	}

	/**
	 * Get the custom field name and the attributes from the custom field job.
	 *
	 * @param string $custom_field_job_type - e.g: field-my_custom_field-0-attribute.
	 *
	 * @return array An array with field name and attributes
	 */
	public static function decode( $custom_field_job_type ) {
		$custom_field_name = '';
		$attributes        = array();

		$parts = explode( '-', $custom_field_job_type );
		$count = count( $parts );
		if ( $count > 2 && 'field' === $parts[0] ) {
			$custom_field_name                = $parts[1];
			$complete_custom_field_name_found = false;
			for ( $i = 2; $i < $count; $i ++ ) {
				if ( ! $complete_custom_field_name_found && is_numeric( $parts[ $i ] ) ) {
					$complete_custom_field_name_found = true;
					continue;
				}
				if ( ! $complete_custom_field_name_found ) {
					$custom_field_name .= '-' . $parts[ $i ];
				} else {
					$attributes[] = self::decode_hyphen( $parts[ $i ] );
				}
			}
		}

		return array( $custom_field_name, $attributes );
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public static function decode_hyphen( $string ) {
		return str_replace( self::CUSTOM_FIELD_KEY_SEPARATOR, '-', $string );
	}

}
