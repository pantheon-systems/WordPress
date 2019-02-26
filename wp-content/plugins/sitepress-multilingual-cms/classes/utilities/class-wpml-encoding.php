<?php

class WPML_Encoding {

	/**
	 * @param string $string The string to decode.
	 * @param string $encodings A comma separated list of encodings in the order that the data was encoded
	 *
	 * @return mixed
	 */
	public static function decode( $string, $encodings ) {
		$decoded_data = $string;

		// NOTE: We decode in the reverse order of the encodings given
		foreach( array_reverse( explode( ',', $encodings ) ) as $encoding ) {
			switch( $encoding ) {
				case 'json':
					$decoded_data = json_decode( $decoded_data, true );
					break;

				case 'base64':
					$decoded_data = base64_decode( $decoded_data );
					break;

				case 'urlencode':
					$decoded_data = urldecode( $decoded_data );
					break;
			}
		}

		return apply_filters( 'wpml_decode_string', $decoded_data, $string, $encodings );
	}

	/**
	 * @param mixed $data The data to encode.
	 * @param string $encodings A comma separated list of encodings in the order that the data was encoded
	 *
	 * @return string
	 */
	public static function encode( $data, $encodings ) {
		$encoded_data = $data;

		foreach( explode( ',', $encodings ) as $encoding ) {
			switch( $encoding ) {
				case 'json':
					$encoded_data = wp_json_encode( $encoded_data );
					break;

				case 'base64':
					$encoded_data = base64_encode( $encoded_data );
					break;

				case 'urlencode':
					$encoded_data = urlencode( $encoded_data );
					break;
			}
		}

		return apply_filters( 'wpml_encode_string', $encoded_data, $data, $encodings );
	}
}

