<?php

class OTGS_Installer_WP_Share_Local_Components_Setting_Hooks {

	const TEMPLATE_CHECKBOX = 'share-local-data-setting';
	const TEMPLATE_RADIO    = 'share-local-data-setting-radio';

	/**
	 * @var OTGS_Installer_Twig_Template_Service
	 */
	private $template_service;

	/**
	 * @var OTGS_Installer_WP_Share_Local_Components_Setting
	 */
	private $setting;

	public function __construct(
		OTGS_Installer_Twig_Template_Service $template_service,
		OTGS_Installer_WP_Share_Local_Components_Setting $setting
	) {
		$this->template_service = $template_service;
		$this->setting          = $setting;
	}

	public function add_hooks() {
		add_action( 'otgs_installer_render_local_components_setting',
		            array(
			            $this,
			            'render_local_components_setting',
		            ),
		            10,
		            5 );
		add_filter( 'otgs_installer_has_local_components_setting',
		            array( $this, 'has_local_components_setting_filter' ),
		            10,
		            2 );
		add_filter( 'otgs_installer_repository_subscription_status',
			array( $this, 'get_installer_repository_subscription_status' ),
			10,
			2 );
	}

	/**
	 * @param array $args
	 *
	 * @throws \InvalidArgumentException
	 */
	public function render_local_components_setting( array $args ) {
		$params = $this->validate_arguments( $args );

		$template = self::TEMPLATE_CHECKBOX;
		if ( (bool) $params['use_radio'] ) {
			$template = self::TEMPLATE_RADIO;
		}

		if ( (bool) $params['use_styles'] ) {
			wp_enqueue_style( OTGS_Installer_WP_Components_Setting_Resources::HANDLES_OTGS_INSTALLER_UI );

			if(!(bool) $params['use_radio']) {
				wp_enqueue_style('otgsSwitcher');
			}
		}

		echo $this->template_service->show( $this->get_model( $params ), $template );
	}

	/**
	 * @param $ignore
	 * @param string $repo (wpml|toolset)
	 *
	 * @return bool
	 */
	public function has_local_components_setting_filter( $ignore, $repo ) {
		return $this->setting->has_setting( $repo );
	}

	public function get_installer_repository_subscription_status( $ignore, $repo ) {
		$subscription = WP_Installer()->get_subscription( $repo );

		return $subscription->get_subscription_status_text();
	}

	private function get_model( $params ) {
		$plugin_name                = $params['plugin_name'];
		$plugin_uri                 = $params['plugin_uri'];
		$plugin_site                = $params['plugin_site'];
		$custom_heading             = $params['custom_heading'];
		$custom_label               = $params['custom_label'];
		$privacy_policy_url         = $params['privacy_policy_url'];
		$privacy_policy_text        = $params['privacy_policy_text'];
		$custom_privacy_policy_text = $params['custom_privacy_policy_text'];
		$repo                       = isset( $params['plugin_repository'] ) ? $params['plugin_repository'] : strtolower( $plugin_name );

		return array(
			'strings'                    => array(
				'heading'                 => __( 'Reporting to', 'installer' ),
				'report_to'               => __( 'Report to', 'installer' ),
				'radio_report_yes'        => __( 'Send theme and plugins information, in order to get faster support and compatibility alerts',
				                                 'installer' ),
				'radio_report_no'         => __( "Don't send this information and skip compatibility alerts",
				                                 'installer' ),
				'which_theme_and_plugins' => __( 'which theme and plugins you are using.', 'installer' ),
			),
			'custom_raw_heading'         => $custom_heading,
			'custom_raw_label'           => $custom_label,
			'custom_privacy_policy_text' => $custom_privacy_policy_text,
			'privacy_policy_url'         => $privacy_policy_url,
			'privacy_policy_text' => $privacy_policy_text,
			'component_name'      => $plugin_name,
			'company_url'         => $plugin_uri,
			'company_site'        => $plugin_site,
			'nonce'               => array(
				'action' => OTGS_Installer_WP_Components_Setting_Ajax::AJAX_ACTION,
				'value'  => wp_create_nonce( OTGS_Installer_WP_Components_Setting_Ajax::AJAX_ACTION ),
			),
			'repo'                => $repo,
			'is_repo_allowed'     => $this->setting->is_repo_allowed( $repo ),
			'has_setting'         => (int) $this->setting->has_setting( $repo ),
		);
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	private function validate_arguments( array $args ) {
		if ( ! $args ) {
			throw new InvalidArgumentException( 'Arguments are missing' );
		}

		$defaults = array(
			'custom_heading'             => null,
			'custom_label'               => null,
			'custom_radio_label_yes'     => null,
			'custom_radio_label_no'      => null,
			'custom_privacy_policy_text' => null,
			'use_styles'                 => false,
			'use_radio'                  => false,
			'privacy_policy_text'        => __( 'Privacy and data usage policy', 'installer' ),
			'plugin_site'                => null,
			'plugin_uri'                 => null,
		);

		$required_arguments = array( 'plugin_name', 'privacy_policy_url' );

		if ( ! $this->must_use_radios( $args ) ) {
			$required_arguments = array( 'plugin_uri', 'plugin_site' );
		}

		foreach ( $required_arguments as $required_argument ) {
			if ( ! $this->has_required_argument( $args, $required_argument ) ) {
				throw new InvalidArgumentException( $required_argument . ' is missing' );
			}
		}

		return array_merge( $defaults, $args );
	}

	/**
	 * @param array $args
	 *
	 * @return bool
	 */
	private function must_use_radios( array $args ) {
		return array_key_exists( 'use_radio', $args ) && $args['use_radio'];
	}

	/**
	 * @param array  $args
	 * @param string $required_argument
	 *
	 * @return bool
	 */
	private function has_required_argument( array $args, $required_argument ) {
		return array_key_exists( $required_argument, $args ) && $args[ $required_argument ];
	}
}
