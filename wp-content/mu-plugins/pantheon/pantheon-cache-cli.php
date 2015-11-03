<?php

/**
 * Manage Pantheon Cache
 */
class Pantheon_Cache_CLI extends WP_CLI_Command {

	/**
	 * Purge the cache for one or more URL paths
	 *
	 * [<path>...]
	 * : Specify one or more URL paths to purge cache for.
	 *
	 * [--all]
	 * : Purge cache for all URL paths.
	 */
	public function clear( $args, $assoc_args ) {

		$instance = Pantheon_Cache::instance();
		$instance->paths = array();

		if ( ! function_exists( 'pantheon_clear_edge' ) ) {
			WP_CLI::warning( "pantheon_clear_edge() function doesn't exist in this environment so cache won't be purged." );
		}

		if ( true === WP_CLI\Utils\get_flag_value( $assoc_args, 'all' ) ) {
			$instance->paths[] = '/.*';
			$instance->cache_clean_urls();
			WP_CLI::success( "Cache purged for all URL paths." );
		} else {
			$instance->enqueue_urls( $args );
			if ( empty( $instance->paths ) ) {
				WP_CLI::error( 'Please provide one or more URL paths to purge, or use the --all flag.' );
			}
			$path_count = count( $instance->paths );
			$instance->cache_clean_urls();
			WP_CLI::success( "Cache purged for {$path_count} URL paths." );
		}
	}

}

WP_CLI::add_command( 'pantheon cache', 'Pantheon_Cache_CLI' );
