<?php

class WPML_PB_Config_Import_Shortcode {

	const PB_SHORTCODE_SETTING       = 'pb_shortcode';
	const PB_MEDIA_SHORTCODE_SETTING = 'wpml_pb_media_shortcode';

	/** @var  WPML_ST_Settings $st_settings */
	private $st_settings;

	public function __construct( WPML_ST_Settings $st_settings ) {
		$this->st_settings = $st_settings;
	}

	public function add_hooks() {
		add_filter( 'wpml_config_array', array( $this, 'wpml_config_filter' ) );
	}

	public function wpml_config_filter( $config_data ) {
		$this->update_shortcodes_config( $config_data );
		$this->update_media_shortcodes_config( $config_data );

		return $config_data;
	}

	/** @param array $config_data */
	private function update_shortcodes_config( $config_data ) {
		$old_shortcode_data = $this->get_settings();

		$shortcode_data = array();
		if ( isset ( $config_data['wpml-config']['shortcodes']['shortcode'] ) ) {
			foreach ( $config_data['wpml-config']['shortcodes']['shortcode'] as $data ) {
				$ignore_content = isset( $data['tag']['attr']['ignore-content'] )
				                  && $data['tag']['attr']['ignore-content'];

				$attributes = array();
				if ( isset( $data['attributes']['attribute'] ) ) {

					$data['attributes']['attribute'] = $this->convert_single_attribute_to_multiple_format( $data['attributes']['attribute'] );

					foreach ( $data['attributes']['attribute'] as $attribute ) {

						if ( $this->is_media_attribute( $attribute ) ) {
							continue;
						}

						if ( ! empty( $attribute['value'] ) ) {
							$attribute_encoding = isset( $attribute['attr']['encoding'] ) ? $attribute['attr']['encoding'] : '';
							$attribute_type     = isset( $attribute['attr']['type'] ) ? $attribute['attr']['type'] : '';
							$attributes[]       = array(
								'value'    => $attribute['value'],
								'encoding' => $attribute_encoding,
								'type'     => $attribute_type,
							);
						}
					}
				}

				if ( ! ( $ignore_content && empty( $attributes ) ) ) {
					$shortcode_data[] = array(
						'tag'        => array(
							'value'              => $data['tag']['value'],
							'encoding'           => isset( $data['tag']['attr']['encoding'] ) ? $data['tag']['attr']['encoding'] : '',
							'encoding-condition' => isset( $data['tag']['attr']['encoding-condition'] ) ? $data['tag']['attr']['encoding-condition'] : '',
							'type'               => isset( $data['tag']['attr']['type'] ) ? $data['tag']['attr']['type'] : '',
							'raw-html'           => isset( $data['tag']['attr']['raw-html'] ) ? $data['tag']['attr']['raw-html'] : '',
							'ignore-content'     => $ignore_content,
						),
						'attributes' => $attributes,
					);
				}
			}
		}

		if ( $shortcode_data != $old_shortcode_data ) {
			$this->st_settings->update_setting( self::PB_SHORTCODE_SETTING, $shortcode_data, true );
		}
	}

	/** @param array $config_data */
	private function update_media_shortcodes_config( $config_data ) {
		$old_shortcodes_data = $this->get_media_settings();
		$shortcodes_data     = array();

		if ( isset ( $config_data['wpml-config']['shortcodes']['shortcode'] ) ) {

			foreach ( $config_data['wpml-config']['shortcodes']['shortcode'] as $data ) {
				$shortcode_data = array();

				if ( isset( $data['attributes']['attribute'] ) ) {
					$attributes = array();

					$data['attributes']['attribute'] = $this->convert_single_attribute_to_multiple_format( $data['attributes']['attribute'] );

					foreach ( $data['attributes']['attribute'] as $attribute ) {

						if ( ! $this->is_media_attribute( $attribute ) ) {
							continue;
						}

						if ( ! empty( $attribute['value'] ) ) {
							$attribute_type = isset( $attribute['attr']['type'] ) ? $attribute['attr']['type'] : '';
							$attributes[ $attribute['value'] ] = array( 'type' => $attribute_type );
						}
					}

					$shortcode_data['attributes'] = $attributes;
				}

				if ( isset( $data['tag']['attr']['type'] )
				     && $data['tag']['attr']['type'] === WPML_Page_Builders_Media_Shortcodes::TYPE_URL
				) {
					$shortcode_data['content'] = array( 'type' => WPML_Page_Builders_Media_Shortcodes::TYPE_URL );
				}

				if ( $shortcode_data ) {
					$shortcode_data['tag'] = array( 'name' => $data['tag']['value'] );
					$shortcodes_data[]     = $shortcode_data;
				}
			}
		}

		if ( $shortcodes_data != $old_shortcodes_data ) {
			update_option( self::PB_MEDIA_SHORTCODE_SETTING, $shortcodes_data, true );
		}
	}

	private function is_media_attribute( array $attribute ) {
		$media_attribute_types = array(
			WPML_Page_Builders_Media_Shortcodes::TYPE_URL,
			WPML_Page_Builders_Media_Shortcodes::TYPE_IDS,
		);

		return isset( $attribute['attr']['type'] )
		       && in_array( $attribute['attr']['type'], $media_attribute_types, true );
	}

	private function convert_single_attribute_to_multiple_format( array $attribute ) {
		if ( ! is_numeric( key( $attribute ) ) ) {
			$attribute = array( $attribute );
		}

		return $attribute;
	}

	public function get_settings() {
		return $this->st_settings->get_setting( self::PB_SHORTCODE_SETTING );
	}

	public function get_media_settings() {
		return get_option( self::PB_MEDIA_SHORTCODE_SETTING, array() );
	}

	public function has_settings() {
		$settings = $this->get_settings();

		return ! empty( $settings );
	}
}
