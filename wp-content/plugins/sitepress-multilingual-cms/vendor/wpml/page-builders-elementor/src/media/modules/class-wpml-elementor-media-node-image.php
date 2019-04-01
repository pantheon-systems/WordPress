<?php

class WPML_Elementor_Media_Node_Image extends WPML_Elementor_Media_Node {

	/**
	 * @param array  $settings
	 * @param string $target_lang
	 * @param string $source_lang
	 *
	 * @return array
	 */
	public function translate( $settings, $target_lang, $source_lang ) {
		$settings = $this->translate_image_property( $settings, 'image', $target_lang, $source_lang );
		$settings = $this->translate_image_property( $settings, '_background_image', $target_lang, $source_lang );
		$settings = $this->translate_image_property( $settings, '_background_hover_image', $target_lang, $source_lang );

		if ( isset( $settings['caption'], $settings['image']['id'] ) ) {
			$image_data          = wp_prepare_attachment_for_js( $settings['image']['id'] );
			$settings['caption'] = $image_data['caption'];
		}

		return $settings;
	}
}
