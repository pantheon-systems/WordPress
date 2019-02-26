<?php

class WPML_ST_Multisite_Filters_Cleaner_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader, IWPML_AJAX_Action_Loader {

	public function create() {
		return new WPML_ST_Multisite_Filters_Cleaner();
	}
}