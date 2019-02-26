<?php

class WPML_URL_Converter_Domain_Strategy extends WPML_URL_Converter_Abstract_Strategy {

	/** @var string[] $domains */
	private $domains = array();

	/**
	 * @param array       $domains
	 * @param string      $default_language
	 * @param array       $active_languages
	 */
	public function __construct(
		$domains,
		$default_language,
		$active_languages
	) {
		parent::__construct( $default_language, $active_languages );

		$domains       = array_map( 'untrailingslashit', $domains );
		$this->domains = array_map( array( $this, 'strip_protocol' ), $domains );

		if ( isset( $this->domains[ $default_language ] ) ) {
			unset( $this->domains[ $default_language ] );
		}
	}

	public function get_lang_from_url_string( $url ) {
		$url = $this->strip_protocol( $url );

		if ( strpos( $url, '?' ) ) {
			$parts = explode( '?', $url );
			$url   = $parts[0];
		}

		foreach ( $this->domains as $code => $domain ) {
			if ( strpos( trailingslashit( $url ), trailingslashit( $domain ) ) === 0 ) {
				return $code;
			}
		}

		return null;
	}

	public function convert_url_string( $source_url, $lang ) {
		$original_source_url = untrailingslashit( $source_url );
		if ( is_admin() && $this->get_url_helper()->is_url_admin( $original_source_url ) ) {
			return $original_source_url;
		}

		return $this->convert_url( $source_url, $lang );
	}

	public function convert_admin_url_string( $source_url, $lang ) {
		return $this->convert_url( $source_url, $lang );
	}

	private function convert_url( $source_url, $lang ) {

		$base_url = isset( $this->domains[ $lang ] ) ? $this->domains[ $lang ] : $this->get_url_helper()->get_abs_home();

		$base_url_parts = $this->parse_domain_and_subdir( $base_url );
		$url_parts      = $this->parse_domain_and_subdir( $source_url );

		if ( isset( $base_url_parts['host'] ) ) {
			$url_parts['host'] = $base_url_parts['host'];
		}

		$converted_url = http_build_url( $url_parts );

		return $this->slash_helper->maybe_user_trailingslashit( $converted_url );
	}

	/**
	 * @param string $base_url
	 *
	 * @return array
	 */
	private function parse_domain_and_subdir( $base_url ) {
		$url_parts = wpml_parse_url( $base_url );
		return $this->slash_helper->parse_missing_host_from_path( $url_parts );
	}

	/**
	 * @param string $url
	 * @param string $language
	 *
	 * @return string
	 */
	public function get_home_url_relative( $url, $language ) {
		return $url;
	}

	/**
	 * @param string $url
	 *
	 * @return array|string
	 */
	private function strip_protocol( $url ) {
		$url_parts = wpml_parse_url( $url );
		$url_parts = $this->slash_helper->parse_missing_host_from_path( $url_parts );
		unset( $url_parts['scheme'] );
		return http_build_url( $url_parts );
	}
}