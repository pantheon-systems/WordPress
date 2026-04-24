<?php
/**
 * ElasticPress Client-Side Search Configuration
 *
 * Routes browser-originated search requests (Autosuggest and Instant Results)
 * directly to the public ElasticPress.io endpoint, bypassing the internal
 * mtlsproxy which is only accessible server-side.
 *
 * Server-side operations (indexing, admin queries, WP_Query integration)
 * continue to route through the mtlsproxy for authenticated access.
 *
 * @package pantheon
 */

namespace Pantheon\ElasticPress;

if ( ! defined( 'EP_DIRECT_HOST' ) ) {
	return;
}

add_filter( 'ep_autosuggest_options', __NAMESPACE__ . '\\filter_autosuggest_options' );
add_filter( 'ep_instant_results_search_endpoint', __NAMESPACE__ . '\\filter_instant_results_endpoint' );

/**
 * Override the Autosuggest endpoint URL to use the direct ElasticPress.io host.
 *
 * @param array $options The autosuggest options passed to the browser JS.
 * @return array Modified options with the direct endpoint URL.
 */
function filter_autosuggest_options( $options ) {
	$index = get_post_index_name();
	if ( ! $index ) {
		return $options;
	}

	$options['endpointUrl'] = EP_DIRECT_HOST . '/' . $index . '/autosuggest';
	return $options;
}

/**
 * Filter the Instant Results search endpoint to use the direct ElasticPress.io URL.
 *
 * @param string $endpoint The default endpoint path.
 * @return string The full direct ElasticPress.io endpoint URL.
 */
function filter_instant_results_endpoint( $endpoint ) {
	$index = get_post_index_name();
	if ( ! $index ) {
		return $endpoint;
	}

	return EP_DIRECT_HOST . '/api/v1/search/posts/' . $index;
}

/**
 * Get the ElasticPress post index name.
 *
 * @return string|false The index name, or false if unavailable.
 */
function get_post_index_name() {
	$post_indexable = \ElasticPress\Indexables::factory()->get( 'post' );
	if ( ! $post_indexable ) {
		return false;
	}

	return $post_indexable->get_index_name();
}
