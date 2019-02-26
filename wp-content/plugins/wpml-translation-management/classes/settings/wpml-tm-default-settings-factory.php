<?php

class WPML_TM_Default_Settings_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader {

	public function create() {
		/** @var TranslationManagement */
		global $iclTranslationManagement;

		return new WPML_TM_Default_Settings( $iclTranslationManagement );
	}
}