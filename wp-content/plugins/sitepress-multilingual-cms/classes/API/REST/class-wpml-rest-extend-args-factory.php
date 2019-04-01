<?php

/**
 * @author OnTheGo Systems
 */
class WPML_REST_Extend_Args_Factory implements IWPML_REST_Action_Loader {
	/**
	 * @return IWPML_Action|IWPML_Action[]|null
	 */
	public function create() {
		global $sitepress;

		return new WPML_REST_Extend_Args( $sitepress );
	}
}
