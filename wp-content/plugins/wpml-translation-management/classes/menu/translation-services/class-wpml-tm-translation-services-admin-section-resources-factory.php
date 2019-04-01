<?php

class WPML_TM_Translation_Services_Admin_Section_Resources_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return WPML_TM_Translation_Services_Admin_Section_Resources
	 */
	public function create() {
		return new WPML_TM_Translation_Services_Admin_Section_Resources();
	}
}