<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 4/10/17
 * Time: 10:15 AM
 */

class WPML_Translation_Modes {

	public function is_translatable_mode( $mode ) {
		$mode = (int) $mode;

		return $mode === WPML_CONTENT_TYPE_TRANSLATE || $mode === WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED;
	}

	public function get_options_for_post_type( $post_type_label ) {
		return array(
			WPML_CONTENT_TYPE_DONT_TRANSLATE           => sprintf( __( "Do not make '%s' translatable", 'sitepress' ), $post_type_label ),
			WPML_CONTENT_TYPE_TRANSLATE                => sprintf( __( "Make '%s' translatable", 'sitepress' ), $post_type_label ),
			WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED => sprintf( __( "Make '%s' appear as translated", 'sitepress' ), $post_type_label ),
		);
	}

	public function get_options() {
		return array(
			WPML_CONTENT_TYPE_TRANSLATE                => __( 'Translatable - only show translated items', 'sitepress' ),
			WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED => __( 'Translatable - use translation if available or fallback to default language', 'sitepress' ),
			WPML_CONTENT_TYPE_DONT_TRANSLATE           => __( 'Not translatable', 'sitepress' ),
		);
	}
}