<?php

class OTGS_Installer_Factory {

	private $installer;
	private $filename_hooks;
	private $icons;
	private $installer_php_functions;
	private $local_components_ajax_setting;
	private $settings;
	private $template_service_loader;
	private $wp_components_hooks;
	private $wp_components_sender;
	private $wp_components_storage;
	private $upgrade_response;
	private $plugin_finder;
	private $plugin_factory;
	private $repositories;

	public function __construct( WP_Installer $installer ) {
		$this->installer = $installer;
	}

	/**
	 * @return OTGS_Installer_Filename_Hooks
	 */
	public function create_filename_hooks() {
		if ( ! $this->filename_hooks ) {
			$this->filename_hooks = new OTGS_Installer_Filename_Hooks( $this->create_installer_php_functions() );
		}

		return $this->filename_hooks;
	}

	public function load_filename_hooks() {
		$filename_hooks = $this->create_filename_hooks();
		$filename_hooks->add_hooks();

		return $this;
	}

	/**
	 * @return OTGS_Installer_Icons
	 */
	public function create_icons() {
		if ( ! $this->icons ) {
			$this->icons = new OTGS_Installer_Icons( $this->get_installer() );
		}

		return $this->icons;
	}

	public function load_icons() {
		$icons = $this->create_icons();
		$icons->add_hooks();

		return $this;
	}

	/**
	 * @return OTGS_Installer_WP_Components_Setting_Ajax
	 */
	public function create_local_components_ajax_setting() {
		if ( ! $this->local_components_ajax_setting ) {
			$this->local_components_ajax_setting = new OTGS_Installer_WP_Components_Setting_Ajax( $this->create_settings(),
				$this->get_installer() );
		}

		return $this->local_components_ajax_setting;
	}

	public function load_local_components_ajax_settings() {
		$settings = $this->create_local_components_ajax_setting();
		$settings->add_hooks();

		return $this;
	}

	/**
	 * @return OTGS_Installer_WP_Components_Setting_Resources
	 */
	public function create_resources() {
		return new OTGS_Installer_WP_Components_Setting_Resources( $this->get_installer() );
	}

	public function load_resources() {
		$resources = $this->create_resources();
		$resources->add_hooks();

		return $this;
	}

	/**
	 * @return OTGS_Installer_WP_Share_Local_Components_Setting_Hooks
	 */
	public function create_settings_hooks() {
		return new OTGS_Installer_WP_Share_Local_Components_Setting_Hooks( $this->create_template_service_loader()
		                                                                        ->get_service(),
			$this->create_settings() );
	}

	public function load_settings_hooks() {
		$settings_hooks = $this->create_settings_hooks();
		$settings_hooks->add_hooks();

		return $this;
	}

	/**
	 * @return OTGS_Installer_Twig_Template_Service_Loader
	 */
	private function create_template_service_loader() {
		if ( ! $this->template_service_loader ) {
			$this->template_service_loader = new OTGS_Installer_Twig_Template_Service_Loader( array(
				$this->get_installer()
				     ->plugin_path()
				. '/templates/components-setting/'
			) );
		}

		return $this->template_service_loader;
	}

	/**
	 * @return OTGS_Installer_WP_Share_Local_Components_Setting
	 */
	private function create_settings() {
		if ( ! $this->settings ) {
			$this->settings = new OTGS_Installer_WP_Share_Local_Components_Setting();
		}

		return $this->settings;
	}

	/**
	 * @return OTGS_Installer_WP_Components_Hooks
	 */
	public function create_wp_components_hooks() {
		if ( ! $this->wp_components_hooks ) {
			$this->wp_components_hooks = new OTGS_Installer_WP_Components_Hooks( $this->create_wp_components_storage(),
				$this->create_wp_components_sender(),
				$this->create_settings(),
				$this->create_installer_php_functions() );
		}

		return $this->wp_components_hooks;
	}

	public function load_wp_components_hooks() {
		$wp_components_hooks = $this->create_wp_components_hooks();
		$wp_components_hooks->add_hooks();

		return $this;
	}

	/**
	 * @return OTGS_Installer_WP_Components_Storage
	 */
	public function create_wp_components_storage() {
		if ( ! $this->wp_components_storage ) {
			$this->wp_components_storage = new OTGS_Installer_WP_Components_Storage();
		}

		return $this->wp_components_storage;
	}

	/**
	 * @return OTGS_Installer_WP_Components_Sender
	 */
	public function create_wp_components_sender() {
		if ( ! $this->wp_components_sender ) {
			$this->wp_components_sender = new OTGS_Installer_WP_Components_Sender( $this->get_installer(),
				$this->create_settings() );
		}

		return $this->wp_components_sender;
	}

