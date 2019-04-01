<?php

class WPML_URL_Converter_Parameter_Strategy extends WPML_URL_Converter_Abstract_Strategy {

	public function get_lang_from_url_string( $url ) {
		return $this->lang_param->lang_by_param( $url, false );
	}

	public function convert_url_string( $source_url, $lang_code ) {
		if ( ! $lang_code || $lang_code === $this->default_language ) {
			$lang_code = '';
		}

		$url_parts  = wpml_parse_url( $source_url );
		$query_args = $this->get_query_args( $url_parts );

		if ( $lang_code ) {
			$query_args['lang'] = $lang_code;
		} else {
			unset( $query_args['lang'] );
		}

		$url_parts['query'] = http_build_query( $query_args );
		$converted_url      = http_build_url( $url_parts );

		return $this->slash_helper->maybe_user_trailingslashit( $converted_url );
	}

	public function convert_admin_url_string( $source_url, $lang ) {
		return $this->convert_url_string( $source_url, $lang );
	}

	/**
	 * @param array $url_parts
	 *
	 * @return array
	 */
	private function get_query_args( array $url_parts ) {
		$query = isset( $url_parts['query'] ) ? $url_parts['query'] : '';
		$query = str_replace( '?', '&', $query );
		parse_str( $query, $query_args );
		return $query_args;
	}

	/**
	 * @param string $url
	 * @param string $language
	 *
	 * @return string
	 */
	public function get_home_url_relative( $url, $language ) {
		if ( $language === $this->default_language ) {
			$language = '';
		}

		if ( $language ) {
			return add_query_arg( 'lang', $language, $url );
		} else {
			return $url;
		}
	}

	/**
	 * @param string $source_url
	 *
	 * @return string
	 */
	public function fix_trailingslashit( $source_url ) {
		$query = wpml_parse_url( $source_url, PHP_URL_QUERY );
		if ( ! empty( $query ) ) {
			$source_url = str_replace( '?' . $query, '', $source_url );
		}

		$source_url = $this->slash_helper->maybe_user_trailingslashit( $source_url );

		if ( ! empty( $query ) ) {
			$source_url .= '?' . untrailingslashit( $query );
		}

		return $source_url;
	}
}