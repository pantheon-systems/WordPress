<?php

class WPML_Beaver_Builder_Cleanup_Hooks_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	public function create() {
		return new WPML_Beaver_Builder_Cleanup_Hooks();
	}
}
