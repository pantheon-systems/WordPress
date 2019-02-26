<?php

/**
 * Class WPML_Media_Save_Translation_Factory
 */
class WPML_Media_Save_Translation_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress, $wpdb;

		return new WPML_Media_Save_Translation( $sitepress, $wpdb, new WPML_Media_File_Factory(), new WPML_Translation_Element_Factory( $sitepress ) );
	}

}