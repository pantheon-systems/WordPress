<?php

/**
 * @author OnTheGo Systems
 */
class WPML_ST_Privacy_Content extends WPML_Privacy_Content {

	/**
	 * @return string
	 */
	protected function get_plugin_name() {
		return 'WPML String Translation';
	}

	/**
	 * @return string|array
	 */
	protected function get_privacy_policy() {
		return __( 'WPML String Translation will send all strings to WPML’s Advanced Translation Editor and to the translation services which are used.', 'wpml-string-translation' );
	}

}