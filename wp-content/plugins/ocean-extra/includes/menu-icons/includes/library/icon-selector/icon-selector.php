<?php
final class OE_Icon_Selector {

	const VERSION = '1.0.0';

	/**
	 * OE_Icon_Selector singleton
	 * 
	 */
	protected static $instance;

	/**
	 * Plugin directory path
	 * 
	 */
	protected $dir;

	/**
	 * Plugin directory url path
	 * 
	 */
	protected $url;

	/**
	 * Icon types registry
	 * 
	 */
	protected $registry;

	/**
	 * Loader
	 * 
	 */
	protected $loader;

	/**
	 * Whether the functionality is loaded on admin
	 * 
	 */
	protected $is_admin_loaded = false;

	/**
	 * Default icon types
	 * 
	 */
	protected $default_types = array(
		'fa'               		=> 'Font_Awesome',
		'fas'               	=> 'Font_Awesome_Solid',
		'fab'               	=> 'Font_Awesome_Brand',
		'simple-line-icons' 	=> 'Simple_Line_Icons',
		'dashicons'        		=> 'Dashicons',
		'elusive'          		=> 'Elusive',
		'foundation-icons' 		=> 'Foundation',
		'genericon'        		=> 'Genericons',
		'image'            		=> 'Image',
		'svg'              		=> 'Svg',
	);

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
	public static function instance( $args = array() ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $args );
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 * 
	 */
	protected function __construct( $args = array() ) {
		$defaults = array(
			'dir' => OE_PATH .'includes/menu-icons/includes/library/icon-selector',
			'url' => OE_URL .'includes/menu-icons/includes/library/icon-selector',
		);

		$args = wp_parse_args( $args, $defaults );
		$keys = array_keys( get_object_vars( $this ) );

		// Disallow.
		unset( $args['registry'] );
		unset( $args['loader'] );
		unset( $args['is_admin_loaded'] );

		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		add_action( 'wp_loaded', array( $this, 'init' ) );
	}

	/**
	 * Register icon types
	 * 
	 */
	public function init() {
		// Initialize icon types registry.
		$this->init_registry();

		// Initialize loader.
		$this->init_loader();

		// Initialize field.
		$this->init_field();

		/**
		 * Fires when Icon Picker is ready
		 * 
		 */
		do_action( 'oe_icon_picker_init', $this );
	}

	/**
	 * Initialize icon types registry
	 * 
	 */
	protected function init_registry() {

		require_once "{$this->dir}/includes/registry.php";
		$this->registry = OE_Icon_Picker_Types_Registry::instance();

		$this->register_default_types();

		/**
		 * Fires when Icon Picker's Registry is ready and the default types are registered.
		 * 
		 */
		do_action( 'oe_icon_picker_types_registry_ready', $this );
	}

	/**
	 * Register default icon types
	 * 
	 */
	protected function register_default_types() {
		require_once "{$this->dir}/includes/fontpack.php";
		OE_Icon_Picker_Fontpack::instance();

		/**
		 * Allow themes/plugins to disable one or more default types
		 * 
		 */
		$default_types = array_filter( (array) apply_filters( 'oe_icon_picker_default_types', $this->default_types ) );

		/**
		 * Validate filtered default types
		 */
		$default_types = array_intersect( $this->default_types, $default_types );

		if ( empty( $default_types ) ) {
			return;
		}

		foreach ( $default_types as $filename => $class_suffix ) {
			$class_name = "OE_Icon_Picker_Type_{$class_suffix}";

			require_once "{$this->dir}/includes/types/{$filename}.php";
			$this->registry->add( new $class_name() );
		}
	}

	/**
	 * Initialize loader
	 * 
	 */
	protected function init_loader() {
		require_once "{$this->dir}/includes/loader.php";
		$this->loader = OE_Icon_Picker_Loader::instance();

		/**
		 * Fires when Icon Picker's Registry is ready and the default types are registered.
		 * 
		 */
		do_action( 'oe_icon_picker_loader_ready', $this );
	}

	/**
	 * Initialize field functionalities
	 * 
	 */
	protected function init_field() {
		require_once "{$this->dir}/includes/fields/base.php";
	}

	/**
	 * Load icon picker functionality on an admin page
	 * 
	 */
	public function load() {
		if ( true === $this->is_admin_loaded ) {
			return;
		}

		$this->loader->load();
		$this->is_admin_loaded = true;
	}
}
add_action( 'wp_loaded', array( 'OE_Icon_Selector', 'instance' ), 7 );
