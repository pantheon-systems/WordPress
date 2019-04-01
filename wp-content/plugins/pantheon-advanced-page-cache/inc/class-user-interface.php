<?php
/**
 * Controller for a variety of user interfaces
 *
 * @package Pantheon_Advanced_Page_Cache
 */

namespace Pantheon_Advanced_Page_Cache;

/**
 * Controller for a variety of admin UI.
 */
class User_Interface {

	/**
	 * Register a toolbar button to purge the cache for the current page.
	 *
	 * @param object $wp_admin_bar Instance of WP_Admin_Bar.
	 */
	public static function action_admin_bar_menu( $wp_admin_bar ) {
		if ( is_admin() || ! is_user_logged_in() || ! ( current_user_can( 'delete_others_posts' ) || current_user_can( 'manage_options' ) ) ) {
			return;
		}

		if ( ! empty( $_GET['message'] ) && 'pantheon-cleared-url-cache' === $_GET['message'] ) {
			$title = __( 'URL Cache Cleared', 'pantheon-advanced-page-cache' );
		} else {
			$title = __( 'Clear URL Cache', 'pantheon-advanced-page-cache' );
		}

		$wp_admin_bar->add_menu(
			array(
				'parent' => '',
				'id'     => 'clear-page-cache',
				'title'  => $title,
				'meta'   => array(
					'title' => __( 'Delete cache of the current URL.', 'pantheon-advanced-page-cache' ),
				),
				'href'   => wp_nonce_url( admin_url( 'admin-ajax.php?action=pantheon_clear_url_cache&path=' . urlencode( preg_replace( '/[ <>\'\"\r\n\t\(\)]/', '', $_SERVER['REQUEST_URI'] ) ) ), 'clear-url-cache' ),
			)
		);
	}

	/**
	 * Handle an admin-ajax request to clear the URL cache.
	 */
	public static function handle_ajax_clear_url_cache() {

		if ( empty( $_GET['_wpnonce'] )
			|| ! wp_verify_nonce( $_GET['_wpnonce'], 'clear-url-cache' )
			|| ! current_user_can( 'delete_others_posts' ) ) {
			wp_die( __( "You shouldn't be doing this.", 'pantheon-advanced-page-cache' ) );
		}

		$ret = pantheon_wp_clear_edge_paths( array( $_GET['path'] ) );
		if ( is_wp_error( $ret ) ) {
			wp_die( $ret->get_error_message() );
		}
		wp_safe_redirect( add_query_arg( 'message', 'pantheon-cleared-url-cache', preg_replace( '/[ <>\'\"\r\n\t\(\)]/', '', $_GET['path'] ) ) );
		exit;
	}
}
