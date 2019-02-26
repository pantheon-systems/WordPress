<?php

/**
 * Class WPML_Media_Post_Batch_Url_Translation_Factory
 */
class WPML_Media_Post_Batch_Url_Translation_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $wpdb;

		if ( WPML_Media_Post_Batch_Url_Translation::is_ajax_request() ) {
			$post_images_translation_factory = new WPML_Media_Post_Images_Translation_Factory();

			return new WPML_Media_Post_Batch_Url_Translation( $post_images_translation_factory->create(), $wpdb );
		}

		return null;
	}

}