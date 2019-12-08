<?php
/**
 * Override Solarium so that more options can be set before executing curl.
 *
 * @package Solr_Power
 */

use Solarium\Core\Client\Adapter\Curl as Curl;

/**
 * Override Solarium so that more options can be set before executing curl.
 */
class PantheonCurl extends Curl {

	/**
	 * {@inheritdoc}
	 *
	 * @param \Solarium\Core\Client\Request  $request  Solarium request object.
	 * @param \Solarium\Core\Client\Endpoint $endpoint Solarium endpoint object.
	 * @return \Solarium\Core\Client\Request
	 */
	public function createHandle( $request, $endpoint ) {
		$handler = parent::createHandle( $request, $endpoint );
		if ( defined( 'PANTHEON_ENVIRONMENT' ) ) {
			curl_setopt( $handler, CURLOPT_SSL_VERIFYPEER, false );
			$client_cert = $_SERVER['HOME'] . '/certs/binding.pem';
			curl_setopt( $handler, CURLOPT_SSLCERT, $client_cert );
		}

		return $handler;
	}
}
