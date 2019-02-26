<?php

class WPML_Meta_Boxes_Post_Edit_Ajax_Factory implements IWPML_Backend_Action_Loader, IWPML_Ajax_Action_Loader {

	/**
	 * @return WPML_Meta_Boxes_Post_Edit_Ajax
	 */
	public function create() {
		global $sitepress, $wpml_post_translations;

		$post_edit_metabox = new WPML_Meta_Boxes_Post_Edit_HTML( $sitepress, $wpml_post_translations );
		return new WPML_Meta_Boxes_Post_Edit_Ajax( $post_edit_metabox );
	}
}