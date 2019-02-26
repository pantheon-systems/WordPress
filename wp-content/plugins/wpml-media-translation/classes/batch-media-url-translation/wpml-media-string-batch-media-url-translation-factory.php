<?php

/**
 * Class WPML_Media_String_Batch_Url_Translation
 */
class WPML_Media_String_Batch_Url_Translation_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $wpdb;

		if ( WPML_Media_String_Batch_Url_Translation::is_ajax_request() ) {
			$string_factory = new WPML_ST_String_Factory( $wpdb );

			return new WPML_Media_String_Batch_Url_Translation( $wpdb, $string_factory );
		}

		return null;
	}

}