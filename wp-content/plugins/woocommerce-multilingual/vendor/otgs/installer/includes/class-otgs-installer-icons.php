<?php

class OTGS_Installer_Icons {

	/**
	 * @var WP_Installer
	 */
	private $installer;

	public function __construct( WP_Installer $installer ) {
		$this->installer = $installer;
	}

	public function add_hooks() {
		add_filter( 'otgs_installer_upgrade_check_response', array( $this, 'add_icons_on_response' ), 10, 2 );
	}

	/**
	 * @param stdClass $response
	 * @param string $name
	 *
	 * @return stdClass
	 */
	public function add_icons_on_response( $response, $name ) {
		$repositories = array_keys( $this->installer->get_repositories() );
		$product = '';
		$repository = '';

		foreach( $repositories as $repository_id ) {
			if ( isset( $this->installer->settings['repositories'][ $repository_id ]['data']['products-map'][ $response->plugin ] ) ) {
				$product = $this->installer->settings['repositories'][ $repository_id ]['data']['products-map'][ $response->plugin ];
				$repository = $repository_id;
				break;
			} elseif ( isset( $this->installer->settings['repositories'][ $repository_id ]['data']['products-map'][ $name ] ) ) {
				$product = $this->installer->settings['repositories'][ $repository_id ]['data']['products-map'][ $name ];
				$repository = $repository_id;
				break;
			}
		}

		if ( $product && $repository ) {
			$base            = $this->installer->plugin_url() . '/../icons/plugin-icons/' . $repository . '/' . $product . '/icon';
			$response->icons = array(
				'svg' => $base . '.svg',
				'1x'  => $base . '-128x128.png',
				'2x'  => $base . '-256x256.png',
			);
		}

		return $response;
	}
}