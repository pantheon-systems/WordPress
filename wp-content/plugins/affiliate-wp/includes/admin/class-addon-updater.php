<?php

//set_site_transient( 'update_plugins', null );

class AffWP_AddOn_Updater {

	private $api_url    = '';
	private $api_data   = array();
	private $addon_id   = '';
	private $name       = '';
	private $slug       = '';
	private $version    = '';

	/**
	 * Class constructor.
	 *
	 * @uses plugin_basename()
	 * @uses hook()
	 *
	 * @param string $_api_url The URL pointing to the custom API endpoint.
	 * @param string $_plugin_file Path to the plugin file.
	 * @param array $_api_data Optional data to send with API calls.
	 * @return void
	 */
	function __construct( $_addon_id, $_plugin_file, $_version ) {
		$this->api_url    = 'https://affiliatewp.com';
		$this->addon_id  = $_addon_id;
		$this->name       = plugin_basename( $_plugin_file );
		$this->slug       = basename( $_plugin_file, '.php');
		$this->version    = $_version;

		// Set up hooks.
		$this->hook();
	}

	/**
	 * Set up Wordpress filters to hook into WP's update process.
	 *
	 * @uses add_filter()
	 *
	 * @return void
	 */
	private function hook() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'show_changelog' ) );
	}

	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update api just when Wordpress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native Wordpress plugin update code.
	 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
	 *
	 * @uses api_request()
	 *
	 * @param array $_transient_data Update array build by Wordpress.
	 * @return array Modified update array with custom plugin data.
	 */
	function check_update( $_transient_data ) {

		global $pagenow;

		if( 'plugins.php' == $pagenow && is_multisite() ) {
			return $_transient_data;
		}

		if( ! is_object( $_transient_data ) ) {
			$_transient_data = new stdClass;
		}

		if ( empty( $_transient_data->response ) || empty( $_transient_data->response[ $this->name ] ) ) {

			$api_response = $this->api_request( 'plugin_latest_version', array( 'slug' => $this->slug ) );

			if( false !== $api_response && is_object( $api_response ) && isset( $api_response->new_version ) ) {
				if( version_compare( $this->version, $api_response->new_version, '<' ) ) {
					$_transient_data->response[ $this->name ] = $api_response;
				}
			}

			$_transient_data->last_checked = time();
			$_transient_data->checked[ $this->name ] = $this->version;

		}

		return $_transient_data;
	}

	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @uses api_request()
	 *
	 * @param mixed $_data
	 * @param string $_action
	 * @param object $_args
	 * @return object $_data
	 */
	function plugins_api_filter( $_data, $_action = '', $_args = null ) {
		if ( ( $_action != 'plugin_information' ) || !isset( $_args->slug ) || ( $_args->slug != $this->slug ) ) {
			return $_data;
		}

		$to_send = array( 'slug' => $this->slug );

		$api_response = $this->api_request( 'plugin_information', $to_send );
		if ( false !== $api_response ) {
			$_data = $api_response;
		}
		return $_data;
	}

	/**
     * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
     *
     * @param string  $file
     * @param array   $plugin
     */
	public function show_update_notification( $file, $plugin ) {

		if( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if( ! is_multisite() || is_network_admin() ) {
			return;
		}

		if ( $this->name != $file ) {
			return;
		}

		// Remove our filter on the site transient
		remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 10 );

		$update_cache = get_site_transient( 'update_plugins' );

		if ( ! is_object( $update_cache ) || empty( $update_cache->response ) || empty( $update_cache->response[ $this->name ] ) ) {

			$cache_key    = md5( 'affwp_plugin_' . sanitize_key( $this->name ) . '_version_info' );
			$version_info = get_transient( $cache_key );

			if( false === $version_info ) {

				$version_info = $this->api_request( 'plugin_latest_version', array( 'slug' => $this->slug ) );

				set_transient( $cache_key, $version_info, HOUR_IN_SECONDS );
			}


			if( ! is_object( $version_info ) ) {
				return;
			}

			if( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$update_cache->response[ $this->name ] = $version_info;

			}

			$update_cache->last_checked = time();
			$update_cache->checked[ $this->name ] = $this->version;

			set_site_transient( 'update_plugins', $update_cache );

		} else {

			$version_info = $update_cache->response[ $this->name ];

		}

		if ( ! empty( $update_cache->response[ $this->name ] ) && version_compare( $this->version, $update_cache->response[ $this->name ]->new_version, '<' ) ) {

			// build a plugin list row, with update notification
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
			echo '<tr class="plugin-update-tr" id="' . $this->slug . '-update" data-slug="' . $this->slug . '" data-plugin="' . $this->slug . '/' . $this->name . '">';
			echo '<td colspan="3" class="plugin-update colspanchange">';
			echo '<div class="update-message notice inline notice-warning notice-alt"><p>';


			$changelog_link = self_admin_url( 'index.php?affwp_action=view_plugin_changelog&plugin=' . $this->name . '&slug=' . $this->slug . '&addon_id=' . $this->addon_id . '&TB_iframe=true&width=772&height=911' );

			if ( empty( $version_info->download_link ) ) {
				printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a>.', 'affiliate-wp' ),
					esc_html( $version_info->name ),
					esc_url( $changelog_link ),
					esc_html( $version_info->new_version )
				);
			} else {
				printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a> or <a href="%4$s">update now</a>.', 'affiliate-wp' ),
					esc_html( $version_info->name ),
					esc_url( $changelog_link ),
					esc_html( $version_info->new_version ),
					esc_url( wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->name, 'upgrade-plugin_' . $this->name ) )
				);
			}

			echo '</p></div></td></tr>';
		}

		// Restore our filter
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
	}

	/**
	 * Calls the API and, if successful, returns the object delivered by the API.
	 *
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 * @uses is_wp_error()
	 *
	 * @param string $_action The requested action.
	 * @param array $_data Parameters for the API action.
	 * @return false||object
	 */
	private function api_request( $_action, $_data ) {

		global $wp_version;

		$data = $_data;

		$data['license'] = affiliate_wp()->settings->get( 'license_key' );

		if( empty( $data['license'] ) ) {
			return;
		}

		if( empty( $data['addon_id'] ) ) {
			$data['addon_id'] = $this->addon_id;
		}

		if( empty( $data['addon_id'] ) ) {
			return;
		}

		$api_params = array(
			'affwp_action' 	=> 'get_version',
			'license' 		=> $data['license'],
			'id' 			=> $data['addon_id'],
			'slug' 			=> $data['slug'],
			'url'           => home_url()
		);

		$request = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		if ( ! is_wp_error( $request ) ) {
			$request = json_decode( wp_remote_retrieve_body( $request ) );
			if( $request && isset( $request->sections ) ) {
				$request->sections = maybe_unserialize( $request->sections );
			}

			return $request;

		} else {

			return false;

		}

	}

	public function show_changelog() {

		if( empty( $_REQUEST['affwp_action'] ) || 'view_plugin_changelog' != $_REQUEST['affwp_action'] ) {
		    return;
		}

		if( empty( $_REQUEST['plugin'] ) ) {
		    return;
		}

		if( empty( $_REQUEST['slug'] ) ) {
		    return;
		}

		if( ! current_user_can( 'update_plugins' ) ) {
			wp_die( __( 'You do not have permission to install plugin updates', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
		}

		$response = $this->api_request( 'plugin_latest_version', array( 'slug' => $_REQUEST['slug'], 'addon_id' => $_REQUEST['addon_id'] ) );

		if( $response && isset( $response->sections['changelog'] ) ) {
			echo '<div style="background:#fff;padding:10px;height:100%;">' . $response->sections['changelog'] . '</div>';
		}

		exit;

	}

}