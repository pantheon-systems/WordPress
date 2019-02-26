<?php

class WPML_Encoding_Validation {

	const MIN_CHAR_SIZE = 20;

	/**
	 * @param $string
	 *
	 * @return int
	 */
	public function is_base64( $string ) {
		return self::MIN_CHAR_SIZE < strlen( $string ) && preg_match_all( '#^([A-Za-z0-9+/]{4})*([A-Za-z0-9+/]{4}|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{2}==)$#', $string, $matches );
	}
}