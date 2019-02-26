<?php

class WPML_Media_Translation_Status implements IWPML_Action {

	const NOT_TRANSLATED = 'media-not-translated';
	const IN_PROGRESS = 'in-progress';
	const TRANSLATED = 'media-translated';
	const NEEDS_MEDIA_TRANSLATION = 'needs-media-translation';

	const STATUS_PREFIX = '_translation_status_';

	/**
	 * @var SitePress
	 */
	private $sitepress;

	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		add_action( 'wpml_tm_send_post_jobs', array( $this, 'set_translation_status_in_progress' ), 10, 5 );
		add_action( 'wpml_pro_translation_completed', array( $this, 'save_bundled_media_translation' ), 10, 3 );
	}

	public function set_translation_status_in_progress( $item_type_name, $item_type, $type_basket_items, $translators, $batch_options ) {
		foreach ( $type_basket_items as $item ) {
			if ( isset( $item['media-translation'] ) ) {
				foreach ( $item['media-translation'] as $attachment_id ) {
					foreach ( array_keys( $item['to_langs'] ) as $lang ) {
						$this->set_status( $attachment_id, $lang, self::IN_PROGRESS );
					}
				}

			}
		}
	}

	private function set_status( $attachment_id, $language, $status ) {
		update_post_meta( $attachment_id, self::STATUS_PREFIX . $language, $status );
	}

	public function save_bundled_media_translation( $new_post_id, $fields, $job ) {

		$media_translations  = $this->get_media_translations( $job );
		$translation_package = new WPML_Element_Translation_Package();

		foreach ( $media_translations as $attachment_id => $translation_data ) {
			$attachment_translation_id = $this->save_attachment_translation(
				$attachment_id,
				$translation_data,
				$translation_package,
				$job->language_code
			);
			if ( $this->should_translate_media_image( $job, $attachment_id ) ) {
				$this->set_status( $attachment_id, $job->language_code, self::NEEDS_MEDIA_TRANSLATION );
			}
		}

	}

	private function should_translate_media_image( $job, $attachment_id ) {
		foreach ( $job->elements as $element ) {
			if ( 'should_translate_media_image_' . $attachment_id === $element->field_type && $element->field_data ) {
				return true;
			}
		}

		return false;
	}

	private function get_media_translations( $job ) {

		$media = array();

		$media_field_regexp = '#^media_([0-9]+)_([a-z_]+)$#';
		foreach ( $job->elements as $element ) {
			if ( preg_match( $media_field_regexp, $element->field_type, $matches ) ) {
				list( , $attachment_id, $media_field ) = $matches;
				$media[ $attachment_id ][ $media_field ] = $element;
			}
		}

		return $media;
	}

    /**
     * @param int $attachment_id
     * @param array $translation_data
     * @param WPML_Element_Translation_Package $translation_package
     * @param string $language
     * @return bool|int|WP_Error
     */
	private function save_attachment_translation( $attachment_id, $translation_data, $translation_package, $language ) {

		$postarr  = array();
		$alt_text = null;

		foreach ( $translation_data as $field => $data ) {

			$translated_value = $translation_package->decode_field_data(
				$data->field_data_translated,
				$data->field_format
			);

			if ( 'alt_text' === $field ) {
				$alt_text = $translated_value;
			} else {

				switch ( $field ) {
					case 'title':
						$wp_post_field = 'post_title';
						break;
					case 'caption':
						$wp_post_field = 'post_excerpt';
						break;
					case 'description':
						$wp_post_field = 'post_content';
						break;
					default:
						$wp_post_field = '';

				}

				if ( $wp_post_field ) {
					$postarr[ $wp_post_field ] = $translated_value;
				}

			}

		}

		$post_element              = new WPML_Post_Element( $attachment_id, $this->sitepress );
		$attachment_translation    = $post_element->get_translation( $language );
		$attachment_translation_id = null !== $attachment_translation ? $attachment_translation->get_id() : false;

		if ( $attachment_translation_id ) {
			$postarr['ID'] = $attachment_translation_id;
			wp_update_post( $postarr );
		} else {
			$postarr['post_type']      = 'attachment';
			$postarr['post_status']    = 'inherit';
			$postarr['guid']           = get_post_field( 'guid', $attachment_id );
			$postarr['post_mime_type'] = get_post_field( 'post_mime_type', $attachment_id );

			$attachment_translation_id = wp_insert_post( $postarr );

			$this->sitepress->set_element_language_details( $attachment_translation_id, 'post_attachment', $post_element->get_trid(), $language );

			$this->copy_attached_file_info_from_original( $attachment_translation_id, $attachment_id );

		}

		if ( null !== $alt_text ) {
			update_post_meta( $attachment_translation_id, '_wp_attachment_image_alt', $alt_text );
		}

		return $attachment_translation_id;
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
	}


}