<?php

/**
 * Created by OnTheGo Systems
 */
class WCML_Troubleshooting_UI extends WPML_Templates_Factory {

	private $woocommerce_wpml;


	function __construct( &$woocommerce_wpml ){
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
	}


	public function get_model() {

		if( get_option( 'wcml_products_to_sync' ) === false ){
			$this->woocommerce_wpml->troubleshooting->wcml_sync_variations_update_option();
		}
		$translated_product_type_terms = WCML_Install::translated_product_type_terms();

		$model = array(
			'prod_with_variations' => $this->woocommerce_wpml->troubleshooting->wcml_count_products_with_variations(),
			'prod_count' => $this->woocommerce_wpml->troubleshooting->wcml_count_products_for_gallery_sync(),
			'prod_categories_count' => $this->woocommerce_wpml->troubleshooting->wcml_count_product_categories(),
			'sync_stock_count' => $this->woocommerce_wpml->troubleshooting->wcml_count_product_stock_sync(),
			'all_products_taxonomies' => $this->get_all_products_taxonomies(),
			'product_type_sync_needed' => !empty( $translated_product_type_terms ) ? true : false,
			'media_def' => defined('WPML_MEDIA_VERSION'),
			'strings' => array(
				'troubl' => __( 'Troubleshooting', 'woocommerce-multilingual' ),
				'backup' => __( 'Please make a backup of your database before you start the synchronization', 'woocommerce-multilingual' ),
				'sync' => __( 'Sync variables products', 'woocommerce-multilingual' ),
				'upd_prod_count' => __( 'Update products count:', 'woocommerce-multilingual' ),
				'prod_var' => __( 'products with variations', 'woocommerce-multilingual' ),
				'sync_var' => __( 'Sync products variations:', 'woocommerce-multilingual' ),
				'left' => __( 'left', 'woocommerce-multilingual' ),
				'sync_gallery' => __( 'Sync products "gallery images":', 'woocommerce-multilingual' ),
				'sync_cat' => __( 'Sync products categories (display type, thumbnail):', 'woocommerce-multilingual' ),
				'dup_terms' => __( 'Duplicate terms ( please select attribute ):', 'woocommerce-multilingual' ),
				'none' => __( 'none', 'woocommerce-multilingual' ),
				'start' => __( 'Start', 'woocommerce-multilingual' ),
				'delete_terms' => __( 'Fix product_type taxonomy terms', 'woocommerce-multilingual' ),
				'sync_stock' => __( 'Sync product stock quantity and status ( synchronizing min stock between translations )', 'woocommerce-multilingual' ),
				'product_type_fix_done' => __( 'Done!', 'woocommerce-multilingual' )
			),
			'nonces' => array(
				'trbl_update_count' => wp_nonce_field('trbl_update_count', 'trbl_update_count_nonce'),
				'trbl_sync_variations' => wp_nonce_field('trbl_sync_variations', 'trbl_sync_variations_nonce'),
				'trbl_gallery_images' => wp_nonce_field('trbl_gallery_images', 'trbl_gallery_images_nonce'),
				'trbl_sync_categories' => wp_nonce_field('trbl_sync_categories', 'trbl_sync_categories_nonce'),
				'trbl_duplicate_terms' => wp_nonce_field('trbl_duplicate_terms', 'trbl_duplicate_terms_nonce'),
				'trbl_product_type_terms' => wp_nonce_field('trbl_product_type_terms', 'trbl_product_type_terms_nonce'),
				'trbl_sync_stock' => wp_nonce_field('trbl_sync_stock', 'trbl_sync_stock_nonce'),
			)
		);

		return $model;
	}

	public function get_all_products_taxonomies(){

		$all_products_taxonomies = get_taxonomies( array( 'object_type' =>array( 'product' ) ), 'objects' );
		unset(
			$all_products_taxonomies[ 'product_type' ],
			$all_products_taxonomies[ 'product_cat' ],
			$all_products_taxonomies[ 'product_tag' ]
		);

		foreach( $all_products_taxonomies as $key => $taxonomy ){
			$all_products_taxonomies[ $key ]->terms_count = wp_count_terms( $key );
			$all_products_taxonomies[ $key ]->tax_key = $key;
		}

		return $all_products_taxonomies;

	}

	public function init_template_base_dir() {
		$this->template_paths = array(
			WCML_PLUGIN_PATH . '/templates/',
		);
	}

	public function get_template() {
		return 'troubleshooting.twig';
	}
}