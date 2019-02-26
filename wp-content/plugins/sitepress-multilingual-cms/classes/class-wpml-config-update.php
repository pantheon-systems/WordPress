<?php

/**
 * Fetch the wpml config files for known plugins and themes
 *
 * @package wpml-core
 */
class WPML_Config_Update {
	/** @var bool */
	private $has_errors;
	private $log;
	/** @var  SitePress $sitepress */
	protected $sitepress;

	/**
	 * @var WP_Http $http
	 */
	private $http;

	/**
	 * @var WPML_Active_Plugin_Provider
	 */
	private $active_plugin_provider;

	/**
	 * WPML_Config_Update constructor.
	 *
	 * @param SitePress     $sitepress
	 * @param WP_Http       $http
	 * @param WPML_Log|null $log
	 */
	public function __construct( $sitepress, $http, WPML_Log $log = null ) {
		$this->sitepress = $sitepress;
		$this->http      = $http;
		$this->log       = $log;
	}

	/**
	 * @param WPML_Active_Plugin_Provider $active_plugin_provider
	 */
	public function set_active_plugin_provider( WPML_Active_Plugin_Provider $active_plugin_provider ) {
		$this->active_plugin_provider = $active_plugin_provider;
	}

	/**
	 * @return WPML_Active_Plugin_Provider
	 */
	public function get_active_plugin_provider() {
		if ( null === $this->active_plugin_provider ) {

			if ( ! class_exists( 'WPML_Active_Plugin_Provider' ) ) {
				require_once WPML_PLUGIN_PATH . '/classes/class-wpml-active-plugin-provider.php';
			}

			$this->active_plugin_provider = new WPML_Active_Plugin_Provider();
		}

		return $this->active_plugin_provider;
	}

	public function run() {
		if ( ! $this->is_config_update_disabled() ) {
			$this->has_errors = false;
			$request_args = array( 'timeout' => 45 );

			$index_response = $this->http->get( ICL_REMOTE_WPML_CONFIG_FILES_INDEX . 'wpml-config/config-index.json', $request_args );

			if ( ! $this->is_a_valid_remote_response( $index_response ) ) {
				$this->log_response( $index_response, 'index', 'wpml-config/config-index.json' );
			} else {
				$arr = json_decode( $index_response['body'] );

				$plugins = isset( $arr->plugins ) ? $arr->plugins : array();
				$themes  = isset( $arr->themes ) ? $arr->themes : array();

				if ( $plugins || $themes ) {
					update_option( 'wpml_config_index', $arr, false );
					update_option( 'wpml_config_index_updated', time(), false );

					$config_files_original = get_option( 'wpml_config_files_arr', null );
					$config_files          = maybe_unserialize( $config_files_original );

					$config_files_for_themes     = array();
					$deleted_configs_for_themes  = array();
					$config_files_for_plugins    = array();
					$deleted_configs_for_plugins = array();
					if ( $config_files ) {
						if ( isset( $config_files->themes ) && $config_files->themes ) {
							$config_files_for_themes    = $config_files->themes;
							$deleted_configs_for_themes = $config_files->themes;
						}
						if ( isset( $config_files->plugins ) && $config_files->plugins ) {
							$config_files_for_plugins    = $config_files->plugins;
							$deleted_configs_for_plugins = $config_files->plugins;
						}
					}

					$current_theme_name = $this->sitepress->get_wp_api()
					                                      ->get_theme_name();

					$current_theme_parent = '';
					if ( method_exists( $this->sitepress->get_wp_api(), 'get_theme_parent_name' ) ) {
						$current_theme_parent = $this->sitepress->get_wp_api()
						                                        ->get_theme_parent_name();
					}

					$active_theme_names = array( $current_theme_name );
					if ( $current_theme_parent ) {
						$active_theme_names[] = $current_theme_parent;
					}

					foreach ( $themes as $theme ) {

						if ( in_array( $theme->name, $active_theme_names, true ) ) {

							unset( $deleted_configs_for_themes[ $theme->name ] );

							if ( ! isset( $config_files_for_themes[ $theme->name ] ) || md5( $config_files_for_themes[ $theme->name ] ) !== $theme->hash ) {
								$theme_response = $this->http->get( ICL_REMOTE_WPML_CONFIG_FILES_INDEX . $theme->path, $request_args );
								if ( ! $this->is_a_valid_remote_response( $theme_response ) ) {
									$this->log_response( $theme_response, 'index', $theme->name );
								} else {
									$config_files_for_themes[ $theme->name ] = $theme_response['body'];
								}
							}
						}
					}

					foreach ( $deleted_configs_for_themes as $key => $deleted_config ) {
						unset( $config_files_for_themes[ $key ] );
					}

					$active_plugins_names = $this->get_active_plugin_provider()
					                             ->get_active_plugin_names();

					foreach ( $plugins as $plugin ) {

						if ( in_array( $plugin->name, $active_plugins_names, true ) ) {

							unset( $deleted_configs_for_plugins[ $plugin->name ] );

							if ( ! isset( $config_files_for_plugins[ $plugin->name ] ) || md5( $config_files_for_plugins[ $plugin->name ] ) !== $plugin->hash ) {
								$plugin_response = $this->http->get( ICL_REMOTE_WPML_CONFIG_FILES_INDEX . $plugin->path, $request_args );

								if ( ! $this->is_a_valid_remote_response( $plugin_response ) ) {
									$this->log_response( $plugin_response, 'index', $plugin->name );
								} else {
									$config_files_for_plugins[ $plugin->name ] = $plugin_response['body'];
								}
							}
						}
					}

					foreach ( $deleted_configs_for_plugins as $key => $deleted_config ) {
						unset( $config_files_for_plugins[ $key ] );
					}

					if ( ! $config_files ) {
						$config_files = new stdClass();
					}
					$config_files->themes  = $config_files_for_themes;
					$config_files->plugins = $config_files_for_plugins;

					update_option( 'wpml_config_files_arr', $config_files, false );
				}
			}

			$wpml_config_files_arr = maybe_unserialize( get_option( 'wpml_config_files_arr', null ) );
			if ( ! $wpml_config_files_arr ) {
				$this->log_response( 'Missing data', 'get_option', 'wpml_config_files_arr' );
			}
			if ( ! $this->has_errors && $this->log ) {
				$this->log->clear();
			}
		}

		return ! $this->has_errors;
	}

