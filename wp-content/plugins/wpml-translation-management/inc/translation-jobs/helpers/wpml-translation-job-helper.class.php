<?php

class WPML_Translation_Job_Helper {

	public function encode_field_data( $data ) {
		return base64_encode( $data );
	}

	public function decode_field_data( $data, $format ) {
		return $this->get_core_translation_management()->decode_field_data( $data, $format );
	}

	protected function get_tm_setting( $indexes ) {
		$core_tm     = $this->get_core_translation_management();
		if ( empty( $core_tm->settings ) ) {
			$core_tm->init();
		}

		$settings = $core_tm->get_settings();

		foreach ( $indexes as $index ) {
			$settings = isset( $settings[ $index ] ) ? $settings[ $index ] : null;
			if ( ! isset( $settings ) ) {
				break;
			}
		}

		return $settings;
	}

	/**
	 * @return TranslationManagement
	 */
	private function get_core_translation_management() {
		/** TranslationManagement $iclTranslationManagement */
		global $iclTranslationManagement;

		return $iclTranslationManagement;
	}
}