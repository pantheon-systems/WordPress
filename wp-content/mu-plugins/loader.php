<?php
/**
 * Plugin Name: Pantheon MU Plugin Loader
 * Description: Loads the MU plugins required to run the site
 * Author: Pantheon Systems
 * Author URI: https://pantheon.io
 * Version: 1.0
 */

if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
	return;
}

// Add mu-plugins here.
$pantheon_mu_plugins = [
	'pantheon-mu-plugin/pantheon.php',
];

foreach ( $pantheon_mu_plugins as $file ) {
	require_once WPMU_PLUGIN_DIR . '/' . $file;
}
unset( $file );

add_action( 'pre_current_active_plugins', function () use ( $pantheon_mu_plugins ) {
	global $plugins, $wp_list_table;

	// Add our own mu-plugins to the page.
	foreach ( $pantheon_mu_plugins as $plugin_file ) {
		// Do not apply markup/translate as it'll be cached.
		$plugin_data = get_plugin_data( WPMU_PLUGIN_DIR . "/$plugin_file", false, false );

		if ( empty( $plugin_data['Name'] ) ) {
			$plugin_data['Name'] = $plugin_file;
		}

		$plugins['mustuse'][ $plugin_file ] = $plugin_data;
	}

	// Recount totals.
	$GLOBALS['totals']['mustuse'] = count( $plugins['mustuse'] );

	// Only apply the rest if we're actually looking at the page.
	if ( $GLOBALS['status'] !== 'mustuse' ) {
		return;
	}

	// Reset the list table's data.
	$wp_list_table->items = $plugins['mustuse'];
	foreach ( $wp_list_table->items as $plugin_file => $plugin_data ) {
		$wp_list_table->items[ $plugin_file ] = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
	}

	$total_this_page = $GLOBALS['totals']['mustuse'];

	if ( $GLOBALS['orderby'] ) {
		uasort( $wp_list_table->items, [ $wp_list_table, '_order_callback' ] );
	}

	// Force showing all plugins.
	// See https://core.trac.wordpress.org/ticket/27110.
	$plugins_per_page = $total_this_page;

	$wp_list_table->set_pagination_args( [
		'total_items' => $total_this_page,
		'per_page'    => $plugins_per_page,
	] );
});

add_filter( 'network_admin_plugin_action_links', function ( $actions, $plugin_file, $plugin_data, $context ) use ( $pantheon_mu_plugins ) {
	if ( $context !== 'mustuse' || ! in_array( $plugin_file, $pantheon_mu_plugins, true ) ) {
		return $actions;
	}

	$actions[] = sprintf( '<span style="color:#333">File: <code>%s</code></span>', $plugin_file );
	return $actions;
}, 10, 4 );