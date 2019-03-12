<?php

class OTGS_Installer_Upgrade_Response {

	/**
	 * @var array
	 */
	private $plugins;

	/**
	 * @var array
	 */
	private $repositories;

	/**
	 * @var OTGS_Installer_Source_Factory
	 */
	private $source_factory;

	/**
	 * @var OTGS_Installer_Package_Product_Finder
	 */
	private $product_finder;

	public function __construct( array $plugins, OTGS_Installer_Repositories $repositories, OTGS_Installer_Source_Factory $source_factory, OTGS_Installer_Package_Product_Finder $product_finder ) {
		$this->plugins        = $plugins;
		$this->repositories   = $repositories;
		$this->source_factory = $source_factory;
		$this->product_finder = $product_finder;
	}

	public function add_hooks() {
		if ( defined( 'DOING_AJAX' ) && isset( $_POST['action'] ) && 'installer_download_plugin' === $_POST['action'] ) {
			add_filter( 'site_transient_update_plugins', array( $this, 'modify_upgrade_response' ) );
		}

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'modify_upgrade_response' ) );
	}

	public function modify_upgrade_response( $update_plugins ) {

		foreach ( $this->plugins as $plugin ) {
			$repository   = $this->repositories->get( $plugin->get_repo() );
			$subscription = $repository->get_subscription();

			if ( $this->should_skip_upgrade_process( $plugin, $update_plugins, $repository ) ) {
				continue;
			}

			$response                 = new stdClass();
			$response->id             = 0;
			$response->slug           = $plugin->get_slug();
			$response->plugin         = $plugin->get_id();
			$response->new_version    = $plugin->get_version();
			$response->upgrade_notice = '';
			$response->url            = $plugin->get_url();

			if ( $subscription->get_site_key() ) {
				$response->package = $this->append_site_key_to_download_url( $plugin->get_url(), $subscription->get_site_key(), $repository->get_id(), $subscription->get_site_url() );
			}

			$response = apply_filters( 'otgs_installer_upgrade_check_response', $response, $plugin->get_name(), $repository->get_id() );

			$update_plugins->checked[ $plugin->get_id() ]  = $plugin->get_installed_version();
			$update_plugins->response[ $plugin->get_id() ] = $response;
		}

		return $update_plugins;
	}

	private function should_skip_upgrade_process( OTGS_Installer_Plugin $plugin, $update_plugins, OTGS_Installer_Repository $repository ) {
		$has_wp_org_update = isset( $update_plugins->response[ $plugin->get_id() ] );
		$subscription      = $repository->get_subscription();
		$needs_upgrade     = $plugin->get_installed_version() && version_compare( $plugin->get_version(), $plugin->get_installed_version(), '>' ) || ! empty( $_POST['reset_to_channel'] );

		if ( ! $needs_upgrade ) {
			return true;
		}

		if ( ! $subscription || ! $subscription->is_valid() ) {
			return true;
		}

		if ( isset( $update_plugins->response[ $plugin->get_id() ] ) ) {
			return true;
		}

		if ( $this->is_plugin_registered_on_external_repo( $plugin ) ) {
			return true;
		}

		$product = $this->product_finder->get_product_in_repository_by_subscription( $repository );

		if ( ! $product || ! $product->is_plugin_registered( $plugin->get_slug() ) ) {
			return true;
		}

		if ( $subscription && $this->should_fallback_under_wp_org_repo( $plugin, $subscription->get_site_key() ) && $has_wp_org_update ) {
			return true;
		}

		return false;
	}

	private function is_plugin_registered_on_external_repo( OTGS_Installer_Plugin $plugin ) {
		$repository = $this->repositories->get( $plugin->get_external_repo() );

		if ( $repository ) {
			$product = $this->product_finder->get_product_in_repository_by_subscription( $repository );

			if ( $product && $plugin->get_external_repo() && $product->is_plugin_registered( $plugin->get_slug() ) ) {
				return true;
			}
		}

		return false;
	}

	private function append_site_key_to_download_url( $url, $key, $repository_id, $site_url ) {

		$url_params['site_key'] = $key;
		$url_params['site_url'] = $site_url;
		$package_source         = $this->source_factory->create()->get();

		// Add extra parameters for custom Installer packages
		if ( $package_source ) {
			$extra = $this->get_extra_url_parameters( $package_source );
			if ( ! empty( $extra['repository'] ) && $extra['repository'] == $repository_id ) {
				unset( $extra['repository'] );
				$url_params = array_merge( $url_params, $extra );
			}
		}

		$url = add_query_arg( $url_params, $url );

		if ( 'wpml' === $repository_id ) {
			$url = add_query_arg( array(
				'using_icl'    => function_exists( 'wpml_site_uses_icl' ) && wpml_site_uses_icl(),
				'wpml_version' => defined( 'ICL_SITEPRESS_VERSION' ) ? ICL_SITEPRESS_VERSION : ''
			), $url );
		}

		return $url;

	}

	private function get_extra_url_parameters( $source ) {
		$parameters = array();

		if ( $source ) {
			foreach ( $source as $key => $val ) {
				$parameters[ $key ] = $val;
			}
		}

		$parameters['installer_version'] = WP_INSTALLER_VERSION;
		$parameters['theme']             = wp_get_theme()->get( 'Name' );
		$parameters['site_name']         = get_bloginfo( 'name' );

		return $parameters;
	}

	private function should_fallback_under_wp_org_repo( OTGS_Installer_Plugin $plugin, $site_key ) {
		return ( $plugin->is_free_on_wporg() || $plugin->has_fallback_on_wporg() && $plugin->has_fallback_on_wporg() && ! $site_key ) && $plugin->get_channel() === WP_Installer_Channels::CHANNEL_PRODUCTION;
	}
}