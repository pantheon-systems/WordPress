<?php

class WPML_Media_Translation_Status_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;

		return new WPML_Media_Translation_Status( $sitepress );
	}

}