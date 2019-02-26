<?php

/**
 * Class WPML_Gutenberg_Integration
 */
class WPML_Gutenberg_Integration {

	const PACKAGE_ID              = 'Gutenberg';
	const GUTENBERG_OPENING_START = '<!-- wp:';
	const GUTENBERG_CLOSING_START = '<!-- /wp:';
	const CLASSIC_BLOCK_NAME      = 'core/classic-block';

	/**
	 * @var WPML_Gutenberg_Strings_In_Block
	 */
	private $strings_in_blocks;

	/**
	 * @var WPML_Gutenberg_Config_Option
	 */
	private $config_option;

	/**
	 * @var SitePress
	 */
	private $sitepress;

	public function __construct(
		WPML_Gutenberg_Strings_In_Block $strings_in_block,
		WPML_Gutenberg_Config_Option $config_option,
		SitePress $sitepress
	) {
		$this->strings_in_blocks = $strings_in_block;
		$this->config_option     = $config_option;
		$this->sitepress         = $sitepress;
	}

	public function add_hooks() {
		add_filter( 'wpml_page_builder_support_required', array( $this, 'page_builder_support_required' ), 10, 1 );
		add_action( 'wpml_page_builder_register_strings', array( $this, 'register_strings' ), 10, 2 );
		add_action( 'wpml_page_builder_string_translated', array( $this, 'string_translated' ), 10, 5 );
		add_filter( 'wpml_config_array', array( $this, 'wpml_config_filter' ) );
		add_filter( 'wpml_pb_should_body_be_translated', array( $this, 'should_body_be_translated_filter' ), PHP_INT_MAX, 3 );
		add_filter( 'wpml_get_translatable_types', array( $this, 'remove_package_strings_type_filter' ), 11 );
	}

	/**
	 * @param array $plugins
	 *
	 * @return array
	 */
	function page_builder_support_required( $plugins ) {
		$plugins[] = self::PACKAGE_ID;

		return $plugins;
	}

	/**
	 * @param WP_Post $post
	 * @param array $package_data
	 */
	function register_strings( WP_Post $post, $package_data ) {

		if ( ! $this->is_gutenberg_post( $post ) ) {
			return;
		}

		if ( self::PACKAGE_ID === $package_data['kind'] ) {

			do_action( 'wpml_start_string_package_registration', $package_data );

			$this->register_blocks(
				$this->parse_blocks( $post->post_content ),
				$package_data
			);

			do_action( 'wpml_delete_unused_package_strings', $package_data );

		}
	}

	/**
	 * @param array $blocks
	 * @param array $package_data
	 */
	private function register_blocks( array $blocks, array $package_data ) {

		foreach ( $blocks as $block ) {

			$block   = $this->sanitize_block( $block );
			$strings = $this->strings_in_blocks->find( $block );

			foreach ( $strings as $string ) {

				do_action(
					'wpml_register_string',
					$string->value,
					$string->id,
					$package_data,
					$string->name,
					$string->type
				);

			}

			if ( isset( $block->innerBlocks ) ) {
				$this->register_blocks( $block->innerBlocks, $package_data );
			}
		}
	}

	/**
	 * @param WP_Block_Parser_Block|array $block
	 *
	 * @return WP_Block_Parser_Block
	 */
	private function sanitize_block( $block ) {
		if ( ! $block instanceof WP_Block_Parser_Block ) {

			if ( empty( $block['blockName'] ) ) {
				$block['blockName'] = self::CLASSIC_BLOCK_NAME;
			}

			$block = new WP_Block_Parser_Block( $block['blockName'], $block['attrs'], $block['innerBlocks'], $block['innerHTML'], $block['innerContent'] );
		}

		return $block;
	}

	/**
	 * @param string $package_kind
	 * @param int $translated_post_id
	 * @param WP_Post $original_post
	 * @param array $string_translations
	 * @param string $lang
	 */
	public function string_translated(
		$package_kind,
		$translated_post_id,
		$original_post,
		$string_translations,
		$lang
	) {

		if ( self::PACKAGE_ID === $package_kind ) {
			$blocks = $this->parse_blocks( $original_post->post_content );

			$blocks = $this->update_block_translations( $blocks, $string_translations, $lang );

			$content = '';
			foreach ( $blocks as $block ) {
				$content .= $this->render_block( $block );
			}

			$this->sitepress->switch_lang( $lang );
			wp_update_post( array( 'ID' => $translated_post_id, 'post_content' => $content ) );
			$this->sitepress->switch_lang( null );
		}

	}

	/**
	 * @param array $blocks
	 * @param array $string_translations
	 * @param string $lang
	 *
	 * @return array
	 */
	private function update_block_translations( $blocks, $string_translations, $lang ) {
		foreach ( $blocks as &$block ) {

			$block = $this->sanitize_block( $block );
			$block = $this->strings_in_blocks->update( $block, $string_translations, $lang );

			if ( isset( $block->blockName ) && 'core/block' === $block->blockName ) {
				$block->attrs['ref'] = apply_filters( 'wpml_object_id', $block->attrs['ref'], 'wp_block', true, $lang );
			}
			if ( isset( $block->innerBlocks ) ) {
				$block->innerBlocks = $this->update_block_translations(
					$block->innerBlocks,
					$string_translations,
					$lang
				);
			}
		}

		return $blocks;
	}

