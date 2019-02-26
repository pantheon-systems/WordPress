<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_AMS_API {

	const HTTP_ERROR_CODE_400 = 400;

	private $auth;
	private $endpoints;
	private $wp_http;

	/**
	 * WPML_TM_ATE_API constructor.
	 *
	 * @param WP_Http                    $wp_http
	 * @param WPML_TM_ATE_Authentication $auth
	 * @param WPML_TM_ATE_AMS_Endpoints  $endpoints
	 */
	public function __construct(
		WP_Http $wp_http,
		WPML_TM_ATE_Authentication $auth,
		WPML_TM_ATE_AMS_Endpoints $endpoints
	) {
		$this->wp_http   = $wp_http;
		$this->auth      = $auth;
		$this->endpoints = $endpoints;
	}

	/**
	 * @param string $translator_email
	 *
	 * @return array|mixed|null|object|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function enable_subscription( $translator_email ) {
		$result = null;

		$verb = 'PUT';
		$url  = $this->endpoints->get_enable_subscription();
		$url  = str_replace( '{translator_email}', base64_encode( $translator_email ), $url );

		$response = $this->signed_request( $verb, $url );

		if ( $this->response_has_body( $response ) ) {

			$result = $this->get_errors( $response );

			if ( ! is_wp_error( $result ) ) {
				$result = json_decode( $response['body'], true );
			}
		}

		return $result;
	}

	/**
	 * @param string $translator_email
	 *
	 * @return bool|WP_Error
	 */
	public function is_subscription_activated( $translator_email ) {
		$result = null;

		$url = $this->endpoints->get_subscription_status();

		$url = str_replace( '{translator_email}', base64_encode( $translator_email ), $url );
		$url = str_replace( '{WEBSITE_UUID}', wpml_get_site_id(), $url );

		$response = $this->signed_request( 'GET', $url );

		if ( $this->response_has_body( $response ) ) {

			$result = $this->get_errors( $response );

			if ( ! is_wp_error( $result ) ) {
				$result = json_decode( $response['body'], true );
				$result = $result['subscription'];
			}
		}

		return $result;
	}

	/**
	 * @return array|mixed|null|object|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function get_status() {
		$result = null;

		$registration_data = $this->get_registration_data();
		$shared            = array_key_exists( 'shared', $registration_data ) ? $registration_data['shared'] : null;

		if ( $shared ) {
			$url = $this->endpoints->get_ams_status();
			$url = str_replace( '{SHARED_KEY}', $shared, $url );

			$response = $this->request( 'GET', $url );

			if ( $this->response_has_body( $response ) ) {
				$response_body = json_decode( $response['body'], true );

				$result = $this->get_errors( $response );

				if ( ! is_wp_error( $result ) ) {
					$registration_data = $this->get_registration_data();
					if ( (bool) $response_body['activated'] ) {
						$registration_data['status'] = WPML_TM_ATE_Authentication::AMS_STATUS_ACTIVE;
						$this->set_registration_data( $registration_data );
					}
					$result = $response_body;
				}
			}
		}

		return $result;
	}

	/**
	 * @param int     $manager_id
	 * @param WP_User $manager
	 * @param array   $translators
	 * @param array   $managers
	 *
	 * @return array|bool|null|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function register_manager( $manager_id, WP_User $manager, array $translators, array $managers ) {

		$manager_data     = $this->get_user_data( $manager, true );
		$translators_data = $this->get_users_data( $translators );
		$managers_data    = $this->get_users_data( $managers, true );

		$result = null;

		if ( $manager_data ) {
			$url = $this->endpoints->get_ams_register_client();

			$params                 = $manager_data;
			$params['website_url']  = get_site_url();
			$params['website_uuid'] = wpml_get_site_id();

			$params['translators']          = $translators_data;
			$params['translation_managers'] = $managers_data;

			$response = $this->request( 'POST', $url, $params );

			if ( $this->response_has_body( $response ) ) {
				$response_body = json_decode( $response['body'], true );

				$result = $this->get_errors( $response );

				if ( ! is_wp_error( $result ) && $this->response_has_keys( $response ) ) {

					$registration_data = $this->get_registration_data();

					$registration_data['user_id'] = $manager_id;
					$registration_data['secret']  = $response_body['secret_key'];
					$registration_data['shared']  = $response_body['shared_key'];
					$registration_data['status']  = WPML_TM_ATE_Authentication::AMS_STATUS_ENABLED;

					$result = $this->set_registration_data( $registration_data );
				}
			}
		}

		return $result;
	}

	/**
	 * @param WP_User $wp_user
	 *
	 * @param bool    $with_name_details
	 *
	 * @return array
	 */
	private function get_user_data( WP_User $wp_user, $with_name_details = false ) {
		$data = array();

		$data['email'] = $wp_user->user_email;

		if ( $with_name_details ) {
			$data['display_name'] = $wp_user->display_name;
			$data['first_name']   = $wp_user->first_name;
			$data['last_name']    = $wp_user->last_name;
		} else {
			$data['name'] = $wp_user->display_name;
		}

		return $data;
	}

	/**
	 * @param array $users
	 * @param bool  $with_name_details
	 *
	 * @return array
	 */
	private function get_users_data( array $users, $with_name_details = false ) {
		$user_data = array();

		foreach ( $users as $user ) {
			$wp_user     = get_user_by( 'id', $user->ID );
			$user_data[] = $this->get_user_data( $wp_user, $with_name_details );
		}

		return $user_data;
	}

	/**
	 * @param $response
	 *
	 * @return bool
	 */
	private function response_has_body( $response ) {
		return ! is_wp_error( $response ) && array_key_exists( 'body', $response );
	}

	private function get_errors( array $response ) {
		$response_errors = null;

		if ( is_wp_error( $response ) ) {
			$response_errors = $response;
		} elseif ( array_key_exists( 'body', $response ) && $response['response']['code'] >= self::HTTP_ERROR_CODE_400 ) {
			$main_error    = array();
			$errors        = array();
			$error_message = $response['response']['message'];

			$response_body = json_decode( $response['body'], true );
			if ( ! $response_body ) {
				$error_message = $response['body'];
				$main_error    = array( $response['body'] );
			} elseif ( array_key_exists( 'errors', $response_body ) ) {
				$errors     = $response_body['errors'];
				$main_error = array_shift( $errors );
				$error_message = $this->get_error_message( $main_error, $response['body'] );
			}

			$response_errors = new WP_Error( $main_error['status'], $error_message, $main_error );

			foreach ( $errors as $error ) {
				$error_message = $this->get_error_message( $error, $response['body'] );
				$error_status = isset( $error['status'] ) ? 'ams_error: ' . $error['status'] : '';
				$response_errors->add( $error_status, $error_message, $error );
			}
		}

		return $response_errors;
	}

	/**
	 * @param array  $ams_error
	 * @param string $default
	 *
	 * @return string
	 */
	private function get_error_message( $ams_error, $default ) {
		$title   = isset( $ams_error['title'] ) ? $ams_error['title'] . ': ' : '';
		$details = isset( $ams_error['detail'] ) ? $ams_error['detail'] : $default;
		return $title . $details;
	}

	private function response_has_keys( $response ) {
		if ( $this->response_has_body( $response ) ) {
			$response_body = json_decode( $response['body'], true );

			return array_key_exists( 'secret_key', $response_body )
			       && array_key_exists( 'shared_key', $response_body );
		}

		return false;
	}

	/**
	 * @return array
	 */
	private function get_registration_data() {
		return get_option( WPML_TM_ATE_Authentication::AMS_DATA_KEY, array() );
	}

	/**
	 * @param $registration_data
	 *
	 * @return bool
	 */
	private function set_registration_data( $registration_data ) {
		return update_option( WPML_TM_ATE_Authentication::AMS_DATA_KEY, $registration_data );
	}

	/**
	 * @param array $managers
	 *
	 * @return array|mixed|null|object|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function synchronize_managers( array $managers ) {
		$result = null;

		$managers_data = $this->get_users_data( $managers, true );

		if ( $managers_data ) {
			$url = $this->endpoints->get_ams_synchronize_managers();
			$url = str_replace( '{WEBSITE_UUID}', wpml_get_site_id(), $url );

			$params = array( 'translation_managers' => $managers_data );

			$response = $this->signed_request( 'PUT', $url, $params );

			if ( $this->response_has_body( $response ) ) {
				$response_body = json_decode( $response['body'], true );

				$result = $this->get_errors( $response );

				if ( ! is_wp_error( $result ) ) {
					$result = $response_body;
				}
			}
		}

		return $result;
	}

	/**
	 * @param array $translators
	 *
	 * @return array|mixed|null|object|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function synchronize_translators( array $translators ) {
		$result = null;

		$translators_data = $this->get_users_data( $translators );

		if ( $translators_data ) {
			$url = $this->endpoints->get_ams_synchronize_translators();

			$params = array( 'translators' => $translators_data );

			$response = $this->signed_request( 'PUT', $url, $params );

			if ( $this->response_has_body( $response ) ) {
				$response_body = json_decode( $response['body'], true );

				$result = $this->get_errors( $response );

				if ( ! is_wp_error( $result ) ) {
					$result = $response_body;
				}
			}
		}

		return $result;
	}

	/**
	 * @param string     $verb
	 * @param string     $url
	 * @param array|null $params
	 *
	 * @return array|WP_Error
	 */
	private function request( $verb, $url, array $params = null ) {
		$verb = strtoupper( $verb );

		$args = array(
			'method'  => $verb,
			'headers' => array(
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
			),
		);
		if ( $params ) {
			$args['body'] = wp_json_encode( $params, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );
		}

		return $this->wp_http->request( $this->add_versions_to_url( $url ), $args );
	}

	/**
	 * @param string     $verb
	 * @param string     $url
	 * @param array|null $params
	 *
	 * @return array|WP_Error
	 */
	private function signed_request( $verb, $url, array $params = null ) {
		$verb       = strtoupper( $verb );
		$signed_url = $this->auth->get_signed_url( $verb, $url, $params );

		return $this->request( $verb, $signed_url, $params );
	}

	/**
	 * @param $url
	 *
	 * @return string
	 */
	private function add_versions_to_url( $url ) {
		$url_parts = wp_parse_url( $url );
		$query     = array();
		if ( array_key_exists( 'query', $url_parts ) ) {
			parse_str( $url_parts['query'], $query );
		}
		$query['wpml_core_version'] = ICL_SITEPRESS_VERSION;
		$query['wpml_tm_version']   = WPML_TM_VERSION;

		$url_parts['query'] = http_build_query( $query );
		$url                = http_build_url( $url_parts );

		return $url;
	}
}
