<?php

class WPML_Translation_Manager_Settings {

	const MAIN_TEMPLATE = 'translation-managers.twig';
	const NONCE_ACTION = 'wpml_translation_manager_actions';

	/** @var WPML_Translation_Manager_View $view */
	private $view;

	/** @var WPML_Translation_Manager_Records $records */
	private $records;

	public function __construct(
		WPML_Translation_Manager_View $view,
		WPML_Translation_Manager_Records $records
	) {
		$this->view    = $view;
		$this->records = $records;
	}

	public function render() {
		if ( current_user_can( 'manage_options' ) ) {
			echo $this->view->show( $this->get_model(), self::MAIN_TEMPLATE );
		}
	}

	public function get_model() {
		$current_user = wp_get_current_user();

		return array(
			'translation_managers' => $this->get_translation_managers(),
			'nonce'                => wp_create_nonce( self::NONCE_ACTION ),
			'user_id'              => $current_user->ID,
			'wp_roles'             => WPML_WP_Roles::get_editor_roles()
		);
	}

	private function get_translation_managers() {
		$users = $this->records->get_users_with_capability();

		foreach ( $users as $user ) {
			$user->edit_link = esc_url( add_query_arg( 'wp_http_referer',
				urlencode( esc_url( stripslashes( $_SERVER['REQUEST_URI'] ) ) ),
				"user-edit.php?user_id={$user->ID}" ) );
			$user->avatar    = get_avatar( $user->ID, 70 );
		}

		return $users;
	}
}

