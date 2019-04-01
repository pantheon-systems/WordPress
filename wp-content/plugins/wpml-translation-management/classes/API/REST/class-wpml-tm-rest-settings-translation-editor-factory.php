<?php

class WPML_TM_REST_Settings_Translation_Editor_Factory extends WPML_REST_Factory_Loader {

	public function create() {
		global $sitepress;

		return new WPML_TM_REST_Settings_Translation_Editor( $sitepress );
	}
}