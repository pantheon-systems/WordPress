<?php

/**
 * Created by OnTheGo Systems
 */
class WCML_Store_URLs_Edit_Base_UI extends WPML_Templates_Factory {

	private $base;
	private $language;
	private $woocommerce_wpml;
	private $sitepress;


	function __construct( $base, $language, &$woocommerce_wpml, &$sitepress){
		parent::__construct();

		$this->base = $base;
		$this->language = $language;
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress = $sitepress;

	}

	public function get_model() {

		$source_language = $this->woocommerce_wpml->url_translation->get_source_slug_language( $this->base );
		$active_languages = $this->sitepress->get_active_languages();

		$model = array(
			'original_base' => $this->base,
			'language' => $this->language,
			'translated_base_value' => '',
			'source_language' => $source_language,
			'data' => $this->get_data_for_base(),
			'orig_flag_url' => $this->sitepress->get_flag_url( $source_language ),
			'orig_display_name' => $this->sitepress->get_display_language_name( $source_language, 'en' ),
			'trnsl_flag_url' => $this->sitepress->get_flag_url( $this->language ),
			'trnsl_display_name' => $active_languages[ $this->language ]['english_name'],
			'strings' => array(
				'orig' => __( 'Original', 'woocommerce-multilingual' ),
				'trnsl_to' => __( 'Translation to', 'woocommerce-multilingual' ),
				'cancel' => __( 'Cancel', 'woocommerce-multilingual' ),
				'save' => __( 'Save', 'woocommerce-multilingual' )
			)
		);

		return $model;
	}

	public function get_data_for_base(){

		$args = array();

		if( $this->base == 'shop' ){
			$original_shop_id = get_option('woocommerce_shop_page_id' );
			$translated_base = apply_filters( 'translate_object_id',$original_shop_id , 'page', false, $this->language );
			if( !is_null($translated_base)){
				$args['translated_base_value'] = urldecode(get_post($translated_base)->post_name);
			}

			$args['original_base_value'] = urldecode(get_post($original_shop_id)->post_name);
			$args['label_name'] = __('Product Shop Base', 'woocommerce-multilingual');
		}else{
			$translated_base = $this->woocommerce_wpml->url_translation->get_base_translation( $this->base, $this->language );
			$args['translated_base_value'] = $translated_base['translated_base'];
			$args['original_base_value'] = $translated_base['original_value'];
			$args['label_name'] = $translated_base['name'];
		}

		return $args;
	}

	public function init_template_base_dir() {
		$this->template_paths = array(
			WCML_PLUGIN_PATH . '/templates/store-urls/',
		);
	}

	public function get_template() {
		return 'edit-base.twig';
	}
}