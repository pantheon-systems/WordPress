<?php

/**
 * Class WPML_Jobs_Notification_Settings
 */
class WPML_User_Jobs_Notification_Settings {

	const BLOCK_NEW_NOTIFICATION_FIELD = 'wpml_block_new_email_notifications';

	public function add_hooks() {
		add_action( 'personal_options_update', array( $this, 'save_new_job_notifications_setting' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_new_job_notifications_setting' ) );

	}

	/**
	 * @param int $user_id
	 */
	public function save_new_job_notifications_setting( $user_id ) {
		$val = 1;
		if ( array_key_exists( self::BLOCK_NEW_NOTIFICATION_FIELD, $_POST ) ) {
			$val = filter_var( $_POST[self::BLOCK_NEW_NOTIFICATION_FIELD], FILTER_SANITIZE_NUMBER_INT );
		}
		update_user_meta( $user_id, self::BLOCK_NEW_NOTIFICATION_FIELD, $val );
	}

	public static function is_new_job_notification_enabled( $user_id ) {
		return ! get_user_meta( $user_id, self::BLOCK_NEW_NOTIFICATION_FIELD, true );
	}
}