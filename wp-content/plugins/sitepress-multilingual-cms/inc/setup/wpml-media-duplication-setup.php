<?php

class WPML_Media_Duplication_Setup {

	const MEDIA_SETTINGS_OPTION_KEY = '_wpml_media';

	public static function initialize_settings(){
		if ( ! get_option( self::MEDIA_SETTINGS_OPTION_KEY, array() ) ){
			$settings = array(
				'new_content_settings' => array(
					'always_translate_media' => true,
					'duplicate_media'        => true,
					'duplicate_featured'     => true,
				)
			);
			update_option( self::MEDIA_SETTINGS_OPTION_KEY, $settings );
		}
	}

}