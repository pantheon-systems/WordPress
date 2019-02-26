<?php

/**
 * Class WPML_Compatibility_Jetpack
 */
class WPML_Compatibility_Jetpack implements IWPML_Action {

	/**
	 * Add hooks.
	 */
	public function add_hooks() {
		add_filter(
			'publicize_should_publicize_published_post',
			array( $this, 'publicize_should_publicize_published_post_filter' ), 10, 2
		);
	}

	/**
	 * Filter to prevent duplicate post from being publicized.
	 *
	 * @param bool $should_publicize Should publicize post.
	 * @param WP_Post $post Post.
	 *
	 * @return bool
	 */
	public function publicize_should_publicize_published_post_filter( $should_publicize, $post ) {
		return ! $this->is_post_duplicated( $post );
	}

	/**
	 * Check if post is a duplicate being created at the moment.
	 * We cannot use standard method to determine duplicate as post meta '_icl_lang_duplicate_of' is not set yet.
	 *
	 * @param $post
	 *
	 * @return bool
	 */
	private function is_post_duplicated( $post ) {
		if (
			apply_filters( 'wpml_is_translated_post_type', false, $post->post_type ) &&
			did_action( 'wpml_before_make_duplicate' )
		) {
			return true;
		}

		return false;
	}
}
