<?php

class OTGS_Installer_Support_Template {

	const TEMPLATE_FILE = 'installer-support';
	const SUPPORT_LINK = 'support-link';

	private $template_service;
	private $logger_storage;
	private $requirements;

	/**
	 * @var OTGS_Installer_Instance[]
	 */
	private $instances;

	public function __construct(
		OTGS_Installer_Twig_Template_Service $template_service,
		OTGS_Installer_Logger_Storage $logger_storage,
		OTGS_Installer_Requirements $requirements,
		OTGS_Installer_Instances $instances
	) {
		$this->template_service = $template_service;
		$this->logger_storage   = $logger_storage;
		$this->requirements     = $requirements;
		$this->instances        = $instances;
	}

	public function show() {
		echo $this->template_service->show( $this->get_model(), self::TEMPLATE_FILE );
	}

	public function render_support_link( $args = array() ) {
		echo $this->template_service->show( $this->get_support_link_model( $args ), self::SUPPORT_LINK );
	}

	private function get_support_link_model( $args = array() ) {
		return array(
			'title' => __( 'Installer Support', 'installer' ),
			'content' => __( 'For retrieving Installer debug information use the %s page.', 'installer' ),
			'link' => array(
				'url' => admin_url( 'admin.php?page=otgs-installer-support' ),
				'text' => __( 'Installer Support', 'installer' ),
			),
			'hide_title' => isset( $args['hide_title'] ) && $args['hide_title'],
		);
	}

	/**
	 * @return array
	 */
	private function get_model() {
		$model = array(
			'log_entries'  => $this->get_log_entries(),
			'strings'      => array(
				'page_title'   => __( 'Installer Support', 'installer' ),
				'log'          => array(
					'title'             => __( 'Installer Log', 'installer' ),
					'request_url'       => __( 'Request URL', 'installer' ),
					'request_arguments' => __( 'Request Arguments', 'installer' ),
					'response'          => __( 'Response', 'installer' ),
					'component'         => __( 'Component', 'installer' ),
					'time'              => __( 'Time', 'installer' ),
					'empty_log'         => __( 'Log is empty', 'installer' ),
				),
				'tester'       => array(
					'title'        => __( 'Installer System Status', 'installer' ),
					'button_label' => __( 'Check Now', 'installer' ),
				),
				'requirements' => array(
					'title' => __( 'Required PHP Libraries', 'installer' ),
				),
				'instances' => array(
					'title' => __( 'All Installer Instances', 'installer' ),
					'path' => __( 'Path', 'installer' ),
					'version' => __( 'Version', 'installer' ),
					'high_priority' => __( 'High priority', 'installer' ),
					'delegated' => __( 'Delegated', 'installer' ),
				),
			),
			'tester'       => array(
				'endpoints' => array(
					array(
						'repository'  => 'wpml',
						'type'        => 'api',
						'description' => __( 'WPML API server', 'installer' )
					),
					array(
						'repository'  => 'toolset',
						'type'        => 'api',
						'description' => __( 'Toolset API server', 'installer' )
					),
					array(
						'repository'  => 'wpml',
						'type'        => 'products',
						'description' => __( 'WPML remote products file', 'installer' )
					),
					array(
						'repository'  => 'toolset',
						'type'        => 'products',
						'description' => __( 'Toolset remote products file', 'installer' )
					),
				),
				'nonce'     => wp_nonce_field( OTGS_Installer_Connection_Test_Ajax::ACTION, OTGS_Installer_Connection_Test_Ajax::ACTION, false ),
			),
			'requirements' => $this->requirements->get(),
			'instances' => $this->instances->get(),
		);

		return $model;
	}

	/**
	 * @return array
	 */
	private function get_log_entries() {
		$log_entries = array();

		foreach ( $this->logger_storage->get() as $log ) {
			$log_entries[] = array(
				'request_url'       => $log->get_request_url(),
				'request_arguments' => $log->get_request_args(),
				'response'          => $log->get_response(),
				'component'         => $log->get_component(),
				'time'              => $log->get_time(),
			);
		}

		return $log_entries;
	}
}