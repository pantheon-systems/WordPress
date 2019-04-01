<?php

class WPML_Elementor_URLs_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	public function create() {
		global $sitepress, $wpml_url_converter;

		return new WPML_Elementor_URLs(
			new WPML_Translation_Element_Factory( $sitepress ),
			$wpml_url_converter->get_strategy(),
			$sitepress
		);
	}
}