	private function is_valid_wpml_config_files_arr( $wpml_config_files_arr ) {
		$is_valid = true;
		$is_valid &= is_object( $wpml_config_files_arr );

		$at_least_plugins_or_themes = false;
		$at_least_plugins_or_themes |= isset( $wpml_config_files_arr->themes ) && is_array( $wpml_config_files_arr->themes ) && $wpml_config_files_arr->themes;
		$at_least_plugins_or_themes |= isset( $wpml_config_files_arr->plugins ) && is_array( $wpml_config_files_arr->plugins ) && $wpml_config_files_arr->plugins;

		return $is_valid && $at_least_plugins_or_themes;
	}

	/**
	 * @param array|WP_Error $response
	 *
	 * @return bool
	 */
	private function is_a_valid_remote_response( $response ) {
		return $response && ! is_wp_error( $response ) && ! $this->is_http_error( $response );
	}

	private function is_http_error( $response ) {
		return $response && is_array( $response )
		       && ( ( array_key_exists( 'response', $response )
		              && array_key_exists( 'code', $response['response'] )
		              && 200 !== (int) $response['response']['code'] )
		            || ! array_key_exists( 'body', $response )
		            || '' === trim( $response['body'] ) );
	}

	/**
	 * @param string|array|WP_Error $response
	 * @param string                $request_type
	 * @param null                  $component
	 * @param array|stdClass|null   $extra_data
	 */
	private function log_response( $response, $request_type = 'unknown', $component = null, $extra_data = null ) {
		if ( ! $this->log ) {
			return;
		}

		$message_type = 'message';

		if ( ! defined( 'JSON_PRETTY_PRINT' ) ) {
			//Fallback -> Introduced in PHP 5.4.0
			define( 'JSON_PRETTY_PRINT', 128 );
		}

		$response_data = null;
		if ( is_scalar( $response ) ) {
			$message_type  = 'app_error';
			$response_data = $response;
		} elseif ( is_wp_error( $response ) ) {
			$message_type  = 'wp_error';
			$response_data = array(
				'code'    => $response->get_error_code(),
				'message' => $response->get_error_message(),
			);
		} elseif ( $this->is_http_error( $response ) ) {
			$message_type = 'http_error';
			if ( array_key_exists( 'response', $response ) ) {
				if ( array_key_exists( 'code', $response['response'] ) ) {
					$response_data['code'] = $response['response']['code'];
				}
				if ( array_key_exists( 'message', $response['response'] ) ) {
					$response_data['message'] = $response['response']['message'];
				}
			}
			$response_data['body'] = 'Missing!';
			if ( array_key_exists( 'body', $response ) ) {
				$response_data['body'] = 'Empty!';
				if ( $response['body'] ) {
					$response_data['body'] = json_decode( json_encode( simplexml_load_string( $response['body'] ) ), true );
				}
			}
		} elseif ( is_array( $response ) ) {
			$response_data = $response;
		} else {
			$response_data = array( wp_json_encode( $response, JSON_PRETTY_PRINT ) );
		}

		$serialized_extra_data = null;
		if ( $extra_data ) {
			$serialized_extra_data = $extra_data;
			if ( is_object( $serialized_extra_data ) ) {
				$serialized_extra_data = get_object_vars( $serialized_extra_data );
			}
			if ( ! is_array( $serialized_extra_data ) ) {
				$serialized_extra_data = array( wp_json_encode( $serialized_extra_data, JSON_PRETTY_PRINT ) );
			}
		}
		$entry = array(
			'request'   => $request_type,
			'type'      => $message_type,
			'component' => $component,
			'response'  => $response_data,
			'extra'     => $serialized_extra_data,
		);
		$this->log->insert( microtime( true ), $entry );
		$this->has_errors = true;
	}

	private function is_config_update_disabled() {
		if ( $this->sitepress->get_wp_api()
		                     ->constant( 'ICL_REMOTE_WPML_CONFIG_DISABLED' ) ) {
			delete_option( 'wpml_config_index' );
			delete_option( 'wpml_config_index_updated' );
			delete_option( 'wpml_config_files_arr' );

			return true;
		}

		return false;
	}
}
