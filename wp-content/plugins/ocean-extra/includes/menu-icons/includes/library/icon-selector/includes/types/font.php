<?php
/**
 * Base font icon handler
 *
 */

require_once dirname( __FILE__ ) . '/base.php';

/**
 * Generic handler for icon fonts
 *
 */
abstract class OE_Icon_Picker_Type_Font extends OE_Icon_Picker_Type {

	/**
	 * Stylesheet ID
	 *
	 */
	protected $stylesheet_id = '';

	/**
	 * JS Controller
	 *
	 */
	protected $controller = 'Font';

	/**
	 * Template ID
	 *
	 */
	protected $template_id = 'font';

	/**
	 * Get icon groups
	 *
	 */
	public function get_groups() {}

	/**
	 * Get icon names
	 *
	 */
	public function get_items() {}

	/**
	 * Get stylesheet URI
	 *
	 */
	public function get_stylesheet_uri() {
		$stylesheet_uri = '';

		if ( 'font-awesome' != $this->stylesheet_id
			&& 'simple-line-icons' != $this->stylesheet_id ) {
			$stylesheet_uri = sprintf(
				'%1$s/css/types/%2$s%3$s.css',
				OE_Icon_Selector::instance()->url,
				$this->stylesheet_id,
				( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min'
			);
		}

		if ( class_exists( 'OCEANWP_Theme_Class' ) ) {
			if ( 'font-awesome' == $this->stylesheet_id ) {
				$stylesheet_uri = OCEANWP_THEME_URI .'/assets/fonts/fontawesome/css/all.min.css';
			}

			if ( 'simple-line-icons' == $this->stylesheet_id ) {
				$stylesheet_uri = OCEANWP_CSS_DIR_URI .'third/simple-line-icons.min.css';
			}
		}

		/**
		 * Filters icon type's stylesheet URI
		 *
		 */
		$stylesheet_uri = apply_filters(
			'oe_icon_picker_icon_type_stylesheet_uri',
			$stylesheet_uri,
			$this->id,
			$this
		);

		return $stylesheet_uri;
	}

	/**
	 * Register assets
	 *
	 */
	public function register_assets( OE_Icon_Picker_Loader $loader ) {
		if ( empty( $this->stylesheet_uri ) ) {
			return;
		}

		$register = true;
		$deps     = false;
		$styles   = wp_styles();

		if ( $styles->query( $this->stylesheet_id, 'registered' ) ) {
			$object = $styles->registered[ $this->stylesheet_id ];

			if ( version_compare( $object->ver, $this->version, '<' ) ) {
				$deps = $object->deps;
				wp_deregister_style( $this->stylesheet_id );
			} else {
				$register = false;
			}
		}

		if ( $register ) {
			wp_register_style( $this->stylesheet_id, $this->stylesheet_uri, $deps, $this->version );
		}

		$loader->add_style( $this->stylesheet_id );
	}

	/**
	 * Constructor
	 *
	 */
	public function __construct( array $args = array() ) {
		parent::__construct( $args );

		if ( empty( $this->stylesheet_id ) ) {
			$this->stylesheet_id = $this->id;
		}

		add_action( 'oe_icon_picker_loader_init', array( $this, 'register_assets' ) );
	}

	/**
	 * Get extra properties data
	 *
	 */
	protected function get_props_data() {
		return array(
			'groups' => $this->groups,
			'items'  => $this->items,
		);
	}

	/**
	 * Get media templates
	 *
	 */
	public function get_templates() {
		$templates = array(
			'icon' => '<i class="_icon {{data.type}} {{ data.icon }}"></i>',
			'item' => sprintf(
				'<div class="attachment-preview js--select-attachment">
					<div class="thumbnail">
						<span class="_icon"><i class="{{data.type}} {{ data.id }}"></i></span>
						<div class="filename"><div>{{ data.name }}</div></div>
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
		$templates = apply_filters( 'oe_icon_picker_font_media_templates', $templates );

		return $templates;
	}
}
