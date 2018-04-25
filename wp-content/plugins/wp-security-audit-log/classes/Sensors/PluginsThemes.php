<?php
/**
 * Sensor: Plugins & Themes
 *
 * Plugins & Themes sensor file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugins & Themes sensor.
 *
 * 5000 User installed a plugin
 * 5001 User activated a WordPress plugin
 * 5002 User deactivated a WordPress plugin
 * 5003 User uninstalled a plugin
 * 5004 User upgraded a plugin
 * 5005 User installed a theme
 * 5006 User activated a theme
 * 5007 User uninstalled a theme
 * 5019 A plugin created a post
 * 5020 A plugin created a page
 * 5021 A plugin created a custom post
 * 5025 A plugin deleted a post
 * 5026 A plugin deleted a page
 * 5027 A plugin deleted a custom post
 * 5031 User updated a theme
 * 2106 A plugin modified a post
 * 2107 A plugin modified a page
 * 2108 A plugin modified a custom post
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_PluginsThemes extends WSAL_AbstractSensor {

	/**
	 * List of Themes.
	 *
	 * @var array
	 */
	protected $old_themes = array();

	/**
	 * List of Plugins.
	 *
	 * @var array
	 */
	protected $old_plugins = array();

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {
		$has_permission = ( current_user_can( 'install_plugins' ) || current_user_can( 'activate_plugins' ) ||
							current_user_can( 'delete_plugins' ) || current_user_can( 'update_plugins' ) || current_user_can( 'install_themes' ) );

		add_action( 'admin_init', array( $this, 'EventAdminInit' ) );
		if ( $has_permission ) {
			add_action( 'shutdown', array( $this, 'EventAdminShutdown' ) );
		}
		add_action( 'switch_theme', array( $this, 'EventThemeActivated' ) );

		// TO DO.
		add_action( 'wp_insert_post', array( $this, 'EventPluginPostCreate' ), 10, 2 );
		add_action( 'delete_post', array( $this, 'EventPluginPostDelete' ), 10, 1 );
	}

	/**
	 * Triggered when a user accesses the admin area.
	 */
	public function EventAdminInit() {
		$this->old_themes = wp_get_themes();
		$this->old_plugins = get_plugins();
	}

	/**
	 * Install, uninstall, activate, deactivate, upgrade and update.
	 */
	public function EventAdminShutdown() {
		// Filter global arrays for security.
		$post_array = filter_input_array( INPUT_POST );
		$get_array = filter_input_array( INPUT_GET );
		$server_array = filter_input_array( INPUT_SERVER );

		$action = '';
		if ( isset( $get_array['action'] ) && '-1' != $get_array['action'] ) {
			$action = $get_array['action'];
		} elseif ( isset( $post_array['action'] ) && '-1' != $post_array['action'] ) {
			$action = $post_array['action'];
		}

		if ( isset( $get_array['action2'] ) && '-1' != $get_array['action2'] ) {
			$action = $get_array['action2'];
		} elseif ( isset( $post_array['action2'] ) && '-1' != $post_array['action2'] ) {
			$action = $post_array['action2'];
		}

		$actype = '';
		if ( isset( $server_array['SCRIPT_NAME'] ) ) {
			$actype = basename( $server_array['SCRIPT_NAME'], '.php' );
		}
		$is_themes = 'themes' == $actype;
		$is_plugins = 'plugins' == $actype;

		// Install plugin.
		if ( in_array( $action, array( 'install-plugin', 'upload-plugin' ) ) && current_user_can( 'install_plugins' ) ) {
			$plugin = array_values( array_diff( array_keys( get_plugins() ), array_keys( $this->old_plugins ) ) );
			if ( count( $plugin ) != 1 ) {
				return $this->LogError(
					'Expected exactly one new plugin but found ' . count( $plugin ),
					array(
						'NewPlugin' => $plugin,
						'OldPlugins' => $this->old_plugins,
						'NewPlugins' => get_plugins(),
					)
				);
			}
			$plugin_path = $plugin[0];
			$plugin = get_plugins();
			$plugin = $plugin[ $plugin_path ];
			$plugin_path = plugin_dir_path( WP_PLUGIN_DIR . '/' . $plugin_path[0] );
			$this->plugin->alerts->Trigger(
				5000, array(
					'Plugin' => (object) array(
						'Name' => $plugin['Name'],
						'PluginURI' => $plugin['PluginURI'],
						'Version' => $plugin['Version'],
						'Author' => $plugin['Author'],
						'Network' => $plugin['Network'] ? 'True' : 'False',
						'plugin_dir_path' => $plugin_path,
					),
				)
			);
		}

		// Activate plugin.
		if ( $is_plugins && in_array( $action, array( 'activate', 'activate-selected' ) ) && current_user_can( 'activate_plugins' ) ) {
			// Check $_GET array case.
			if ( isset( $get_array['plugin'] ) ) {
				if ( ! isset( $get_array['checked'] ) ) {
					$get_array['checked'] = array();
				}
				$get_array['checked'][] = $get_array['plugin'];
			}

			// Check $_POST array case.
			if ( isset( $post_array['plugin'] ) ) {
				if ( ! isset( $post_array['checked'] ) ) {
					$post_array['checked'] = array();
				}
				$post_array['checked'][] = $post_array['plugin'];
			}

			if ( isset( $get_array['checked'] ) && ! empty( $get_array['checked'] ) ) {
				foreach ( $get_array['checked'] as $plugin_file ) {
					$plugin_file = WP_PLUGIN_DIR . '/' . $plugin_file;
					$plugin_data = get_plugin_data( $plugin_file, false, true );
					$this->plugin->alerts->Trigger(
						5001, array(
							'PluginFile' => $plugin_file,
							'PluginData' => (object) array(
								'Name' => $plugin_data['Name'],
								'PluginURI' => $plugin_data['PluginURI'],
								'Version' => $plugin_data['Version'],
								'Author' => $plugin_data['Author'],
								'Network' => $plugin_data['Network'] ? 'True' : 'False',
							),
						)
					);
				}
			} elseif ( isset( $post_array['checked'] ) && ! empty( $post_array['checked'] ) ) {
				foreach ( $post_array['checked'] as $plugin_file ) {
					$plugin_file = WP_PLUGIN_DIR . '/' . $plugin_file;
					$plugin_data = get_plugin_data( $plugin_file, false, true );
					$this->plugin->alerts->Trigger(
						5001, array(
							'PluginFile' => $plugin_file,
							'PluginData' => (object) array(
								'Name' => $plugin_data['Name'],
								'PluginURI' => $plugin_data['PluginURI'],
								'Version' => $plugin_data['Version'],
								'Author' => $plugin_data['Author'],
								'Network' => $plugin_data['Network'] ? 'True' : 'False',
							),
						)
					);
				}
			}
		}

		// Deactivate plugin.
		if ( $is_plugins && in_array( $action, array( 'deactivate', 'deactivate-selected' ) ) && current_user_can( 'activate_plugins' ) ) {
			// Check $_GET array case.
			if ( isset( $get_array['plugin'] ) ) {
				if ( ! isset( $get_array['checked'] ) ) {
					$get_array['checked'] = array();
				}
				$get_array['checked'][] = $get_array['plugin'];
			}

			// Check $_POST array case.
			if ( isset( $post_array['plugin'] ) ) {
				if ( ! isset( $post_array['checked'] ) ) {
					$post_array['checked'] = array();
				}
				$post_array['checked'][] = $post_array['plugin'];
			}

			if ( isset( $get_array['checked'] ) && ! empty( $get_array['checked'] ) ) {
				foreach ( $get_array['checked'] as $plugin_file ) {
					$plugin_file = WP_PLUGIN_DIR . '/' . $plugin_file;
					$plugin_data = get_plugin_data( $plugin_file, false, true );
					$this->plugin->alerts->Trigger(
						5002, array(
							'PluginFile' => $plugin_file,
							'PluginData' => (object) array(
								'Name' => $plugin_data['Name'],
								'PluginURI' => $plugin_data['PluginURI'],
								'Version' => $plugin_data['Version'],
								'Author' => $plugin_data['Author'],
								'Network' => $plugin_data['Network'] ? 'True' : 'False',
							),
						)
					);
				}
			} elseif ( isset( $post_array['checked'] ) && ! empty( $post_array['checked'] ) ) {
				foreach ( $post_array['checked'] as $plugin_file ) {
					$plugin_file = WP_PLUGIN_DIR . '/' . $plugin_file;
					$plugin_data = get_plugin_data( $plugin_file, false, true );
					$this->plugin->alerts->Trigger(
						5002, array(
							'PluginFile' => $plugin_file,
							'PluginData' => (object) array(
								'Name' => $plugin_data['Name'],
								'PluginURI' => $plugin_data['PluginURI'],
								'Version' => $plugin_data['Version'],
								'Author' => $plugin_data['Author'],
								'Network' => $plugin_data['Network'] ? 'True' : 'False',
							),
						)
					);
				}
			}
		}

		// Uninstall plugin.
		if ( $is_plugins && in_array( $action, array( 'delete-selected' ) ) && current_user_can( 'delete_plugins' ) ) {
			if ( ! isset( $post_array['verify-delete'] ) ) {
				// First step, before user approves deletion
				// TODO store plugin data in session here.
			} else {
				// second step, after deletion approval
				// TODO use plugin data from session.
				foreach ( $post_array['checked'] as $plugin_file ) {
					$plugin_name = basename( $plugin_file, '.php' );
					$plugin_name = str_replace( array( '_', '-', '  ' ), ' ', $plugin_name );
					$plugin_name = ucwords( $plugin_name );
					$plugin_file = WP_PLUGIN_DIR . '/' . $plugin_file;
					$this->plugin->alerts->Trigger(
						5003, array(
							'PluginFile' => $plugin_file,
							'PluginData' => (object) array(
								'Name' => $plugin_name,
							),
						)
					);
				}
			}
		}

		// Uninstall plugin for WordPress version 4.6.
		if ( in_array( $action, array( 'delete-plugin' ) ) && current_user_can( 'delete_plugins' ) ) {
			if ( isset( $post_array['plugin'] ) ) {
				$plugin_file = WP_PLUGIN_DIR . '/' . $post_array['plugin'];
				$plugin_name = basename( $plugin_file, '.php' );
				$plugin_name = str_replace( array( '_', '-', '  ' ), ' ', $plugin_name );
				$plugin_name = ucwords( $plugin_name );
				$this->plugin->alerts->Trigger(
					5003, array(
						'PluginFile' => $plugin_file,
						'PluginData' => (object) array(
							'Name' => $plugin_name,
						),
					)
				);
			}
		}

		// Upgrade plugin.
		if ( in_array( $action, array( 'upgrade-plugin', 'update-plugin', 'update-selected' ) ) && current_user_can( 'update_plugins' ) ) {
			$plugins = array();

			// Check $_GET array cases.
			if ( isset( $get_array['plugins'] ) ) {
				$plugins = explode( ',', $get_array['plugins'] );
			} elseif ( isset( $get_array['plugin'] ) ) {
				$plugins[] = $get_array['plugin'];
			}

			// Check $_POST array cases.
			if ( isset( $post_array['plugins'] ) ) {
				$plugins = explode( ',', $post_array['plugins'] );
			} elseif ( isset( $post_array['plugin'] ) ) {
				$plugins[] = $post_array['plugin'];
			}
			if ( isset( $plugins ) ) {
				foreach ( $plugins as $plugin_file ) {
					$plugin_file = WP_PLUGIN_DIR . '/' . $plugin_file;
					$plugin_data = get_plugin_data( $plugin_file, false, true );
					$this->plugin->alerts->Trigger(
						5004, array(
							'PluginFile' => $plugin_file,
							'PluginData' => (object) array(
								'Name' => $plugin_data['Name'],
								'PluginURI' => $plugin_data['PluginURI'],
								'Version' => $plugin_data['Version'],
								'Author' => $plugin_data['Author'],
								'Network' => $plugin_data['Network'] ? 'True' : 'False',
							),
						)
					);
				}
			}
		}

		// Update theme.
		if ( in_array( $action, array( 'upgrade-theme', 'update-theme', 'update-selected-themes' ) ) && current_user_can( 'install_themes' ) ) {
			// Themes.
			$themes = array();

			// Check $_GET array cases.
			if ( isset( $get_array['slug'] ) || isset( $get_array['theme'] ) ) {
				$themes[] = isset( $get_array['slug'] ) ? $get_array['slug'] : $get_array['theme'];
			} elseif ( isset( $get_array['themes'] ) ) {
				$themes = explode( ',', $get_array['themes'] );
			}

			// Check $_POST array cases.
			if ( isset( $post_array['slug'] ) || isset( $post_array['theme'] ) ) {
				$themes[] = isset( $post_array['slug'] ) ? $post_array['slug'] : $post_array['theme'];
			} elseif ( isset( $post_array['themes'] ) ) {
				$themes = explode( ',', $post_array['themes'] );
			}
			if ( isset( $themes ) ) {
				foreach ( $themes as $theme_name ) {
					$theme = wp_get_theme( $theme_name );
					$this->plugin->alerts->Trigger(
						5031, array(
							'Theme' => (object) array(
								'Name' => $theme->Name,
								'ThemeURI' => $theme->ThemeURI,
								'Description' => $theme->Description,
								'Author' => $theme->Author,
								'Version' => $theme->Version,
								'get_template_directory' => $theme->get_template_directory(),
							),
						)
					);
				}
			}
		}

		// Install theme.
		if ( in_array( $action, array( 'install-theme', 'upload-theme' ) ) && current_user_can( 'install_themes' ) ) {
			$themes = array_diff( wp_get_themes(), $this->old_themes );
			foreach ( $themes as $theme ) {
				$this->plugin->alerts->Trigger(
					5005, array(
						'Theme' => (object) array(
							'Name' => $theme->Name,
							'ThemeURI' => $theme->ThemeURI,
							'Description' => $theme->Description,
							'Author' => $theme->Author,
							'Version' => $theme->Version,
							'get_template_directory' => $theme->get_template_directory(),
						),
					)
				);
			}
		}

		// Uninstall theme.
		if ( in_array( $action, array( 'delete-theme' ) ) && current_user_can( 'install_themes' ) ) {
			foreach ( $this->GetRemovedThemes() as $theme ) {
				$this->plugin->alerts->Trigger(
					5007, array(
						'Theme' => (object) array(
							'Name' => $theme->Name,
							'ThemeURI' => $theme->ThemeURI,
							'Description' => $theme->Description,
							'Author' => $theme->Author,
							'Version' => $theme->Version,
							'get_template_directory' => $theme->get_template_directory(),
						),
					)
				);
			}
		}
	}

	/**
	 * Activated a theme.
	 *
	 * @param string $theme_name - Theme name.
	 */
	public function EventThemeActivated( $theme_name ) {
		$theme = null;
		foreach ( wp_get_themes() as $item ) {
			if ( $theme_name == $item->Name ) {
				$theme = $item;
				break;
			}
		}
		if ( null == $theme ) {
			return $this->LogError(
				'Could not locate theme named "' . $theme . '".',
				array(
					'ThemeName' => $theme_name,
					'Themes' => wp_get_themes(),
				)
			);
		}
		$this->plugin->alerts->Trigger(
			5006, array(
				'Theme' => (object) array(
					'Name' => $theme->Name,
					'ThemeURI' => $theme->ThemeURI,
					'Description' => $theme->Description,
					'Author' => $theme->Author,
					'Version' => $theme->Version,
					'get_template_directory' => $theme->get_template_directory(),
				),
			)
		);
	}

	/**
	 * Plugin creates/modifies posts.
	 *
	 * @param int    $post_id - Post ID.
	 * @param object $post - Post object.
	 */
	public function EventPluginPostCreate( $post_id, $post ) {
		// Filter $_REQUEST array for security.
		$get_array = filter_input_array( INPUT_GET );
		$post_array = filter_input_array( INPUT_POST );

		$wp_actions = array( 'editpost', 'heartbeat', 'inline-save', 'trash', 'untrash' );
		if ( isset( $get_array['action'] ) && ! in_array( $get_array['action'], $wp_actions ) ) {
			if ( ! in_array( $post->post_type, array( 'attachment', 'revision', 'nav_menu_item', 'customize_changeset', 'custom_css' ) )
				|| ! empty( $post->post_title ) ) {
				// If the plugin modify the post.
				if ( false !== strpos( $get_array['action'], 'edit' ) ) {
					$editor_link = $this->GetEditorLink( $post );
					$this->plugin->alerts->Trigger(
						2106, array(
							'PostID'    => $post->ID,
							'PostType'  => $post->post_type,
							'PostTitle' => $post->post_title,
							'PostStatus' => $post->post_status,
							'PostUrl' => get_permalink( $post->ID ),
							$editor_link['name'] => $editor_link['value'],
						)
					);
				} else {
					$this->plugin->alerts->Trigger(
						5019, array(
							'PostID'    => $post->ID,
							'PostType'  => $post->post_type,
							'PostTitle' => $post->post_title,
							'Username'  => 'Plugins',
						)
					);
				}
			}
		}

		if ( isset( $post_array['action'] ) && ! in_array( $post_array['action'], $wp_actions ) ) {
			if ( ! in_array( $post->post_type, array( 'attachment', 'revision', 'nav_menu_item', 'customize_changeset', 'custom_css' ) )
				|| ! empty( $post->post_title ) ) {
				// If the plugin modify the post.
				if ( false !== strpos( $post_array['action'], 'edit' ) ) {
					$event = $this->GetEventTypeForPostType( $post, 2106, 2107, 2108 );
					$editor_link = $this->GetEditorLink( $post );
					$this->plugin->alerts->Trigger(
						$event, array(
							'PostID'    => $post->ID,
							'PostType'  => $post->post_type,
							'PostTitle' => $post->post_title,
							$editor_link['name'] => $editor_link['value'],
						)
					);
				} else {
					$event = $this->GetEventTypeForPostType( $post, 5019, 5020, 5021 );
					$this->plugin->alerts->Trigger(
						$event, array(
							'PostID'    => $post->ID,
							'PostType'  => $post->post_type,
							'PostTitle' => $post->post_title,
							'Username'  => 'Plugins',
						)
					);
				}
			}
		}
	}

	/**
	 * Plugin deletes posts.
	 *
	 * @param integer $post_id - Post ID.
	 */
	public function EventPluginPostDelete( $post_id ) {
		// Filter $_REQUEST array for security.
		$get_array = filter_input_array( INPUT_GET );
		$post_array = filter_input_array( INPUT_POST );

		if ( empty( $get_array['action'] ) && isset( $get_array['page'] ) ) {
			$post = get_post( $post_id );
			if ( ! in_array( $post->post_type, array( 'attachment', 'revision', 'nav_menu_item', 'customize_changeset', 'custom_css' ) )
				|| ! empty( $post->post_title ) ) {
				$this->plugin->alerts->Trigger(
					5025, array(
						'PostID'    => $post->ID,
						'PostType'  => $post->post_type,
						'PostTitle' => $post->post_title,
						'Username'  => 'Plugins',
					)
				);
			}
		}

		if ( empty( $post_array['action'] ) && isset( $post_array['page'] ) ) {
			$post = get_post( $post_id );
			if ( ! in_array( $post->post_type, array( 'attachment', 'revision', 'nav_menu_item', 'customize_changeset', 'custom_css' ) )
				|| ! empty( $post->post_title ) ) {
				$this->plugin->alerts->Trigger(
					5025, array(
						'PostID'    => $post->ID,
						'PostType'  => $post->post_type,
						'PostTitle' => $post->post_title,
						'Username'  => 'Plugins',
					)
				);
			}
		}
	}

	/**
	 * Get removed themes.
	 *
	 * @return array of WP_Theme objects
	 */
	protected function GetRemovedThemes() {
		$result = $this->old_themes;
		foreach ( $result as $i => $theme ) {
			if ( file_exists( $theme->get_template_directory() ) ) {
				unset( $result[ $i ] );
			}
		}
		return array_values( $result );
	}

	/**
	 * Get event code by post type.
	 *
	 * @param object $post - Post object.
	 * @param int    $type_post - Code for post.
	 * @param int    $type_page - Code for page.
	 * @param int    $type_custom - Code for custom post type.
	 */
	protected function GetEventTypeForPostType( $post, $type_post, $type_page, $type_custom ) {
		if ( empty( $post ) || ! isset( $post->post_type ) ) {
			return false;
		}

		switch ( $post->post_type ) {
			case 'page':
				return $type_page;
			case 'post':
				return $type_post;
			default:
				return $type_custom;
		}
	}

	/**
	 * Get editor link.
	 *
	 * @param object $post - The post object.
	 * @return array $editor_link name and value link.
	 */
	private function GetEditorLink( $post ) {
		$name = 'EditorLink';
		$name .= ( 'page' == $post->post_type ) ? 'Page' : 'Post' ;
		$value = get_edit_post_link( $post->ID );
		$editor_link = array(
			'name'  => $name,
			'value' => $value,
		);
		return $editor_link;
	}
}
