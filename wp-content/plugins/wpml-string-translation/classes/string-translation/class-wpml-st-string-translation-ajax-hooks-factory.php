<?php

class WPML_ST_String_Translation_AJAX_Hooks_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $wpdb;

		$ajax_hooks = array();
		$ajax_hooks[] = new WPML_ST_String_Translation_Priority_AJAX( $wpdb );
		return $ajax_hooks;

	}
}