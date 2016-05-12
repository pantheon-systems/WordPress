<?php
/**
 *
 */
abstract class BackWPup_JobTypes {

	public $info = array();

	/**
	 *
	 */
	abstract public function __construct();

	/**
	 * Get the default Options
	 *
	 * @return array of default options
	 */
	abstract public function option_defaults();

	/**
	 * @param $jobid
	 */
	abstract public function edit_tab( $jobid );

	/**
	 * @param $jobid
	 */
	abstract public function edit_form_post_save( $jobid );

	/**
	 * use wp_enqueue_script() here to load js for tab
	 */
	public function admin_print_scripts() {

	}

	/**
	 *
	 */
	public function edit_inline_js() {

	}

	/**
	 *
	 */
	public function edit_ajax() {

	}

	/**
	 *
	 */
	public function wizard_admin_print_styles() {

	}

	/**
	 *
	 */
	public function wizard_admin_print_scripts() {

	}

	/**
	 *
	 */
	public function wizard_inline_js() {

	}

	/**
	 * @param $job_settings
	 */
	public function wizard_page( array $job_settings ) {

	}

	/**
	 * @param $job_settings
	 *
	 * @return array
	 */
	public function wizard_save( array $job_settings ) {

		return $job_settings;
	}

	/**
	 *
	 */
	public function admin_print_styles() {

	}

	/**
	 * @return bool
	 */
	public function creates_file() {

		return FALSE;
	}

	/**
	 * @param $job_object BackWPup_Job Object
	 * @return bool
	 */
	abstract public function job_run( BackWPup_Job $job_object );
}
