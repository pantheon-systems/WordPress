<?php

class WPML_Media_Attachments_Duplication_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	public function create() {
		global $sitepress, $wpdb, $wpml_language_resolution;

		$sync_factory = new WPML_Element_Sync_Settings_Factory();

		if ( $sync_factory->create( WPML_Element_Sync_Settings_Factory::POST )->is_sync( 'attachment' ) ) {
			return new WPML_Media_Attachments_Duplication(
				$sitepress,
				new WPML_Model_Attachments( $sitepress, wpml_get_post_status_helper() ),
				$wpdb,
				$wpml_language_resolution
			);
		}

		return null;
	}
}