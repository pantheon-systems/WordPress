<?php

class WPML_TM_Wizard_Steps_Factory implements IWPML_AJAX_Action_Loader {

	public function create() {
		global $wpdb, $sitepress;

		return new WPML_TM_Wizard_Steps(
			new WPML_Translation_Manager_Records( $wpdb, new WPML_WP_User_Query_Factory() ),
			new WPML_Translator_Records( $wpdb, new WPML_WP_User_Query_Factory() ),
			new WPML_TM_Translation_Services_Admin_Section_Factory(),
			$sitepress
		);
	}
}