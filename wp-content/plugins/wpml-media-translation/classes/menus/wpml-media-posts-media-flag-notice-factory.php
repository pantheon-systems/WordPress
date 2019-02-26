<?php

class WPML_Media_Posts_Media_Flag_Notice_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;

		if ( current_user_can( 'manage_options' ) && ! WPML_Media::has_setup_run() && $sitepress->is_setup_complete() ) {
			return new WPML_Media_Posts_Media_Flag_Notice( $sitepress );
		}

		return null;
	}

}