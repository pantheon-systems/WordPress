<?php

class WPML_Attachment_Action_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader, IWPML_AJAX_Action_Loader, IWPML_Deferred_Action_Loader {

	public function get_load_action() {
		return 'wpml_loaded';
	}

	public function create() {
		global $sitepress, $wpdb;

		return new WPML_Attachment_Action( $sitepress, $wpdb );
	}

}