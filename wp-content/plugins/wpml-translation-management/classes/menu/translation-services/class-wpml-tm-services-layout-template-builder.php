<?php

class WPML_TM_Services_Layout_Template_Builder {

	/** @var IWPML_Template_Service */
	private $template_service;

	/** @var WPML_TM_Services_List_Template_Builder */
	private $list_template_builder;

	/** @var WPML_TP_Service[] */
	private $partner_services;

	/** @var WPML_TP_Service[] */
	private $other_services;

	/** @var WPML_TP_Service[] */
	private $management_services;

	/** @var WPML_TM_Translation_Services_Admin_Active_Template */
	private $active_service_template;

	public function __construct(
		IWPML_Template_Service $template_service,
		WPML_TM_Services_List_Template_Builder $list_template_builder,
		WPML_TM_Translation_Services_Admin_Active_Template $active_template
	) {
		$this->template_service        = $template_service;
		$this->active_service_template = $active_template;
		$this->list_template_builder   = $list_template_builder;

		$this->partner_services    = array();
		$this->other_services      = array();
		$this->management_services = array();
	}

	/**
	 * @param WPML_TP_Service[] $partner_services
	 * @return self
	 */
	public function set_partner_services( $partner_services ) {
		$this->partner_services = $partner_services;

		return $this;
	}

	/**
	 * @param WPML_TP_Service[] $other_services
	 * @return self
	 */
	public function set_other_services( $other_services ) {
		$this->other_services = $other_services;

		return $this;
	}

	/**
	 * @param WPML_TP_Service[] $management_services
	 * @return self
	 */
	public function set_management_services( $management_services ) {
		$this->management_services = $management_services;

		return $this;
	}

	/**
	 * @return WPML_TM_Translation_Services_Admin_Section_Services_Layout_Template
	 */
	public function build() {
		$services = array();
		if ( ! empty( $this->partner_services ) ) {
			$services[] = $this->list_template_builder->set_services( $this->partner_services )
			                                          ->set_header( __( 'Partner Translation Services',
				                                          'wpml-translation-management' ) )
			                                          ->set_param_prefix( 'partner_' )
			                                          ->set_show_popularity( true )
			                                          ->build();
		}
		if ( ! empty( $this->other_services ) ) {
			$services[] = $this->list_template_builder->set_services( $this->other_services )
			                                          ->set_header( __( 'Other Translation Services',
				                                          'wpml-translation-management' ) )
			                                          ->set_param_prefix( 'other_' )
			                                          ->set_show_popularity( false )
			                                          ->build();
		}
		if ( ! empty( $this->management_services ) ) {
			$services[] = $this->list_template_builder->set_services( $this->management_services )
			                                          ->set_header( __( 'Translation Management System',
				                                          'wpml-translation-management' ) )
			                                          ->set_param_prefix( 'management_' )
			                                          ->set_show_popularity( false )
			                                          ->build();
		}

		return new WPML_TM_Translation_Services_Admin_Section_Services_Layout_Template (
			$this->template_service,
			$this->active_service_template,
			TranslationProxy::has_preferred_translation_service(),
			$services
		);
	}

}