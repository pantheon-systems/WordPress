<?php

class WPML_Admin_Pagination_Factory {
	/**
	 * @var int
	 */
	private $items_per_page;

	public function __construct( $items_per_page ) {
		$this->items_per_page = $items_per_page;
	}

	/**
	 * @return WPML_Admin_Pagination_Render
	 */
	public function create( $total_items, $page_param_name = 'paged' ) {
		$pagination = new WPML_Admin_Pagination();
		$pagination->set_total_items( $total_items );
		$pagination->set_items_per_page( $this->items_per_page );
		$pagination->set_page_param_name($page_param_name);

		$page = 1;
		if ( isset( $_GET[ $page_param_name ] ) ) {
			$page = filter_var( $_GET[ $page_param_name ], FILTER_SANITIZE_NUMBER_INT );
		}
		$pagination->set_current_page( $page );

		$template = new WPML_Twig_Template_Loader(
			array(
				WPML_PLUGIN_PATH . '/templates/pagination'
			)
		);

		return new WPML_Admin_Pagination_Render( $template->get_template(), $pagination );
	}
}