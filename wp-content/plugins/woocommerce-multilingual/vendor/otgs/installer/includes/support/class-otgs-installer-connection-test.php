<?php

class OTGS_Installer_Connection_Test {

	private $repositories;
	private $upgrade_response;
	private $logger_storage;
	private $log_factory;

	public function __construct(
		OTGS_Installer_Repositories $repositories,
		OTGS_Installer_Upgrade_Response $upgrade_response,
		OTGS_Installer_Logger_Storage $logger_storage,
		OTGS_Installer_Log_Factory $log_factory
	) {
		$this->repositories     = $repositories;
		$this->upgrade_response = $upgrade_response;
		$this->logger_storage   = $logger_storage;
		$this->log_factory      = $log_factory;
	}

	/**
	 * @param string $repo_id
	 *
	 * @return null|string
	 */
	public function get_api_status( $repo_id ) {
		return $this->get_url_status( $this->repositories->get( $repo_id )->get_api_url() );
	}

	/**
	 * @param string $repo_id
	 *
	 * @return null|string
	 */
	public function get_products_status( $repo_id ) {
		return $this->get_url_status( $this->repositories->get( $repo_id )->get_products_url() );
	}

	/**
	 * @param string $plugin_id
	 *
	 * @return bool|string
	 */
	public function get_download_status( $plugin_id ) {
		$plugins_updates = get_site_transient( 'update_plugins' );
		$update_response = $this->upgrade_response->modify_upgrade_response( $plugins_updates );
		$response        = false;
		$error_message   = '';

		if ( isset( $update_response->response[ $plugin_id ] ) ) {
			$request_response    = wp_remote_head( $update_response->response[ $plugin_id ]->package );
			$parsed_download_url = wp_parse_url( $update_response->response[ $plugin_id ]->package );
			parse_str( $parsed_download_url['query'], $download_args );

			if ( is_wp_error( $request_response ) ) {
				$error_message = $request_response->get_error_message();
			} elseif ( ! $this->is_response_successful( $request_response ) ) {
				$error_message = 'Invalid response';
			}

			if ( $error_message ) {
				$this->log(
					sprintf(
						'%s: an error occurred while trying to get information of this download URL. Error: %s, download: %s, version: %s.',
						$plugin_id,
						$error_message,
						$download_args['download'],
						$download_args['version']
					),
					$update_response->response[ $plugin_id ]->package
				);
			} else {
				$response = true;
			}
		}

		return $response;
	}

	/**
	 * @param array $response
	 *
	 * @return bool
	 */
	private function is_response_successful( $response ) {
		return in_array( $response['response']['code'], $this->get_success_codes(), true );
	}

	/**
	 * @param string $url
	 *
	 * @return bool
	 */
	private function get_url_status( $url ) {
		$response      = false;
		$res           = wp_remote_get( $url );
		$error_message = '';

		if ( is_wp_error( $res ) ) {
			$error_message = sprintf( "Your site can't communicate with %s. Code %d: %s.", $url, $res->get_error_code(), $res->get_error_message() );
		} elseif ( ! $this->is_response_successful( $res ) ) {
			$error_message = sprintf( "Your site can't communicate with %s. Code %d.", $url, $res['response']['code'] );
		}

		if ( $error_message ) {
			$this->log(
				$error_message,
				$url
			);
		} else {
			$response = true;
		}

		return $response;
	}

	private function log( $msg, $url ) {
		$this->logger_storage->add(
			$this->log_factory
				->create()
				->set_request_url( $url )
				->set_component( OTGS_Installer_Logger_Storage::PRODUCTS_FILE_CONNECTION_TEST )
				->set_response( $msg )
		);
	}

	private function get_success_codes() {
		return array( 302, 200 );
	}
}