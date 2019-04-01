<?php

/**
 * Class WPML_TF_Feedback
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Feedback implements IWPML_TF_Data_Object {

	/** @var int */
	private $id;

	/** @var  string */
	private $date_created;

	/** @var WPML_TF_Feedback_Status */
	private $status;

	/** @var  int */
	private $rating;

	/** @var  string */
	private $content;

	/** @var  int */
	private $document_id;

	/** @var  string */
	private $document_type;

	/** @var  string */
	private $language_from;

	/** @var  string */
	private $language_to;

	/** @var  int|null */
	private $job_id;

	/** @var WPML_TF_Feedback_Reviewer */
	private $reviewer;

	/** @var WPML_TF_Collection $messages */
	private $messages;

	/** @var WPML_TF_Backend_Document_Information $document_information */
	private $document_information;

	/** @var WPML_TF_TP_Responses $tp_rating_responses */
	private $tp_responses;

	/**
	 * WPML_Translation_Feedback constructor.
	 *
	 * @param array                                $data
	 * @param WPML_TF_Backend_Document_Information $document_information
	 */
	public function __construct(
		$data = array(),
		WPML_TF_Backend_Document_Information $document_information = null
	) {
		$this->id            = array_key_exists( 'id', $data ) ? (int) $data['id'] : null;
		$this->date_created  = array_key_exists( 'date_created', $data )
			? sanitize_text_field( $data['date_created'] ) : null;
		$this->rating        = array_key_exists( 'rating', $data ) ? (int) $data['rating'] : null;
		$this->content       = array_key_exists( 'content', $data )
			? sanitize_text_field( $data['content'] ) : '';
		$this->document_id   = array_key_exists( 'document_id', $data ) ? (int) $data['document_id'] : null;
		$this->document_type = array_key_exists( 'document_type', $data )
			? sanitize_text_field( $data['document_type'] ) : null;
		$this->language_from = array_key_exists( 'language_from', $data )
			? sanitize_text_field( $data['language_from'] ) : null;
		$this->language_to   = array_key_exists( 'language_to', $data )
			? sanitize_text_field( $data['language_to'] ) : null;
		$this->job_id        = array_key_exists( 'job_id', $data ) ? (int) $data['job_id'] : null;
		$this->messages      = array_key_exists( 'messages', $data ) && $data['messages'] instanceof WPML_TF_Collection
			? $data['messages'] : new WPML_TF_Message_Collection();

		if ( array_key_exists( 'reviewer_id', $data ) ) {
			$this->set_reviewer( $data['reviewer_id'] );
		}

		$this->status = array_key_exists( 'status', $data )
			? new WPML_TF_Feedback_Status( $data['status'] ) : new WPML_TF_Feedback_Status( 'pending' );

		$this->set_tp_responses( new WPML_TF_TP_Responses( $data ) );

		if ( $document_information ) {
			$this->set_document_information( $document_information );
		}
	}

	/**
	 * @return int|mixed|null
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return mixed|null|string
	 */
	public function get_date_created() {
		return $this->date_created;
	}

	/**
	 * @return string
	 */
	public function get_status() {
		return $this->status->get_value();
	}

	/**
	 * @param string $status
	 */
	public function set_status( $status ) {
		$this->status->set_value( $status );
	}

	/**
	 * @return int
	 */
	public function get_rating() {
		return $this->rating;
	}

	/**
	 * @param int $rating
	 */
	public function set_rating( $rating ) {
		$this->rating = (int) $rating;
	}

	/**
	 * @return mixed|null|string
	 */
	public function get_content() {
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function set_content( $content ) {
		$this->content = sanitize_text_field( $content );
	}

	/**
	 * @return int|null
	 */
	public function get_document_id() {
		return $this->document_id;
	}

	/**
	 * @return null|string
	 */
	public function get_document_type() {
		return $this->document_type;
	}

	/**
	 * @return null|string
	 */
	public function get_language_from() {
		return $this->language_from;
	}

	/**
	 * @return null|string
	 */
	public function get_language_to() {
		return $this->language_to;
	}

	/**
	 * @return int|null
	 */
	public function get_job_id() {
		return $this->job_id;
	}

	/**
	 * @return WPML_TF_Feedback_Reviewer
	 */
	public function get_reviewer() {
		if ( ! isset( $this->reviewer ) ) {
			$this->set_reviewer( 0 );
		}

		return $this->reviewer;
	}

	/**
	 * @param int $reviewer_id
	 */
	public function set_reviewer( $reviewer_id ) {
		$this->reviewer = new WPML_TF_Feedback_Reviewer( $reviewer_id );
	}

	/**
	 * @return WPML_TF_Collection
	 */
	public function get_messages() {
		return $this->messages;
	}

	/**
	 * @param WPML_TF_Message $message
	 */
	public function add_message( WPML_TF_Message $message ) {
		$this->messages->add( $message );
	}

	/** @param WPML_TF_TP_Responses $tp_responses */
	public function set_tp_responses( WPML_TF_TP_Responses $tp_responses ) {
		$this->tp_responses = $tp_responses;
	}

	/** @return WPML_TF_TP_Responses */
	public function get_tp_responses() {
		return $this->tp_responses;
	}

	/**
	 * @return string|null
	 */
	public function get_text_status() {
		return $this->status->get_display_text();
	}

	/** @return array */
	public function get_next_status() {
		return $this->status->get_next_status();
	}

	/** @return bool */
	public function is_pending() {
		return $this->status->is_pending();
	}

	/**
	 * @return string
	 */
	public function get_document_flag_url() {
		return $this->document_information->get_flag_url( $this->get_language_to() );
	}

	/**
	 * @return string
	 */
	public function get_source_document_flag_url() {
		return $this->document_information->get_flag_url( $this->get_language_from() );
	}

	/**
	 * @return bool
	 */
	public function is_local_translation() {
		return $this->document_information->is_local_translation( $this->get_job_id() );
	}

	/**
	 * @return string
	 */
	public function get_translator_name() {
		return $this->document_information->get_translator_name( $this->get_job_id() );
	}

	/**
	 * @return array
	 */
	public function get_available_translators() {
		return $this->document_information->get_available_translators( $this->language_from, $this->language_to );
	}

	/**
	 * @param WPML_TF_Backend_Document_Information $document_information
	 */
	public function set_document_information( WPML_TF_Backend_Document_Information $document_information ) {
		$this->document_information = $document_information;
		$this->document_information->init( $this->get_document_id(), $this->get_document_type() );
	}

	/** @return WPML_TF_Backend_Document_Information */
	public function get_document_information() {
		return $this->document_information;
	}
}
