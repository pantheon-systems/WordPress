<?php

/**
 * Class WPML_Media_Post_Batch_Url_Translation
 */
class WPML_Media_Post_Batch_Url_Translation extends WPML_Media_Batch_Url_Translation implements IWPML_Action {

	const AJAX_ACTION = 'wpml_media_translate_media_url_in_posts';

	/**
	 * @var WPML_Media_Post_Images_Translation
	 */
	private $post_image_translation;

	/**
	 * WPML_Media_Post_Batch_Url_Translation constructor.
	 *
	 * @param WPML_Media_Post_Images_Translation $post_image_translation
	 * @param wpdb $wpdb
	 */
	public function __construct( WPML_Media_Post_Images_Translation $post_image_translation, wpdb $wpdb ) {
		parent::__construct( $wpdb );
		$this->post_image_translation = $post_image_translation;
		$this->wpdb                   = $wpdb;
	}

	/**
	 * @return string
	 */
	protected function get_ajax_action() {
		return self::AJAX_ACTION;
	}

	public static function is_ajax_request() {
		return isset( $_POST['action'] ) && self::AJAX_ACTION === $_POST['action'];
	}

	/**
	 * @param int $number_of_posts_left
	 *
	 * @return string
	 */
	protected function get_response_message( $number_of_posts_left ) {
		return sprintf(
			__( 'Translating media urls in post translations: %s', 'wpml-media' ),
			$number_of_posts_left > 0 ?
				sprintf( __( '%d left', 'wpml-media' ), $number_of_posts_left ) :
				__( 'done!', 'wpml-media' )
		);
	}

	protected function get_ajax_error_message() {
		return array(
			'key'   => 'wpml_media_batch_urls_update_errors_posts',
			'value' => esc_js( __( 'Translating media urls in posts translations failed: Please try again (%s)', 'wpml-media' ) )
		);
	}

	/**
	 * @param int $offset
	 *
	 * @return int
	 */
	protected function process_batch( $offset ) {

		$posts = $this->wpdb->get_col( "
			SELECT SQL_CALC_FOUND_ROWS element_id AS id 
			FROM {$this->wpdb->prefix}icl_translations 
			WHERE element_type LIKE 'post_%' 
				AND element_type <> 'post_attachment' 
				AND source_language_code IS NULL
			ORDER BY element_id ASC
			LIMIT {$offset}, " . self::BATCH_SIZE );

		$number_of_all_posts = (int) $this->wpdb->get_var( "SELECT FOUND_ROWS()" );

		foreach ( $posts as $post_id ) {
			$this->post_image_translation->translate_images( $post_id );
		}

		return $number_of_all_posts - $offset - self::BATCH_SIZE;
	}

	protected function process_batch_for_selected_media( $offset, $attachment_id ) {
		$media_url = wpml_like_escape( wp_get_attachment_url( $attachment_id ) );
		if ( ! $media_url ) {
			return 0;
		}
		preg_match( "/(.+)\.([a-z]+)$/", $media_url, $match );
		$media_url_no_extension = wpml_like_escape( $match[1] );
		$extension              = wpml_like_escape( $match[2] );

		$batch_size = $this->get_batch_size( parent::BATCH_SIZE_FACTOR_SPECIFIC_MEDIA );

		$posts      = $this->wpdb->get_col( "
			SELECT SQL_CALC_FOUND_ROWS element_id AS id 
			FROM {$this->wpdb->prefix}icl_translations t
			JOIN {$this->wpdb->posts} p ON t.element_id = p.ID
			WHERE element_type LIKE 'post_%' 
				AND element_type <> 'post_attachment' 
				AND source_language_code IS NULL
				AND (
					post_content LIKE '%{$media_url}%' OR 
					post_content LIKE '%{$media_url_no_extension}-%x%.{$extension}%'
				) 
			ORDER BY element_id ASC
			LIMIT {$offset}, " . $batch_size );

		$number_of_all_posts = (int) $this->wpdb->get_var( "SELECT FOUND_ROWS()" );

		foreach ( $posts as $post_id ) {
			$this->post_image_translation->translate_images( $post_id );
		}

		return $number_of_all_posts - $offset - $batch_size;
	}

}