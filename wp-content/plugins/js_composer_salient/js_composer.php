<?php
/*
Plugin Name: Salient WPBakery Page Builder
Plugin URI: http://wpbakery.com
Description: Drag and drop page builder for WordPress. Take full control over your WordPress site, build any layout you can imagine – no programming knowledge required.
Version: 5.6
Author: Michael M - WPBakery.com | Modified by ThemeNectar
Author URI: http://wpbakery.com
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Current WPBakery Page Builder version
 */
if ( ! defined( 'WPB_VC_VERSION' ) ) {
	/**
	 *
	 */
	define( 'WPB_VC_VERSION', '5.6' );
}

/*nectar addition*/
define( 'SALIENT_VC_ACTIVE', true );
/*nectar addition end*/

/**
 * Vc starts here. Manager sets mode, adds required wp hooks and loads required object of structure
 *
 * Manager controls and access to all modules and classes of VC.
 *
 * @package WPBakeryVisualComposer
 * @since   4.2
 */
class Vc_Manager {
	/**
	 * Set status/mode for VC.
	 *
	 * It depends on what functionality is required from vc to work with current page/part of WP.
	 *
	 * Possible values:
	 *  none - current status is unknown, default mode;
	 *  page - simple wp page;
	 *  admin_page - wp dashboard;
	 *  admin_frontend_editor - WPBakery Page Builder front end editor version;
	 *  admin_settings_page - settings page
	 *  page_editable - inline version for iframe in front end editor;
	 *
	 * @since 4.2
	 * @var string
	 */
	private $mode = 'none';
	/**
	 * Enables WPBakery Page Builder to act as the theme plugin.
	 *
	 * @since 4.2
	 * @var bool
	 */
	private $is_as_theme = false;
	/**
	 * Vc is network plugin or not.
	 * @since 4.2
	 * @var bool
	 */
	private $is_network_plugin = null;
	/**
	 * List of paths.
	 *
	 * @since 4.2
	 * @var array
	 */
	private $paths = array();
	/**
	 * Default post types where to activate WPBakery Page Builder meta box settings
	 * @since 4.2
	 * @var array
	 */
	private $editor_default_post_types = array( 'page' ); // TODO: move to Vc settings
	/**
	 * Directory name in theme folder where composer should search for alternative templates of the shortcode.
	 * @since 4.2
	 * @var string
	 */
	private $custom_user_templates_dir = false;

	/**
	 * Set updater mode
	 * @since 4.2
	 * @var bool
	 */
	private $disable_updater = false;
	/**
	 * Modules and objects instances list
	 * @since 4.2
	 * @var array
	 */
	private $factory = array();
	/**
	 * File name for components manifest file.
	 *
	 * @since 4.4
	 * @var string
	 */
	private $components_manifest = 'components.json';
	/**
	 * @var string
	 */
	 /*nectar addition*/
  private $plugin_name = 'js_composer_salient/js_composer.php';
  /*nectar addition end*/

	/**
	 * Core singleton class
	 * @var self - pattern realization
	 */
	private static $_instance;

	/**
	 * @var Vc_Current_User_Access|false
	 * @since 4.8
	 */
	private $current_user_access = false;
	/**
	 * @var Vc_Role_Access|false
	 * @since 4.8
	 */
	private $role_access = false;

	public $editor_post_types;

