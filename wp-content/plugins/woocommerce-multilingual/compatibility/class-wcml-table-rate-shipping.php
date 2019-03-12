<?php

/**
 * Class WCML_Table_Rate_Shipping
 */
class WCML_Table_Rate_Shipping {

	/**
	 * @var SitePress
	 */
	public $sitepress;

	/**
	 * @var woocommerce_wpml
	 */
	public $woocommerce_wpml;

	/**
	 * WCML_Table_Rate_Shipping constructor.
	 *
	 * @param SitePress $sitepress
	 * @param woocommerce_wpml $woocommerce_wpml
	 */
	function __construct( SitePress $sitepress, woocommerce_wpml $woocommerce_wpml ) {
		$this->sitepress = $sitepress;
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks(){

		add_action( 'init', array( $this, 'init' ), 9 );

		if ( ! is_admin() ) {
			add_filter( 'get_the_terms', array( $this, 'shipping_class_id_in_default_language' ), 10, 3 );
			add_filter( 'woocommerce_shipping_table_rate_is_available', array( $this, 'shipping_table_rate_is_available' ), 10, 3 );
		}

		if ( wcml_is_multi_currency_on() ) {
			add_filter( 'woocommerce_table_rate_query_rates_args', array( $this, 'filter_query_rates_args' ) );
			add_filter( 'woocommerce_table_rate_package_row_base_price', array(
				$this,
				'filter_product_base_price'
			), 10, 3 );
		}

	}

	/**
	 * Register shipping labels for string translation.
	 */
	public function init() {
		// Register shipping label
		if (
			isset( $_GET[ 'page' ] ) &&
			(
				$_GET[ 'page' ] === 'shipping_zones' ||
				(
					$_GET['page'] == 'wc-settings' &&
					isset( $_GET[ 'tab' ] ) &&
					$_GET['tab'] == 'shipping'
				)
			)
		) {

			$this->show_pointer_info();

			if( isset( $_POST[ 'shipping_label' ] ) &&
			    isset( $_POST[ 'woocommerce_table_rate_title' ] ) ){
				do_action( 'wpml_register_single_string', 'woocommerce', sanitize_text_field( $_POST[ 'woocommerce_table_rate_title' ] ) . '_shipping_method_title', sanitize_text_field( $_POST[ 'woocommerce_table_rate_title' ] ) );
				if( version_compare( WC()->version, '3.0.0', '<' ) ){
					$shipping_labels = array_map( 'woocommerce_clean', $_POST[ 'shipping_label' ] );
				} else{
					$shipping_labels = array_map( 'wc_clean', $_POST[ 'shipping_label' ] );
				}
				foreach ( $shipping_labels as $key => $shipping_label ) {
					$rate_key = isset( $_GET[ 'instance_id' ] ) ? 'table_rate'.$_GET[ 'instance_id' ].$_POST[ 'rate_id' ][ $key ] : $shipping_label;
					do_action( 'wpml_register_single_string', 'woocommerce', $rate_key. '_shipping_method_title', $shipping_label );
				}
			}
		}
	}

	/**
	 * @param $terms
	 * @param $post_id
	 * @param $taxonomy
	 *
	 * @return mixed
	 */
	public function shipping_class_id_in_default_language( $terms, $post_id, $taxonomy ) {
		global $icl_adjust_id_url_filter_off;

		$is_product_object = 'product' === get_post_type( $post_id ) || 'product_variation' === get_post_type( $post_id );
		if( $terms && $is_product_object && 'product_shipping_class' === $taxonomy ){

			if( is_admin() ){
				$shipp_class_language = $this->woocommerce_wpml->products->get_original_product_language( $post_id );
			}else {
				$shipp_class_language = $this->sitepress->get_default_language();
			}

			$cache_key = md5( json_encode ( $terms ) );
			$cache_key .= ":".$post_id.$shipp_class_language;

			$cache_group = 'trnsl_shipping_class';
			$cache_terms = wp_cache_get( $cache_key, $cache_group );

			if( $cache_terms ) return $cache_terms;

			foreach ( $terms as $k => $term ) {

				$shipping_class_id = apply_filters( 'translate_object_id', $term->term_id, 'product_shipping_class', false, $shipp_class_language );

				$icl_adjust_id_url_filter = $icl_adjust_id_url_filter_off;
				$icl_adjust_id_url_filter_off = true;

				$terms[ $k ] = get_term( $shipping_class_id,  'product_shipping_class' );

				$icl_adjust_id_url_filter_off = $icl_adjust_id_url_filter;
			}

			wp_cache_set ( $cache_key, $terms, $cache_group );
		}

		return $terms;
	}

	/**
	 * It's not possible to filter rate_min and rate_max so we use the original price to compare against these values
	 */
	public function filter_query_rates_args( $args ){

		if( isset( $args['price'] ) && get_option( 'woocommerce_currency') != $this->woocommerce_wpml->multi_currency->get_client_currency() ){
			$args['price'] = $this->woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $args['price'] );
		}

		return $args;
	}

