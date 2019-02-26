<?php

/**
 * Class WPML_TF_Feedback_Collection_Filter
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Feedback_Collection_Filter implements IWPML_TF_Collection_Filter {

	/** @var bool $exclude_rating_only */
	private $exclude_rating_only;

	/** @var array $language_pairs */
	private $language_pairs;

	/** @var int $pending_tp_ratings */
	private $pending_tp_ratings;

	/** @var int tp_feedback_id */
	private $tp_feedback_id;

	/** @var int $post_id */
	private $post_id;

	/** @var int $reviewer_id */
	private $reviewer_id;

	/**
	 * WPML_TF_Feedback_Collection_Filter constructor.
	 *
	 * @param array $args
	 */
	public function __construct( array $args ) {
		if ( isset( $args['exclude_rating_only'] ) ) {
			$this->exclude_rating_only = $args['exclude_rating_only'];
		}

		if ( isset( $args['language_pairs'] ) ) {
			$this->language_pairs = $args['language_pairs'];
		}

		if ( isset( $args['pending_tp_ratings'] ) ) {
			$this->pending_tp_ratings = (int) $args['pending_tp_ratings'];
		}

		if ( isset( $args['tp_feedback_id'] ) ) {
			$this->tp_feedback_id = (int) $args['tp_feedback_id'];
		}

		if ( isset( $args['post_id'] ) ) {
			$this->post_id = (int) $args['post_id'];
		}

		if ( isset( $args['reviewer_id'] ) ) {
			$this->reviewer_id = (int) $args['reviewer_id'];
		}
	}

	/** @return null|bool */
	private function get_exclude_rating_only() {
		return $this->exclude_rating_only;
	}

	/** @return null|array */
	private function get_language_pairs() {
		return $this->language_pairs;
	}

	/** @return null|int */
	private function get_pending_tp_ratings() {
		return $this->pending_tp_ratings;
	}

	/** @return null|int */
	private function get_tp_feedback_id() {
		return $this->tp_feedback_id;
	}

	/** @return null|int */
	private function get_reviewer_id() {
		return $this->reviewer_id;
	}

	/** @return null|int */
	private function get_post_id() {
		return $this->post_id;
	}

	/** @return array */
	public function get_posts_args() {
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => WPML_TF_Feedback_Post_Convert::POST_TYPE,
			'suppress_filters' => false,
			'post_status'      => array( 'any', 'trash' ),
		);

		if ( $this->get_exclude_rating_only() ) {
			$args['exclude_tf_rating_only'] = true;
		}

		if ( is_array( $this->get_language_pairs() ) ) {

			$meta_query = array(
				'relation' => 'OR',
			);

			foreach ( $this->get_language_pairs() as $from => $targets ) {

				foreach ( $targets as $to => $value ) {

					$meta_query[] = array(
						'relation' => 'AND',
						array(
							'key'     => WPML_TF_Data_Object_Storage::META_PREFIX . 'language_from',
							'value'   => $from,
							'compare' => '=',
						),
						array(
							'key'     => WPML_TF_Data_Object_Storage::META_PREFIX . 'language_to',
							'value'   => $to,
							'compare' => '=',
						),
					);
				}
			}

			$args['meta_query'] = $meta_query;

		} elseif ( $this->get_pending_tp_ratings() ) {
			$args['posts_per_page'] = $this->get_pending_tp_ratings();
			$args['orderby']        = 'ID';
			$args['order']          = 'ASC';
			$args['meta_query']     = array(
				array(
					'key'     => WPML_TF_Data_Object_Storage::META_PREFIX . 'tp_rating_id',
					'value'   => '',
					'compare' => '=',
					'type'    => 'CHAR',
				),
			);
		} elseif ( $this->get_tp_feedback_id() ) {
			$args['posts_per_page'] = 1;
			$args['meta_query']     = array(
				array(
					'key'     => WPML_TF_Data_Object_Storage::META_PREFIX . 'tp_feedback_id',
					'value'   => $this->get_tp_feedback_id(),
					'compare' => '=',
				),
			);

		} elseif ( $this->get_post_id() ) {
			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key'     => WPML_TF_Data_Object_Storage::META_PREFIX . 'document_id',
					'value'   => $this->get_post_id(),
					'compare' => '=',
				),
				array(
					'key'     => WPML_TF_Data_Object_Storage::META_PREFIX . 'document_type',
					'value'   => 'post_' . get_post_type( $this->get_post_id() ),
					'compare' => '=',
				),
			);
		} elseif ( $this->get_reviewer_id() ) {
			$args['meta_query'] = array(
				array(
					'key'     => WPML_TF_Data_Object_Storage::META_PREFIX . 'reviewer_id',
					'value'   => $this->get_reviewer_id(),
					'compare' => '=',
				),
			);
		}

		return $args;
	}

	/** @return WPML_TF_Feedback_Collection */
	public function get_new_collection() {
		return new WPML_TF_Feedback_Collection();
	}
}
