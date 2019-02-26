<?php

class WPML_Display_As_Translated_Attachments_Query_Factory implements IWPML_Frontend_Action_Loader {

	public function create() {
		global $sitepress, $wpml_post_translations;

		return new WPML_Display_As_Translated_Attachments_Query( $sitepress, $wpml_post_translations );
	}
}