	/**
	 * Constructor loads API functions, defines paths and adds required wp actions
	 *
	 * @since  4.2
	 */
	private function __construct() {
		$dir = dirname( __FILE__ );
		/**
		 * Define path settings for WPBakery Page Builder.
		 *
		 * APP_ROOT        - plugin directory.
		 * WP_ROOT         - WP application root directory.
		 * APP_DIR         - plugin directory name.
		 * CONFIG_DIR      - configuration directory.
		 * ASSETS_DIR      - asset directory full path.
		 * ASSETS_DIR_NAME - directory name for assets. Used from urls creating.
		 * CORE_DIR        - classes directory for core vc files.
		 * HELPERS_DIR     - directory with helpers functions files.
		 * SHORTCODES_DIR  - shortcodes classes.
		 * SETTINGS_DIR    - main dashboard settings classes.
		 * TEMPLATES_DIR   - directory where all html templates are hold.
		 * EDITORS_DIR     - editors for the post contents
		 * PARAMS_DIR      - complex params for shortcodes editor form.
		 * UPDATERS_DIR    - automatic notifications and updating classes.
		 */
		$this->setPaths( array(
			'APP_ROOT' => $dir,
			'WP_ROOT' => preg_replace( '/$\//', '', ABSPATH ),
			'APP_DIR' => basename( plugin_basename( $dir ) ),
			'CONFIG_DIR' => $dir . '/config',
			'ASSETS_DIR' => $dir . '/assets',
			'ASSETS_DIR_NAME' => 'assets',
			'AUTOLOAD_DIR' => $dir . '/include/autoload',
			'CORE_DIR' => $dir . '/include/classes/core',
			'HELPERS_DIR' => $dir . '/include/helpers',
			'SHORTCODES_DIR' => $dir . '/include/classes/shortcodes',
			'SETTINGS_DIR' => $dir . '/include/classes/settings',
			'TEMPLATES_DIR' => $dir . '/include/templates',
			'EDITORS_DIR' => $dir . '/include/classes/editors',
			'PARAMS_DIR' => $dir . '/include/params',
			'UPDATERS_DIR' => $dir . '/include/classes/updaters',
			'VENDORS_DIR' => $dir . '/include/classes/vendors',
		) );
		// Load API
		require_once $this->path( 'HELPERS_DIR', 'helpers_factory.php' );
		require_once $this->path( 'HELPERS_DIR', 'helpers.php' );
		require_once $this->path( 'CORE_DIR', 'interfaces.php' );
		require_once $this->path( 'CORE_DIR', 'class-vc-sort.php' ); // used by wpb-map
		require_once $this->path( 'CORE_DIR', 'class-wpb-map.php' );
		require_once $this->path( 'CORE_DIR', 'class-vc-shared-library.php' );
		require_once $this->path( 'HELPERS_DIR', 'helpers_api.php' );
		require_once $this->path( 'HELPERS_DIR', 'helpers_deprecated.php' );
		require_once $this->path( 'HELPERS_DIR', 'filters.php' );
		require_once $this->path( 'PARAMS_DIR', 'params.php' );
		require_once $this->path( 'AUTOLOAD_DIR', 'vc-shortcode-autoloader.php' );
		require_once $this->path( 'SHORTCODES_DIR', 'shortcodes.php' );
		// Add hooks
		add_action( 'plugins_loaded', array(
			$this,
			'pluginsLoaded',
		), 9 );
		add_action( 'init', array(
			$this,
			'init',
		), 9 );
		$this->setPluginName( $this->path( 'APP_DIR', 'js_composer.php' ) );
		register_activation_hook( __FILE__, array(
			$this,
			'activationHook',
		) );
	}

	/**
	 * Get the instane of VC_Manager
	 *
	 * @return self
	 */
	public static function getInstance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * prevent the instance from being cloned (which would create a second instance of it)
	 */
	private function __clone() {
	}

	/**
	 * prevent from being unserialized (which would create a second instance of it)
	 */
	private function __wakeup() {
	}

	/**
	 * Callback function WP plugin_loaded action hook. Loads locale
	 *
	 * @since  4.2
	 * @access public
	 */
	public function pluginsLoaded() {
		// Setup locale
		do_action( 'vc_plugins_loaded' );
		load_plugin_textdomain( 'js_composer', false, $this->path( 'APP_DIR', 'locale' ) );
	}

	/**
	 * Callback function for WP init action hook. Sets Vc mode and loads required objects.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return void
	 */
	public function init() {
		do_action( 'vc_before_init' );
		$this->setMode();
		do_action( 'vc_after_set_mode' );
		/**
		 * Set version of VC if required.
		 */
		$this->setVersion();
		// Load required
		/* nectar addition */ 
		//! vc_is_updater_disabled() && vc_updater()->init();
		/* nectar addition end */ 
		/**
		 * Init default hooks and options to load.
		 */
		$this->vc()->init();
		/**
		 * if is admin and not front end editor.
		 */
		is_admin() && ! vc_is_frontend_editor() && $this->asAdmin();
		/**
		 * if frontend editor is enabled init editor.
		 */
		vc_enabled_frontend() && vc_frontend_editor()->init();
		do_action( 'vc_before_mapping' ); // VC ACTION
		// Include default shortcodes.
		$this->mapper()->init(); //execute all required
		do_action( 'vc_after_mapping' ); // VC ACTION
		// Load && Map shortcodes from Automapper.
		vc_automapper()->map();
		/* nectar addition */ 
		/*if ( vc_user_access()->wpAny( 'manage_options' )->part( 'settings' )->can( 'vc-updater-tab' )->get() ) {
			vc_license()->setupReminder();
		} */
		do_action( 'vc_after_init' );
	}

