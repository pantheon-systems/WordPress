<?php

class WPML_Media_Set_Initial_Language_Factory implements IWPML_Backend_Action_Loader {

	public function create(){
		global $sitepress, $wpdb;
		return new WPML_Media_Set_Initial_Language( $wpdb, $sitepress->get_default_language() );
	}

}