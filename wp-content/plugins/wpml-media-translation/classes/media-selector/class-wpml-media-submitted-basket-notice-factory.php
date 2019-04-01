<?php

class WPML_Media_Submitted_Basket_Notice_Factory implements IWPML_Backend_Action_Loader {

	public function create() {
		global $sitepress;
		if ( $sitepress->get_wp_api()->is_tm_page( 'basket' ) && $this->basket_has_media() ) {
			$template_loader = new WPML_Twig_Template_Loader( array( WPML_MEDIA_PATH . '/templates/media-selector/' ) );

			return new WPML_Media_Submitted_Basket_Notice( $template_loader );
		}

		return null;

	}

	private function basket_has_media() {
		$basket     = TranslationProxy_Basket::get_basket( true );
		$item_types = TranslationProxy_Basket::get_basket_items_types();

		foreach ( $item_types as $item_type => $type_type ) {
			if ( isset( $basket[ $item_type ] ) ) {
				foreach ( $basket[ $item_type ] as $item ) {
					if ( ! empty( $item['media-translation'] ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

}