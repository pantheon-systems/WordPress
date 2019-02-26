<?php

class WPML_Compatibility_Plugin_Visual_Composer_Grid_Hooks implements IWPML_Action {

	/** @var IWPML_Current_Language $current_language */
	private $current_language;

	/** @var WPML_Translation_Element_Factory $element_factory */
	private $element_factory;

	public function __construct(
		IWPML_Current_Language $current_language,
		WPML_Translation_Element_Factory $element_factory
	) {
		$this->current_language = $current_language;
		$this->element_factory = $element_factory;
	}

	public function add_hooks() {
		add_filter( 'wpml_pb_shortcode_decode', array( $this, 'vc_grid_link_decode' ), 10, 3 );
		add_filter( 'vc_shortcode_content_filter', array( $this, 'vc_shortcode_content_filter' ) );
	}

	/**
	 * @param string|array $string
	 * @param string       $encoding
	 * @param string       $encoded_string
	 *
	 * @return string|array
	 */
	function vc_grid_link_decode( $string, $encoding, $encoded_string ) {
		if ( 'vc_link' === $encoding && empty( $string ) ) {
			return $encoded_string;
		}

		return $string;
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	public function vc_shortcode_content_filter( $content ) {
		$pattern = '/(\[vc_basic_grid.*item=")([^"]*)(".*\])/';
		return preg_replace_callback( $pattern, array( $this, 'replace_grid_id' ), $content );
	}

	/**
	 * @param array $matches
	 *
	 * @return string
	 */
	private function replace_grid_id( array $matches ) {
		$grid_id = (int) $matches[2];

		if ( $grid_id > 0 ) {
			$before      = $matches[1];
			$after       = $matches[3];
			$element     = $this->element_factory->create( $grid_id, 'post' );
			$translation = $element->get_translation( $this->current_language->get_current_language() );

			if ( $translation ) {
				$grid_id = $translation->get_element_id();
			}

			return $before . $grid_id . $after;
		}

		return $matches[0];
	}
}