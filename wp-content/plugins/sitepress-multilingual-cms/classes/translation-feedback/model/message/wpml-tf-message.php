<?php

/**
 * Class WPML_TF_Message
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Message implements IWPML_TF_Data_Object {

	/** @var int $id */
	private $id;

	/** @var  int $feedback_id */
	private $feedback_id;

	/** @var  string $date_created */
	private $date_created;

	/** @var  string $content */
	private $content;

	/** @var  string $author_id */
	private $author_id;

	/**
	 * WPML_Translation_Feedback constructor.
	 *
	 * @param array $data
	 */
	public function __construct( $data = array() ) {
		$this->id           = array_key_exists( 'id', $data ) ? (int) $data['id'] : null;
		$this->feedback_id  = array_key_exists( 'feedback_id', $data ) ? (int) $data['feedback_id'] : null;
		$this->date_created = array_key_exists( 'date_created', $data )
			? sanitize_text_field( $data['date_created'] ) : null;
		$this->content      = array_key_exists( 'content', $data )
			? sanitize_text_field( $data['content'] ) : null;
		$this->author_id    = array_key_exists( 'author_id', $data )
			? (int) $data['author_id'] : null;
	}

	/**
	 * @return int|mixed|null
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return int|null
	 */
	public function get_feedback_id() {
		return $this->feedback_id;
	}

	/**
	 * @return mixed|null|string
	 */
	public function get_date_created() {
		return $this->date_created;
	}

	/**
	 * @return mixed|null|string
	 */
	public function get_content() {
		return $this->content;
	}

	/**
	 * @return int|null
	 */
	public function get_author_id() {
		return $this->author_id;
	}

	/** @return string */
	public function get_author_display_label() {
		$label = __( 'Translator', 'sitepress' );

		if ( user_can( $this->get_author_id(), 'manage_options' ) ) {
			$label = __( 'Admin', 'sitepress' );
		}

		return $label;
	}

	/** @return bool */
	public function author_is_current_user() {
		$current_user = wp_get_current_user();
		return $current_user->ID === $this->author_id;
	}
}
