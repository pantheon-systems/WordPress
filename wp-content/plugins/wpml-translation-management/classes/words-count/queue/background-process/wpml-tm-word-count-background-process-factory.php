<?php

class WPML_TM_Word_Count_Background_Process_Factory {

	const PREFIX                 = 'wpml_tm';
	const ACTION_REQUESTED_TYPES = 'word_count_background_process_requested_types';

	/**
	 * @return WPML_TM_Word_Count_Background_Process_Requested_Types
	 */
	public function create_requested_types() {
		$records_factory   = new WPML_TM_Word_Count_Records_Factory();
		$records           = $records_factory->create();

		$setters_factory = new WPML_TM_Word_Count_Setters_Factory();
		$setters         = $setters_factory->create();

		$requested_types_items = new WPML_TM_Word_Count_Queue_Items_Requested_Types( $records );

		return new WPML_TM_Word_Count_Background_Process_Requested_Types( $requested_types_items, $setters, $records );
	}
}