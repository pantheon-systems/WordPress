<?php

/**
 * Class WPML_TF_Common_Hooks_Factory
 *
 * @author OnTheGoSystems
 */
class WPML_TF_WP_Cron_Events_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	/** @return WPML_TF_WP_Cron_Events */
	public function create() {
		return new WPML_TF_WP_Cron_Events(
			new WPML_TF_Settings_Read(),
			new WPML_TF_TP_Ratings_Synchronize_Factory()
		);
	}
}
