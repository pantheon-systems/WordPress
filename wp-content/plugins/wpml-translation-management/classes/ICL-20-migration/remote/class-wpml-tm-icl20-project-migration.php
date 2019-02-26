<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Project {
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
	 * @param int    $project_id
	 * @param string $access_key
	 * @param string $new_token
	 *
	 * @return bool|null
	 * @throws \WPML_TM_ICL20MigrationException
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/tsapi-887
	 *
	 */
	public function migrate( $project_id, $access_key, $new_token ) {
		$url = $this->end_point . '/projects/' . $project_id . '/migrate_service.json';

		$args = array(
			'method'  => 'POST',
			'headers' => array(
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
			),
			'body'    => wp_json_encode( array(
				                             'accesskey'     => $access_key,
				                             'custom_fields' => array(
					                             'api_token' => $new_token,
				                             )
			                             ) )
		);

		$response = $this->http->post( $url, $args );

		$code = (int) $response['response']['code'];
		if ( $code !== 200 ) {
			$message = $response['response']['message'];
			if ( array_key_exists( 'body', $response ) ) {
				$body = json_decode( $response['body'], JSON_OBJECT_AS_ARRAY );
				if ( isset( $body['status']['message'] ) ) {
					$message .= PHP_EOL . $body['status']['message'];
				}
			}

			throw new WPML_TM_ICL20MigrationException( $message, $code );
		}

		return true;
	}

	/**
	 * @param int    $project_id
	 * @param string $access_key
	 *
	 * @return bool
	 * @throws WPML_TM_ICL20MigrationException
	 */
	public function rollback_migration( $project_id, $access_key ) {
		$url = $this->end_point . '/projects/' . $project_id . '/rollback_migration.json';

		$args = array(
			'method'  => 'POST',
			'headers' => array(
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
			),
			'body'    => wp_json_encode( array( 'accesskey' => $access_key ) )
		);

		$response = $this->http->post( $url, $args );

		$code = (int) $response['response']['code'];
		if ( $code !== 200 ) {
			$message = $response['response']['message'];
			if ( array_key_exists( 'body', $response ) ) {
				$body = json_decode( $response['body'], JSON_OBJECT_AS_ARRAY );
				if ( isset( $body['status']['message'] ) ) {
					$message .= PHP_EOL . $body['status']['message'];
				}
			}

			throw new WPML_TM_ICL20MigrationException( $message, $code );
		}

		return true;
	}
}