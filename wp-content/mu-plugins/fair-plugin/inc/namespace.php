<?php
/**
 * Namespace for the plugin.
 *
 * @package FAIR
 */

namespace FAIR;

use Fragen\Git_Updater;

const NS_SEPARATOR = '\\';

function bootstrap() {
	// Prevent accidental re-initialization of the plugin.
	static $did_init = false;
	if ( $did_init ) {
		return;
	}

	$did_init = true;

	register_class_path( __NAMESPACE__, __DIR__ . '/inc' );

	// Modules:
	Avatars\bootstrap();
	Credits\bootstrap();
	Dashboard_Widgets\bootstrap();
	Default_Repo\bootstrap();
	Disable_Openverse\bootstrap();
	Importers\bootstrap();
	Pings\bootstrap();
	Salts\bootstrap();
	Settings\bootstrap();
	User_Notification\bootstrap();
	Version_Check\bootstrap();

	// Self-update check.
	( new Git_Updater\Lite( PLUGIN_FILE ) )->run();
}

/**
 * Register a path for autoloading.
 *
 * @param string $prefix The namespace prefix.
 * @param string $path   The path to the class files.
 * @return void
 */
function register_class_path( string $prefix, string $path ) : void {
	$prefix_length = strlen( $prefix );
	spl_autoload_register( function ( $class ) use ( $prefix, $prefix_length, $path ) {
		if ( strpos( $class, $prefix . NS_SEPARATOR ) !== 0 ) {
			return;
		}

		// Strip prefix from the start (ala PSR-4).
		$class = substr( $class, $prefix_length + 1 );
		$class = strtolower( $class );
		$class = str_replace( '_', '-', $class );
		$file  = '';

		// Split on namespace separator.
		$last_ns_pos = strripos( $class, NS_SEPARATOR );
		if ( $last_ns_pos !== false ) {
			$namespace = substr( $class, 0, $last_ns_pos );
			$class     = substr( $class, $last_ns_pos + 1 );
			$file      = str_replace( NS_SEPARATOR, DIRECTORY_SEPARATOR, $namespace ) . DIRECTORY_SEPARATOR;
		}
		$file .= 'class-' . $class . '.php';

		$path = $path . $file;

		if ( file_exists( $path ) ) {
			require_once $path;
		}
	} );
	Version_Check\bootstrap();
}
