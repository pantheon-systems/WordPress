<?php

class WPML_URL_HTTP_Referer_Factory {

	/**
	 * @return WPML_URL_HTTP_Referer
	 */
	public function create() {
		return new WPML_URL_HTTP_Referer( new WPML_Rest( new WP_Http() ) );
	}
}