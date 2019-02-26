<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Main_Admin_Menu {
	const MENU_ORDER_LANGUAGES                       = 100;
	const MENU_ORDER_THEMES_AND_PLUGINS_LOCALIZATION = 200;
	const MENU_ORDER_TAXONOMY_TRANSLATION            = 900;
	const MENU_ORDER_SETTINGS                        = 9900;
	const MENU_ORDER_MAX                             = 10000;

	/** @var string */
	private $languages_menu_slug;
	/**
	 * @var WPML_Admin_Menu_Root
	 */
	private $root;
	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * WPML_Menu_Main constructor.
	 *
	 * @param SitePress $sitepress
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress           = $sitepress;
		$this->languages_menu_slug = WPML_PLUGIN_FOLDER . '/menu/languages.php';
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	public function configure() {
		$this->root = new WPML_Admin_Menu_Root( array(
			                                  'menu_id' => 'WPML',
			                                  'page_title' => __( 'WPML', 'sitepress' ),
			                                  'menu_title' => __( 'WPML', 'sitepress' ),
			                                  'capability' => 'wpml_manage_languages',
			                                  'menu_slug',
			                                  'function'   => null,
			                                  'icon_url'   => ICL_PLUGIN_URL . '/res/img/icon16.png',
		                                  ) );

		$this->root->init_hooks();

		$this->languages();

		if ( $this->sitepress->is_setup_complete() ) {
			do_action( 'icl_wpml_top_menu_added' );

			if ( $this->is_wpml_setup_completed() ) {
				$this->themes_and_plugins_localization();

				if ( ! $this->is_tm_active() ) {
					$this->translation_options();
				}
			}

			$this->taxonomy_translation();

			do_action( 'wpml_core_admin_menus_added' );
		}

		$this->support();

		do_action( 'wpml_core_admin_menus_completed' );
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	private function languages() {
		$menu = new WPML_Admin_Menu_Item();
		$menu->set_order( self::MENU_ORDER_LANGUAGES );
		$menu->set_page_title( __( 'Languages', 'sitepress' ) );
		$menu->set_menu_title( __( 'Languages', 'sitepress' ) );
		$menu->set_capability( 'wpml_manage_languages' );
		$menu->set_menu_slug( $this->languages_menu_slug );
		$this->root->add_item( $menu );
	}

	/**
	 * @return bool
	 */
	private function is_wpml_setup_completed() {
		return $this->sitepress->get_setting( 'existing_content_language_verified' )
		       && 2 <= count( $this->sitepress->get_active_languages() );
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	private function themes_and_plugins_localization() {
		$menu             = new WPML_Admin_Menu_Item();
		$menu->set_order( self::MENU_ORDER_THEMES_AND_PLUGINS_LOCALIZATION );
		$menu->set_page_title( __( 'Theme and plugins localization', 'sitepress' ) );
		$menu->set_menu_title( __( 'Theme and plugins localization', 'sitepress' ) );
		$menu->set_capability( 'wpml_manage_theme_and_plugin_localization' );
		$menu->set_menu_slug( WPML_PLUGIN_FOLDER . '/menu/theme-localization.php' );
		$this->root->add_item( $menu );
	}

	/**
	 * @return bool
	 */
	private function is_tm_active() {
		return $this->sitepress->get_wp_api()->defined( 'WPML_TM_VERSION' );
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	private function translation_options() {
		$menu = new WPML_Admin_Menu_Item();
		$menu->set_order( self::MENU_ORDER_SETTINGS );
		$menu->set_page_title( __( 'Settings', 'sitepress' ) );
		$menu->set_menu_title( __( 'Settings', 'sitepress' ) );
		$menu->set_capability( 'wpml_manage_translation_options' );
		$menu->set_menu_slug( WPML_PLUGIN_FOLDER . '/menu/translation-options.php' );
		$this->root->add_item( $menu );
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	private function taxonomy_translation() {
		$menu = new WPML_Admin_Menu_Item();
		$menu->set_order( self::MENU_ORDER_TAXONOMY_TRANSLATION );
		$menu->set_page_title( __( 'Taxonomy translation', 'sitepress' ) );
		$menu->set_menu_title( __( 'Taxonomy translation', 'sitepress' ) );
		$menu->set_capability( 'wpml_manage_taxonomy_translation' );
		$menu->set_menu_slug( WPML_PLUGIN_FOLDER . '/menu/taxonomy-translation.php' );
		$menu->set_function( array( $this->sitepress, 'taxonomy_translation_page' ) );
		$this->root->add_item( $menu );
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	private function support() {
		$menu_slug = WPML_PLUGIN_FOLDER . '/menu/support.php';

		$menu             = new WPML_Admin_Menu_Item();
		$menu->set_order( self::MENU_ORDER_MAX );
		$menu->set_page_title( __( 'Support', 'sitepress' ) );
		$menu->set_menu_title( __( 'Support', 'sitepress' ) );
		$menu->set_capability( 'wpml_manage_support' );
		$menu->set_menu_slug( $menu_slug );
		$this->root->add_item( $menu );

		$this->troubleshooting_menu( $menu_slug );
		$this->debug_information_menu( $menu_slug );
	}

	/**
	 * @param $parent
	 *
	 * @throws \InvalidArgumentException
	 */
	private function troubleshooting_menu( $parent ) {
		$menu = new WPML_Admin_Menu_Item();
		$menu->set_parent_slug( $parent );
		$menu->set_page_title( __( 'Troubleshooting', 'sitepress' ) );
		$menu->set_menu_title( __( 'Troubleshooting', 'sitepress' ) );
		$menu->set_capability( 'wpml_manage_troubleshooting' );
		$menu->set_menu_slug( WPML_PLUGIN_FOLDER . '/menu/troubleshooting.php' );
		$this->root->add_item( $menu );
	}

	/**
	 * @param $parent
	 *
	 * @throws \InvalidArgumentException
	 */
	private function debug_information_menu( $parent ) {
		$menu = new WPML_Admin_Menu_Item();
		$menu->set_parent_slug( $parent );
		$menu->set_page_title( __( 'Debug information', 'sitepress' ) );
		$menu->set_menu_title( __( 'Debug information', 'sitepress' ) );
		$menu->set_capability( 'wpml_manage_troubleshooting' );
		$menu->set_menu_slug( WPML_PLUGIN_FOLDER . '/menu/debug-information.php' );
		$this->root->add_item( $menu );
	}

}