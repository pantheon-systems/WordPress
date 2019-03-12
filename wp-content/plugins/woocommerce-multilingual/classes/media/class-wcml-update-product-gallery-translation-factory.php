<?php

class WCML_Update_Product_Gallery_Translation_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;

		if ( class_exists( 'WPML_Media_Usage_Factory' ) ) {
			return new WCML_Update_Product_Gallery_Translation(
				new WPML_Translation_Element_Factory( $sitepress ),
				new WPML_Media_Usage_Factory()
			);
		}

		return null;
	}

}