<?php

class WPML_Elementor_Media_Node_WP_Widget_Media_Gallery extends WPML_Elementor_Media_Node {

	/**
	 * @param array  $settings
	 * @param string $target_lang
	 * @param string $source_lang
	 *
	 * @return array
	 */
	public function translate( $settings, $target_lang, $source_lang ) {
		if ( isset( $settings['wp']['ids'] ) ) {
			$ids = explode( ',', $settings['wp']['ids'] );

			foreach ( $ids as &$id ) {
				$id = $this->media_translate->translate_id( (int) $id, $target_lang );
			}

			$settings['wp']['ids'] = implode( ',', $ids );
		}

		return $settings;
	}
}
