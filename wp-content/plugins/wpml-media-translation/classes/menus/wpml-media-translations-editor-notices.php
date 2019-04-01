<?php

/**
 * Class WPML_Media_Editor_Notices
 */
class WPML_Media_Editor_Notices implements IWPML_Action {
	const TEXT_EDIT_NOTICE_DISMISSED = '_wpml_media_editor_text_edit_notice_dismissed';

	public function add_hooks() {
		add_action( 'wp_ajax_wpml_media_editor_text_edit_notice_dismissed', array( $this, 'dismiss_texts_change_notice' ) );
	}

	public function dismiss_texts_change_notice() {
		update_user_meta( get_current_user_id(), self::TEXT_EDIT_NOTICE_DISMISSED, 1 );
		wp_send_json_success();
	}

}
