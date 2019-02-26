<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Media_Privacy_Content_Factory implements IWPML_Backend_Action_Loader {

	/**
	 * @return IWPML_Action
	 */
	public function create() {
		return new WPML_Media_Privacy_Content();
	}
}