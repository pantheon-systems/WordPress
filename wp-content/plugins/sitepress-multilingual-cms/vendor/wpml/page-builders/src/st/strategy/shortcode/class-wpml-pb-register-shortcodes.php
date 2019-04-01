<?php

/**
 * Class WPML_PB_Register_Shortcodes
 */
class WPML_PB_Register_Shortcodes {

	private $handle_strings;
	/** @var  WPML_PB_Shortcode_Strategy $shortcode_strategy */
	private $shortcode_strategy;
	/** @var  WPML_PB_Shortcode_Encoding $encoding */
	private $encoding;
	/** @var WPML_PB_Reuse_Translations $reuse_translations */
	private $reuse_translations;

	private $existing_package_strings;
	/** @var  int $location_index */
	private $location_index;

	/**
	 * WPML_Add_Wrapper_Shortcodes constructor.
	 *
	 * @param WPML_PB_String_Registration $handle_strings
	 * @param WPML_PB_Shortcode_Strategy $shortcode_strategy,
	 * @param WPML_PB_Shortcode_Encoding $encoding,
	 * @param WPML_PB_Reuse_Translations $reuse_translations
	 */
	public function __construct(
		WPML_PB_String_Registration $handle_strings,
		WPML_PB_Shortcode_Strategy $shortcode_strategy,
		WPML_PB_Shortcode_Encoding $encoding,
		WPML_PB_Reuse_Translations $reuse_translations = null
	) {
		$this->handle_strings         = $handle_strings;
		$this->shortcode_strategy     = $shortcode_strategy;
		$this->encoding               = $encoding;
		$this->reuse_translations     = $reuse_translations;
	}

	public function register_shortcode_strings( $post_id, $content ) {

		$this->location_index = 1;

		$content = apply_filters( 'wpml_pb_shortcode_content_for_translation', $content, $post_id );

		$shortcode_parser               = $this->shortcode_strategy->get_shortcode_parser();
		$shortcodes                     = $shortcode_parser->get_shortcodes( $content );
		$this->existing_package_strings = $this->shortcode_strategy->get_package_strings( $this->shortcode_strategy->get_package_key( $post_id ) );

		if ( $this->reuse_translations ) {
			$this->reuse_translations->set_original_strings( $this->existing_package_strings );
		}

		foreach ( $shortcodes as $shortcode ) {

			if ( $this->should_handle_content( $shortcode ) ) {
				$shortcode_content  = $shortcode['content'];
				$encoding           = $this->shortcode_strategy->get_shortcode_tag_encoding( $shortcode['tag'] );
				$encoding_condition = $this->shortcode_strategy->get_shortcode_tag_encoding_condition( $shortcode['tag'] );
				$type               = $this->shortcode_strategy->get_shortcode_tag_type( $shortcode['tag'] );
				$shortcode_content  = $this->encoding->decode( $shortcode_content, $encoding, $encoding_condition );
				$this->register_string( $post_id, $shortcode_content, $shortcode, 'content', $type );
			}

			$attributes              = (array) shortcode_parse_atts( $shortcode['attributes'] );
			$translatable_attributes = $this->shortcode_strategy->get_shortcode_attributes( $shortcode['tag'] );
			if ( ! empty( $attributes ) ) {
				foreach ( $attributes as $attr => $attr_value ) {
					if ( in_array( $attr, $translatable_attributes, true ) ) {
						$encoding   = $this->shortcode_strategy->get_shortcode_attribute_encoding( $shortcode['tag'], $attr );
						$type       = $this->shortcode_strategy->get_shortcode_attribute_type( $shortcode['tag'], $attr );
						$attr_value = $this->encoding->decode( $attr_value, $encoding );

						$this->register_string( $post_id, $attr_value, $shortcode, $attr, $type );
					}
				}
			}
		}

		if ( $this->reuse_translations ) {
			$this->reuse_translations->find_and_reuse( $post_id, $this->existing_package_strings );
		}

		$this->clean_up_package_leftovers();

		$this->mark_post_as_migrate_location_done( $post_id );
	}

	/**
	 * @param array $shortcode
	 *
	 * @return bool
	 */
	private function should_handle_content( $shortcode ) {
		$tag = $shortcode['tag'];

		$handle_content = ! (
			$this->shortcode_strategy->get_shortcode_ignore_content( $tag )
			|| in_array(
				$this->shortcode_strategy->get_shortcode_tag_type( $tag ),
				array(
					'media-url',
					'media-ids',
				),
				true
			)
		);

		/**
		 * Allow page builders to override if the shortcode should be handled as a translatable string.
		 *
		 * @since 4.2
		 * @param bool $handle_content.
		 * @param array $shortcode {
		 *
		 *      @type string $tag.
		 *      @type string $content.
		 *      @type string $attributes.
		 * }
		 */
		return apply_filters( 'wpml_pb_should_handle_content', $handle_content, $shortcode );
	}

	function get_updated_shortcode_string_title( $string_id, $shortcode, $attribute ) {
		$current_title = $this->get_shortcode_string_title( $string_id );

		$current_title_parts = explode( ':', $current_title );
		$current_title_parts = array_map( 'trim', $current_title_parts );

		$shortcode_tag = $shortcode['tag'];
		if ( isset( $current_title_parts[1] ) ) {
			$shortcode_attributes = explode( ',', $current_title_parts[1] );
			$shortcode_attributes = array_map( 'trim', $shortcode_attributes );
		}
		$shortcode_attributes[] = $attribute;
		sort( $shortcode_attributes );
		$shortcode_attributes = array_unique( $shortcode_attributes );

		return $shortcode_tag . ': ' . implode( ', ', $shortcode_attributes );
	}

	function get_shortcode_string_title( $string_id ) {
		return $this->handle_strings->get_string_title( $string_id );
	}

	public function register_string( $post_id, $content, $shortcode, $attribute, $editor_type ) {
		if ( is_array( $content ) ) {
			foreach ( $content as $key => $data ) {
				if ( $data['translate'] ) {
					$this->register_string( $post_id, $data['value'], $shortcode, $attribute . ' ' . $key, $editor_type );
				}
			}
		} else {
			$this->remove_from_clean_up_list( $content );
			try {
				$string_id    = $this->handle_strings->get_string_id_from_package( $post_id, $content );
				$string_title = $this->get_updated_shortcode_string_title( $string_id, $shortcode, $attribute );
				$string_id    = $this->handle_strings->register_string( $post_id, $content, $editor_type, $string_title, '', $this->location_index );
				if ( $string_id ) {
					$this->location_index ++;
				}
			} catch ( Exception $exception ) {

			}
		}
	}

	private function remove_from_clean_up_list( $value ) {
		$hash_value = md5( $value );
		if ( isset( $this->existing_package_strings[ $hash_value ] ) ) {
			unset( $this->existing_package_strings[ $hash_value ] );
		}
	}

	private function clean_up_package_leftovers() {
		if ( ! empty( $this->existing_package_strings ) ) {
			foreach ( $this->existing_package_strings as $string_data ) {
				$this->shortcode_strategy->remove_string( $string_data );
			}
		}
	}

	/**
	 * @param int $post_id
	 */
	private function mark_post_as_migrate_location_done( $post_id ) {
		update_post_meta( $post_id, WPML_PB_Integration::MIGRATION_DONE_POST_META, true );
	}


}
