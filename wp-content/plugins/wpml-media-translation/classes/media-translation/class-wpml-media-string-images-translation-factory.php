<?php

/**
 * Class WPML_Media_String_Images_Translation_Factory
 */
class WPML_Media_String_Images_Translation_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $wpdb, $sitepress;

		$media_localization_settings = WPML_Media::get_setting( 'media_files_localization' );

		if ( $media_localization_settings['strings'] ) {
			$image_translator = new WPML_Media_Image_Translate( $sitepress, new WPML_Media_Attachment_By_URL_Factory() );
			$image_updater    = new WPML_Media_Translated_Images_Update( new WPML_Media_Img_Parse(), $image_translator, new WPML_Media_Sizes() );
			$string_factory   = new WPML_ST_String_Factory( $wpdb );

			return new WPML_Media_String_Images_Translation( $image_updater, $string_factory );
		}

		return null;
	}

}