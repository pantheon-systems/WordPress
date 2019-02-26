<?php

class OTGS_Installer_Plugin_Finder {

	private $plugins;
	private $plugin_factory;
	private $repositories;

	public function __construct( OTGS_Installer_Plugin_Factory $plugin_factory, array $repositories ) {
		$this->plugin_factory = $plugin_factory;
		$this->repositories   = $repositories;
		$this->load();
	}

	private function load() {
		if ( ! $this->plugins ) {
			foreach ( $this->repositories as $repo_key => $repository ) {
				foreach ( $repository['data']['downloads']['plugins'] as $slug => $plugin ) {
					$this->plugins[ $repo_key ][ $slug ] = $this->plugin_factory->create( array(
						'name'              => $plugin['name'],
						'slug'              => $plugin['slug'],
						'description'       => $plugin['description'],
						'changelog'         => $plugin['changelog'],
						'version'           => $plugin['version'],
						'date'              => $plugin['date'],
						'url'               => $plugin['url'],
						'free_on_wporg'     => isset( $plugin['free-on-wporg'] ) ? $plugin['free-on-wporg'] : '',
						'fallback_on_wporg' => isset( $plugin['fallback-free-on-wporg'] ) ? $plugin['fallback-free-on-wporg'] : '',
						'basename'          => $plugin['basename'],
						'external_repo'     => isset( $plugin['external-repo'] ) ? $plugin['external-repo'] : '',
						'is_lite'           => isset( $plugin['is-lite'] ) ? $plugin['is-lite'] : '',
						'repo'              => $repo_key,
					) );
				}
			}
		}
	}

	public function get_plugin( $slug, $repo ) {
		foreach ( $this->plugins[ $repo ] as $plugin ) {
			if ( $slug === $plugin->get_slug() ) {
				return $plugin;
			}
		}
		return null;
	}

	public function get_plugin_by_name( $name ) {
		foreach ( $this->plugins as $repo ) {
			foreach ( $repo as $plugin ) {
				if ( $name === strip_tags( $plugin->get_name() ) ) {
					return $plugin;
				}
			}
		}
		return null;
	}
}