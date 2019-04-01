<?php

class OTGS_Installer_Site_Key_Ajax {

	private $subscription_fetch;
	private $logger;
	private $repositories;
	private $subscription_factory;

	public function __construct(
		OTGS_Installer_Fetch_Subscription $subscription_fetch,
		OTGS_Installer_Logger $logger,
		OTGS_Installer_Repositories $repositories,
		OTGS_Installer_Subscription_Factory $subscription_factory
	) {
		$this->subscription_fetch   = $subscription_fetch;
		$this->logger               = $logger;
		$this->repositories         = $repositories;
		$this->subscription_factory = $subscription_factory;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_save_site_key', array( $this, 'save' ) );
		add_action( 'wp_ajax_remove_site_key', array( $this, 'remove' ) );
		add_action( 'wp_ajax_update_site_key', array( $this, 'update' ) );
	}

	public function save() {
		$repository = isset( $_POST['repository_id'] ) && $_POST['repository_id'] ? sanitize_text_field( $_POST['repository_id'] ) : null;
		$nonce      = isset( $_POST['nonce'] ) && $_POST['nonce'] ? sanitize_text_field( $_POST['nonce'] ) : null;
		$site_key   = isset( $_POST[ 'site_key_' . $repository ] ) && $_POST[ 'site_key_' . $repository ] ? sanitize_text_field( $_POST[ 'site_key_' . $repository ] ) : null;
		$site_key   = preg_replace( '/[^A-Za-z0-9]/', '', $site_key );
		$error      = '';

		if ( ! $repository || ! $nonce || ! $site_key || ! wp_verify_nonce( $nonce, 'save_site_key_' . $repository ) ) {
			wp_send_json_error( esc_html__( 'Invalid request!', 'installer' ) );
		}

		try {
			$subscription = $this->subscription_fetch->get( $repository, $site_key, WP_Installer::SITE_KEY_VALIDATION_SOURCE_REGISTRATION );
			if ( $subscription ) {
				$subscription_data = $this->subscription_factory->create( array(
					'data'          => $subscription,
					'key'           => $site_key,
					'site_url'      => get_site_url(),
					'registered_by' => get_current_user_id()
				) );

				$repository = $this->repositories->get( $repository );
				$repository->set_subscription( $subscription_data );
				$this->repositories->save_subscription( $repository );
			} else {
				$error = __( 'Invalid site key for the current site.', 'installer' ) . '<br /><div class="installer-footnote">' . __( 'Please note that the site key is case sensitive.', 'installer' ) . '</div>';
			}
		} catch ( OTGS_Installer_Site_Key_Exception $e ) {
			$repository_data = $this->repositories->get( $repository );
			$error           = $this->get_error_message( $e, $repository_data );
		}

		$response = array( 'error' => $error );

		if ( $this->logger->get_api_log() ) {
			$response['debug'] = $this->logger->get_api_log();
		}

		wp_send_json_success( $response );
	}

	public function remove() {
		$repository   = isset( $_POST['repository_id'] ) ? sanitize_text_field( $_POST['repository_id'] ) : null;
		$nonce        = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;
		$nonce_action = 'remove_site_key_' . $repository;

		if ( wp_verify_nonce( $nonce, $nonce_action ) ) {
			$repository = $this->repositories->get( $repository );
			$repository->set_subscription( null );
			$this->repositories->save_subscription( $repository );
		}

		wp_send_json_success();
		$this->repositories->refresh();
	}

	public function update() {
		$error      = '';
		$nonce      = isset( $_POST['nonce'] ) ? $_POST['nonce'] : null;
		$repository = isset( $_POST['repository_id'] ) ? sanitize_text_field( $_POST['repository_id'] ) : null;

		if ( $nonce && $repository && wp_verify_nonce( $nonce, 'update_site_key_' . $repository ) ) {
			$repository_data = $this->repositories->get( $repository );
			$site_key        = $repository_data->get_subscription()->get_site_key();

			if ( $site_key ) {
				try {
					$subscription = $this->subscription_fetch->get( $repository, $site_key, WP_Installer::SITE_KEY_VALIDATION_SOURCE_REGISTRATION );

					if ( $subscription ) {
						$subscription_data = $this->subscription_factory->create( array(
							'data'          => $subscription,
							'key'           => $site_key,
							'site_url'      => get_site_url(),
							'registered_by' => get_current_user_id(),
						) );
						$repository_data->set_subscription( $subscription_data );
					} else {
						$repository_data->set_subscription( null );
						$error = __( 'Invalid site key for the current site. If the error persists, try to un-register first and then register again with the same site key.', 'installer' );
					}

					$this->repositories->save_subscription( $repository_data );
					$this->repositories->refresh();
				} catch ( OTGS_Installer_Site_Key_Exception $e ) {
					$error = $this->get_error_message( $e, $repository_data );
				}
			}

		}

		wp_send_json_success( array( 'error' => $error ) );
	}

	private function get_error_message( Exception $e, OTGS_Installer_Repository $repository_data ) {
		$error = $e->getMessage();
		if ( preg_match( '#Could not resolve host: (.*)#', $error, $matches ) || preg_match( '#Couldn\'t resolve host \'(.*)\'#', $error, $matches ) ) {
			$error = sprintf( __( "%s cannot access %s to register. Try again to see if it's a temporary problem. If the problem continues, make sure that this site has access to the Internet. You can still use the plugin without registration, but you will not receive automated updates.", 'installer' ),
				'<strong><i>' . $repository_data->get_product_name() . '</i></strong>',
				'<strong><i>' . $matches[1] . '</i></strong>'
			);
		}

		return $error;
	}
}