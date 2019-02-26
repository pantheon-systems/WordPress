<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_Support_Info {

	public function is_simplexml_extension_loaded() {
		return extension_loaded( 'simplexml' );
	}
}
