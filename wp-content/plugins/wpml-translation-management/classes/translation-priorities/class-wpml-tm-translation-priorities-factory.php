<?php

/**
 * Class WPML_TM_Translation_Priorities_Factory
 *
 */
class WPML_TM_Translation_Priorities_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;

		return new WPML_TM_Translation_Priorities_Register_Action( $sitepress );
	}

}