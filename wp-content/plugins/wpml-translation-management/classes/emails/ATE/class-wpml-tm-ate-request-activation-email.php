<?php

class WPML_TM_ATE_Request_Activation_Email {

	const REQUEST_ACTIVATION_TEMPLATE = 'notification/request-ate-activation.twig';

	/** @var WPML_TM_Email_Notification_View */
	private $email_view;

	public function __construct( WPML_TM_Email_Notification_View $email_view ) {
		$this->email_view = $email_view;
	}

	public function send_email( $to_manager, $from_user ) {
		$site_name           = get_option( 'blogname' );
		$translators_tab_url = WPML_TM_Page::get_translators_url();

		$model = array(
			'setup_url'       => esc_url( $translators_tab_url ),
			'casual_name'     => $to_manager->user_firstname,
			'username'        => $to_manager->display_name,
			'intro_message_1' => sprintf( __( 'The translator %1$s is requesting to use the Advanced Translation Editor on site %2$s.', 'wpml-translation-management' ), $from_user->display_name, $site_name ),
			'setup'           => __( "Manage translators' access", 'wpml-translation-management' ),
			'reminder'        => sprintf( __( '* Remember, your login name for %1$s is %2$s. If you need help with your password, use the password reset in the login page.', 'wpml-translation-management' ), $site_name, $to_manager->user_login ),
		);

		$to      = $to_manager->display_name . ' <' . $to_manager->user_email . '>';
		$message = $this->email_view->render_model( $model, self::REQUEST_ACTIVATION_TEMPLATE );
		$subject = sprintf( __( "Request to use WPML's Advanced Translation Editor by %s", 'wpml-translation-management' ), $site_name );

		$headers = array(
			'MIME-Version: 1.0',
			'Content-type: text/html; charset=UTF-8',
		);

		return wp_mail( $to, $subject, $message, $headers );
	}

}
