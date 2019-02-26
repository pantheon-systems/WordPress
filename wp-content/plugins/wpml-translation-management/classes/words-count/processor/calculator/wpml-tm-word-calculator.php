<?php

/**
 * @see https://onthegosystems.myjetbrains.com/youtrack/issue/wpmltm-2327
 */
class WPML_TM_Word_Calculator {

	const ASIAN_LANGUAGE_CHAR_SIZE = 6;

	/** @var WPML_PHP_Functions $php_functions */
	private $php_functions;

	public function __construct( WPML_PHP_Functions $php_functions ) {
		$this->php_functions = $php_functions;
	}

	/**
	 * @param string $string
	 * @param string $language_code
	 *
	 * @return int
	 */
	public function count_words( $string, $language_code ) {
		$sanitized_source = $this->sanitize_string( $string );
		$words            = 0;

		if ( in_array( $language_code, self::get_asian_languages() ) ) {
			$words += strlen( strip_tags( $sanitized_source ) ) / self::ASIAN_LANGUAGE_CHAR_SIZE;
		} else {
			$words += count( preg_split( '/[\s,:;!\.\?\(\)\[\]\-_\'"\\/]+/', $sanitized_source, 0, PREG_SPLIT_NO_EMPTY ) );
		}

		return (int) $words;
	}

	/** @return bool */
	private function exclude_shortcodes_in_words_count() {
		if ( $this->php_functions->defined( 'EXCLUDE_SHORTCODES_IN_WORDS_COUNT' ) ) {
			return (bool) $this->php_functions->constant( 'EXCLUDE_SHORTCODES_IN_WORDS_COUNT' );
		}

		return false;
	}

	/**
	 * @param string $source
	 *
	 * @return string
	 */
	private function sanitize_string( $source ) {
		$result = $source;
		$result = html_entity_decode( $result );
		$result = strip_tags( $result );
		$result = trim( $result );
		$result = $this->strip_urls( $result );

		if ( $this->exclude_shortcodes_in_words_count() ) {
			$result = strip_shortcodes( $result );
		} else {
			$result = $this->extract_content_in_shortcodes( $result );
		}

		return $result;
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	private function extract_content_in_shortcodes( $string ) {
		return preg_replace( '#(?:\[/?)[^/\]]+/?\]#s', '', $string );
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	private function strip_urls( $string ) {
		return preg_replace( '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $string );
	}

	public static function get_asian_languages() {
		return array( 'ja', 'ko', 'zh-hans', 'zh-hant', 'mn', 'ne', 'hi', 'pa', 'ta', 'th' );
	}
}
