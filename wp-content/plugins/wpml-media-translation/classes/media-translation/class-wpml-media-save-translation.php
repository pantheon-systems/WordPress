<?php

class WPML_Media_Save_Translation implements IWPML_Action {

	/**
	 * @var SitePress
	 */
	private $sitepress;
	/**
	 * @var wpdb
	 */
	private $wpdb;
	/**
	 * @var WPML_Media_File_Factory
	 */
	private $media_file_factory;
	/**
	 * @var array
	 */
	private $post_data;
	/**
	 * @var WPML_Translation_Element_Factory
	 */
	private $translation_element_factory;


	/**
	 * WPML_Media_Save_Translation constructor.
	 *
	 * @param SitePress $sitepress
	 * @param wpdb $wpdb
	 * @param WPML_Media_File_Factory $media_file_factory
	 * @param WPML_Translation_Element_Factory $translation_element_factory
	 */
	public function __construct( SitePress $sitepress, wpdb $wpdb, WPML_Media_File_Factory $media_file_factory, WPML_Translation_Element_Factory $translation_element_factory ) {
		$this->sitepress                   = $sitepress;
		$this->wpdb                        = $wpdb;
		$this->media_file_factory          = $media_file_factory;
		$this->translation_element_factory = $translation_element_factory;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_wpml_media_save_translation', array( $this, 'save_media_translation' ) );
	}

	public function save_media_translation() {

		if ( wp_verify_nonce( $_POST['wpnonce'], 'media-translation' ) ) {
			$post_array['ID']       = (int) $_POST['translated-attachment-id'];
			$original_attachment_id = (int) $_POST['original-attachment-id'];
			$translated_language    = sanitize_text_field( $_POST['translated-language'] );

			if ( isset( $_POST['translation']['title'] ) ) {
				$post_array['post_title'] = sanitize_text_field( $_POST['translation']['title'] );
			}
			if ( isset( $_POST['translation']['caption'] ) ) {
				$post_array['post_excerpt'] = sanitize_text_field( $_POST['translation']['caption'] );
			}
			if ( isset( $_POST['translation']['description'] ) ) {
				$post_array['post_content'] = sanitize_text_field( $_POST['translation']['description'] );
			}

			if ( $post_array['ID'] ) {
				$attachment_id = wp_update_post( $post_array );

				if ( $this->should_restore_media() ) {
					$this->restore_media_file( $attachment_id, $original_attachment_id, $translated_language );
				}

			} else {

				$post_array['post_type']      = 'attachment';
				$post_array['post_status']    = 'inherit';
				$post_array['guid']           = get_post_field( 'guid', $original_attachment_id );
				$post_array['post_mime_type'] = get_post_field( 'post_mime_type', $original_attachment_id );

				$attachment_id = $this->create_attachment_translation( $original_attachment_id, $post_array );

				$this->sitepress->set_element_language_details(
					$attachment_id,
					'post_attachment',
					$this->get_post_trid_value(),
					$this->get_post_lang_value()
				);

				if ( ! $this->has_media_upload() ) {
					$this->copy_attached_file_info_from_original( $attachment_id, $original_attachment_id );
				}

				$this->mark_media_as_not_translated( $attachment_id, $translated_language );

			}
			$translation_status = WPML_Media_Translation_Status::NEEDS_MEDIA_TRANSLATION;

			if ( $this->has_media_upload() ) {
				$this->update_media_file( $attachment_id, $original_attachment_id, $translated_language );
				$translation_status = WPML_Media_Translation_Status::TRANSLATED;
			}

			if ( isset( $_POST['translation']['alt-text'] ) ) {
				update_post_meta(
					$attachment_id,
					'_wp_attachment_image_alt',
					sanitize_text_field( $_POST['translation']['alt-text'] )
				);
			}

			if ( 0 === strpos( get_post_field( 'post_mime_type', $original_attachment_id ), 'image/' ) ) {
				$translated_thumb = wp_get_attachment_thumb_url( $attachment_id );
				$original_thumb   = wp_get_attachment_thumb_url( $original_attachment_id );
				$media_file_is_translated = $translated_thumb !== $original_thumb;
			} else {
				$media_file_is_translated = get_attached_file( $attachment_id ) !== get_attached_file( $original_attachment_id );
				$translated_thumb = wp_mime_type_icon( $original_attachment_id );
			}

			$media_usage = get_post_meta( $original_attachment_id, WPML_Media_Usage::FIELD_NAME, true );
			$posts_list  = array();
			if ( isset( $media_usage['posts'] ) ) {
				foreach ( $media_usage['posts'] as $post_id ) {
					$post_element     = $this->translation_element_factory->create( $post_id, 'post' );
					$post_translation = $post_element->get_translation( $translated_language );
					if ( $post_translation ) {
						$posts_list[] = array(
							'url'   => get_edit_post_link( $post_translation->get_id() ),
							'title' => get_post_field( 'post_title', $post_translation->get_id() )
						);
					}
				}
			}

			if ( isset( $media_usage['posts'] ) && $this->should_restore_media() ) {
				do_action( 'wpml_restored_media_file_translation', $attachment_id, $media_usage['posts'] );
			}

			$response = array(
				'attachment_id' => $attachment_id,
				'thumb'         => $media_file_is_translated ? $translated_thumb : false,
				'status'        => $translation_status,
				'usage'         => $posts_list
			);

			wp_send_json_success( $response );
		} else {
			wp_send_json_error( array( 'error' => __( 'Invalid nonce', 'wpml-media' ) ) );
		}
	}

