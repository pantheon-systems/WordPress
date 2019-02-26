<?php

class WPML_Deactivate_Old_Media_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		return new WPML_Deactivate_Old_Media( new WPML_PHP_Functions() );
	}
}