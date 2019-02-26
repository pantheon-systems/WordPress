<?php

class WPML_Admin_Pagination_Render {

	const TEMPLATE = 'pagination.twig';

	/**
	 * @var IWPML_Template_Service
	 */
	private $template;

	/**
	 * @var WPML_Admin_Pagination
	 */
	private $pagination;

	public function __construct( IWPML_Template_Service $template, WPML_Admin_Pagination $pagination ) {
		$this->template = $template;
		$this->pagination = $pagination;
	}

	public function get_model() {
		$model = array(
			'strings' => array(
				'list_navigation' => __( 'Navigation', 'sitepress' ),
				'first_page'      => __( 'First page', 'sitepress' ),
				'previous_page'   => __( 'Previous page', 'sitepress' ),
				'next_page'       => __( 'Next page', 'sitepress' ),
				'last_page'       => __( 'Last page', 'sitepress' ),
				'current_page'    => __( 'Current page', 'sitepress' ),
				'of'              => __( 'of', 'sitepress' ),
			),
			'pagination' => $this->pagination,
			'total_items' => $this->pagination->get_total_items(),
			'total_items_text' => sprintf(
				_n( '%s item', '%s items', $this->pagination->get_total_items(), 'sitepress' ),
				$this->pagination->get_total_items()
			),
		);

		return $model;
	}

	/**
	 * @param array $items
	 *
	 * @return array
	 */
	public function paginate( $items ) {
		$total      = count( $items );
		$limit      = $this->pagination->get_items_per_page(); //per page
		$total_pages = ceil( $total / $limit );
		$page       = max( $this->pagination->get_current_page(), 1 );
		$page       = min( $page, $total_pages );
		$offset     = ( $page - 1 ) * $limit;

		if ( $offset < 0 ) {
			$offset = 0;
		}

		return array_slice( $items, $offset, $limit );

	}
}