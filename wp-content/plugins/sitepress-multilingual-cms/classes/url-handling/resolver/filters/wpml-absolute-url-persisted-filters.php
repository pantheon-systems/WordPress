<?php

class WPML_Absolute_Url_Persisted_Filters implements IWPML_Action {

	/** @var WPML_Absolute_Url_Persisted $url_persisted */
	private $url_persisted;

	public function __construct( WPML_Absolute_Url_Persisted $url_persisted ) {
		$this->url_persisted = $url_persisted;
	}

	public function add_hooks() {
		add_filter( 'wp_insert_post_data', array( $this, 'wp_insert_post_data_filter' ), 10, 2 );
		add_action( 'delete_post', array( $this, 'delete_post_action' ) );

		add_filter( 'wp_update_term_data', array( $this, 'wp_update_term_data_filter' ), 10, 3 );
		add_action( 'pre_delete_term', array( $this, 'pre_delete_term_action' ), 10, 2 );

		add_filter( 'rewrite_rules_array', array( $this, 'rewrite_rules_array_filter' ) );
	}

	/**
	 * @param array $data
	 * @param array $postarr
	 *
	 * @return mixed
	 */
	public function wp_insert_post_data_filter( $data, $postarr ) {
		if ( isset( $postarr['ID'] ) && $postarr['ID'] ) {
			$this->delete_persisted_post_url( $postarr['ID'] );
		}

		return $data;
	}

	/** @param int $post_id */
	public function delete_post_action( $post_id ) {
		$this->delete_persisted_post_url( $post_id );
	}

	/** @param int $post_id */
	private function delete_persisted_post_url( $post_id ) {
		$url_before_update = get_permalink( $post_id );
		$this->url_persisted->delete( $url_before_update );
	}

	/**
	 * @param array  $data
	 * @param int    $term_id
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	public function wp_update_term_data_filter( $data, $term_id, $taxonomy ) {
		$this->delete_persisted_term_url( $term_id, $taxonomy );
		return $data;
	}

	/**
	 * @param int    $term_id
	 * @param string $taxonomy
	 */
	public function pre_delete_term_action( $term_id, $taxonomy ) {
		$this->delete_persisted_term_url( $term_id, $taxonomy );
	}

	/**
	 * @param int    $term_id
	 * @param string $taxonomy
	 */
	private function delete_persisted_term_url( $term_id, $taxonomy ) {
		$url_before_update = get_term_link( $term_id, $taxonomy );
		$this->url_persisted->delete( $url_before_update );
	}

	/**
	 * @param array $rules
	 *
	 * @return array
	 */
	public function rewrite_rules_array_filter( $rules ) {
		$this->url_persisted->reset();
		return $rules;
	}
}
