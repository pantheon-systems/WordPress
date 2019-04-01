<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ICL20_Acknowledge {
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
	 * @return bool
	 * @throws \WPML_TM_ICL20MigrationException
	 *
	 * Note: `ts_id` (aka `website_id`) = `website_id`
	 *
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/icldev-2322
	 */
	public function acknowledge_icl( $ts_id, $ts_accesskey ) {
		$url = $this->end_point . '/wpml/websites/' . $ts_id . '/migrated';
		$url = add_query_arg( array(
			                      'accesskey' => $ts_accesskey,
		                      ),
		                      $url );

		$response = $this->http->post( $url,
		                               array(
			                               'method'  => 'POST',
			                               'headers' => array(
				                               'Accept'       => 'application/json',
				                               'Content-Type' => 'application/json',
			                               ),
		                               ) );

		$code = (int) $response['response']['code'];
		if ( $code !== 200 ) {
			throw new WPML_TM_ICL20MigrationException( $response['response']['message'], $code );
		}

		return true;
	}
}