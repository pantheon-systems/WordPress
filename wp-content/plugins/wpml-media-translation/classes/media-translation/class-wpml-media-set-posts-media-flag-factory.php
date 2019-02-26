<?php

class WPML_Media_Set_Posts_Media_Flag_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $wpdb;

		$post_media_usage_factory = new WPML_Media_Post_Media_Usage_Factory();

		return new WPML_Media_Set_Posts_Media_Flag(
			$wpdb,
			new WPML_Notices( new WPML_Notice_Render() ),
			$post_media_usage_factory->create(),
			new WPML_Media_Post_With_Media_Files_Factory()
		);
	}

}