<?php

/**
 * Class WPML_TF_Backend_Hooks_Factory
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Hooks_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return WPML_TF_Backend_Hooks
	 */
	public function create() {
		/** @var wpdb $wpdb */
		global $wpdb;

		return new WPML_TF_Backend_Hooks(
			new WPML_TF_Backend_Bulk_Actions_Factory(),
			new WPML_TF_Backend_Feedback_List_View_Factory(),
			new WPML_TF_Backend_Styles(),
			new WPML_TF_Backend_Scripts(),
			$wpdb
		);
	}
}