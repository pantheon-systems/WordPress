<?php
/**
 * Class for methods for file/folder related things
 */
class BackWPup_File {

	/**
	 *
	 * Get the folder for blog uploads
	 *
	 * @return string
	 */
	public static function get_upload_dir() {

		if ( is_multisite() ) {
			if ( defined( 'UPLOADBLOGSDIR' ) )
				return trailingslashit( str_replace( '\\', '/',ABSPATH . UPLOADBLOGSDIR ) );
			elseif ( is_dir( trailingslashit( WP_CONTENT_DIR ) . 'uploads/sites') )
				return str_replace( '\\', '/', trailingslashit( WP_CONTENT_DIR ) . 'uploads/sites/' );
			elseif ( is_dir( trailingslashit( WP_CONTENT_DIR ) . 'uploads' ) )
				return str_replace( '\\', '/', trailingslashit( WP_CONTENT_DIR ) . 'uploads/' );
			else
				return trailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) );
		} else {
			$upload_dir = wp_upload_dir( null, false, true );
			return trailingslashit( str_replace( '\\', '/', $upload_dir[ 'basedir' ] ) );
		}

	}

	/**
	 *
	 * check if path in open basedir
	 *
	 * @param string $file the file path to check
	 *
	 * @return bool is it in open basedir
	 */
	public static function is_in_open_basedir( $file ) {

		$ini_open_basedir = ini_get( 'open_basedir' );

		if ( empty( $ini_open_basedir ) ) {
			return TRUE;
		}

		$open_base_dirs = explode( PATH_SEPARATOR, $ini_open_basedir );
		$file           = trailingslashit( strtolower( str_replace( '\\', '/', $file ) ) );

		foreach ( $open_base_dirs as $open_base_dir ) {
			if ( empty( $open_base_dir ) ) {
				continue;
			}
			$open_base_dir = realpath( $open_base_dir );
			$open_base_dir = strtolower( str_replace( '\\', '/', $open_base_dir ) );
			$part = substr( $file, 0, strlen( $open_base_dir ) );
			if ( $part === $open_base_dir ) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 *
	 * get size of files in folder
	 *
	 * @param string $folder the folder to calculate
	 * @param bool $deep went thrue suborders
	 * @return int folder size in byte
	 */
	public static function get_folder_size( $folder, $deep = TRUE ) {

		$files_size = 0;

		if ( ! is_readable( $folder ) )
			return $files_size;

		if ( $dir = opendir( $folder ) ) {
			while ( FALSE !== ( $file = readdir( $dir ) ) ) {
				if ( in_array( $file, array( '.', '..' ), true ) || is_link( $folder . '/' . $file ) ) {
					continue;
				}
				if ( $deep && is_dir( $folder . '/' . $file ) ) {
					$files_size = $files_size + self::get_folder_size( $folder . '/' . $file, TRUE );
				}
				elseif ( is_link( $folder . '/' . $file ) ) {
					continue;
				}
				elseif ( is_readable( $folder . '/' . $file ) ) {
					$file_size = filesize( $folder . '/' . $file );
					if ( empty( $file_size ) || ! is_int( $file_size ) ) {
						continue;
					}
					$files_size = $files_size + $file_size;
				}
			}
			closedir( $dir );
		}

		return $files_size;
	}

	/**
	 * Get an absolute path if it is relative
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public static function get_absolute_path( $path = '/' ) {

		$path = str_replace( '\\', '/', $path );
		$content_path = trailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) );

		//use WP_CONTENT_DIR as root folder
		if ( empty( $path ) || $path === '/' ) {
			$path = $content_path;
		}

		//make relative path to absolute
		if ( substr( $path, 0, 1 ) !== '/' && ! preg_match( '#^[a-zA-Z]:/#', $path ) ) {
			$path =  $content_path . $path;
		}

		return $path;
	}

	/**
	 *
	 * Check is folder readable and exists create it if not
	 * add .htaccess or index.html file in folder to prevent directory listing
	 *
	 * @param string $folder the folder to check
	 * @param bool   $donotbackup Create a file that the folder will not backuped
	 *
     * @return string with error message if one
	 */
	public static function check_folder( $folder, $donotbackup = FALSE ) {

		$folder = BackWPup_File::get_absolute_path( $folder );
		$folder = untrailingslashit( $folder );

		//check that is not home of WP
		$uploads = BackWPup_File::get_upload_dir();
		if ( $folder === untrailingslashit( str_replace( '\\', '/', ABSPATH ) )
		     || $folder === untrailingslashit( str_replace( '\\', '/', dirname( ABSPATH ) ) )
		     || $folder === untrailingslashit( str_replace( '\\', '/', WP_PLUGIN_DIR ) )
		     || $folder === untrailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) )
		     || $folder === untrailingslashit( $uploads )
		     || $folder === '/'
		) {
			return sprintf( __( 'Folder %1$s not allowed, please use another folder.', 'backwpup' ), $folder );
		}

		//open base dir check
		if ( ! BackWPup_File::is_in_open_basedir( $folder ) ) {
			return sprintf( __( 'Folder %1$s is not in open basedir, please use another folder.', 'backwpup' ), $folder );
		}

		//create folder if it not exists
		if ( ! is_dir( $folder ) ) {
			if ( ! wp_mkdir_p( $folder ) ) {
				return sprintf( __( 'Cannot create folder: %1$s', 'backwpup' ), $folder );
			}
		}

		//check is writable dir
		if ( ! is_writable( $folder ) ) {
			return sprintf( __( 'Folder "%1$s" is not writable', 'backwpup' ), $folder );
		}

		//create files for securing folder
		if ( get_site_option( 'backwpup_cfg_protectfolders' ) ) {
			$server_software = strtolower( $_SERVER[ 'SERVER_SOFTWARE' ] );
			//IIS
			if ( strstr( $server_software, 'microsoft-iis' ) ) {
				if ( ! file_exists( $folder . '/web.config' ) ) {
					file_put_contents( $folder . '/web.config', "<configuration>" . PHP_EOL . "\t<system.webServer>" . PHP_EOL . "\t\t<authorization>" . PHP_EOL . "\t\t\t<deny users=" * " />" . PHP_EOL . "\t\t</authorization>" . PHP_EOL . "\t</system.webServer>" . PHP_EOL . "</configuration>" );
				}
			} //Nginx
			elseif ( strstr( $server_software, 'nginx' ) ) {
				if ( ! file_exists( $folder . '/index.php' ) ) {
					file_put_contents( $folder . '/index.php', "<?php" . PHP_EOL . "header( \$_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found' );" . PHP_EOL . "header( 'Status: 404 Not Found' );" . PHP_EOL );
				}
			} //Aapche and other
			else {
				if ( ! file_exists( $folder . '/.htaccess' ) ) {
					file_put_contents( $folder . '/.htaccess', "<Files \"*\">" . PHP_EOL . "<IfModule mod_access.c>" . PHP_EOL . "Deny from all" . PHP_EOL . "</IfModule>" . PHP_EOL . "<IfModule !mod_access_compat>" . PHP_EOL . "<IfModule mod_authz_host.c>" . PHP_EOL . "Deny from all" . PHP_EOL . "</IfModule>" . PHP_EOL . "</IfModule>" . PHP_EOL . "<IfModule mod_access_compat>" . PHP_EOL . "Deny from all" . PHP_EOL . "</IfModule>" . PHP_EOL . "</Files>" );
				}
				if ( ! file_exists( $folder . '/index.php' ) ) {
					file_put_contents( $folder . '/index.php', "<?php" . PHP_EOL . "header( \$_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found' );" . PHP_EOL . "header( 'Status: 404 Not Found' );" . PHP_EOL );
				}
			}
		}

		//Create do not backup file for this folder
		if ( $donotbackup && ! file_exists( $folder . '/.donotbackup' ) ) {
			file_put_contents( $folder . '/.donotbackup', __( 'BackWPup will not backup folders and its sub folders when this file is inside.', 'backwpup' ) );
		}

		return '';
	}
}
