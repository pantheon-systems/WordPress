<?php

class WPML_Media_Populate_Media_Strings_Translations_Factory implements IWPML_Backend_Action_Loader {
	public function create() {
		global $sitepress;

		if ( class_exists( 'WPML_Element_Translation_Package' ) ) {
			return new WPML_Media_Populate_Media_Strings_Translations(
				new WPML_Translation_Element_Factory( $sitepress ),
				new WPML_Element_Translation_Package()
			);
		}
	}
}