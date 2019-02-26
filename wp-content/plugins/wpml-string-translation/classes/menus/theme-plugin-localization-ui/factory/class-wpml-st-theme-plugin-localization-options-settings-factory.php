<?php

class WPML_ST_Theme_Plugin_Localization_Options_Settings_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		return new WPML_ST_Theme_Plugin_Localization_Options_Settings();
	}
}