	/**
	 * @param array|WP_Block_Parser_Block $block
	 *
	 * @return string
	 */
	private function render_block( $block ) {
		$content = '';

		$block = $this->sanitize_block( $block );

		if ( self::CLASSIC_BLOCK_NAME !== $block->blockName ) {
			$block_type = preg_replace( '/^core\//', '', $block->blockName );

			$block_attributes = '';
			if ( $this->has_non_empty_attributes( $block ) ) {
				$block_attributes = ' ' . json_encode( $block->attrs );
			}
			$content .= self::GUTENBERG_OPENING_START . $block_type . $block_attributes . ' -->';

			$content .= $this->render_inner_HTML( $block );

			$content .= self::GUTENBERG_CLOSING_START . $block_type . ' -->';
		} else {
			$content = wpautop( $block->innerHTML );
		}

		return $content;

	}

	private function has_non_empty_attributes( WP_Block_Parser_Block $block ) {
		return (bool) ( (array) $block->attrs );
	}

	/**
	 * @param WP_Block_Parser_Block $block
	 *
	 * @return string
	 */
	private function render_inner_HTML( $block ) {

		if ( isset ( $block->innerBlocks ) && count( $block->innerBlocks ) ) {
			$inner_html_parts = $this->guess_inner_HTML_parts( $block );

			$content = $inner_html_parts[0];

			foreach ( $block->innerBlocks as $inner_block ) {
				$content .= $this->render_block( $inner_block );
			}

			$content .= $inner_html_parts[1];

		} else {
			$content = $block->innerHTML;
		}

		return $content;

	}

	/**
	 * The gutenberg parser doesn't handle inner blocks correctly
	 * It should really return the HTML before and after the blocks
	 * We're just guessing what it is here
	 * The usual innerHTML would be: <div class="xxx"></div>
	 * The columns block also includes new lines: <div class="xxx">\n\n</div>
	 * So we try to split at ></ and also include white space and new lines between the tags
	 *
	 * @param WP_Block_Parser_Block $block
	 *
	 * @return array
	 */
	private function guess_inner_HTML_parts( $block ) {
		$inner_HTML = $block->innerHTML;

		$parts = array( $inner_HTML, '' );

		switch( $block->blockName ) {
			case 'core/media-text':
				$html_to_find = '<div class="wp-block-media-text__content">';
				$pos          = mb_strpos( $inner_HTML, $html_to_find ) + mb_strlen( $html_to_find );
				$parts        = array(
					mb_substr( $inner_HTML, 0, $pos ),
					mb_substr( $inner_HTML, $pos )
				);
				break;

			default:
				preg_match( '#>\s*</#', $inner_HTML, $matches );

				if ( count( $matches ) === 1 ) {
					$parts = explode( $matches[0], $inner_HTML );
					if ( count( $parts ) === 2 ) {
						$match_mid_point = 1 + ( mb_strlen( $matches[0] ) - 3 ) / 2;
						// This is the first ">" char plus half the remaining between the tags

						$parts[0] .= mb_substr( $matches[0], 0, $match_mid_point );
						$parts[1] = mb_substr( $matches[0], $match_mid_point ) . $parts[1];
					}
				}
				break;
		}

		return $parts;
	}

	/**
	 * @param array $config_data
	 *
	 * @return array
	 */
	public function wpml_config_filter( $config_data ) {
		$this->config_option->update_from_config( $config_data );

		return $config_data;
	}

	/**
	 * @param bool    $translate
	 * @param WP_Post $post
	 * @param string  $context
	 *
	 * @return bool
	 */
	public function should_body_be_translated_filter( $translate, WP_Post $post, $context = '' ) {
		if ( 'translate_images_in_post_content' === $context && $this->is_gutenberg_post( $post ) ) {
			$translate = true;
		}

		return $translate;
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return bool
	 */
	private function is_gutenberg_post( WP_Post $post ) {
		return (bool) preg_match( '/' . self::GUTENBERG_OPENING_START . '/', $post->post_content );
	}

	private function parse_blocks( $content ) {
		global $wp_version;
		if ( version_compare( $wp_version, '5.0-beta1', '>=' ) ) {
			return parse_blocks( $content );
		} else {
			return gutenberg_parse_blocks( $content );
		}
	}

	/**
	 * Remove Gutenberg (string package) from translation dashboard filters
	 *
	 * @param array $types
	 *
	 * @return mixed
	 */
	public function remove_package_strings_type_filter( $types ) {

		if ( array_key_exists( 'gutenberg', $types ) ) {
			unset( $types['gutenberg'] );
		}

		return $types;
	}
}
