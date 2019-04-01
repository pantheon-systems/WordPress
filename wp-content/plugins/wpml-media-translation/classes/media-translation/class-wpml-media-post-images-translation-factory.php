<?php

/**
 * Class WPML_Media_Post_Images_Translation_Factory
 */
class WPML_Media_Post_Images_Translation_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return IWPML_Action|null|WPML_Media_Post_Images_Translation
	 */
	public function create() {
		global $wpdb, $sitepress;

		$media_localization_settings = WPML_Media::get_setting( 'media_files_localization' );
		if ( $media_localization_settings['posts'] || WPML_Media_Post_Batch_Url_Translation::is_ajax_request() ) {
			$image_translator = new WPML_Media_Image_Translate( $sitepress, new WPML_Media_Attachment_By_URL_Factory() );
			$image_updater    = new WPML_Media_Translated_Images_Update( new WPML_Media_Img_Parse(), $image_translator, new WPML_Media_Sizes() );

			return new WPML_Media_Post_Images_Translation(
				$image_updater,
				$sitepress,
				$wpdb,
				new WPML_Translation_Element_Factory( $sitepress ),
				new WPML_Media_Custom_Field_Images_Translation_Factory(),
				new WPML_Media_Usage_Factory()
			);
		}

		return null;
	}

}