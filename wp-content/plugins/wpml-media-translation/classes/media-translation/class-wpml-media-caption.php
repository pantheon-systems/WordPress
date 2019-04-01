<?php

/**
 * Class WPML_Media_Caption
 */
class WPML_Media_Caption {

	private $shortcode;
	private $content_string;
	private $attributes;
	private $attachment_id;
	private $link;
	private $img;
	private $caption;

	public function __construct( $caption_shortcode, $attributes_data, $content_string ) {
		$this->shortcode      = $caption_shortcode;
		$this->content_string = $content_string;

		$this->attributes    = $this->find_attributes_array( $attributes_data );
		$this->attachment_id = $this->find_attachment_id( $this->attributes );

		$this->link = $this->find_link( $content_string );

		$img_parser    = new WPML_Media_Img_Parse();
		$this->img     = current( $img_parser->get_imgs( $content_string ) );
		$this->caption = trim( strip_tags( $content_string ) );
	}


	/**
	 * @return int
	 */
	public function get_id() {
		return $this->attachment_id;
	}

	public function get_caption() {
		return $this->caption;
	}

	public function get_shortcode_string() {
		return $this->shortcode;
	}

	public function get_content() {
		return $this->content_string;
	}

	public function get_image_alt() {
		if ( isset( $this->img['attributes']['alt'] ) ) {
			return $this->img['attributes']['alt'];
		} else {
			return '';
		}
	}

	public function get_link() {
		return $this->link;
	}

	/**
	 * @param string $attributes_list
	 *
	 * @return array
	 */
	private function find_attributes_array( $attributes_list ) {
		$attributes = array();
		if ( preg_match_all( '/(\S+)=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?/', $attributes_list, $attribute_matches ) ) {
			foreach ( $attribute_matches[1] as $k => $key ) {
				$attributes[ $key ] = $attribute_matches[2][ $k ];
			}
		}

		return $attributes;
	}

	/**
	 * @param array $attributes
	 *
	 * @return null|int
	 */
	private function find_attachment_id( $attributes ) {
		$attachment_id = null;
		if ( isset( $attributes['id'] ) ) {
			if ( preg_match( '/attachment_([0-9]+)\b/', $attributes['id'], $id_match ) ) {
				if ( 'attachment' === get_post_type( (int) $id_match[1] ) ) {
					$attachment_id = (int) $id_match[1];
				}
			}
		}

		return $attachment_id;
	}

	/**
	 * @param $string
	 *
	 * @return array
	 */
	private function find_link( $string ) {
		$link = array();
		if ( preg_match( '/<a ([^>]+)>(.+)<\/a>/s', $string, $a_match ) ) {
			if ( preg_match( '/href=["\']([^"]+)["\']/', $a_match[1], $url_match ) ) {
				$link['url'] = $url_match[1];
			}
		}

		return $link;
	}

}