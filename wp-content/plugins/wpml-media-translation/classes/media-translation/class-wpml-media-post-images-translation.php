<?php

/**
 * Class WPML_Media_Post_Images_Translation
 * Translate images in posts translations when a post is created or updated
 */
class WPML_Media_Post_Images_Translation implements IWPML_Action {

	/**
	 * @var WPML_Media_Translated_Images_Update
	 */
	private $images_updater;

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * @var wpdb
	 */
	private $wpdb;
	/**
	 * @var WPML_Translation_Element_Factory
	 */
	private $translation_element_factory;
	/**
	 * @var WPML_Media_Custom_Field_Images_Translation_Factory
	 */
	private $custom_field_images_translation_factory;
	/**
	 * @var WPML_Media_Usage_Factory
	 */
	private $media_usage_factory;

	private $captions_map = array();

	private $translate_locks = array();

	public function __construct(
		WPML_Media_Translated_Images_Update $images_updater,
		SitePress $sitepress,
		wpdb $wpdb,
		WPML_Translation_Element_Factory $translation_element_factory,
		WPML_Media_Custom_Field_Images_Translation_Factory $custom_field_images_translation_factory,
		WPML_Media_Usage_Factory $media_usage_factory
	) {
		$this->images_updater                          = $images_updater;
		$this->sitepress                               = $sitepress;
		$this->wpdb                                    = $wpdb;
		$this->translation_element_factory             = $translation_element_factory;
		$this->custom_field_images_translation_factory = $custom_field_images_translation_factory;
		$this->media_usage_factory                     = $media_usage_factory;
	}

	public function add_hooks() {
		add_action( 'save_post', array( $this, 'translate_images' ), PHP_INT_MAX, 1 );
		add_filter( 'wpml_pre_save_pro_translation', array( $this, 'translate_images_in_content' ), PHP_INT_MAX, 2 );
		add_filter( 'wpml_pre_save_pro_translation', array(
			$this,
			'replace_placeholders_and_id_in_caption_shortcode'
		), PHP_INT_MAX, 2 );
		add_action( 'wpml_tm_job_fields',
			array( $this, 'replace_caption_placeholders_in_fields' ), 10, 2 );
		add_filter( 'wpml_tm_job_data_post_content', array(
			$this,
			'restore_placeholders_in_translated_job_body'
		), 10, 1 );

		add_action( 'icl_make_duplicate', array( $this, 'translate_images_in_duplicate' ), PHP_INT_MAX, 4 );

		add_action( 'wpml_added_media_file_translation', array( $this, 'translate_url_in_post' ), PHP_INT_MAX, 1 );
		add_action( 'wpml_pro_translation_completed', array( $this, 'translate_images' ), PHP_INT_MAX, 1 );
		add_action( 'wpml_restored_media_file_translation', array( $this, 'translate_url_in_post' ), PHP_INT_MAX, 2 );
	}

	/**
	 * @param int $post_id
	 */
	public function translate_images( $post_id ) {
		if ( isset( $this->translate_locks[ $post_id ] ) ) {
			return;
		}

		$this->translate_locks[ $post_id ] = true;

		$post = get_post( $post_id );

		if ( ! $post ) {
			return;
		}

		/** @var WPML_Post_Element $post_element */
		$post_element    = $this->translation_element_factory->create( $post_id, 'post' );
		$source_language = $post_element->get_source_language_code();

		if ( null !== $source_language ) {

			$this->translate_images_in_post_content( $post, $post_element );
			$this->translate_images_in_custom_fields( $post_id );

		} else { // is original
			foreach ( array_keys( $this->sitepress->get_active_languages() ) as $target_language ) {
				/** @var WPML_Post_Element $translation */
				$translation = $post_element->get_translation( $target_language );
				if ( null !== $translation && $post_id !== $translation->get_id() ) {
					$this->translate_images_in_post_content( get_post( $translation->get_id() ), $translation );
					$this->translate_images_in_custom_fields( $translation->get_id() );
				}
			}
		}

		unset( $this->translate_locks[ $post_id ] );
	}

	/**
	 * @param int $master_post_id
	 * @param string $language
	 * @param array $post_array
	 * @param int $post_id
	 */
	public function translate_images_in_duplicate( $master_post_id, $language, $post_array, $post_id ) {
		$this->translate_images( $post_id );
	}

	/**
	 * @param WP_Post $post
	 * @param WPML_Post_Element $post_element
	 */
	private function translate_images_in_post_content( WP_Post $post, WPML_Post_Element $post_element ) {

		if ( (bool) apply_filters( 'wpml_pb_should_body_be_translated', true, $post, 'translate_images_in_post_content' ) ) {
			$post_content_filtered = $this->images_updater->replace_images_with_translations(
				$post->post_content,
				$post_element->get_language_code(),
				$post_element->get_source_language_code()
			);

			if ( $post_content_filtered !== $post->post_content ) {
				$this->wpdb->update(
					$this->wpdb->posts,
					array( 'post_content' => $post_content_filtered ),
					array( 'ID' => $post->ID ),
					array( '%s' ),
					array( '%d' )
				);
			}
		} elseif ( $this->is_updated_from_media_translation_menu() ) {
			do_action( 'wpml_pb_resave_post_translation', $post_element );
		}
	}

	private function is_updated_from_media_translation_menu() {
		$allowed_actions = array(
			'wpml_media_save_translation',
			'wpml_media_translate_media_url_in_posts',
		);

		return isset( $_POST['action'] ) && in_array( $_POST['action'], $allowed_actions, true );
	}