	public function show_pointer_info(){

		$pointer_ui = new WCML_Pointer_UI(
			sprintf( __( 'You can translate this method title on the %sWPML String Translation page%s. Use the search on the top of that page to find the method title string.', 'woocommerce-multilingual' ), '<a href="'.admin_url('admin.php?page='.WPML_ST_FOLDER.'/menu/string-translation.php').'">', '</a>' ),
			'https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-table-rate-shipping-woocommerce-multilingual/',
			'woocommerce_table_rate_title'
		);

		$pointer_ui->show();


		$pointer_ui = new WCML_Pointer_UI(
			sprintf( __( 'You can translate the labels of your table rates on the %sWPML String Translation page%s. Use the search on the top of that page to find the labels strings.', 'woocommerce-multilingual' ), '<a href="'.admin_url('admin.php?page='.WPML_ST_FOLDER.'/menu/string-translation.php').'">', '</a>' ),
			'https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-table-rate-shipping-woocommerce-multilingual/',
			'shipping_rates .shipping_label a'
		);

		$pointer_ui->show();
	}


	public function filter_product_base_price( $row_base_price, $_product, $qty ){

		if( get_option( 'woocommerce_currency') != $this->woocommerce_wpml->multi_currency->get_client_currency() ){
			$row_base_price = apply_filters( 'wcml_product_price_by_currency', WooCommerce_Functions_Wrapper::get_product_id( $_product ), get_option( 'woocommerce_currency') ) * $qty;
		}

		return $row_base_price;
	}

	/**
	 * @param bool $available
	 * @param array $package
	 * @param WC_Shipping_Method $object
	 *
	 * @return bool
	 */
	public function shipping_table_rate_is_available( $available, $package, $object ) {

		if ( ! $available ) {
			add_filter( 'option_woocommerce_table_rate_priorities_' . $object->instance_id, array(
				$this,
				'filter_table_rate_priorities'
			) );
			remove_filter( 'woocommerce_shipping_table_rate_is_available', array(
				$this,
				'shipping_table_rate_is_available'
			), 10, 3 );

			$available = $object->is_available( $package );

			add_filter( 'woocommerce_shipping_table_rate_is_available', array(
				$this,
				'shipping_table_rate_is_available'
			), 10, 3 );
		}

		return $available;
	}

	/**
	 * @param array $priorities
	 *
	 * @return array
	 */
	public function filter_table_rate_priorities( $priorities ) {

		foreach ( $priorities as $slug => $priority ) {

			$shipping_class_term = get_term_by( 'slug', $slug, 'product_shipping_class' );
			if ( $shipping_class_term->slug !== $slug ) {
				unset( $priorities[ $slug ] );
				$priorities[ $shipping_class_term->slug ] = $priority;
			}
		}

		return $priorities;
	}

}