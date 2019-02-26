<?php

class WPML_Absolute_Url_Persisted_Filters_Factory implements IWPML_Backend_Action_Loader, IWPML_AJAX_Action_Loader {

	public function create() {
		$url_persisted = WPML_Absolute_Url_Persisted::get_instance();

		if ( $url_persisted->has_urls() ) {
			return new WPML_Absolute_Url_Persisted_Filters( $url_persisted );
		}

		return null;
	}
}
