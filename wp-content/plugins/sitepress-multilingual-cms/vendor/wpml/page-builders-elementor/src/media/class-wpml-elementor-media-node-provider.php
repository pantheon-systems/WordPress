<?php

class WPML_Elementor_Media_Node_Provider {

	/** @var WPML_Page_Builders_Media_Translate $media_translate */
	private $media_translate;

	/** @var WPML_Elementor_Media_Node[] */
	private $nodes = array();

	public function __construct( WPML_Page_Builders_Media_Translate $media_translate ) {
		$this->media_translate = $media_translate;
	}

	/**
	 * @param string $type
	 *
	 * @return WPML_Elementor_Media_Node
	 */
	public function get( $type ) {
		if ( ! array_key_exists( $type, $this->nodes ) ) {
			switch ( $type ) {
				case 'image':
					$node = new WPML_Elementor_Media_Node_Image( $this->media_translate );
					break;

				case 'slides':
					$node = new WPML_Elementor_Media_Node_Slides( $this->media_translate );
					break;

				case 'call-to-action':
					$node = new WPML_Elementor_Media_Node_Call_To_Action( $this->media_translate );
					break;

				case 'media-carousel':
					$node = new WPML_Elementor_Media_Node_Media_Carousel( $this->media_translate );
					break;

				case 'image-box':
					$node = new WPML_Elementor_Media_Node_Image_Box( $this->media_translate );
					break;

				case 'image-gallery':
					$node = new WPML_Elementor_Media_Node_Image_Gallery( $this->media_translate );
					break;

				case 'image-carousel':
					$node = new WPML_Elementor_Media_Node_Image_Carousel( $this->media_translate );
					break;

				case 'wp-widget-media_image':
					$node = new WPML_Elementor_Media_Node_WP_Widget_Media_Image( $this->media_translate );
					break;

				case 'wp-widget-media_gallery':
					$node = new WPML_Elementor_Media_Node_WP_Widget_Media_Gallery( $this->media_translate );
					break;

				default:
					$node = null;
			}

			$this->nodes[ $type ] = $node;
		}

		return $this->nodes[ $type ];
	}
}
