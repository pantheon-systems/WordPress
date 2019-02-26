<?php

/**
 * @author OnTheGo Systems
 */
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

	/**
	 * @return OTGS_Installer_Icons
	 */
	public function create_icons() {
		if ( ! $this->icons ) {
			$this->icons = new OTGS_Installer_Icons( $this->get_installer() );
		}

		return $this->icons;
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

	public function create_resources() {
		return new OTGS_Installer_WP_Components_Setting_Resources( $this->get_installer() );
	}

	public function create_settings_hooks() {
		return new OTGS_Installer_WP_Share_Local_Components_Setting_Hooks( $this->create_template_service_loader()
		                                                                        ->get_service(),
		                                                                   $this->create_settings() );
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
	public function create_settings() {
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
	 * @return WP_Installer
	 */
	private function get_installer() {
		return $this->installer;
	}
}