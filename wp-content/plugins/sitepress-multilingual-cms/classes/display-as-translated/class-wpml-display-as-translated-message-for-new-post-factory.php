<?php

class WPML_Display_As_Translated_Message_For_New_Post_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $pagenow, $sitepress;

		$notices = wpml_get_admin_notices();

		if ( 'post-new.php' === $pagenow ) {
			return new WPML_Display_As_Translated_Message_For_New_Post( $sitepress, $notices );
		} else {
			$notices->remove_notice( WPML_Notices::DEFAULT_GROUP, 'WPML_Display_As_Translated_Message_For_New_Post' );
			return null;
		}
	}

}
