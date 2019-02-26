<?php

abstract class WPML_Translation_Roles_Ajax extends WPML_TM_AJAX implements IWPML_Action {

	const USER_SEARCH_LIMIT = 10;

	/** @var IWPML_Translation_Roles_View $view */
	private $view;

	/** @var WPML_Translation_Roles_Records $records */
	private $records;

	/** @var WPML_Super_Globals_Validation $post_vars */
	protected $post_vars;

	/** @var WPML_WP_User_Factory $user_factory */
	private $user_factory;

	public function __construct(
		IWPML_Translation_Roles_View $view,
		WPML_Translation_Roles_Records $records,
		WPML_Super_Globals_Validation $post_vars,
		WPML_WP_User_Factory $user_factory
	) {
		$this->view         = $view;
		$this->records      = $records;
		$this->post_vars    = $post_vars;
		$this->user_factory = $user_factory;
	}

	public function add_hooks() {
		$role = $this->get_role();
		add_action( 'wp_ajax_wpml_remove_translation_' . $role, array( $this, 'remove_translation_role' ) );
		add_action( 'wp_ajax_wpml_search_translation_' . $role, array( $this, 'search_for_translation_roles' ) );
		add_action( 'wp_ajax_wpml_add_translation_' . $role, array( $this, 'add_translation_role' ) );
		add_action( 'wp_ajax_wpml_send_instructions_to_translation_' . $role, array( $this, 'send_instructions' ) );
	}

	public function remove_translation_role() {
		if ( $this->is_valid_request( $this->get_nonce() ) && $user = $this->get_user() ) {
			$user->remove_cap( $this->get_capability() );
			do_action( 'wpml_tm_ate_synchronize_' . $this->get_role() . 's' );
			wp_send_json_success();
		} else {
			wp_send_json_error( __( 'Could not find user!', 'wpml-translation-management' ) );
		}
	}

	public function search_for_translation_roles() {
		if ( $this->is_valid_request( $this->get_nonce() ) ) {
			$results = $this->records->search_for_users_without_capability( $this->post_vars->post( 'search' ), self::USER_SEARCH_LIMIT );

			wp_send_json_success( $results );
		}
	}

	public function add_translation_role() {
		if ( $this->is_valid_request( $this->get_nonce() ) && $user = $this->get_user() ) {

			if ( ! is_wp_error( $user ) ) {
				$user->add_cap( $this->get_capability() );

				$this->on_user_created( $user );

				$user->data->edit_link = esc_url( "user-edit.php?user_id={$user->ID}" );
				$user->data->avatar = get_avatar( $user->ID );

				$new_row = $this->view->show(
					array(
						'user' => (array) $user->data
					),
					$this->get_user_row_template()
				);

				do_action( 'wpml_tm_ate_synchronize_' . $this->get_role() . 's' );

				if ( $this->post_vars->post( 'sendEmail', FILTER_VALIDATE_BOOLEAN ) ) {
					$this->send_instructions_to_user( $user );
				}

				wp_send_json_success( $new_row );
			} else {
				wp_send_json_error( $user->get_error_message() );
			}
		} else {
			wp_send_json_error( __( 'Could not find user!', 'wpml-translation-management' ) );
		}
	}

	public function send_instructions() {
		if ( $this->is_valid_request( $this->get_nonce() ) && $user = $this->get_user() ) {
			if ( $this->send_instructions_to_user( $user ) ) {
				wp_send_json_success( sprintf( __( 'An email has been sent to %s', 'wpml-translation-management' ), $user->user_login ) );
			} else {
				wp_send_json_error( __( 'Failed to send email', 'wpml-translation-management' ) );
			}
		}
	}

	private function get_user() {
		$user_id = $this->post_vars->post( 'user_id', FILTER_SANITIZE_NUMBER_INT );
		if ( $user_id ) {
			return $this->user_factory->create( $user_id );
		} else {
			return $this->create_new_wp_user();
		}
	}

	/**
	 * @return null|WP_Error|WP_User
	 */
	private function create_new_wp_user() {
		$first_name = $this->post_vars->post( 'first' );
		$last_name  = $this->post_vars->post( 'last' );
		$email      = $this->post_vars->post( 'email', FILTER_SANITIZE_EMAIL );
		$user_name  = $this->post_vars->post( 'user' );
		$role       = $this->post_vars->post( 'role' );

		if ( $email && $user_name && $role ) {
			$user_id = wp_insert_user(
				array(
					'first_name' => $first_name,
					'last_name'  => $last_name,
					'user_email' => $email,
					'user_login' => $user_name,
					'role'       => $role,
					'user_pass'  => wp_generate_password(),
				)
			);

			if ( ! is_wp_error( $user_id ) ) {
				wp_send_new_user_notifications( $user_id );

				return $this->user_factory->create( $user_id );
			} else {
				return $user_id;
			}
		} else {
			return null;
		}
	}

	abstract public function get_role();
	abstract public function get_nonce();
	abstract public function get_capability();
	abstract public function get_user_row_template();
	abstract public function on_user_created( WP_User $user );
	abstract public function send_instructions_to_user( WP_User $user );
}