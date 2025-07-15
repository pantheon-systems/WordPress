<?php

namespace FAIR\Pings;

/**
 * Bootstrap.
 */
function bootstrap() {
    add_filter( 'pre_option_ping_sites', __NAMESPACE__ . '\\remove_pingomatic_from_ping_sites' );
    add_filter( 'query_vars', __NAMESPACE__ . '\\register_query_vars' );
    add_action( 'init', __NAMESPACE__ . '\\get_indexnow_key' );
    add_action( 'init', __NAMESPACE__ . '\\add_key_rewrite_rule' );
    add_action( 'template_redirect', __NAMESPACE__ . '\\handle_key_file_request' );
    add_action( 'transition_post_status', __NAMESPACE__ . '\\ping_indexnow', 10, 3 );
}

/**
 * Register query vars.
 *
 * @param array $vars Array of query vars.
 * @return array Modified array of query vars.
 */
function register_query_vars( $vars ) {
    $vars[] = 'fair_indexnow_key';
    return $vars;
}

/**
 * Remove pingomatic.com from the ping_sites option.
 *
 * @param string $value The ping_sites option value.
 */
function remove_pingomatic_from_ping_sites( $value ) {
    $value = str_replace( 'http://rpc.pingomatic.com/', '', $value );
	$value = str_replace( "\n\n", "\n", trim( $value, "\n" ) );
    return $value;
}

/**
 * Generate and store the IndexNow key if it doesn't exist.
 *
 * @return string Unique site key.
 */
function get_indexnow_key() : string {
    $key = get_option( 'fair_indexnow_key' );

    if ( ! $key ) {
        // Generate a random key that meets IndexNow requirements.
        // Must be 8-128 hexadecimal characters (a-f, 0-9).
        $key = strtolower( wp_generate_password( 40, false, false ) );

        update_option( 'fair_indexnow_key', $key );

        // Flush the rewrite rules.
        flush_rewrite_rules();
    }

    return $key;
}

/**
 * Add rewrite rule for the IndexNow key file.
 */
function add_key_rewrite_rule() {
    $key = get_indexnow_key();

    add_rewrite_rule(
        'fair-indexnow-' . $key . '$',
        'index.php?fair_indexnow_key=' . $key,
        'top'
    );
}

/**
 * Handle the IndexNow key file request.
 */
function handle_key_file_request() {
    if ( ! get_query_var( 'fair_indexnow_key' ) ) {
        return;
    }

    $key = get_indexnow_key();
    if ( ! $key || $key !== get_query_var( 'fair_indexnow_key' ) ) {
        wp_die( 'Invalid key: ' . get_query_var( 'fair_indexnow_key' ), 'IndexNow Key Error', [ 'response' => 403 ] );
        return;
    }

    // Set the content type to text/plain
    header( 'Content-Type: text/plain' );
    header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + YEAR_IN_SECONDS ) . ' GMT' );
    header( 'Cache-Control: public, max-age=' . YEAR_IN_SECONDS );

    // Output the key
    echo $key;
    exit;
}

/**
 * Ping IndexNow when a post status changes.
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Old post status.
 * @param WP_Post $post       Post object.
 */
function ping_indexnow( $new_status, $old_status, $post ) : void {
    // Only ping for published posts.
    if ( 'publish' !== $new_status ) {
        return;
    }

    // Skip revisions and autosaves.
    if ( wp_is_post_revision( $post ) || wp_is_post_autosave( $post ) ) {
        return;
    }

    // Skip non-public post types.
    if ( ! is_post_type_viewable( $post->post_type ) ) {
        return;
    }

    $key = get_option( 'fair_indexnow_key' );
    if ( ! $key ) {
        return;
    }

    $url = get_permalink( $post );
    if ( ! $url ) {
        return;
    }

    // Allow for filtering the URL list.
    $url_list = apply_filters( 'fair_indexnow_url_list', [ $url ] );

    // Allow for filtering the key location.
    $key_location = apply_filters( 'fair_indexnow_key_location', trailingslashit( home_url( 'fair-indexnow-' . $key ) ) );

    // The "false" on the end of the x-source-info header determines whether this is a manual submission or not.
    $data = [
            'host'        => wp_parse_url( home_url(), PHP_URL_HOST ),
            'key'         => $key,
            'keyLocation' => $key_location,
            'urlList'     => $url_list,
    ];
    $request = [
        'body' => wp_json_encode( $data, JSON_UNESCAPED_SLASHES ),
        'headers' => [
            'Content-Type'  => 'application/json; charset=utf-8',
            'x-source-info' => 'https://example.com/fair-wp/indexnow/false', // TODO: replace example.com with the domain we end up using.
        ],
    ];

    // Ping IndexNow.
    $response = wp_remote_post(
        'https://api.indexnow.org/indexnow',
        $request
    );

    // Log the response for debugging. As per https://www.indexnow.org/documentation#response, either 200 or 202 is acceptable.
    if ( is_wp_error( $response ) ) {
        error_log( 'IndexNow ping failed: ' . $response->get_error_message() . print_r( $request, true ) );
        return;
    }

    $status = wp_remote_retrieve_response_code( $response );
    if ( ! in_array( $status, [ 200, 202 ], true ) ) {
        error_log( 'IndexNow ping failed: ' . $status . print_r( $request, true ) );
    }
}
