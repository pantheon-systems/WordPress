<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Custom_XML extends WPML_WP_Option {

	public function get_key() {
		return 'wpml-tm-custom-xml';
	}

	public function get_default() {
		return '';
	}
}
