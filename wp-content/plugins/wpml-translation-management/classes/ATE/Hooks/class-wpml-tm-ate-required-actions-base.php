<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ATE_Required_Actions_Base {
	private $ate_enabled;

	protected function is_ate_enabled() {
		if ( null === $this->ate_enabled ) {
			$tm_settings            = wpml_get_setting_filter( null, 'translation-management' );
			$doc_translation_method = null;
			if ( array_key_exists( 'doc_translation_method', $tm_settings ) ) {
				$doc_translation_method = $tm_settings['doc_translation_method'];
			}
			$this->ate_enabled = $doc_translation_method === ICL_TM_TMETHOD_ATE;
		}

		return $this->ate_enabled;
	}
}