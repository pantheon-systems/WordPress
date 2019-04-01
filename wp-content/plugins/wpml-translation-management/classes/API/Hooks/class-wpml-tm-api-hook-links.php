<?php

/**
 * Class WPML_TM_API_Hook_Links
 *
 * This class provides various links by hooks
 */
class WPML_TM_API_Hook_Links implements IWPML_Action {

	public function add_hooks() {
		// TODO: Use WPML_API_Hook_Links::POST_TRANSLATION_SETTINGS_PRIORITY + 1 instead of the hardcoded 11.
		// It's done this way right now so there's no potential for an error if TM is updated before Core for
		// the minor 3.9.1 release
		add_filter( 'wpml_get_post_translation_settings_link', array(
			$this,
			'get_post_translation_settings_link'
		), 11, 1 );
	}

	public function get_post_translation_settings_link( $link ) {
		return admin_url( 'admin.php?page=' . WPML_TM_FOLDER . WPML_Translation_Management::PAGE_SLUG_SETTINGS . '&sm=mcsetup#icl_custom_posts_sync_options' );
	}
}