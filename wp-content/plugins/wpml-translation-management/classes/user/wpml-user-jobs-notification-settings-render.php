<?php

class WPML_User_Jobs_Notification_Settings_Render {

	private $section_template;

	/**
	 * WPML_User_Jobs_Notification_Settings_Render constructor.
	 *
	 * @param WPML_User_Jobs_Notification_Settings_Template|null $notification_settings_template
	 */
	public function __construct( WPML_User_Jobs_Notification_Settings_Template $notification_settings_template ) {
		$this->section_template = $notification_settings_template;
	}

	public function add_hooks() {
		add_action( 'wpml_user_profile_options', array( $this, 'render_options' ) );
	}

	/**
	 * @param int $user_id
	 */
	public function render_options( $user_id ) {
		$field_checked = checked( true, WPML_User_Jobs_Notification_Settings::is_new_job_notification_enabled( $user_id ), false );
		echo $this->get_notification_template()->get_setting_section( $field_checked );
	}

	/**
	 * @return null|WPML_User_Jobs_Notification_Settings_Template
	 */
	private function get_notification_template() {
		return $this->section_template;
	}
}