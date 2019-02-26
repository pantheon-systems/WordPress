<?php

/**
 * Class WPML_TF_Feedback_Query
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Feedback_Query {

	/** @var WPML_TF_Data_Object_Storage $feedback_storage */
	private $feedback_storage;

	/** @var WPML_TF_Data_Object_Storage $message_storage */
	private $message_storage;

	/** @var WPML_TF_Collection_Filter_Factory $collection_filter_factory */
	private $collection_filter_factory;

	/** @var  WPML_TF_Feedback_Collection $unfiltered_feedback_collection */
	private $unfiltered_feedback_collection;

	/** @var int $unfiltered_items_count */
	private $unfiltered_items_count;

	/** @var int $trashed_items_count */
	private $trashed_items_count;

	/** @var int $total_items_count */
	private $total_items_count;

	/** @var $filtered_items_count int */
	private $filtered_items_count;

	/** @var bool $is_in_trash */
	private $is_in_trash = false;

	/**
	 * WPML_TF_Feedback_Collection_Factory constructor.
	 *
	 * @param WPML_TF_Data_Object_Storage         $feedback_storage
	 * @param WPML_TF_Data_Object_Storage         $message_storage
	 * @param WPML_TF_Collection_Filter_Factory   $collection_filter_factory
	 */
	public function __construct(
		WPML_TF_Data_Object_Storage $feedback_storage,
		WPML_TF_Data_Object_Storage $message_storage,
		WPML_TF_Collection_Filter_Factory $collection_filter_factory
	) {
		$this->feedback_storage          = $feedback_storage;
		$this->message_storage           = $message_storage;
		$this->collection_filter_factory = $collection_filter_factory;
	}

	/**
	 * @return WPML_TF_Feedback_Collection
	 */
	public function get_unfiltered_collection() {
		if ( ! $this->unfiltered_feedback_collection ) {

			$storage_filters = array(
				'exclude_rating_only' => true,
			);

			if ( ! current_user_can( 'manage_options' ) ) {
				$storage_filters['reviewer_id'] = get_current_user_id();
			}

			$feedback_collection_filter           = $this->collection_filter_factory->create( 'feedback', $storage_filters );
			$this->unfiltered_feedback_collection = $this->feedback_storage->get_collection( $feedback_collection_filter );

			$this->unfiltered_items_count = count( $this->unfiltered_feedback_collection );
		}

		return $this->unfiltered_feedback_collection;
	}

	/**
	 * @param array $args
	 *
	 * @return WPML_TF_Feedback_Collection
	 */
	public function get( array $args ) {
		$feedback_collection = clone $this->get_unfiltered_collection();
		$feedback_collection = $this->trash_filter_collection( $feedback_collection, $args );
		$feedback_collection = $this->filter_collection( $feedback_collection, $args );
		$feedback_collection = $this->sort_collection( $feedback_collection, $args );
		$feedback_collection = $this->apply_pagination( $feedback_collection, $args );

		$message_filter_args       = array( 'feedback_ids' => $feedback_collection->get_ids() );
		$message_collection_filter = $this->collection_filter_factory->create( 'message', $message_filter_args );

		/** @var WPML_TF_Message_Collection $message_collection */
		$message_collection = $this->message_storage->get_collection( $message_collection_filter );

		$feedback_collection->link_messages_to_feedback( $message_collection );

		return $feedback_collection;
	}

	/**
	 * @param WPML_TF_Feedback_Collection $feedback_collection
	 * @param array                       $args
	 *
	 * @return WPML_TF_Feedback_Collection
	 */
	public function trash_filter_collection( WPML_TF_Feedback_Collection $feedback_collection, array $args ) {
		if ( isset( $args['status'] ) && 'trash' === $args['status'] ) {
			$this->is_in_trash = true;
			$feedback_collection->filter_by( 'status', 'trash' );
			$this->trashed_items_count = count( $feedback_collection );
			$this->total_items_count   = $this->unfiltered_items_count - $this->trashed_items_count;
		} else {
			$feedback_collection->remove_trashed();
			$this->total_items_count   = count( $feedback_collection );
			$this->trashed_items_count = $this->unfiltered_items_count - $this->total_items_count;
		}

		return $feedback_collection;
	}

	/**
	 * @param WPML_TF_Feedback_Collection $feedback_collection
	 * @param array                       $args
	 *
	 * @return WPML_TF_Feedback_Collection
	 */
	private function filter_collection( WPML_TF_Feedback_Collection $feedback_collection, array $args ) {
		if ( isset( $args['status'] ) && 'trash' !== $args['status'] ) {
			$feedback_collection->filter_by( 'status', $args['status'] );
		} elseif ( isset( $args['language'] ) ) {
			$feedback_collection->filter_by( 'language', $args['language'] );
		} elseif ( isset( $args['post_id'] ) ) {
			$feedback_collection->filter_by( 'post_id', $args['post_id'] );
		}

		$this->filtered_items_count = count( $feedback_collection );

		return $feedback_collection;
	}

	/**
	 * @param WPML_TF_Feedback_Collection $feedback_collection
	 * @param array                       $args
	 *
	 * @return WPML_TF_Feedback_Collection
	 */
	private function sort_collection( WPML_TF_Feedback_Collection $feedback_collection, array $args ) {
		$order_by = 'pending';
		$order    = 'desc';

		if ( isset( $args['orderby'] ) ) {
			$order_by = $args['orderby'];
		}

		if ( isset( $args['order'] ) ) {
			$order = $args['order'];
		}

		$feedback_collection->sort_collection( $order_by, $order );

		return $feedback_collection;
	}

	/**
	 * @param WPML_TF_Feedback_Collection $feedback_collection
	 * @param array                       $args
	 *
	 * @return WPML_TF_Feedback_Collection
	 */
	private function apply_pagination( WPML_TF_Feedback_Collection $feedback_collection, array $args ) {
		if ( isset( $args['paged'], $args['items_per_page'] ) ) {
			$offset = $args['items_per_page'] * max( 0,  $args['paged'] - 1 );
			$feedback_collection->reduce_collection( $offset, $args['items_per_page'] );
		}

		return $feedback_collection;
	}

	/**
	 * @return int
	 */
	public function get_total_items_count() {
		return $this->total_items_count;
	}

	/** @return int */
	public function get_total_trashed_items_count() {
		return $this->trashed_items_count;
	}

	/** @return int */
	public function get_filtered_items_count() {
		return $this->filtered_items_count;
	}

	/** @return bool */
	public function is_in_trash() {
		return $this->is_in_trash;
	}

	/**
	 * @param int  $feedback_id
	 * @param bool $with_messages
	 *
	 * @return null|WPML_TF_Feedback
	 */
	public function get_one( $feedback_id, $with_messages = true ) {
		$feedback = $this->feedback_storage->get( $feedback_id );

		if ( $feedback && $with_messages ) {
			$filter_args = array(
				'feedback_id' => $feedback_id,
			);

			$filter   = new WPML_TF_Message_Collection_Filter( $filter_args );
			$messages = $this->message_storage->get_collection( $filter );

			/** @var WPML_TF_Feedback $feedback */
			foreach ( $messages as $message ) {
				$feedback->add_message( $message );
			}
		}

		return $feedback;
	}
}
