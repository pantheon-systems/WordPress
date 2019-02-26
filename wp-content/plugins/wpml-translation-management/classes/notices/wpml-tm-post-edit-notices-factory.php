<?php

class WPML_TM_Post_Edit_Notices_Factory {

	const TEMPLATES_PATH = '/templates/notices/post-edit/';

	public function create() {
		/**
		 * @var SitePress $sitepress
		 * @var TranslationManagement $iclTranslationManagement
		 * @var WPML_TM_Translation_Status_Display $wpml_tm_status_display_filter
		 */
		global $sitepress, $iclTranslationManagement, $wpml_tm_status_display_filter;

		$status_helper = wpml_get_post_status_helper();

		$paths = array( WPML_TM_PATH . self::TEMPLATES_PATH );
		$template_service_loader = new WPML_Twig_Template_Loader( $paths );
		$template_service = $template_service_loader->get_template();

		$super_globals = new WPML_Super_Globals_Validation();

		if ( ! $wpml_tm_status_display_filter ) {
			wpml_tm_load_status_display_filter();
		}

		$use_translation_editor = ICL_TM_TMETHOD_EDITOR === (int) $iclTranslationManagement->settings['doc_translation_method'];

		return new WPML_TM_Post_Edit_Notices(
			$status_helper,
			$sitepress,
			$template_service,
			$super_globals,
			$wpml_tm_status_display_filter,
			new WPML_Translation_Element_Factory( $sitepress, new WPML_WP_Cache() ),
			$use_translation_editor
		);
	}
}
