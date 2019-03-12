<?php

class OTGS_Installer_Plugin_Finder {

	/**
	 * @var array
	 */
	private $plugins = array();
	private $plugin_factory;
	private $repositories;

	/**
	 * @var array
	 */
	private $installed_plugins;

	public function __construct( OTGS_Installer_Plugin_Factory $plugin_factory, array $repositories ) {
		$this->plugin_factory = $plugin_factory;
		$this->repositories   = $repositories;
		$this->load();
	}

	private function load() {
		if ( ! $this->plugins ) {
			foreach ( $this->repositories as $repo_key => $repository ) {
				foreach ( $repository['data']['downloads']['plugins'] as $slug => $plugin ) {
					$plugin_id = $this->get_installed_plugin_id_by_slug( $plugin['slug'] );

					if ( ! $plugin_id ) {
						$plugin_id = $this->get_installed_plugin_id_by_name( $plugin['name'] );
					}

					$this->plugins[] = $this->plugin_factory->create( array(
						'name'              => $plugin['name'],
						'slug'              => $plugin['slug'],
						'description'       => $plugin['description'],
						'changelog'         => $plugin['changelog'],
						'version'           => $plugin['version'],
						'installed_version' => isset( $this->installed_plugins[ $plugin_id ]['Version'] ) ? $this->installed_plugins[ $plugin_id ]['Version'] : null ,
						'date'              => $plugin['date'],
						'url'               => $plugin['url'],
						'free_on_wporg'     => isset( $plugin['free-on-wporg'] ) ? $plugin['free-on-wporg'] : '',
						'fallback_on_wporg' => isset( $plugin['fallback-free-on-wporg'] ) ? $plugin['fallback-free-on-wporg'] : '',
						'basename'          => $plugin['basename'],
						'external_repo'     => isset( $plugin['external-repo'] ) ? $plugin['external-repo'] : '',
						'is_lite'           => isset( $plugin['is-lite'] ) ? $plugin['is-lite'] : '',
						'repo'              => $repo_key,
						'id'                => $plugin_id,
						'channel'           => $plugin['channel'],
					) );
				}
			}
		}
	}

	/**
	 * @return OTGS_Installer_Plugin[]
	 */
	public function get_all() {
		return $this->plugins;
	}

	public function get_otgs_installed_plugins() {
		$installed_plugins = array();

		foreach ( $this->plugins as $plugin ) {
			if ( $plugin->get_installed_version() ) {
				$installed_plugins[] = $plugin;
			}
		}

		return $installed_plugins;
	}

	/**
	 * @param string $slug
	 * @param string $repo
	 *
	 * @return null|OTGS_Installer_Plugin
	 */
	public function get_plugin( $slug, $repo = '' ) {
		foreach ( $this->plugins as $plugin ) {
			if ( $slug === $plugin->get_slug() ) {

				if ( ! $repo || $plugin->get_repo() === $repo ) {
					return $plugin;
				}
			}
		}

		return null;
	}

	/**
	 * @param string $name
	 *
	 * @return null|OTGS_Installer_Plugin
	 */
	public function get_plugin_by_name( $name ) {
		foreach ( $this->plugins as $plugin ) {
			if ( $name === strip_tags( $plugin->get_name() ) ) {
				return $plugin;
			}
		}

		return null;
	}

	/**
	 * @param $slug
	 *
	 * @return null|string
	 */
	private function get_installed_plugin_id_by_slug( $slug ) {
		foreach ( $this->get_installed_plugins() as $plugin_id => $plugin ) {
			$plugin_slug = explode( '/', $plugin_id );

			if ( $slug === $plugin_slug[0] ) {
				return $plugin_id;
			}
		}

		return null;
	}

	private function get_installed_plugins() {
		if ( ! $this->installed_plugins ) {
			$this->installed_plugins = get_plugins();
		}

		return $this->installed_plugins;
	}

	/**
	 * @param string $name
	 *
	 * @return string|null
	 */
	private function get_installed_plugin_id_by_name( $name ) {
		$plugin_id = array_keys( wp_list_filter( $this->get_installed_plugins(), array( 'Name' => $name ) ) );

		return current( $plugin_id );
	}
}