<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Custom_Columns_Factory implements IWPML_Backend_Action_Loader, IWPML_Ajax_Action_Loader {
	private $hooks;

	/**
	 * @return WPML_Custom_Columns
	 */
	public function create() {
		global $sitepress;

		return new WPML_Custom_Columns( $sitepress );
	}

}
