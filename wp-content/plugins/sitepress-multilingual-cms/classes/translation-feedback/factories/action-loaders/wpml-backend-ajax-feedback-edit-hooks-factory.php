<?php

/**
 * Class WPML_TF_Backend_AJAX_Feedback_Edit_Hooks_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_AJAX_Feedback_Edit_Hooks_Factory extends WPML_AJAX_Base_Factory implements IWPML_Backend_Action_Loader {

	const AJAX_ACTION = 'wpml-tf-backend-feedback-edit';

	public function create() {
		$hooks = null;

		if ( $this->is_valid_action( self::AJAX_ACTION ) ) {
			$feedback_storage = new WPML_TF_Data_Object_Storage( new WPML_TF_Feedback_Post_Convert() );
			$message_storage  = new WPML_TF_Data_Object_Storage( new WPML_TF_Message_Post_Convert() );

			$feedback_query = new WPML_TF_Feedback_Query(
				$feedback_storage,
				$message_storage,
				new WPML_TF_Collection_Filter_Factory()
			);

			$feedback_edit = new WPML_TF_Feedback_Edit(
				$feedback_query,
				$feedback_storage,
				$message_storage,
				class_exists( 'WPML_TP_Client_Factory' ) ? new WPML_TP_Client_Factory() : null
			);

			$template_loader = new WPML_Twig_Template_Loader(
				array( WPML_PLUGIN_PATH . WPML_TF_Backend_Feedback_Row_View::TEMPLATE_FOLDER )
			);
			$row_view = new WPML_TF_Backend_Feedback_Row_View( $template_loader->get_template() );

			$hooks = new WPML_TF_Backend_AJAX_Feedback_Edit_Hooks(
				$feedback_edit,
				$row_view,
				$_POST
			);
		}

		return $hooks;
	}
}
