<?php

class WPML_TM_Translation_Services_Admin_Section_Ajax_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return WPML_TM_Translation_Services_Admin_Section_Ajax
	 */
	public function create() {
		$tp_client_factory = new WPML_TP_Client_Factory();
		$active_service_template_factory = new WPML_TM_Translation_Services_Admin_Active_Template_Factory();
		return new WPML_TM_Translation_Services_Admin_Section_Ajax( $tp_client_factory->create(), $active_service_template_factory );
	}
}