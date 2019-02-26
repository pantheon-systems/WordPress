<?php

/**
 * Class WPML_TF_Backend_Options_AJAX_Hooks_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Options_AJAX_Hooks_Factory extends WPML_AJAX_Base_Factory implements IWPML_Backend_Action_Loader {

	const AJAX_ACTION = 'wpml-tf-backend-options';

	/**
	 * @return IWPML_Action|null
	 */
	public function create() {
		global $sitepress;

		$hooks = null;

		if ( $this->is_valid_action( self::AJAX_ACTION ) ) {
			$settings_read = new WPML_TF_Settings_Read();
			/** @var WPML_TF_Settings $tf_settings */
			$tf_settings = $settings_read->get( 'WPML_TF_Settings' );

			$hooks = new WPML_TF_Backend_Options_AJAX_Hooks(
				$tf_settings,
				new WPML_TF_Settings_Write(),
				new WPML_TF_Promote_Notices( $sitepress ),
				$_POST
			);
		}

		return $hooks;
	}
}