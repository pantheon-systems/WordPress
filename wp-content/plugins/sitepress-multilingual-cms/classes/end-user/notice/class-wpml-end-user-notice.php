<?php

class WPML_End_User_Notice extends WPML_Notice {

	const NOTICE_GROUP = 'end-user-notice';

	/**
	 * @param int $user_id
	 */
	public function __construct( $user_id, WPML_Twig_Template $twig_service ) {
		$text = $twig_service->show(array(
			'header' => __( 'Need help with how to translate?', 'sitepress' ),
			'content_1' => __( 'Get personalized instructions for translating this site. You will also have access to WPML-users support.', 'sitepress' ),
			'content_2' => __( 'Registration is free and only takes a minute.', 'sitepress' ),
			'button_label' => __( 'Get translation instructions', 'sitepress' ),
			'confirm_message' => __( 'We are sending you to WPML.org to see your personalized translation instructions. If you need to access these instructions again, click on the "How to translate" button in the list of pages or in WPML\'s Translation Dashboard.', 'sitepress' ),
		), 'notice.twig');

		parent::__construct( $user_id, $text, self::NOTICE_GROUP );

		$this->set_css_class_types( 'info' );
		$this->set_dismissible( true );

		$this->add_display_callback( array( $this, 'belongs_to_current_user' ) );
	}

	/**
	 * @return bool
	 */
	public function belongs_to_current_user() {
		return get_current_user_id() === $this->get_id();
	}
}
