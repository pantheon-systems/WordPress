<?php

class WPML_Package_Translation_UI {
	var     $load_priority = 101;
	private $menu_root     = '';

	const MENU_SLUG = 'wpml-package-management';

	public function __construct() {
		add_action( 'wpml_loaded', array( $this, 'loaded' ), $this->load_priority );
	}

	public function loaded() {
		
		if ($this->passed_dependencies()) {
			$this->set_admin_hooks();
			do_action( 'WPML_PT_HTML' );
		}
	}

	private function passed_dependencies() {
		return defined( 'ICL_SITEPRESS_VERSION' )
		       && defined( 'WPML_ST_VERSION' )
		       && defined( 'WPML_TM_VERSION' );
	}
	
	private function set_admin_hooks() {
		if(is_admin()) {
			add_action( 'wpml_admin_menu_configure', array( $this, 'menu' ) );
			add_action( 'wpml_admin_menu_root_configured', array( $this, 'main_menu_configured' ), 10, 2 );

			add_action( 'admin_register_scripts', array( $this, 'admin_register_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}
	}

	public function main_menu_configured( $menu_id, $root_slug ) {
		if ( 'WPML' === $menu_id ) {
			$this->menu_root = $root_slug;
		}
	}

	/**
	 * @param string $menu_id
	 */
	public function menu( $menu_id ) {
		if ( 'WPML' !== $menu_id || ! defined( 'ICL_PLUGIN_PATH' ) ) {
			return;
		}
		global $sitepress;
		if ( ! isset( $sitepress ) || ( method_exists( $sitepress, 'get_setting' ) && ! $sitepress->get_setting( 'setup_complete' ) ) ) {
			return;
		}

		global $sitepress_settings;

		if ( ! isset( $sitepress_settings['existing_content_language_verified' ] ) || ! $sitepress_settings['existing_content_language_verified' ] ) {
			return;
		}

		if ( current_user_can( 'wpml_manage_string_translation' ) ) {
			$menu               = array();
			$menu['order']      = 1300;
			$menu['page_title'] = __( 'Packages', 'wpml-string-translation' );
			$menu['menu_title'] = __( 'Packages', 'wpml-string-translation' );
			$menu['capability'] = 'wpml_manage_string_translation';
			$menu['menu_slug']  = self::MENU_SLUG;
			$menu['function']   = array(
				'WPML_Package_Translation_HTML_Packages',
				'package_translation_menu'
			);

			do_action( 'wpml_admin_menu_register_item', $menu );
			$this->admin_register_scripts();
		}
	}

	function admin_register_scripts() {
		wp_register_script( 'wpml-package-trans-man-script', WPML_PACKAGE_TRANSLATION_URL . '/resources/js/wpml_package_management.js', array( 'jquery' ) );
	}

	function admin_enqueue_scripts( $hook ) {
		if ( get_plugin_page_hookname( self::MENU_SLUG, $this->menu_root ) === $hook ) {
			wp_enqueue_script( 'wpml-package-trans-man-script' );
		}
	}
}