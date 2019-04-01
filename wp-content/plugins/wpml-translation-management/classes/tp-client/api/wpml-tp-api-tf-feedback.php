<?php

/**
 * Class WPML_TP_API_TF_Feedback
 *
 * @author OnTheGoSystems
 */
class WPML_TP_API_TF_Feedback extends WPML_TP_Abstract_API {

	const URI_SEND       = '/batches/{batch_id}/jobs/{original_file_id}/feedbacks';
	const URI_GET_STATUS = '/feedbacks/{feedback_id}';

	/** @var string $endpoint_uri */
	private $endpoint_uri;

	/** @return string */
	protected function get_endpoint_uri() {
		return $this->endpoint_uri;
	}

	/** @return bool */
	protected function is_authenticated() {
		return true;
	}

	/**
	 * @param WPML_TF_Feedback $feedback
	 * @param array            $args
	 *
	 * @return int|false
	 */
	public function send( WPML_TF_Feedback $feedback, array $args ) {
		$previous_sent_feedback_id = $feedback->get_tp_responses()->get_feedback_id();

		if ( $previous_sent_feedback_id ) {
			return $previous_sent_feedback_id;
		}

		$this->endpoint_uri = self::URI_SEND;
		$ret = false;

		$feedback_parameters = array(
			'message' => $feedback->get_content(),
		);

		if ( array_key_exists( 'email', $args ) ) {
			$feedback_parameters['email'] = $args['email'];
		}

		$params = array(
			'batch_id'         => $this->tp_client->get_tm_jobs()->get_batch_id( $feedback->get_job_id() ),
			'original_file_id' => $this->get_original_file_id(
				$feedback->get_job_id(),
				$feedback->get_document_information()->get_source_id()
			),
			'feedback' => $feedback_parameters,
		);

		$response = $this->post( $params );

		if ( isset( $response->feedback->id ) ) {
			$ret = (int) $response->feedback->id;
		}

		return $ret;
	}

	/**
	 * @param WPML_TF_Feedback $feedback
	 *
	 * @return false[string
	 */
	public function status( WPML_TF_Feedback $feedback ) {
		$this->endpoint_uri = self::URI_GET_STATUS;
		$status = false;

		$params = array(
			'feedback_id' => $feedback->get_tp_responses()->get_feedback_id(),
		);

		$response = $this->get( $params );

		if ( isset( $response->status ) ) {
			$status = $response->status;
		}

		return $status;
	}
}
