<?php

class WCML_REST_API_Query_Filters_Terms{

	/** @var SitePress  */
	private $sitepress;

	/**
	 * WCML_REST_API_Query_Filters_Terms constructor.
	 *
	 * @param SitePress $sitepress
	 */
	public function __construct( $sitepress ){
		$this->sitepress = $sitepress;
	}

	public function add_hooks(){
		add_action( 'woocommerce_rest_product_cat_query', array($this, 'filter_terms_query' ), 10, 2 );
		add_action( 'woocommerce_rest_product_tag_query', array($this, 'filter_terms_query' ), 10, 2 );
	}

	/**
	 * When lang=all don't filter terms by language
	 *
	 * @param array $args
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 * @throws WCML_REST_Invalid_Language_Exception
	 */
	public function filter_terms_query( $args, $request ) {

		$data = $request->get_params();

		if ( isset( $data['lang'] ) ) {

			$active_languages = $this->sitepress->get_active_languages();

			if ( 'all' === $data['lang'] ) {
				remove_filter( 'terms_clauses', array( $this->sitepress, 'terms_clauses' ), 10, 4 );
				remove_filter( 'get_term', array( $this->sitepress, 'get_term_adjust_id' ), 1, 1 );
			} elseif ( ! isset( $active_languages[ $data['lang'] ] ) ) {
				throw new WCML_REST_Invalid_Language_Exception( $data['lang'] );
			}

		}

		return $args;
	}

}
