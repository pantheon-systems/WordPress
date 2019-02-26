<?php
require WPML_ST_PATH . '/inc/functions.php';
require WPML_ST_PATH . '/inc/private-actions.php';
require WPML_ST_PATH . '/inc/private-filters.php';

/**
 * @return WPML_Admin_Texts
 */
function wpml_st_load_admin_texts() {
	global $wpml_st_admin_texts;

	if ( ! $wpml_st_admin_texts ) {
		global $iclTranslationManagement, $WPML_String_Translation;
		$wpml_st_admin_texts = new WPML_Admin_Texts( $iclTranslationManagement, $WPML_String_Translation );
	}

	return $wpml_st_admin_texts;
}

/**
 * @return WPML_ST_String_Factory
 */
function wpml_st_load_string_factory() {
	global $wpml_st_string_factory, $wpdb;

	if ( ! isset( $wpml_st_string_factory ) ) {
		$wpml_st_string_factory = new WPML_ST_String_Factory( $wpdb );
	}

	return $wpml_st_string_factory;
}
