<?php

class WCML_Setup_Handlers {

	/** @var  woocommerce_wpml */
	private $woocommerce_wpml;

	public function __construct( woocommerce_wpml $woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function save_attributes( array $data ) {

		if ( isset( $data['attributes'] ) ) {
			$this->woocommerce_wpml->attributes->set_translatable_attributes( $data['attributes'] );
		}

	}

	public function save_multi_currency( array $data ) {

		$this->woocommerce_wpml->get_multi_currency();

		if ( ! empty( $data['enabled'] ) ) {
			$this->woocommerce_wpml->multi_currency->enable();
		} else {
			$this->woocommerce_wpml->multi_currency->disable();
		}

	}

	public function install_store_pages( array $data ) {

		if ( ! empty( $data['install_missing_pages'] ) ) {
			WC_Install::create_pages();
		}

		if ( ! empty( $data['install_missing_pages'] ) || ! empty( $data['create_pages'] ) ) {
			$this->woocommerce_wpml->store->create_missing_store_pages_with_redirect();
		}

	}

	public function save_translation_options( $data ){

		if ( isset( $data['translation-option'] ) ) {

			$settings_helper = wpml_load_settings_helper();

			if( 1 === (int) $data['translation-option'] ){
				$settings_helper->set_post_type_display_as_translated( 'product' );
				$settings_helper->set_post_type_translation_unlocked_option( 'product' );
				$settings_helper->set_taxonomy_display_as_translated('product_cat' );
				$settings_helper->set_taxonomy_translation_unlocked_option( 'product_cat' );
			}else{
				$settings_helper->set_post_type_translatable( 'product' );
				$settings_helper->set_post_type_translation_unlocked_option( 'product', false );
				$settings_helper->set_taxonomy_translatable('product_cat' );
				$settings_helper->set_taxonomy_translation_unlocked_option( 'product_cat', false );
			}
		}
	}


}