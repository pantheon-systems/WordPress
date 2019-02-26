<?php

class WPML_TM_Jobs_Summary_Report_Hooks_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader {

	/**
	 * @return WPML_TM_Jobs_Summary_Report_Hooks
	 */
	public function create() {
		global $iclTranslationManagement;

		return new WPML_TM_Jobs_Summary_Report_Hooks(
			new WPML_TM_Jobs_Summary_Report_Process_Factory(),
			$iclTranslationManagement
		);
	}
}