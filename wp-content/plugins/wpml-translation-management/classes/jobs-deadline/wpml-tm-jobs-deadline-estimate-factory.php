<?php

class WPML_TM_Jobs_Deadline_Estimate_Factory {

	public function create() {
		global $wpdb;
		$word_count_records_factory = new WPML_TM_Word_Count_Records_Factory();
		$word_count_records         = $word_count_records_factory->create();
		$single_process_factory     = new WPML_TM_Word_Count_Single_Process_Factory();
		$single_process             = $single_process_factory->create();
		$st_package_factory = class_exists( 'WPML_ST_Package_Factory' ) ? new WPML_ST_Package_Factory() : null;
		$translatable_element_provider = new WPML_TM_Translatable_Element_Provider( $word_count_records, $single_process, $st_package_factory );
		$translation_jobs_collection = new WPML_Translation_Jobs_Collection( $wpdb, array() );

		return new WPML_TM_Jobs_Deadline_Estimate( $translatable_element_provider, $translation_jobs_collection );
	}
}
