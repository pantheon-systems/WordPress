<?php

class WCML_Product_Gallery_Filter implements IWPML_Action {

	/**
	 * @var WPML_Translation_Element_Factory
	 */
	private $translation_element_factory;

	public function __construct( WPML_Translation_Element_Factory $translation_element_factory ) {
		$this->translation_element_factory = $translation_element_factory;
	}

	public function add_hooks() {
		add_filter( 'get_post_metadata', array( $this, 'localize_image_ids' ), 10, 3 );
	}

	public function localize_image_ids( $value, $object_id, $meta_key ) {
		if ( '_product_image_gallery' === $meta_key &&
		     in_array( get_post_type( $object_id ), array( 'product', 'product_variation' ) ) ) {

			remove_filter( 'get_post_metadata', array( $this, 'localize_image_ids' ), 10 );

			$meta_value = array();

			$post_element   = $this->translation_element_factory->create( $object_id, 'post' );
			$source_element = $post_element->get_source_element();
			if ( null !== $source_element ) {
				$original_gallery_value = get_post_meta( $source_element->get_id(), '_product_image_gallery', true );
				if ( $original_gallery_value ) {
					$original_gallery = explode( ',', $original_gallery_value );
					$original_gallery = array_filter( $original_gallery );

					foreach ( $original_gallery as $attachment_id ) {
						$attachment_element    = $this->translation_element_factory->create( $attachment_id, 'post' );
						$translated_attachment = $attachment_element->get_translation( $post_element->get_language_code() );
						if ( null !== $translated_attachment ) {
							$meta_value[] = $translated_attachment->get_id();
						} else {
							$meta_value[] = $attachment_id;
						}
					}
				}
			}

			if ( ! empty( $meta_value ) ) {
				$value = implode( ',', $meta_value );
			}

			add_filter( 'get_post_metadata', array( $this, 'localize_image_ids' ), 10, 3 );

		}

		return $value;
	}

}