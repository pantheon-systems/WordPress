<?php

/**
 * Class WPML_TF_Translation_Queue_Hooks
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Translation_Queue_Hooks implements IWPML_Action {

	/** @var WPML_TF_Data_Object_Storage $feedback_storage */
	private $feedback_storage;

	public function __construct( WPML_TF_Data_Object_Storage $feedback_storage ) {
		$this->feedback_storage = $feedback_storage;
	}

	public function add_hooks() {
		add_filter( 'wpml_tm_allowed_translators_for_job', array( $this, 'add_reviewer_to_allowed_translators' ), 10, 2 );
	}

	public function add_reviewer_to_allowed_translators(
		array $allowed_translators,
		WPML_Translation_Job $translation_job
	) {
		$current_user_id = get_current_user_id();

		if ( $translation_job->get_translator_id() !== $current_user_id ) {

			$filter_args = array(
				'reviewer_id' => $current_user_id,
			);

			$collection_filter   = new WPML_TF_Feedback_Collection_Filter( $filter_args );
			$feedback_collection = $this->feedback_storage->get_collection( $collection_filter );

			foreach ( $feedback_collection as $feedback ) {
				/** @var WPML_TF_Feedback $feedback */
				if ( $feedback->get_job_id() === (int) $translation_job->get_id()
				     && 'fixed' !== $feedback->get_status()
				) {
					$allowed_translators[] = $current_user_id;
					break;
				}
			}
		}

		return $allowed_translators;
	}
}
