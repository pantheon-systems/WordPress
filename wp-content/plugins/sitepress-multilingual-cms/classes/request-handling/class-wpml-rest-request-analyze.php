<?php

class WPML_REST_Request_Analyze {

	/** @var WPML_URL_Converter $url_converter */
	private $url_converter;

	/** @var array $active_language_codes */
	private $active_language_codes;

	public function __construct( WPML_URL_Converter $url_converter, array $active_language_codes ) {
		$this->url_converter         = $url_converter;
		$this->active_language_codes = $active_language_codes;
	}

	/** @return bool */
	public function is_rest_request() {
		if ( array_key_exists( 'rest_route', $_REQUEST ) ) {
			return true;
		}

		$rest_url_prefix = 'wp-json';

		if ( function_exists( 'rest_get_url_prefix' ) ) {
			$rest_url_prefix = rest_get_url_prefix();
		}

		$uri_part = $this->get_uri_part( $this->has_valid_language_prefix() ? 1 : 0 );

		return $uri_part === $rest_url_prefix;
	}

	/** @return bool */
	private function has_valid_language_prefix() {
		if ( $this->url_converter->get_strategy() instanceof WPML_URL_Converter_Subdir_Strategy ) {
			$maybe_lang = $this->get_uri_part();
			return in_array( $maybe_lang, $this->active_language_codes, true );
		}

		return false;
	}

	/**
	 * @param int $index
	 *
	 * @return string
	 */
	private function get_uri_part( $index = 0 ) {
		$parts = explode( '/', ltrim( $_SERVER['REQUEST_URI'], '/' ) );
		return isset( $parts[ $index ] ) ? $parts[ $index ] : '';
	}

	/**
	 * This is to keep backward compatibility for sites using REST requests
	 * with a language as a directory prefix. As a consequence, those REST requests
	 * are filtering resources by language.
	 *
	 * @return bool
	 */
	public function should_load_on_frontend() {
		return $this->has_valid_language_prefix();
	}
}
