<?php

class OTGS_Installer_WP_Components_Sender {

	/**
	 * @var WP_Installer
	 */
	private $installer;

	/**
	 * @var OTGS_Installer_WP_Share_Local_Components_Setting
	 */
	private $settings;

	public function __construct( WP_Installer $installer, OTGS_Installer_WP_Share_Local_Components_Setting $settings ) {
		$this->installer = $installer;
		$this->settings = $settings;
	}

	public function send( array $components, $force = false ) {

		if ( ! $this->installer->get_repositories() ) {
			$this->installer->load_repositories_list();
		}

		if ( ! $this->installer->get_settings() ) {
			$this->installer->save_settings();
		}

		foreach ( $this->installer->get_repositories() as $key => $repository ) {
			$site_key = $this->installer->get_site_key( $key );
			if ( $site_key && $this->settings->is_repo_allowed( $key ) ) {
				wp_remote_post(
					$repository['api-url'] . '?action=update_site_components',
					apply_filters( 'installer_fetch_components_data_request', array(
						'body' => array(
							'action'     => 'update_site_components',
							'site_key'   => $site_key,
							'site_url'   => get_site_url(),
							'components' => $components,
							'phpversion' => phpversion(),
							'force'      => $force,
						),
					) )
				);
			}
		}
	}
}