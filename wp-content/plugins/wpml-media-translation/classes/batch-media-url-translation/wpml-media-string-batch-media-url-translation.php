<?php

/**
 * Class WPML_Media_String_Batch_Url_Translation
 */
class WPML_Media_String_Batch_Url_Translation extends WPML_Media_Batch_Url_Translation implements IWPML_Action {

	const BATCH_SIZE = 500;
	const AJAX_ACTION = 'wpml_media_translate_media_url_in_strings';

	/**
	 * @var WPML_ST_String_Factory
	 */
	private $string_factory;

	/**
	 * WPML_Media_String_Batch_Url_Translation constructor.
	 *
	 * @param wpdb $wpdb
	 * @param WPML_ST_String_Factory $string_factory
	 */
	public function __construct(
		wpdb $wpdb,
		WPML_ST_String_Factory $string_factory
	) {
		parent::__construct( $wpdb );
		$this->string_factory = $string_factory;
	}

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
	protected function get_ajax_action() {
		return self::AJAX_ACTION;
	}

	public static function is_ajax_request() {
		return isset( $_POST['action'] ) && self::AJAX_ACTION === $_POST['action'];
	}

	/**
	 * @param int $number_of_strings_left
	 *
	 * @return string
	 */
	protected function get_response_message( $number_of_strings_left ) {
		return sprintf(
			__( 'Translating media urls in string translations: %s', 'wpml-media' ),
			$number_of_strings_left > 0 ?
				sprintf( __( '%d left', 'wpml-media' ), $number_of_strings_left ) :
				__( 'done!', 'wpml-media' )
		);
	}

	protected function get_ajax_error_message() {
		return array(
			'key'   => 'wpml_media_batch_urls_update_error_strings',
			'value' => esc_js( __( 'Translating media urls in string translations failed: Please try again (%s)', 'wpml-media' ) )
		);
	}

	/**
	 * @param int $offset
	 *
	 * @return int
	 */
	protected function process_batch( $offset ) {

		$original_strings = $this->wpdb->get_col( "
			SELECT SQL_CALC_FOUND_ROWS id, language 
			FROM {$this->wpdb->prefix}icl_strings 
			ORDER BY id ASC 
			LIMIT {$offset}, " . self::BATCH_SIZE
		);

		$number_of_all_strings = (int) $this->wpdb->get_var( 'SELECT FOUND_ROWS()' );

		foreach ( $original_strings as $string_id ) {
			$string              = $this->string_factory->find_by_id( $string_id );
			$string_translations = $string->get_translations();
			foreach ( $string_translations as $string_translation ) {
				if ( $string_translation->value ) {
					$string->set_translation( $string_translation->language, $string_translation->value );
				}
			}
		}

		return $number_of_all_strings - $offset - self::BATCH_SIZE;
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

		$original_strings = $this->wpdb->get_col( "
			SELECT SQL_CALC_FOUND_ROWS id, language 
			FROM {$this->wpdb->prefix}icl_strings
			WHERE (
					value LIKE '%{$media_url}%' OR 
					value LIKE '%{$media_url_no_extension}-%x%.{$extension}%'
				) 
			ORDER BY id ASC 
			LIMIT {$offset}, " . $batch_size
		);

		$number_of_all_strings = (int) $this->wpdb->get_var( 'SELECT FOUND_ROWS()' );

		foreach ( $original_strings as $string_id ) {
			$string              = $this->string_factory->find_by_id( $string_id );
			$string_translations = $string->get_translations();
			foreach ( $string_translations as $string_translation ) {
				if ( $string_translation->value ) {
					$string->set_translation( $string_translation->language, $string_translation->value );
				}
			}
		}

		return $number_of_all_strings - $offset - $batch_size;
	}

}