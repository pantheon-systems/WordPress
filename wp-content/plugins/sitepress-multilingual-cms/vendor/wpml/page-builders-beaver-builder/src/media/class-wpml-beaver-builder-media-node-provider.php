<?php

class WPML_Beaver_Builder_Media_Node_Provider {

	/** @var WPML_Page_Builders_Media_Translate $media_translate */
	private $media_translate;

	/** @var WPML_Beaver_Builder_Media_Node[] */
	private $nodes = array();

	public function __construct( WPML_Page_Builders_Media_Translate $media_translate ) {
		$this->media_translate = $media_translate;
	}

	/**
	 * @param string $type
	 *
	 * @return WPML_Beaver_Builder_Media_Node
	 */
	public function get( $type ) {
		if ( ! array_key_exists( $type, $this->nodes ) ) {
			switch ( $type ) {
				case 'photo':
					$node = new WPML_Beaver_Builder_Media_Node_Photo( $this->media_translate );
					break;

				case 'gallery':
					$node = new WPML_Beaver_Builder_Media_Node_Gallery( $this->media_translate );
					break;

				case 'content-slider':
					$node = new WPML_Beaver_Builder_Media_Node_Content_Slider( $this->media_translate );
					break;

				case 'slideshow':
					$node = new WPML_Beaver_Builder_Media_Node_Slideshow( $this->media_translate );
					break;

				default:
					$node = null;
			}

			$this->nodes[ $type ] = $node;
		}

		return $this->nodes[ $type ];
	}
}
