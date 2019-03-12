<?php

/**
 * Created by OnTheGo Systems
 */
class WCML_Custom_Files_UI extends WPML_Templates_Factory {

	private $woocommerce_wpml;
	private $product_id;
	private $is_variation;

	/**
	 * WCML_Custom_Files_UI constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param int $product_id
	 * @param bool  $is_variation
	 */
	function __construct( &$woocommerce_wpml, $product_id, $is_variation = false ) {
		parent::__construct();

		$this->woocommerce_wpml = &$woocommerce_wpml;
		$this->product_id = $product_id;
		$this->is_variation = $is_variation;

	}

	public function get_model() {

		$model = array(
			'product_id' => $this->product_id,
			'is_variation' => $this->is_variation,
			'nonce' => wp_nonce_field('wcml_save_files_option','wcml_save_files_option_nonce'),
			'sync_custom' => get_post_meta( $this->product_id, 'wcml_sync_files', true ),
			'strings' => array(
				'use_custom' => __( 'Use custom settings for translations download files', 'woocommerce-multilingual' ),
				'use_same' => __( 'Use the same files for translations', 'woocommerce-multilingual' ),
				'separate' => __( 'Add separate download files for translations when you translate this product', 'woocommerce-multilingual' )
			)
		);

		return $model;
	}

	public function init_template_base_dir() {
		$this->template_paths = array(
			WCML_PLUGIN_PATH . '/templates/',
		);
	}

	public function get_template() {
		return 'custom-files.twig';
	}
}