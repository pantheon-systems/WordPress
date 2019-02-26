<?php

class WPML_Compatibility_Tiny_Compress_Images {

	/** @var \WPML_Translation_Element_Factory */
	private $element_factory;

	/**
	 * WPML_Compatibility_Tiny_Compress_Images constructor.
	 *
	 * @param \WPML_Translation_Element_Factory $element_factory
	 */
	function __construct( WPML_Translation_Element_Factory $element_factory ) {
		$this->element_factory = $element_factory;
	}

	public function add_hooks() {
		add_action( 'updated_tiny_postmeta', array( $this, 'updated_tiny_postmeta_action' ), 10, 3 );
	}

	/**
	 * @param int    $post_id
	 * @param string $meta_key
	 * @param mixed  $meta_value
	 */
	public function updated_tiny_postmeta_action( $post_id, $meta_key, $meta_value ) {
		$attachment   = $this->element_factory->create_post( $post_id );
		$translations = $attachment->get_translations();

		if ( ! $translations ) {
			return;
		}

		$attached_file = get_attached_file( $post_id );

		/** @var WPML_Translation_Element $translation */
		foreach ( $translations as $translation ) {
			$translation_id = $translation->get_id();
			if ( $translation_id !== (int) $post_id && $this->source_and_translation_matches( $attached_file, $translation_id ) ) {
				update_post_meta( $translation_id, $meta_key, $meta_value );
			}
		}
	}

	private function source_and_translation_matches( $source_attachment_file, $translated_attachment_id ) {
		return get_attached_file( $translated_attachment_id ) === $source_attachment_file;
	}
}