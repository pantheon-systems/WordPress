<?php

/**
 * Class WPML_TF_Backend_Feedback_List_View_Factory
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Feedback_List_View_Factory {

	public function create() {
		/** @var SitePress $sitepress*/
		global $sitepress;

		$template_loader = new WPML_Twig_Template_Loader(
			array( WPML_PLUGIN_PATH . WPML_TF_Backend_Feedback_List_View::TEMPLATE_FOLDER ) );

		$feedback_query = new WPML_TF_Feedback_Query(
			new WPML_TF_Data_Object_Storage( new WPML_TF_Feedback_Post_Convert() ),
			new WPML_TF_Data_Object_Storage( new WPML_TF_Message_Post_Convert() ),
			new WPML_TF_Collection_Filter_Factory()
		);

		return new WPML_TF_Backend_Feedback_List_View(
			$template_loader->get_template(),
			$feedback_query,
			new WPML_Admin_Pagination(),
			new WPML_Admin_Table_Sort(),
			new WPML_TF_Feedback_Page_Filter( $sitepress, $feedback_query )
		);
	}

}