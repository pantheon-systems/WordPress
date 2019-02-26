<?php

/**
 * Class WPML_TP_Abstract_API
 *
 * @author OnTheGoSystems
 */
abstract class WPML_TP_Abstract_API {

	/** @var WPML_TP_Client $tp_client */
	protected $tp_client;

	/** @var null|Exception $exception */
	protected $exception;

	/** @var null|string $error_message */
	protected $error_message;

	public function __construct( WPML_TP_Client $tp_client ) {
		$this->tp_client = $tp_client;
	}

	/** @return string */
	abstract protected function get_endpoint_uri();

	/** @return bool */
	abstract protected function is_authenticated();

	/**
	 * @param array $params
	 *
	 * @return mixed
	 */
	protected function get( array $params = array() ) {
		return $this->remote_call( $params, 'GET' );
	}

	/**
	 * @param array $params
	 *
	 * @return mixed
	 */
	protected function post( array $params = array() ) {
		return $this->remote_call( $params, 'POST' );
	}

	protected function put( array $params = array() ) {
		// @todo: Implement put
	}

	protected function delete( array $params = array() ) {
		// @todo: Implement delete
	}

	/**
	 * @param array  $params
	 * @param string $method
	 *
	 * @return mixed
	 */
	private function remote_call( array $params, $method ) {
		$response = false;

		try {
			$params   = $this->pre_process_params( $params );
			$response = TranslationProxy_Api::proxy_request( $this->get_endpoint_uri(), $params, $method );
		} catch ( Exception $e ) {
			$this->exception = $e;
		}

		return $response;
	}

	/**
	 * @param array $params
	 *
	 * @return array
	 */
	private function pre_process_params( array $params ) {
		if ( $this->is_authenticated() ) {
			$params['accesskey'] = $this->tp_client->get_project()->get_access_key();
		}

		return $params;
	}

	/**
	 * WPML does not store the Translation Proxy Job ID
	 * We have to identify the job somehow.
	 * This is why we are using `original_file_id`.
	 * It is the same as used in the XLIFF file as a value of `original` attribute.
	 * The combination of `original_file_id` and `batch_id` will be always unique.
	 * Translation Proxy provides this call, with these arguments, for this specific reason.
	 *
	 * @see https://git.onthegosystems.com/tp/translation-proxy/wikis/rate_translation
	 * @see https://git.onthegosystems.com/tp/translation-proxy/wikis/send_feedback
	 *
	 * @param int $job_id
	 * @param int $document_source_id
	 *
	 * @return string
	 */
	protected function get_original_file_id( $job_id, $document_source_id ) {
		return $job_id . '-' . md5( $job_id . $document_source_id );
	}

	/** @return null|Exception */
	public function get_exception() {
		return $this->exception;
	}

	/** @return null|string */
	public function get_error_message() {
		return $this->exception->getMessage();
	}
}
