<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Token {
	private $end_point;
	private $http;

	/**
	 * WPML_TM_ICL20 constructor.
	 *
	 * @param WP_Http $http
	 * @param string  $end_point
	 */
	public function __construct( WP_Http $http, $end_point ) {
		$this->http      = $http;
		$this->end_point = $end_point;
	}

	/**
	 * @param string $ts_accesskey
	 * @param int    $ts_id
	 *
	 * @return string|null
	 * @throws \WPML_TM_ICL20MigrationException
	 *
	 * Note: `ts_id` (aka `website_id`) = `website_id`
	 *
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/icldev-2285
	 */
	public function get_token( $ts_id, $ts_accesskey ) {
		if ( ! $ts_id ) {
			throw new WPML_TM_ICL20MigrationException( 'Missing ICL Website ID ($ts_id)' );
		}
		if ( ! $ts_accesskey ) {
			throw new WPML_TM_ICL20MigrationException( 'Missing ICL Access KEY ($ts_accesskey)' );
		}

		$url = $this->end_point . '/wpml/websites/' . $ts_id . '/token';
		$url = add_query_arg( array(
			                      'accesskey' => $ts_accesskey,
		                      ),
		                      $url );

		$response = $this->http->get( $url,
		                              array(
			                              'method'  => 'GET',
			                              'headers' => array(
				                              'Accept: application/json'
			                              ),
		                              ) );

		$code = (int) $response['response']['code'];
		if ( 200 !== $code ) {
			throw new WPML_TM_ICL20MigrationException( $response['response']['message'], $code );
		}

		if ( array_key_exists( 'body', $response ) ) {
			$response_data = json_decode( $response['body'], JSON_OBJECT_AS_ARRAY );

			if ( array_key_exists( 'api_token', $response_data )
			     && '' !== trim( $response_data['api_token'] ) ) {
				return $response_data['api_token'];
			}
		}

		return null;
	}
}