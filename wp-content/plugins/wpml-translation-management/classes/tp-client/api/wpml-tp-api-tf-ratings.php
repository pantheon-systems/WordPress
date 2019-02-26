<?php

/**
 * Class WPML_TP_API_TF_Ratings
 *
 * @author OnTheGoSystems
 */
class WPML_TP_API_TF_Ratings extends WPML_TP_Abstract_API {

	/** @return string */
	protected function get_endpoint_uri() {
		return '/batches/{batch_id}/jobs/{original_file_id}/ratings';
	}

	/** @return bool */
	protected function is_authenticated() {
		return true;
	}

	/**
	 * @param WPML_TF_Feedback $feedback
	 *
	 * @return int|false
	 */
	public function send( WPML_TF_Feedback $feedback ) {
		$params = array(
			'batch_id'         => $this->tp_client->get_tm_jobs()->get_batch_id( $feedback->get_job_id() ),
			'original_file_id' => $this->get_original_file_id(
				$feedback->get_job_id(),
				$feedback->get_document_information()->get_source_id()
			),
			'rating'           => array(
				'rating' => $feedback->get_rating(),
			),
		);

		$response = $this->post( $params );

		if ( isset( $response->rating->id ) ) {
			return (int) $response->rating->id;
		}

		return false;
	}
}
