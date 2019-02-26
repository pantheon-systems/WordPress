<?php

/**
 * Class WPML_Media_Attachment_Image_Update_Factory
 */
class WPML_Media_Attachment_Image_Update_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $wpdb;

		return new WPML_Media_Attachment_Image_Update( $wpdb );
	}
}