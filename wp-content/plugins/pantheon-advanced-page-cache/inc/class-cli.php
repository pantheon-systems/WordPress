<?php
/**
 * WP-CLI commands for managing the Pantheon Advanced Page Cache.
 *
 * @package Pantheon_Advanced_Page_Cache
 */

namespace Pantheon_Advanced_Page_Cache;

use WP_CLI;

/**
 * Manage the Pantheon Advanced Page Cache.
 */
class CLI {

	/**
	 * Purge one or more surrogate keys from cache.
	 *
	 * ## OPTIONS
	 *
	 * <key>...
	 * : One or more surrogate keys.
	 *
	 * ## EXAMPLES
	 *
	 *     # Purge the 'post-1' surrogate key from cache.
	 *     $ wp pantheon cache purge-key post-1
	 *     Success: Purged key.
	 *
	 * @subcommand purge-key
	 */
	public function purge_key( $args ) {
		$ret = pantheon_wp_clear_edge_keys( $args );
		if ( is_wp_error( $ret ) ) {
			WP_CLI::error( $ret );
		} else {
			$message = count( $args ) > 1 ? 'Purged keys.' : 'Purged key.';
			WP_CLI::success( $message );
		}
	}

	/**
	 * Purge one or more paths from cache.
	 *
	 * ## OPTIONS
	 *
	 * <key>...
	 * : One or more paths.
	 *
	 * ## EXAMPLES
	 *
	 *     # Purge the homepage from cache.
	 *     $ wp pantheon cache purge-path '/'
	 *     Success: Purged path.
	 *
	 * @subcommand purge-path
	 */
	public function purge_path( $args ) {
		$ret = pantheon_wp_clear_edge_paths( $args );
		if ( is_wp_error( $ret ) ) {
			WP_CLI::error( $ret );
		} else {
			$message = count( $args ) > 1 ? 'Purged paths.' : 'Purged path.';
			WP_CLI::success( $message );
		}
	}

	/**
	 * Purge the entire page cache.
	 *
	 * WARNING! Purging the entire page cache can have a severe performance
	 * impact on a high-traffic site. We encourage you to explore other options
	 * first.
	 *
	 * ## OPTIONS
	 *
	 * [--yes]
	 * : Answer yes to the confirmation message.
	 *
	 * ## EXAMPLES
	 *
	 *     # Purging the entire page cache will display a confirmation prompt.
	 *     $ wp pantheon cache purge-all
	 *     Are you sure you want to purge the entire page cache? [y/n] y
	 *     Success: Purged page cache.
	 *
	 * @subcommand purge-all
	 */
	public function purge_all( $_, $assoc_args ) {
		WP_CLI::confirm( 'Are you sure you want to purge the entire page cache?', $assoc_args );
		$ret = pantheon_wp_clear_edge_all();
		if ( is_wp_error( $ret ) ) {
			WP_CLI::error( $ret );
		} else {
			WP_CLI::success( 'Purged page cache.' );
		}
	}

}
