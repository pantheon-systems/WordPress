<?php

class WPML_Translation_Proxy_Networking {

	const API_VERSION = 1.1;

	/** @var WP_Http $http */
	private $http;

	/** @var WPML_TP_Lock $tp_lock */
	private $tp_lock;

	public function __construct( WP_Http $http, WPML_TP_Lock $tp_lock ) {
		$this->http    = $http;
		$this->tp_lock = $tp_lock;
	}

	/**
	 * @param string    $url
	 * @param array     $params
	 * @param string    $method
	 * @param bool|true $has_return_value
	 * @param bool|true $json_response
	 * @param bool|true $has_api_response
	 *
	 * @return array|mixed|stdClass|string
	 * @throws WPMLTranslationProxyApiException
	 */
	public function send_request(
		$url,
		$params = array(),
		$method = 'GET',
		$has_return_value = true,
		$json_response = true,
		$has_api_response = true
	) {
		if ( $this->tp_lock->is_locked( $url ) ) {
			throw new WPMLTranslationProxyApiException( 'Communication with translation proxy is not allowed.' );
		}

		if ( ! $url ) {
			throw new WPMLTranslationProxyApiException( 'Empty target URL given!' );
		}

		$response = null;
		$method   = strtoupper( $method );

		if ( $params ) {
			$url = TranslationProxy_Api::add_parameters_to_url( $url, $params );
			if ( 'GET' === $method ) {
				$url .= '?' . wpml_http_build_query( $params );
			}
		}
		if ( ! isset( $params['api_version'] ) || ! $params['api_version'] ) {
			$params['api_version'] = self::API_VERSION;
		}

		WPML_TranslationProxy_Com_Log::log_call( $url, $params );
		$api_response = $this->call_remote_api( $url, $params, $method, $has_return_value );

		if ( $has_return_value ) {
			if ( ! isset( $api_response['headers']['content-type'] ) ) {
				throw new WPMLTranslationProxyApiException( 'Invalid HTTP response, no content type in header given!' );
			}
			$content_type = $api_response['headers']['content-type'];
			$api_response = $api_response['body'];
			$api_response = strpos( $content_type, 'zip' ) !== false ? gzdecode( $api_response ) : $api_response;
			WPML_TranslationProxy_Com_Log::log_response( $json_response ? $api_response : 'XLIFF received' );
			if ( $json_response ) {
				$response = json_decode( $api_response );
				if ( $has_api_response ) {
					if ( ! $response || ! isset( $response->status->code ) || $response->status->code !== 0 ) {
						$exception_message = $this->get_exception_message( $url,
						                                                   $method,
						                                                   $params,
						                                                   $response );
						if ( isset( $response->status->message ) ) {
							$exception_message = '';
							if ( isset( $response->status->code ) ) {
								$exception_message = '(' . $response->status->code . ') ';
							}
							$exception_message .= $response->status->message;
						}
						throw new WPMLTranslationProxyApiException( $exception_message );
					}
					$response = $response->response;
				}
			} else {
				$response = $api_response;
			}
		}

		return $response;
	}

	public function get_extra_fields_remote( $project ) {

		$params = array(
			'accesskey'   => $project->access_key,
			'api_version' => self::API_VERSION,
			'project_id'  => $project->id,
		);

		return TranslationProxy_Api::proxy_request( '/projects/{project_id}/extra_fields.json', $params );
	}

	/**
	 * @param string $url
	 * @param array  $params
	 * @param string $method
	 * @param bool   $has_return_value
	 *
	 * @throws \WPMLTranslationProxyApiException
	 *
	 * @return null|string
	 */
	private function call_remote_api(
		$url,
		$params,
		$method,
		$has_return_value = true
	) {
		$context  = $this->filter_request_params( $params, $method );
		$response = $this->http->request( $url, $context );
		if ( ( $has_return_value && (bool) $response === false )
		     || is_wp_error( $response )
		     || ( isset( $response['response']['code'] ) && $response['response']['code'] > 400 ) ) {
			throw new WPMLTranslationProxyApiException( $this->get_exception_message( $url,
			                                                                          $method,
			                                                                          $context,
			                                                                          $response ) );
		}

		return $response;
	}

	private function get_exception_message( $url, $method, $context, $response ) {
		$sanitized_url      = WPML_TranslationProxy_Com_Log::sanitize_url( $url );
		$sanitized_context  = WPML_TranslationProxy_Com_Log::sanitize_data( $context );
		$sanitized_response = WPML_TranslationProxy_Com_Log::sanitize_data( $response );

		return 'Cannot communicate with the remote service |'
		       . ' url: '
		       . '`'
		       . $sanitized_url
		       . '`'
		       . ' method: '
		       . '`'
		       . $method
		       . '`'
		       . ' param: '
		       . '`'
		       . wp_json_encode( $sanitized_context )
		       . '`'
		       . ' response: '
		       . '`'
		       . wp_json_encode( $sanitized_response )
		       . '`';
	}

	/**
	 * @param array $params request parameters
	 * @param string $method HTTP request method
	 *
	 * @return array
	 */
	private function filter_request_params( $params, $method ) {
		$request_filter = new WPML_TP_HTTP_Request_Filter( array(
			'method'    => $method,
			'body'      => $params,
			'sslverify' => false,
			'timeout'   => 60

		) );

		return $request_filter->out();
	}
}