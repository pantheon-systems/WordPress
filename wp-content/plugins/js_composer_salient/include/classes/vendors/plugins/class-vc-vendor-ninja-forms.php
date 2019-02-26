<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Ninja Forms vendor
 * @since 4.4
 */
class Vc_Vendor_NinjaForms implements Vc_Vendor_Interface {
	private static $ninjaCount;

	/**
	 * Implement interface, map ninja forms shortcode
	 * @since 4.4
	 */
	public function load() {
		vc_lean_map( 'ninja_form', array(
			$this,
			'addShortcodeSettings',
		) );

		add_filter( 'vc_frontend_editor_load_shortcode_ajax_output', array(
			$this,
			'replaceIds',
		) );
	}

	/**
	 * Mapping settings for lean method.
	 *
	 * @since 4.9
	 *
	 * @param $tag
	 *
	 * @return array
	 */
	public function addShortcodeSettings( $tag ) {

		$ninja_forms = $this->get_forms();

		return array(
			'base' => $tag,
			'name' => __( 'Ninja Forms', 'js_composer' ),
			'icon' => 'icon-wpb-ninjaforms',
			'category' => __( 'Content', 'js_composer' ),
			'description' => __( 'Place Ninja Form', 'js_composer' ),
			'params' => array(
				array(
					'type' => 'dropdown',
					'heading' => __( 'Select ninja form', 'js_composer' ),
					'param_name' => 'id',
					'value' => $ninja_forms,
					'save_always' => true,
					'description' => __( 'Choose previously created ninja form from the drop down list.', 'js_composer' ),
				),
			),
		);
	}

	private function get_forms() {
		$ninja_forms = array();
		if ( $this->is_ninja_forms_three() ) {

			$ninja_forms_data = ninja_forms_get_all_forms();

			if ( ! empty( $ninja_forms_data ) ) {
				// Fill array with Name=>Value(ID)
				foreach ( $ninja_forms_data as $key => $value ) {
					if ( is_array( $value ) ) {
						$ninja_forms[ $value['name'] ] = $value['id'];
					}
				}
			}
		} else {

			$ninja_forms_data = Ninja_Forms()->form()->get_forms();

			if ( ! empty( $ninja_forms_data ) ) {
				// Fill array with Name=>Value(ID)
				foreach ( $ninja_forms_data as $form ) {
					$ninja_forms[ $form->get_setting( 'title' ) ] = $form->get_id();
				}
			}
		}

		return $ninja_forms;
	}

	private function is_ninja_forms_three() {
		return ( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3.0', '<' ) || get_option( 'ninja_forms_load_deprecated', false ) );
	}

	public function replaceIds( $output ) {
		if ( is_null( self::$ninjaCount ) ) {
			self::$ninjaCount = 1;
		} else {
			self::$ninjaCount ++;
		}
		$patterns = array(
			'(nf-form-)(\d+)(-cont)',
			'(nf-form-title-)(\d+)()',
			'(nf-form-errors-)(\d+)()',
			'(form.id\s*=\s*\')(\d+)(\')',
		);
		$time = time() . self::$ninjaCount . rand( 100, 999 );
		foreach ( $patterns as $pattern ) {
			$output = preg_replace( '/' . $pattern . '/', '${1}' . $time . '${3}', $output );
		}
		$replaceTo = <<<JS
if (typeof nfForms !== 'undefined') {
  nfForms = nfForms.filter( function(item, index) {
    if (item && item.id) {
      return document.querySelector('#nf-form-' + item.id + '-cont')
    }
  })
}
JS;
		$response = str_replace( 'var nfForms', $replaceTo . ';var nfForms', $output );

		return $response;
	}
}
