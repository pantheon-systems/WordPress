<?php

class WPML_Slash_Management {

	public function match_trailing_slash_to_reference( $url, $reference_url ) {
		if ( trailingslashit( $reference_url ) === $reference_url && ! $this->has_lang_param( $url ) ) {
			return trailingslashit( $url );
		} else {
			return untrailingslashit( $url );
		}
	}

	/**
	 * @param string $url
	 *
	 * @return bool
	 */
	private function has_lang_param( $url ) {
		return strpos( $url, '?lang=' ) !== false || strpos( $url, '&lang=' ) !== false;
	}

	/**
	 * @param string $url
	 * @param string $method Deprecated.
	 *
	 * @return mixed|string
	 */
	public function maybe_user_trailingslashit( $url, $method = '' ) {
		$url_parts = wpml_parse_url( $url );

		if ( ! $url_parts ) {
			return $url;
		}

		$url_parts = $this->parse_missing_host_from_path( $url_parts );

		if ( $this->is_root_url_with_trailingslash( $url_parts )
			 || $this->is_root_url_without_trailingslash_and_without_query_args( $url_parts )
		) {
			return $url;
		}

		$path = isset( $url_parts['path'] ) ? $url_parts['path'] : '';

		if ( ! $path && isset( $url_parts['query'] ) ) {
			$url_parts['path'] = '/';
		} elseif ( $this->is_file_path( $path ) ) {
			$url_parts['path'] = untrailingslashit( $path );
		} elseif ( $method ) {
			$url_parts['path'] = 'untrailingslashit' === $method ? untrailingslashit( $path ) : trailingslashit( $path );
		} else {
			$url_parts['path'] = $this->user_trailingslashit( $path );
		}

		return http_build_url( $url_parts );
	}

	/**
	 * Follows the logic of WordPress core user_trailingslashit().
	 * Can be called on plugins_loaded event, when $wp_rewrite is not set yet.
	 *
	 * @param $path
	 *
	 * @return string
	 */
	private function user_trailingslashit( $path ) {
		global $wp_rewrite;

		if ( $wp_rewrite ) {
			return user_trailingslashit( $path );
		}

		$permalink_structure  = get_option( 'permalink_structure' );
		$use_trailing_slashes = ( '/' === substr( $permalink_structure, - 1, 1 ) );

		if ( $use_trailing_slashes ) {
			$path = trailingslashit( $path );
		} else {
			$path = untrailingslashit( $path );
		}

		return apply_filters( 'user_trailingslashit', $path, '' );
	}

	/**
	 * @param array $url_parts
	 *
	 * @return bool
	 */
	private function is_root_url_without_trailingslash_and_without_query_args( array $url_parts ) {
		return ! isset( $url_parts['path'] ) && ! isset( $url_parts['query'] );
	}

	/**
	 * @param array $url_parts
	 *
	 * @return bool
	 */
	private function is_root_url_with_trailingslash( array $url_parts ) {
		return isset( $url_parts['path'] ) && '/' === $url_parts['path'];
	}

	/**
	 * @see Test_WPML_Lang_Domains_Converter::check_domains_and_subdir
	 *
	 * @param array $url_parts
	 *
	 * @return array
	 */
	public function parse_missing_host_from_path( array $url_parts ) {
		if ( ! isset( $url_parts['host'] ) && isset( $url_parts['path'] ) ) {
			$domain_and_subdir = explode( '/', $url_parts['path'] );
			$domain = $domain_and_subdir[0];
			$url_parts['host'] = $domain;
			array_shift( $domain_and_subdir );

			if ( $domain_and_subdir ) {
				$url_parts['path'] = preg_replace( '/^(' . $url_parts['host'] . ')/', '', $url_parts['path'] );
			} else {
				unset( $url_parts['path'] );
			}
		}

		return $url_parts;
	}

	/**
	 * @param string $path
	 *
	 * @return bool
	 */
	private function is_file_path( $path ) {
		$pathinfo = pathinfo( $path );
		return isset( $pathinfo['extension'] ) && $pathinfo['extension'];
	}
}
