<?php

class WPML_PB_Handle_Post_Body {

	private $page_builders_built;

	public function __construct( WPML_Page_Builders_Page_Built $page_builders_built ) {
		$this->page_builders_built = $page_builders_built;
	}

	public function add_hooks() {
		add_filter( 'wpml_pb_should_body_be_translated', array( $this, 'should_translate' ), 10, 3 );
		add_action( 'wpml_pb_finished_adding_string_translations', array( $this, 'copy' ), 10, 3 );
	}

	/**
	 * @param int $translate
	 * @param WP_Post $post
	 *
	 * @return int
	 */
	public function should_translate( $translate, WP_Post $post ) {
		return $this->page_builders_built->is_page_builder_page( $post ) ? 0 : $translate;
	}

	/**
	 * @param int $new_post_id
	 * @param int $original_post_id
	 * @param array $fields
	 */
	public function copy( $new_post_id, $original_post_id, array $fields ) {
		$original_post = get_post( $original_post_id );
		if ( $original_post && $this->page_builders_built->is_page_builder_page( $original_post ) && ! $this->job_has_packages( $fields ) ) {
			wp_update_post( array( 'ID' => $new_post_id, 'post_content' => $original_post->post_content ) );
			do_action( 'wpml_pb_after_page_without_elements_post_content_copy', $new_post_id, $original_post_id );
		}
	}

	/**
	 * @param array $fields
	 *
	 * @return bool
	 */
	private function job_has_packages( array $fields ) {
		foreach ( $fields as $key => $field ) {
			if ( 0 === strpos( $key, 'package' ) ) {
				return true;
			}
		}

		return false;
	}
}