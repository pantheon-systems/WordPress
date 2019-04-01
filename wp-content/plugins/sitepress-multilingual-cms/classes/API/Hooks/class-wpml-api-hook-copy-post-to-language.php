<?php

/**
 * Class WPML_API_Hook_Copy_Post_To_Language
 */
class WPML_API_Hook_Copy_Post_To_Language implements IWPML_Action {

	/** @var WPML_Post_Duplication $post_duplication */
	private $post_duplication;

	public function __construct( WPML_Post_Duplication $post_duplication ) {
		$this->post_duplication = $post_duplication;
	}

	public function add_hooks() {
		add_filter( 'wpml_copy_post_to_language', array( $this, 'copy_post_to_language' ), 10, 3 );
	}

	public function copy_post_to_language( $post_id, $target_language, $mark_as_duplicate ) {
		$duplicate_post_id = $this->post_duplication->make_duplicate( $post_id, $target_language );

		if( ! $mark_as_duplicate ) {
			delete_post_meta( $duplicate_post_id, '_icl_lang_duplicate_of' );
		}

		return $duplicate_post_id;
	}
}