	/**
	 * @param int $post_id
	 */
	private function translate_images_in_custom_fields( $post_id ) {

		$custom_fields_image_translation = $this->custom_field_images_translation_factory->create();
		if ( $custom_fields_image_translation ) {
			$post_meta = get_metadata( 'post', $post_id );
			foreach ( $post_meta as $meta_key => $meta_value ) {
				$custom_fields_image_translation->translate_images( null, $post_id, $meta_key, $meta_value[0] );
			}
		}

	}

	/**
	 * @param array $postarr
	 * @param stdClass $job
	 *
	 * @return array
	 */
	public function translate_images_in_content( array $postarr, stdclass $job ) {

		$postarr['post_content'] = $this->images_updater->replace_images_with_translations(
			$postarr['post_content'],
			$job->language_code,
			$job->source_language_code
		);

		return $postarr;
	}

	public function translate_url_in_post( $attachment_id, $posts = array() ) {
		if ( ! $posts ) {
			$media_usage = $this->media_usage_factory->create( $attachment_id );
			$posts       = $media_usage->get_posts();
		}

		foreach ( $posts as $post_id ) {
			$this->translate_images( $post_id );
		}
	}

	public function replace_placeholders_and_id_in_caption_shortcode( array $postarr, stdClass $job ) {

		$media = $this->find_media_in_job( $job );

		$postarr['post_content'] = $this->replace_caption_placeholders_in_string(
			$postarr['post_content'],
			$media,
			$job->language_code
		);

		return $postarr;
	}
	
	public function replace_caption_placeholders_in_string( $text, $media, $language ) {

		$caption_parser = new WPML_Media_Caption_Tags_Parse();
		$captions       = $caption_parser->get_captions( $text );

		foreach ( $captions as $caption ) {
			$attachment_id     = $caption->get_id();
			$caption_shortcode = $new_caption_shortcode = $caption->get_shortcode_string();

			if ( isset( $media[ $attachment_id ] ) ) {

				if ( isset( $media[ $attachment_id ]['caption'] ) ) {
					$new_caption_shortcode = $this->replace_placeholder_with_caption( $new_caption_shortcode, $caption, $media[ $attachment_id ]['caption'] );
				}

				if ( isset( $media[ $attachment_id ]['alt'] ) ) {
					$new_caption_shortcode = $this->replace_placeholder_with_alt_text( $new_caption_shortcode, $caption, $media[ $attachment_id ]['alt'] );
				}

			}

			$new_caption_shortcode = $this->replace_caption_id_with_translated_id(
				$new_caption_shortcode,
				$attachment_id,
				$language
			);

			if ( $new_caption_shortcode !== $caption_shortcode ) {
				$text                                     = str_replace( $caption_shortcode, $new_caption_shortcode, $text );
				$this->captions_map[ $caption_shortcode ] = $new_caption_shortcode;
			}

		}

		return $text;
	}

	/**
	 * @param int      $new_post_id
	 * @param array    $fields
	 * @param stdClass $job
	 */
	public function replace_caption_placeholders_in_fields( array $fields, stdClass $job ) {

		$media = $this->find_media_in_job( $job );

		foreach ( $fields as $field_id => $field ) {
			$fields[ $field_id ]['data'] = $this->replace_caption_placeholders_in_string(
				$field['data'],
				$media,
				$job->language_code
			);
		}

		return $fields;
	}
	
	private function replace_placeholder_with_caption( $caption_shortcode, WPML_Media_Caption $caption, $new_caption ) {
		$caption_content     = $caption->get_content();
		$new_caption_content = str_replace( WPML_Media_Add_To_Translation_Package::CAPTION_PLACEHOLDER, $new_caption, $caption_content );

		return str_replace( $caption_content, $new_caption_content, $caption_shortcode );
	}

	private function replace_placeholder_with_alt_text( $caption_shortcode, WPML_Media_Caption $caption, $new_alt_text ) {
		return str_replace( 'alt="' . WPML_Media_Add_To_Translation_Package::ALT_PLACEHOLDER . '"', 'alt="' . $new_alt_text . '"', $caption_shortcode );
	}

	private function replace_caption_id_with_translated_id( $caption_shortcode, $attachment_id, $language ) {
		$post_element = $this->translation_element_factory->create( $attachment_id, 'post' );
		$translation  = $post_element->get_translation( $language );
		if ( $translation ) {

			$translated_id = $translation->get_id();

			$caption_shortcode = str_replace(
				'id="attachment_' . $attachment_id . '"',
				'id="attachment_' . $translated_id . '"',
				$caption_shortcode
			);
		}

		return $caption_shortcode;
	}
	private function find_media_in_job( stdClass $job ) {
		$media = array();

		foreach ( $job->elements as $element ) {
			$field_type = explode( '_', $element->field_type );
			if ( 'media' === $field_type[0] ) {
				if ( ! isset( $media[ $field_type[1] ] ) ) {
					$media[ $field_type[1] ] = array();
				}
				$media[ $field_type[1] ][ $field_type[2] ] = base64_decode( $element->field_data_translated );
			}
		}

		return $media;
	}

	public function restore_placeholders_in_translated_job_body( $new_body ) {
		/**
		 * Translation management is updating the translated job data with the post_content
		 * from the translated post.
		 * We want the translated job data to contain the placeholders so we need to
		 * find the captions we replaced and restore it with the version with the placeholders
		 */

		foreach ( $this->captions_map as $caption_with_placeholder => $caption_in_post ) {
			$new_body = str_replace( $caption_in_post, $caption_with_placeholder, $new_body );
		}
		$this->captions_map = array();

		return $new_body;
	}

}