<?php

/**
 * Class WPML_TM_MCS_Pagination_Render
 */
class WPML_TM_MCS_Pagination_Render {

	/**
	 * Twig template path.
	 */
	const TM_MCS_PAGINATION_TEMPLATE = 'tm-mcs-pagination.twig';

	/**
	 * @var IWPML_Template_Service
	 */
	private $template;

	/**
	 * @var WPML_Admin_Pagination
	 */
	private $pagination;

	/**
	 * @var int Items per page
	 */
	private $items_per_page;

	/**
	 * @var int Total items
	 */
	private $total_items;

	/**
	 * @var int Current page
	 */
	private $current_page;

	/**
	 * @var int Total pages
	 */
	private $total_pages;

	/**
	 * WPML_TM_MCS_Pagination_Render constructor.
	 *
	 * @param IWPML_Template_Service $template Twig template service.
	 * @param WPML_Admin_Pagination $pagination Admin pagination object.
	 */
	public function __construct( IWPML_Template_Service $template, WPML_Admin_Pagination $pagination ) {
		$this->template       = $template;
		$this->pagination     = $pagination;
		$this->items_per_page = $pagination->get_items_per_page();
		$this->total_items    = $pagination->get_total_items();
		$this->current_page   = $pagination->get_current_page();
		$this->total_pages    = $pagination->get_total_pages();
	}

	/**
	 * Get twig model.
	 *
	 * @return array
	 */
	private function get_model() {
		$from = min( ( $this->current_page - 1 ) * $this->items_per_page + 1, $this->total_items );
		$to   = min( $this->current_page * $this->items_per_page, $this->total_items );
		if ( - 1 === $this->current_page ) {
			$from = min( 1, $this->total_items );
			$to   = $this->total_items;
		}

		$model = array(
			'strings'        => array(
				'displaying'    => __( 'Displaying', 'wpml-translation-management' ),
				'of'            => __( 'of', 'wpml-translation-management' ),
				'display_all'   => __( 'Display all results', 'wpml-translation-management' ),
				'display_less'  => __( 'Display 20 results per page', 'wpml-translation-management' ),
				'nothing_found' => __( 'Nothing found', 'wpml-translation-management' ),
			),
			'pagination'     => $this->pagination,
			'from'           => number_format_i18n( $from ),
			'to'             => number_format_i18n( $to ),
			'current_page'   => number_format_i18n( $this->current_page ),
			'total_items'    => $this->total_items,
			'total_pages'    => $this->total_pages,
			'paginate_links' => $this->paginate_links(),
			'select'         => array( 10, 20, 50, 100 ),
			'select_value'   => $this->items_per_page,
		);

		return $model;
	}

	/**
	 * Render model via twig.
	 *
	 * @return mixed
	 */
	public function render() {
		return $this->template->show( $this->get_model(), self::TM_MCS_PAGINATION_TEMPLATE );
	}

	/**
	 * Paginate links.
	 *
	 * @return array
	 */
	public function paginate_links() {
		$page_links = array();

		if ( - 1 === $this->current_page ) {
			return $page_links;
		}

		if ( $this->total_pages < 2 ) {
			return $page_links;
		}

		$end_size = 1;
		$mid_size = 2;
		$dots     = false;

		for ( $n = 1; $n <= $this->total_pages; $n ++ ) {
			if ( $n === $this->current_page ) {
				$page_links[] = array(
					'class'  => 'current',
					'number' => number_format_i18n( $n ),
				);
			} else {
				if (
					$n <= $end_size ||
					( $this->current_page && $n >= $this->current_page - $mid_size && $n <= $this->current_page + $mid_size ) ||
					$n > $this->total_pages - $end_size
				) {
					$page_links[] = array(
						'class'  => '',
						'number' => number_format_i18n( $n ),
					);
					$dots         = true;
				} elseif ( $dots ) {
					$page_links[] = array(
						'class'  => 'dots',
						'number' => '…',
					);
					$dots         = false;
				}
			}
		}

		return $page_links;
	}
}
