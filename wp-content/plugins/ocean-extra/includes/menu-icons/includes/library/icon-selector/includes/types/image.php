<?php
/**
 * Image icon handler
 *
 */

require_once dirname( __FILE__ ) . '/base.php';

/**
 * Image icon
 *
 */
class OE_Icon_Picker_Type_Image extends OE_Icon_Picker_Type {

	/**
	 * Icon type ID
	 *
	 */
	protected $id = 'image';

	/**
	 * JS Controller
	 *
	 */
	protected $controller = 'Img';

	/**
	 * Template ID
	 *
	 */
	protected $template_id = 'image';

	/**
	 * Constructor
	 *
	 */
	public function __construct( $args = array() ) {
		if ( empty( $this->name ) ) {
			$this->name = __( 'Image', 'ocean-extra' );
		}

		parent::__construct( $args );
	}

	/**
	 * Get extra properties data
	 *
	 */
	protected function get_props_data() {
		return array(
			'mimeTypes' => $this->get_image_mime_types(),
		);

		return $props;
	}

	/**
	 * Get media templates
	 *
	 */
	public function get_templates() {
		$templates = array(
			'icon' => '<img src="{{ data.url }}" class="_icon" />',
		);

		/**
		 * Filter media templates
		 *
		 */
		$templates = apply_filters( 'oe_icon_picker_image_media_templates', $templates );

		return $templates;
	}

	/**
	 * Get image mime types
	 *
	 */
	protected function get_image_mime_types() {
		$mime_types = get_allowed_mime_types();

		foreach ( $mime_types as $id => $type ) {
			if ( false === strpos( $type, 'image/' ) ) {
				unset( $mime_types[ $id ] );
			}
		}

		/**
		 * Filter image mime types
		 *
		 */
		$mime_types = apply_filters( 'oe_icon_picker_image_mime_types', $mime_types );

		// We need to exclude image/svg*.
		if ( apply_filters( 'oe_icon_picker_image_mime_types_svg', true ) ) {
			unset( $mime_types['svg'] );
		}

		return $mime_types;
	}
}