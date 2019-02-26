<?php

class WPML_End_User_Notice_Validate {
	/** @var  WPML_End_User_Notice_Action_Execution */
	private $action_execution;

	/**
	 * @param WPML_End_User_Notice_Action_Execution $action_execution
	 */
	public function __construct( WPML_End_User_Notice_Action_Execution $action_execution ) {
		$this->action_execution = $action_execution;
	}

	/**
	 * @return bool
	 */
	public function is_valid( $user_id ) {
		if ( $this->action_execution->has_action_been_executed( $user_id ) ) {
			return false;
		}

		if ( $this->is_user_a_person_who_registered_wpml( $user_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param $user_id
	 *
	 * @return bool
	 */
	private function is_user_a_person_who_registered_wpml( $user_id ) {
		$registering_user_id = (int) WPML_Installer_Gateway::get_instance()->get_registering_user_id();
		return $registering_user_id === $user_id;
	}
}
