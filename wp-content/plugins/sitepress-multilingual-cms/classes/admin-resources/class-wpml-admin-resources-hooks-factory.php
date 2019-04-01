<?php

class WPML_Admin_Resources_Hooks_Factory implements IWPML_Backend_Action_Loader {

	/** @return WPML_Admin_Resources_Hooks */
	public function create() {
		return new WPML_Admin_Resources_Hooks();
	}
}