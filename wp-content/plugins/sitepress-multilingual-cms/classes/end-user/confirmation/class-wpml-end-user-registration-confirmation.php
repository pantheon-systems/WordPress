<?php

class WPML_End_User_Registration_Confirmation implements IWPML_Action {
	/** @var WPML_End_User_Confirmation_Auth */
	private $auth;

	/** @var  WPML_End_User_Notice_Action_Execution */
	private $action_execution;

	/**
	 * @param WPML_End_User_Confirmation_Auth $auth
	 * @param WPML_End_User_Notice_Action_Execution $action_execution
	 */
	public function __construct(
		WPML_End_User_Confirmation_Auth $auth,
		WPML_End_User_Notice_Action_Execution $action_execution
	) {
		$this->auth = $auth;
		$this->action_execution = $action_execution;
	}


	public function add_hooks() {
		add_action( 'wp_ajax_nopriv_confirm_end_user_registration', array( $this, 'confirm_end_user_registration' ), 10, 0 );
		add_action( 'wp_ajax_confirm_end_user_registration', array( $this, 'confirm_end_user_registration' ), 10, 0 );
	}

	public function confirm_end_user_registration() {
		try {
			$data = $this->get_data();
			if ( ! $this->auth->is_valid( $data ) ) {
				throw new InvalidArgumentException( 'Authentication failed.', 403 );
			}

			$this->action_execution->mark_action_as_executed( $data->get_user_id() );
			wp_send_json_success();
		} catch ( InvalidArgumentException $e ) {
			wp_send_json_error( $e->getMessage(), $e->getCode() );
		}
	}

	/**
	 * @throws InvalidArgumentException
	 * @return WPML_End_User_Confirmation_Auth_Data
	 */
	private function get_data() {
		$fields = array( 'site_key', 'user' );
		foreach ( $fields as $field ) {
			if ( ! isset( $_POST[ $field ] ) ) {
				throw new InvalidArgumentException( "Field $field is missing.", 406 );
			}
		}

		return new WPML_End_User_Confirmation_Auth_Data(
			$_POST['site_key'], $_POST['user']
		);
	}
}
