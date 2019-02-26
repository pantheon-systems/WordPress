<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ATE_API {
	private $wp_http;
	private $auth;
	private $endpoints;

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
	 * @param array $params
	 *
	 * @see https://bitbucket.org/emartini_crossover/ate/wiki/API/V1/jobs/create
	 *
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function create_jobs( array $params ) {
		$verb = 'POST';
		$url  = $this->endpoints->get_ate_jobs();

		$signed_url = $this->auth->get_signed_url( $verb, $url, $params );

		$result = $this->wp_http->request( $signed_url,
		                                   array(
			                                   'timeout' => 60,
			                                   'method'  => $verb,
			                                   'headers' => $this->json_headers(),
			                                   'body'    => wp_json_encode( $params, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES ),
		                                   ) );

		return $this->get_response( $result );
	}

	/**
	 * @param int|string|array $ate_job_id
	 *
	 * @return array|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function confirm_received_job( $ate_job_id ) {
		$verb = 'GET';
		$url  = $this->endpoints->get_ate_confirm_job( $ate_job_id );

		$signed_url = $this->auth->get_signed_url( $verb, $url );
		$result     = $this->wp_http->request( $signed_url,
		                                       array(
			                                       'timeout' => 60,
			                                       'method'  => $verb,
			                                       'headers' => $this->json_headers(),
		                                       ) );

		return $this->get_response( $result );
	}

	/**
	 * @param int    $job_id
	 * @param string $return_url
	 *
	 * @return string|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function get_editor_url( $job_id, $return_url ) {
		$url = $this->endpoints->get_ate_editor();
		$url = str_replace( array(
			                    '{job_id}',
			                    '{translator_email}',
			                    '{return_url}'
		                    ),
		                    array(
			                    $job_id,
			                    urlencode( filter_var( wp_get_current_user()->user_email, FILTER_SANITIZE_URL ) ),
			                    urlencode( filter_var( $return_url, FILTER_SANITIZE_URL ) ),
		                    ),
		                    $url );

		return $this->auth->get_signed_url( 'GET', $url, null );
	}

	/**
	 * @param int $ate_job_id
	 *
	 * @return array|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function get_job( $ate_job_id ) {
		$verb = 'GET';
		$url  = $this->endpoints->get_ate_jobs( $ate_job_id );

		$signed_url = $this->auth->get_signed_url( $verb, $url, null );

		$result = $this->wp_http->request( $signed_url,
		                                   array(
			                                   'timeout' => 60,
			                                   'method'  => $verb,
			                                   'headers' => $this->json_headers(),
		                                   ) );

		return $this->get_response( $result );
	}

	/**
	 * @param null|array $job_ids
	 * @param null|array $statuses
	 *
	 * @return array|mixed|null|object|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function get_jobs( $job_ids, $statuses = null ) {
		$verb = 'GET';
		$url  = $this->endpoints->get_ate_jobs( $job_ids, $statuses );

		$signed_url = $this->auth->get_signed_url( $verb, $url, null );

		$result = $this->wp_http->request( $signed_url,
		                                   array(
			                                   'timeout' => 60,
			                                   'method'  => $verb,
			                                   'headers' => $this->json_headers(),
		                                   ) );

		return $this->get_response( $result );
	}

	/**
	 * @param null|array $job_ids
	 *
	 * @return array|mixed|null|object|WP_Error
	 * @throws \InvalidArgumentException
	 */
	public function get_non_delivered_ate_jobs( $job_ids ) {
		return $this->get_jobs( $job_ids,
		                        array(
			                        WPML_TM_ATE_AMS_Endpoints::ATE_JOB_STATUS_CREATED,
			                        WPML_TM_ATE_AMS_Endpoints::ATE_JOB_STATUS_TRANSLATING,
			                        WPML_TM_ATE_AMS_Endpoints::ATE_JOB_STATUS_TRANSLATED,
			                        WPML_TM_ATE_AMS_Endpoints::ATE_JOB_STATUS_DELIVERING,
		                        ) );
	}

	private function get_response( $result ) {
		$errors = $this->get_response_errors( $result );
		if ( is_wp_error( $errors ) ) {
			return $errors;
		}

		return $this->get_response_body( $result );
	}

	private function get_response_body( $result ) {
		if ( is_array( $result ) && array_key_exists( 'body', $result ) && ! is_wp_error( $result ) ) {
			$body = json_decode( $result['body'] );

			if ( isset( $body->authenticated ) && ! (bool) $body->authenticated ) {
				return new WP_Error( 'ate_auth_failed', $body->message );
			}

			return $body;
		}

		return $result;
	}

	private function get_response_errors( $response ) {
		$response_errors = null;
		if ( is_wp_error( $response ) ) {
			$response_errors = $response;
		} elseif ( array_key_exists( 'body', $response ) && $response['response']['code'] >= 400 ) {
			$errors = array();

			$response_body = json_decode( $response['body'], true );

			if ( is_array( $response_body ) && array_key_exists( 'errors', $response_body ) ) {
				$errors = $response_body['errors'];
			}

			$response_errors = new WP_Error( $response['response']['code'], $response['response']['message'], $errors );
		}

		return $response_errors;
	}

	/**
	 * @return array
	 */
	private function json_headers() {
		return array(
			'Accept'       => 'application/json',
			'Content-Type' => 'application/json',
		);
	}
}
