<?php

/**
 * Class WPML_TF_Message_Post_Convert
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Message_Post_Convert extends WPML_TF_Data_Object_Post_Convert {

	const POST_TYPE = 'wpml_tf_message';

	/**
	 * @return array
	 */
	public function get_post_fields() {
		return array(
			'id'           => 'ID',
			'date_created' => 'post_date',
			'content'      => 'post_content',
			'feedback_id'  => 'post_parent',
		);
	}

	/**
	 * @return array
	 */
	public function get_meta_fields() {
		return array(
			'author_id',
		);
	}

	/**
	 * @param IWPML_TF_Data_Object $message
	 *
	 * @return array
	 * @throws Exception
	 */
	public function to_post_data( IWPML_TF_Data_Object $message ) {
		if( ! $message instanceof WPML_TF_Message ) {
			throw new Exception( 'The $message argument must be an instance of WPML_TF_Message' );
		}

		/** @var WPML_TF_Message $message */
		$post               = new stdClass();
		$post->ID           = $message->get_id();
		$post->post_date    = $message->get_date_created();
		$post->post_content = $message->get_content();
		$post->post_parent  = $message->get_feedback_id();
		$post->post_type    = self::POST_TYPE;

		return array(
			'post'     => $post,
			'metadata' => array(
				'author_id' => $message->get_author_id(),
			),
		);
	}

	/**
	 * @param array $post_data
	 *
	 * @return WPML_TF_Message
	 */
	public function to_object( array $post_data ) {
		$message_data = $this->build_object_data_for_constructor( $post_data );
		return new WPML_TF_Message( $message_data );
	}
}