<?php

/**
 * @author OnTheGo Systems
 *
 * NOTE: This uses the Frontend loader because is_admin() returns false during wp_login
 */
class WPML_TM_ATE_Translator_Login_Factory implements IWPML_Frontend_Action_Loader {

	public function create() {
		global $wpdb;

		if ( WPML_TM_ATE_Status::is_enabled_and_activated() ) {
			return new WPML_TM_ATE_Translator_Login(
				new WPML_TM_AMS_Translator_Activation_Records( new WPML_WP_User_Factory() ),
				new WPML_Translator_Records( $wpdb, new WPML_WP_User_Query_Factory() ),
				new WPML_TM_AMS_API( new WP_Http(), new WPML_TM_ATE_Authentication(), new WPML_TM_ATE_AMS_Endpoints() )
			);
		}
	}

}
