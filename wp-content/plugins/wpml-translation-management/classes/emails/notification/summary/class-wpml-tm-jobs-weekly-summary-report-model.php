<?php

class WPML_TM_Jobs_Weekly_Summary_Report_Model implements WPML_TM_Jobs_Summary_Report_Model {

	/**
	 * @return string
	 */
	public function get_subject() {
		return __( 'Translation updates for %1$s until %2$s', 'wpml-translation-management' );
	}

	/**
	 * @return string
	 */
	public function get_summary_text() {
		return __( 'This week %1$s had the following %2$s translation updates', 'wpml-translation-management' );
	}
}