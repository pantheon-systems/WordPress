<?php
/**
 * @author OnTheGo Systems
 */
class WPML_TM_ATE {
	private $translation_method_ate_enabled;

	public function is_translation_method_ate_enabled() {
		if ( null === $this->translation_method_ate_enabled ) {
			$tm_settings            = wpml_get_setting_filter( null, 'translation-management' );
			$doc_translation_method = null;
			if ( array_key_exists( 'doc_translation_method', $tm_settings ) ) {
				$doc_translation_method = $tm_settings['doc_translation_method'];
			}
			$this->translation_method_ate_enabled = $doc_translation_method === ICL_TM_TMETHOD_ATE;
		}

		return $this->translation_method_ate_enabled;
	}

}