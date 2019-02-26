<?php

/**
 * Class WPML_TF_XML_RPC_Hooks_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_TF_XML_RPC_Hooks_Factory implements IWPML_Frontend_Action_Loader {

	/** @return WPML_TF_XML_RPC_Hooks */
	public function create() {
		return new WPML_TF_XML_RPC_Hooks(
			new WPML_TF_XML_RPC_Feedback_Update_Factory(),
			new WPML_WP_API()
		);
	}
}
