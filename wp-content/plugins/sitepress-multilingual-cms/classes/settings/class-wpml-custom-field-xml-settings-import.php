<?php

class WPML_Custom_Field_XML_Settings_Import {

	/** @var  WPML_Custom_Field_Setting_Factory $setting_factory */
	private $setting_factory;
	/** @var  array $settings_array */
	private $settings_array;

	/**
	 * WPML_Custom_Field_XML_Settings_Import constructor.
	 *
	 * @param WPML_Custom_Field_Setting_Factory $setting_factory
	 * @param array                             $settings_array
	 */
	public function __construct( $setting_factory, $settings_array ) {
		$this->setting_factory = $setting_factory;
		$this->settings_array  = $settings_array;
	}

	/**
	 * Runs the actual import of the xml
	 */
	public function run() {
		$config = $this->settings_array;
		foreach (
			array(
				'post_meta_setting' => array(
					WPML_POST_META_CONFIG_INDEX_PLURAL,
					WPML_POST_META_CONFIG_INDEX_SINGULAR
				),
				'term_meta_setting' => array(
					WPML_TERM_META_CONFIG_INDEX_PLURAL,
					WPML_TERM_META_CONFIG_INDEX_SINGULAR
				)
			) as $setting_constructor => $settings
		) {
			if ( ! empty( $config[ $settings[0] ] ) ) {
				$field = $config[ $settings[0] ][ $settings[1] ];
				$cf    = ! is_numeric( key( current( $config[ $settings[0] ] ) ) ) ? array( $field ) : $field;
				foreach ( $cf as $c ) {
					$setting = call_user_func_array( array(
						$this->setting_factory,
						$setting_constructor
					), array( trim( $c['value'] ) ) );
					$this->import_action( $c, $setting );
					$setting->make_read_only();
					$this->import_editor_settings( $c, $setting );
					if ( isset( $c[ 'attr' ][ 'translate_link_target' ] ) || isset( $c[ 'custom-field' ] ) ) {
						$setting->set_translate_link_target( isset( $c[ 'attr' ][ 'translate_link_target' ] ) ? (bool) $c[ 'attr' ][ 'translate_link_target' ] : false, isset( $c[ 'custom-field' ] ) ? $c[ 'custom-field' ] : array() );
					}
					if ( isset( $c[ 'attr' ][ 'convert_to_sticky' ] ) ) {
						$setting->set_convert_to_sticky( (bool) $c[ 'attr' ][ 'convert_to_sticky' ] );
					}
					if ( isset( $c[ 'attr' ][ 'encoding' ] ) ) {
						$setting->set_encoding( $c[ 'attr' ][ 'encoding' ] );
					}
				}
			}
		}

		$this->import_custom_field_texts();
	}

	private function import_action( $c, $setting ) {
		if ( ! $setting->is_unlocked() ) {
			switch ( $c['attr']['action'] ) {
				case 'translate':
					$setting->set_to_translatable();
					break;

				case 'copy':
					$setting->set_to_copy();
					break;

				case 'copy-once':
					$setting->set_to_copy_once();
					break;

				default:
					$setting->set_to_nothing();
					break;
			}
		}
	}
	
	private function import_editor_settings( $c, $setting ) {
		if ( isset( $c[ 'attr' ][ 'style' ] ) ) {
			$setting->set_editor_style( $c[ 'attr' ][ 'style' ] );
		}
		if ( isset( $c[ 'attr' ][ 'label' ] ) ) {
			$setting->set_editor_label( $c[ 'attr' ][ 'label' ] );
		}					
		if ( isset( $c[ 'attr' ][ 'group' ] ) ) {
			$setting->set_editor_group( $c[ 'attr' ][ 'group' ] );
		}					
	}

	private function import_custom_field_texts() {
		$config = $this->settings_array;

		if ( isset( $config['custom-fields-texts']['key'] ) ) {
			foreach( $config['custom-fields-texts']['key'] as $field ) {
				$setting = $this->setting_factory->post_meta_setting( $field['attr']['name'] );
				$setting->set_attributes_whitelist( $this->get_custom_field_texts_keys( $field['key'] ) );
			}
		}
	}

	private function get_custom_field_texts_keys( $data ) {
		if ( isset( $data['attr'] ) ) { // single
			$data = array( $data );
		}

		$sub_fields = array();

		foreach( $data as $key ) {
			$sub_fields[ $key['attr']['name'] ] = isset( $key['key'] ) ? $this->get_custom_field_texts_keys( $key['key'] ) : array();
		}
		return $sub_fields;
	}
}