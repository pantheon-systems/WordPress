<?php

class WPML_PB_Shortcode_Strategy implements IWPML_PB_Strategy {

	private $shortcodes = array();
	/** @var  WPML_PB_Factory $factory */
	private $factory;

	/** @var WPML_Page_Builder_Settings $page_builder_settings */
	private $page_builder_settings;

	public function __construct( WPML_Page_Builder_Settings $page_builder_settings ) {
		$this->page_builder_settings = $page_builder_settings;
	}

	public function add_shortcodes( $shortcode_data ) {

		foreach ( $shortcode_data as $shortcode ) {
			$tag         = $shortcode['tag']['value'];
			$is_raw_html = isset( $shortcode['tag']['raw-html'] ) && $shortcode['tag']['raw-html'];

			if ( $is_raw_html && ! $this->page_builder_settings->is_raw_html_translatable() ) {
				continue;
			}

			if ( ! in_array( $tag, $this->shortcodes ) ) {
				$this->shortcodes[ $tag ] = array(
					'encoding'           => $shortcode['tag']['encoding'],
					'encoding-condition' => isset( $shortcode['tag']['encoding-condition'] ) ? $shortcode['tag']['encoding-condition'] : '',
					'type'               => isset( $shortcode['tag']['type'] ) ? $shortcode['tag']['type'] : '',
					'ignore-content'     => isset( $shortcode['tag']['ignore-content'] ) ? (bool) $shortcode['tag']['ignore-content'] : false,
					'attributes'         => array(),
				);
			}
			if ( isset( $shortcode['attributes'] ) ) {
				foreach ( $shortcode['attributes'] as $attribute ) {
					$this->shortcodes[ $tag ]['attributes'][ $attribute['value'] ] = $attribute;
				}
			}
		}
	}

	public function get_shortcodes() {
		return array_keys( $this->shortcodes );
	}

	public function get_shortcode_attributes( $tag ) {
		return array_keys( $this->shortcodes[ $tag ]['attributes'] );
	}

	public function get_shortcode_tag_encoding( $tag ) {
		return $this->shortcodes[ $tag ]['encoding'];
	}

	public function get_shortcode_tag_encoding_condition( $tag ) {
		return $this->shortcodes[ $tag ]['encoding-condition'];
	}

	public function get_shortcode_tag_type( $tag ) {
		if ( $this->shortcodes[ $tag ]['type'] ) {
			return strtoupper( $this->shortcodes[ $tag ]['type'] );
		}
		return 'VISUAL';
	}

	public function get_shortcode_ignore_content( $tag ) {
		return $this->shortcodes[ $tag ]['ignore-content'];
	}

	public function get_shortcode_attribute_encoding( $tag, $attribute ) {
		return $this->shortcodes[ $tag ]['attributes'][ $attribute ]['encoding'];
	}

	public function get_shortcode_attribute_type( $tag, $attribute ) {
		if ( $this->shortcodes[ $tag ]['attributes'][ $attribute ]['type'] ) {
			return strtoupper( $this->shortcodes[ $tag ]['attributes'][ $attribute ]['type'] );
		}
		return 'LINE';
	}

	public function get_shortcode_parser() {
		return $this->factory->get_shortcode_parser( $this );
	}

	/**
	 * @param $post
	 *
	 */
	public function register_strings( $post ) {
		$register_shortcodes = $this->factory->get_register_shortcodes( $this );
		$register_shortcodes->register_shortcode_strings( $post->ID, $post->post_content );
	}

	public function set_factory( $factory ) {
		$this->factory = $factory;
	}

	public function get_package_key( $page_id ) {
		return array(
			'kind'    => $this->get_package_kind(),
			'name'    => $page_id,
			'title'   => 'Page Builder Page ' . $page_id,
			'post_id' => $page_id,
		);
	}

	public function get_package_kind() {
		return 'Page Builder ShortCode Strings';
	}

	public function get_update_post( $package_data ) {
		return $this->factory->get_update_post( $package_data, $this );
	}

	public function get_content_updater() {
		return $this->factory->get_shortcode_content_updater( $this );
	}

	public function get_package_strings( $package_data ) {
		return $this->factory->get_string_translations( $this )->get_package_strings( $package_data );
	}

	public function remove_string( $string_data ) {
		return $this->factory->get_string_translations( $this )->remove_string( $string_data );

	}

	/**
	 * @param int $post_id
	 * @param object $post_content
	 */
	public function migrate_location( $post_id, $post_content ) {
		$migrate_locations = $this->factory->get_register_shortcodes( $this, true );
		$migrate_locations->register_shortcode_strings( $post_id, $post_content );
	}
}
