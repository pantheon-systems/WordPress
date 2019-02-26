<?php

class WPML_Elementor_DB {

	/**
	 * @var \Elementor\DB
	 */
	private $elementor_db;

	// @codingStandardsIgnoreLine
	public function __construct( \Elementor\DB $elementor_db ) {
		$this->elementor_db = $elementor_db;
	}

	/**
	 * @param int $post_id
	 */
	public function save_plain_text( $post_id ) {
		$this->elementor_db->save_plain_text( $post_id );
	}
}
