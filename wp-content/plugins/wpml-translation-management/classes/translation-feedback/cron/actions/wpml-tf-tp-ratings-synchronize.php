<?php

/**
 * Class WPML_TF_TP_Ratings_Synchronize
 *
 * @author OnTheGoSystems
 */
class WPML_TF_TP_Ratings_Synchronize {

	const MAX_RATINGS_TO_SYNCHRONIZE     = 5;
	const PENDING_SYNC_RATING_IDS_OPTION = 'wpml_tf_pending_sync_rating_ids';
	const MAX_ATTEMPTS_TO_SYNC           = 3;

	/** @var WPML_TF_Data_Object_Storage $feedback_storage */
	private $feedback_storage;

	/** @var WPML_TP_API_TF_Ratings $tp_ratings */
	private $tp_ratings;

	/** @var array $pending_ids */
	private $pending_ids;

	/**
	 * WPML_TF_TP_Ratings_Synchronize constructor.
	 *
	 * @param WPML_TF_Data_Object_Storage $feedback_storage
	 * @param WPML_TP_API_TF_Ratings      $tp_ratings
	 */
	public function __construct( WPML_TF_Data_Object_Storage $feedback_storage, WPML_TP_API_TF_Ratings $tp_ratings ) {
		$this->feedback_storage = $feedback_storage;
		$this->tp_ratings       = $tp_ratings;
	}

	/** @param bool $clear_all_pending_ratings */
	public function run( $clear_all_pending_ratings = false ) {
		$this->pending_ids = get_option( self::PENDING_SYNC_RATING_IDS_OPTION, array() );

		$filter_args = array(
			'pending_tp_ratings' => $clear_all_pending_ratings ? -1 : self::MAX_RATINGS_TO_SYNCHRONIZE,
		);

		$feedback_filter = new WPML_TF_Feedback_Collection_Filter( $filter_args );
		/** @var WPML_TF_Feedback_Collection $feedback_collection */
		$feedback_collection = $this->feedback_storage->get_collection( $feedback_filter );
		$time_threshold      = 5 * MINUTE_IN_SECONDS;

		foreach ( $feedback_collection as $feedback ) {
			/** @var WPML_TF_Feedback $feedback */
			$time_since_creation = time() - strtotime( $feedback->get_date_created() );

			if ( ! $clear_all_pending_ratings && $time_since_creation < $time_threshold ) {
				continue;
			}

			$tp_rating_id = $this->tp_ratings->send( $feedback );

			if ( $tp_rating_id || $clear_all_pending_ratings ) {
				$this->set_tp_rating_id( $feedback, $tp_rating_id );
			} else {
				$this->handle_pending_rating_sync( $feedback );
			}
		}

		if ( $this->pending_ids ) {
			update_option( self::PENDING_SYNC_RATING_IDS_OPTION, $this->pending_ids, false );
		} else {
			delete_option( self::PENDING_SYNC_RATING_IDS_OPTION );
		}
	}

	private function set_tp_rating_id( WPML_TF_Feedback $feedback, $tp_rating_id ) {
		$feedback->get_tp_responses()->set_rating_id( (int) $tp_rating_id );
		$this->feedback_storage->persist( $feedback );
	}

	private function handle_pending_rating_sync( WPML_TF_Feedback $feedback ) {
		$this->increment_attempts( $feedback->get_id() );

		if ( $this->exceeds_max_attempts( $feedback->get_id() ) ) {
			$this->set_tp_rating_id( $feedback, 0 );
			unset( $this->pending_ids[ $feedback->get_id() ] );
		}
	}

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	private function exceeds_max_attempts( $id ) {
		return isset( $this->pending_ids[ $id ] ) && $this->pending_ids[ $id ] >= self::MAX_ATTEMPTS_TO_SYNC;
	}

	/** @param int $id */
	private function increment_attempts( $id ) {
		if ( ! isset( $this->pending_ids[ $id ] ) ) {
			$this->pending_ids[ $id ] = 1;
		} else {
			$this->pending_ids[ $id ]++;
		}
	}
}
