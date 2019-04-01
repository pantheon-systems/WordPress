<?php
if ( ! function_exists( 'object_to_array' ) ) {
	function object_to_array( $obj ) {
		return json_decode( json_encode( $obj ), true );
	}
}

if ( ! function_exists( 'wpml_get_admin_url' ) ) {
	/**
	 * A more helpful version of `admin_url`
	 * Is not called it `wpml_admin_url` because there is already a class with the same name
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	function wpml_get_admin_url( array $args = array() ) {
		if ( ! $args ) {
			return admin_url();
		}

		$default_args = array(
			'path'   => '',
			'scheme' => 'admin',
			'query'  => array(),
		);

		$args = array_merge( $default_args, $args );

		$admin_url = '';
		if ( $args['path'] || $args['scheme'] ) {
			$admin_url = admin_url( $args['path'], $args['scheme'] );
		}

		if ( $args['query'] ) {
			$admin_url_parsed = wp_parse_url( $admin_url );

			if ( is_string( $args['query'] ) ) {
				$admin_url_parsed['query'] = $args['query'];
			} elseif ( is_array( $args['query'] ) ) {
				$admin_url_parsed['query'] = http_build_query( $args['query'] );
			}

			$admin_url = http_build_url( $admin_url_parsed );
		}

		return $admin_url;
	}
}
