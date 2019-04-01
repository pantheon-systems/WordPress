<?php

class WPML_TM_Word_Count_Process_Hooks implements IWPML_Action {

	/** @var WPML_TM_Word_Count_Background_Process_Factory $process_factory */
	private $process_factory;

	/**
	 * @param WPML_TM_Word_Count_Background_Process_Factory $process_factory
	 */
	public function __construct( WPML_TM_Word_Count_Background_Process_Factory $process_factory ) {
		$this->process_factory = $process_factory;
	}

	/**
	 * We need to include the hooks located in WP_Async_Request::__construct.
	 */
	public function add_hooks() {
		$this->process_factory->create_requested_types();
	}
}
