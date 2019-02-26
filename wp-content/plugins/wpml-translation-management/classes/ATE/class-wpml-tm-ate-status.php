<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ATE_Status {

	public static function is_enabled() {
		$tm_settings            = wpml_get_setting_filter( null, 'translation-management' );
		$doc_translation_method = null;
		if ( is_array( $tm_settings ) && array_key_exists( 'doc_translation_method', $tm_settings ) ) {
			$doc_translation_method = $tm_settings['doc_translation_method'];
		}
		return $doc_translation_method === ICL_TM_TMETHOD_ATE;
	}

	public static function is_active() {
		$ams_data = get_option( WPML_TM_ATE_Authentication::AMS_DATA_KEY, array() );
		if ( $ams_data && array_key_exists( 'status', $ams_data ) ) {
			return $ams_data['status'] === WPML_TM_ATE_Authentication::AMS_STATUS_ACTIVE;
		}

		return false;
	}

	public static function is_enabled_and_activated() {
		return self::is_enabled() && self::is_active();
	}
}