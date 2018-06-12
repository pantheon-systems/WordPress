<?php
/**
 * Classes Auto Loader.
 *
 * @package Wsal
 */
class WSAL_Autoloader {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var object
	 */
	protected $plugin;

	/**
	 * Paths to load.
	 *
	 * @var array
	 */
	protected $paths = array();

	/**
	 * Method: Constructor.
	 *
	 * @param WpSecurityAuditLog $plugin - Instance of WpSecurityAuditLog.
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		$this->plugin = $plugin;

		// Register autoloader.
		spl_autoload_register( array( $this, 'LoadClass' ) );
	}

	/**
	 * Method: Register class.
	 *
	 * @param string $prefix - Prefix of the class.
	 * @param string $path - Path of the file.
	 */
	public function Register( $prefix, $path ) {
		if ( ! isset( $this->paths[ $prefix ] ) ) {
			$this->paths[ $prefix ] = array();
		}
		$this->paths[ $prefix ][] = rtrim( str_replace( '\\', '/', $path ), '/' ) . '/';
	}

	/**
	 * This is the class autoloader. You should not call this directly.
	 *
	 * @param string $class - Class name.
	 * @return boolean - True if class is found and loaded, false otherwise.
	 */
	public function LoadClass( $class ) {
		foreach ( $this->paths as $prefix => $paths ) {
			foreach ( $paths as $path ) {
				if ( strstr( $class, $prefix ) !== false ) {
					$file = $path . str_replace( '_', DIRECTORY_SEPARATOR, substr( $class, strlen( $prefix ) ) ) . '.php';
					if ( file_exists( $file ) ) {
						$s = $this->plugin->profiler->Start( 'Autoload ' . basename( $file ) );
						require_once( $file );
						$s->Stop();
						return class_exists( $class, false ) || interface_exists( $class, false );
					}
				}
			}
		}
		return false;
	}

	/**
	 * Returns the class name of a particular file that contains the class.
	 *
	 * @param string $file File name.
	 * @return string|false Class name or false on error.
	 */
	public function GetClassFileClassName( $file ) {
		$file = str_replace( '\\', '/', $file ); // Win/DOS hotfix.

		foreach ( $this->paths as $prefix => $paths ) {
			foreach ( $paths as $path ) {
				if ( strstr( $file, $path ) !== false ) {
					return str_replace(
						array( $path, '/' ),
						array( $prefix, '_' ),
						substr( $file, 0, -4 ) // Remove '.php'.
					);
				}
			}
		}
		return false;
	}
}
