<?php

class WPML_TM_Jobs_Deadline_Cron_Hooks_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	public function create() {
		global $iclTranslationManagement;

		return new WPML_TM_Jobs_Deadline_Cron_Hooks( new WPML_TM_Overdue_Jobs_Report_Factory(), $iclTranslationManagement );
	}
}
