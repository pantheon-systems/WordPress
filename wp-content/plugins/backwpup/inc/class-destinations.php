<?php
/**
 * Base class for adding BackWPup destinations.
 *
 * @package    BackWPup
 * @subpackage BackWPup_Destinations
 * @since      3.0.0
 * @access private
 */
abstract class BackWPup_Destinations {

	/**
	 * @return array
	 */
	abstract public function option_defaults();

	/**
	 * @param $jobid int
	 */
	abstract public function edit_tab( $jobid );

	/**
	 * @param $jobid int
	 */
	public function edit_auth( $jobid ) {

	}

	/**
	 * @param $jobid int
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
	 * @param $job_settings array
	 */
	public function wizard_page( array $job_settings ) {

		echo '<br /><pre>';
		print_r( $job_settings );
		echo '</pre>';
	}

	/**
	 * @param $job_settings array
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
	 * @param $jobdest string
	 * @param $backupfile
	 */
	public function file_delete( $jobdest, $backupfile ) {

	}

	/**
	 * @param $jobid int
	 * @param $get_file
	 */
	public function file_download( $jobid, $get_file ) {

	}

	/**
	 * @param $jobdest string
	 * @return array
	 */
	public function file_get_list( $jobdest ) {

		return FALSE;
	}

	/**
	 * @param $job_object BackWPup_Job
	 */
	abstract public function job_run_archive( BackWPup_Job $job_object );

	/**
	 * @param $job_object BackWPup_Job
	 */
	public function job_run_sync( BackWPup_Job $job_object ) {

	}

	/**
	 * @param $job_settings array
	 * @return bool
	 */
	abstract public function can_run( array $job_settings );
}
