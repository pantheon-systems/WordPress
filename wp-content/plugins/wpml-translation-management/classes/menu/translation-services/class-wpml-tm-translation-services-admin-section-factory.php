<?php

class WPML_TM_Translation_Services_Admin_Section_Factory implements IWPML_TM_Admin_Section_Factory {

	const OPTION_ITEMS_PER_PAGE = 'wpml_tm_services_per_page';
	const DEFAULT_ITEMS_PER_PAGE = 10;

	/** @var SitePress */
	private $sitepress;

	/** @var  WPML_Twig_Template_Loader */
	private $twig_loader;

	/** @var WP_Installer */
	private $wp_installer;

	/** @var  WPML_TP_API_Services */
	private $tp_api_services;

	/** @var WPML_TM_Translation_Services_Admin_Active_Template_Factory */
	private $active_template_factory;

	/** @var WPML_TM_Services_Layout_Template_Builder */
	private $layout_template_builder;

	public function __construct(
		SitePress $sitepress = null,
		WPML_Twig_Template_Loader $twig_loader = null,
		$wp_installer = null,
		WPML_TP_API_Services $tp_api_services = null,
		WPML_TM_Translation_Services_Admin_Active_Template_Factory $active_template_factory = null,
		WPML_TM_Services_Layout_Template_Builder $layout_template_builder = null
	) {
		if ( ! $sitepress ) {
			global $sitepress;
		}
		$this->sitepress = $sitepress;

		$this->twig_loader             = $twig_loader;
		$this->tp_api_services         = $tp_api_services;
		$this->active_template_factory = $active_template_factory;
		$this->wp_installer            = $wp_installer;
		$this->layout_template_builder = $layout_template_builder;
	}


	/**
	 * @return WPML_TM_Translation_Services_Admin_Section
	 */
	public function create() {
		return new WPML_TM_Translation_Services_Admin_Section(
			$this->sitepress,
			$this->site_key_exists() ?
				$this->create_services_list_template() :
				new WPML_TM_Translation_Services_Admin_Section_No_Site_Key_Template( $this->get_twig_loader()->get_template() )
		);
	}

	/**
	 * @return bool|string
	 */
	private function site_key_exists() {
		$site_key = false;

		if ( class_exists( 'WP_Installer' ) ) {
			$repository_id = 'wpml';
			$site_key = $this->get_wp_installer()->get_site_key( $repository_id );
		}

		return $site_key;
	}

	/**
	 * @param WPML_Twig_Template_Loader $twig_loader
	 * @param WPML_TP_Client $tp_client
	 *
	 * @return WPML_TM_Translation_Services_Admin_Section_Services_Layout_Template
	 */
	private function create_services_list_template() {
		return $this->get_layout_template_builder()
		            ->set_partner_services( $this->get_tp_api_services()->get_translation_services( true ) )
		            ->set_other_services( $this->get_tp_api_services()->get_translation_services( false ) )
		            ->set_management_services( $this->get_tp_api_services()->get_translation_management_systems() )
		            ->build();
	}

	/**
	 * @return WPML_Twig_Template_Loader
	 */
	private function get_twig_loader() {
		if ( ! $this->twig_loader ) {
			$this->twig_loader = new WPML_Twig_Template_Loader( array(
				WPML_TM_PATH . '/templates/menus/translation-services/',
				WPML_PLUGIN_PATH . '/templates/pagination/',
			) );
		}

		return $this->twig_loader;
	}

	/**
	 * @return WP_Installer
	 */
	private function get_wp_installer() {
		if(!$this->wp_installer) {
			$this->wp_installer = WP_Installer();
		}

		return $this->wp_installer;
	}

	/**
	 * @return WPML_TP_API_Services
	 */
	private function get_tp_api_services() {
		if ( ! $this->tp_api_services ) {
			$tp_client_factory     = new WPML_TP_Client_Factory();
			$this->tp_api_services = $tp_client_factory->create()->services();
		}

		return $this->tp_api_services;
	}

	/**
	 * @return WPML_TM_Translation_Services_Admin_Active_Template_Factory
	 */
	private function get_active_template_factory() {
		if ( ! $this->active_template_factory ) {
			$this->active_template_factory = new WPML_TM_Translation_Services_Admin_Active_Template_Factory();
		}

		return $this->active_template_factory;
	}

	/**
	 * @return WPML_TM_Services_Layout_Template_Builder
	 */
	private function get_layout_template_builder() {
		if ( ! $this->layout_template_builder ) {
			$active_service_template = $this->get_active_template_factory()->create();

			$list_template_builder = new WPML_TM_Services_List_Template_Builder(
				$this->get_twig_loader()->get_template(),
				$this->create_pagination_factory(),
				new WPML_TM_Translation_Services_Admin_Section_Services_List_Model_Mapper( $active_service_template )
			);

			$this->layout_template_builder = new WPML_TM_Services_Layout_Template_Builder(
				$this->get_twig_loader()->get_template(),
				$list_template_builder,
				$active_service_template
			);
		}

		return $this->layout_template_builder;
	}

	private function create_pagination_factory() {
		return new WPML_Admin_Pagination_Factory( 10 );
	}
}