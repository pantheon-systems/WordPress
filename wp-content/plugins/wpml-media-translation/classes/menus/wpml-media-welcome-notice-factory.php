<?php

class WPML_Media_Welcome_Notice_Factory implements IWPML_Backend_Action_Loader {
	const DISMISSED = 'dismissed';
	const USER_META = 'wpml-media-welcome-message';

	public function create() {
		global $sitepress;
		if ( current_user_can( 'manage_translations' ) && ! $this->dismissed() && $sitepress->is_setup_complete() && WPML_Media::has_setup_run() ) {
			return new WPML_Media_Welcome_Notice( $sitepress->get_wp_api()->is_tm_page( 'dashboard' ) );
		}

		return null;
	}

	private function dismissed() {
		$meta = get_user_meta( get_current_user_id(), self::USER_META, true );

		return isset( $meta['status'] ) && self::DISMISSED === $meta['status'];
	}

}