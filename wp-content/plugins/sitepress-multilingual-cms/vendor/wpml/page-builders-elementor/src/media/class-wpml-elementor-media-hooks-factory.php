<?php

class WPML_Elementor_Media_Hooks_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		return new WPML_Page_Builders_Media_Hooks(
			new WPML_Elementor_Update_Media_Factory(),
			WPML_Elementor_Integration_Factory::SLUG
		);
	}
}
