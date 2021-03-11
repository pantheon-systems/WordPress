<?php
/**
 * Icon Picker Loader
 *
 */

final class OE_Icon_Picker_Loader {

	/**
	 * OE_Icon_Picker_Loader singleton
	 *
	 */
	protected static $instance;

	/**
	 * Script IDs
	 *
	 */
	protected $script_ids = array();

	/**
	 * Stylesheet IDs
	 *
	 */
	protected $style_ids = array();

	/**
	 * Printed media templates
	 *
	 */
	protected $printed_templates = array();

	/**
	 * Setter magic
	 *
	 */
	public function __isset( $name ) {
		return isset( $this->$name );
	}

	/**
	 * Getter magic
	 *
	 */
	public function __get( $name ) {
		if ( isset( $this->$name ) ) {
			return $this->$name;
		}

		return null;
	}

	/**
	 * Get instance
	 *
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 */
	protected function __construct() {
		$this->register_assets();

		add_filter( 'media_view_strings', array( $this, '_media_view_strings' ) );

		/**
		 * Fires when Icon Picker loader is ready
		 *
		 */
		do_action( 'oe_icon_picker_loader_init', $this );
	}

	/**
	 * Add script
	 *
	 */
	public function add_script( $script_id ) {
		if ( wp_script_is( $script_id, 'registered' ) ) {
			$this->script_ids[] = $script_id;

			return true;
		}

		return false;
	}

	/**
	 * Add stylesheet
	 *
	 */
	public function add_style( $stylesheet_id ) {
		if ( wp_style_is( $stylesheet_id, 'registered' ) ) {
			$this->style_ids[] = $stylesheet_id;

			return true;
		}

		return false;
	}

	/**
	 * Register scripts & styles
	 *
	 */
	protected function register_assets() {
		$icon_picker = OE_Icon_Selector::instance();

		if ( defined( 'ICON_PICKER_SCRIPT_DEBUG' ) && ICON_PICKER_SCRIPT_DEBUG ) {
			$assets_url = '//localhost:8080';
			$suffix     = '';
		} else {
			$assets_url = $icon_picker->url;
			$suffix     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		}

		wp_register_script(
			'icon-picker',
			"{$assets_url}/js/icon-picker{$suffix}.js",
			array( 'media-views' ),
			OE_Icon_Selector::VERSION,
			true
		);
		$this->add_script( 'icon-picker' );

		wp_register_style(
			'icon-picker',
			"{$icon_picker->url}/css/icon-picker{$suffix}.css",
			false,
			OE_Icon_Selector::VERSION
		);
		$this->add_style( 'icon-picker' );
	}

	/**
	 * Load admin functionalities
	 *
	 */
	public function load() {
		if ( ! is_admin() ) {
			_doing_it_wrong(
				__METHOD__,
				'It should only be called on admin pages.',
				esc_html( OE_Icon_Selector::VERSION )
			);

			return;
		}

		if ( ! did_action( 'oe_icon_picker_loader_init' ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					'It should not be called until the %s hook.',
					'<code>oe_icon_picker_loader_init</code>'
				),
				esc_html( OE_Icon_Selector::VERSION )
			);

			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, '_enqueue_assets' ) );
		add_action( 'print_media_templates', array( $this, '_media_templates' ) );
	}

	/**
	 * Filter media view strings
	 *
	 */
	public function _media_view_strings( $strings ) {
		$strings['iconPicker'] = array(
			'frameTitle' => __( 'Icon Picker', 'ocean-extra' ),
			'allFilter'  => __( 'All', 'ocean-extra' ),
			'selectIcon' => __( 'Select Icon', 'ocean-extra' ),
		);

		return $strings;
	}

	/**
	 * Enqueue scripts & styles
	 *
	 */
	public function _enqueue_assets() {
		$icon_picker = OE_Icon_Selector::instance();

		wp_localize_script(
			'icon-picker',
			'iconPicker',
			array(
				'types' => $icon_picker->registry->get_types_for_js(),
			)
		);

		// Some pages don't call this by default, so let's make sure.
		wp_enqueue_media();

		foreach ( $this->script_ids as $script_id ) {
			wp_enqueue_script( $script_id );
		}

		foreach ( $this->style_ids as $style_id ) {
			wp_enqueue_style( $style_id );
		}

		/**
		 * Fires when admin functionality is loaded
		 *
		 */
		do_action( 'oe_icon_picker_admin_loaded', $icon_picker );
	}

	/**
	 * Media templates
	 *
	 */
	public function _media_templates() {
		$icon_picker = OE_Icon_Selector::instance();

		foreach ( $icon_picker->registry->types as $type ) {
			if ( empty( $type->templates ) ) {
				continue;
			}

			$template_id_prefix = "tmpl-iconpicker-{$type->template_id}";
			if ( in_array( $template_id_prefix, $this->printed_templates, true ) ) {
				continue;
			}

			foreach ( $type->templates as $template_id_suffix => $template ) {
				$this->_print_template( "{$template_id_prefix}-{$template_id_suffix}", $template );
			}

			$this->printed_templates[] = $template_id_prefix;
		}

		/**
		 * Fires after all media templates have been printed
		 *
		 */
		do_action( 'oe_icon_picker_print_media_templates', $icon_picker );
	}

	/**
	 * Print media template
	 *
	 */
	protected function _print_template( $id, $template ) {
		?>
			<script type="text/html" id="<?php echo esc_attr( $id ) ?>">
				<?php echo $template; // xss ok ?>
			</script>
		<?php
	}
}