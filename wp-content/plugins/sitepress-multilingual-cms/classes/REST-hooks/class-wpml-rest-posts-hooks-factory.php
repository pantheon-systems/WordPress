<?php

class WPML_REST_Posts_Hooks_Factory extends WPML_REST_Factory_Loader {

	public function create() {
		global $sitepress, $wpml_term_translations;

		return new WPML_REST_Posts_Hooks( $sitepress, $wpml_term_translations );
	}
}
