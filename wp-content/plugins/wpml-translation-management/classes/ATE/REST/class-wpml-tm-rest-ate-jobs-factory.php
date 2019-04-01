<?php

class WPML_TM_REST_ATE_Jobs_Factory extends WPML_REST_Factory_Loader {

	public function create() {
		$ate_jobs_records = new WPML_TM_ATE_Job_Records();
		$ate_jobs         = new WPML_TM_ATE_Jobs( $ate_jobs_records );

		return new WPML_TM_REST_ATE_Jobs( $ate_jobs );
	}
}