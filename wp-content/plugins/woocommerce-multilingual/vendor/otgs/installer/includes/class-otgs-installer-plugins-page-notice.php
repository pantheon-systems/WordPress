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

	private $plugin_finder;

	public function __construct( OTGS_Installer_Twig_Template_Service $template_service, OTGS_Installer_Plugin_Finder $plugin_finder ) {
		$this->template_service = $template_service;
		$this->plugin_finder = $plugin_finder;
	}

	public function add_hooks() {
		foreach ( $this->get_plugins() as $plugin_id => $plugin_data ) {
			add_action( 'after_plugin_row_' . $plugin_id, array(
				$this,
				'show_purchase_notice_under_plugin'
			), 10, 2 );
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
	public function show_purchase_notice_under_plugin( $plugin_file, $plugin_data ) {
		$should_display_subscription_notice = isset( $this->plugins[ $plugin_file ][ self::DISPLAY_SUBSCRIPTION_NOTICE_KEY ] )
			? $this->plugins[ $plugin_file ][ self::DISPLAY_SUBSCRIPTION_NOTICE_KEY ]
			: false;

		$plugin = $this->plugin_finder->get_plugin_by_name( $plugin_data['Name'] );

		if ( $should_display_subscription_notice ) {
			if ( $plugin && 'toolset' === $plugin->get_external_repo() && $plugin->is_lite() ) {
				echo $this->template_service->show( $this->get_toolset_lite_notice_model( $plugin->get_name() ), self::TEMPLATE );
			} else {
				echo $this->template_service->show( $this->get_model(), self::TEMPLATE );
			}
		}
	}

	/**
	 * @return array
	 */
	private function get_model() {
		$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );

		list( $tr_classes, $notice_classes ) = $this->get_classes();

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

	private function get_toolset_lite_notice_model( $plugin_name ) {
		$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );

		list( $tr_classes, $notice_classes ) = $this->get_classes();

		return array(
			'strings'   => array(
				'valid_subscription' => sprintf( __( 'You are using the complementary %1$s. For the %2$s, %3$s.', 'installer' ),
					$plugin_name, '<a href="https://wpml.org/documentation/developing-custom-multilingual-sites/types-and-views-lite/?utm_source=viewsplugin&utm_campaign=wpml-toolset-lite&utm_medium=plugins-page&utm_term=features-link">' . __( 'complete set of features', 'installer' ) . '</a>', '<a href="https://toolset.com/?add-to-cart=631305&buy_now=1&apply_coupon=eyJjb3Vwb25fbmFtZSI6IndwbWwgY291cG9uIGJhc2ljIiwiY291cG9uX2lkIjoiODAyMDE2In0=">' . __( 'upgrade to Toolset', 'installer' ) . '</a>' ),
			),
			'css'       => array(
				'tr_classes'     => $tr_classes,
				'notice_classes' => $notice_classes,
			),
			'col_count' => $wp_list_table->get_column_count(),
		);
	}

	private function get_classes() {
		$tr_classes     = 'plugin-update-tr';
		$notice_classes = 'update-message installer-q-icon';

		if ( version_compare( get_bloginfo( 'version' ), '4.6', '>=' ) ) {
			$tr_classes     = 'plugin-update-tr installer-plugin-update-tr';
			$notice_classes = 'notice inline notice-warning notice-alt';
		}

		return array( $tr_classes, $notice_classes );
	}
}