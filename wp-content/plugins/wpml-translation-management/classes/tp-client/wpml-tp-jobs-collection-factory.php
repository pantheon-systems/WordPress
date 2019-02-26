<?php

class WPML_TP_Jobs_Collection_Factory {

	/**
	 * @return WPML_TP_Jobs_Collection
	 */
	public function create() {
		$tp_jobs_collection = null;
		$current_project = TranslationProxy::get_current_project();

		if ( $current_project ) {
			$tp_jobs_collection = new WPML_TP_Jobs_Collection(
				TranslationProxy::get_current_project(),
				new WPML_TP_Job_Factory(),
				new WPML_Translation_Batch_Factory()
			);
		}

		return $tp_jobs_collection;
	}
}