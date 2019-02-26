<?php

class WPML_Wizard_Fetch_Content_Action implements IWPML_Action {

	const AJAX_ACTION = 'wpml_wizard_fetch_content';

	public function add_hooks() {
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'fetch_content' ) );
	}

	public function fetch_content() {
		$content = apply_filters( 'wpml_wizard_fetch_' . $_POST['step_slug'], '' );
		wp_send_json_success( $content );
	}
}