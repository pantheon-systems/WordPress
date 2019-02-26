<?php

class WPML_Elementor_URLs implements IWPML_Action {

	/** @var WPML_Translation_Element_Factory */
	private $element_factory;

	/** @var IWPML_URL_Converter_Strategy */
	private $language_converter;

	/** @var IWPML_Current_Language  */
	private $current_language;

	public function __construct(
		WPML_Translation_Element_Factory $element_factory,
		IWPML_URL_Converter_Strategy $language_converter,
		IWPML_Current_Language $current_language
	) {
		$this->element_factory    = $element_factory;
		$this->language_converter = $language_converter;
		$this->current_language = $current_language;
	}

	public function add_hooks() {
		add_filter( 'elementor/document/urls/edit', array( $this, 'adjust_edit_with_elementor_url' ), 10, 2 );
	}

	public function adjust_edit_with_elementor_url( $url, $elementor_document ) {
		$post = $elementor_document->get_main_post();

		$post_element  = $this->element_factory->create_post( $post->ID );
		$post_language = $post_element->get_language_code();

		if ( ! $post_language ) {
			$post_language = $this->current_language->get_current_language();
		}

		return $this->language_converter->convert_admin_url_string( $url, $post_language );
	}
}