	/**
	 * @return Vc_Current_User_Access
	 * @since 4.8
	 */
	public function getCurrentUserAccess() {
		if ( ! $this->current_user_access ) {
			require_once vc_path_dir( 'CORE_DIR', 'access/class-vc-current-user-access.php' );
			$this->current_user_access = new Vc_Current_User_Access();
		}

		return $this->current_user_access;
	}

	/**
	 * @param false|Vc_Current_User_Access $current_user_access
	 */
	public function setCurrentUserAccess( $current_user_access ) {
		$this->current_user_access = $current_user_access;
	}

	/**
	 * @return Vc_Role_Access
	 * @since 4.8
	 */
	public function getRoleAccess() {
		if ( ! $this->role_access ) {
			require_once vc_path_dir( 'CORE_DIR', 'access/class-vc-role-access.php' );
			$this->role_access = new Vc_Role_Access();
		}

		return $this->role_access;
	}

	/**
	 * @param false|Vc_Role_Access $role_access
	 */
	public function setRoleAccess( $role_access ) {
		$this->role_access = $role_access;
	}

	/**
	 * Enables to add hooks in activation process.
	 * @since 4.5
	 *
	 * @param $networkWide
	 */
	public function activationHook( $networkWide = false ) {
		do_action( 'vc_activation_hook', $networkWide );
	}

