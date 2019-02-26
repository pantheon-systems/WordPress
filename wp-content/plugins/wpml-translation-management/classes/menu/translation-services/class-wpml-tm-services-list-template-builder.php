<?php

class WPML_TM_Services_List_Template_Builder {
	/** @var IWPML_Template_Service */
	private $template_service;

	/**
	 * @var WPML_TM_Translation_Services_Admin_Section_Services_List_Model_Mapper
	 */
	private $model_mapper;

	/** @var WPML_Admin_Pagination_Factory */
	private $pagination_factory;

	/** @var WPML_TP_Service[]*/
	private $services;

	/** @var string */
	private $header;

	/** @var string  */
	private $param_prefix = '';

	/** @var bool  */
	private $show_popularity = false;

	public function __construct(
		IWPML_Template_Service $template_service,
		WPML_Admin_Pagination_Factory $pagination_factory,
		WPML_TM_Translation_Services_Admin_Section_Services_List_Model_Mapper $model_mapper
	) {
		$this->template_service = $template_service;
		$this->pagination_factory = $pagination_factory;
		$this->model_mapper = $model_mapper;
	}

	/**
	 * @param WPML_TP_Service[] $services
	 * @return self
	 */
	public function set_services( array $services ) {
		$this->services = $services;
		return $this;
	}

	/**
	 * @param string $header
	 * @return self
	 */
	public function set_header( $header ) {
		$this->header = $header;
		return $this;
	}

	/**
	 * @param string $param_prefix
	 * @return self
	 */
	public function set_param_prefix( $param_prefix ) {
		$this->param_prefix = $param_prefix;
		return $this;
	}

	/**
	 * @param bool $show_popularity
	 * @return self
	 */
	public function set_show_popularity( $show_popularity ) {
		$this->show_popularity = $show_popularity;
		return $this;
	}

	/**
	 * @throws InvalidArgumentException
	 * @return WPML_TM_Services_List_Template
	 */
	public function build() {
		if ( ! $this->services ) {
			throw new InvalidArgumentException( 'Services have to be defined' );
		}
		if ( empty( $this->header ) ) {
			throw new InvalidArgumentException( 'Header cannot be empty' );
		}

		return new WPML_TM_Services_List_Template(
			$this->template_service,
			$this->model_mapper,
			$this->services,
			$this->pagination_factory->create( count( $this->services ), $this->param_prefix . 'page' )->get_model(),
			$this->header,
			$this->show_popularity
		);
	}
}