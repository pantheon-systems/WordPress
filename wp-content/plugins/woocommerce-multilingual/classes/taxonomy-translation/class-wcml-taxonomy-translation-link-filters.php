<?php

class WCML_Taxonomy_Translation_Link_Filters{

	/**
	 * @var WCML_Attributes
	 */
	private $wcml_attributes;

	public function __construct( WCML_Attributes $wcml_attributes ) {
		$this->wcml_attributes = $wcml_attributes;
	}

	public function add_filters(){
		add_filter( 'wpml_notice_text', array( $this, 'override_translation_notice_text' ), 10, 2 );
		add_filter( 'wpml_taxonomy_slug_translation_ui', array( $this, 'slug_translation_ui_class' ), 10, 2 );
	}

	/**
	 * @param string text
	 * @param array $notice
	 *
	 * @return string
	 */
	public function override_translation_notice_text( $text, $notice ) {
		if( 'taxonomy-term-help-notices' === $notice['group'] ) {
			$taxonomy = get_taxonomy( $notice['id'] );
			if ( false !== $taxonomy ) {

			$link = sprintf(
				'<a href="%s">%s</a>',
				$this->get_screen_url( $taxonomy->name ),
				sprintf( esc_html__( '%s translation', 'woocommerce-multilingual' ), $taxonomy->labels->singular_name )
			);

				$text = sprintf(
					esc_html__( 'Translating %s? Use the %s table for easier translation.', 'woocommerce-multilingual' ),
					$taxonomy->labels->name,
					$link
				);
			}
		}

		return $text;
	}

	/**
	 * @param string $taxonomy
	 *
	 * @return string
	 */
	public function get_screen_url( $taxonomy = '' ) {
		$url = false;

		$base_url = admin_url( 'admin.php' );
		$args     = array( 'page' => 'wpml-wcml' );

		$built_in_taxonomies = array( 'product_cat', 'product_tag', 'product_shipping_class' );
		if ( in_array( $taxonomy, $built_in_taxonomies, true ) ) {
			$args['tab'] = $taxonomy;
		} else {

			$translatable_attributes = $this->get_translatable_attributes();

			if ( in_array( $taxonomy, $translatable_attributes, true ) ) {
				$args['tab']      = 'product-attributes';
				$args['taxonomy'] = $taxonomy;
			} else {
				$custom_taxonomies = get_object_taxonomies( 'product', 'objects' );

				$translatable_taxonomies = array();
				foreach ( $custom_taxonomies as $product_taxonomy_name => $product_taxonomy_object ) {
					if ( is_taxonomy_translated( $product_taxonomy_name ) ) {
						$translatable_taxonomies[] = $product_taxonomy_name;
					}
				}

				if ( in_array( $taxonomy, $translatable_taxonomies, true ) ) {
					$args['tab']      = 'custom-taxonomies';
					$args['taxonomy'] = $taxonomy;
				}
			}
		}

		if ( count( $args ) > 1 ) {
			$url = add_query_arg( $args, $base_url );
		}

		return $url;
	}


	private function get_translatable_attributes(){

		$translatable_attributes = array();
		foreach ( $this->wcml_attributes->get_translatable_attributes() as $attribute ) {
			$translatable_attributes[] = 'pa_' . $attribute->attribute_name;
		}

		return $translatable_attributes;
	}

	public function slug_translation_ui_class( $ui_class, $taxonomy ){

		if( in_array( $taxonomy, $this->get_translatable_attributes() ) ){

			$ui_class = new WCML_St_Taxonomy_UI( get_taxonomy( $taxonomy ) );
		}

		return $ui_class;
	}
}
