<?php

/**
 * Class WPML_TF_XML_RPC_Feedback_Update
 *
 * @author OnTheGoSystems
 */
class WPML_TF_XML_RPC_Feedback_Update {

	/** @var WPML_TF_Data_Object_Storage $feedback_storage */
	private $feedback_storage;

	/** @var WPML_TP_Project $tp_project */
	private $tp_project;

	public function __construct( WPML_TF_Data_Object_Storage $feedback_storage, WPML_TP_Project $tp_project ) {
		$this->feedback_storage = $feedback_storage;
		$this->tp_project       = $tp_project;
	}

	public function set_status( array $args ) {
		if ( $this->valid_arguments( $args ) ) {
			$feedback = $this->get_feedback( $args['feedback']['id'] );

			if ( $feedback ) {
				/** @var WPML_TF_Feedback $feedback */
				$feedback->set_status( $args['feedback']['status'] );
				$this->feedback_storage->persist( $feedback );
			}
		}
	}

	/**
	 * @param array $args
	 *
	 * @return bool
	 */
	private function valid_arguments( array $args ) {
		$valid = false;

		if ( isset( $args['feedback']['id'], $args['feedback']['status'], $args['authorization_hash'] ) ) {
			$expected_hash = sha1( $this->tp_project->get_id() . $this->tp_project->get_access_key() . $args['feedback']['id'] );

			if ( $expected_hash === $args['authorization_hash'] ) {
				$valid = true;
			}
		}

		return $valid;
	}

	/**
	 * @param int $tp_feedback_id
	 *
	 * @return null|WPML_TF_Feedback
	 */
	private function get_feedback( $tp_feedback_id ) {
		$feedback    = null;
		$filter_args = array(
			'tp_feedback_id' => $tp_feedback_id,
		);

		$collection_filter = new WPML_TF_Feedback_Collection_Filter( $filter_args );
		$collection = $this->feedback_storage->get_collection( $collection_filter );

		if ( $collection->count() ) {
			$feedback = $collection->current();
		}

		return $feedback;
	}
}
