<?php

class WCML_Append_Gallery_To_Post_Media_Ids_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		return new WCML_Append_Gallery_To_Post_Media_Ids();
	}
}