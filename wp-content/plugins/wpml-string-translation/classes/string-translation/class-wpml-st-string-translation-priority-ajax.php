<?php

class WPML_ST_String_Translation_Priority_AJAX implements IWPML_Action{

	/** @var wpdb */
	private $wpdb;

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb) {
		$this->wpdb = $wpdb;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_wpml_change_string_translation_priority', array( $this, 'change_string_translation_priority' ) );
	}

	public function change_string_translation_priority() {

		if ( $this->verify_ajax( 'wpml_change_string_translation_priority_nonce' ) ) {

			$change_string_translation_priority_dialog = new WPML_Strings_Translation_Priority( $this->wpdb );

			$string_ids = array_map( 'intval', $_POST['strings'] );
			$priority   = filter_var( isset( $_POST['priority'] ) ? $_POST['priority'] : '', FILTER_SANITIZE_SPECIAL_CHARS );
			$change_string_translation_priority_dialog->change_translation_priority_of_strings( $string_ids, $priority );

			wp_send_json_success();
		}
	}

	private function verify_ajax( $ajax_action ) {
		return isset( $_POST['wpnonce'] ) && wp_verify_nonce( $_POST['wpnonce'], $ajax_action );
	}
}