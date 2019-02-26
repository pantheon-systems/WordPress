<?php

/**
 * Created by PhpStorm.
 * User: andreasciamanna
 * Date: 22/05/2018
 * Time: 08:44
 */
class WPML_Current_Screen {
	private $translatable_types = array();
	private $allowed_screen_ids_for_edit_posts_list = array();
	private $allowed_screen_ids_for_edit_post = array();

	public function is_edit_posts_list() {
		return $this->get() && in_array( $this->get()->id, $this->get_allowed_screen_ids_for_edit_posts_list() )
		       && $this->has_posts();

	}

	public function is_edit_post() {
		return $this->get() && in_array( $this->get()->id, $this->get_allowed_screen_ids_for_edit_post() )
		       && $this->has_post();

	}

	private function get_translatable_types() {
		if ( ! $this->translatable_types ) {
			$translatable_types       = apply_filters( 'wpml_translatable_documents', array() );
			if ( $translatable_types ) {
				$this->translatable_types = array_keys( $translatable_types );
			}
		}

		return $this->translatable_types;
	}

	private function get_allowed_screen_ids_for_edit_posts_list() {
		if ( ! $this->allowed_screen_ids_for_edit_posts_list ) {
			foreach ( $this->get_translatable_types() as $translatable_type ) {
				$this->allowed_screen_ids_for_edit_posts_list[] = 'edit-' . $translatable_type;
			}
		}

		return $this->allowed_screen_ids_for_edit_posts_list;
	}


	private function get_allowed_screen_ids_for_edit_post() {
		if ( ! $this->allowed_screen_ids_for_edit_post ) {
			foreach ( $this->get_translatable_types() as $translatable_type ) {
				$this->allowed_screen_ids_for_edit_post[] = $translatable_type;
			}
		}

		return $this->allowed_screen_ids_for_edit_post;
	}

	public function get_posts() {
		if ( $this->has_posts() && $this->is_edit_posts_list() ) {
			return $GLOBALS['posts'];
		} elseif ( $this->has_post() && $this->is_edit_post() ) {
			$post = $this->get_post();

			if ( $post ) {
				return array( $post );
			}
		}

		return array();
	}

	private function get_post() {
		if ( $this->has_post() && $this->is_edit_post() ) {
			$post_id   = filter_var( $_GET['post'], FILTER_SANITIZE_NUMBER_INT );
			$post_type = $this->get_post_type();

			return get_post( $post_id, $post_type );
		}

		return null;
	}

	public function get_post_type() {
		return $this->get() ? $this->get()->post_type : null;
	}

	public function id_ends_with( $suffix ) {
		return $this->get() && ( substr( $this->get()->id, - strlen( $suffix ) ) === $suffix );
	}

	/**
	 * @return WP_Screen|null
	 */
	private function get() {
		return array_key_exists( 'current_screen', $GLOBALS ) ? $GLOBALS['current_screen'] : null;
	}

	private function has_posts() {
		return $this->get()
		       && ( array_key_exists( 'posts', $GLOBALS ) )
		       && $GLOBALS['posts'];
	}

	private function has_post() {
		return $this->get()
		       && array_key_exists( 'post', $_GET );
	}
}