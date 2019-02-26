<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_AMS_Synchronize_Actions_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return IWPML_Action|IWPML_Action[]|null
	 */
	public function create() {
		if ( WPML_TM_ATE_Status::is_enabled_and_activated() ) {
			$endpoints = new WPML_TM_ATE_AMS_Endpoints();
			$http      = new WP_Http();
			$auth      = new WPML_TM_ATE_Authentication();
			$ams_api   = new WPML_TM_AMS_API( $http, $auth, $endpoints );

			global $wpdb;
			$user_query_factory = new WPML_WP_User_Query_Factory();

			$translator_records            = new WPML_Translator_Records( $wpdb, $user_query_factory );
			$manager_records               = new WPML_Translation_Manager_Records( $wpdb, $user_query_factory );
			$admin_translators             = new WPML_Translator_Admin_Records( $wpdb, $user_query_factory );
			$user_records                  = new WPML_TM_AMS_Users( $manager_records, $translator_records, $admin_translators );
			$user_factory                  = new WPML_WP_User_Factory();
			$translator_activation_records = new WPML_TM_AMS_Translator_Activation_Records( new WPML_WP_User_Factory() );

			return new WPML_TM_AMS_Synchronize_Actions(
				$ams_api,
				$user_records,
				$user_factory,
				$translator_activation_records
			);
		}
	}
}