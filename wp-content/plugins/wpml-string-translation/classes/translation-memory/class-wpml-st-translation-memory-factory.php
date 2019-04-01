<?php

class WPML_ST_Translation_Memory_Factory implements IWPML_AJAX_Action_Loader, IWPML_Backend_Action_Loader {

	public function create() {
		global $wpdb, $sitepress;

		return array(
			'translations' => new WPML_ST_Translation_Memory( new WPML_ST_Translation_Memory_Records( $wpdb ) ),
			'settings-ui'  => new WPML_ST_Translation_Memory_Settings_UI( $sitepress ),
		);
	}
}