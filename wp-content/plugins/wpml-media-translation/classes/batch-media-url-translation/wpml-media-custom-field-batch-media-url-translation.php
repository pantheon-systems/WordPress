<?php

/**
 * Class WPML_Media_Custom_Field_Batch_Url_Translation
 */
class WPML_Media_Custom_Field_Batch_Url_Translation extends WPML_Media_Batch_Url_Translation implements IWPML_Action {

	const AJAX_ACTION = 'wpml_media_translate_media_url_in_custom_fields';

	/**
	 * @var WPML_Media_Custom_Field_Images_Translation
	 */
	private $custom_field_translation;
	/**
	 * @var array
	 */
	private $translatable_custom_fields;

	/**
	 * WPML_Media_Custom_Field_Batch_Url_Translation constructor.
	 *
	 * @param WPML_Media_Custom_Field_Images_Translation $custom_field_translation
	 * @param wpdb $wpdb
	 * @param array $translatable_custom_fields
	 */
	public function __construct(
		WPML_Media_Custom_Field_Images_Translation $custom_field_translation,
		wpdb $wpdb,
		array $translatable_custom_fields
	) {
		parent::__construct( $wpdb );
		$this->custom_field_translation   = $custom_field_translation;
		$this->translatable_custom_fields = $translatable_custom_fields;
	}

	/**
	 * @return string
	 */
	protected function get_ajax_action() {
		return self::AJAX_ACTION;
	}

	public static function is_ajax_request(){
		return isset( $_POST['action'] ) && self::AJAX_ACTION === $_POST['action'];
	}

	/**
	 * @param int $number_of_custom_fields_left
	 *
	 * @return string
	 */
	protected function get_response_message( $number_of_custom_fields_left ) {
		return sprintf(
			__( 'Translating media urls in custom field translations: %s', 'wpml-media' ),
			$number_of_custom_fields_left > 0 ?
				sprintf( __( '%d left', 'wpml-media' ), $number_of_custom_fields_left ) :
				__( 'done!', 'wpml-media' )
		);
	}

	protected function get_ajax_error_message() {
		return array(
			'key'   => 'wpml_media_batch_urls_update_error_custom_fields',
			'value' => esc_js( __( 'Translating media urls in custom fields translations failed: Please try again (%s)', 'wpml-media' ) )
		);
	}

	protected function process_batch( $offset ) {

		if ( $this->translatable_custom_fields ) {
			$translatable_custom_fields_where_in = wpml_prepare_in( $this->translatable_custom_fields );
			$custom_fields = $this->wpdb->get_results( "
				SELECT SQL_CALC_FOUND_ROWS t.element_id AS post_id, p.meta_id, p.meta_key, p.meta_value  
				FROM {$this->wpdb->prefix}icl_translations t 
					JOIN {$this->wpdb->prefix}postmeta p ON t.element_id = p.post_id
				WHERE t.element_type LIKE 'post_%' 
					AND t.element_type <> 'post_attachment' 
					AND t.source_language_code IS NULL
					AND p.meta_key IN ({$translatable_custom_fields_where_in})
				ORDER BY t.element_id ASC
				LIMIT {$offset}, " . self::BATCH_SIZE );

			$number_of_all_custom_fields = (int) $this->wpdb->get_var( "SELECT FOUND_ROWS()" );

			foreach ( $custom_fields as $custom_field ) {
				$this->custom_field_translation->translate_images(
					$custom_field->meta_id,
					$custom_field->post_id,
					$custom_field->meta_key,
					$custom_field->meta_value
				);
			}

		} else {
			$number_of_all_custom_fields = 0;
		}

		return $number_of_all_custom_fields - $offset - self::BATCH_SIZE;
	}

	protected function process_batch_for_selected_media( $offset, $attachment_id ){
		$media_url = wpml_like_escape( wp_get_attachment_url( $attachment_id ) );
		if ( ! $media_url ) {
			return 0;
		}
		preg_match( "/(.+)\.([a-z]+)$/", $media_url, $match );
		$media_url_no_extension = wpml_like_escape( $match[1] );
		$extension              = wpml_like_escape( $match[2] );

		$batch_size = $this->get_batch_size( parent::BATCH_SIZE_FACTOR_SPECIFIC_MEDIA );
		if ( $this->translatable_custom_fields ) {
			$translatable_custom_fields_where_in = wpml_prepare_in( $this->translatable_custom_fields );
			$custom_fields = $this->wpdb->get_results( "
				SELECT SQL_CALC_FOUND_ROWS t.element_id AS post_id, p.meta_id, p.meta_key, p.meta_value  
				FROM {$this->wpdb->prefix}icl_translations t 
					JOIN {$this->wpdb->prefix}postmeta p ON t.element_id = p.post_id
				WHERE t.element_type LIKE 'post_%' 
					AND t.element_type <> 'post_attachment' 
					AND t.source_language_code IS NULL
					AND p.meta_key IN ({$translatable_custom_fields_where_in})
					AND (
						p.meta_value LIKE '%{$media_url}%' OR 
						p.meta_value LIKE '%{$media_url_no_extension}-%x%.{$extension}%'
					) 
				ORDER BY t.element_id ASC
				LIMIT {$offset}, " . $batch_size );

			$number_of_all_custom_fields = (int) $this->wpdb->get_var( "SELECT FOUND_ROWS()" );

			foreach ( $custom_fields as $custom_field ) {
				$this->custom_field_translation->translate_images(
					$custom_field->meta_id,
					$custom_field->post_id,
					$custom_field->meta_key,
					$custom_field->meta_value
				);
			}

		} else {
			$number_of_all_custom_fields = 0;
		}

		return $number_of_all_custom_fields - $offset - $batch_size;

	}

}