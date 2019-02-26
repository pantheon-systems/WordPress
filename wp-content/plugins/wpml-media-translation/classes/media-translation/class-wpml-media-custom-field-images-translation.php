<?php

/**
 * Class WPML_Media_Custom_Field_Images_Translation
 * Translate images in posts custom fields translations when a custom field is created or updated
 */
class WPML_Media_Custom_Field_Images_Translation implements IWPML_Action {

	/**
	 * @var WPML_Media_Custom_Field_Images_Translation
	 */
	private $images_updater;
	/**
	 * @var SitePress
	 */
	private $sitepress;
	/**
	 * @var TranslationManagement
	 */
	private $iclTranslationManagement;

	/**
	 * WPML_Media_Custom_Field_Images_Translation constructor.
	 *
	 * @param WPML_Media_Translated_Images_Update $images_updater
	 * @param SitePress $sitepress
	 * @param TranslationManagement $iclTranslationManagement
	 */
	public function __construct(
		WPML_Media_Translated_Images_Update $images_updater,
		SitePress $sitepress,
		TranslationManagement $iclTranslationManagement

	) {
		$this->images_updater           = $images_updater;
		$this->sitepress                = $sitepress;
		$this->iclTranslationManagement = $iclTranslationManagement;
	}

	public function add_hooks() {
		add_action( 'updated_post_meta', array( $this, 'translate_images' ), PHP_INT_MAX, 4 );
		add_action( 'added_post_meta', array( $this, 'translate_images' ), PHP_INT_MAX, 4 );
	}

	/**
	 * @param int $meta_id
	 * @param int $object_id
	 * @param string $meta_key
	 * @param string $meta_value
	 */
	public function translate_images( $meta_id, $object_id, $meta_key, $meta_value ) {

		$settings_factory = new WPML_Custom_Field_Setting_Factory( $this->iclTranslationManagement );
		$setting          = $settings_factory->post_meta_setting( $meta_key );

		$is_custom_field_translatable = $this->sitepress->get_wp_api()
		                                                ->constant( 'WPML_TRANSLATE_CUSTOM_FIELD' ) === $setting->status();
		$post_type                    = get_post_type( $object_id );
		$is_post_translatable         = $this->sitepress->is_translated_post_type( $post_type );

		if ( is_string( $meta_value ) && $is_post_translatable && $is_custom_field_translatable ) {
			$post_element    = new WPML_Post_Element( $object_id, $this->sitepress );
			$source_language = $post_element->get_source_language_code();
			if ( null !== $source_language ) {
				$this->filter_meta_value_and_update(
					$meta_value,
					$meta_key,
					$post_element->get_language_code(),
					$source_language,
					$object_id
				);
			} else {
				foreach ( array_keys( $this->sitepress->get_active_languages() ) as $language ) {
					$translation = $post_element->get_translation( $language );
					if( $translation ) {
						$this->filter_meta_value_and_update(
							$meta_value,
							$meta_key,
							$language,
							$source_language,
							$translation->get_id()
						);
					}
				}
			}
		}
	}

	/**
	 * @param string $meta_value
	 * @param string $target_language
	 * @param string $source_language
	 * @param string $meta_key
	 * @param int $post_id
	 *
	 * @return string
	 */
	private function filter_meta_value_and_update( $meta_value, $meta_key, $target_language, $source_language, $post_id ) {
		$meta_value_filtered = $this->images_updater->replace_images_with_translations(
			$meta_value,
			$target_language,
			$source_language
		);

		remove_action( 'updated_post_meta', array( $this, 'translate_images' ), PHP_INT_MAX, 4 );
		update_post_meta( $post_id, $meta_key, wp_slash( $meta_value_filtered ), $meta_value );
		add_action( 'updated_post_meta', array( $this, 'translate_images' ), PHP_INT_MAX, 4 );

		return $meta_value_filtered;
	}
}