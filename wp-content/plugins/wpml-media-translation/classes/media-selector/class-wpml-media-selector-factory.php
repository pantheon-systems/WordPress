<?php

class WPML_Media_Selector_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;

		$wpml_wp_api     = $sitepress->get_wp_api();
		$wpml_media_path = $wpml_wp_api->constant( 'WPML_MEDIA_PATH' );

		return new WPML_Media_Selector(
			$sitepress,
			new WPML_Twig_Template_Loader( array( $wpml_media_path . '/templates/media-selector/' ) ),
			new WPML_Media_Post_With_Media_Files_Factory(),
			new WPML_Translation_Element_Factory( $sitepress )
		);
	}


}