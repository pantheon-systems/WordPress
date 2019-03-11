<?php

class OTGS_Installer_Fetch_Subscription {

	private $package_source_factory;
	private $plugin_finder;
	private $repositories;
	private $logger;
	private $log_factory;

	public function __construct(
		OTGS_Installer_Source_Factory $package_source_factory,
		OTGS_Installer_Plugin_Finder $plugin_finder,
		OTGS_Installer_Repositories $repositories,
		OTGS_Installer_Logger $logger,
		OTGS_Installer_Log_Factory $log_factory
	) {
		$this->package_source_factory = $package_source_factory;
		$this->plugin_finder          = $plugin_finder;
		$this->repositories           = $repositories;
		$this->logger                 = $logger;
		$this->log_factory            = $log_factory;
	}

	/**
	 * @param string $repository_id
	 * @param string $site_key
	 * @param string $source
	 *
	 * @return bool|stdClass
	 * @throws OTGS_Installer_Fetch_Subscription_Exception
	 */
	public function get( $repository_id, $site_key, $source ) {
		if ( ! $repository_id || ! $site_key || ! $source ) {
			throw new OTGS_Installer_Fetch_Subscription_Exception( 'Repository, site key and source are required fields.' );
		}

		$subscription_data = false;

		$args['body'] = array(
			'action'   => 'site_key_validation',
			'site_key' => $site_key,
			'site_url' => $this->get_installer_site_url( $repository_id ),
			'source'   => $source
		);

		if ( $repository_id === 'wpml' ) {
			$args['body']['using_icl']    = function_exists( 'wpml_site_uses_icl' ) && wpml_site_uses_icl();
			$args['body']['wpml_version'] = defined( 'ICL_SITEPRESS_VERSION' ) ? ICL_SITEPRESS_VERSION : '';
		}

		$args['body']['installer_version'] = WP_INSTALLER_VERSION;
		$args['body']['theme']             = wp_get_theme()->get( 'Name' );
		$args['body']['site_name']         = get_bloginfo( 'name' );
		$args['body']['repository_id']     = $repository_id;
		$args['body']['versions']          = $this->get_local_product_versions();
		$args['timeout']                   = 45;

		$package_source = $this->package_source_factory->create()->get();

		// Add extra parameters for custom Installer packages
		if ( $package_source ) {
			$extra = $this->get_extra_url_parameters( $package_source );
			if ( ! empty( $extra['repository'] ) && $extra['repository'] == $repository_id ) {
				unset( $extra['repository'] );
				foreach ( $extra as $key => $val ) {
					$args['body'][ $key ] = $val;
				}
			}
		}

		$repository = $this->repositories->get( $repository_id );

		$valid_response = null;
		$valid_body     = null;
		$api_url        = null;

		foreach ( array( $repository->get_api_url(), $repository->get_api_url( false ) ) as $api_url ) {
			$valid_response = false;
			$valid_body     = false;

			$response = wp_remote_post(
				$api_url,
				apply_filters( 'installer_fetch_subscription_data_request', $args )
			);

			if ( is_wp_error( $response ) ) {
				continue;
			}

			$valid_response = true;

			$body = trim( wp_remote_retrieve_body( $response ) );

			if ( ! $body || ! is_serialized( $body ) ) {
				continue;
			}

			$valid_body = true;

			break;
		}

		$this->logger->add_api_log( "POST {$api_url}" );
		$this->logger->add_api_log( $args );

		$this->logger->add_log( "POST {$api_url} - fetch subscription data" );

		if ( $valid_response ) {
			if ( $valid_body ) {
				$data = unserialize( $body );
				$this->logger->add_api_log( $data );

				if ( isset( $data->subscription_data ) && $data->subscription_data ) {
					$subscription_data = $data->subscription_data;
				} else {
					$this->store_log( $args, $api_url, isset( $data->error ) ? $data->error : '' );
				}

				do_action( 'installer_fetched_subscription_data', $data, $repository_id );
			} else {
				$this->store_log( $args, $api_url, $response->get_error_message() );
				$this->logger->add_api_log( $body );
			}

		} else {
			$this->store_log( $args, $api_url, $response->get_error_message() );
			$this->logger->add_api_log( $response );
			throw new OTGS_Installer_Fetch_Subscription_Exception( $response->get_error_message() );
		}

		return $subscription_data;
	}

	private function store_log( $args, $request_url, $response ) {
		$log = $this->log_factory->create();
		$log->set_request_args( $args )
		    ->set_request_url( $request_url )
		    ->set_response( $response )
		    ->set_component( OTGS_Installer_Logger_Storage::COMPONENT_SUBSCRIPTION );

		$this->logger->save_log( $log );
	}

	/**
	 * @return array
	 */
	private function get_local_product_versions() {
		$installed_plugins = $this->plugin_finder->get_otgs_installed_plugins();
		$versions          = array();

		foreach ( $installed_plugins as $plugin ) {
			$versions[ $plugin->get_slug() ] = $plugin->get_installed_version();
		}

		return $versions;
	}

	/**
	 * @param string $source
	 *
	 * @return array
	 */
	private function get_extra_url_parameters( $source ) {
		if ( $source ) {
			$parameters = $source;
		}

		$parameters['installer_version'] = WP_INSTALLER_VERSION;
		$parameters['theme']             = wp_get_theme()->get( 'Name' );
		$parameters['site_name']         = get_bloginfo( 'name' );

		return $parameters;
	}

	/**
	 * @param bool $repository_id
	 *
	 * @return string
	 */
	private function get_installer_site_url( $repository_id = false ) {
		global $current_site;

		$site_url = get_site_url();

		if ( $repository_id && is_multisite() && $this->repositories->get_all() ) {
			$network_settings = maybe_unserialize( get_site_option( 'wp_installer_network' ) );

			if ( isset( $network_settings[ $repository_id ] ) ) {
				$site_url = get_site_url( $current_site->blog_id );
			}

		}

		return $site_url;
	}
}