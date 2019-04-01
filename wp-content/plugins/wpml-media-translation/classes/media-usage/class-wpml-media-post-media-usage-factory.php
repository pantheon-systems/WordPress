<?php

class WPML_Media_Post_Media_Usage_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	public function create(){
		global $sitepress;
		return new WPML_Media_Post_Media_Usage(
			$sitepress,
			new WPML_Media_Post_With_Media_Files_Factory(),
			new WPML_Media_Usage_Factory()
		);
	}

}