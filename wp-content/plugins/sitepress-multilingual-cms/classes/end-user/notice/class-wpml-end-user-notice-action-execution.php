<?php

class WPML_End_User_Notice_Action_Execution {

	const OPTION_NAME = 'end-user-notice-action-executed';

	/**
	 * @param $user_id
	 */
	public function mark_action_as_executed( $user_id ) {
		update_user_meta( $user_id, self::OPTION_NAME, true );
	}

	/**
	 * @param $user_id
	 * @return bool
	 */
	public function has_action_been_executed( $user_id ) {
		return (bool) get_user_meta( $user_id, self::OPTION_NAME, true );
	}
}
