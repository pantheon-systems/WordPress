<?php

class WCML_Page_Builders {

	/**
	 * @var SitePress
	 */
	private $sitepress;

	function __construct( &$sitepress ) {
		$this->sitepress = $sitepress;

	}

	public function get_page_builders_string_packages( $product_id ) {
		return apply_filters( 'wpml_st_get_post_string_packages', false, $product_id );
	}

	public function get_page_builders_strings( $product_id, $target_language ) {

		$string_packages     = $this->get_page_builders_string_packages( $product_id );
		$translation_package = array();

		if ( $string_packages ) {

			foreach ( $string_packages as $package_id => $string_package ) {

				$translation_package[ $package_id ] = array(
					'title' => $string_package->title
				);

				$strings = $string_package->get_package_strings();

				$translated_strings = $string_package->get_translated_strings( array() );

				foreach ( $strings as $string ) {

					if ( isset( $translated_strings[ $string->name ][ $target_language ] ) ) {
						$string->translated_value = $translated_strings[ $string->name ][ $target_language ]['value'];
					}

					$translation_package[ $package_id ]['strings'][] = $string;
				}
			}
		}

		return $translation_package;

	}

	public function get_page_builders_strings_section( $data, $product_id, $target_language ) {

		$string_packages = $this->get_page_builders_strings( $product_id, $target_language );
		$strings_section = false;

		foreach ( $string_packages as $string_package ) {
			$strings_section = new WPML_Editor_UI_Field_Section( $string_package['title'] );

			foreach ( $string_package['strings'] as $string ) {
				$field_label = apply_filters( 'wpml_string_title_from_id', false, $string->id );
				$strings_section->add_field( new WCML_Editor_UI_WYSIWYG_Field( $string->name, $field_label, $data, true ) );
			}
		}

		return $strings_section;
	}

	public function page_builders_data( $element_data, $product_id, $target_language ) {

		$string_packages = $this->get_page_builders_strings( $product_id, $target_language );

		foreach ( $string_packages as $string_package ) {

			foreach ( $string_package['strings'] as $string ) {
				$element_data[ $string->name ] = array( 'original' => $string->value );
				if ( isset( $string->translated_value ) ) {
					$element_data[ $string->name ]['translation'] = $string->translated_value;
				}
			}
		}

		return $element_data;
	}

	public function save_page_builders_strings( $translations, $product_id, $target_language ) {

		$string_packages = $this->get_page_builders_strings( $product_id, $target_language );

		foreach ( $string_packages as $string_package ) {

			foreach ( $string_package['strings'] as $string ) {

				do_action(
					'wpml_add_string_translation',
					$string->id,
					$target_language,
					$translations[ md5( $string->name ) ],
					$this->sitepress->get_wp_api()->constant( 'ICL_TM_COMPLETE' )
				);

			}
		}
	}
}