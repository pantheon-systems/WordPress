<?php

/**
 * Class WPML_TF_Backend_Feedback_List_View
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Feedback_List_View {

	const TEMPLATE_FOLDER = '/templates/translation-feedback/backend/';
	const TEMPLATE_NAME   = 'feedback-list-page.twig';
	const ITEMS_PER_PAGE  = 20;

	/** @var IWPML_Template_Service $template_service */
	private $template_service;

	/** @var WPML_TF_Feedback_Query $feedback_query */
	private $feedback_query;

	/** @var WPML_Admin_Pagination $pagination */
	private $pagination;

	/** @var WPML_Admin_Table_Sort $table_sort */
	private $table_sort;

	/** @var WPML_TF_Feedback_Page_Filter $page_filter */
	private $page_filter;

	/**
	 * WPML_TF_Backend_Feedback_List_View constructor.
	 *
	 * @param IWPML_Template_Service $template_service
	 * @param WPML_TF_Feedback_Query $feedback_query
	 * @param WPML_Admin_Pagination  $pagination
	 * @param WPML_Admin_Table_Sort  $table_sort
	 * @param WPML_TF_Feedback_Page_Filter $page_filter
	 */
	public function __construct(
		IWPML_Template_Service $template_service,
		WPML_TF_Feedback_Query $feedback_query,
		WPML_Admin_Pagination $pagination,
		WPML_Admin_Table_Sort $table_sort,
		WPML_TF_Feedback_Page_Filter $page_filter
	) {
		$this->template_service = $template_service;
		$this->feedback_query   = $feedback_query;
		$this->pagination       = $pagination;
		$this->table_sort       = $table_sort;
		$this->page_filter      = $page_filter;
	}

	/** @return string */
	public function render_page() {
		$args                = $this->parse_request_args();
		$feedback_collection = $this->feedback_query->get( $args );

		$model = array(
			'strings'                => $this->get_strings(),
			'columns'                => $this->get_columns(),
			'pagination'             => $this->get_pagination( $args ),
			'page_filters'           => $this->get_page_filters(),
			'admin_page_hook'        => WPML_TF_Backend_Hooks::PAGE_HOOK,
			'current_query'          => $this->get_current_query(),
			'nonce'                  => wp_create_nonce( WPML_TF_Backend_Hooks::PAGE_HOOK ),
			'ajax_action'            => WPML_TF_Backend_AJAX_Feedback_Edit_Hooks_Factory::AJAX_ACTION,
			'ajax_nonce'             => wp_create_nonce( WPML_TF_Backend_AJAX_Feedback_Edit_Hooks_Factory::AJAX_ACTION ),
			'has_admin_capabilities' => current_user_can( 'manage_options' ),
			'is_in_trash'            => $this->feedback_query->is_in_trash(),
			'feedback_collection'    => $feedback_collection,
		);

		return $this->template_service->show( $model, self::TEMPLATE_NAME );
	}

	/** @return array */
	private function parse_request_args() {
		$args = array(
			'paged'          => 1,
			'items_per_page' => self::ITEMS_PER_PAGE,
		);

		if ( isset( $_GET['paged'] ) ) {
			$args['paged'] = filter_var( $_GET['paged'], FILTER_SANITIZE_NUMBER_INT );
		}

		if ( isset( $_GET['order'], $_GET['orderby'] ) ) {
			$args['order']   = filter_var( $_GET['order'], FILTER_SANITIZE_STRING );
			$args['orderby'] = filter_var( $_GET['orderby'], FILTER_SANITIZE_STRING );
		}

		if ( isset( $_GET['status'] ) ) {
			$args['status'] = filter_var( $_GET['status'], FILTER_SANITIZE_STRING );
		} elseif ( isset( $_GET['language'] ) ) {
			$args['language'] = filter_var( $_GET['language'], FILTER_SANITIZE_STRING );
		}

		if ( isset( $_GET['post_id'] ) ) {
			$args['post_id'] = filter_var( $_GET['post_id'], FILTER_SANITIZE_NUMBER_INT );
		}

		return $args;
	}

	/** @return array */
	private function get_strings() {
		$strings = array(
			'page_title'    => __( 'Translation Feedback', 'sitepress' ),
			'page_subtitle' => __( 'Issues with translations', 'sitepress' ),
			'table_header'  => WPML_TF_Backend_Feedback_Row_View::get_columns_strings(),
			'screen_reader' => array(
				'select_all'        => __( 'Select All', 'sitepress' ),
			),
			'pagination' => array(
				'list_navigation' => __( 'Feedback list navigation', 'sitepress' ),
				'first_page'      => __( 'First page', 'sitepress' ),
				'previous_page'   => __( 'Previous page', 'sitepress' ),
				'next_page'       => __( 'Next page', 'sitepress' ),
				'last_page'       => __( 'Last page', 'sitepress' ),
				'current_page'    => __( 'Current page', 'sitepress' ),
				'of'              => __( 'of', 'sitepress' ),
			),
			'bulk_actions' => array(
				'select'       => __( 'Select bulk action', 'sitepress' ),
				'apply_button' => __( 'Apply', 'sitepress' ),
				'options'      => array(
					'-1'      => __( 'Bulk Actions', 'sitepress' ),
					'fixed'   => __( 'Mark as fixed', 'sitepress' ),
					'pending' => __( 'Mark as new', 'sitepress' ),
					'trash'   => __( 'Move to trash', 'sitepress' ),

				),
			),
			'row_summary' => WPML_TF_Backend_Feedback_Row_View::get_summary_strings(),
			'row_details' => WPML_TF_Backend_Feedback_Row_View::get_details_strings(),
			'no_feedback' => __( 'No feedback found.', 'sitepress' ),
		);

		if ( $this->feedback_query->is_in_trash() ) {
			$strings['bulk_actions']['options'] = array(
				'-1'      => __( 'Bulk Actions', 'sitepress' ),
				'untrash' => __( 'Restore', 'sitepress' ),
				'delete'  => __( 'Delete permanently', 'sitepress' ),

			);
		}

		return $strings;
	}

	/** @return array */
	private function get_columns() {
		$this->table_sort->set_primary_column( 'feedback' );

		return array(
			'feedback' => array(
				'url'     => $this->table_sort->get_column_url( 'feedback' ),
				'classes' => $this->table_sort->get_column_classes( 'feedback' ),
			),
			'rating' => array(
				'url'     => $this->table_sort->get_column_url( 'rating' ),
				'classes' => $this->table_sort->get_column_classes( 'rating' ),
			),
			'status' => array(
				'url'     => $this->table_sort->get_column_url( 'status' ),
				'classes' => $this->table_sort->get_column_classes( 'status' ),
			),
			'document_url' => array(
				'url'     => $this->table_sort->get_column_url( 'document_title' ),
				'classes' => $this->table_sort->get_column_classes( 'document_title' ),
			),
			'date' => array(
				'url'     => $this->table_sort->get_column_url( 'date' ),
				'classes' => $this->table_sort->get_column_classes( 'date' ),
			),
		);
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	private function get_pagination( array $args ) {
		$this->pagination->set_total_items( $this->feedback_query->get_filtered_items_count() );
		$this->pagination->set_items_per_page( $args['items_per_page'] );
		$this->pagination->set_current_page( $args['paged'] );

		return array(
			'first_page'    => $this->pagination->get_first_page_url(),
			'previous_page' => $this->pagination->get_previous_page_url(),
			'next_page'     => $this->pagination->get_next_page_url(),
			'last_page'     => $this->pagination->get_last_page_url(),
			'current_page'  => $this->pagination->get_current_page(),
			'total_pages'   => $this->pagination->get_total_pages(),
			'total_items'   => sprintf(
				_n( '%s item', '%s items', $this->pagination->get_total_items() ),
				$this->pagination->get_total_items()
			),
		);
	}

	/** @return array */
	private function get_page_filters() {
		$this->page_filter->populate_counters_and_labels();

		return array(
			'all_and_trash' => $this->page_filter->get_all_and_trash_data(),
			'statuses'      => $this->page_filter->get_statuses_data(),
			'languages'     => $this->page_filter->get_languages_data(),
		);
	}

	/** @return array */
	private function get_current_query() {
		return array(
			'filters' => $this->page_filter->get_current_filters(),
			'sorters' => $this->table_sort->get_current_sorters(),
		);
	}
}
