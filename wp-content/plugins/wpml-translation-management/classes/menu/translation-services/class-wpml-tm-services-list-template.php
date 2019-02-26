<?php

class WPML_TM_Services_List_Template {

	const SERVICES_LIST_TEMPLATE = 'services-list.twig';

	/**
	 * @var IWPML_Template_Service
	 */
	private $template_service;

	/**
	 * @var WPML_TM_Translation_Services_Admin_Section_Services_List_Model_Mapper
	 */
	private $model_mapper;

	/**
	 * @var WPML_TP_Service[]
	 */
	private $services;

	/**
	 * @var array
	 */
	private $pagination;

	/**
	 * @var string
	 */
	private $header;

	/** @var bool */
	private $show_popularity;

	/**
	 * WPML_TM_Translation_Services_Admin_Section_Services_List_Template constructor.
	 *
	 * @param IWPML_Template_Service $template_service
	 * @param WPML_TM_Translation_Services_Admin_Section_Services_List_Model_Mapper $model_mapper
	 * @param WPML_TP_Service[] $services
	 * @param array $pagination
	 * @param WPML_Admin_Table_Sort $table_sort
	 * @param string $header
	 * @param bool $show_popularity
	 */
	public function __construct(
		IWPML_Template_Service $template_service,
		WPML_TM_Translation_Services_Admin_Section_Services_List_Model_Mapper $model_mapper,
		array $services,
		array $pagination,
		$header,
		$show_popularity
	) {
		$this->template_service = $template_service;
		$this->model_mapper = $model_mapper;
		$this->services = $services;
		$this->pagination = $pagination;
		$this->header = $header;
		$this->show_popularity = $show_popularity;
	}


	public function render() {
		return $this->template_service->show( $this->get_services_list_model(), self::SERVICES_LIST_TEMPLATE );
	}

	/**
	 * @return array
	 */
	private function get_services_list_model() {
		$pagination_strings = $this->pagination['strings'];
		$pagination_strings['total_items_text'] = $this->pagination['total_items_text'];

		$model = array(
			'services' => array_map( array( $this->model_mapper, 'map' ), $this->services ),
			'header'           => $this->header,
			'show_popularity'  => $this->show_popularity,
			'strings'          => array(
				'activate'         => __( 'Activate', 'wpml-translation-management' ),
				'documentation'    => __( 'Documentation', 'wpml-translation-management' ),
				'filter'           => array(
					'search'       => __( 'Search', 'wpml-translation-management' ),
					'clean_search' => __( 'Clear search', 'wpml-translation-management' ),
				),
				'columns'          => array(
					'name'        => __( 'Name', 'wpml-translation-management' ),
					'description' => __( 'Description', 'wpml-translation-management' ),
					'popularity'  => __( 'Popularity', 'wpml-translation-management' ),
				),
				'pagination' => $pagination_strings,
			)
		);

		return $model;
	}
}