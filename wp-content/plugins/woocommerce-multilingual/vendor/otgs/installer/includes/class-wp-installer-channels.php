<?php

/**
 * Class WP_Installer_Channels
 * @since 1.8
 */
class WP_Installer_Channels{

	const CHANNEL_PRODUCTION = 'production';
	const CHANNEL_BETA = 'beta';
	const CHANNEL_DEVELOPMENT = 'development';

	protected static $_instance = null;

	function __construct() {
		add_action( 'init', array( $this, 'init' ), 20 ); // after Installer
	}

	/**
	 * @return null|WP_Installer_Channels
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Get the channel literal id based on the numeric id
	 *
	 * @param mixed $id
	 *
	 * @return string
	 */
	public static function channel_name_by_id( $id ) {
		if ( self::CHANNEL_DEVELOPMENT === $id ) {
			$channel = __( 'Development', 'installer' );
		} elseif ( self::CHANNEL_BETA === $id ) {
			$channel = __( 'Beta', 'installer' );
		} else {
			$channel = __( 'Production', 'installer' );
		}

		return $channel;
	}

	/**
	 * Initialization
	 */
	public function init(){
		global $pagenow;

		if ( defined( 'DOING_AJAX' ) ) {
			add_action( 'wp_ajax_installer_set_channel', array( $this, 'set_channel' ) );
		}

		if ( $pagenow === 'plugin-install.php' && isset( $_GET['tab'] ) && $_GET['tab'] === 'commercial' ) {
			wp_enqueue_script( 'installer-channels', WP_Installer()->res_url() . '/res/js/channels.js', array( 'jquery' ), WP_Installer()->version() );
		}

	}

	/**
	 * Ajax handler for channel switching
	 */
	public function set_channel(){
		$repository_id  = sanitize_text_field( $_POST['repository_id'] );
		$channel = sanitize_text_field( $_POST['channel'] );

		$response = array();

		if( $_POST['nonce'] === wp_create_nonce( 'installer_set_channel:' . $repository_id )  ){

			if( isset( WP_Installer()->settings['repositories'][$repository_id] ) ){
				WP_Installer()->settings['repositories'][$repository_id]['channel'] = $channel;
				WP_Installer()->settings['repositories'][$repository_id]['no-prompt'] = $_POST['noprompt'] === 'true';
				WP_Installer()->save_settings();
			}

			WP_Installer()->refresh_repositories_data();

			$response['status'] = 'OK';
		}

		echo json_encode( $response );
		exit;
	}

	/**
	 * @param string $repository_id
	 *
	 * @return int
	 */
	public function get_channel( $repository_id ){
		$channel = self::CHANNEL_PRODUCTION;
		if( isset( WP_Installer()->settings['repositories'][$repository_id]['channel'] ) ){
			$channel = WP_Installer()->settings['repositories'][$repository_id]['channel'];
		}
		return $channel;
	}

	/**
	 * @param $repository_id
	 *
	 * @return bool
	 */
	private function get_no_prompt( $repository_id ) {
		$settings  = WP_Installer()->settings;

		return ! empty( $settings['repositories'][ $repository_id ]['no-prompt'] );
	}

	/**
	 * @param string $repository_id
	 * @param array $downloads
	 */
	public function load_channel_selector( $repository_id, $downloads ) {

		$available_channels = $this->get_available_channels( $repository_id );

		if ( $available_channels ) {
			$args = array(
				'can_switch'      => $this->can_use_unstable_channels( $downloads ) || $this->get_channel( $repository_id ) > 1,
				'channels'        => $available_channels,
				'repository_id'   => $repository_id,
				'current_channel' => $this->get_channel( $repository_id ),
				'no_prompt'       => $this->get_no_prompt( $repository_id ),
				'nonce'           => wp_create_nonce( 'installer_set_channel:' . $repository_id )
			);
			extract( $args );
			include WP_Installer()->plugin_path() . '/templates/channel-selector.php';
		}
	}

	/**
	 * The beta and development channels can be used only when already using the most up to date versions
	 * @param array $downloads
	 *
	 * @return bool
	 */
	public function can_use_unstable_channels( $downloads ){

		$can = true;
		foreach( $downloads as $download ){
			$available_version =  $download['version'];
			$installed_version = WP_Installer()->plugin_is_installed( $download['name'], $download['slug'] );
			if( $installed_version !== false && version_compare( $available_version, $installed_version, '>' ) ){
				$can = false;
				break;
			}
		}

		return $can;
	}

