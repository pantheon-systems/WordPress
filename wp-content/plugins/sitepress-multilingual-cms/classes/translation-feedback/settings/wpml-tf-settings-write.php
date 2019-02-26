<?php

/**
 * Class WPML_TF_Settings_Write
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Settings_Write extends WPML_TF_Settings_Handler {

	/**
	 * @param IWPML_TF_Settings $settings
	 *
	 * @return bool
	 */
	public function save( IWPML_TF_Settings $settings ) {
		return update_option( $this->get_option_name( get_class( $settings ) ), $settings->get_properties() );
	}
}
