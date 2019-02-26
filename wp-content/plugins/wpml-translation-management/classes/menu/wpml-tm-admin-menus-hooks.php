<?php

class WPML_TM_Admin_Menus_Hooks implements IWPML_Action {

	public function add_hooks() {
		add_action( 'init', array( $this, 'redirect_settings_menu' ) );
	}

	public function redirect_settings_menu() {
		if ( isset( $_GET['page'], $_GET['sm'] )
		     && WPML_TM_FOLDER . WPML_Translation_Management::PAGE_SLUG_MANAGEMENT === $_GET['page']
			 && in_array( $_GET['sm'], array( 'mcsetup', 'notifications', 'custom-xml-config' ), true )
		) {
			$query         = $_GET;
			$query['page'] = WPML_TM_FOLDER . WPML_Translation_Management::PAGE_SLUG_SETTINGS;
			wp_safe_redirect( add_query_arg( $query ) );
		}
	}
}
