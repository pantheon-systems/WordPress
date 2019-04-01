<?php

class WPML_Elementor_DB_Factory {

	/**
	 * @return null|WPML_Elementor_DB
	 */
	public function create() {
		$wpml_elementor_db = null;

		if ( version_compare( phpversion(), '5.3.0', '>=' ) && class_exists( '\Elementor\DB' ) ) {
			// @codingStandardsIgnoreLine
			$elementor_db = new \Elementor\DB();

			if ( method_exists( $elementor_db, 'save_plain_text' ) ) {
				$wpml_elementor_db = new WPML_Elementor_DB( $elementor_db );
			}
		}

		return $wpml_elementor_db;
	}
}
