<?php

/**
 * Class WPML_Media_Batch_Url_Translation
 */
abstract class WPML_Media_Batch_Url_Translation {

	const BATCH_SIZE = 10;

	const BATCH_SIZE_FACTOR_ALL_MEDIA = 1;
	const BATCH_SIZE_FACTOR_SPECIFIC_MEDIA = 10;

	/**
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * WPML_Media_Batch_Url_Translation constructor.
	 *
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_' . $this->get_ajax_action(), array( $this, 'run_batch' ) );
	}

	public function run_batch() {
		$offset        = isset( $_POST['offset'] ) ? (int) $_POST['offset'] : 0;
		$attachment_id = isset( $_POST['attachment_id'] ) ? (int) $_POST['attachment_id'] : 0;
		$all_media     = ! empty( $_POST['global'] );

		if ( $all_media ) {
			$number_of_elements_left = $this->process_batch( $offset );
		} else {
			$number_of_elements_left = $this->process_batch_for_selected_media( $offset, $attachment_id );
		}
		$batch_size_factor = $all_media ? self::BATCH_SIZE_FACTOR_ALL_MEDIA : self::BATCH_SIZE_FACTOR_SPECIFIC_MEDIA;
		$response = array(
			'offset'   => $offset + $this->get_batch_size( $batch_size_factor ),
			'continue' => (int) ( $number_of_elements_left > 0 ),
			'message'  => $this->get_response_message( $number_of_elements_left )
		);

		wp_send_json_success( $response );
	}

	/**
	 * @param int $number_of_elements_left
	 *
	 * @return string
	 */
	abstract protected function get_response_message( $number_of_elements_left );

	/**
	 * @param int $offset
	 *
	 * @return int
	 */
	abstract protected function process_batch( $offset );

	/**
	 * @param int $offset
	 * @param int $attachment_id
	 *
	 * @return int
	 */
	abstract protected function process_batch_for_selected_media( $offset, $attachment_id );

	/**
	 * @return array
	 */
	abstract protected function get_ajax_error_message();

	/**
	 * @param int $batch_size_factor
	 *
	 * @return int
	 */
	protected function get_batch_size( $batch_size_factor = self::BATCH_SIZE_FACTOR_ALL_MEDIA ){
		return $batch_size_factor * self::BATCH_SIZE;
	}

	/**
	 * @return string
	 */
	abstract protected function get_ajax_action();

}