<?php

/**
 * Class WPML_Media_Attachment_Image_Update
 * Allows adding a custom image to a translated attachment
 */
class WPML_Media_Attachment_Image_Update implements IWPML_Action {

	const TRANSIENT_FILE_UPLOAD_PREFIX = 'wpml_media_file_update_';

	/**
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * WPML_Media_Attachment_Image_Update constructor.
	 *
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_wpml_media_upload_file', array( $this, 'handle_upload' ) );
	}

	public function handle_upload() {
		if ( $this->is_valid_action() ) {

			$original_attachment_id = (int) $_POST['original-attachment-id'];
			$attachment_id          = (int) $_POST['attachment-id'];
			$file_array             = $_FILES['file'];
			$target_language        = $_POST['language'];

			$thumb_path = '';
			$thumb_url  = '';

			$upload_overrides = apply_filters( 'wpml_media_wp_upload_overrides', array( 'test_form' => false ) );
			$file = wp_handle_upload( $file_array, $upload_overrides );

			if ( ! isset( $file['error'] ) ) {

				if( 0 === strpos( $file['type'], 'image/' ) ){

					$editor = wp_get_image_editor( $file['file'] );

					if ( ! is_wp_error( $editor ) ) {

						$resizing = $editor->resize( 150, 150, true );
						if ( is_wp_error( $resizing ) ) {
							wp_send_json_error( $resizing->get_error_message() );
						} else {
							$thumb = $editor->save();
						}

						if ( ! empty( $thumb ) ) {
							$uploads_dir = wp_get_upload_dir();

							$thumb_url = $uploads_dir['baseurl'] . $uploads_dir['subdir'] . '/' . $thumb['file'];
							$thumb_path = $thumb['path'];
						}

					} else {
						wp_send_json_error( __( 'Failed to load the image editor', 'wpml-media' ) );
					}

				} else {
					$thumb_url = wp_mime_type_icon( $original_attachment_id );
				}

				set_transient(
					self::TRANSIENT_FILE_UPLOAD_PREFIX . $original_attachment_id . '_' . $target_language,
					array(
						'upload' => $file,
						'thumb'  => $thumb_path
					),
					HOUR_IN_SECONDS
				);

				wp_send_json_success( array(
					'attachment_id' => $attachment_id,
					'thumb'         => $thumb_url,
					'name'          => basename( $file['file'] )
				) );

			} else {
				wp_send_json_error( $file['error'] );
			}

		} else {
			wp_send_json_error( 'invalid action' );
		}
	}

	private function is_valid_action() {
		$is_attachment_id = isset( $_POST['attachment-id'] );
		$is_post_action   = isset( $_POST['action'] ) && 'wpml_media_upload_file' === $_POST['action'];

		return $is_attachment_id && $is_post_action && wp_verify_nonce( $_POST['wpnonce'], 'media-translation' );
	}

}