	/**
	 * Load required components to enable useful functionality.
	 *
	 * @access public
	 * @since 4.4
	 */
	public function loadComponents() {
		$manifest_file = apply_filters( 'vc_autoload_components_manifest_file', vc_path_dir( 'AUTOLOAD_DIR', $this->components_manifest ) );
		if ( is_file( $manifest_file ) ) {
			ob_start();
			require_once $manifest_file;
			$data = ob_get_clean();
			if ( $data ) {
				$components = (array) json_decode( $data );
				$components = apply_filters( 'vc_autoload_components_list', $components );
				$dir = vc_path_dir( 'AUTOLOAD_DIR' );
				foreach ( $components as $component => $description ) {
					$component_path = $dir . '/' . $component;
					if ( false === strpos( $component_path, '*' ) ) {
						require_once $component_path;
					} else {
						$components_paths = glob( $component_path );
						if ( is_array( $components_paths ) ) {
							foreach ( $components_paths as $path ) {
								if ( false === strpos( $path, '*' ) ) {
									require_once $path;
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Load required logic for operating in Wp Admin dashboard.
	 *
	 * @since  4.2
	 * @access protected
	 *
	 * @return void
	 */
	protected function asAdmin() {
		/* nectar addition
		vc_license()->init(); */
		vc_backend_editor()->addHooksSettings();
	}

	/**
	 * Set VC mode.
	 *
	 * Mode depends on which page is requested by client from server and request parameters like vc_action.
	 *
	 * @since  4.2
	 * @access protected
	 *
	 * @return void
	 */
	protected function setMode() {
		/**
		 * TODO: Create another system (When ajax rebuild).
		 * Use vc_action param to define mode.
		 * 1. admin_frontend_editor - set by editor or request param
		 * 2. admin_backend_editor - set by editor or request param
		 * 3. admin_frontend_editor_ajax - set by request param
		 * 4. admin_backend_editor_ajax - set by request param
		 * 5. admin_updater - by vc_action
		 * 6. page_editable - by vc_action
		 */
		if ( is_admin() ) {
			if ( 'vc_inline' === vc_action() ) {
				vc_user_access()->wpAny( array(
					'edit_post',
					(int) vc_request_param( 'post_id' ),
				) )->validateDie()->part( 'frontend_editor' )->can()->validateDie();
				$this->mode = 'admin_frontend_editor';
			} elseif ( ( vc_user_access()->wpAny( 'edit_posts', 'edit_pages' )
					->get() ) && ( 'vc_upgrade' === vc_action() || ( 'update-selected' === vc_get_param( 'action' ) && $this->pluginName() === vc_get_param( 'plugins' ) ) )
			) {
				$this->mode = 'admin_updater';
			} elseif ( vc_user_access()->wpAny( 'manage_options' )->get() && isset( $_GET['page'] ) && array_key_exists( $_GET['page'], vc_settings()->getTabs() ) ) {
				$this->mode = 'admin_settings_page';
			} else {
				$this->mode = 'admin_page';
			}
		} else {
			if ( isset( $_GET['vc_editable'] ) && 'true' === $_GET['vc_editable'] ) {
				vc_user_access()->checkAdminNonce()->validateDie()->wpAny( array(
					'edit_post',
					(int) vc_request_param( 'vc_post_id' ),
				) )->validateDie()->part( 'frontend_editor' )->can()->validateDie();
				$this->mode = 'page_editable';
			} else {
				$this->mode = 'page';
			}
		}
	}

	/**
	 * Sets version of the VC in DB as option `vc_version`
	 *
	 * @since 4.3.2
	 * @access protected
	 *
	 * @return void
	 */
	protected function setVersion() {
		$version = get_option( 'vc_version' );
		if ( ! is_string( $version ) || version_compare( $version, WPB_VC_VERSION ) !== 0 ) {
			add_action( 'vc_after_init', array(
				vc_settings(),
				'rebuild',
			) );
			update_option( 'vc_version', WPB_VC_VERSION );
		}
	}

	/**
	 * Get current mode for VC.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return string
	 */
	public function mode() {
		return $this->mode;
	}

	/**
	 * Setter for paths
	 *
	 * @since  4.2
	 * @access protected
	 *
	 * @param $paths
	 */
	protected function setPaths( $paths ) {
		$this->paths = $paths;
	}

	/**
	 * Gets absolute path for file/directory in filesystem.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param $name - name of path dir
	 * @param string $file - file name or directory inside path
	 *
	 * @return string
	 */
	public function path( $name, $file = '' ) {
		$path = $this->paths[ $name ] . ( strlen( $file ) > 0 ? '/' . preg_replace( '/^\//', '', $file ) : '' );

		return apply_filters( 'vc_path_filter', $path );
	}

	/**
	 * Set default post types. Vc editors are enabled for such kind of posts.
	 *
	 * @param array $type - list of default post types.
	 */
	public function setEditorDefaultPostTypes( array $type ) {
		/*nectar addition */
		//$this->editor_default_post_types = $type;
		$options = get_option( 'salient_redux' );
		$nectar_vc_post_types = (!empty($options['product_tab_position']) && $options['product_tab_position'] == 'fullwidth') ? array('page','post','portfolio','product') : array('page','post','portfolio');
		$nectar_vc_post_types = $type;
		/*nectar addition end */
	}

	/**
	 * Returns list of default post types where user can use WPBakery Page Builder editors.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return array
	 */
	public function editorDefaultPostTypes() {
		/*nectar addition */
		$options = get_option( 'salient_redux' );
		$nectar_vc_post_types = (!empty($options['product_tab_position']) && $options['product_tab_position'] == 'fullwidth') ? array('page','post','portfolio','product') : array('page','post','portfolio');
		return $nectar_vc_post_types;
		/*nectar addition end */
	}

	/**
	 * Get post types where VC editors are enabled.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return array
	 */
	public function editorPostTypes() {
		if ( is_null( $this->editor_post_types ) ) {
			$post_types = array_keys( vc_user_access()->part( 'post_types' )->getAllCaps() );
			$this->editor_post_types = $post_types ? $post_types : $this->editorDefaultPostTypes();
		}

		return $this->editor_post_types;
	}

	/**
	 * Set post types where VC editors are enabled.
	 *
	 * @since  4.4
	 * @access public
	 *
	 * @param array $post_types
	 */
	public function setEditorPostTypes( array $post_types ) {
		$this->editor_post_types = ! empty( $post_types ) ? $post_types : $this->editorDefaultPostTypes();

		require_once ABSPATH . 'wp-admin/includes/user.php';

		$editable_roles = get_editable_roles();
		foreach ( $editable_roles as $role => $settings ) {
			$part = vc_role_access()->who( $role )->part( 'post_types' );
			$all_post_types = $part->getAllCaps();

			foreach ( $all_post_types as $post_type => $value ) {
				$part->getRole()->remove_cap( $part->getStateKey() . '/' . $post_type );
			}
			$part->setState( 'custom' );

			foreach ( $this->editor_post_types as $post_type ) {
				$part->setCapRule( $post_type );
			}
		}

	}

	/**
	 * Setter for as-theme-plugin status for VC.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param bool $value
	 */
	public function setIsAsTheme( $value = true ) {
		$this->is_as_theme = (boolean) $value;
	}

	/**
	 * Get as-theme-plugin status
	 *
	 * As theme plugin status used by theme developers. It disables settings
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return bool
	 */
	public function isAsTheme() {
		return (boolean) $this->is_as_theme;
	}

	/**
	 * Setter for as network plugin for MultiWP.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param bool $value
	 */
	public function setAsNetworkPlugin( $value = true ) {
		$this->is_network_plugin = $value;
	}

	/**
	 * Gets VC is activated as network plugin.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return bool
	 */
	public function isNetworkPlugin() {
		if ( is_null( $this->is_network_plugin ) ) {
			// Check is VC as network plugin
			if ( is_multisite() && ( is_plugin_active_for_network( $this->pluginName() ) || is_network_only_plugin( $this->pluginName() ) ) ) {
				$this->setAsNetworkPlugin( true );
			}
		}

		return $this->is_network_plugin ? true : false;
	}

	/**
	 * Setter for disable updater variable.
	 * @since 4.2
	 * @see
	 *
	 * @param bool $value
	 */
	public function disableUpdater( $value = true ) {
		$this->disable_updater = $value;
	}

	/**
	 * Get is vc updater is disabled;
	 *
	 * @since 4.2
	 * @see to where updater will be
	 *
	 * @return bool
	 */
	public function isUpdaterDisabled() {
		return is_admin() && $this->disable_updater;
	}

	/**
	 * Set user directory name.
	 *
	 * Directory name is the directory name vc should scan for custom shortcodes template.
	 *
	 * @since    4.2
	 * @access   public
	 *
	 * @param $dir - path to shortcodes templates inside developers theme
	 */
	public function setCustomUserShortcodesTemplateDir( $dir ) {
		preg_replace( '/\/$/', '', $dir );
		$this->custom_user_templates_dir = $dir;
	}

	/**
	 * Get default directory where shortcodes templates area placed.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return string - path to default shortcodes
	 */
	public function getDefaultShortcodesTemplatesDir() {
		return vc_path_dir( 'TEMPLATES_DIR', 'shortcodes' );
	}

	/**
	 *
	 * Get shortcodes template dir.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @param $template
	 *
	 * @return string
	 */
	public function getShortcodesTemplateDir( $template ) {
		return false !== $this->custom_user_templates_dir ? $this->custom_user_templates_dir . '/' . $template : locate_template( 'vc_templates' . '/' . $template );
	}

	/**
	 * Directory name where template files will be stored.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return string
	 */
	public function uploadDir() {
		return 'js_composer';
	}

	/**
	 * Getter for VC_Mapper instance
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return Vc_Mapper
	 */
	public function mapper() {
		if ( ! isset( $this->factory['mapper'] ) ) {
			require_once $this->path( 'CORE_DIR', 'class-vc-mapper.php' );
			$this->factory['mapper'] = new Vc_Mapper();
		}

		return $this->factory['mapper'];
	}

	/**
	 * WPBakery Page Builder.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return Vc_Base
	 */
	public function vc() {
		if ( ! isset( $this->factory['vc'] ) ) {
			do_action( 'vc_before_init_vc' );
			require_once $this->path( 'CORE_DIR', 'class-vc-base.php' );
			$vc = new Vc_Base();
			// DI Set template new modal editor.
			require_once $this->path( 'EDITORS_DIR', 'popups/class-vc-templates-panel-editor.php' );
			require_once $this->path( 'CORE_DIR', 'shared-templates/class-vc-shared-templates.php' );
			$vc->setTemplatesPanelEditor( new Vc_Templates_Panel_Editor() );
			// Create shared templates
			$vc->shared_templates = new Vc_Shared_Templates();

			// DI Set edit form
			require_once $this->path( 'EDITORS_DIR', 'popups/class-vc-shortcode-edit-form.php' );
			$vc->setEditForm( new Vc_Shortcode_Edit_Form() );

			// DI Set preset new modal editor.
			require_once $this->path( 'EDITORS_DIR', 'popups/class-vc-preset-panel-editor.php' );
			$vc->setPresetPanelEditor( new Vc_Preset_Panel_Editor() );

			$this->factory['vc'] = $vc;
			do_action( 'vc_after_init_vc' );
		}

		return $this->factory['vc'];
	}

	/**
	 * Vc options.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return Vc_Settings
	 */
	public function settings() {
		if ( ! isset( $this->factory['settings'] ) ) {
			do_action( 'vc_before_init_settings' );
			require_once $this->path( 'SETTINGS_DIR', 'class-vc-settings.php' );
			$this->factory['settings'] = new Vc_Settings();
			do_action( 'vc_after_init_settings' );
		}

		return $this->factory['settings'];
	}

	/**
	 * Vc license settings.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return Vc_License
	 */
	public function license() {
		if ( ! isset( $this->factory['license'] ) ) {
			do_action( 'vc_before_init_license' );
			require_once $this->path( 'SETTINGS_DIR', 'class-vc-license.php' );
			$this->factory['license'] = new Vc_License();
			do_action( 'vc_after_init_license' );
		}

		return $this->factory['license'];
	}

	/**
	 * Get frontend VC editor.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return Vc_Frontend_Editor
	 */
	public function frontendEditor() {
		if ( ! isset( $this->factory['frontend_editor'] ) ) {
			do_action( 'vc_before_init_frontend_editor' );
			require_once $this->path( 'EDITORS_DIR', 'class-vc-frontend-editor.php' );
			$this->factory['frontend_editor'] = new Vc_Frontend_Editor();
		}

		return $this->factory['frontend_editor'];
	}

	/**
	 * Get backend VC editor. Edit page version.
	 *
	 * @since 4.2
	 *
	 * @return Vc_Backend_Editor
	 */
	public function backendEditor() {
		if ( ! isset( $this->factory['backend_editor'] ) ) {
			do_action( 'vc_before_init_backend_editor' );
			require_once $this->path( 'EDITORS_DIR', 'class-vc-backend-editor.php' );
			$this->factory['backend_editor'] = new Vc_Backend_Editor();
		}

		return $this->factory['backend_editor'];
	}

	/**
	 * Gets automapper instance.
	 *
	 * @since  4.2
	 * @access public
	 *
	 * @return Vc_Automapper
	 */
	public function automapper() {
		if ( ! isset( $this->factory['automapper'] ) ) {
			do_action( 'vc_before_init_automapper' );
			require_once $this->path( 'SETTINGS_DIR', 'class-vc-automapper.php' );
			$this->factory['automapper'] = new Vc_Automapper();
			do_action( 'vc_after_init_automapper' );
		}

		return $this->factory['automapper'];
	}

	/**
	 * Gets updater instance.
	 * @since 4.2
	 *
	 * @return Vc_Updater
	 */
	public function updater() {

		if ( ! isset( $this->factory['updater'] ) ) {
			do_action( 'vc_before_init_updater' );
			require_once $this->path( 'UPDATERS_DIR', 'class-vc-updater.php' );
			$updater = new Vc_Updater();
			require_once vc_path_dir( 'UPDATERS_DIR', 'class-vc-updating-manager.php' );
			$updater->setUpdateManager( new Vc_Updating_Manager( WPB_VC_VERSION, $updater->versionUrl(), $this->pluginName() ) );
			$this->factory['updater'] = $updater;
			do_action( 'vc_after_init_updater' );
		}

		return $this->factory['updater'];
	}

	/**
	 * Getter for plugin name variable.
	 * @since 4.2
	 *
	 * @return string
	 */
	public function pluginName() {
		return $this->plugin_name;
	}

	/**
	 * @since 4.8.1
	 *
	 */
	public function setPluginName( $name ) {
		$this->plugin_name = $name;
	}

	/**
	 * Get absolute url for VC asset file.
	 *
	 * Assets are css, javascript, less files and images.
	 *
	 * @since 4.2
	 *
	 * @param $file
	 *
	 * @return string
	 */
	public function assetUrl( $file ) {
		return preg_replace( '/\s/', '%20', plugins_url( $this->path( 'ASSETS_DIR_NAME', $file ), __FILE__ ) );
	}
}

/**
 * Main WPBakery Page Builder manager.
 * @var Vc_Manager $vc_manager - instance of composer management.
 * @since 4.2
 */
global $vc_manager;
if ( ! $vc_manager ) {
	$vc_manager = Vc_Manager::getInstance();
	// Load components
	$vc_manager->loadComponents();
}
