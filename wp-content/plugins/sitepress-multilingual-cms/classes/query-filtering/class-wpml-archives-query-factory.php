<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 26/10/17
 * Time: 5:36 PM
 */

class WPML_Archives_Query_Factory implements IWPML_Frontend_Action_Loader {

	public function create() {
		global $sitepress, $wpdb;
		return new WPML_Archives_Query(
			$wpdb,
			new WPML_Language_Where_Clause(
				$sitepress,
				$wpdb,
				new WPML_Display_As_Translated_Posts_Query( $wpdb )
			)
		);
	}
}