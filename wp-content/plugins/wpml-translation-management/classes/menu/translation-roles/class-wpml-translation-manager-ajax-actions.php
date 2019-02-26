<?php

class WPML_Translation_Manager_Ajax extends WPML_Translation_Roles_Ajax {

	const TRANSLATION_MANAGER_INSTRUCTIONS_TEMPLATE = 'notification/translation-manager-instructions.twig';

	/** @var WPML_TM_Email_Notification_View $email_view */
	private $email_view;

	public function __construct(
		IWPML_Translation_Roles_View $view,
		WPML_Translation_Roles_Records $records,
		WPML_Super_Globals_Validation $post_vars,
		WPML_WP_User_Factory $user_factory,
		WPML_TM_Email_Notification_View $email_view
	) {
		parent::__construct( $view, $records, $post_vars, $user_factory );
		$this->email_view = $email_view;
	}

	public function get_role() {
		return 'manager';
	}

	public function get_nonce() {
		return WPML_Translation_Manager_Settings::NONCE_ACTION;
	}

	public function get_capability() {
		return WPML_Manage_Translations_Role::CAPABILITY;
	}

	public function get_user_row_template() {
		return 'translation-managers-row.twig';
	}

	public function on_user_created( WP_User $user ) {

	}

	public function send_instructions_to_user( WP_User $user ) {
		$site_name             = get_option( 'blogname' );
		$translation_setup_url = admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php' );
		$admin_user            = wp_get_current_user();

		$model = array(
			'setup_url'       => esc_url( $translation_setup_url ),
			'username'        => $user->display_name,
			'intro_message_1' => sprintf( __( 'You are the Translation Manager for %s. This role lets you manage everything related to translation for this site.', 'wpml-translation-management' ), $site_name ),
			'intro_message_2' => __( 'Before you can start sending content to translation, you need to complete a short setup.', 'wpml-translation-management' ),
			'setup'           => __( 'Set-up the translation', 'wpml-translation-management' ),
			'reminder'        => sprintf( __( '* Remember, your login name for %1$s is %2$s. If you need help with your password, use the password reset in the login page.', 'wpml-translation-management' ), $site_name, $user->user_login ),
			'at_your_service' => __( 'At your service', 'wpml-translation-management' ),
			'admin_name'      => $admin_user->display_name,
			'admin_for_site'  => sprintf( __( 'Administrator for %s', 'wpml-translation-management' ), $site_name ),
		);

		$to      = $user->display_name . ' <' . $user->user_email . '>';
		$message = $this->email_view->render_model( $model, self::TRANSLATION_MANAGER_INSTRUCTIONS_TEMPLATE );
		$subject = sprintf( __( 'You are now the Translation Manager for %s - action needed', 'wpml-translation-management' ), $site_name );

		$headers = array(
			'MIME-Version: 1.0',
			'Content-type: text/html; charset=UTF-8',
			'Reply-To: ' . $admin_user->display_name . ' <' . $admin_user->user_email . '>',
		);

		add_filter( 'wp_mail_from_name', array( $this, 'wp_mail_from_name_filter' ), 10, 1 );
		wp_mail( $to, $subject, $message, $headers );
		remove_filter( 'wp_mail_from_name', array( $this, 'wp_mail_from_name_filter' ), 10, 1 );

		return true;
	}

	public function wp_mail_from_name_filter( $from_name ) {
		$admin_user = wp_get_current_user();

		return $admin_user->display_name;
	}

}