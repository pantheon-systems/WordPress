<?php

class WPML_Media_Post_With_Media_Files_Factory {
	/**
	 * @param $post_id
	 *
	 * @return WPML_Media_Post_With_Media_Files
	 */
	public function create( $post_id ) {
		global $sitepress, $iclTranslationManagement;

		return new WPML_Media_Post_With_Media_Files(
			$post_id,
			new WPML_Media_Img_Parse(),
			new WPML_Media_Attachment_By_URL_Factory(),
			$sitepress,
			new WPML_Custom_Field_Setting_Factory( $iclTranslationManagement )
		);
	}
}