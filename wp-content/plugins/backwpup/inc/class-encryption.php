<?php
/**
 *
 */
class BackWPup_Encryption {

	/**
	 *
	 * Encrypt a string (Passwords)
	 *
	 * @param string $string value to encrypt
	 *
	 * @return string encrypted string
	 */
	public static function encrypt( $string ) {

		if ( empty( $string ) ) {
			return $string;
		}

		//only encrypt if needed
		if ( strpos( $string, '$BackWPup$ENC1$' ) !== FALSE || strpos( $string, '$BackWPup$RIJNDAEL$' ) !== FALSE ) {
			if ( strpos( $string, '$BackWPup$ENC1$O$' ) !== FALSE && strpos( $string, '$BackWPup$RIJNDAEL$O$' ) !== FALSE && defined( 'BACKWPUP_ENC_KEY' ) && BACKWPUP_ENC_KEY )  {
				$string = self::decrypt( $string );
			} else {
				return $string;
			}
		}

		if ( defined( 'BACKWPUP_ENC_KEY' ) && BACKWPUP_ENC_KEY  ) {
			$key = BACKWPUP_ENC_KEY;
			$key_type = 'O$';
		} else {
			$key = DB_NAME . DB_USER . DB_PASSWORD;
			$key_type = '';
		}

		$key = md5( $key );

		if ( ! function_exists( 'mcrypt_encrypt' ) ) {
			$result = '';
			for ( $i = 0; $i < strlen( $string ); $i ++ ) {
				$char    = substr( $string, $i, 1 );
				$keychar = substr( $key, ( $i % strlen( $key ) ) - 1, 1 );
				$char    = chr( ord( $char ) + ord( $keychar ) );
				$result .= $char;
			}

			return '$BackWPup$ENC1$' . $key_type . base64_encode( $result );
		}

		return '$BackWPup$RIJNDAEL$' . $key_type . base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $key ), trim( $string ), MCRYPT_MODE_CBC, md5( md5( $key ) ) ) );
	}

	/**
	 *
	 * Decrypt a string (Passwords)
	 *
	 * @param string $string value to decrypt
	 *
	 * @return string decrypted string
	 */
	public static function decrypt( $string ) {

		if ( empty( $string ) ) {
			return $string;
		}


		if ( strpos( $string, '$BackWPup$ENC1$O$' ) !== FALSE || strpos( $string, '$BackWPup$RIJNDAEL$O$' ) !== FALSE ) {
			$key_type = 'O$';
			$key = BACKWPUP_ENC_KEY;
		} else {
			$key_type = '';
			$key = DB_NAME . DB_USER . DB_PASSWORD;
		}

		$key = md5( $key );

		if ( strpos( $string, '$BackWPup$ENC1$' . $key_type ) !== FALSE ) {
			$string = str_replace( '$BackWPup$ENC1$' . $key_type, '', $string );
			$result = '';
			$string = base64_decode( $string );
			for ( $i = 0; $i < strlen( $string ); $i ++ ) {
				$char    = substr( $string, $i, 1 );
				$keychar = substr( $key, ( $i % strlen( $key ) ) - 1, 1 );
				$char    = chr( ord( $char ) - ord( $keychar ) );
				$result .= $char;
			}

			return $result;
		}

		if ( function_exists( 'mcrypt_encrypt' ) && strpos( $string, '$BackWPup$RIJNDAEL$' . $key_type ) !== FALSE ) {
			$string = str_replace( '$BackWPup$RIJNDAEL$' . $key_type, '', $string );

			return trim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $key ), base64_decode( $string ), MCRYPT_MODE_CBC, md5( md5( $key ) ) ), "\0" );
		}

		return $string;
	}
}
