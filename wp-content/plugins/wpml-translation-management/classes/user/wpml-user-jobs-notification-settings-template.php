<?php

/**
 * Class WPML_User_Jobs_Notification_Settings_Template
 */
class WPML_User_Jobs_Notification_Settings_Template {

	const TEMPLATE_FILE = 'job-email-notification.twig';

	/**
	 * @var WPML_Twig_Template
	 */
	private $template_service;

	/**
	 * WPML_User_Jobs_Notification_Settings_Template constructor.
	 *
	 * @param IWPML_Template_Service $template_service
	 */
	public function __construct( IWPML_Template_Service $template_service ) {
		$this->template_service = $template_service;
	}

	/**
	 * @param string $notification_input
	 *
	 * @return string
	 */
	public function get_setting_section( $notification_input ) {
		$model = $this->get_model( $notification_input );

		return $this->template_service->show( $model, self::TEMPLATE_FILE );
	}

	/**
	 * @param string $notification_input
	 *
	 * @return array
	 */
	private function get_model( $notification_input ) {
		$model = array(
			'strings' => array(
				'section_title' => __( 'WPML Translator Settings', 'wpml-translation-management' ),
				'field_title' => __( 'Notification emails:', 'wpml-translation-management' ),
				'field_name' => WPML_User_Jobs_Notification_Settings::BLOCK_NEW_NOTIFICATION_FIELD,
				'field_text' => __( 'Send me a notification email when there is something new to translate', 'wpml-translation-management' ),
				'checked' => $notification_input,
			),
		);

		return $model;
	}
}