<?php

/**
 * Class WPML_TM_MCS_Search_Factory
 */
class WPML_TM_MCS_Search_Factory {
	/**
	 * Create MCS Search.
	 *
	 * @param string $search_string
	 *
	 * @return WPML_TM_MCS_Search_Render
	 */
	public function create( $search_string = '' ) {
		$template = new WPML_Twig_Template_Loader(
			array(
				WPML_TM_PATH . '/templates/menus/mcsetup',
			)
		);

		return new WPML_TM_MCS_Search_Render( $template->get_template(), $search_string );
	}
}
