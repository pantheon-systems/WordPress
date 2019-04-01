<?php

class WPML_Tax_Permalink_Filters_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader {

	public function create() {
		global $wpml_url_converter, $sitepress;

		return new WPML_Tax_Permalink_Filters(
			$wpml_url_converter,
			new WPML_WP_Cache_Factory(),
			new WPML_Translation_Element_Factory( $sitepress ),
			WPML_Get_LS_Languages_Status::get_instance()
		);
	}
}