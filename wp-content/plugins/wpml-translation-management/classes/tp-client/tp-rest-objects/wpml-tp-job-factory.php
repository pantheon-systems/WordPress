<?php

class WPML_TP_Job_Factory {

	/**
	 * @param stdClass $job
	 *
	 * @return WPML_TP_Job
	 */
	public function create( stdClass $job ) {
		return new WPML_TP_Job( $job );
	}
}