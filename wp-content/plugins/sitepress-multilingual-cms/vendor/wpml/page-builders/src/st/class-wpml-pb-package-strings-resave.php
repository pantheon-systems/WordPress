<?php

class WPML_PB_Package_Strings_Resave {

	/** @var WPML_ST_String_Factory $string_factory */
	private $string_factory;

	public function __construct( WPML_ST_String_Factory $string_factory ) {
		$this->string_factory = $string_factory;
	}

	/**
	 * @param WPML_Post_Element $post_element
	 *
	 * @return WPML_Package[]
	 */
	public function from_element( WPML_Post_Element $post_element ) {
		if ( ! $post_element->get_source_element() ) {
			return array();
		}

		$target_lang      = $post_element->get_language_code();
		$original_post_id = $post_element->get_source_element()->get_id();

		/** @var WPML_Package[] $string_packages */
		$string_packages =  apply_filters( 'wpml_st_get_post_string_packages', array(), $original_post_id );

		foreach ( $string_packages as $string_package ) {

			/** @var stdClass[] $strings */
			$strings = $string_package->get_package_strings();

			foreach ( $strings as $string ) {
				$this->resave_string_translation( $string->id, $target_lang );
			}
		}

		return $string_packages;
	}

	/**
	 * @param int    $string_id
	 * @param string $target_lang
	 */
	private function resave_string_translation( $string_id, $target_lang ) {
		$string       = $this->string_factory->find_by_id( $string_id );
		$translations = wp_list_filter( $string->get_translations(), array( 'language' => $target_lang ) );

		if ( $translations ) {
			$translation = reset( $translations );

			$string->set_translation(
				$target_lang,
				$translation->value,
				$translation->status,
				$translation->translator_id,
				$translation->translation_service,
				$translation->batch_id
			);
		}
	}
}
