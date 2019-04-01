<?php

/**
 * Class WPML_TF_Message_Collection_Filter
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Message_Collection_Filter implements IWPML_TF_Collection_Filter {

	/** @var  int $feedback_id */
	private $feedback_id;

	/** @var  array|null */
	private $feedback_ids;

	/**
	 * WPML_TF_Feedback_Collection_Filter constructor.
	 *
	 * @param array $args
	 */
	public function __construct( array $args = array() ) {
		if ( isset( $args['feedback_id'] ) ) {
			$this->feedback_id = (int) $args['feedback_id'];
		}

		if ( isset( $args['feedback_ids'] ) && is_array( $args['feedback_ids'] ) ) {
			$this->feedback_ids = $args['feedback_ids'];
		}
	}

	/**
	 * @return int|null
	 */
	private function get_feedback_id() {
		return $this->feedback_id;
	}


	/**
	 * @return array|null
	 */
	private function get_feedback_ids() {
		return $this->feedback_ids;
	}

	/**
	 * @return array
	 */
	public function get_posts_args() {
		$posts_args = array(
			'posts_per_page'   => -1,
			'post_type'        => WPML_TF_Message_Post_Convert::POST_TYPE,
			'suppress_filters' => false,
			'post_status'      => 'any',
		);

		if ( $this->get_feedback_id() ) {
			$posts_args['post_parent'] = $this->get_feedback_id();
		}

		if ( $this->get_feedback_ids() ) {
			$posts_args['post_parent__in'] = $this->get_feedback_ids();
		}

		return $posts_args;
	}

	/**
	 * @return WPML_TF_Message_Collection
	 */
	public function get_new_collection() {
		return new WPML_TF_Message_Collection();
	}
}