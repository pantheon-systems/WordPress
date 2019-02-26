<?php

class WPML_TM_Translation_Services_Refresh_Services_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return WPML_TM_Translation_Services_Refresh
	 */
	public function create() {
		$hooks = null;

		if ( $this->is_visible() ) {
			$template_service = new WPML_Twig_Template_Loader( array( WPML_TM_PATH . '/templates/menus/translation-services' ) );
			$tp_client_factory = new WPML_TP_Client_Factory();
			$tp_client = $tp_client_factory->create();
			$hooks = new WPML_TM_Translation_Services_Refresh( $template_service->get_template(), $tp_client->services() );
		}

		return $hooks;
	}

	/**
	 * @return string
	 */
	private function is_visible() {
		return ( isset( $_GET['page'] ) && 'sitepress-multilingual-cms/menu/troubleshooting.php' === $_GET['page'] ) ||
		       ( isset( $_POST['action'] ) && WPML_TM_Translation_Services_Refresh::AJAX_ACTION === $_POST['action'] );
	}
}