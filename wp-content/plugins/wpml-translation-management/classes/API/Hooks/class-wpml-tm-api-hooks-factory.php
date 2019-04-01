<?php

class WPML_TM_API_Hooks_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	public function create() {
		$hooks = array();

		$hooks[] = new WPML_TM_API_Hook_Links();

		return $hooks;
	}
}