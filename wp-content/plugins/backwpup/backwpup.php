<?php
/**
 * Plugin Name: BackWPup
 * Plugin URI: http://backwpup.com
 * Description: WordPress Backup Plugin
 * Author: Inpsyde GmbH
 * Author URI: http://inpsyde.com
 * Version: 3.3.1
 * Text Domain: backwpup
 * Domain Path: /languages/
 * Network: true
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
 */

/**
 *	Copyright (C) 2012-2016 Inpsyde GmbH (email: info@inpsyde.com)
 *
 *	This program is free software; you can redistribute it and/or
 *	modify it under the terms of the GNU General Public License
 *	as published by the Free Software Foundation; either version 2
 *	of the License, or (at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program; if not, write to the Free Software
 *	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

if ( ! class_exists( 'BackWPup' ) ) {

	// Don't activate on anything less than PHP 5.2.7 or WordPress 3.1
	if ( version_compare( PHP_VERSION, '5.2.7', '<' ) || version_compare( get_bloginfo( 'version' ), '3.8', '<' ) || ! function_exists( 'spl_autoload_register' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		deactivate_plugins( __FILE__ );
		die( 'BackWPup requires PHP version 5.2.7 with spl extension or greater and WordPress 3.8 or greater.' );
	}

	//Start Plugin
	if ( function_exists( 'add_filter' ) ) {
		add_action( 'plugins_loaded', array( 'BackWPup', 'get_instance' ), 11 );
	}

	/**
	 * Main BackWPup Plugin Class
	 */
	final class BackWPup {

		private static $instance = NULL;
		private static $plugin_data = array();
		private static $autoload = array();
		private static $destinations = array();
		private static $registered_destinations = array();
		private static $job_types = array();
		private static $wizards = array();

		/**
		 * Set needed filters and actions and load
		 */
		private function __construct() {

			// Nothing else matters if we're not on the main site
			if ( ! is_main_site() ) {
				return;
			}
			//auto loader
			spl_autoload_register( array( $this, 'autoloader' ) );
			//start upgrade if needed
			if ( get_site_option( 'backwpup_version' ) != self::get_plugin_data( 'Version' ) ) {
				BackWPup_Install::activate();
			}
			//load pro features
			if ( class_exists( 'BackWPup_Pro' ) ) {
				BackWPup_Pro::get_instance();
			}
			//WP-Cron
			if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
				if ( ! empty( $_GET[ 'backwpup_run' ] ) && class_exists( 'BackWPup_Job' ) ) {
					//early disable caches
					BackWPup_Job::disable_caches();
					//add action for running jobs in wp-cron.php
					add_action( 'wp_loaded', array( 'BackWPup_Cron', 'cron_active' ), PHP_INT_MAX );
				} else {
					//add cron actions
					add_action( 'backwpup_cron', array( 'BackWPup_Cron', 'run' ) );
					add_action( 'backwpup_check_cleanup', array( 'BackWPup_Cron', 'check_cleanup' ) );
				}
				//if in cron the rest is not needed
				return;
			}
			//deactivation hook
			register_deactivation_hook( __FILE__, array( 'BackWPup_Install', 'deactivate' ) );
			//Admin bar
			if ( get_site_option( 'backwpup_cfg_showadminbar', FALSE ) ) {
				add_action( 'init', array( 'BackWPup_Adminbar', 'get_instance' ) );
			}
			//only in backend
			if ( is_admin() && class_exists( 'BackWPup_Admin' ) ) {
				BackWPup_Admin::get_instance();
			}
			//work with wp-cli
			if ( defined( 'WP_CLI' ) && WP_CLI && method_exists( 'WP_CLI', 'add_command' ) ) {
				WP_CLI::add_command( 'backwpup', 'BackWPup_WP_CLI' );
			}
		}

		/**
		 * @static
		 *
		 * @return self
		 */
		public static function get_instance() {

			if (NULL === self::$instance) {
				self::$instance = new self;
			}
			return self::$instance;
		}


		private function __clone() {}

		/**
		 * get information about the Plugin
		 *
		 * @param string $name Name of info to get or NULL to get all
		 * @return string|array
		 */
		public static function get_plugin_data( $name = NULL ) {

			if ( $name )
				$name = strtolower( trim( $name ) );

			if ( empty( self::$plugin_data ) ) {
				self::$plugin_data = get_file_data( __FILE__, array(
																   'name'        => 'Plugin Name',
																   'version'     => 'Version'
															  ), 'plugin' );
				self::$plugin_data[ 'name' ]        = trim( self::$plugin_data[ 'name' ] );
				//set some extra vars
				self::$plugin_data[ 'basename' ] = plugin_basename( dirname( __FILE__ ) );
				self::$plugin_data[ 'mainfile' ] = __FILE__ ;
				self::$plugin_data[ 'plugindir' ] = untrailingslashit( dirname( __FILE__ ) ) ;
				self::$plugin_data[ 'hash' ] = get_site_option( 'backwpup_cfg_hash' );
				if ( empty( self::$plugin_data[ 'hash' ] ) || strlen( self::$plugin_data[ 'hash' ] ) < 6 || strlen( self::$plugin_data[ 'hash' ] ) > 12 ) {
					self::$plugin_data[ 'hash' ] = substr( md5( md5( __FILE__ ) ), 14, 6 );
					update_site_option( 'backwpup_cfg_hash', self::$plugin_data[ 'hash' ] );
				}
				if ( defined( 'WP_TEMP_DIR' ) && is_dir( WP_TEMP_DIR ) ) {
					self::$plugin_data['temp'] = str_replace( '\\', '/', get_temp_dir() ) . 'backwpup-' . self::$plugin_data['hash'] . '/';
				} else {
					$upload_dir                = wp_upload_dir();
					self::$plugin_data['temp'] = str_replace( '\\', '/', $upload_dir['basedir'] ) . '/backwpup-' . self::$plugin_data['hash'] . '-temp/';
				}
				self::$plugin_data[ 'running_file' ] = self::$plugin_data[ 'temp' ] . 'backwpup-working.php';
				self::$plugin_data[ 'url' ] = plugins_url( '', __FILE__ );
				self::$plugin_data[ 'cacert' ] = apply_filters( 'backwpup_cacert_bundle', ABSPATH . WPINC . '/certificates/ca-bundle.crt' );
				//get unmodified WP Versions
				include ABSPATH . WPINC . '/version.php';
				/** @var $wp_version string */
				self::$plugin_data[ 'wp_version' ] = $wp_version;
				//Build User Agent
				self::$plugin_data[ 'user-agent' ] = self::$plugin_data[ 'name' ].'/' . self::$plugin_data[ 'version' ] . '; WordPress/' . self::$plugin_data[ 'wp_version' ] . '; ' . home_url();
			}

			if ( ! empty( $name ) )
				return self::$plugin_data[ $name ];
			else
				return self::$plugin_data;
		}


		/**
		 * include not existing classes automatically
		 *
		 * @param string $class Class to load from file
		 */
		private function autoloader( $class ) {

			//BackWPup classes auto load
			if ( strstr( strtolower( $class ), 'backwpup_' ) ) {
				$dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR;
				$class_file_name = 'class-' . str_replace( array( 'backwpup_', '_' ), array( '', '-' ), strtolower( $class ) ) . '.php';
				if ( strstr( strtolower( $class ), 'backwpup_pro' ) ) {
					$dir .=  'pro' . DIRECTORY_SEPARATOR;
					$class_file_name = str_replace( 'pro-','', $class_file_name );
				}
				if ( file_exists( $dir . $class_file_name ) )
					require $dir . $class_file_name;
			}

			// namespaced PSR-0
			if ( ! empty( self::$autoload ) ) {
				$pos = strrpos( $class, '\\' );
				if ( $pos !== FALSE ) {
					$class_path = str_replace( '\\', DIRECTORY_SEPARATOR, substr( $class, 0, $pos ) ) . DIRECTORY_SEPARATOR . str_replace( '_', DIRECTORY_SEPARATOR, substr( $class, $pos + 1 ) ) . '.php';
					foreach ( self::$autoload as $prefix => $dir ) {
						if ( $class === strstr( $class, $prefix ) ) {
							if ( file_exists( $dir . DIRECTORY_SEPARATOR . $class_path ) )
								require $dir . DIRECTORY_SEPARATOR . $class_path;
						}
					}
				} // Single class file
				elseif ( ! empty( self::$autoload[ $class ] ) && is_file( self::$autoload[ $class ] ) ) {
					require self::$autoload[ $class ];
				}
			}

			//Google SDK Auto loading
			$classPath = explode( '_', $class );
			if ( $classPath[0] == 'Google' ) {
				if ( count( $classPath ) > 3 ) {
					$classPath = array_slice( $classPath, 0, 3 );
				}
				$filePath = self::get_plugin_data( 'plugindir' ) . '/vendor/' . implode( '/', $classPath ) . '.php';
				if ( file_exists( $filePath ) ) {
					require $filePath;
				}
			}

		}

		/**
		 * Load Plugin Translation
		 *
		 * @return bool Text domain loaded
		 */
		public static function load_text_domain() {

			if ( is_textdomain_loaded( 'backwpup' ) ) {
				return TRUE;
			}

			return load_plugin_textdomain( 'backwpup', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Get a array of instances for Backup Destination's
		 *
		 * @param $key string Key of Destination where get class instance from
		 * @return array BackWPup_Destinations
		 */
		public static function get_destination( $key ) {

			$key  = strtoupper( $key );

			if ( isset( self::$destinations[ $key ] ) && is_object( self::$destinations[ $key ] ) )
				return self::$destinations[ $key ];

			$reg_dests = self::get_registered_destinations();
			if ( ! empty( $reg_dests[ $key ][ 'class' ] ) ) {
				self::$destinations[ $key ] = new $reg_dests[ $key ][ 'class' ];
			} else {
				return NULL;
			}

			return self::$destinations[ $key ];
		}

		/**
		 * Get a array of registered Destination's for Backups
		 *
		 * @return array BackWPup_Destinations
		 */
		public static function get_registered_destinations() {

			//only run it one time
			if ( ! empty( self::$registered_destinations ) )
				return self::$registered_destinations;

			//add BackWPup Destinations
			// to folder
			self::$registered_destinations[ 'FOLDER' ] 	= array(
								'class' => 'BackWPup_Destination_Folder',
								'info'	=> array(
									'ID'        	=> 'FOLDER',
									'name'       	=> __( 'Folder', 'backwpup' ),
									'description' 	=> __( 'Backup to Folder', 'backwpup' ),
								),
								'can_sync' => FALSE,
								'needed' => array(
									'php_version'	=> '',
									'functions'	=> array(),
									'classes'	=> array()
								),
								'autoload'	=> array()
							);
			// backup with mail
			self::$registered_destinations[ 'EMAIL' ] 	= array(
								'class' => 'BackWPup_Destination_Email',
								'info'	=> array(
									'ID'        	=> 'EMAIL',
									'name'       	=> __( 'Email', 'backwpup' ),
									'description' 	=> __( 'Backup sent via email', 'backwpup' ),
								),
								'can_sync' => FALSE,
								'needed' => array(
									'php_version'	=> '5.2.4',
									'functions'	=> array(),
									'classes'	=> array()
								),
								'autoload'	=> array()
							);
			// backup to ftp
			self::$registered_destinations[ 'FTP' ] 	= array(
								'class' => 'BackWPup_Destination_Ftp',
								'info'	=> array(
									'ID'        	=> 'FTP',
									'name'       	=> __( 'FTP', 'backwpup' ),
									'description' 	=> __( 'Backup to FTP', 'backwpup' ),
								),
								'can_sync' => FALSE,
								'needed' => array(
									'mphp_version'	=> '',
									'functions'	=> array( 'ftp_nb_fput' ),
									'classes'	=> array()
								),
								'autoload'	=> array()
							);
			// backup to dropbox
			self::$registered_destinations[ 'DROPBOX' ] 	= array(
								'class' => 'BackWPup_Destination_Dropbox',
								'info'	=> array(
									'ID'        	=> 'DROPBOX',
									'name'       	=> __( 'Dropbox', 'backwpup' ),
									'description' 	=> __( 'Backup to Dropbox', 'backwpup' ),
								),
								'can_sync' => FALSE,
								'needed' => array(
									'php_version'	=> '',
									'functions'	=> array( 'curl_exec' ),
									'classes'	=> array()
								),
								'autoload'	=> array()
							);
			// Backup to S3
			self::$registered_destinations[ 'S3' ] 	= array(
								'class' => 'BackWPup_Destination_S3',
								'info'	=> array(
									'ID'        	=> 'S3',
									'name'       	=> __( 'S3 Service', 'backwpup' ),
									'description' 	=> __( 'Backup to an S3 Service', 'backwpup' ),
								),
								'can_sync' => FALSE,
								'needed' => array(
									'php_version'	=> '5.3.3',
									'functions'	=> array( 'curl_exec' ),
									'classes'	=> array( 'XMLWriter' )
								),
								'autoload'	=> array( 	'Aws\\Common' => dirname( __FILE__ ) .'/vendor',
														'Aws\\S3' => dirname( __FILE__ ) .'/vendor',
														'Symfony\\Component\\EventDispatcher'  => dirname( __FILE__ ) . '/vendor',
														'Guzzle' => dirname( __FILE__ ) . '/vendor'	)
							);
			// backup to MS Azure
			self::$registered_destinations[ 'MSAZURE' ] 	= array(
								'class' => 'BackWPup_Destination_MSAzure',
								'info'	=> array(
									'ID'        	=> 'MSAZURE',
									'name'       	=> __( 'MS Azure', 'backwpup' ),
									'description' 	=> __( 'Backup to Microsoft Azure (Blob)', 'backwpup' ),
								),
								'can_sync' => FALSE,
								'needed' => array(
									'php_version'	=> '5.3.2',
									'functions'	=> array(),
									'classes'	=> array()
								),
								'autoload'	=> array( 'WindowsAzure' => dirname( __FILE__ ) . '/vendor' )
							);
			// backup to Rackspace Cloud
			self::$registered_destinations[ 'RSC' ] 	= array(
								'class' => 'BackWPup_Destination_RSC',
								'info'	=> array(
									'ID'        	=> 'RSC',
									'name'       	=> __( 'RSC', 'backwpup' ),
									'description' 	=> __( 'Backup to Rackspace Cloud Files', 'backwpup' ),
								),
								'can_sync' => FALSE,
								'needed' => array(
									'php_version'	=> '5.3.3',
									'functions'	=> array( 'curl_exec' ),
									'classes'	=> array()
								),
								'autoload'	=> array( 'OpenCloud' => dirname( __FILE__ ) . '/vendor',
													  'Guzzle' => dirname( __FILE__ ) . '/vendor',
													  'Psr' => dirname( __FILE__ ) . '/vendor' )
							);
			// backup to Sugarsync
			self::$registered_destinations[ 'SUGARSYNC' ] 	= array(
								'class' => 'BackWPup_Destination_SugarSync',
								'info'	=> array(
									'ID'        	=> 'SUGARSYNC',
									'name'       	=> __( 'SugarSync', 'backwpup' ),
									'description' 	=> __( 'Backup to SugarSync', 'backwpup' ),
								),
								'can_sync' => FALSE,
								'needed' => array(
									'php_version'	=> '',
									'functions'	=> array( 'curl_exec' ),
									'classes'	=> array()
								),
								'autoload'	=> array()
							);

			//Hook for adding Destinations like above
			self::$registered_destinations = apply_filters( 'backwpup_register_destination', self::$registered_destinations );

			//check BackWPup Destinations
			foreach ( self::$registered_destinations as $dest_key => $dest ) {
				self::$registered_destinations[ $dest_key ][ 'error'] = '';
				// check PHP Version
				if ( ! empty( $dest[ 'needed' ][ 'php_version' ] ) && version_compare( PHP_VERSION, $dest[ 'needed' ][ 'php_version' ], '<' ) ) {
					self::$registered_destinations[ $dest_key ][ 'error' ] .= sprintf( __( 'PHP Version %1$s is to low, you need Version %2$s or above.', 'backwpup' ), PHP_VERSION, $dest[ 'needed' ][ 'php_version' ] ) . ' ';
					self::$registered_destinations[ $dest_key ][ 'class' ] = NULL;
				}
				//check functions exists
				if ( ! empty( $dest[ 'needed' ][ 'functions' ] ) ) {
					foreach ( $dest[ 'needed' ][ 'functions' ] as $function_need ) {
						if ( ! function_exists( $function_need ) ) {
							self::$registered_destinations[ $dest_key ][ 'error' ] .= sprintf( __( 'Missing function "%s".', 'backwpup' ), $function_need ) . ' ';
							self::$registered_destinations[ $dest_key ][ 'class' ] = NULL;
						}
					}
				}
				//check classes exists
				if ( ! empty( $dest[ 'needed' ][ 'classes' ] ) ) {
					foreach ( $dest[ 'needed' ][ 'classes' ] as $class_need ) {
						if ( ! class_exists( $class_need ) ) {
							self::$registered_destinations[ $dest_key ][ 'error' ] .= sprintf( __( 'Missing class "%s".', 'backwpup' ), $class_need ) . ' ';
							self::$registered_destinations[ $dest_key ][ 'class' ] = NULL;
						}
					}
				}
				//add class/namespace to auto load
				if ( ! empty( self::$registered_destinations[ $dest_key ][ 'class' ] ) && ! empty( self::$registered_destinations[ $dest_key ][ 'autoload' ] ) )
					self::$autoload = array_merge( self::$autoload, self::$registered_destinations[ $dest_key ][ 'autoload' ] );

			}

			return self::$registered_destinations;
		}


		/**
		 * Gets a array of instances from Job types
		 *
		 * @return array BackWPup_JobTypes
		 */
		public static function get_job_types() {

			if ( !empty( self::$job_types ) )
				return self::$job_types;

			self::$job_types[ 'DBDUMP' ]	= new BackWPup_JobType_DBDump;
			self::$job_types[ 'FILE' ] 		= new BackWPup_JobType_File;
			self::$job_types[ 'WPEXP' ] 	= new BackWPup_JobType_WPEXP;
			self::$job_types[ 'WPPLUGIN' ]  = new BackWPup_JobType_WPPlugin;
			self::$job_types[ 'DBCHECK' ]   = new BackWPup_JobType_DBCheck;

			self::$job_types = apply_filters( 'backwpup_job_types', self::$job_types );

			//remove types can't load
			foreach ( self::$job_types as $key => $job_type ) {
				if ( empty( $job_type ) || ! is_object( $job_type ) )
					unset( self::$job_types[ $key ] );
			}

			return self::$job_types;
		}


		/**
		 * Gets a array of instances from Wizards
		 *
		 * @return array BackWPup_Pro_Wizards
		 */
		public static function get_wizards() {

			if ( !empty( self::$wizards ) )
				return self::$wizards;

			self::$wizards  = apply_filters( 'backwpup_pro_wizards', self::$wizards );

			//remove wizards can't load
			foreach ( self::$wizards as $key => $wizard ) {
				if ( empty( $wizard ) || ! is_object( $wizard ) )
					unset( self::$wizards[ $key ] );
			}

			return self::$wizards;

		}

	}

}
