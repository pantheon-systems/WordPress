<?php

interface WPML_TM_Jobs_Summary_Report_Model {

	/**
	 * @return string
	 */
	public function get_subject();

	/**
	 * @return string
	 */
	public function get_summary_text();
}