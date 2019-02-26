<?php

abstract class WPML_Elementor_Media_Node_With_Slides extends WPML_Elementor_Media_Node {

	/** @return string */
	abstract protected function get_image_property_name();

	/**
	 * @param array  $settings
	 * @param string $target_lang
	 * @param string $source_lang
	 *
	 * @return mixed
	 */
	public function translate( $settings, $target_lang, $source_lang ) {
		if ( ! isset( $settings['slides'] ) || ! is_array( $settings['slides'] ) ) {
			return $settings;
		}

		foreach ( $settings['slides'] as &$slide ) {
			$slide = $this->translate_image_property( $slide, $this->get_image_property_name(), $target_lang, $source_lang );
		}

		return $settings;
	}
}
