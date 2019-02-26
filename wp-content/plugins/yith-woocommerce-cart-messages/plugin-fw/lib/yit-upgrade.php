<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YIT_Upgrade' ) ) {
	/**
	 * YIT Upgrade
	 *
	 * Notify and Update plugin
	 *
	 * @class       YIT_Upgrade
	 * @package     YITH
	 * @since       1.0
	 * @author      Your Inspiration Themes
	 * @see         WP_Updater Class
	 */
	class YIT_Upgrade {

		/**
		 * @var string XML notifier update
		 */
		protected $_remote_url = 'https://update.yithemes.com/plugin-xml.php';

		/**
		 * @var string api server url
		 */
		protected $_package_url = 'https://yithemes.com';

		/**
		 * @var array The registered plugins
		 */
		protected $_plugins = array();

		/**
		 * @var YIT_Upgrade The main instance
		 */
		protected static $_instance;

		/**
		 * Construct
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 */
		public function __construct() {
			add_filter( 'upgrader_pre_download', array( $this, 'upgrader_pre_download' ), 10, 3 );
			add_action( 'update-custom_upgrade-plugin-multisite', array( $this, 'upgrade_plugin_multisite' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			$is_debug_enabled = defined( 'YIT_LICENCE_DEBUG' ) && YIT_LICENCE_DEBUG;
			if ( $is_debug_enabled ) {
				$this->_package_url = defined( 'YIT_LICENCE_DEBUG_LOCALHOST' ) ? YIT_LICENCE_DEBUG_LOCALHOST : 'http://dev.yithemes.com';
				add_filter( 'block_local_requests', '__return_false' );
			}

			add_action( 'install_plugins_pre_plugin-information', array(
				$this,
				'show_changelog_for_premium_plugins'
			) );
			add_action( 'wp_ajax_yith_plugin_fw_get_premium_changelog', array(
				$this,
				'show_changelog_for_premium_plugins'
			) );
		}

		/**
		 * show changelog for premium plugins
		 *
		 * @since 3.0.14
		 */
		public function show_changelog_for_premium_plugins() {
			if ( isset( $_GET['plugin'] ) && isset( $_GET['section'] ) && 'changelog' === $_GET['section'] ) {
				$plugin_init = $_GET['plugin'];
				if ( isset( $this->_plugins[ $plugin_init ] ) ) {
					// this is YITH Premium Plugin
					if ( ! empty( $this->_plugins[ $plugin_init ]['info']['changelog'] ) ) {
						$plugin_name = $this->_plugins[ $plugin_init ]['info']['Name'];
						$changelog   = $this->_plugins[ $plugin_init ]['info']['changelog'];
						$template    = YIT_CORE_PLUGIN_TEMPLATE_PATH . '/upgrade/changelog.php';
						if ( file_exists( $template ) ) {
							include( $template );
						}
						die();
					}
					$error    = __( 'An unexpected error occurred, please try again later. Thanks!', 'yith-plugin-fw' );
					$template = YIT_CORE_PLUGIN_TEMPLATE_PATH . '/upgrade/error.php';
					if ( file_exists( $template ) ) {
						include( $template );
					} else {
						echo "<p>$error</p>";
					}
					die();
				}
			}
		}

		/**
		 * Main plugin Instance
		 *
		 * @param $plugin_slug | string The plugin slug
		 * @param $plugin_init | string The plugin init file
		 *
		 * @return void
		 *
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register( $plugin_slug, $plugin_init ) {

			if ( ! function_exists( 'get_plugins' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$plugins     = get_plugins();
			$plugin_info = $plugins[ $plugin_init ];

			$plugin = $this->_plugins[ $plugin_init ] = array(
				'info' => $plugin_info,
				'slug' => $plugin_slug,
			);

			$transient = 'yith_register_' . md5( $plugin_slug );
			if ( apply_filters( 'yith_register_delete_transient', false ) ) {
				delete_transient( $transient );
			}
			$info = get_transient( $transient );
			if ( false === $info || apply_filters( 'yith_register_delete_transient', false ) ) {
				$xml         = $this->get_remote_url( $plugin );
				$remote_xml  = wp_remote_get( $xml );

				$error = false;
				if ( ! is_wp_error( $remote_xml ) && isset( $remote_xml['response']['code'] ) && '200' == $remote_xml['response']['code'] ) {
					$plugin_remote_info = @simplexml_load_string( $remote_xml['body'] );
					if ( $plugin_remote_info ) {
						$info['Latest']    = (string) $plugin_remote_info->latest;
						$info['changelog'] = (string) $plugin_remote_info->changelog;
						if ( is_multisite() && current_user_can( 'update_plugins' ) ) {
							YIT_Plugin_Licence()->check( $plugin_slug, false );
						}
						set_transient( $transient, $info, DAY_IN_SECONDS );
					} else {
						$error = true;
						error_log( sprintf( 'SimpleXML error in %s:%s [plugin slug: %s]',
							__FILE__, __FUNCTION__, $plugin_slug ) );
					}
				} else {
					$error = true;
				}

				if ( $error ) {
					// If error, set empty value in the transient to prevent multiple requests
					$info = array( 'Latest' => '', 'changelog' => '' );
					set_transient( $transient, $info, HOUR_IN_SECONDS );
				}
			}

			$this->_plugins[ $plugin_init ]['info']['Latest']    = $info['Latest'];
			$this->_plugins[ $plugin_init ]['info']['changelog'] = $info['changelog'];

			/* === HOOKS === */
			if ( ! is_multisite() || is_plugin_active_for_network( $plugin_init ) ) {
				add_action( 'load-plugins.php', array( $this, 'remove_wp_plugin_update_row' ), 25 );
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
			} else if ( is_multisite() && current_user_can( 'update_plugins' ) ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'multisite_updater_script' ) );
			}
		}

		/**
		 * Add the multisite updater scripts
		 *
		 * @return void
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function multisite_updater_script() {
			$update_url = $changelogs = $details_url = array();
			$strings    = array(
				'new_version' => __( 'There is a new version of %plugin_name% available.', 'yith-plugin-fw' ),
				'latest'      => __( 'View version %latest% details.', 'yith-plugin-fw' ),
				'unavailable' => __( 'Automatic update is unavailable for this plugin,', 'yith-plugin-fw' ),
				'activate'    => __( 'please <a href="%activate_link%">activate</a> your copy of %plugin_name%.', 'yith-plugin-fw' ),
				'update_now'  => __( 'Update now.', 'yith-plugin-fw' )

			);

			foreach ( $this->_plugins as $init => $info ) {
				$update_url[ $init ]  = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin-multisite&plugin=' ) . $init, 'upgrade-plugin-multisite_' . $init );
				$details_url[ $init ] = admin_url( 'admin-ajax.php?action=yith_plugin_fw_get_premium_changelog&tab=plugin-information&plugin=' . $init . '&section=changelog&TB_iframe=true&width=640&height=662' );
			}

			$localize_script_args = array(
				'registered'             => $this->_plugins,
				'activated'              => YIT_Plugin_Licence()->get_activated_products(),
				'licence_activation_url' => YIT_Plugin_Licence()->get_licence_activation_page_url(),
				'update_url'             => $update_url,
				'details_url'            => $details_url,
				'strings'                => $strings,
			);
			$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			yit_enqueue_script( 'yit-multisite-updater', YIT_CORE_PLUGIN_URL . '/assets/js/multisite-updater' . $suffix . '.js', array( 'jquery' ), false, true );

			wp_localize_script( 'yit-multisite-updater', 'plugins', $localize_script_args );
		}

		public function admin_enqueue_scripts() {
			global $pagenow;
			if ( 'plugins.php' === $pagenow ) {
				wp_enqueue_style( 'yit-upgrader', YIT_CORE_PLUGIN_URL . '/assets/css/yit-upgrader.css' );
			}
		}

		/**
		 * Retrive the zip package file
		 *
		 * @param bool $reply Whether to bail without returning the package. Default false.
		 * @param string $package The package file name.
		 * @param \WP_Upgrader $upgrader WP_Upgrader instance.
		 *
		 * @return string | The download file
		 *
		 * @since    1.0
		 * @see      wp-admin/includes/class-wp-upgrader.php
		 * @access   public
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function upgrader_pre_download( $reply, $package, $upgrader ) {
			$plugin  = false;
			$is_bulk = $upgrader->skin instanceof Bulk_Plugin_Upgrader_Skin;

			if ( ! $is_bulk ) {
				$plugin = isset( $upgrader->skin->plugin ) ? $upgrader->skin->plugin : false;
			} else {
				//Bulk action upgrade
				$action_url = parse_url( $upgrader->skin->options['url'] );
				parse_str( rawurldecode( htmlspecialchars_decode( $action_url['query'] ) ), $output );
				$plugins = isset( $output['plugins'] ) ? $output['plugins'] : '';
				$plugins = explode( ',', $plugins );
				foreach ( $plugins as $plugin_init ) {
					$to_upgrade = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_init );
					if ( $to_upgrade['Name'] == $upgrader->skin->plugin_info['Name'] ) {
						$plugin = $plugin_init;
					}
				}
			}

			/**
			 * It isn't YITH Premium plugins, please wordpress update it for me!
			 */
			if ( ! $plugin ) {
				return $reply;
			}

			$plugin_info = YIT_Plugin_Licence()->get_product( $plugin );

			/**
			 * False ? It isn't YITH Premium plugins, please wordpress update it for me!
			 */
			if ( false === $plugin_info ) {
				return $reply;
			}

			$licence    = YIT_Plugin_Licence()->get_licence();
			$product_id = $plugin_info['product_id'];
			$args       = array(
				'email'       => $licence[ $product_id ]['email'],
				'licence_key' => $licence[ $product_id ]['licence_key'],
				'product_id'  => $plugin_info['product_id'],
				'secret_key'  => $plugin_info['secret_key'],
				'instance'    => YIT_Plugin_Licence()->get_home_url(),
				'wc-api'      => 'download-api',
				'request'     => 'download'
			);

			if ( ! preg_match( '!^(http|https|ftp)://!i', $package ) && file_exists( $package ) ) {
				//Local file or remote?
				return $package;
			}

			if ( empty( $package ) ) {
				return new WP_Error( 'no_package', $upgrader->strings['no_package'] );
			}

			$upgrader->skin->feedback( 'downloading_package', __( 'YITH Repository', 'yith-plugin-fw' ) );

			$download_file = $this->_download_url( $package, $args );

			/**
			 * Regenerate update_plugins transient
			 */
			$this->force_regenerate_update_transient();

			if ( is_wp_error( $download_file ) ) {
				return new WP_Error( 'download_failed', $upgrader->strings['download_failed'], $download_file->get_error_message() );
			}

			return $download_file;
		}

		/**
		 * Retrive the temp filename
		 *
		 * @param string $url The package url
		 * @param string $body The post data fields
		 * @param int $timeout Execution timeout (default: 300)
		 *
		 * @return string | The temp filename
		 *
		 * @since    1.0
		 * @see      wp-admin/includes/class-wp-upgrader.php
		 * @access   protected
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		protected function _download_url( $url, $body, $timeout = 300 ) {

			//WARNING: The file is not automatically deleted, The script must unlink() the file.
			if ( ! $url ) {
				return new WP_Error( 'http_no_url', __( 'Invalid URL Provided.', 'yit' ) );
			}

			$tmpfname = wp_tempnam( $url );

			$args = array(
				'timeout'  => $timeout,
				'stream'   => true,
				'filename' => $tmpfname,
				'body'     => $body
			);

			if ( ! $tmpfname ) {
				return new WP_Error( 'http_no_file', __( 'Could not create Temporary file.', 'yit' ) );
			}

			$response = wp_safe_remote_post( $url, $args );

			if ( is_wp_error( $response ) ) {
				unlink( $tmpfname );

				return $response;
			}

			if ( 200 != wp_remote_retrieve_response_code( $response ) ) {
				unlink( $tmpfname );

				return new WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );
			}

			$content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );

			if ( $content_md5 ) {
				$md5_check = verify_file_md5( $tmpfname, $content_md5 );
				if ( is_wp_error( $md5_check ) ) {
					unlink( $tmpfname );

					return $md5_check;
				}
			}

			return $tmpfname;
		}

		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @return object Main instance
		 *
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Delete the update plugins transient
		 *
		 * @return void
		 *
		 * @since  1.0
		 * @see    update_plugins transient and pre_set_site_transient_update_plugins hooks
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function force_regenerate_update_transient() {
			delete_site_transient( 'update_plugins' );
		}

		/**
		 * Check for plugins update
		 *
		 * If a new plugin version is available set it in the pre_set_site_transient_update_plugins hooks
		 *
		 * @param mixed $transient | update_plugins transient value
		 * @param bool $save | Default: false. Set true to regenerate the update_transient plugins
		 *
		 * @return mixed $transient | The new update_plugins transient value
		 *
		 * @since  1.0
		 * @see    update_plugins transient and pre_set_site_transient_update_plugins hooks
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function check_update( $transient, $save = false ) {

			foreach ( $this->_plugins as $init => $plugin ) {
				$plugin_slug = $this->_plugins[ $init ]['slug'];
				$xml         = $this->get_remote_url( $plugin );
				$remote_xml  = wp_remote_get( $xml );

				if ( ! is_wp_error( $remote_xml ) && isset( $remote_xml['response']['code'] ) && '200' == $remote_xml['response']['code'] ) {

					$plugin_remote_info = @simplexml_load_string( $remote_xml['body'] );

					if ( $plugin_remote_info ) {

						if ( version_compare( $plugin_remote_info->latest, $plugin['info']['Version'], '>' ) && ! isset( $transient->response[ $init ] ) ) {

							$package = YIT_Plugin_Licence()->check( $init ) ? $this->_package_url : null;

							$obj                          = new stdClass();
							$obj->slug                    = (string) $init;
							$obj->new_version             = (string) $plugin_remote_info->latest;
							$obj->changelog               = (string) $plugin_remote_info->changelog;
							$obj->package                 = $package;
							$obj->plugin                  = $init;
							$transient->response[ $init ] = $obj;
						}

					} else {
						error_log( sprintf( 'SimpleXML error in %s:%s [plugin slug: %s]',
							__FILE__, __FUNCTION__, $plugin_slug ) );

						return $transient;
					}
				}
			}

			if ( $save ) {
				set_site_transient( 'update_plugins', $transient );
			}

			return $transient;
		}

		/**
		 * Add the plugin update row in plugin page
		 *
		 * @return void
		 * @fire "in_theme_update_message-{$init}" action
		 *
		 * @since    1.0
		 * @see      after_plugin_row_{$init} action
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_update_row() {

			$current = get_site_transient( 'update_plugins' );
			$init    = str_replace( 'after_plugin_row_', '', current_filter() );

			if ( ! isset( $current->response[ $init ] ) ) {
				return false;
			}

			/**
			 * stdClass Object
			 */
			$r = $current->response[ $init ];

			$changelog_id = str_replace( array( '/', '.php', '.' ), array( '-', '', '-' ), $init );
			$details_url  = self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $init . '&section=changelog&TB_iframe=true&width=640&height=662' );

			/**
			 * @see wp_plugin_update_rows() in wp-single\wp-admin\includes\update.php
			 */
			$wp_list_table = _get_list_table( 'WP_MS_Themes_List_Table' );

			if ( is_network_admin() || ! is_multisite() || true ) {
				global $wp_version;
				$is_wp_4_6 = version_compare( $wp_version, '4.6', '>=' );

				echo '<tr class="plugin-update-tr' . ( is_plugin_active( $init ) ? ' active' : '' ) . '"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange">';

				echo '<div class="update-message' . ( $is_wp_4_6 ? ' notice inline notice-warning notice-alt' : '' ) . '">';

				echo( $is_wp_4_6 ? '<p>' : '' );

				if ( ! current_user_can( 'update_plugins' ) ) {
					printf( __( 'There is a new version of %1$s available. <a href="%2$s" class="thickbox yit-changelog-button open-plugin-details-modal" title="%3$s">View version %4$s details</a>.', 'yith-plugin-fw' ), $this->_plugins[ $init ]['info']['Name'], esc_url( $details_url ), esc_attr( $this->_plugins[ $init ]['info']['Name'] ), $r->new_version );
				} elseif ( is_plugin_active_for_network( $init ) ) {
					printf( __( 'There is a new version of %1$s available. <a href="%2$s" class="thickbox yit-changelog-button open-plugin-details-modal" title="%3$s">View version %4$s details</a>. <em>You have to activate the plugin on a single site of the network to benefit from automatic updates.</em>', 'yith-plugin-fw' ), $this->_plugins[ $init ]['info']['Name'], esc_url( $details_url ), esc_attr( $this->_plugins[ $init ]['info']['Name'] ), $r->new_version );
				} elseif ( empty( $r->package ) ) {
					printf( __( 'There is a new version of %1$s available. <a href="%2$s" class="thickbox yit-changelog-button open-plugin-details-modal" title="%3$s">View version %4$s details</a>. <em>Automatic update is unavailable for this plugin, please <a href="%5$s" title="License activation">activate</a> your copy of %6s.</em>', 'yith-plugin-fw' ), $this->_plugins[ $init ]['info']['Name'], esc_url( $details_url ), esc_attr( $this->_plugins[ $init ]['info']['Name'] ), $r->new_version, YIT_Plugin_Licence()->get_licence_activation_page_url(), $this->_plugins[ $init ]['info']['Name'] );
				} else {
					printf( __( 'There is a new version of %1$s available. <a href="%2$s" class="thickbox yit-changelog-button open-plugin-details-modal" title="%3$s">View version %4$s details</a> or <a href="%5$s">update now</a>.', 'yith-plugin-fw' ), $this->_plugins[ $init ]['info']['Name'], esc_url( $details_url ), esc_attr( $this->_plugins[ $init ]['info']['Name'] ), $r->new_version, wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $init, 'upgrade-plugin_' . $init ) );
				}

				echo( $is_wp_4_6 ? '</p>' : '' );

				/**
				 * Fires at the end of the update message container in each
				 * row of the themes list table.
				 *
				 * The dynamic portion of the hook name, `$theme_key`, refers to
				 * the theme slug as found in the WordPress.org themes repository.
				 *
				 * @since Wordpress 3.1.0
				 * }
				 */
				do_action( "in_theme_update_message-{$init}", $this->_plugins[ $init ], $r->changelog, $changelog_id );

				echo '</div></td></tr>';
			}
		}

		/**
		 * Remove the standard plugin_update_row
		 *
		 * Remove the standard plugin_update_row and Add a custom plugin update row in plugin page.
		 *
		 * @return void
		 * @fire "in_theme_update_message-{$init}" action
		 *
		 * @since    1.0
		 * @see      after_plugin_row_{$init} action
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function remove_wp_plugin_update_row() {
			foreach ( $this->_plugins as $init => $plugin ) {
				remove_action( "after_plugin_row_{$init}", 'wp_plugin_update_row', 10 );
				add_action( "after_plugin_row_{$init}", array( $this, 'plugin_update_row' ) );
				//add_action( "in_theme_update_message-{$init}", array( $this, 'in_theme_update_message' ), 10, 3 );
			}
		}

		public function in_theme_update_message( $plugin, $changelog, $changelog_id, $echo = true ) {
			$res = "<div id='{$changelog_id}' class='yit-plugin-changelog-wrapper'>
                    <div class='yit-plugin-changelog'>
                        <h2 class='yit-plugin-changelog-title'>{$plugin['info']['Name']} - Changelog</h2>
                        <p>{$changelog}</p>
                    </div>
                </div>";

			if ( $echo ) {
				echo $res;
			} else {
				return $res;
			}
		}

		/**
		 * Auto-Update Plugin in multisite
		 *
		 * Manage the non standard upgrade-plugin-multisite action
		 *
		 * @return void
		 *
		 * @since    1.0
		 * @see      upgrade-plugin action
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function upgrade_plugin_multisite() {

			$plugin = isset( $_REQUEST['plugin'] ) ? trim( $_REQUEST['plugin'] ) : '';
			$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';

			if ( 'upgrade-plugin-multisite' != $action ) {
				wp_die( __( 'You can\'t update the plugins for this site.', 'yith-plugin-fw' ) );
			}

			if ( ! current_user_can( 'update_plugins' ) ) {
				wp_die( __( 'You do not have sufficient permissions to update the plugins for this site.', 'yith-plugin-fw' ) );
			}

			$this->check_update( get_site_transient( 'update_plugins' ), true );

			check_admin_referer( 'upgrade-plugin-multisite_' . $plugin );

			$title        = __( 'Update Plugin', 'yith-plugin-fw' );
			$parent_file  = 'plugins.php';
			$submenu_file = 'plugins.php';

			wp_enqueue_script( 'updates' );
			require_once( ABSPATH . 'wp-admin/admin-header.php' );

			$nonce = 'upgrade-plugin-multisite_' . $plugin;
			$url   = 'update.php?action=upgrade-plugin-multisite&plugin=' . urlencode( $plugin );

			$upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact( 'title', 'nonce', 'url', 'plugin' ) ) );
			$upgrader->upgrade( $plugin );

			include( ABSPATH . 'wp-admin/admin-footer.php' );
		}

		/**
		 * Retreive the remote url with query string args
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return string the remote url
		 */
		public function get_remote_url( $plugin_info ) {

			$license = $is_membership_license = false;
			$slug    = isset( $plugin_info['slug'] ) ? $plugin_info['slug'] : false;

			if( function_exists( 'YIT_Plugin_Licence' ) && false !== $slug ){
				//Get license for YITH Plugins
				$enabled_license = YIT_Plugin_Licence()->get_licence();

				if( isset( $enabled_license[ $slug ]['activated'] ) && false !== $enabled_license[ $slug ]['activated'] ){
					if( isset( $enabled_license[ $slug ]['licence_key'] ) ){
						$license = $enabled_license[ $slug ]['licence_key'];
					}

					if( isset( $enabled_license[ $slug ]['is_membership'] ) ){
						$is_membership_license = $enabled_license[ $slug ]['is_membership'];
					}
				}
			}

			$args = array(
				'plugin'                => $slug,
				'instance'              => md5( $_SERVER['SERVER_NAME'] ),
				'license'               => $license,
				'is_membership_license' => $is_membership_license,
				'server_ip'             => $_SERVER['SERVER_ADDR'],
				'version'               => isset( $plugin_info['info']['Version'] ) ? $plugin_info['info']['Version'] : '1.0.0'
			);

			$args = apply_filters( 'yith_get_remove_url_args', $args );

			$url = add_query_arg( $args, $this->_remote_url );

			return $url;
		}
	}
}

if ( ! function_exists( 'YIT_Upgrade' ) ) {
	/**
	 * Main instance of plugin
	 *
	 * @return YIT_Upgrade
	 * @since  1.0
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 */
	function YIT_Upgrade() {
		return YIT_Upgrade::instance();
	}
}

/**
 * Instance a YIT_Upgrade object
 */
YIT_Upgrade();
