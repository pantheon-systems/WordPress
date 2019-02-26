<?php

/**
 * Class WPML_TF_Feedback_Post_Convert
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Feedback_Post_Convert extends WPML_TF_Data_Object_Post_Convert {

	const POST_TYPE = 'wpml_tf_feedback';

	/**
	 * @return array
	 */
	public function get_post_fields() {
		return array(
			'id'           => 'ID',
			'date_created' => 'post_date',
			'content'      => 'post_content',
			'status'       => 'post_status',
		);
	}

	/**
	 * @return array
	 */
	public function get_meta_fields() {
		return array(
			'rating',
			'document_id',
			'document_type',
			'language_from',
			'language_to',
			'job_id',
			'reviewer_id',
			'tp_rating_id',
			'tp_feedback_id',
			'feedback_forward_method'
		);
	}

	/**
	 * @param IWPML_TF_Data_Object $feedback
	 *
	 * @return array
	 * @throws Exception
	 */
	public function to_post_data( IWPML_TF_Data_Object $feedback ) {
		if( ! $feedback instanceof WPML_TF_Feedback ) {
			throw new Exception( 'The $feedback argument must be an instance of WPML_TF_Feedback' );
		}

		/** @var WPML_TF_Feedback $feedback */
		$post               = new stdClass();
		$post->ID           = $feedback->get_id();
		$post->post_date    = $feedback->get_date_created();
		$post->post_content = $feedback->get_content();
		$post->post_status  = $feedback->get_status();
		$post->post_type    = self::POST_TYPE;

		return array(
			'post'     => $post,
			'metadata' => array(
				'rating'                  => $feedback->get_rating(),
				'document_id'             => $feedback->get_document_id(),
				'document_type'           => $feedback->get_document_type(),
				'language_from'           => $feedback->get_language_from(),
				'language_to'             => $feedback->get_language_to(),
				'job_id'                  => $feedback->get_job_id(),
				'reviewer_id'             => $feedback->get_reviewer()->get_id(),
				'tp_rating_id'            => $feedback->get_tp_responses()->get_rating_id(),
				'tp_feedback_id'          => $feedback->get_tp_responses()->get_feedback_id(),
				'feedback_forward_method' => $feedback->get_tp_responses()->get_feedback_forward_method(),
			),
		);
	}

	/**
	 * @param array $post_data
	 *
	 * @return WPML_TF_Feedback
	 */
	public function to_object( array $post_data ) {
		$feedback_data = $this->build_object_data_for_constructor( $post_data );
		$feedback_factory = new WPML_TF_Feedback_Factory();
		return $feedback_factory->create( $feedback_data );
	}
}