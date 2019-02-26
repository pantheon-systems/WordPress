<?php

/**
 * Class WPML_TF_Feedback_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Feedback_Factory {

	/**
	 * @param array $feedback_data
	 *
	 * @return WPML_TF_Feedback
	 */
	public function create( array $feedback_data ) {
		global $sitepress;

		$document_information = new WPML_TF_Backend_Document_Information(
			$sitepress,
			class_exists( 'WPML_TP_Client_Factory' ) ? new WPML_TP_Client_Factory() : null
		);

		return new WPML_TF_Feedback( $feedback_data, $document_information );
	}
}
