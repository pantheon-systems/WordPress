<?php

class WPML_TM_REST_AMS_Clients_Factory extends WPML_REST_Factory_Loader {

	public function create() {
		global $wpdb;

		$endpoints                     = new WPML_TM_ATE_AMS_Endpoints();
		$user_query_factory            = new WPML_WP_User_Query_Factory();
		$translators                   = new WPML_Translator_Records( $wpdb, $user_query_factory );
		$admin_translators             = new WPML_Translator_Admin_Records( $wpdb, $user_query_factory );
		$managers                      = new WPML_Translation_Manager_Records( $wpdb, $user_query_factory );
		$users                         = new WPML_TM_AMS_Users( $managers, $translators, $admin_translators );
		$http                          = new WP_Http();
		$auth                          = new WPML_TM_ATE_Authentication();
		$api                           = new WPML_TM_AMS_API( $http, $auth, $endpoints );
		$translator_activation_records = new WPML_TM_AMS_Translator_Activation_Records( new WPML_WP_User_Factory() );
		$strings                       = new WPML_TM_MCS_ATE_Strings( $auth, $endpoints );

		return new WPML_TM_REST_AMS_Clients( $api, $users, $translator_activation_records, $strings );
	}
}