	/**
	 * Get available updates channels. Only include channels with actual downloads available.
	 *
	 * @param string $repository_id
	 *
	 * @return array
	 */
	public function get_available_channels( $repository_id ) {

		$beta = false;
		$dev  = false;

		$downloads = WP_Installer()->settings['repositories'][ $repository_id ]['data']['downloads'];
		foreach ( $downloads as $type => $download_types ) {
			foreach ( $download_types as $download ) {
				$extra_channels = isset( $download['extra_channels'] ) ? array_keys( $download['extra_channels'] ) : array();
				if ( ! $beta && in_array( self::CHANNEL_BETA, $extra_channels ) ) {
					$beta = true;
				}
				if ( ! $dev && in_array( self::CHANNEL_DEVELOPMENT, $extra_channels ) ) {
					$dev = true;
				}
				if ( $beta && $dev ) {
					break;
				}
			}
		}

		$channels = array();
		if ( $beta || $dev ) {
			$channels[ self::CHANNEL_PRODUCTION ] = self::channel_name_by_id( self::CHANNEL_PRODUCTION );
			if ( $beta ) {
				$channels[ self::CHANNEL_BETA ] = self::channel_name_by_id( self::CHANNEL_BETA );
			}
			if ( $dev ) {
				$channels[ self::CHANNEL_DEVELOPMENT ] = self::channel_name_by_id( self::CHANNEL_DEVELOPMENT );
			}
		}

		return $channels;
	}

	/**
	 * @param string $repository_id
	 * @param array $downloads
	 *
	 * @return array
	 */
	public function filter_downloads_by_channel( $repository_id, $downloads ) {

		$current_channel = $this->get_channel( $repository_id );

		foreach ( $downloads as $type => $type_downloads ) {
			foreach ( $type_downloads as $slug => $download ) {

				$override_download = array();
				if ( $current_channel === self::CHANNEL_DEVELOPMENT ) {
					if( ! empty( $download['channels']['development'] ) ){
						$override_download            = $download['channels']['development'];
						$override_download['channel'] = self::CHANNEL_DEVELOPMENT;
					}elseif( ! empty( $download['channels']['beta'] ) ){
						$override_download            = $download['channels']['beta'];
						$override_download['channel'] = self::CHANNEL_BETA;
					}
				}elseif ( $current_channel === self::CHANNEL_BETA && ! empty( $download['channels']['beta'] ) ) {
					$override_download            = $download['channels']['beta'];
					$override_download['channel'] = self::CHANNEL_BETA;
				}

				if ( $override_download ) {
					foreach ( $override_download as $key => $value ) {
						$downloads[ $type ][ $slug ][ $key ] = $value;
					}
				} else {
					$downloads[ $type ][ $slug ]['channel'] = self::CHANNEL_PRODUCTION;
				}
				unset ( $downloads[ $type ][ $slug ]['channels'] );

				$downloads[ $type ][ $slug ]['extra_channels'] = array();
				if( isset( $download['channels'] ) ) {
				    foreach( $download['channels'] as $channel_id => $channel ){
					    $downloads[ $type ][ $slug ]['extra_channels'][$channel_id] = array(
						    'version' => $channel['version']
					    );
				    }
				}

			}
		}

		return $downloads;
	}

	/**
	 * Get the source channel for the installed version when on the Beta or Development channel
	 * @param string $version
	 * @param string $repository_id
	 * @param string $download_id
	 * @param string $download_kind
	 *
	 * @return string
	 */
	public function get_download_source_channel( $version, $repository_id, $download_id, $download_kind ) {

		$version_channel    = '';
		$installer_settings = WP_Installer()->get_settings();
		if ( isset( $installer_settings['repositories'][ $repository_id ] ) ) {
			$repository_data = $installer_settings['repositories'][ $repository_id ]['data'];
			if ( isset( $repository_data['downloads'][ $download_kind ][ $download_id ]['extra_channels'] ) ) {

				foreach ( $repository_data['downloads'][ $download_kind ][ $download_id ]['extra_channels'] as $channel_id => $channel_data ) {
					if ( $version === $channel_data['version'] ) {
						$version_channel = self::channel_name_by_id( $channel_id );
						break;
					}

				}
			}

		}

		return $version_channel;
	}
}