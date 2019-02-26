<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Media_Privacy_Content extends WPML_Privacy_Content {

	/**
	 * @return string
	 */
	protected function get_plugin_name() {
		return 'WPML Media Translation';
	}

	/**
	 * @return string|array
	 */
	protected function get_privacy_policy() {
		return __( 'WPML Media Translation will send the email address and name of each manager and assigned translator as well as the content itself to Advanced Translation Editor and to the translation services which are used.', 'wpml-media' );
	}

}