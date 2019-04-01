<?php

/**
 * Class WPML_Troubleshoot_Sync_Posts_Taxonomies
 */
class WPML_Troubleshoot_Sync_Posts_Taxonomies {

	const BATCH_SIZE = 5;

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var WPML_Term_Translation_Utils $term_translation_utils */
	private $term_translation_utils;

	public function __construct( SitePress $sitePress, WPML_Term_Translation_Utils $term_translation_utils ) {
		$this->sitepress              = $sitePress;
		$this->term_translation_utils = $term_translation_utils;
	}

	public function run() {
		if ( ! array_key_exists( 'post_type', $_POST ) || ! array_key_exists( 'batch_number', $_POST ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Some parameters are missing for this request.', 'sitepress' ) ) );
			return;
		}

		$post_type    = filter_var( $_POST['post_type'], FILTER_SANITIZE_STRING );
		$batch_number = (int) filter_var( $_POST['batch_number'], FILTER_SANITIZE_NUMBER_INT );

		$posts = $this->get_posts_batch( $post_type, $batch_number );
		$this->synchronize_batch( $posts );

		$new_batch_number = $batch_number + 1;

		$response_data = array(
			'post_type'    => $post_type,
			'batch_number' => $new_batch_number,
			'message'      => sprintf( esc_html__( 'Running now batch #%d', 'sitepress' ), $new_batch_number ),
		);

		if ( count( $posts ) < self::BATCH_SIZE ) {
			$total_posts_processed = ( $batch_number * self::BATCH_SIZE ) + count( $posts );

			$response_data['completed'] = true;
			$response_data['message']   = sprintf( esc_html__( 'Completed: %1$d posts were processed for "%2$s".', 'sitepress' ), $total_posts_processed, $post_type );
		}

		wp_send_json_success( $response_data );
	}

	/**
	 * @param string $type
	 * @param int    $batch_number
	 *
	 * @return array
	 */
	private function get_posts_batch( $type, $batch_number ) {
		$this->sitepress->switch_lang( $this->sitepress->get_default_language() );

		$args = array(
			'post_type'      => $type,
			'offset'         => $batch_number * self::BATCH_SIZE,
			'order_by'       => 'ID',
			'order'          => 'ASC',
			'posts_per_page' => self::BATCH_SIZE,
			'suppress_filters' => false,
		);

		$posts = get_posts( $args );

		$this->sitepress->switch_lang();

		return $posts;
	}

	/**
	 * @param array $posts
	 */
	private function synchronize_batch( $posts ) {
		$active_languages = $this->sitepress->get_active_languages();

		foreach ( $active_languages as $language_code => $active_language ) {

			foreach ( $posts as $post ) {
				$this->term_translation_utils->sync_terms( $post->ID, $language_code );
			}
		}
	}
}