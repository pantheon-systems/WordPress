<?php
/**
 * Pantheon mu-plugin helper functions
 *
 * @package pantheon
 */

namespace Pantheon;

/**
 * Helper function that returns the current WordPress version.
 *
 * @return string
 */
function _pantheon_get_current_wordpress_version(): string {
	include ABSPATH . WPINC . '/version.php';
	return $wp_version; // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
}

/**
 * Helper function to get the request headers.
 *
 * @param array $headers Optional. An array of headers to process. Defaults to $_SERVER.
 * @return array Processed headers in standard HTTP header format.
 */
function _pantheon_get_request_headers( array $headers = [] ): array {
	$headers = ! empty( $headers ) ? $headers : ( ! empty( $_SERVER ) ? $_SERVER : [] );

	if ( empty( $headers ) ) {
		return [];
	}

	foreach ( $headers as $key => $value ) {
		if ( substr( $key, 0, 5 ) !== 'HTTP_' ) {
			continue;
		}

		/**
		 * Convert HTTP headers to standard HTTP header format.
		 *
		 * We use str_replace twice so that we can use ucwords to capitalize
		 * the first letter of each word, e.g. HTTP_USER_AGENT to User-Agent.
		 */
		$header = str_replace( ' ', '-', ucwords( str_replace( '_', ' ', strtolower( substr( $key, 5 ) ) ) ) );
		$headers[ $header ] = $value;
	}

	return $headers;
}

/**
 * Helper function to get a specific header value.
 *
 * @param string $key The header key to retrieve.
 * @return string The value of the specified header, or an empty string if not found.
 */
function _pantheon_get_header( string $key ): string {
	$headers = _pantheon_get_request_headers();
	return ! empty( $headers[ $key ] ) ? esc_textarea( $headers[ $key ] ) : '';
}
