<?php

namespace Pantheon_Sessions;

class Admin {

	private static $instance;

	private static $capability = 'manage_options';

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Admin;
			self::$instance->setup_actions();
		}
		return self::$instance;
	}

	/**
	 * Load admin actions
	 */
	private function setup_actions() {

		add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
		add_action( 'wp_ajax_pantheon_clear_session', array( $this, 'handle_clear_session' ) );

	}

	/**
	 * Register the admin menu
	 */
	public function action_admin_menu() {

		add_management_page( __( 'Pantheon Sessions', 'pantheon-sessions' ), __( 'Sessions', 'pantheon-sessions' ), self::$capability, 'pantheon-sessions', array( $this, 'handle_page' ) );

	}

	/**
	 * Render the admin page
	 */
	public function handle_page() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		require_once dirname( __FILE__ ) . '/class-list-table.php';

		echo '<div class="wrap">';

		echo '<div>';
		$query_args = array(
			'action'       => 'pantheon_clear_session',
			'nonce'        => wp_create_nonce( 'pantheon_clear_session' ),
			'session'      => 'all',
			);
		if ( $wpdb->get_var( "SELECT COUNT(session_id) FROM $wpdb->pantheon_sessions" ) ) {
			echo '<a class="button pantheon-clear-all-sessions" style="float:right; margin-top: 9px;" href="' . esc_url( add_query_arg( $query_args, admin_url( 'admin-ajax.php' ) ) ) . '">' . esc_html__( 'Clear All', 'pantheon-sessions' ) . '</a>';
		}
		echo '<h2>' . esc_html__( 'Pantheon Sessions', 'pantheon-sessions' ) . '</h2>';
		if ( isset( $_GET['message'] ) && in_array( $_GET['message'], array( 'delete-all-session', 'delete-session' ) ) ) {
			if ( 'delete-all-session' === $_GET['message'] ) {
				$message = __( 'Cleared all sessions.', 'pantheon-sessions' );
			} else if ( 'delete-session' === $_GET['message'] ) {
				$message = __( 'Session cleared.', 'pantheon-sessions' );
			}
			echo '<div id="message" class="updated"><p>' . esc_html( $message ) . '</p></div>';
		}
		echo '</div>';

		$wp_list_table = new List_Table;
		$wp_list_table->prepare_items();
		$wp_list_table->display();

		echo '</div>';

		add_action( 'admin_footer', array( $this, 'action_admin_footer' ) );

	}

	/**
	 * Handle a request to clear all sessions
	 */
	public function handle_clear_session() {
		global $wpdb;

		if ( ! current_user_can( self::$capability ) || ! wp_verify_nonce( $_GET['nonce'], 'pantheon_clear_session' ) ) {
			wp_die( esc_html__( "You don't have permission to do this.", 'pantheon-sessions' ) );
		}

		if ( 'all' == $_GET['session'] ) {
			$wpdb->query( "DELETE FROM $wpdb->pantheon_sessions" );
			$message = 'delete-all-session';
		} else {
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->pantheon_sessions WHERE session_id=%s", sanitize_text_field( $_GET['session'] ) ) );
			$message = 'delete-session';
		}
		wp_safe_redirect( add_query_arg( 'message', $message, wp_get_referer() ) );
		exit;

	}

	/**
	 * Stuff that needs to go in the footer
	 */
	public function action_admin_footer() {
		?>
	<script>
	(function($){
		$(document).ready(function(){
			$('.pantheon-clear-all-sessions').on('click', function( e ){
				if ( ! confirm( '<?php esc_html_e( "Are you sure you want to clear all active sessions?", "pantheon-sessions" ); ?>') ) {
					e.preventDefault();
				}
			});
		});
	}(jQuery))
	</script>
		<?php
	}

}
