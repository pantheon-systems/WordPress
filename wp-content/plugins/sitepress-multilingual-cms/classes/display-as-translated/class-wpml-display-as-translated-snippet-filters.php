<?php

class WPML_Display_As_Translated_Snippet_Filters implements IWPML_Action {

	public function add_hooks() {
		add_filter( 'wpml_should_use_display_as_translated_snippet', array( $this, 'filter_post_types' ), 10, 2 );
	}

	public function filter_post_types( $should_use_snippet, array $post_type ) {
		return $should_use_snippet || $this->is_media_ajax_query( $post_type ) || $this->is_admin_media_list_page() || WPML_Ajax::is_frontend_ajax_request();
	}

	private function is_admin_media_list_page() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();
		if (
			null !== $screen &&
			'upload' === $screen->base &&
			'list' === get_user_meta( get_current_user_id(), 'wp_media_library_mode', true )
		) {
			return true;
		}

		return false;
	}

	private function is_media_ajax_query( array $post_type ) {
		return false !== strpos( $_SERVER['REQUEST_URI'], 'admin-ajax' )
		       && isset( $_REQUEST['action'] ) && 'query-attachments' === $_REQUEST['action']
		       && array_key_exists( 'attachment', $post_type );
	}
}