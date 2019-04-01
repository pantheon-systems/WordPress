<?php

class WPML_Elementor_Media_Node_WP_Widget_Media_Image extends WPML_Elementor_Media_Node {

	/**
	 * @param array  $settings
	 * @param string $target_lang
	 * @param string $source_lang
	 *
	 * @return mixed
	 */
	public function translate( $settings, $target_lang, $source_lang ) {
		if ( isset( $settings['wp']['attachment_id'] ) ) {
			$translated_id = $this->media_translate->translate_id( $settings['wp']['attachment_id'], $target_lang );

			if ( $translated_id !== (int) $settings['wp']['attachment_id'] ) {
				$settings['wp']['attachment_id'] = $translated_id;

				$settings['wp']['url'] = $this->media_translate->translate_image_url(
					$settings['wp']['url'],
					$target_lang,
					$source_lang
				);

				$image_data                    = wp_prepare_attachment_for_js( $translated_id );
				$settings['wp']['caption']     = $image_data['caption'];
				$settings['wp']['alt']         = $image_data['alt'];
				$settings['wp']['image_title'] = $image_data['title'];
			}
		}

		return $settings;
	}
}
