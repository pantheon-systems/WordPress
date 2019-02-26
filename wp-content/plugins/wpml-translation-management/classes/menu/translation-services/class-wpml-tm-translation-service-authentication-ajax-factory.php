<?php

class WPML_TM_Translation_Service_Authentication_Ajax_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return WPML_TM_Translation_Service_Authentication_Ajax
	 */
	public function create() {
		global $sitepress;

		$networking             = wpml_tm_load_tp_networking();
		$project_factory        = new WPML_TP_Project_Factory();
		$auth_factory           = new WPML_TP_Service_Authentication_Factory( $sitepress, $networking, $project_factory );
		$service_authentication = new WPML_TP_Service_Authentication_Ajax_Action(
			$auth_factory,
			isset( $_POST['custom_fields'] ) ? $_POST['custom_fields'] : null
		);
		$service_invalidation = new WPML_TP_Service_Invalidation_Ajax_Action( $auth_factory );

		return new WPML_TM_Translation_Service_Authentication_Ajax( $service_authentication, $service_invalidation );
	}
}