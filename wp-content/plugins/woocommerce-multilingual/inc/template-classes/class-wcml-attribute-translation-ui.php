<?php


class WCML_Attribute_Translation_UI extends WPML_Templates_Factory {

	private $woocommerce_wpml;
	private $sitepress;

	public function __construct( &$woocommerce_wpml, &$sitepress ){
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress = $sitepress;
	}

	public function get_model() {

		$product_attributes = $this->woocommerce_wpml->attributes->get_translatable_attributes();
		$taxonomy = isset( $_GET['taxonomy'] ) ? $_GET['taxonomy'] : false;
		$selected_attribute = false;
		$translation_ui = '';

		if( $product_attributes  && !empty( $taxonomy ) ) {
			foreach ($product_attributes as $attribute) {
				if ($attribute->attribute_name == $taxonomy) {
					$selected_attribute = $attribute;
					break;
				}
			}
		}

		if( $selected_attribute ){
			$selected_attribute_name = $selected_attribute ? 'pa_' . $selected_attribute->attribute_name : '';
			$WPML_Translate_Taxonomy =
				new WPML_Taxonomy_Translation(
					$selected_attribute_name,
					array(
						'taxonomy_selector'=> false
					)
				);

		}elseif( $product_attributes ){
			$empty_value = new stdClass();
			$empty_value->attribute_name = '';
			$empty_value->attribute_label = __( '--Attribute--', 'woocommerce-multilingual' );
			array_unshift( $product_attributes, $empty_value );

			$WPML_Translate_Taxonomy =
				new WPML_Taxonomy_Translation(
					'',
					array(
						'taxonomy_selector' => false
					),
					new WPML_UI_Screen_Options_Factory( $this->sitepress )
				);
		}

		if( isset( $WPML_Translate_Taxonomy ) ){
			ob_start();
			$WPML_Translate_Taxonomy->render();
			$translation_ui = ob_get_contents();
			ob_end_clean();
		}

		$model = array(
			'attributes' => $product_attributes,
			'selected_attribute' => $selected_attribute,
			'strings' => array(
				'no_attributes' => __( 'There are no translatable product attributes defined', 'woocommerce-multilingual' ),
				'select_label'	=> __('Select the attribute to translate: ', 'woocommerce-multilingual'),
				'loading'       => __( 'Loading ...', 'woocommerce-multilingual' )
			),
			'translation_ui' => $translation_ui

		);

		return $model;
	}

	public function init_template_base_dir() {
		$this->template_paths = array(
			WCML_PLUGIN_PATH . '/templates/',
		);
	}

	public function get_template() {
		return 'attribute-translation.twig';
	}
}