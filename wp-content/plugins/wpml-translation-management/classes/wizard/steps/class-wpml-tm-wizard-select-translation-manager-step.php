<?php

class WPML_TM_Wizard_Select_Translation_Manager_Step extends WPML_Twig_Template_Loader {

	private $model = array();

	public function __construct() {
		parent::__construct( array(
				WPML_TM_PATH . '/templates/wizard',
				WPML_TM_PATH . '/templates/menus/translation-managers',
			)
		);
	}

	public function render() {
		$this->add_strings();
		$this->add_roles();
		$this->add_nonce();
		$this->add_user_id();

		return $this->get_template()->show( $this->model, 'select-translation-manager-step.twig' );
	}

	public function add_strings() {

		$skip_url = add_query_arg(
			array(
				'page'        => WPML_TM_FOLDER . '/menu/main.php',
				'sm' => 'dashboard',
				'skip_wizard' => 1,
			),
			admin_url( '/admin.php' )
		);

		$this->model['strings'] = WPML_Translation_Manager_View::get_strings();
		$this->model['strings'] = array_merge( $this->model['strings'],
			array(
				'title'                  => __( 'Select the Translation Manager for this site', 'wpml-translation-management' ),
				'intro'                  => __( 'The Translation Manager will be responsible for the ongoing translation of the site.', 'wpml-translation-management' ),
				'role_includes_heading'  => __( 'This role includes:', 'wpml-translation-management' ),
				'role_tasks'             => array(
					__( 'Choosing the translation method of the site and setting up translators', 'wpml-translation-management' ),
					__( 'Choosing which content to translate', 'wpml-translation-management' ),
					__( 'Communicating with translators', 'wpml-translation-management' ),
					__( 'Receiving email notifications when translations complete', 'wpml-translation-management' ),
					__( 'Receiving notifications when there are issues with translations', 'wpml-translation-management' ),
				),
				'intro_2'                => __( "If you are building this site for a client, it's better to wait until you deliver the site. Then, set your client as the site's Translation Manager. As the site's admin, you can translate everything without completing this wizard.", 'wpml-translation-management' ),
				'use_myself'             => __( 'Make myself the site’s Translation Manager', 'wpml-translation-management' ),
				'myself_warning'         => __( 'Often not recommended', 'wpml-translation-management' ),
				'myself_warning_tooltip' => __( "If you are building this site for a client, it's a lot better to make your client the Translation Manager.", 'wpml-translation-management' ),
				'button_text'            => __( 'Set Translation Manager', 'wpml-translation-management' ),
				'section_heading'        => __( 'Set Translation Manager', 'wpml-translation-management' ),
				'create_heading'         => __( 'Create a new user to be the Translation Manager:', 'wpml-translation-management' ),
				'go_back'                => __( 'I changed my mind', 'wpml-translation-management' ),
				'existing_heading'       => __( 'Select an existing WordPress user (needs to be an editor or above):', 'wpml-translation-management' ),
				'skip_setup'             => __( 'Skip this setup', 'wpml-translation-management' ),
				'skip_url'               => $skip_url,
				'privacy_info'                => __( 'Translation Management will send out information about the person who manages translations, the translators, and the translated content.', 'wpml-translation-management' ),
			)
		);
	}

	private function add_roles() {
		$this->model['wp_roles'] = WPML_WP_Roles::get_editor_roles();
	}

	private function add_nonce() {
		$this->model['nonce'] = wp_create_nonce( WPML_Translation_Manager_Settings::NONCE_ACTION );
	}

	private function add_user_id() {
		$this->model['user_id'] = get_current_user_id();
	}

}