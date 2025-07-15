<?php
/**
 * Prevents calls to the Credits API.
 * Prevents calls to Gravatar.com.
 *
 * @package FAIR
 */

namespace FAIR\Credits;

use WP_Error;

const WP_CORE_LATEST_MAJOR_RELEASE = '6.8';
const JSON_BASE = __DIR__ . '/json/';

/**
 * Bootstrap.
 */
function bootstrap() {
	add_filter( 'pre_http_request', __NAMESPACE__ . '\\replace_credits_api', 10, 3 );
}

/**
 * Replace versions 1.0 and 1.1 of the Credits API with local file copies.
 *
 * @param bool|array $value Filtered value, or false to proceed.
 * @param array $parsed_args The request's arguments.
 * @param string $url The request's URL.
 * @return bool|array|WP_Error Replaced value, false to proceed, or WP_Error on failure.
 */
function replace_credits_api( $response, array $parsed_args, string $url ) {
	if ( strpos( $url, 'api.wordpress.org/core/credits/' ) === false ) {
		return $response;
	}

	$major_version = get_major_version( $url );
	if ( is_wp_error( $major_version ) ) {
		return $major_version;
	}

	$credits = get_credits( $major_version );
	if ( is_wp_error( $credits ) ) {
		return $credits;
	}

	// Credits API 1.0 returns a serialized value rather than JSON.
	if ( strpos( $url, 'credits/1.0' ) !== false ) {
		$credits = serialize( json_decode( $credits, true ) );
	}

	return get_response( $credits );
}

/**
 * Get the major version from the URL.
 *
 * Falls back to the currently installed major version if the version is not found in the URL.
 *
 * @param string $url The request's URL.
 * @return string|WP_Error The major version string, or WP_Error on failure.
 */
function get_major_version( string $url ) {
	// wp_get_wp_version() was introduced in 6.7.
	$wp_version = function_exists( 'wp_get_wp_version' ) ? wp_get_wp_version() : $GLOBALS['wp_version'];
	$version_parts = explode( '.', $wp_version );
	$installed_major = $version_parts[0] . '.' . $version_parts[1];

	$query = parse_url( $url, PHP_URL_QUERY );
	if ( ! is_string( $query ) ) {
		return $installed_major;
	}

	parse_str( $query, $params );
	if ( ! isset( $params['version'] ) ) {
		return $installed_major;
	}

	// Only the X.X major version is used by the Credits API.
	if ( ! preg_match( '/^([0-9]+\.[0-9])/', $params['version'], $matches ) ) {
		return new WP_Error(
			'invalid_version',
			sprintf(
				/* translators: %s: The version string. */
				__( '%s is not a valid version string.', 'fair' ),
				$params['version']
			)
		);
	}

	return $matches[1];
}

/**
 * Get the credits for a WordPress major version.
 *
 * @param string $major_version The WordPress major version string.
 * @return string|WP_Error The credits in JSON format, or WP_Error on failure.
 */
function get_credits( string $major_version ) {
	$file = JSON_BASE . $major_version . '.json';
	if ( file_exists( $file ) ) {
		return trim( file_get_contents( $file ) );
	}

	// The Credits API always falls back to the latest major version.
	$fallback_file = JSON_BASE . WP_CORE_LATEST_MAJOR_RELEASE . '.json';
	if ( file_exists( $fallback_file ) ) {
		return trim( file_get_contents( $fallback_file ) );
	}

	return new WP_Error(
		'no_credits_found',
		__( 'No credits could be found.', 'fair' )
	);
}

/**
 * Get a fake response.
 *
 * @param string $body The response's body.
 * @return array The response.
 */
function get_response( string $body ) : array {
	return [
		'response' => [
			'code' => 200,
			'message' => 'OK',
		],
		'body' => $body,
		'headers' => [],
		'cookies' => [],
		'http_response_code' => 200,
	];
}
