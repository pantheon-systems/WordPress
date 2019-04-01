<?php

class WCML_REST_API_Query_Filters_Products{

	/** @var WPML_Query_Filter */
	private $wpml_query_filter;

	/**
	 * WCML_REST_API_Query_Filters_Products constructor.
	 * @param WPML_Query_Filter $wpml_query_filter
	 */
	public function __construct( WPML_Query_Filter $wpml_query_filter ) {
		$this->wpml_query_filter = $wpml_query_filter;
	}

	public function add_hooks(){
		add_filter( 'woocommerce_rest_product_query', array( $this, 'filter_products_query' ), 10, 2 );
	}

	/**
	 * When lang=all don't filter products by language
	 *
	 * @param array $args
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 */
	public function filter_products_query( $args, $request ){
		$data = $request->get_params();
		if( isset( $data['lang'] ) && $data['lang'] === 'all' ){
			remove_filter( 'posts_join', array( $this->wpml_query_filter, 'posts_join_filter' ), 10 );
			remove_filter( 'posts_where', array( $this->wpml_query_filter, 'posts_where_filter' ), 10 );
		}
		return $args;
	}


}
