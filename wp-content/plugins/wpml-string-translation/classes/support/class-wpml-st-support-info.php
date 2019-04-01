<?php

/**
 * @author OnTheGo Systems
 */
class WPML_ST_Support_Info {

	public function is_mbstring_extension_loaded() {
		return extension_loaded( 'mbstring' );
	}
}
