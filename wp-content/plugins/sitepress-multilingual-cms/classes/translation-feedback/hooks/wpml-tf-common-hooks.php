<?php

/**
 * Class WPML_TF_Common_Hooks
 * @author OnTheGoSystems
 */
class WPML_TF_Common_Hooks implements IWPML_Action {

	/** @var WPML_TF_Data_Object_Storage $feedback_storage */
	private $feedback_storage;

	public function __construct( WPML_TF_Data_Object_Storage $feedback_storage ) {
		$this->feedback_storage = $feedback_storage;
	}

	/**
	 * method init
	 */
	public function add_hooks() {
		add_action( 'init', array( $this, 'init_action' ) );
		add_action( 'deleted_post', array( $this, 'cleanup_post_feedback_data' ) );
	}

	/**
	 * method init_action
	 */
	public function init_action() {
		register_post_type( WPML_TF_Feedback_Post_Convert::POST_TYPE );
		register_post_type( WPML_TF_Message_Post_Convert::POST_TYPE );
	}

	/** @param $post_id int */
	public function cleanup_post_feedback_data( $post_id ) {
		if ( WPML_TF_Feedback_Post_Convert::POST_TYPE === get_post_type( $post_id ) ) {
			return;
		}

		$args = array(
			'post_id' => $post_id,
		);

		$collection_filter   = new WPML_TF_Feedback_Collection_Filter( $args );
		$feedback_collection = $this->feedback_storage->get_collection( $collection_filter );

		foreach ( $feedback_collection as $feedback ) {
			$this->feedback_storage->delete( $feedback->get_id(), true );
		}
	}
}