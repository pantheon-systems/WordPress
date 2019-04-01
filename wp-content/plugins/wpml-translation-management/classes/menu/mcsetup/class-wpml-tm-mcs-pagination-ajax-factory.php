<?php

/**
 * Class WPML_TM_MCS_Pagination_Ajax_Factory
 */
class WPML_TM_MCS_Pagination_Ajax_Factory implements IWPML_AJAX_Action_Loader {

	/**
	 * Create MCS Pagination.
	 *
	 * @return WPML_TM_MCS_Pagination_Ajax
	 */
	public function create() {
		/** @var TranslationManagement $iclTranslationManagement */
		global $iclTranslationManagement;

		return new WPML_TM_MCS_Pagination_Ajax( $iclTranslationManagement );
	}
}
