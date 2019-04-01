<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_ATE_Post_Edit_Actions_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return IWPML_Action|IWPML_Action[]|null
	 */
	public function create() {
		$tm_ate    = new WPML_TM_ATE();
		$endpoints = new WPML_TM_ATE_AMS_Endpoints();

		if ( $tm_ate->is_translation_method_ate_enabled() ) {
			return new WPML_TM_ATE_Post_Edit_Actions( $endpoints );
		}

		return null;
	}
}