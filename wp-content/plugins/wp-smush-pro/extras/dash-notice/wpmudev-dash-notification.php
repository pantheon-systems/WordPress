<?php
/////////////////////////////////////////////////////////////////////////
/* -------- WPMU DEV Dashboard Notice - Aaron Edwards (Incsub) ------- */
/* This provides notices of available updates for our premium products */
if ( ! class_exists( 'WPMUDEV_Dashboard_Notice4' ) ) {
	class WPMUDEV_Dashboard_Notice4 {

		var $version = '4.2';
		var $screen_id = false;
		var $product_name = false;
		var $product_update = false;
		var $theme_pack = 128;
		var $server_url = 'https://premium.wpmudev.org/api/dashboard/v1/';
		var $update_count = 0;

		function __construct() {
			add_action( 'init', array( &$this, 'init' ) );
			add_action( 'plugins_loaded', array( &$this, 'remove_older' ), 5 );
		}

		function remove_older() {
			global $WPMUDEV_Dashboard_Notice3;

			//remove 3.0 notices
			if ( is_object( $WPMUDEV_Dashboard_Notice3 ) ) {
				remove_action( 'init', array( $WPMUDEV_Dashboard_Notice3, 'init' ) );
				remove_action( 'plugins_loaded', array( $WPMUDEV_Dashboard_Notice3, 'init' ) );
			} else if ( method_exists( 'WPMUDEV_Dashboard_Notice3', 'init' ) ) { //if class is not in global (some projects included inside a method), we have to use a hacky way to remove the filter
				$this->deregister_hook( 'init', 'WPMUDEV_Dashboard_Notice3', 'init', 10 );
				$this->deregister_hook( 'plugins_loaded', 'WPMUDEV_Dashboard_Notice3', 'init', 10 );
			}

			//remove version 2.0
			if ( method_exists( 'WPMUDEV_Dashboard_Notice', 'init' ) ) {
				$this->deregister_hook( 'init', 'WPMUDEV_Dashboard_Notice', 'init', 10 );
				$this->deregister_hook( 'plugins_loaded', 'WPMUDEV_Dashboard_Notice', 'init', 10 );
			}

			//remove version 1.0
			remove_action( 'admin_notices', 'wdp_un_check', 5 );
			remove_action( 'network_admin_notices', 'wdp_un_check', 5 );
		}

		/* Adapted from: https://github.com/herewithme/wp-filters-extras/ - Copyright 2012 Amaury Balmer - amaury@beapi.fr */
		function deregister_hook( $hook_name, $class_name, $method_name, $priority ) {
			global $wp_filter;

			// Take only filters on right hook name and priority
			if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
				return false;
			}

			// Loop on filters registered
			foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
				// Test if filter is an array ! (always for class/method)
				if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
					// Test if object is a class, class and method is equal to param !
					if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) == $class_name && $filter_array['function'][1] == $method_name ) {
						if ( class_exists( 'WP_Hook' ) ) { //introduced in WP 4.7 https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
							unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $unique_id ] );
						} else {
							unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
						}
						return true;
					}
				}
			}

			return false;
		}

		function init() {
			global $wpmudev_un;

			if ( class_exists( 'WPMUDEV_Dashboard' ) || ( isset( $wpmudev_un->version ) && version_compare( $wpmudev_un->version, '3.4', '<' ) ) ) {
				return;
			}

			// Schedule update cron on main site only
			if ( is_main_site() ) {
				if ( ! wp_next_scheduled( 'wpmudev_scheduled_jobs' ) ) {
					wp_schedule_event( time(), 'twicedaily', 'wpmudev_scheduled_jobs' );
				}

				add_action( 'wpmudev_scheduled_jobs', array( $this, 'updates_check' ) );
			}
			add_action( 'delete_site_transient_update_plugins', array(
				&$this,
				'updates_check'
			) ); //refresh after upgrade/install
			add_action( 'delete_site_transient_update_themes', array(
				&$this,
				'updates_check'
			) ); //refresh after upgrade/install

			if ( is_admin() && current_user_can( 'install_plugins' ) ) {

				add_action( 'site_transient_update_plugins', array( &$this, 'filter_plugin_count' ) );
				add_action( 'site_transient_update_themes', array( &$this, 'filter_theme_count' ) );
				add_filter( 'plugins_api', array(
					&$this,
					'filter_plugin_info'
				), 101, 3 ); //run later to work with bad autoupdate plugins
				add_filter( 'themes_api', array(
					&$this,
					'filter_plugin_info'
				), 101, 3 ); //run later to work with bad autoupdate plugins
				add_action( 'load-plugins.php', array( &$this, 'filter_plugin_rows' ), 21 ); //make sure it runs after WP's
				add_action( 'load-themes.php', array( &$this, 'filter_plugin_rows' ), 21 ); //make sure it runs after WP's
				add_action( 'core_upgrade_preamble', array( &$this, 'disable_checkboxes' ) );
				add_action( 'activated_plugin', array( &$this, 'set_activate_flag' ) );
				add_action( 'wp_ajax_wdpun-changelog', array( &$this, 'popup_changelog_ajax' ) );
				add_action( 'wp_ajax_wdpun-dismiss', array( &$this, 'dismiss_ajax' ) );

				//if dashboard is installed but not activated
				if ( file_exists( WP_PLUGIN_DIR . '/wpmudev-updates/update-notifications.php' ) ) {
					if ( ! get_site_option( 'wdp_un_autoactivated' ) ) {
						//include plugin API if necessary
						if ( ! function_exists( 'activate_plugin' ) ) {
							require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
						}
						$result = activate_plugin( '/wpmudev-updates/update-notifications.php', network_admin_url( 'admin.php?page=wpmudev' ), is_multisite() );
						if ( ! is_wp_error( $result ) ) { //if autoactivate successful don't show notices
							update_site_option( 'wdp_un_autoactivated', 1 );

							return;
						}
					}

					add_action( 'admin_print_styles', array( &$this, 'notice_styles' ) );
					add_action( 'admin_print_footer_scripts', array( &$this, 'notice_scripts' ) );
					add_action( 'all_admin_notices', array( &$this, 'activate_notice' ), 5 );
				} else { //dashboard not installed at all
					if ( get_site_option( 'wdp_un_autoactivated' ) ) {
						update_site_option( 'wdp_un_autoactivated', 0 );//reset flag when dashboard is deleted
					}
					add_action( 'admin_print_styles', array( &$this, 'notice_styles' ) );
					add_action( 'admin_print_footer_scripts', array( &$this, 'notice_scripts' ) );
					add_action( 'all_admin_notices', array( &$this, 'install_notice' ), 5 );
				}
			}
		}

		function is_allowed_screen() {
			global $wpmudev_notices;
			$screen          = get_current_screen();
			if ($screen && is_object($screen)) $this->screen_id = $screen->id;

			//Show special message right after plugin activation
			if ( in_array( $this->screen_id, array(
					'plugins',
					'plugins-network'
				) ) && ( isset( $_GET['activate'] ) || isset( $_GET['activate-multi'] ) )
			) {
				$activated = get_site_option( 'wdp_un_activated_flag' );
				if ( $activated === false ) {
					$activated = 1;
				} //on first encounter of new installed notice show
				if ( $activated ) {
					if ( $activated >= 2 ) {
						update_site_option( 'wdp_un_activated_flag', 0 );
					} else {
						update_site_option( 'wdp_un_activated_flag', 2 );
					}

					return true;
				}
			}

			//check dismiss flag
			$dismissed = get_site_option( 'wdp_un_dismissed' );
			if ( $dismissed && $dismissed > strtotime( '-1 week' ) ) {
				return false;
			}

			//always show on certain core pages if updates are available
			$updates = get_site_option( 'wdp_un_updates_available' );
			if ( is_array( $updates ) && count( $updates ) ) {
				$this->update_count = count( $updates );
				if ( in_array( $this->screen_id, array(
					'update-core',
					'update-core-network'
				) ) ) {
					return true;
				}
			}

			//check our registered plugins for hooks
			if ( isset( $wpmudev_notices ) && is_array( $wpmudev_notices ) ) {
				foreach ( $wpmudev_notices as $product ) {
					if ( isset( $product['screens'] ) && is_array( $product['screens'] ) && in_array( $this->screen_id, $product['screens'] ) ) {
						$this->product_name = $product['name'];
						//if this plugin needs updating flag it
						if ( isset( $product['id'] ) && isset( $updates[ $product['id'] ] ) ) {
							$this->product_update = true;
						}

						return true;
					}
				}
			}

			return false;
		}

		function auto_install_url() {
			$function = is_multisite() ? 'network_admin_url' : 'admin_url';

			return wp_nonce_url( $function( "update.php?action=install-plugin&plugin=install_wpmudev_dash" ), "install-plugin_install_wpmudev_dash" );
		}

		function activate_url() {
			$function = is_multisite() ? 'network_admin_url' : 'admin_url';

			return wp_nonce_url( $function( 'plugins.php?action=activate&plugin=wpmudev-updates%2Fupdate-notifications.php' ), 'activate-plugin_wpmudev-updates/update-notifications.php' );
		}

		function install_notice() {
			if ( ! $this->is_allowed_screen() ) return; ?>

			<div class="notice wdpun-notice" style="display: none;">

			<input type="hidden" name="msg_id" value="<?php _e( 'install', 'wpmudev' ); ?>" />

			<div class="wdpun-notice-logo"></div>
			<div class="wdpun-notice-message">
				<?php
				if ( $this->product_name ) {
					if ( $this->product_update ) {
						printf( __( 'Important updates are available for <strong>%s</strong>. Install the free WPMU DEV Dashboard plugin now for updates and support!', 'wpmudev' ), esc_html( $this->product_name ) );
					} else {
						printf( __( '<strong>%s</strong> is almost ready - install the free WPMU DEV Dashboard plugin for updates and support!', 'wpmudev' ), esc_html( $this->product_name ) );
					}

				} else if ( $this->update_count ) {
					_e( 'Important updates are available for your WPMU DEV plugins/themes. Install the free WPMU DEV Dashboard plugin now for updates and support!', 'wpmudev' );
				} else {
					_e( 'Almost ready - install the free WPMU DEV Dashboard plugin for updates and support!', 'wpmudev' );
				}
				?>
			</div><!-- end wdpun-notice-message -->
			<div class="wdpun-notice-cta">
				<a href="<?php echo $this->auto_install_url(); ?>"
				   class="wdpun-button wdpun-button-small"><?php _e( 'Install Plugin', 'wpmudev' ); ?></a>

				<button class="wdpun-button wdpun-button-notice-dismiss"
				        data-msg="<?php _e( 'Saving...', 'wpmudev' ); ?>">
					<?php _e( 'Dismiss', 'wpmudev' ); ?>
				</button>
			</div><!-- end wdpun-notice-cta -->

			</div><!-- end notice wdpun-notice -->

			<?php

			return;
		}

		function activate_notice() {
			if ( ! $this->is_allowed_screen() ) return; ?>

			<div class="notice wdpun-notice" style="display: none;">

			<input type="hidden" name="msg_id" value="<?php _e( 'activate', 'wpmudev' ); ?>" />

			<div class="wdpun-notice-logo"><span></span></div>
			<div class="wdpun-notice-message">
				<?php
				if ( $this->product_name ) {
					if ( $this->product_update ) {
						printf( __( 'Important updates are available for <strong>%s</strong>. Activate the WPMU DEV Dashboard to update now!', 'wpmudev' ), esc_html( $this->product_name ) );
					} else {
						printf( __( 'Just one more step to enable updates and support for <strong>%s</strong>!', 'wpmudev' ), esc_html( $this->product_name ) );
					}

				} else if ( $this->update_count ) {
					_e( 'Important updates are available for your WPMU DEV plugins/themes. Activate the WPMU DEV Dashboard to update now!', 'wpmudev' );
				} else {
					_e( "Just one more step - activate the WPMU DEV Dashboard plugin and you're all done!", 'wpmudev' );
				}
				?>
			</div><!-- end wdpun-notice-message -->
			<div class="wdpun-notice-cta">
				<a href="<?php echo $this->activate_url(); ?>"
				   class="wdpun-button wdpun-button-small"><?php _e( 'Activate WPMU DEV Dashboard', 'wpmudev' ); ?></a>

				<button class="wdpun-button wdpun-button-notice-dismiss"
				        data-msg="<?php _e( 'Saving...', 'wpmudev' ); ?>">
					<?php _e( 'Dismiss', 'wpmudev' ); ?>
				</button>
			</div><!-- end wdpun-notice-cta -->

			</div><!-- end notice wdpun-notice -->

			<?php

			return;
		}

		function notice_styles() {
			if ( !$this->is_allowed_screen() ) return;
			?>
			<style type="text/css" media="all">
				.cf:after{content:"";display:table;clear:both}@media only screen and (min-width: 1200px){.hide-to-large{display:none}}@media only screen and (min-width: 1140px){.hide-to-desktop{display:none}}.wrap>.wdpun-notice.notice,.wrap #header>.wdpun-notice.notice{width:100%}.wrap #header>.wdpun-notice.notice{box-shadow:none}.wdpun-notice *,.wdpun-notice *:after,.wdpun-notice *:before{box-sizing:border-box}.wdpun-notice.notice{background:#fff;border:1px solid #E5E5E5;border-radius:6px;box-shadow:0 1px 1px 0 rgba(0,0,0,0.05);clear:both;display:block;font:400 13px/20px "Open Sans",Arial,sans-serif;overflow:hidden;margin:10px 20px 20px 0;min-height:80px;padding:0;position:relative;text-align:center;z-index:1}.wdpun-notice.notice.loading:before{background-color:rgba(255,255,255,0.7);bottom:0;content:attr(data-message);font-size:22px;font-weight:600;left:0;line-height:80px;position:absolute;right:0;text-align:center;top:0;z-index:5}.wdpun-notice.notice.loading>div{-webkit-filter:blur(2px);filter:blur(2px)}.wdpun-notice-logo{background-color:transparent;background-image:url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjU2cHgiIGhlaWdodD0iNTZweCIgdmlld0JveD0iMCAwIDU2IDU2IiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPCEtLSBHZW5lcmF0b3I6IFNrZXRjaCAzLjguMyAoMjk4MDIpIC0gaHR0cDovL3d3dy5ib2hlbWlhbmNvZGluZy5jb20vc2tldGNoIC0tPgogICAgPHRpdGxlPndwbXVkZXYtc3ZnPC90aXRsZT4KICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPgogICAgPGRlZnM+PC9kZWZzPgogICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9ImlQYWQtUHJvLUxhbmRzY2FwZSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTIzLjAwMDAwMCwgLTIyLjAwMDAwMCkiPgogICAgICAgICAgICA8ZyBpZD0id3BtdWRldi1zdmciIHRyYW5zZm9ybT0idHJhbnNsYXRlKDIzLjAwMDAwMCwgMjIuMDAwMDAwKSI+CiAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMjgsNTYgQzEyLjUzNTg3MSw1NiAwLDQzLjQ2NDEyOSAwLDI4IEMwLDEyLjUzNjc3NDIgMTIuNTM1ODcxLDAgMjgsMCBDNDMuNDY0MTI5LDAgNTYsMTIuNTM2Nzc0MiA1NiwyOCBDNTYsNDMuNDY0MTI5IDQzLjQ2NDEyOSw1NiAyOCw1NiBMMjgsNTYgWiIgaWQ9IkZpbGwtMSIgZmlsbD0iI0ZGRDAwNSI+PC9wYXRoPgogICAgICAgICAgICAgICAgPHBhdGggZD0iTTUxLjQ4Mzg3MSwyOCBDNTEuNDgzODcxLDQwLjk2OTQxOTQgNDAuOTY5NDE5NCw1MS40ODM4NzEgMjgsNTEuNDgzODcxIEMxNS4wMzA1ODA2LDUxLjQ4Mzg3MSA0LjUxNjEyOTAzLDQwLjk2OTQxOTQgNC41MTYxMjkwMywyOCBDNC41MTYxMjkwMywxNS4wMzA1ODA2IDE1LjAzMDU4MDYsNC41MTYxMjkwMyAyOCw0LjUxNjEyOTAzIEM0MC45Njk0MTk0LDQuNTE2MTI5MDMgNTEuNDgzODcxLDE1LjAzMDU4MDYgNTEuNDgzODcxLDI4IiBpZD0iRmlsbC0zIiBmaWxsPSIjMzMzMzMzIj48L3BhdGg+CiAgICAgICAgICAgICAgICA8cGF0aCBkPSJNNDIuNzc4ODUxNiwzMi42NzY2MzIzIEw0Mi43Nzg4NTE2LDEyLjUzMTA4MzkgTDM5LjUzMDg1MTYsMTUuMDU4MzA5NyBMMzkuNTMwODUxNiwzMi42NzY2MzIzIEwzOS41MjcyMzg3LDMyLjY3NjYzMjMgQzM5LjUyNzIzODcsMzMuNzA4MTE2MSAzOC43ODI5ODA2LDM0LjU0NDUwMzIgMzcuODY2MjA2NSwzNC41NDQ1MDMyIEMzNi45NDY3MjI2LDM0LjU0NDUwMzIgMzYuMjAyNDY0NSwzMy43MDgxMTYxIDM2LjIwMjQ2NDUsMzIuNjc2NjMyMyBMMzYuMjA3ODgzOSwyMy4zMjM3MjkgQzM2LjIwNzg4MzksMjAuMjc0NDM4NyAzNC4wMDU4MTk0LDE3LjgwMTQwNjUgMzEuMjkwNzIyNiwxNy44MDE0MDY1IEMyOC41NzM4MTk0LDE3LjgwMTQwNjUgMjYuMzcyNjU4MSwyMC4yNzQ0Mzg3IDI2LjM3MjY1ODEsMjMuMzIzNzI5IEwyNi4zNzI2NTgxLDMyLjY3NjYzMjMgQzI2LjM3MjY1ODEsMzMuNzA4MTE2MSAyNS42Mjg0LDM0LjU0NDUwMzIgMjQuNzA4OTE2MSwzNC41NDQ1MDMyIEMyMy43OTIxNDE5LDM0LjU0NDUwMzIgMjMuMDQ1MTc0MiwzMy43MDgxMTYxIDIzLjA0NTE3NDIsMzIuNjc2NjMyMyBMMjMuMDUyNCwyMy4zMjM3MjkgQzIzLjA1MjQsMjAuMjc0NDM4NyAyMC44NTEyMzg3LDE3LjgwMTQwNjUgMTguMTM2MTQxOSwxNy44MDE0MDY1IEMxNS40MjEwNDUyLDE3LjgwMTQwNjUgMTMuMjE4OTgwNiwyMC4yNzQ0Mzg3IDEzLjIxODk4MDYsMjMuMzIzNzI5IEwxMy4yMjA3ODcxLDIzLjMyMzcyOSBMMTMuMjIwNzg3MSw0My40NjkyNzc0IEwxNi40Njg3ODcxLDQwLjk0MTE0ODQgTDE2LjQ3MjQsMjMuMzIzNzI5IEMxNi40NzI0LDIyLjI5MjI0NTIgMTcuMjE2NjU4MSwyMS40NTU4NTgxIDE4LjEzNjE0MTksMjEuNDU1ODU4MSBDMTkuMDU0NzIyNiwyMS40NTU4NTgxIDE5Ljc5OTg4MzksMjIuMjkyMjQ1MiAxOS43OTk4ODM5LDIzLjMyMzcyOSBMMTkuNzkyNjU4MSwzMi42NzY2MzIzIEMxOS43OTI2NTgxLDM1LjcyNTkyMjYgMjEuOTkzODE5NCwzOC4xOTg5NTQ4IDI0LjcwODkxNjEsMzguMTk4OTU0OCBDMjcuNDI0MDEyOSwzOC4xOTg5NTQ4IDI5LjYyNTE3NDIsMzUuNzI1OTIyNiAyOS42MjUxNzQyLDMyLjY3NjYzMjMgTDI5LjYyNDI3MSwzMi42NzY2MzIzIEwyOS42MjY5ODA2LDIzLjMyMzcyOSBDMjkuNjI2OTgwNiwyMi4yOTIyNDUyIDMwLjM3MjE0MTksMjEuNDU1ODU4MSAzMS4yOTA3MjI2LDIxLjQ1NTg1ODEgQzMyLjIwODQsMjEuNDU1ODU4MSAzMi45NTI2NTgxLDIyLjI5MjI0NTIgMzIuOTUyNjU4MSwyMy4zMjM3MjkgTDMyLjk0NzIzODcsMzIuNjc2NjMyMyBDMzIuOTQ3MjM4NywzNS43MjU5MjI2IDM1LjE0OTMwMzIsMzguMTk4OTU0OCAzNy44NjYyMDY1LDM4LjE5ODk1NDggQzQwLjU4MDQsMzguMTk4OTU0OCA0Mi43ODA2NTgxLDM1LjcyNTkyMjYgNDIuNzgwNjU4MSwzMi42NzY2MzIzIEw0Mi43Nzg4NTE2LDMyLjY3NjYzMjMgTDQyLjc3ODg1MTYsMzIuNjc2NjMyMyBaIiBpZD0iRmlsbC01IiBmaWxsPSIjRjlFQzFDIj48L3BhdGg+CiAgICAgICAgICAgIDwvZz4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPg==);background-repeat:no-repeat;background-position:50% 50%;display:block;height:56px;margin:10px auto 0;width:56px}.wdpun-notice .wdpun-notice-message{color:#23282D;display:block;font-family:"Open Sans",Arial,sans-serif;font-size:13px;line-height:20px;padding:10px}.wdpun-notice .wdpun-notice-message strong{font-weight:600}.wdpun-notice .wdpun-button{background:#00ACCA;border:1px solid #0087B9;border-radius:2px;color:#fff;cursor:pointer;display:block;font-weight:500;font-size:16px;height:auto;line-height:18px;margin:0;padding:10px 20px;text-decoration:none;-webkit-transition:color 0.3s, opacity 0.3s, background 0.3s;transition:color 0.3s, opacity 0.3s, background 0.3s;white-space:nowrap}.wdpun-notice .wdpun-button:hover:not(:focus):not(:active){background-color:#0093B1;color:#fff}.wdpun-notice .wdpun-button:focus,.wdpun-notice .wdpun-button:active{background:#0082a1;background:-webkit-linear-gradient(top, #0082a1 0%, #008fae 100%);background:linear-gradient(to bottom, #0082a1 0%, #008fae 100%);filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#0082a1', endColorstr='#008fae',GradientType=0 );border-color:transparent;color:#fff;outline-color:transparent;outline-style:none}.wdpun-notice .wdpun-button-small{padding:5px 15px}.wdpun-notice .wdpun-button-notice-dismiss{background:transparent;border:none;border-radius:0;color:#C5C5C5;padding:0;text-transform:none;-webkit-transition:color 0.3s;transition:color 0.3s}.wdpun-notice .wdpun-button-notice-dismiss:hover:not(:focus):not(:active),.wdpun-notice .wdpun-button-notice-dismiss:active,.wdpun-notice .wdpun-button-notice-dismiss:focus{background:transparent;color:#666}.wdpun-notice .wdpun-notice-cta{border-top:1px solid #E5E5E5;background:#F8F8F8;clear:both;display:block;padding:15px 20px;position:relative;text-align:center;white-space:nowrap}.wdpun-notice .wdpun-notice-cta .wdpun-button{vertical-align:middle}.wdpun-notice .wdpun-notice-cta .wdpun-button-notice-dismiss{margin:10px auto 0}.wdpun-notice .wdpun-notice-cta input[type="email"]{line-height:20px;margin:0;max-width:320px;min-width:50px;padding-right:0;padding-left:0;text-align:center;vertical-align:middle}@media only screen and (min-width: 601px){.wdpun-notice.notice{text-align:left}.wdpun-notice-logo{float:left;margin:10px}.wdpun-notice .wdpun-notice-message{margin-top:5px;margin-left:76px;padding:10px 20px 10px 10px}.wdpun-notice .wdpun-button{display:inline-block;font-size:14px}}@media only screen and (min-width: 783px){.wdpun-notice .wdpun-notice-cta .wdpun-button-notice-dismiss{margin-top:0}.wdpun-notice button+button,.wdpun-notice .wdpun-button+button,.wdpun-notice button+.wdpun-button,.wdpun-notice .wdpun-button+.wdpun-button,.wdpun-notice a+button,.wdpun-notice a+.wdpun-button{margin-left:10px}}@media only screen and (min-width: 961px){.wdpun-notice.notice{display:table}.wdpun-notice-logo{border-radius:0;height:auto;margin:0;min-height:80px;min-width:80px;width:5%}.wdpun-notice .wdpun-notice-logo,.wdpun-notice .wdpun-notice-message,.wdpun-notice .wdpun-notice-cta{cursor:default;display:table-cell;float:none;vertical-align:middle}.wdpun-notice .wdpun-notice-message{margin-top:0;max-width:100%;min-height:80px;width:75%}.wdpun-notice .wdpun-notice-cta{border-left:1px solid #E5E5E5;border-top:none;padding:0 30px;width:20%}}@media only screen and (min-width: 1140px){.wdpun-notice .wdpun-button{font-size:13px}}
			</style>
			<?php
		}

		function notice_scripts() {
			if ( !$this->is_allowed_screen() ) return;
			?>
			<script type="text/javascript">
				!function($){function n(){function n(){a.fadeIn(500)}function i(){a.fadeTo(100,0,function(){a.slideUp(100,function(){a.remove()})})}function t(n,t){"0"!==e?(a.attr("data-message",t),a.addClass("saving"),s.action=n,jQuery.post(window.ajaxurl,s,i)):i()}var a=jQuery(".wdpun-notice"),e=a.find("input[name=msg_id]").val(),o=a.find(".wdpun-button-notice-dismiss"),s={};s.msg_id=e,o.click(function(n){n.preventDefault(),t("wdpun-dismiss",o.data("msg"))}),window.setTimeout(n,500)}$(n)}(jQuery);
			</script>
			<?php
		}

		function get_id_plugin( $plugin_file ) {
			return get_file_data( $plugin_file, array(
				'name'    => 'Plugin Name',
				'id'      => 'WDP ID',
				'version' => 'Version'
			) );
		}

		//simple check for updates
		function updates_check() {
			global $wp_version;
			$local_projects = array();

			//----------------------------------------------------------------------------------//
			//plugins directory
			//----------------------------------------------------------------------------------//
			$plugins_root = WP_PLUGIN_DIR;
			if ( empty( $plugins_root ) ) {
				$plugins_root = ABSPATH . 'wp-content/plugins';
			}

			$plugins_dir  = @opendir( $plugins_root );
			$plugin_files = array();
			if ( $plugins_dir ) {
				while ( ( $file = readdir( $plugins_dir ) ) !== false ) {
					if ( substr( $file, 0, 1 ) == '.' ) {
						continue;
					}
					if ( is_dir( $plugins_root . '/' . $file ) ) {
						$plugins_subdir = @ opendir( $plugins_root . '/' . $file );
						if ( $plugins_subdir ) {
							while ( ( $subfile = readdir( $plugins_subdir ) ) !== false ) {
								if ( substr( $subfile, 0, 1 ) == '.' ) {
									continue;
								}
								if ( substr( $subfile, - 4 ) == '.php' ) {
									$plugin_files[] = "$file/$subfile";
								}
							}
						}
					} else {
						if ( substr( $file, - 4 ) == '.php' ) {
							$plugin_files[] = $file;
						}
					}
				}
			}
			@closedir( $plugins_dir );
			@closedir( $plugins_subdir );

			if ( $plugins_dir && ! empty( $plugin_files ) ) {
				foreach ( $plugin_files as $plugin_file ) {
					if ( is_readable( "$plugins_root/$plugin_file" ) ) {

						unset( $data );
						$data = $this->get_id_plugin( "$plugins_root/$plugin_file" );

						if ( isset( $data['id'] ) && ! empty( $data['id'] ) ) {
							$local_projects[ $data['id'] ]['type']     = 'plugin';
							$local_projects[ $data['id'] ]['version']  = $data['version'];
							$local_projects[ $data['id'] ]['filename'] = $plugin_file;
						}
					}
				}
			}

			//----------------------------------------------------------------------------------//
			// mu-plugins directory
			//----------------------------------------------------------------------------------//
			$mu_plugins_root = WPMU_PLUGIN_DIR;
			if ( empty( $mu_plugins_root ) ) {
				$mu_plugins_root = ABSPATH . 'wp-content/mu-plugins';
			}

			if ( is_dir( $mu_plugins_root ) && $mu_plugins_dir = @opendir( $mu_plugins_root ) ) {
				while ( ( $file = readdir( $mu_plugins_dir ) ) !== false ) {
					if ( substr( $file, - 4 ) == '.php' ) {
						if ( is_readable( "$mu_plugins_root/$file" ) ) {

							unset( $data );
							$data = $this->get_id_plugin( "$mu_plugins_root/$file" );

							if ( isset( $data['id'] ) && ! empty( $data['id'] ) ) {
								$local_projects[ $data['id'] ]['type']     = 'mu-plugin';
								$local_projects[ $data['id'] ]['version']  = $data['version'];
								$local_projects[ $data['id'] ]['filename'] = $file;
							}
						}
					}
				}
				@closedir( $mu_plugins_dir );
			}

			//----------------------------------------------------------------------------------//
			// wp-content directory
			//----------------------------------------------------------------------------------//
			$content_plugins_root = WP_CONTENT_DIR;
			if ( empty( $content_plugins_root ) ) {
				$content_plugins_root = ABSPATH . 'wp-content';
			}

			$content_plugins_dir  = @opendir( $content_plugins_root );
			$content_plugin_files = array();
			if ( $content_plugins_dir ) {
				while ( ( $file = readdir( $content_plugins_dir ) ) !== false ) {
					if ( substr( $file, 0, 1 ) == '.' ) {
						continue;
					}
					if ( ! is_dir( $content_plugins_root . '/' . $file ) ) {
						if ( substr( $file, - 4 ) == '.php' ) {
							$content_plugin_files[] = $file;
						}
					}
				}
			}
			@closedir( $content_plugins_dir );

			if ( $content_plugins_dir && ! empty( $content_plugin_files ) ) {
				foreach ( $content_plugin_files as $content_plugin_file ) {
					if ( is_readable( "$content_plugins_root/$content_plugin_file" ) ) {
						unset( $data );
						$data = $this->get_id_plugin( "$content_plugins_root/$content_plugin_file" );

						if ( isset( $data['id'] ) && ! empty( $data['id'] ) ) {
							$local_projects[ $data['id'] ]['type']     = 'drop-in';
							$local_projects[ $data['id'] ]['version']  = $data['version'];
							$local_projects[ $data['id'] ]['filename'] = $content_plugin_file;
						}
					}
				}
			}

			//----------------------------------------------------------------------------------//
			//themes directory
			//----------------------------------------------------------------------------------//
			$themes_root = WP_CONTENT_DIR . '/themes';
			if ( empty( $themes_root ) ) {
				$themes_root = ABSPATH . 'wp-content/themes';
			}

			$themes_dir   = @opendir( $themes_root );
			$themes_files = array();
			$local_themes = array();
			if ( $themes_dir ) {
				while ( ( $file = readdir( $themes_dir ) ) !== false ) {
					if ( substr( $file, 0, 1 ) == '.' ) {
						continue;
					}
					if ( is_dir( $themes_root . '/' . $file ) ) {
						$themes_subdir = @ opendir( $themes_root . '/' . $file );
						if ( $themes_subdir ) {
							while ( ( $subfile = readdir( $themes_subdir ) ) !== false ) {
								if ( substr( $subfile, 0, 1 ) == '.' ) {
									continue;
								}
								if ( substr( $subfile, - 4 ) == '.css' ) {
									$themes_files[] = "$file/$subfile";
								}
							}
						}
					} else {
						if ( substr( $file, - 4 ) == '.css' ) {
							$themes_files[] = $file;
						}
					}
				}
			}
			@closedir( $themes_dir );
			@closedir( $themes_subdir );

			if ( $themes_dir && ! empty( $themes_files ) ) {
				foreach ( $themes_files as $themes_file ) {

					//skip child themes
					if ( strpos( $themes_file, '-child' ) !== false ) {
						continue;
					}

					if ( is_readable( "$themes_root/$themes_file" ) ) {

						unset( $data );
						$data = $this->get_id_plugin( "$themes_root/$themes_file" );

						if ( isset( $data['id'] ) && ! empty( $data['id'] ) ) {
							$local_projects[ $data['id'] ]['type']     = 'theme';
							$local_projects[ $data['id'] ]['filename'] = substr( $themes_file, 0, strpos( $themes_file, '/' ) );

							//keep record of all themes for 133 themepack
							if ( $data['id'] == $this->theme_pack ) {
								$local_themes[ $themes_file ]['id']       = $data['id'];
								$local_themes[ $themes_file ]['filename'] = substr( $themes_file, 0, strpos( $themes_file, '/' ) );
								$local_themes[ $themes_file ]['version']  = $data['version'];
								//increment 133 theme pack version to lowest in all of them
								if ( isset( $local_projects[ $data['id'] ]['version'] ) && version_compare( $data['version'], $local_projects[ $data['id'] ]['version'], '<' ) ) {
									$local_projects[ $data['id'] ]['version'] = $data['version'];
								} else if ( ! isset( $local_projects[ $data['id'] ]['version'] ) ) {
									$local_projects[ $data['id'] ]['version'] = $data['version'];
								}
							} else {
								$local_projects[ $data['id'] ]['version'] = $data['version'];
							}
						}
					}
				}
			}
			update_site_option( 'wdp_un_local_themes', $local_themes );

			update_site_option( 'wdp_un_local_projects', $local_projects );

			//now check the API

			$projects   = array();
			$theme      = wp_get_theme();
			$ms_allowed = $theme->get_allowed();
			foreach ( $local_projects as $pid => $item ) {
				if ( ! empty( $blog_projects[ $pid ] ) ) { //not yet implemented
					// This project is activated on a blog!
					$active = true;
				} else {
					if ( is_multisite() ) {
						if ( 'theme' == $item['type'] ) {
							// If the theme is available on main site it's "active".
							$slug   = $item['filename'];
							$active = ! empty( $ms_allowed[ $slug ] );
						} else {
							$active = is_plugin_active_for_network( $item['filename'] );
						}
					} else {
						if ( 'theme' == $item['type'] ) {
							$slug   = $item['filename'];
							$active = ( $theme->stylesheet == $slug || $theme->template == $slug );
						} else {
							$active = is_plugin_active( $item['filename'] );
						}
					}
				}
				$extra = '';

				/**
				 * Collect extra data from individual plugins.
				 *
				 * @since  4.0.0
				 * @api    wpmudev_api_project_extra_data-$pid
				 *
				 * @param  string $extra Default extra data is an empty string.
				 */
				$extra = apply_filters( "wpmudev_api_project_extra_data-$pid", $extra );
				$extra = apply_filters( 'wpmudev_api_project_extra_data', $extra, $pid );

				$projects[ $pid ] = array(
					'version' => $item['version'],
					'active'  => $active ? true : false,
					'extra'   => $extra,
				);
			}

			//get WP/BP version string to help with support
			$wp_ver = is_multisite() ? "WordPress Multisite $wp_version" : "WordPress $wp_version";
			if ( defined( 'BP_VERSION' ) ) {
				$wp_ver .= ', BuddyPress ' . BP_VERSION;
			}

			//add blog count if multisite
			$blog_count = is_multisite() ? get_blog_count() : 1;

			$url = $this->server_url . 'updates';

			$options = array(
				'timeout'    => 15,
				'sslverify'  => false, // Many hosts have no updated CA bundle.
				'user-agent' => 'Dashboard Notification/' . $this->version
			);

			$options['body'] = array(
				'blog_count' => $blog_count,
				'wp_version' => $wp_ver,
				'projects'   => json_encode( $projects ),
				'domain'     => network_site_url(),
				'admin_url'  => network_admin_url(),
				'home_url'   => network_home_url(),
			);

			$response = wp_remote_post( $url, $options );
			if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$data = $response['body'];
				if ( $data != 'error' ) {
					$data = json_decode( $data, true );
					if ( is_array( $data ) ) {

						//we've made it here with no errors, now check for available updates
						$remote_projects = isset( $data['projects'] ) ? $data['projects'] : array();
						$updates         = array();

						//check for updates
						if ( is_array( $remote_projects ) ) {
							foreach ( $remote_projects as $id => $remote_project ) {
								if ( isset( $local_projects[ $id ] ) && is_array( $local_projects[ $id ] ) ) {
									//match
									$local_version  = $local_projects[ $id ]['version'];
									$remote_version = $remote_project['version'];

									if ( version_compare( $remote_version, $local_version, '>' ) ) {
										//add to array
										$updates[ $id ]                     = $local_projects[ $id ];
										$updates[ $id ]['url']              = $remote_project['url'];
										$updates[ $id ]['name']             = $remote_project['name'];
										$updates[ $id ]['version']          = $local_version;
										$updates[ $id ]['new_version']      = $remote_version;
										$updates[ $id ]['autoupdate']       = $remote_project['autoupdate'];
									}
								}
							}

							//record results
							update_site_option( 'wdp_un_updates_available', $updates );
						} else {
							return false;
						}
					}
				}
			}
		}

		function filter_plugin_info( $res, $action, $args ) {
			global $wp_version;
			$cur_wp_version = preg_replace( '/-.*$/', '', $wp_version );

			if ( ( $action == 'plugin_information' || $action == 'theme_information' ) && strpos( $args->slug, 'wpmudev_install' ) !== false ) {
				$string  = explode( '-', $args->slug );
				$id      = intval( $string[1] );
				$updates = get_site_option( 'wdp_un_updates_available' );
				//if in details iframe on update core page short-curcuit it
				if ( did_action( 'install_plugins_pre_plugin-information' ) && is_array( $updates ) && isset( $updates[ $id ] ) ) {
					$this->popup_changelog( $id );
				}

				$res                = new stdClass;
				$res->name          = $updates[ $id ]['name'];
				$res->slug          = sanitize_title( $updates[ $id ]['name'] );
				$res->version       = $updates[ $id ]['version'];
				$res->rating        = 100;
				$res->homepage      = $updates[ $id ]['url'];
				$res->download_link = '';
				$res->tested        = $cur_wp_version;

				return $res;
			}

			if ( $action == 'plugin_information' && strpos( $args->slug, 'install_wpmudev_dash' ) !== false ) {
				$res                = new stdClass;
				$res->name          = 'WPMU DEV Dashboard';
				$res->slug          = 'wpmu-dev-dashboard';
				$res->version       = '';
				$res->rating        = 100;
				$res->homepage      = 'https://premium.wpmudev.org/project/wpmu-dev-dashboard/';
				$res->download_link = $this->server_url . "download-dashboard";
				$res->tested        = $cur_wp_version;

				return $res;
			}

			return $res;
		}

		function filter_plugin_rows() {
			if ( ! current_user_can( 'update_plugins' ) ) {
				return;
			}

			//don't show on per site plugins list, just like core
			if ( is_multisite() && ! is_network_admin() ) { return; }

			$updates = get_site_option( 'wdp_un_updates_available' );
			if ( is_array( $updates ) && count( $updates ) ) {
				foreach ( $updates as $id => $plugin ) {
					if ( $plugin['autoupdate'] != '2' ) {
						if ( $plugin['type'] == 'theme' ) {
							remove_all_actions( 'after_theme_row_' . $plugin['filename'] );
							add_action( 'after_theme_row_' . $plugin['filename'], array( &$this, 'plugin_row' ), 99, 2 );
						} else {
							remove_all_actions( 'after_plugin_row_' . $plugin['filename'] );
							add_action( 'after_plugin_row_' . $plugin['filename'], array(
								&$this,
								'plugin_row'
							), 99, 2 );
						}
					}
				}
			}

			$local_themes = get_site_option( 'wdp_un_local_themes' );
			if ( is_array( $local_themes ) && count( $local_themes ) ) {
				foreach ( $local_themes as $id => $plugin ) {
					remove_all_actions( 'after_theme_row_' . $plugin['filename'] );
					//only add the notice if specific version is wrong
					if ( isset( $updates[ $this->theme_pack ] ) && version_compare( $plugin['version'], $updates[ $this->theme_pack ]['new_version'], '<' ) ) {
						add_action( 'after_theme_row_' . $plugin['filename'], array( &$this, 'themepack_row' ), 9, 2 );
					}
				}
			}
		}

		function filter_plugin_count( $value ) {
			global $wp_version;
			$cur_wp_version = preg_replace( '/-.*$/', '', $wp_version );

			//remove any conflicting slug local WPMU DEV plugins from WP update notifications
			$local_projects = get_site_option( 'wdp_un_local_projects' );
			if ( is_array( $local_projects ) && count( $local_projects ) ) {
				foreach ( $local_projects as $id => $plugin ) {
					if ( isset( $value->response[ $plugin['filename'] ] ) ) {
						unset( $value->response[ $plugin['filename'] ] );
					}
				}
			}

			$updates = get_site_option( 'wdp_un_updates_available' );
			if ( is_array( $updates ) && count( $updates ) ) {
				foreach ( $updates as $id => $plugin ) {
					if ( $plugin['type'] != 'theme' && $plugin['autoupdate'] != '2' ) {

						//build plugin class
						$object              = new stdClass;
						$object->url         = $plugin['url'];
						$object->slug        = "wpmudev_install-$id";
						$object->new_version = $plugin['new_version'];
						$object->package     = '';
						$object->tested      = $cur_wp_version;

						//add to class
						$value->response[ $plugin['filename'] ] = $object;
					}
				}
			}

			return $value;
		}

		function filter_theme_count( $value ) {

			$updates = get_site_option( 'wdp_un_updates_available' );
			if ( is_array( $updates ) && count( $updates ) ) {
				foreach ( $updates as $id => $theme ) {
					if ( $theme['type'] == 'theme' && $theme['autoupdate'] != '2' ) {

						$theme_slug = $theme['filename'];

						//build theme listing
						$value->response[ $theme_slug ]['theme']       = $theme['filename'];
						$value->response[ $theme_slug ]['url']         = admin_url( 'admin-ajax.php?action=wdpun-changelog&pid=' . $id );
						$value->response[ $theme_slug ]['new_version'] = $theme['new_version'];
						$value->response[ $theme_slug ]['package']     = '';
					}
				}
			}

			//filter 133 theme pack themes from the list unless update is available
			$local_themes = get_site_option( 'wdp_un_local_themes' );
			if ( is_array( $local_themes ) && count( $local_themes ) ) {
				foreach ( $local_themes as $id => $theme ) {
					$theme_slug = $theme['filename'];

					//add to count only if new version exists, otherwise remove
					if ( isset( $updates[ $theme['id'] ] ) && isset( $updates[ $theme['id'] ]['new_version'] ) && version_compare( $theme['version'], $updates[ $theme['id'] ]['new_version'], '<' ) ) {
						$value->response[ $theme_slug ]['new_version'] = $updates[ $theme['id'] ]['new_version'];
						$value->response[ $theme_slug ]['package']     = '';
					} else if ( isset( $value ) && isset( $value->response ) && isset( $theme_slug ) && isset( $value->response[ $theme_slug ] ) ) {
						unset( $value->response[ $theme_slug ] );
					}
				}
			}

			return $value;
		}

		function plugin_row( $file, $plugin_data ) {
			//get new version and update url
			$updates = get_site_option( 'wdp_un_updates_available' );
			if ( is_array( $updates ) && count( $updates ) ) {
				foreach ( $updates as $id => $plugin ) {
					if ( $plugin['filename'] == $file ) {
						$project_id = $id;
						$version    = $plugin['new_version'];
						$plugin_url = $plugin['url'];
						$autoupdate = $plugin['autoupdate'];
						$filename   = $plugin['filename'];
						$type       = $plugin['type'];
						break;
					}
				}
			} else {
				return false;
			}

			$plugins_allowedtags = array(
				'a'       => array( 'href' => array(), 'title' => array() ),
				'abbr'    => array( 'title' => array() ),
				'acronym' => array( 'title' => array() ),
				'code'    => array(),
				'em'      => array(),
				'strong'  => array()
			);
			$plugin_name         = wp_kses( $plugin_data['Name'], $plugins_allowedtags );

			$info_url = admin_url( 'admin-ajax.php?action=wdpun-changelog&pid=' . $project_id . '&TB_iframe=true&width=640&height=800' );
			if ( file_exists( WP_PLUGIN_DIR . '/wpmudev-updates/update-notifications.php' ) ) {
				$message    = "Activate WPMU DEV Dashboard";
				$action_url = $this->activate_url();
			} else { //dashboard not installed at all
				$message    = "Install WPMU DEV Dashboard";
				$action_url = $this->auto_install_url();
			}

			if ( current_user_can( 'update_plugins' ) ) {
				echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange"><div class="update-message notice inline notice-warning notice-alt"><p>';
				printf( 'There is a new version of %1$s available on WPMU DEV. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a> or <a href="%5$s">%6$s</a> to update.', $plugin_name, esc_url( $info_url ), esc_attr( $plugin_name ), $version, esc_url( $action_url ), $message );
				echo '</p></div></td></tr>';
			}
		}

		function themepack_row( $file, $plugin_data ) {

			//get new version and update url
			$updates = get_site_option( 'wdp_un_updates_available' );
			if ( isset( $updates[ $this->theme_pack ] ) ) {
				$plugin     = $updates[ $this->theme_pack ];
				$project_id = $this->theme_pack;
				$version    = $plugin['new_version'];
				$plugin_url = $plugin['url'];
			} else {
				return false;
			}

			$plugins_allowedtags = array(
				'a'       => array( 'href' => array(), 'title' => array() ),
				'abbr'    => array( 'title' => array() ),
				'acronym' => array( 'title' => array() ),
				'code'    => array(),
				'em'      => array(),
				'strong'  => array()
			);
			$plugin_name         = wp_kses( $plugin_data['Name'], $plugins_allowedtags );

			$info_url = admin_url( 'admin_ajax.php?action=wdpun-changelog&pid=' . $project_id . '&TB_iframe=true&width=640&height=800' );
			if ( file_exists( WP_PLUGIN_DIR . '/wpmudev-updates/update-notifications.php' ) ) {
				$message    = "Activate WPMU DEV Dashboard";
				$action_url = $this->activate_url();
			} else { //dashboard not installed at all
				$message    = "Install WPMU DEV Dashboard";
				$action_url = $this->auto_install_url();
			}

			if ( current_user_can( 'update_themes' ) ) {
				echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange"><div class="update-message notice inline notice-warning notice-alt"><p>';
				printf( 'There is a new version of %1$s available on WPMU DEV. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a> or <a href="%5$s">%6$s</a> to update.', $plugin_name, esc_url( $info_url ), esc_attr( $plugin_name ), $version, esc_url( $action_url ), $message );
				echo '</p></div></td></tr>';
			}
		}

		function disable_checkboxes() {

			$updates = get_site_option( 'wdp_un_updates_available' );
			if ( ! is_array( $updates ) || ( is_array( $updates ) && ! count( $updates ) ) ) {
				return;
			}

			$jquery = "<script type='text/javascript'>";

			if ( file_exists( WP_PLUGIN_DIR . '/wpmudev-updates/update-notifications.php' ) ) {
				$message    = "Activate WPMU DEV Dashboard";
				$action_url = $this->activate_url();
			} else { //dashboard not installed at all
				$message    = "Install WPMU DEV Dashboard";
				$action_url = $this->auto_install_url();
			}
			$jquery .= "var wdp_note = '<br><span class=\"notice inline notice-warning notice-alt\">" . sprintf( '<a href="%s">%s</a> to update.', esc_url( $action_url ), $message ) . "</span>';\n";

			foreach ( (array) $updates as $id => $project ) {
				$slug = $project['filename'];
				$jquery .= "jQuery(\"input:checkbox[value='" . esc_attr( $slug ) . "']\").closest('tr').find('td p').last().append(wdp_note);\n";
				$jquery .= "jQuery(\"input:checkbox[value='" . esc_attr( $slug ) . "']\").remove();\n";
			}

			//disable checkboxes for 133 theme pack themes
			$local_themes = get_site_option( 'wdp_un_local_themes' );
			if ( is_array( $local_themes ) && count( $local_themes ) ) {
				foreach ( $local_themes as $id => $theme ) {
					$jquery .= "jQuery(\"input:checkbox[value='" . esc_attr( $theme['filename'] ) . "']\").closest('tr').find('td p').last().append(wdp_note);\n";
					$jquery .= "jQuery(\"input:checkbox[value='" . esc_attr( $theme['filename'] ) . "']\").remove();\n";
				}
			}

			$jquery .= "</script>\n";

			echo $jquery;
		}

		function set_activate_flag( $plugin ) {
			$data = $this->get_id_plugin( WP_PLUGIN_DIR . '/' . $plugin );
			if ( isset( $data['id'] ) && ! empty( $data['id'] ) ) {
				update_site_option( 'wdp_un_activated_flag', 1 );
			}
		}

		function popup_changelog( $project_id ) {
			/**
			 * Dashboard popup template: Project changelog
			 *
			 * Displays the changelog of a specific project.
			 *
			 * Following variables are passed into the template:
			 *   $pid (project ID)
			 *
			 * @since   4.0.5
			 * @package WPMUDEV_Dashboard
			 */

			$url = $this->server_url . 'changelog/' . $project_id;

			$options = array(
				'timeout'    => 15,
				'sslverify'  => false, // Many hosts have no updated CA bundle.
				'user-agent' => 'Dashboard Notification/' . $this->version
			);

			$response = wp_remote_get( $url, $options );
			if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$changelog = json_decode( wp_remote_retrieve_body( $response ), true );
			}

			$updates = get_site_option( 'wdp_un_updates_available' );
			$item    = $updates[ $project_id ];

			if ( ! $changelog || ! is_array( $changelog ) || ! $item ) {
				wp_die( __( 'We did not find any data for this plugin or theme...', 'wpmudev' ) );
			}
			$dlg_id = 'dlg-' . md5( time() . '-' . $project_id );


			?>
			<div id="content" class="<?php echo esc_attr( $dlg_id ); ?>">
				<script src="<?php echo includes_url( '/wp-includes/js/jquery/jquery.js' ); ?>"></script>
				<link rel="stylesheet"
				      href="https://fonts.googleapis.com/css?family=Roboto+Condensed%3A400%2C700%7CRoboto%3A400%2C500%2C300%2C300italic%2C100"
				      type="text/css" media="all"/>
				<style>
					* {
						box-sizing: border-box;
						-moz-box-sizing: border-box;
					}

					html, body {
						margin: 0;
						padding: 0;
						height: 100%;
						font-family: 'Roboto', 'Helvetica Neue', Helvetica, sans-serif;
						font-size: 15px;
					}

					h1, h2, h3, h4 {
						font-family: 'Roboto Condensed', 'Roboto', 'Helvetica Neue', Helvetica, sans-serif;
						font-weight: 700;
						color: #777771;
					}

					h1 {
						font-size: 3em;
					}

					p {
						font-size: 1.2em;
						font-weight: 300;
						color: #777771;
					}

					a {
						color: #19b4cf;
						text-decoration: none;
					}

					a:hover,
					a:focus,
					a:active {
						color: #387ac1;
					}

					#content {
						min-height: 100%;
						text-align: center;
						background: #FFF;
						position: absolute;
						left: 0;
						top: 0;
						right: 0;
						bottom: 0;
						overflow: auto;
					}

					#content .excerpt {
						width: 100%;
						background-color: #14485F;
						padding: 10px;
						color: #FFF;
					}

					#content .excerpt h1 {
						margin: 30px;
						color: #FFF;
						font-weight: 100;
					}

					#content .versions h4 {
						font-size: 15px;
						text-transform: uppercase;
						text-align: left;
						padding: 0 0 15px;
						font-weight: bold;
						line-height: 20px;
					}

					#content .excerpt a {
						float: left;
						margin-right: 40px;
						text-decoration: none;
						color: #6ECEDE;
					}

					#content .excerpt a:hover,
					#content .excerpt a:focus,
					#content .excerpt a:active {
						color: #C7F7FF;
					}

					#content .footer {
						background-color: #0B2F3F;
						padding: 20px 0;
						margin: 0;
						position: relative;
					}

					#content .footer p {
						color: #FFF;
						margin: 10px 0;
						padding: 0;
						font-size: 15px;
					}

					#content .information {
						padding: 0;
						text-align: left;
					}

					#content .versions > li {
						border-bottom: 1px solid #E5E5E5;
						padding: 40px;
						margin: 0;
					}

					#content .versions > li.new {
						background: #fffff6;
					}

					#content .information .current-version,
					#content .information .new-version {
						border-radius: 5px;
						color: #FFF;
						cursor: default;
						display: inline-block;
						position: relative;
						top: -2px;
						margin: 0 0 0 10px;
						padding: 1px 5px;
						font-size: 10px;
						line-height: 20px;
						height: 20px;
						box-sizing: border-box;
					}

					#content .information .new-version {
						background: #FDCE43;
						text-shadow: 0 1px 1px #DDAE30;
					}

					#content .current-version {
						background: #00ACCA;
						text-shadow: 0 1px 1px #008CAA;
					}

					#content .versions {
						margin: 0;
						padding: 0;
					}

					#content .versions .changes {
						list-style: disc;
						padding: 0 0 0 20px;
						margin: 0;
					}

					#content .versions .changes li {
						padding: 3px 0 3px 20px;
						margin: 0;
						color: #777771;
						cursor: default;
					}

					#content .version-meta {
						float: right;
						text-align: right;
					}
				</style>

				<div class="excerpt">
					<h1><?php printf( esc_attr__( '%s changelog', 'wpmudev' ), esc_html( $item['name'] ) ); ?></h1>
				</div>

				<div class="information">

					<ul class="versions">
						<?php
						foreach ( $changelog as $log ) {
							$row_class = '';
							$badge     = '';

							if ( ! is_array( $log ) ) {
								continue;
							}
							if ( empty( $log ) ) {
								continue;
							}

							// -1 .. local is higher (dev) | 0 .. equal | 1 .. new version available
							$version_check = version_compare( $log['version'], $item['version'] );

							if ( $item['version'] && 1 === $version_check ) {
								$row_class = 'new';
							}

							if ( $item['version'] ) {
								if ( 0 === $version_check ) {
									$badge = sprintf(
										'<div class="current-version">%s %s</div>',
										'<i class="wdv-icon wdv-icon-ok"></i>',
										__( 'Current', 'wpmudev' )
									);
								} elseif ( 1 === $version_check ) {
									$badge = sprintf(
										'<div class="new-version">%s %s</div>',
										'<i class="wdv-icon wdv-icon-star"></i>',
										__( 'New', 'wpmudev' )
									);
								}
							}

							$version = $log['version'];

							if ( empty( $log['time'] ) ) {
								$rel_date = '';
							} else {
								$rel_date = date_i18n( get_option( 'date_format' ), $log['time'] );
							}

							printf(
								'<li class="%1$s"><h4>%2$s %3$s <small class="version-meta">%4$s</small></h4>',
								esc_attr( $row_class ),
								sprintf(
									esc_html__( 'Version %s', 'wpmudev' ), esc_html( $version )
								),
								wp_kses_post( $badge ),
								esc_html( $rel_date )
							);

							$notes        = explode( "\n", $log['log'] );
							$detail_level = 0;
							$detail_class = 'intro';

							echo '<ul class="changes">';
							foreach ( $notes as $note ) {
								if ( 0 === strpos( $note, '<p>' ) ) {
									if ( 1 == $detail_level ) {
										printf(
											'<li class="toggle-details">
									<a href="#" class="for-intro">%s</a><a href="#" class="for-detail">%s</a>
									</li>',
											esc_html__( 'Show all changes', 'wpmudev' ),
											esc_html__( 'Hide details', 'wpmudev' )
										);
										$detail_class = 'detail';
									}
									$detail_level += 1;
								}

								$note = stripslashes( $note );
								$note = preg_replace( '/(<br ?\/?>|<p>|<\/p>)/', '', $note );
								$note = trim( preg_replace( '/^\s*(\*|\-)\s*/', '', $note ) );
								$note = str_replace( array( '<', '>' ), array( '&lt;', '&gt;' ), $note );
								$note = preg_replace( '/`(.*?)`/', '<code>\1</code>', $note );
								if ( empty( $note ) ) {
									continue;
								}

								printf(
									'<li class="version-%s">%s</li>',
									esc_attr( $detail_class ),
									wp_kses_post( $note )
								);
							}
							echo '</ul></li>';
						}
						?>
					</ul>
				</div>

				<div class="footer">
					<p>Copyright 2009 - <?php echo esc_html( date( 'Y' ) ); ?> WPMU DEV</p>
				</div>

				<style>
					.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes .for-detail,
					.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes .version-detail {
						display: none;
					}

					.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes .for-intro {
						display: inline-block;
					}

					.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes.show-details .for-intro {
						display: none;
					}

					.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes.show-details .for-detail {
						display: inline-block;
					}

					.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes.show-details .version-detail {
						display: list-item;
					}

					.<?php echo esc_attr( $dlg_id ); ?> .versions ul.changes .toggle-details {
						padding: 8px 0 4px;
						text-align: right;
						font-size: 12px;
						list-style: none;
					}
				</style>
				<script>
					jQuery(function () {
						jQuery('.<?php echo esc_attr( $dlg_id ); ?>').on('click', '.toggle-details a', function (ev) {
							var li = jQuery(this),
								ver = li.closest('.changes');

							ev.preventDefault();
							ev.stopPropagation();
							ver.toggleClass('show-details');
							return false;
						});
					});
				</script>
			</div>
			<?php
			exit; //this is for output, we are done after this.
		}

		function popup_changelog_ajax() {
			$project_id = $_GET['pid'];
			$this->popup_changelog( $project_id );
		}

		function dismiss_ajax() {
			update_site_option( 'wdp_un_dismissed', time() );
			wp_send_json_success();
		}

	}

	$GLOBALS['WPMUDEV_Dashboard_Notice4'] = new WPMUDEV_Dashboard_Notice4();
}

//disable older versions
if ( ! class_exists( 'WPMUDEV_Dashboard_Notice' ) ) {
	class WPMUDEV_Dashboard_Notice {
	}
}

if ( ! class_exists( 'WPMUDEV_Dashboard_Notice3' ) ) {
	class WPMUDEV_Dashboard_Notice3 {
	}
}