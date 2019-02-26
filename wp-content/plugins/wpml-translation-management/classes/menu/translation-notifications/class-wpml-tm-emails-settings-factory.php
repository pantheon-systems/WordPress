<?php

class WPML_TM_Emails_Settings_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return WPML_TM_Emails_Settings
	 */
	public function create() {
		global $iclTranslationManagement;

		$hooks = null;

		if ( $this->is_tm_settings_page() ) {
			$template_service = new WPML_Twig_Template_Loader( array( WPML_TM_PATH . '/templates/settings' ) );
			$hooks = new WPML_TM_Emails_Settings( $template_service->get_template(), $iclTranslationManagement );
		}

		return $hooks;
	}

	private function is_tm_settings_page() {
		return isset( $_GET['page'] )
			&& WPML_TM_FOLDER . WPML_Translation_Management::PAGE_SLUG_SETTINGS === filter_var( $_GET['page'], FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	}
}