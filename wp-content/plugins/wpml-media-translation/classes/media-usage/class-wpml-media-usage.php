<?php

class WPML_Media_Usage {

	const FIELD_NAME = '_wpml_media_usage';

	/**
	 * @var int
	 */
	private $attachment_id;
	/**
	 * @var array
	 */
	private $usage;

	/**
	 * @param int $attachment_id
	 */
	public function __construct( $attachment_id ) {
		$this->attachment_id = $attachment_id;

		$usage       = get_post_meta( $this->attachment_id, self::FIELD_NAME, true );
		$this->usage = empty( $usage ) ? array() : $usage;
	}

	/**
	 * @return array
	 */
	public function get_posts() {
		return empty( $this->usage['posts'] ) ? array() : $this->usage['posts'];
	}

	/**
	 * @param int $post_id
	 */
	public function add_post( $post_id ) {
		$posts                = $this->get_posts();
		$posts[]              = $post_id;
		$this->usage['posts'] = array_unique( $posts );
		$this->update_usage();
	}

	/**
	 * @param int $post_id
	 */
	public function remove_post( $post_id ) {
		$this->usage['posts'] = array_values( array_diff( (array) $this->usage['posts'], array( $post_id ) ) );
		$this->update_usage();
	}

	private function update_usage() {
		update_post_meta( $this->attachment_id, self::FIELD_NAME, $this->usage );
	}

}