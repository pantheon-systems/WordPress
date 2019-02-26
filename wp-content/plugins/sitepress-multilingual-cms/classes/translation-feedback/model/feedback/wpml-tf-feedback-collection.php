<?php

/**
 * Class WPML_TF_Feedback_Collection
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Feedback_Collection extends WPML_TF_Collection {

	private $order;
	private $filter_value;

	/**
	 * @param int $offset
	 * @param int $length
	 */
	public function reduce_collection( $offset, $length ) {
		$this->collection = array_slice( $this->collection, $offset, $length, true );
	}

	/**
	 * @param string $property
	 * @param string $order
	 */
	public function sort_collection( $property, $order ) {
		$this->order = $order;
		$method      = 'compare_by_' . $property;

		if ( method_exists( $this, $method ) ) {
			// Use @ to avoid warnings in unit tests => see bug https://bugs.php.net/bug.php?id=50688
			@uasort( $this->collection, array( $this, $method ) );
		}
	}

	/**
	 * @param WPML_TF_Feedback $a
	 * @param WPML_TF_Feedback $b
	 *
	 * @return mixed
	 */
	private function compare_by_pending( WPML_TF_Feedback $a, WPML_TF_Feedback $b ) {
		if ( $a->is_pending() && ! $b->is_pending() ) {
			$compare = -1;
		} elseif ( ! $a->is_pending() && $b->is_pending() ) {
			$compare = 1;
		} else {
			$compare = $this->compare_by_id( $a, $b );
		}

		return $compare;
	}

	/**
	 * @param WPML_TF_Feedback $a
	 * @param WPML_TF_Feedback $b
	 *
	 * @return mixed
	 */
	private function compare_by_feedback( WPML_TF_Feedback $a, WPML_TF_Feedback $b ) {
		if ( 'asc' === $this->order ) {
			$compare = strcasecmp( $a->get_content(), $b->get_content() );
		} else {
			$compare = strcasecmp( $b->get_content(), $a->get_content() );
		}

		if ( 0 === $compare ) {
			$compare = $this->compare_by_id( $a, $b );
		}

		return $compare;
	}

	/**
	 * @param WPML_TF_Feedback $a
	 * @param WPML_TF_Feedback $b
	 *
	 * @return mixed
	 */
	private function compare_by_rating( WPML_TF_Feedback $a, WPML_TF_Feedback $b ) {
		if ( 'asc' === $this->order ) {
			$compare = $a->get_rating() - $b->get_rating();
		} else {
			$compare = $b->get_rating() - $a->get_rating();
		}

		if ( 0 === $compare ) {
			$compare = $this->compare_by_id( $a, $b );
		}

		return $compare;
	}

	/**
	 * @param WPML_TF_Feedback $a
	 * @param WPML_TF_Feedback $b
	 *
	 * @return mixed
	 */
	private function compare_by_status( WPML_TF_Feedback $a, WPML_TF_Feedback $b ) {
		if ( 'asc' === $this->order ) {
			$compare = strcmp( $a->get_text_status(), $b->get_text_status() );
		} else {
			$compare = strcmp( $b->get_text_status(), $a->get_text_status() );
		}

		if ( 0 === $compare ) {
			$compare = $this->compare_by_id( $a, $b );
		}

		return $compare;
	}

	/**
	 * @param WPML_TF_Feedback $a
	 * @param WPML_TF_Feedback $b
	 *
	 * @return mixed
	 */
	private function compare_by_document_title( WPML_TF_Feedback $a, WPML_TF_Feedback $b ) {
		if ( 'asc' === $this->order ) {
			$compare = strcasecmp( $a->get_document_information()->get_title(), $b->get_document_information()->get_title() );
		} else {
			$compare = strcasecmp( $b->get_document_information()->get_title(), $a->get_document_information()->get_title() );
		}

		if ( 0 === $compare ) {
			$compare = $this->compare_by_id( $a, $b );
		}

		return $compare;
	}

	/**
	 * @param WPML_TF_Feedback $a
	 * @param WPML_TF_Feedback $b
	 *
	 * @return mixed
	 */
	private function compare_by_date( WPML_TF_Feedback $a, WPML_TF_Feedback $b ) {
		if ( 'asc' === $this->order ) {
			$compare = strtotime( $a->get_date_created() ) - strtotime( $b->get_date_created() );
		} else {
			$compare = strtotime( $b->get_date_created() ) - strtotime( $a->get_date_created() );
		}

		if ( 0 === $compare ) {
			$compare = $this->compare_by_id( $a, $b );
		}

		return $compare;
	}

	/**
	 * @param WPML_TF_Feedback $a
	 * @param WPML_TF_Feedback $b
	 *
	 * @return int
	 */
	private function compare_by_id( WPML_TF_Feedback $a, WPML_TF_Feedback $b ) {
		if ( 'asc' === $this->order ) {
			return $a->get_id() - $b->get_id();
		} else {
			return $b->get_id() - $a->get_id();
		}
	}

	/**
	 * @param WPML_TF_Message_Collection $message_collection
	 */
	public function link_messages_to_feedback( WPML_TF_Message_Collection $message_collection ) {
		foreach ( $message_collection as $message ) {

			/** @var WPML_TF_Message $message */
			if ( array_key_exists( $message->get_feedback_id(), $this->collection ) ) {
				$this->collection[ $message->get_feedback_id() ]->add_message( $message );
			}
		}
	}

	/**
	 * @param string $property
	 * @param string $value
	 */
	public function filter_by( $property, $value ) {
		$this->filter_value = $value;
		$method             = 'filter_by_' . $property;

		if ( method_exists( $this, $method ) ) {
			$this->collection = array_filter( $this->collection, array( $this, $method ) );
		}
	}

	/**
	 * @param WPML_TF_Feedback $feedback
	 *
	 * @return bool
	 */
	private function filter_by_status( WPML_TF_Feedback $feedback ) {
		if ( $feedback->get_status() === $this->filter_value ) {
			return true;
		}

		return false;
	}

	/**
	 * @param WPML_TF_Feedback $feedback
	 *
	 * @return bool
	 */
	private function filter_by_language( WPML_TF_Feedback $feedback ) {
		if ( $feedback->get_language_to() === $this->filter_value ) {
			return true;
		}

		return false;
	}

	/**
	 * @param WPML_TF_Feedback $feedback
	 *
	 * @return bool
	 */
	private function filter_by_post_id( WPML_TF_Feedback $feedback ) {
		if ( 0 === strpos( $feedback->get_document_type(), 'post_' )
		     && $feedback->get_document_id() === (int) $this->filter_value
		) {
			return true;
		}

		return false;
	}

	public function remove_trashed() {
		foreach ( $this->collection as $id => $feedback ) {
			/** @var WPML_TF_Feedback $feedback */
			if ( 'trash' === $feedback->get_status() ) {
				unset( $this->collection[ $id ] );
			}
		}
	}
}
