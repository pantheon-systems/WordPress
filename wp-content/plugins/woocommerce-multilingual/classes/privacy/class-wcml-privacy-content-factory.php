<?php

/**
 * @author OnTheGo Systems
 */
class WCML_Privacy_Content_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return IWPML_Action
	 */
	public function create() {
		if ( class_exists( 'WPML_Privacy_Content' ) ) {
			return new WCML_Privacy_Content();
		}

		return null;
	}
}