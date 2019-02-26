<?php

class WPML_Copy_Once_Custom_Field_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader  {
	public function create() {
		global $sitepress, $wpdb, $wpml_post_translations;
		return new WPML_Copy_Once_Custom_Field( $sitepress, $wpdb, $wpml_post_translations );
	}
}
