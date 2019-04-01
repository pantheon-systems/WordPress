<?php

class WPML_Beaver_Builder_Media_Hooks_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	public function create() {
		return new WPML_Page_Builders_Media_Hooks(
			new WPML_Beaver_Builder_Update_Media_Factory(),
			WPML_Beaver_Builder_Integration_Factory::SLUG
		);
	}
}
