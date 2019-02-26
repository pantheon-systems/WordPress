<?php

class WPML_TM_Admin_Menus_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		if ( isset( $_GET['page'], $_GET['sm'] ) ) {
			return new WPML_TM_Admin_Menus_Hooks();
		}
	}
}