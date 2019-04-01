<?php

class WPML_ST_Theme_Plugin_Localization_Resources_Factory implements IWPML_Backend_Action_Loader {

	/** @return WPML_ST_Theme_Plugin_Localization_Resources */
	public function create() {
		return new WPML_ST_Theme_Plugin_Localization_Resources();
	}
}