<?php

/**
 * Class WPML_Media_Img_Parse
 */
class WPML_Media_Img_Parse{

	/**
	 * @param string $text
	 *
	 * @return array
	 */
	public function get_imgs( $text ){
		$images = $this->get_from_img_tags( $text );

		if ( $this->can_parse_blocks( $text ) ) {
			$blocks = parse_blocks( $text );
			$images = array_merge( $images, $this->get_from_css_background_images_in_blocks( $blocks ) );
		} else {
			$images = array_merge( $images, $this->get_from_css_background_images( $text ) );
		}

		return $images;
	}

	/**
	 * @param string $text
	 *
	 * @return array
	 */
	private function get_from_img_tags( $text ) {
		$images = array();

		if( preg_match_all( '/<img ([^>]+)>/s', $text, $matches ) ){
			foreach ( $matches[1] as $i => $match ){
				if( preg_match_all('/(\S+)\\s*=\\s*["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?/', $match, $attribute_matches ) ){
					$attributes = array();
					foreach( $attribute_matches[1] as $k => $key ){
						$attributes[$key] = $attribute_matches[2][$k];
					}
					if( isset( $attributes['src'] ) ){
						$images[$i]['attributes'] = $attributes;
						$images[$i]['attachment_id'] = $this->get_attachment_id_from_attributes( $images[$i]['attributes'] );
					}
				}
			}
		}

		return $images;
	}

	/**
	 * @param string $text
	 *
	 * @return array
	 */
	private function get_from_css_background_images( $text ) {
		$images = array();

		if( preg_match_all( '/<\w+[^>]+style\s?=\s?"[^"]*?background-image:url\(\s?([^\s\)]+)\s?\)/', $text, $matches ) ){
			foreach ( $matches[1] as $src ) {
				$images[] = array(
					'attributes'    => array( 'src' => $src ),
					'attachment_id' => null,
				);
			}
		}

		return $images;
	}

	/**
	 * @param array $blocks
	 *
	 * @return array
	 */
	private function get_from_css_background_images_in_blocks( $blocks ) {
		$images = array();

		foreach ( $blocks as $block ) {
			$block = $this->sanitize_block( $block );

			if ( ! empty( $block->innerBlocks ) ) {
				$inner_images = $this->get_from_css_background_images_in_blocks( $block->innerBlocks );
				$images = array_merge( $images, $inner_images );
				continue;
			}

			if ( ! isset( $block->innerHTML, $block->attrs->id ) ) {
				continue;
			}

			$background_images = $this->get_from_css_background_images( $block->innerHTML );
			$image             = reset( $background_images );

			if ( $image ) {
				$image['attachment_id'] = $block->attrs->id;
				$images[]               = $image;
			}
		}

		return $images;
	}

	/**
	 * `parse_blocks` does not specify which kind of collection it should return
	 * (not always an array of `WP_Block_Parser_Block`) and the block parser can be filtered,
	 *  so we'll cast it to a standard object for now.
	 *
	 * @param mixed $block
	 *
	 * @return stdClass|WP_Block_Parser_Block
	 */
	private function sanitize_block( $block ) {
		$block = (object) $block;

		if ( isset( $block->attrs ) ) {
			/** Sometimes `$block->attrs` is an object or an array, so we'll use an object */
			$block->attrs = (object) $block->attrs;
		}

		return $block;
	}

	function can_parse_blocks( $string ) {
		return false !== strpos( $string, '<!-- wp:' ) && function_exists( 'parse_blocks' );
	}

	/**
	 * @param $attributes
	 *
	 * @return null|int
	 */
	private function get_attachment_id_from_attributes( $attributes ){
		$attachment_id = null;
		if( isset( $attributes['class'] ) ){
			if( preg_match('/wp-image-([0-9]+)\b/', $attributes['class'], $id_match ) ){
				if( 'attachment' === get_post_type( (int) $id_match[1] ) ){
					$attachment_id = (int) $id_match[1];
				}
			}
		}
		return $attachment_id;
	}

}