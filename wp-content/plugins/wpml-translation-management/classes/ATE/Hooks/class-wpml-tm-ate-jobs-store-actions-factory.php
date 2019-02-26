<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ATE_Jobs_Store_Actions_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return IWPML_Action|IWPML_Action[]|null
	 */
	public function create() {
		if ( WPML_TM_ATE_Status::is_enabled() ) {

			$ate_jobs_records = new WPML_TM_ATE_Job_Records();
			$ate_jobs         = new WPML_TM_ATE_Jobs( $ate_jobs_records );

			return new WPML_TM_ATE_Jobs_Store_Actions( $ate_jobs );
		}
	}
}