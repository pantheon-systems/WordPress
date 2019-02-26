<?php

class WPML_TM_Word_Count_Ajax_Hooks implements IWPML_Action {

	/** @var WPML_TM_Word_Count_Report $report */
	private $report;

	/** @var WPML_TM_Word_Count_Background_Process_Factory $process_factory*/
	private $process_factory;

	/** @var bool $requested_types_status */
	private $requested_types_status;

	public function __construct(
		WPML_TM_Word_Count_Report $report,
		WPML_TM_Word_Count_Background_Process_Factory $process_factory,
		$requested_types_status
	) {
		$this->report                 = $report;
		$this->process_factory        = $process_factory;
		$this->requested_types_status = $requested_types_status;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_wpml_word_count_get_report', array( $this, 'get_report' ) );
		add_action( 'wp_ajax_wpml_word_count_start_count', array( $this, 'start_count' ) );
		add_action( 'wp_ajax_wpml_word_count_cancel_count', array( $this, 'cancel_count' ) );
	}

	public function __call( $method_name, $arguments ) {
		check_admin_referer( WPML_TM_Word_Count_Hooks_Factory::NONCE_ACTION, 'nonce' );
		$this->{$method_name}();
		wp_send_json_success();
	}

	private function get_report() {
		$data = array(
			'status' => $this->requested_types_status,
			'report' => $this->report->render(),
		);

		wp_send_json_success( $data );
	}

	private function start_count() {
		$requested_types = array();

		foreach ( array( 'post_types', 'package_kinds' ) as $group ) {
			$requested_types[ $group ] = isset( $_POST['requested_types'][ $group ] )
				? filter_var_array( $_POST['requested_types'][ $group ] ) : array();
		}

		$requested_types_process = $this->process_factory->create_requested_types();
		$this->report->set_requested_types( $requested_types );
		$requested_types_process->init( $requested_types );
	}

	private function cancel_count() {
		update_option(
			WPML_TM_Word_Count_Hooks_Factory::OPTION_KEY_REQUESTED_TYPES_STATUS,
			WPML_TM_Word_Count_Hooks_Factory::PROCESS_COMPLETED
		);
	}
}
