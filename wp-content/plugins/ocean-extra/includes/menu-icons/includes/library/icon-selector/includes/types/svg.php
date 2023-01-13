<?php
/**
 * SVG icon handler
 *
 */

require_once dirname( __FILE__ ) . '/image.php';

/**
 * Image icon
 *
 */
class OE_Icon_Picker_Type_Svg extends OE_Icon_Picker_Type_Image {

	/**
	 * Icon type ID
	 *
	 */
	protected $id = 'svg';

	/**
	 * Template ID
	 *
	 */
	protected $template_id = 'svg';

	/**
	 * Mime type
	 *
	 */
	protected $mime_type = 'image/svg+xml';

	/**
	 * Constructor
	 *
	 */
	public function __construct( $args = array() ) {
		$this->name = __( 'SVG', 'ocean-extra' );

		parent::__construct( $args );
		add_filter( 'upload_mimes', array( $this, '_add_mime_type' ) );
	}

	/**
	 * Add SVG support
	 *
	 */
	public function _add_mime_type( array $mimes ) {
		if ( ! isset( $mimes['svg'] ) ) {
			if ( apply_filters( 'oe_icon_picker_svg_mime_type', true ) ) {
				$mimes['svg'] = $this->mime_type;
			}
		}

		return $mimes;
	}

	/**
	 * Get extra properties data
	 *
	 */
	protected function get_props_data() {
		return array(
			'mimeTypes' => array( $this->mime_type ),
		);
	}

	/**
	 * Media templates
	 *
	 */
	public function get_templates() {
		$templates = array(
			'icon' => '<img src="{{ data.url }}" class="_icon" />',
			'item' => sprintf(
				'<div class="attachment-preview js--select-attachment svg-icon">
					<div class="thumbnail">
						<div class="centered">
							<img src="{{ data.url }}" alt="{{ data.alt }}" class="_icon _{{data.type}}" />
						</div>
					</div>
				</div>
				<a class="check" href="#" title="%s"><div class="media-modal-icon"></div></a>',
				esc_attr__( 'Deselect', 'ocean-extra' )
			),
		);

		/**
		 * Filter media templates
		 *
		 */
		$templates = apply_filters( 'oe_icon_picker_svg_media_templates', $templates );

		return $templates;
	}
}
