<?php

class WPML_ST_WCML_Taxonomy_Labels_Translation implements IWPML_Action {

	public function add_hooks() {
		add_filter(
			'wpml_label_translation_data',
			array( $this, 'alter_slug_translation_display' ),
			WPML_ST_Taxonomy_Labels_Translation::PRIORITY_GET_LABEL + 1,
			2
		);
	}

	/**
	 * @param array  $data
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	public function alter_slug_translation_display( $data, $taxonomy ) {
		if ( ! empty( $data['st_default_lang'] ) ) {
			$source_lang = $data['st_default_lang'];

			if ( $this->is_product_attribute( $taxonomy ) || $this->is_shipping_class( $taxonomy ) ) {
				$data[ $source_lang ]['showSlugTranslationField'] = false;
			}
		}

		return $data;
	}

	/**
	 * @param string $taxonomy
	 *
	 * @return bool
	 */
	private function is_product_attribute( $taxonomy ) {
		return 0 === strpos( $taxonomy, 'pa_' );
	}

	/**
	 * @param string $taxonomy
	 *
	 * @return bool
	 */
	private function is_shipping_class( $taxonomy ) {
		return 'product_shipping_class' === $taxonomy;
	}
}
