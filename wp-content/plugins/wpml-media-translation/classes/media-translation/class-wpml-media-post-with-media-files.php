<?php

class WPML_Media_Post_With_Media_Files {

	/**
	 * @var int
	 */
	private $post_id;
	/**
	 * @var WPML_Media_Img_Parse
	 */
	private $media_parser;
	/**
	 * @var WPML_Media_Attachment_By_URL_Factory
	 */
	private $attachment_by_url_factory;
	/**
	 * @var SitePress $sitepress
	 */
	private $sitepress;
	/**
	 * @var WPML_Custom_Field_Setting_Factory
	 */
	private $cf_settings_factory;

	/**
	 * WPML_Media_Post_With_Media_Files constructor.
	 *
	 * @param $post_id
	 * @param WPML_Media_Img_Parse $media_parser
	 * @param WPML_Media_Attachment_By_URL_Factory $attachment_by_url_factory
	 * @param SitePress $sitepress
	 * @param WPML_Custom_Field_Setting_Factory $cf_settings_factory
	 */
	public function __construct(
		$post_id,
		WPML_Media_Img_Parse $media_parser,
		WPML_Media_Attachment_By_URL_Factory $attachment_by_url_factory,
		SitePress $sitepress,
		WPML_Custom_Field_Setting_Factory $cf_settings_factory
	) {
		$this->post_id                   = $post_id;
		$this->media_parser              = $media_parser;
		$this->attachment_by_url_factory = $attachment_by_url_factory;
		$this->sitepress                 = $sitepress;
		$this->cf_settings_factory       = $cf_settings_factory;
	}

	public function get_media_ids() {
		$media_ids = array();

		if ( $post = get_post( $this->post_id ) ) {

			$content_to_parse   = apply_filters( 'wpml_media_content_for_media_usage', $post->post_content, $post );
			$post_content_media = $this->media_parser->get_imgs( $content_to_parse );
			$media_ids          = $this->_get_ids_from_media_array( $post_content_media );

			if ( $featured_image = get_post_meta( $this->post_id, '_thumbnail_id', true ) ) {
				$media_ids[] = $featured_image;
			}

			$media_localization_settings = WPML_Media::get_setting( 'media_files_localization' );
			if ( $media_localization_settings['custom_fields'] ) {
				$custom_fields_content = $this->get_content_in_translatable_custom_fields();
				$custom_fields_media   = $this->media_parser->get_imgs( $custom_fields_content );
				$media_ids             = array_merge( $media_ids, $this->_get_ids_from_media_array( $custom_fields_media ) );
			}

			if ( $gallery_media_ids = $this->get_gallery_media_ids( $content_to_parse ) ) {
				$media_ids = array_unique( array_values( array_merge( $media_ids, $gallery_media_ids ) ) );
			}

			if ( $attached_media_ids = $this->get_attached_media_ids( $this->post_id ) ) {
				$media_ids = array_unique( array_values( array_merge( $media_ids, $attached_media_ids ) ) );
			}

		}

		return apply_filters( 'wpml_ids_of_media_used_in_post', $media_ids, $this->post_id );
	}

	/**
	 * @param array $media_array
	 *
	 * @return array
	 */
	private function _get_ids_from_media_array( $media_array ) {
		$media_ids = array();
		foreach ( $media_array as $media ) {
			if ( isset( $media['attachment_id'] ) ) {
				$media_ids[] = $media['attachment_id'];
			} else {
				$attachment_by_url = $this->attachment_by_url_factory->create( $media['attributes']['src'], wpml_get_current_language() );
				if ( $attachment_id = $attachment_by_url->get_id() ) {
					$media_ids[] = $attachment_id;
				}

			}
		}

		return $media_ids;
	}

	/**
	 * @param string $post_content
	 *
	 * @return array
	 */
	private function get_gallery_media_ids( $post_content ) {

		$galleries_media_ids    = array();
		$gallery_shortcode_regex = '/\[gallery [^[]*ids=["\']([0-9,\s]+)["\'][^[]*\]/m';
		if ( preg_match_all( $gallery_shortcode_regex, $post_content, $matches ) ) {
			foreach ( $matches[1] as $gallery_ids_string ) {
				$media_ids_array = explode( ',', $gallery_ids_string );
				foreach ( $media_ids_array as $media_id ) {
					if ( 'attachment' === get_post_type ( $media_id ) ) {
						$galleries_media_ids[] = (int) $media_id;
					}

				}
			}
		}

		return $galleries_media_ids;
	}

	/**
	 * @param $languages
	 *
	 * @return array
	 */
	public function get_untranslated_media( $languages ) {

		$untranslated_media = array();

		$post_media = $this->get_media_ids();

		foreach ( $post_media as $attachment_id ) {

			$post_element = new WPML_Post_Element( $attachment_id, $this->sitepress );
			foreach ( $languages as $language ) {
				$translation = $post_element->get_translation( $language );
				if ( null === $translation || ! $this->media_file_is_translated( $attachment_id, $translation->get_id() ) ) {
					$untranslated_media[] = $attachment_id;
					break;
				}
			}

		}

		return $untranslated_media;
	}

	private function media_file_is_translated( $attachment_id, $translated_attachment_id ) {
		return get_post_meta( $attachment_id, '_wp_attached_file', true )
		       !== get_post_meta( $translated_attachment_id, '_wp_attached_file', true );
	}

	private function get_content_in_translatable_custom_fields() {
		$content = '';

		$post_meta = get_metadata( 'post', $this->post_id );

		foreach ( $post_meta as $meta_key => $meta_value ) {
			$setting         = $this->cf_settings_factory->post_meta_setting( $meta_key );
			$is_translatable = $this->sitepress->get_wp_api()
			                                   ->constant( 'WPML_TRANSLATE_CUSTOM_FIELD' ) === $setting->status();
			if ( is_string( $meta_value[0] ) && $is_translatable ) {
				$content .= $meta_value[0];
			}

		}

		return $content;
	}

	private function get_attached_media_ids( $post_id ) {
		$attachments = get_children(
			array(
				'post_parent' => $post_id,
				'post_status' => 'inherit',
				'post_type'   => 'attachment',
			)
		);
		return array_keys( $attachments );
	}
}