	private function has_media_upload() {
		return ! empty ( $_POST['update-media-file'] );
	}

	private function should_restore_media() {
		return ! empty( $_POST['restore-media'] );
	}

	/**
	 * @param int $original_attachment_id
	 * @param array $post_array
	 *
	 * @return int
	 */
	private function create_attachment_translation( $original_attachment_id, $post_array ) {

		$post_element = $this->translation_element_factory->create( $original_attachment_id, 'post' );
		$this->set_post_trid_value( $post_element->get_trid() );
		$this->set_post_lang_value( $_POST['translated-language'] );

		add_filter( 'wpml_tm_save_post_trid_value', array( $this, 'get_post_trid_value' ) );
		add_filter( 'wpml_tm_save_post_lang_value', array( $this, 'get_post_lang_value' ) );

		$attachment_id = wp_insert_post( $post_array );

		remove_filter( 'wpml_tm_save_post_trid_value', array( $this, 'get_post_trid_value' ) );
		remove_filter( 'wpml_tm_save_post_lang_value', array( $this, 'get_post_lang_value' ) );

		return $attachment_id;
	}

	private function set_post_trid_value( $value ) {
		$this->post_data['post_icl_trid'] = $value;
	}

	public function get_post_trid_value() {
		return $this->post_data['post_icl_trid'];
	}

	private function set_post_lang_value( $value ) {
		$this->post_data['post_icl_language'] = $value;
	}

	public function get_post_lang_value() {
		return $this->post_data['post_icl_language'];
	}

	private function restore_media_file( $attachment_id, $original_attachment_id, $language ) {

		$media_file = $this->media_file_factory->create( $attachment_id );
		$media_file->delete();

		$this->copy_attached_file_info_from_original( $attachment_id, $original_attachment_id );
		$this->mark_media_as_not_translated( $original_attachment_id, $language );
	}

	private function copy_attached_file_info_from_original( $attachment_id, $original_attachment_id ) {
		$meta_keys = array(
			'_wp_attachment_metadata',
			'_wp_attached_file',
			'_wp_attachment_backup_sizes'
		);
		foreach ( $meta_keys as $meta_key ) {
			update_post_meta( $attachment_id, $meta_key,
				get_post_meta( $original_attachment_id, $meta_key, true ) );
		}

		/**
		 * Fires after attachment post meta is copied
		 *
		 * @since 4.1.0
		 *
		 * @param int $original_attachment_id The ID of the source/original attachment.
		 * @param int $attachment_id          The ID of the duplicated attachment.
		 */
		do_action( 'wpml_after_copy_attached_file_postmeta', $original_attachment_id, $attachment_id );
	}

	private function mark_media_as_not_translated( $attachment_id, $language ) {
		update_post_meta( $attachment_id, WPML_Media_Translation_Status::STATUS_PREFIX . $language, WPML_Media_Translation_Status::NEEDS_MEDIA_TRANSLATION );
	}

	private function mark_media_as_translated( $attachment_id, $language ) {
		update_post_meta( $attachment_id, WPML_Media_Translation_Status::STATUS_PREFIX . $language, WPML_Media_Translation_Status::TRANSLATED );
	}

	/**
	 * @param array $file
	 *
	 * @return array
	 */
	private function get_attachment_post_data( $file ) {
		$postarr = array(
			'post_mime_type'    => $file['type'],
			'guid'              => $file['url'],
			'post_modified'     => current_time( 'mysql' ),
			'post_modified_gmt' => current_time( 'mysql', 1 )
		);

		return $postarr;
	}

	private function update_media_file( $attachment_id, $original_attachment_id, $translated_language_code ) {
		$transient_key = WPML_Media_Attachment_Image_Update::TRANSIENT_FILE_UPLOAD_PREFIX . $original_attachment_id . '_' . $translated_language_code;

		$media_file_upload = get_transient( $transient_key );
		if ( $media_file_upload ) {

			$file = $media_file_upload['upload'];

			// delete previous media file + sizes
			$media_file = $this->media_file_factory->create( $attachment_id );
			$media_file->delete();

			$post_data = $this->get_attachment_post_data( $file );
			$this->wpdb->update( $this->wpdb->posts, $post_data, array( 'ID' => $attachment_id ) );
			update_attached_file( $attachment_id, $file['file'] );

			/**
			 * Fires after attached file is updated
			 *
			 * @since 4.1.0
			 *
			 * @param int $attachment_id The ID of uploaded attachment.
			 * @param array $file {
			 *     Uploaded file data.
			 *
			 *     @type string file Absolute path to file.
			 *
			 *     @type string url File URL.
			 *
			 *     @type string type File type.
			 * }
			 * @param string $translated_language_code Attachment language code.
			 */
			do_action( 'wpml_updated_attached_file', $attachment_id, $file, $translated_language_code );

			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file['file'] ) );

			$this->mark_media_as_translated( $original_attachment_id, $translated_language_code );
			do_action( 'wpml_added_media_file_translation', $original_attachment_id, $file, $translated_language_code );

			delete_transient( $transient_key );
		}
	}

}