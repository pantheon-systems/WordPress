<?php

class WCML_Update_Product_Gallery_Translation implements IWPML_Action {

	/**
	 * @var WPML_Translation_Element_Factory
	 */
	private $translation_element_factory;
	/**
	 * @var WPML_Media_Usage_Factory
	 */
	private $media_usage_factory;

	public function __construct(
		WPML_Translation_Element_Factory $translation_element_factory,
		WPML_Media_Usage_Factory $media_usage_factory
	) {
		$this->translation_element_factory = $translation_element_factory;
		$this->media_usage_factory         = $media_usage_factory;
	}

	public function add_hooks() {
		add_action( 'wpml_added_media_file_translation', array( $this, 'update_meta' ), PHP_INT_MAX, 3 );
	}

	/**
	 * @param int $original_attachment_id
	 */
	public function update_meta( $original_attachment_id, $file, $language ) {
		$media_usage = $this->media_usage_factory->create( $original_attachment_id );

		$posts = $media_usage->get_posts();
		foreach ( $posts as $source_post_id ) {
			$source_post                 = $this->translation_element_factory->create( $source_post_id, 'post' );
			$original_attachment_element = $this->translation_element_factory->create( $original_attachment_id, 'post' );
			$updated_attachment_element  = $original_attachment_element->get_translation( $language );
			$meta_value                  = $this->get_translated_gallery( $source_post_id, $updated_attachment_element );
			$this->update_gallery( $meta_value, $source_post, $updated_attachment_element );
		}
	}

	/**
	 * @param int $source_post_id
	 * @param WPML_Post_Element $updated_attachment_element
	 *
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	private function get_translated_gallery( $source_post_id, WPML_Post_Element $updated_attachment_element ) {
		$meta_value = array();

		$original_gallery_meta = get_post_meta( $source_post_id, '_product_image_gallery', true );
		if ( '' !== $original_gallery_meta ) {
			$original_gallery = explode( ',', $original_gallery_meta );

			foreach ( $original_gallery as $original_attachment_id ) {
				$attachment_element    = $this->translation_element_factory->create( $original_attachment_id, 'post' );
				$translated_attachment = $attachment_element->get_translation( $updated_attachment_element->get_language_code() );
				if ( null !== $translated_attachment ) {
					$meta_value[] = $translated_attachment->get_id();
				} else {
					$meta_value[] = $original_attachment_id;
				}
			}
		}

		return $meta_value;
	}

	/**
	 * @param array $meta_value
	 * @param WPML_Post_Element $source_post
	 * @param WPML_Post_Element $updated_attachment_element
	 *
	 * @throws \InvalidArgumentException
	 */
	private function update_gallery(
		array $meta_value,
		WPML_Post_Element $source_post,
		WPML_Post_Element $updated_attachment_element
	) {
		if ( ! empty( $meta_value ) ) {
			$translated_post = $source_post->get_translation( $updated_attachment_element->get_language_code() );
			/** $translated_post could be null  */
			if ( $translated_post ) {
				$value = implode( ',', $meta_value );
				update_post_meta( $translated_post->get_id(), '_product_image_gallery', $value );
			}
		}
	}

}