<?php

class WPML_REST_Language_Middleware_Hooks_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;

		return new WPML_REST_Language_Middleware_Hooks( $sitepress );
	}
}
