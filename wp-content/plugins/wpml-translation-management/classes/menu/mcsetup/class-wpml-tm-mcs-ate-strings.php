<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_MCS_ATE_Strings {

	const AMS_STATUS_ACTIVE_NOT_ALL_SUBSCRIBED = 'active-not-all-subscribed';
	/**
	 * @var WPML_TM_ATE_Authentication
	 */
	private $authentication;
	private $authentication_data;
	/**
	 * @var WPML_TM_ATE_AMS_Endpoints
	 */
	private $endpoints;
	private $statuses;

	/**
	 * WPML_TM_MCS_ATE constructor.
	 *
	 * @param WPML_TM_ATE_Authentication $authentication
	 * @param WPML_TM_ATE_AMS_Endpoints  $endpoints
	 */
	public function __construct( WPML_TM_ATE_Authentication $authentication, WPML_TM_ATE_AMS_Endpoints $endpoints ) {
		$this->authentication = $authentication;
		$this->endpoints      = $endpoints;

		$this->authentication_data = get_option( WPML_TM_ATE_Authentication::AMS_DATA_KEY, array() );

		$ate_console_link = $this->get_console_link();

		$this->statuses = array(
			WPML_TM_ATE_Authentication::AMS_STATUS_NON_ACTIVE => array(
				'type'   => 'error',
				'message'   => array(
					'status' => __( 'Advanced Translation Editor is not active yet', 'wpml-translation-management' ),
					'text'   => __( 'Request activation to receive an email with directions to activate the service.',
					                'wpml-translation-management' ),
				),
				'button' => __( 'Request activation', 'wpml-translation-management' ),
			),
			WPML_TM_ATE_Authentication::AMS_STATUS_ENABLED => array(
				'type'   => 'info',
				'message'   => array(
					'status' => __( 'Advanced Translation Editor is being activated', 'wpml-translation-management' ),
					'text'   => '',
				),
				'button' => '',
			),
			WPML_TM_ATE_Authentication::AMS_STATUS_ACTIVE  => array(
				'type'   => 'success',
				'message'   => array(
					'status' => __( 'Advanced Translation Editor is enabled and active', 'wpml-translation-management' ),
					'text'   => $ate_console_link,
				),
				'button' => __( 'Advanced Translation Editor is active', 'wpml-translation-management' ),
			),
			self::AMS_STATUS_ACTIVE_NOT_ALL_SUBSCRIBED     => array(
				'type'   => 'success',
				'message'   => array(
					'status' => __( "WPML's Advanced Translation Editor is enabled, but not all your translators can use it.", 'wpml-translation-management' ),
					'text'   => $ate_console_link,
				),
				'button' => __( 'Advanced Translation Editor is active', 'wpml-translation-management' ),
			),
		);
	}

	/**
	 * @return string|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function get_auto_login() {
		$shared = null;
		if ( array_key_exists( 'shared', $this->authentication_data ) ) {
			$shared = $this->authentication_data['shared'];
		}

		if ( $shared ) {
			$url = $this->endpoints->get_ams_auto_login();

			$user = wp_get_current_user();

			$user_email = $user->user_email;

			return $this->authentication->get_signed_url(
				'GET',
				$url,
				array(
					'translation_manager' => $user_email,
					'return_url' => WPML_TM_Page::get_translators_url( array( 'refresh_subscriptions' => '1' ) ),
				)
			);
		}

		return '#';
	}

	public function get_status_HTML( $status, $all_users_have_subscription = true ) {
		if ( $status === WPML_TM_ATE_Authentication::AMS_STATUS_ACTIVE && ! $all_users_have_subscription ) {
			$status = self::AMS_STATUS_ACTIVE_NOT_ALL_SUBSCRIBED;
		}
		$message = $this->get_status_attribute( $status, 'message' );

		return '<strong>' . $message['status'] . '</strong> ' . $message['text'];
	}

	/**
	 * @return string
	 */
	private function get_console_link() {

		$ate_console_link = '';
		if ( current_user_can( WPML_Manage_Translations_Role::CAPABILITY )
		     || $this->is_authenticated_user() ) {
			$ate_console_link_text = __( "Manage translators' access", 'wpml-translation-management' );
			$ate_console_link_url  = $this->get_auto_login();
			$ate_console_link      = '<a class="wpml-external-link js-ate-console" href="'
			                         . $ate_console_link_url
			                         . '" target="_blank">'
			                         . $ate_console_link_text
			                         . '</a>';

		}

		return $ate_console_link;
	}

	/**
	 * @return bool
	 */
	private function is_authenticated_user() {
		$authenticated_user_id = 0;
		if ( isset( $this->authentication_data['user_id'] ) ) {
			$authenticated_user_id = $this->authentication_data['user_id'];
		}
		return get_current_user_id() === $authenticated_user_id;
	}

	/**
	 * @return string
	 */
	public function get_status() {
		$ate_status = WPML_TM_ATE_Authentication::AMS_STATUS_NON_ACTIVE;
		if ( array_key_exists( 'status', $this->authentication_data ) ) {
			$ate_status = $this->authentication_data['status'];
		}

		return $ate_status;
	}

	/**
	 * @param string     $attribute
	 * @param null|mixed $default
	 *
	 * @return mixed
	 */
	public function get_current_status_attribute( $attribute, $default = null ) {
		return $this->get_status_attribute( $this->get_status(), $attribute, $default );
	}

	/**
	 * @param string $status
	 * @param string $attribute
	 * @param null|mixed $default
	 *
	 * @return mixed
	 */
	public function get_status_attribute( $status, $attribute, $default = null ) {
		$status_attributes = $this->statuses[ $status ];

		if ( array_key_exists( $attribute, $status_attributes ) ) {
			return $status_attributes[ $attribute ];
		}

		return $default;
	}

	public function get_statuses() {
		return $this->statuses;
	}

	public function get_synchronize_button_text() {
		return __( 'Synchronize translators and translation managers', 'wpml-translation-management' );
	}
}
