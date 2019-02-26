<?php

class WPML_Media_Translation_Editor_Layout_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		return new WPML_Media_Translation_Editor_Layout();
	}

}