<?php

class WPML_End_User_Account_Creation_Disabled implements IWPML_Action {

	const NONCE = 'wpml-end-user-disabling-option';

	/** @var  WPML_End_User_Account_Creation_Disabled_Option */
	private $disabling_option;

	/**
	 * @param WPML_End_User_Account_Creation_Disabled_Option $disabling_option
	 */
	public function __construct( WPML_End_User_Account_Creation_Disabled_Option $disabling_option ) {
		$this->disabling_option = $disabling_option;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_wpml_end_user_get_info', array( $this, 'set_option_value' ), 10, 0 );
	}

	public function set_option_value() {
		$nonce = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : false;
		if ( ! wp_verify_nonce( $nonce, self::NONCE ) ) {
			return wp_send_json_error( null, 403 );
		}

		$value = (bool) $_POST['value'];
		$this->disabling_option->set_option( $value );

		wp_send_json_success();
	}
}