	/**
	 * @return OTGS_Installer_PHP_Functions
	 */
	public function create_installer_php_functions() {
		if ( ! $this->installer_php_functions ) {
			$this->installer_php_functions = new OTGS_Installer_PHP_Functions();
		}

		return $this->installer_php_functions;
	}

	/**
	 * @return OTGS_Installer_Debug_Info
	 */
	public function create_debug_info_hook() {
		return new OTGS_Installer_Debug_Info( $this->get_installer() );
	}

	public function load_debug_info_hooks() {
		$debug_info = $this->create_debug_info_hook();
		$debug_info->add_hooks();

		return $this;
	}

	/**
	 * @return OTGS_Installer_Plugin_Factory
	 */
	public function get_plugin_factory() {
		if ( ! $this->plugin_factory ) {
			$this->plugin_factory = new OTGS_Installer_Plugin_Factory();
		}

		return $this->plugin_factory;
	}

	/**
	 * @return OTGS_Installer_Plugin_Finder
	 */
	public function get_plugin_finder() {
		if ( ! $this->plugin_finder ) {
			$settings            = $this->get_installer()->get_settings();
			$this->plugin_finder = new OTGS_Installer_Plugin_Finder( $this->get_plugin_factory(), $settings['repositories'] );
		}

		return $this->plugin_finder;
	}

	/**
	 * @return OTGS_Installer_Upgrade_Response
	 */
	public function create_upgrade_response() {
		if ( ! $this->upgrade_response ) {
			$this->upgrade_response = new OTGS_Installer_Upgrade_Response(
				$this->get_plugin_finder()->get_all(),
				$this->get_repositories(),
				new OTGS_Installer_Source_Factory(),
				new OTGS_Installer_Package_Product_Finder()
			);
		}

		return $this->upgrade_response;
	}

	public function load_upgrade_response() {
		$upgrade_response = $this->create_upgrade_response();
		$upgrade_response->add_hooks();

		return $this;
	}

	/**
	 * @return OTGS_Installer_Site_Key_Ajax
	 */
	public function create_site_key_ajax_handler() {
		$logger = new OTGS_Installer_Logger(
			$this->installer,
			new OTGS_Installer_Logger_Storage( new OTGS_Installer_Log_Factory() )
		);

		$fetch_subscription = new OTGS_Installer_Fetch_Subscription(
			new OTGS_Installer_Source_Factory(),
			$this->get_plugin_finder(),
			$this->get_repositories(),
			$logger,
			new OTGS_Installer_Log_Factory()
		);

		return new OTGS_Installer_Site_Key_Ajax(
			$fetch_subscription,
			$logger,
			$this->get_repositories(),
			new OTGS_Installer_Subscription_Factory()
		);
	}

	public function load_site_key_ajax_handler() {
		$site_key_ajax_handler = $this->create_site_key_ajax_handler();
		$site_key_ajax_handler->add_hooks();

		return $this;
	}

	public function load_installer_support_hooks() {
		$support_hooks = new OTGS_Installer_Support_Hooks( new OTGS_Installer_Support_Template_Factory( $this->get_installer()->plugin_path() ) );
		$support_hooks->add_hooks();

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$support_ajax = new OTGS_Installer_Connection_Test_Ajax(
				new OTGS_Installer_Connection_Test(
					$this->get_repositories(),
					$this->create_upgrade_response(),
					new OTGS_Installer_Logger_Storage( new OTGS_Installer_Log_Factory() ),
					new OTGS_Installer_Log_Factory()
				)
			);

			$support_ajax->add_hooks();
		}

		return $this;
	}

	public function load_translation_service_info_hooks() {
		$translation_services = new Translation_Service_Info();
		$translation_services->add_hooks();

		return $this;
	}
	/**
	 * @return OTGS_Installer_Repositories
	 */
	private function get_repositories() {
		if ( ! $this->repositories ) {
			$repositories_factory = new OTGS_Installer_Repositories_Factory( $this->get_installer() );
			$this->repositories = $repositories_factory->create( $this->installer );
		}

		return $this->repositories;
	}


	/**
	 * @return $this
	 */
	public function load_plugins_update_cache_cleaner() {
		$plugins_update_cache_cleaner = new OTGS_Installer_Plugins_Update_Cache_Cleaner();
		$plugins_update_cache_cleaner->add_hooks();

		return $this;
	}

	public function load_buy_url_hooks() {
		$buy_url = new OTGS_Installer_Buy_URL_Hooks( $this->installer->get_embedded_at() );
		$buy_url->add_hooks();

		return $this;
	}

	/**
	 * @return WP_Installer
	 */
	private function get_installer() {
		return $this->installer;
	}
}