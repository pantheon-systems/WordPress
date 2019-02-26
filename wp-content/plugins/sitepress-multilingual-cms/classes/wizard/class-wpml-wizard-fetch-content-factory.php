<?php

class WPML_Wizard_Fetch_Content_Factory extends WPML_AJAX_Base_Factory {

	public function create() {
		if ( $this->is_valid_action( WPML_Wizard_Fetch_Content_Action::AJAX_ACTION ) ) {
			return new WPML_Wizard_Fetch_Content_Action();
		}
	}
}