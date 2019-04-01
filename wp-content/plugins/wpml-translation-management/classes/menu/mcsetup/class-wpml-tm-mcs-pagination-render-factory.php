<?php

/**
 * Class WPML_TM_MCS_Pagination_Render_Factory
 */
class WPML_TM_MCS_Pagination_Render_Factory {
	/**
	 * @var int Items per page
	 */
	private $items_per_page;

	/**
	 * WPML_TM_MCS_Pagination_Render_Factory constructor.
	 *
	 * @param int $items_per_page
	 */
	public function __construct( $items_per_page ) {
		$this->items_per_page = $items_per_page;
	}

	/**
	 * @param $items_per_page
	 * @param $total_items
	 * @param int $current_page
	 *
	 * @return WPML_TM_MCS_Pagination_Render
	 */
	public function create( $total_items, $current_page = 1 ) {
		$pagination = new WPML_Admin_Pagination();
		$pagination->set_items_per_page( $this->items_per_page );
		$pagination->set_total_items( $total_items );
		$pagination->set_current_page( $current_page );


		$template = new WPML_Twig_Template_Loader(
			array(
				WPML_TM_PATH . '/templates/menus/mcsetup',
			)
		);

		return new WPML_TM_MCS_Pagination_Render( $template->get_template(), $pagination );
	}
}
