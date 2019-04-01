<?php

/**
 * Class WPML_Media_Editor_Notices_Factory
 */
class WPML_Media_Editor_Notices_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		return new WPML_Media_Editor_Notices();
	}

}
