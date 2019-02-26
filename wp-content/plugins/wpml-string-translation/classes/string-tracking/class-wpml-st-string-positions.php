<?php

abstract class WPML_ST_String_Positions {

	const TEMPLATE_PATH = '/templates/string-tracking/';

	/**
	 * @var WPML_ST_DB_Mappers_String_Positions $string_position_mapper
	 */
	protected $string_position_mapper;

	/**
	 * @var IWPML_Template_Service $template_service
	 */
	protected $template_service;

	public function __construct(
		WPML_ST_DB_Mappers_String_Positions $string_position_mapper,
		IWPML_Template_Service $template_service
	) {
		$this->string_position_mapper = $string_position_mapper;
		$this->template_service       = $template_service;
	}

	/**
	 * @param int $string_id
	 *
	 * @return array
	 */
	abstract protected function get_model( $string_id );

	/** @return string */
	abstract protected function get_template_name();

	/**
	 * @param int $string_id
	 */
	public function dialog_render( $string_id ) {
		echo $this->get_template_service()->show( $this->get_model( $string_id ), $this->get_template_name() );
	}

	/**
	 * @return WPML_ST_DB_Mappers_String_Positions
	 */
	protected function get_mapper() {
		return $this->string_position_mapper;
	}

	/**
	 * @return IWPML_Template_Service
	 */
	protected function get_template_service() {
		return $this->template_service;
	}
}