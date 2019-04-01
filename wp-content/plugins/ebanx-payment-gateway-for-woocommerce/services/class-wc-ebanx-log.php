<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class is responsible to get some data about platform, plugins, theme and some options
 * to register on log
 */
class WC_EBANX_Log {
	/**
	 * This method is responsible to get platform info to be logged
	 */
	public static function get_platform_info() {
		$environment = new WC_EBANX_Environment();
		return array(
			'platform' => array(
				'name'    => 'WORDPRESS',
				'version' => $environment->platform->version,
				'theme'   => self::get_theme_data(),
				'plugins' => self::get_plugins_data(),
				'options' => self::get_options(),
			),
			'server'   => array(
				'language'        => $environment->interpreter,
				'web_server'      => $environment->web_server,
				'database_server' => $environment->database_server,
				'os'              => $environment->operating_system,
			),
		);
	}

	/**
	 * Logs to WP if WP_DEBUG is active.
	 *
	 * @param string $log
	 */
	public static function wp_write_log( $log ) {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}

	/**
	 * This method is responsible to get some active plugins public data to be logged
	 */
	private static function get_plugins_data() {
		return array_map(
			function ( $plugin ) {
					return get_file_data(
						WC_EBANX_DIR . '../' . $plugin,
						array(
							'version'     => 'version',
							'Plugin Name' => 'Plugin Name',
							'Description' => 'Description',
							'Plugin URI'  => 'Plugin URI',
							'Author'      => 'Author',
							'License'     => 'License',
							'Author URI'  => 'Author URI',
						)
					);
			}, get_option( 'active_plugins' )
		);
	}

	/**
	 * Gets some data from active theme to be logged
	 */
	private static function get_theme_data() {
		$wp_theme = wp_get_theme();

		return [
			'Name'        => $wp_theme->get( 'Name' ),
			'ThemeURI'    => $wp_theme->get( 'ThemeURI' ),
			'Description' => $wp_theme->get( 'Description' ),
			'Author'      => $wp_theme->get( 'Author' ),
			'AuthorURI'   => $wp_theme->get( 'AuthorURI' ),
			'Version'     => $wp_theme->get( 'Version' ),
			'Template'    => $wp_theme->get( 'Template' ),
			'Status'      => $wp_theme->get( 'Status' ),
			'Tags'        => $wp_theme->get( 'Tags' ),
			'TextDomain'  => $wp_theme->get( 'TextDomain' ),
			'DomainPath'  => $wp_theme->get( 'DomainPath' ),
		];
	}

	/**
	 * Retrieve some options to be logged
	 */
	private static function get_options() {
		$wp_theme = wp_get_theme();

		return array(
			'admin_email'     => get_option( 'admin_email' ),
			'blogname'        => get_option( 'blogname' ),
			'blogdescription' => get_option( 'blogdescription' ),
			'home'            => get_option( 'home' ),
			'siteurl'         => get_option( 'siteurl' ),
			'template'        => get_option( 'template' ),
		);
	}
}
