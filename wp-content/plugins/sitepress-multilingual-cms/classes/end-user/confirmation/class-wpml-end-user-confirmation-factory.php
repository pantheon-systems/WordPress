<?php

class WPML_End_User_Confirmation_Factory implements IWPML_Frontend_Action_Loader, IWPML_Backend_Action_Loader {
	/**
	 * @return WPML_End_User_Registration_Confirmation
	 */
	public function create() {
		return new WPML_End_User_Registration_Confirmation(
			new WPML_End_User_Confirmation_Auth(),
			new WPML_End_User_Notice_Action_Execution()
		);
	}
}