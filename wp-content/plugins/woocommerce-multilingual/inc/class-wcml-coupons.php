<?php

class WCML_Coupons {

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var Sitepress */
	private $sitepress;

	public function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
	}

	public function add_hooks() {

		add_action( 'woocommerce_coupon_loaded', array( $this, 'wcml_coupon_loaded' ) );
		add_action( 'admin_init', array( $this, 'icl_adjust_terms_filtering' ) );

		add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'is_valid_for_product' ), 10, 4 );
	}

	public function wcml_coupon_loaded( $coupons_data ) {

		$wc_27_coupons = method_exists( 'WC_Coupon', 'get_amount' );

		$coupon_product_ids                 = $wc_27_coupons ? $coupons_data->get_product_ids() : $coupons_data->product_ids;
		$coupon_excluded_product_ids        = $wc_27_coupons ? $coupons_data->get_excluded_product_ids() : $coupons_data->exclude_product_ids;
		$coupon_product_categories          = $wc_27_coupons ? $coupons_data->get_product_categories() : $coupons_data->product_categories;
		$coupon_excluded_product_categories = $wc_27_coupons ? $coupons_data->get_excluded_product_categories() : $coupons_data->exclude_product_categories;

		$product_ids                    = array();
		$exclude_product_ids            = array();
		$product_categories_ids         = array();
		$exclude_product_categories_ids = array();

		foreach ( $coupon_product_ids as $prod_id ) {
			$post_type    = get_post_field( 'post_type', $prod_id );
			$trid         = $this->sitepress->get_element_trid( $prod_id, 'post_' . $post_type );
			$translations = $this->sitepress->get_element_translations( $trid, 'post_' . $post_type );
			foreach ( $translations as $translation ) {
				$product_ids[] = $translation->element_id;
			}
		}
		foreach ( $coupon_excluded_product_ids as $prod_id ) {
			$post_type    = get_post_field( 'post_type', $prod_id );
			$trid         = $this->sitepress->get_element_trid( $prod_id, 'post_' . $post_type );
			$translations = $this->sitepress->get_element_translations( $trid, 'post_' . $post_type );
			foreach ( $translations as $translation ) {
				$exclude_product_ids[] = $translation->element_id;
			}
		}

		foreach ( $coupon_product_categories as $cat_id ) {
			$term         = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $cat_id, 'product_cat' );
			$trid         = $this->sitepress->get_element_trid( $term->term_taxonomy_id, 'tax_product_cat' );
			$translations = $this->sitepress->get_element_translations( $trid, 'tax_product_cat' );

			foreach ( $translations as $translation ) {
				$product_categories_ids[] = $translation->term_id;
			}
		}

		foreach ( $coupon_excluded_product_categories as $cat_id ) {
			$term         = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $cat_id, 'product_cat' );
			$trid         = $this->sitepress->get_element_trid( $term->term_taxonomy_id, 'tax_product_cat' );
			$translations = $this->sitepress->get_element_translations( $trid, 'tax_product_cat' );
			foreach ( $translations as $translation ) {
				$exclude_product_categories_ids[] = $translation->term_id;
			}
		}

		if ( $wc_27_coupons ) {
			$coupons_data->set_product_ids( $product_ids );
			$coupons_data->set_excluded_product_ids( $exclude_product_ids );
			$coupons_data->set_product_categories( $product_categories_ids );
			$coupons_data->set_excluded_product_categories( $exclude_product_categories_ids );
		} else {
			$coupons_data->product_ids                = $product_ids;
			$coupons_data->exclude_product_ids        = $exclude_product_ids;
			$coupons_data->product_categories         = $product_categories_ids;
			$coupons_data->exclude_product_categories = $exclude_product_categories_ids;
		}

		return $coupons_data;
	}

	public function icl_adjust_terms_filtering() {
		if ( is_admin() && isset( $_GET['action'] ) && $_GET['action'] == 'woocommerce_json_search_products_and_variations' ) {
			global $icl_adjust_id_url_filter_off;
			$icl_adjust_id_url_filter_off = true;
		}
	}
	
	/**
	 * @param bool $valid
	 * @param WC_Product $product
	 * @param WC_Coupon $object
	 * @param array $values
	 *
	 * @return bool
	 */
	public function is_valid_for_product( $valid, $product, $object, $values ) {

		if ( version_compare( WC()->version, '3.0', '<' ) ) {
			$product_id = $product->is_type( 'variation' ) ? $product->parent->id : $product->id;
		} else {
			$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
		}

		$translated_product_id = apply_filters( 'translate_object_id', $product_id, 'product', false, $this->sitepress->get_current_language() );

		if ( $translated_product_id && $product_id !== $translated_product_id ) {

			remove_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'is_valid_for_product' ), 10, 4 );

			$valid = $object->is_valid_for_product( wc_get_product( $translated_product_id ), $values );

			add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'is_valid_for_product' ), 10, 4 );

		}

		return $valid;

	}

}