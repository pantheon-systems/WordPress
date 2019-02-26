<?php

/**
 * Class WPML_TM_MCS_Search_Render
 */
class WPML_TM_MCS_Search_Render {

	/**
	 * Twig template path.
	 */
	const TM_MCS_SEARCH_TEMPLATE = 'tm-mcs-search.twig';

	/**
	 * @var IWPML_Template_Service
	 */
	private $template;

	/**
	 * @var string Search string
	 */
	private $search_string;

	/**
	 * WPML_TM_MCS_Search_Render constructor.
	 *
	 * @param IWPML_Template_Service $template Twig template service.
	 * @param string $search_string Search string.
	 */
	public function __construct( IWPML_Template_Service $template, $search_string ) {
		$this->template      = $template;
		$this->search_string = $search_string;
	}

	/**
	 * Get twig model.
	 *
	 * @return array
	 */
	public function get_model() {
		$model = array(
			'strings'       => array(
				'search_for' => __( 'Search for', 'wpml-translation-management' ),
			),
			'search_string' => $this->search_string,
		);

		return $model;
	}

	/**
	 * Render model via twig.
	 *
	 * @return mixed
	 */
	public function render() {
		return $this->template->show( $this->get_model(), self::TM_MCS_SEARCH_TEMPLATE );
	}
}
