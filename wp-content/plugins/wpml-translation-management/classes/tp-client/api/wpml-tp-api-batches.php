<?php

/**
 * Class WPML_TP_API_Batches
 */
class WPML_TP_API_Batches extends WPML_TP_Abstract_API {

	const API_VERSION           = 1.1;
	const CREATE_BATCH_ENDPOINT = '/projects/{project_id}/batches.json';
	const ADD_JOB_ENDPOINT      = '/batches/{batch_id}/jobs.json';

	private $endpoint_uri;

	protected function get_endpoint_uri() {
		return $this->endpoint_uri;
	}

	protected function is_authenticated() {
		return true;
	}

	/**
	 * @throws WPML_TP_Batch_Exception
	 *
	 * @param array       $batch_data
	 * @param false|array $extra_fields
	 *
	 * @return false|stdClass
	 *
	 * @link https://git.onthegosystems.com/tp/translation-proxy/wikis/create_batch_job
	 */
	public function create( array $batch_data, $extra_fields ) {
		$batch           = false;
		$this->endpoint_uri = self::CREATE_BATCH_ENDPOINT;

		$params = array(
			'api_version'  => self::API_VERSION,
			'project_id'   => $this->tp_client->get_project()->get_id(),
			'batch'        => $batch_data,
		);

		if ( $extra_fields ) {
			$params['extra_fields'] = $extra_fields;
		}

		$response = $this->post( $params );

		if ( $this->get_exception() ) {
			throw new WPML_TP_Batch_Exception( $this->get_error_message() );
		}

		if ( $response ) {
			$batch = new WPML_TP_Batch( $response->batch );
		}

		return $batch;
	}

	/**
	 * @param int   $batch_id
	 * @param array $job_data
	 *
	 * @return false|WPML_TP_Job
	 *
	 * @link https://git.onthegosystems.com/tp/translation-proxy/wikis/add_files_batch_job
	 */
	public function add_job( $batch_id, array $job_data ) {
		$job                = false;
		$this->endpoint_uri = self::ADD_JOB_ENDPOINT;

		$params = array(
			'api_version' => self::API_VERSION,
			'batch_id'    => $batch_id,
			'job'         => $job_data,
		);

		$response = $this->post( $params );

		if ( $response ) {
			$job = new WPML_TP_Job( $response->job );
		}

		return $job;
	}

	/**
	 * @link https://git.onthegosystems.com/tp/translation-proxy/wikis/commit_batch_job
	 */
	public function commit() {
		// To be implemented
	}

	/**
	 * @link https://git.onthegosystems.com/tp/translation-proxy/wikis/send-preview-bundle-job
	 */
	public function send_preview_bundle() {
		// To be implemented
	}
}
