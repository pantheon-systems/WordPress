<?php

/**
 * Class WPML_Media_String_Images_Translation
 * Translate images in posts strings translations when a string translation is created or updated
 */
class WPML_Media_String_Images_Translation implements IWPML_Action {

	/**
	 * @var WPML_Media_String_Images_Translation
	 */
	private $images_updater;
	/**
	 * @var WPML_ST_String_Factory
	 */
	private $string_factory;

	/**
	 * WPML_Media_String_Images_Translation constructor.
	 *
	 * @param WPML_Media_Translated_Images_Update $images_updater
	 * @param WPML_ST_String_Factory $string_factory
	 */
	public function __construct( WPML_Media_Translated_Images_Update $images_updater, WPML_ST_String_Factory $string_factory ) {
		$this->images_updater = $images_updater;
		$this->string_factory = $string_factory;
	}

	public function add_hooks() {
		add_filter( 'wpml_st_string_translation_before_save', array( $this, 'translate_images' ), PHP_INT_MAX, 3 );
	}

	/**
	 * @param array  $translation_data
	 * @param string $target_language
	 * @param int $string_id
	 *
	 * @return array
	 */
	public function translate_images( $translation_data, $target_language, $string_id ) {
		if ( ! empty( $translation_data['value'] ) ) {
			$original_string = $this->string_factory->find_by_id( $string_id );

			$translation_data['value'] = $this->images_updater->replace_images_with_translations(
				$translation_data['value'],
				$target_language,
				$original_string->get_language()
			);
		}

		return $translation_data;
	}

}