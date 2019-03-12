<?php

class OTGS_Installer_Plugins_Page_Notice {

	const TEMPLATE = 'plugins-page';
	const DISPLAY_SUBSCRIPTION_NOTICE_KEY = 'display_subscription_notice';
	const DISPLAY_SETTING_NOTICE_KEY = 'display_setting_notice';

	private $plugins = array();

	/**
	 * @var OTGS_Installer_Twig_Template_Service
	 */
	private $template_service;

	public function __construct( OTGS_Installer_Twig_Template_Service $template_service ) {
		$this->template_service = $template_service;
	}

	public function add_hooks() {
		foreach ( $this->get_plugins() as $plugin_id => $plugin_data ) {
			add_action( 'after_plugin_row_' . $plugin_id, array(
				$this,
				'show_purchase_notice_under_plugin'
			), 10, 3 );
		}
	}

	/**
	 * @return array
	 */
	public function get_plugins() {
		return $this->plugins;
	}

	public function add_plugin( $plugin_id, $plugin_data ) {
		$this->plugins[ $plugin_id ] = $plugin_data;
	}

	/**
	 * @param string $plugin_file
	 */
	public function show_purchase_notice_under_plugin( $plugin_file ) {
		$should_display_subscription_notice = isset( $this->plugins[ $plugin_file ][ self::DISPLAY_SUBSCRIPTION_NOTICE_KEY ] )
			? $this->plugins[ $plugin_file ][ self::DISPLAY_SUBSCRIPTION_NOTICE_KEY ]
			: false;

		if ( $should_display_subscription_notice ) {
			echo $this->template_service->show( $this->get_model(), self::TEMPLATE );
		}
	}

	/**
	 * @return array
	 */
	private function get_model() {
		$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );

		$tr_classes     = 'plugin-update-tr';
		$notice_classes = 'update-message installer-q-icon';

		if ( version_compare( get_bloginfo( 'version' ), '4.6', '>=' ) ) {
			$tr_classes     = 'plugin-update-tr installer-plugin-update-tr';
			$notice_classes = 'notice inline notice-warning notice-alt';
		}

		if ( is_multisite() ) {
			if ( is_network_admin() ) {
				$menu_url = network_admin_url( 'plugin-install.php?tab=commercial' );
			} else {
				$menu_url = admin_url( 'options-general.php?page=installer' );
			}
		} else {
			$menu_url = admin_url( 'plugin-install.php?tab=commercial' );
		}

		return array(
			'strings'   => array(
				'valid_subscription' => sprintf( __( 'You must have a valid subscription in order to get upgrades or support for this plugin. %sPurchase a subscription or enter an existing site key%s.', 'installer' ),
					'<a href="' . $menu_url . '">', '</a>' ),
			),
			'css'       => array(
				'tr_classes'     => $tr_classes,
				'notice_classes' => $notice_classes,
			),
			'col_count' => $wp_list_table->get_column_count(),
		);
	}
}