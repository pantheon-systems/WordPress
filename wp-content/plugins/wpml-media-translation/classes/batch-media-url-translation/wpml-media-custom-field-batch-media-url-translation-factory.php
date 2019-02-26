<?php

/**
 * Class WPML_Media_Custom_Field_Batch_Url_Translation_Factory
 */
class WPML_Media_Custom_Field_Batch_Url_Translation_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $wpdb, $sitepress;

		if ( WPML_Media_Custom_Field_Batch_Url_Translation::is_ajax_request() ) {

			$translatable_custom_fields = $sitepress->get_custom_fields_translation_settings(
				$sitepress->get_wp_api()->constant( 'WPML_TRANSLATE_CUSTOM_FIELD' )
			);

			$custom_field_images_translation_factory = new WPML_Media_Custom_Field_Images_Translation_Factory();

			return new WPML_Media_Custom_Field_Batch_Url_Translation(
				$custom_field_images_translation_factory->create(), $wpdb, $translatable_custom_fields );

		}

		return null;
	}

}