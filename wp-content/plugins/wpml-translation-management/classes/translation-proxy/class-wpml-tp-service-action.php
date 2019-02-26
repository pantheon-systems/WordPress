<?php

class WPML_TP_Service_Action extends WPML_SP_User {

	/**
	 * Gets the current translation service
	 *
	 * @return bool|stdClass
	 */
	protected function get_current_service() {

		return $this->sitepress->get_setting( 'translation_service' );
	}

	/**
	 * Saves the input service as the current translation service setting.
	 *
	 * @param stdClass $service
	 */
	protected function set_current_service( $service ) {
		do_action( 'wpml_tm_before_set_translation_service', $service );
		$this->sitepress->set_setting( 'translation_service', $service, true );
	}
}
