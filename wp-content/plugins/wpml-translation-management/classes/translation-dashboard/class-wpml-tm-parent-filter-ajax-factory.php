<?php

class WPML_TM_Parent_Filter_Ajax_Factory implements IWPML_AJAX_Action_Loader {

	public function create() {
		global $sitepress, $wp_post_types;
		return new WPML_TM_Parent_Filter_Ajax( $sitepress, array_keys( $wp_post_types ) );
	}
}