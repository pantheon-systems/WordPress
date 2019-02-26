<?php

/**
 * Class WPML_TP_Client_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_TP_Client_Factory {

	/** @return WPML_TP_Client */
	public function create() {
		global $sitepress, $wpdb;

		$translation_service  = $sitepress->get_setting( 'translation_service' );
		$translation_projects = $sitepress->get_setting( 'icl_translation_projects' );

		$project = new WPML_TP_Project( $translation_service, $translation_projects );
		$tm_jobs = new WPML_TP_TM_Jobs( $wpdb );

		return new WPML_TP_Client( $project, $tm_jobs );
	}
}
