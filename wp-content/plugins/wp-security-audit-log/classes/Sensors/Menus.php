<?php
/**
 * Sensor: Menus
 *
 * Menus sensor file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menus sensor.
 *
 * 2078 User created new menu
 * 2079 User added content to a menu
 * 2080 User removed content from a menu
 * 2081 User deleted menu
 * 2082 User changed menu setting
 * 2083 User modified content in a menu
 * 2084 User changed name of a menu
 * 2085 User changed order of the objects in a menu
 * 2089 User moved objects as a sub-item
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_Menus extends WSAL_AbstractSensor {

	/**
	 * Menu object.
	 *
	 * @var object
	 */
	protected $_old_menu = null;

	/**
	 * Old Menu objects.
	 *
	 * @var array
	 */
	protected $_old_menu_terms = array();

	/**
	 * Old Menu Items.
	 *
	 * @var array
	 */
	protected $_old_menu_items = array();

	/**
	 * Old Menu Locations.
	 *
	 * @var array
	 */
	protected $_old_menu_locations = null;

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {
		add_action( 'wp_create_nav_menu', array( $this, 'CreateMenu' ), 10, 2 );
		add_action( 'wp_delete_nav_menu', array( $this, 'DeleteMenu' ), 10, 1 );
		add_action( 'wp_update_nav_menu', array( $this, 'UpdateMenu' ), 10, 2 );

		add_action( 'wp_update_nav_menu_item', array( $this, 'UpdateMenuItem' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'ManageMenuLocations' ) );
		add_action( 'admin_init', array( $this, 'EventAdminInit' ) );

		// Customizer trigger.
		add_action( 'customize_register', array( $this, 'CustomizeInit' ) );
		add_action( 'customize_save_after', array( $this, 'CustomizeSave' ) );
	}

	/**
	 * Menu item updated.
	 *
	 * @param int   $menu_id - Menu ID.
	 * @param int   $menu_item_db_id - Menu item DB ID.
	 * @param array $args - An array of items used to update menu.
	 */
	public function UpdateMenuItem( $menu_id, $menu_item_db_id, $args ) {
		// Filter $_POST global array for security.
		$post_array = filter_input_array( INPUT_POST );

		$old_menu_items = array();
		if ( isset( $post_array['menu-item-title'] ) && isset( $post_array['menu-name'] ) ) {
			$is_changed_order = false;
			$is_sub_item = false;
			$new_menu_items = array_keys( $post_array['menu-item-title'] );
			$items = wp_get_nav_menu_items( $menu_id );
			if ( ! empty( $this->_old_menu_items ) ) {
				foreach ( $this->_old_menu_items as $old_item ) {
					if ( $old_item['menu_id'] == $menu_id ) {
						$item_id = $old_item['item_id'];
						if ( $item_id == $menu_item_db_id ) {
							if ( $old_item['menu_order'] != $args['menu-item-position'] ) {
								$is_changed_order = true;
							}
							if ( ! empty( $args['menu-item-parent-id'] ) ) {
								$is_sub_item = true;
							}
							if ( ! empty( $args['menu-item-title'] ) && $old_item['title'] != $args['menu-item-title'] ) {
								// Verify nonce.
								if ( ! wp_verify_nonce( $post_array['update-nav-menu-nonce'], 'update-nav_menu' ) ) {
									return false;
								}

								$this->EventModifiedItems( $post_array['menu-item-object'][ $menu_item_db_id ], $post_array['menu-item-title'][ $menu_item_db_id ], $post_array['menu-name'] );
							}
						}
						$old_menu_items[ $item_id ] = array(
							'type' => $old_item['object'],
							'title' => $old_item['title'],
							'parent' => $old_item['menu_item_parent'],
						);
					}
				}
			}
			if ( $is_changed_order && wp_verify_nonce( $post_array['meta-box-order-nonce'], 'meta-box-order' ) ) {
				$item_name = $old_menu_items[ $menu_item_db_id ]['title'];
				$this->EventChangeOrder( $item_name, $old_item['menu_name'] );
			}
			if ( $is_sub_item && wp_verify_nonce( $post_array['update-nav-menu-nonce'], 'update-nav_menu' ) ) {
				$item_parent_id = $args['menu-item-parent-id'];
				$item_name = $old_menu_items[ $menu_item_db_id ]['title'];
				if ( $old_menu_items[ $menu_item_db_id ]['parent'] != $item_parent_id ) {
					$parent_name = isset( $old_menu_items[ $item_parent_id ]['title'] ) ? $old_menu_items[ $item_parent_id ]['title'] : false;
					$this->EventChangeSubItem( $item_name, $parent_name, $post_array['menu-name'] );
				}
			}
			$added_items = array_diff( $new_menu_items, array_keys( $old_menu_items ) );

			// Add Items to the menu.
			if ( count( $added_items ) > 0 && wp_verify_nonce( $post_array['update-nav-menu-nonce'], 'update-nav_menu' ) ) {
				if ( in_array( $menu_item_db_id, $added_items ) ) {
					$this->EventAddItems( $post_array['menu-item-object'][ $menu_item_db_id ], $post_array['menu-item-title'][ $menu_item_db_id ], $post_array['menu-name'] );
				}
			}
			$removed_items = array_diff( array_keys( $old_menu_items ), $new_menu_items );

			// Remove items from the menu.
			if ( count( $removed_items ) > 0 && wp_verify_nonce( $post_array['update-nav-menu-nonce'], 'update-nav_menu' ) ) {
				if ( array_search( $menu_item_db_id, $new_menu_items ) == (count( $new_menu_items ) - 1) ) {
					foreach ( $removed_items as $removed_item_id ) {
						$this->EventRemoveItems( $old_menu_items[ $removed_item_id ]['type'], $old_menu_items[ $removed_item_id ]['title'], $post_array['menu-name'] );
					}
				}
			}
		}
	}

	/**
	 * New menu created.
	 *
	 * @param int   $term_id - Term ID.
	 * @param array $menu_data - Menu data.
	 */
	public function CreateMenu( $term_id, $menu_data ) {
		$this->plugin->alerts->Trigger(
			2078, array(
				'MenuName' => $menu_data['menu-name'],
			)
		);
	}

	/**
	 * New menu created.
	 *
	 * @global array $_POST Data post.
	 */
	public function ManageMenuLocations() {
		// Filter $_POST global array for security.
		$post_array = filter_input_array( INPUT_POST );

		// Verify nonce.
		if ( isset( $post_array['_wpnonce'] ) && ! wp_verify_nonce( $post_array['_wpnonce'], 'save-menu-locations' ) ) {
			return false;
		}

		// Manage Location tab.
		if ( isset( $post_array['menu-locations'] ) ) {
			$new_locations = $post_array['menu-locations'];
			if ( isset( $new_locations['primary'] ) ) {
				$this->LocationSetting( $new_locations['primary'], 'primary' );
			}
			if ( isset( $new_locations['social'] ) ) {
				$this->LocationSetting( $new_locations['social'], 'social' );
			}
		}
	}

	/**
	 * Menu location.
	 *
	 * @param integer $new_location - New location.
	 * @param string  $type - Location type.
	 */
	private function LocationSetting( $new_location, $type ) {
		$old_locations = get_nav_menu_locations();
		if ( 0 != $new_location ) {
			$menu = wp_get_nav_menu_object( $new_location );
			if ( ! empty( $old_locations[ $type ] ) && $old_locations[ $type ] != $new_location ) {
				$this->EventMenuSetting( $menu->name, 'Enabled', 'Location: ' . $type . ' menu' );
			}
		} else {
			if ( ! empty( $old_locations[ $type ] ) ) {
				$menu = wp_get_nav_menu_object( $old_locations[ $type ] );
				$this->EventMenuSetting( $menu->name, 'Disabled', 'Location: ' . $type . ' menu' );
			}
		}
	}

	/**
	 * Menu deleted.
	 *
	 * @param int $term_id - Term ID.
	 */
	public function DeleteMenu( $term_id ) {
		if ( $this->_old_menu ) {
			$this->plugin->alerts->Trigger(
				2081, array(
					'MenuName' => $this->_old_menu->name,
				)
			);
		}
	}

	/**
	 * Menu updated.
	 *
	 * @param int   $menu_id - Menu ID.
	 * @param array $menu_data (Optional) Menu data.
	 */
	public function UpdateMenu( $menu_id, $menu_data = null ) {
		if ( ! empty( $menu_data ) ) {
			$content_names_old = array();
			$content_types_old = array();
			$content_order_old = array();

			$items = wp_get_nav_menu_items( $menu_id );
			if ( ! empty( $items ) ) {
				foreach ( $items as $item ) {
					array_push( $content_names_old, $item->title );
					array_push( $content_types_old, $item->object );
					$content_order_old[ $item->ID ] = $item->menu_order;
				}
			}

			// Filter $_POST global array for security.
			$post_array = filter_input_array( INPUT_POST );

			// Menu changed name.
			if ( ! empty( $this->_old_menu_terms ) && isset( $post_array['menu'] ) && isset( $post_array['menu-name'] ) ) {
				foreach ( $this->_old_menu_terms as $old_menu_term ) {
					if ( $old_menu_term['term_id'] == $post_array['menu'] && wp_verify_nonce( $post_array['update-nav-menu-nonce'], 'update-nav_menu' ) ) {
						if ( $old_menu_term['name'] != $post_array['menu-name'] ) {
							$this->EventChangeName( $old_menu_term['name'], $post_array['menu-name'] );
						} else {
							// Remove the last menu item.
							if ( count( $content_names_old ) == 1 && count( $content_types_old ) == 1 ) {
								$this->EventRemoveItems( $content_types_old[0], $content_names_old[0], $post_array['menu-name'] );
							}
						}
					}
				}
			}

			// Enable/Disable menu setting.
			$nav_menu_options = maybe_unserialize( get_option( 'nav_menu_options' ) );
			$auto_add = null;
			if ( isset( $nav_menu_options['auto_add'] ) ) {
				if ( in_array( $menu_id, $nav_menu_options['auto_add'] ) ) {
					if ( empty( $post_array['auto-add-pages'] ) ) {
						$auto_add = 'Disabled';
					}
				} else {
					if ( isset( $post_array['auto-add-pages'] ) ) {
						$auto_add = 'Enabled';
					}
				}
			} else {
				if ( isset( $post_array['auto-add-pages'] ) ) {
					$auto_add = 'Enabled';
				}
			}

			// Alert 2082 Auto add pages.
			if ( ! empty( $auto_add ) ) {
				$this->EventMenuSetting( $menu_data['menu-name'], $auto_add, 'Auto add pages' );
			}

			$nav_menu_locations = get_nav_menu_locations();

			$location_primary = null;
			if ( isset( $this->_old_menu_locations['primary'] ) && isset( $nav_menu_locations['primary'] ) ) {
				if ( $nav_menu_locations['primary'] == $menu_id && $this->_old_menu_locations['primary'] != $nav_menu_locations['primary'] ) {
					$location_primary = 'Enabled';
				}
			} elseif ( empty( $this->_old_menu_locations['primary'] ) && isset( $nav_menu_locations['primary'] ) ) {
				if ( $nav_menu_locations['primary'] == $menu_id ) {
					$location_primary = 'Enabled';
				}
			} elseif ( isset( $this->_old_menu_locations['primary'] ) && empty( $nav_menu_locations['primary'] ) ) {
				if ( $this->_old_menu_locations['primary'] == $menu_id ) {
					$location_primary = 'Disabled';
				}
			}

			// Alert 2082 Primary menu.
			if ( ! empty( $location_primary ) ) {
				$this->EventMenuSetting( $menu_data['menu-name'], $location_primary, 'Location: primary menu' );
			}

			$location_social = null;
			if ( isset( $this->_old_menu_locations['social'] ) && isset( $nav_menu_locations['social'] ) ) {
				if ( $nav_menu_locations['social'] == $menu_id && $this->_old_menu_locations['social'] != $nav_menu_locations['social'] ) {
					$location_social = 'Enabled';
				}
			} elseif ( empty( $this->_old_menu_locations['social'] ) && isset( $nav_menu_locations['social'] ) ) {
				if ( $nav_menu_locations['social'] == $menu_id ) {
					$location_social = 'Enabled';
				}
			} elseif ( isset( $this->_old_menu_locations['social'] ) && empty( $nav_menu_locations['social'] ) ) {
				if ( $this->_old_menu_locations['social'] == $menu_id ) {
					$location_social = 'Disabled';
				}
			}

			// Alert 2082 Social links menu.
			if ( ! empty( $location_social ) ) {
				$this->EventMenuSetting( $menu_data['menu-name'], $location_social, 'Location: social menu' );
			}
		}
	}

	/**
	 * Set old menu terms and items.
	 */
	private function BuildOldMenuTermsAndItems() {
		$menus = wp_get_nav_menus();
		if ( ! empty( $menus ) ) {
			foreach ( $menus as $menu ) {
				array_push(
					$this->_old_menu_terms, array(
						'term_id' => $menu->term_id,
						'name' => $menu->name,
					)
				);
				$items = wp_get_nav_menu_items( $menu->term_id );
				if ( ! empty( $items ) ) {
					foreach ( $items as $item ) {
						array_push(
							$this->_old_menu_items, array(
								'menu_id' => $menu->term_id,
								'item_id' => $item->ID,
								'title' => $item->title,
								'object' => $item->object,
								'menu_name' => $menu->name,
								'menu_order' => $item->menu_order,
								'url' => $item->url,
								'menu_item_parent' => $item->menu_item_parent,
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Triggered when a user accesses the admin area.
	 */
	public function EventAdminInit() {
		// Filter global arrays for security.
		$server_array = filter_input_array( INPUT_SERVER );
		$get_array = filter_input_array( INPUT_GET );

		// Check if SCRIPT_NAME exists or not.
		$script_name = '';
		if ( ! empty( $server_array['SCRIPT_NAME'] ) ) {
			$script_name = $server_array['SCRIPT_NAME'];
		}

		$is_nav_menu = basename( $script_name ) == 'nav-menus.php';
		if ( $is_nav_menu ) {
			if ( isset( $get_array['action'] ) && 'delete' == $get_array['action'] ) {
				if ( isset( $get_array['menu'] ) ) {
					$this->_old_menu = wp_get_nav_menu_object( $get_array['menu'] );
				}
			} else {
				$this->BuildOldMenuTermsAndItems();
			}
			$this->_old_menu_locations = get_nav_menu_locations();
		}
	}

	/**
	 * Customize set old data.
	 */
	public function CustomizeInit() {
		$this->BuildOldMenuTermsAndItems();
		$this->_old_menu_locations = get_nav_menu_locations();
	}

	/**
	 * Customize Events Function.
	 */
	public function CustomizeSave() {
		$update_menus = array();
		$menus = wp_get_nav_menus();
		if ( ! empty( $menus ) ) {
			foreach ( $menus as $menu ) {
				array_push(
					$update_menus, array(
						'term_id' => $menu->term_id,
						'name' => $menu->name,
					)
				);
			}
		}

		// Deleted Menu.
		if ( isset( $update_menus ) && isset( $this->_old_menu_terms ) ) {
			$terms = array_diff( array_map( 'serialize', $this->_old_menu_terms ), array_map( 'serialize', $update_menus ) );
			$terms = array_map( 'unserialize', $terms );

			if ( isset( $terms ) && count( $terms ) > 0 ) {
				foreach ( $terms as $term ) {
					$this->plugin->alerts->Trigger(
						2081, array(
							'MenuName' => $term['name'],
						)
					);
				}
			}
		}

		// Filter $_POST global array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['action'] ) && 'customize_save' == $post_array['action'] ) {
			if ( isset( $post_array['wp_customize'], $post_array['customized'] ) ) {
				$customized = json_decode( wp_unslash( $post_array['customized'] ), true );
				if ( is_array( $customized ) ) {
					foreach ( $customized as $key => $value ) {
						if ( ! empty( $value['nav_menu_term_id'] ) ) {
							$is_occurred_event = false;
							$menu = wp_get_nav_menu_object( $value['nav_menu_term_id'] );
							$content_name = ! empty( $value['title'] ) ? $value['title'] : 'no title';
							if ( ! empty( $this->_old_menu_items ) ) {
								foreach ( $this->_old_menu_items as $old_item ) {
									$item_id = substr( trim( $key, ']' ), 14 );
									if ( $old_item['item_id'] == $item_id ) {
										// Modified Items in the menu.
										if ( $old_item['title'] != $content_name ) {
											$is_occurred_event = true;
											$this->EventModifiedItems( $value['type_label'], $content_name, $menu->name );
										}
										// Moved as a sub-item.
										if ( $old_item['menu_item_parent'] != $value['menu_item_parent'] && 0 != $value['menu_item_parent'] ) {
											$is_occurred_event = true;
											$parent_name = $this->GetItemName( $value['nav_menu_term_id'], $value['menu_item_parent'] );
											$this->EventChangeSubItem( $content_name, $parent_name, $menu->name );
										}
										// Changed order of the objects in a menu.
										if ( $old_item['menu_order'] != $value['position'] ) {
											$is_occurred_event = true;
											$this->EventChangeOrder( $content_name, $menu->name );
										}
									}
								}
							}
							// Add Items to the menu.
							if ( ! $is_occurred_event ) {
								$menu_name = ! empty( $customized['new_menu_name'] ) ? $customized['new_menu_name'] : $menu->name;
								$this->EventAddItems( $value['type_label'], $content_name, $menu_name );
							}
						} else {
							// Menu changed name.
							if ( isset( $update_menus ) && isset( $this->_old_menu_terms ) ) {
								foreach ( $this->_old_menu_terms as $old_menu ) {
									foreach ( $update_menus as $update_menu ) {
										if ( $old_menu['term_id'] == $update_menu['term_id'] && $old_menu['name'] != $update_menu['name'] ) {
											$this->EventChangeName( $old_menu['name'], $update_menu['name'] );
										}
									}
								}
							}
							// Setting Auto add pages.
							if ( ! empty( $value ) && isset( $value['auto_add'] ) ) {
								if ( $value['auto_add'] ) {
									$this->EventMenuSetting( $value['name'], 'Enabled', 'Auto add pages' );
								} else {
									$this->EventMenuSetting( $value['name'], 'Disabled', 'Auto add pages' );
								}
							}
							// Setting Location.
							if ( false !== strpos( $key, 'nav_menu_locations[' ) ) {
								$loc = substr( trim( $key, ']' ), 19 );
								if ( ! empty( $value ) ) {
									$menu = wp_get_nav_menu_object( $value );
									$menu_name = ! empty( $customized['new_menu_name'] ) ? $customized['new_menu_name'] : ( ! empty( $menu ) ? $menu->name : '');
									$this->EventMenuSetting( $menu_name, 'Enabled', 'Location: ' . $loc . ' menu' );
								} else {
									if ( ! empty( $this->_old_menu_locations[ $loc ] ) ) {
										$menu = wp_get_nav_menu_object( $this->_old_menu_locations[ $loc ] );
										$menu_name = ! empty( $customized['new_menu_name'] ) ? $customized['new_menu_name'] : ( ! empty( $menu ) ? $menu->name : '');
										$this->EventMenuSetting( $menu_name, 'Disabled', 'Location: ' . $loc . ' menu' );
									}
								}
							}
							// Remove items from the menu.
							if ( false !== strpos( $key, 'nav_menu_item[' ) ) {
								$item_id = substr( trim( $key, ']' ), 14 );
								if ( ! empty( $this->_old_menu_items ) ) {
									foreach ( $this->_old_menu_items as $old_item ) {
										if ( $old_item['item_id'] == $item_id ) {
											$this->EventRemoveItems( $old_item['object'], $old_item['title'], $old_item['menu_name'] );
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Added content to a menu.
	 *
	 * @param string $content_type - Type of content.
	 * @param string $content_name - Name of content.
	 * @param string $menu_name - Menu name.
	 */
	private function EventAddItems( $content_type, $content_name, $menu_name ) {
		$this->plugin->alerts->Trigger(
			2079, array(
				'ContentType' => $content_type,
				'ContentName' => $content_name,
				'MenuName' => $menu_name,
			)
		);
	}

	/**
	 * Removed content from a menu.
	 *
	 * @param string $content_type - Type of content.
	 * @param string $content_name - Name of content.
	 * @param string $menu_name - Menu name.
	 */
	private function EventRemoveItems( $content_type, $content_name, $menu_name ) {
		$this->plugin->alerts->Trigger(
			2080, array(
				'ContentType' => $content_type,
				'ContentName' => $content_name,
				'MenuName' => $menu_name,
			)
		);
	}

	/**
	 * Changed menu setting.
	 *
	 * @param string $menu_name - Menu Name.
	 * @param string $status - Status of menu.
	 * @param string $menu_setting - Menu setting.
	 */
	private function EventMenuSetting( $menu_name, $status, $menu_setting ) {
		$this->plugin->alerts->Trigger(
			2082, array(
				'Status' => $status,
				'MenuSetting' => $menu_setting,
				'MenuName' => $menu_name,
			)
		);
	}

	/**
	 * Modified content in a menu.
	 *
	 * @param string $content_type - Type of content.
	 * @param string $content_name - Name of content.
	 * @param string $menu_name - Menu name.
	 */
	private function EventModifiedItems( $content_type, $content_name, $menu_name ) {
		$this->plugin->alerts->Trigger(
			2083, array(
				'ContentType' => $content_type,
				'ContentName' => $content_name,
				'MenuName' => $menu_name,
			)
		);
	}

	/**
	 * Changed name of a menu.
	 *
	 * @param string $old_menu_name - Old Menu Name.
	 * @param string $new_menu_name - New Menu Name.
	 */
	private function EventChangeName( $old_menu_name, $new_menu_name ) {
		$this->plugin->alerts->Trigger(
			2084, array(
				'OldMenuName' => $old_menu_name,
				'NewMenuName' => $new_menu_name,
			)
		);
	}

	/**
	 * Changed order of the objects in a menu.
	 *
	 * @param string $item_name - Item name.
	 * @param string $menu_name - Menu name.
	 */
	private function EventChangeOrder( $item_name, $menu_name ) {
		$this->plugin->alerts->Trigger(
			2085, array(
				'ItemName' => $item_name,
				'MenuName' => $menu_name,
			)
		);
	}

	/**
	 * Moved objects as a sub-item.
	 *
	 * @param string $item_name - Item name.
	 * @param string $parent_name - Parent Name.
	 * @param string $menu_name - Menu Name.
	 */
	private function EventChangeSubItem( $item_name, $parent_name, $menu_name ) {
		$this->plugin->alerts->Trigger(
			2089, array(
				'ItemName' => $item_name,
				'ParentName' => $parent_name,
				'MenuName' => $menu_name,
			)
		);
	}

	/**
	 * Get menu item name.
	 *
	 * @param int $term_id - Term ID.
	 * @param int $item_id - Item ID.
	 */
	private function GetItemName( $term_id, $item_id ) {
		$item_name = '';
		$menu_items = wp_get_nav_menu_items( $term_id );
		foreach ( $menu_items as $menu_item ) {
			if ( $menu_item->ID == $item_id ) {
				$item_name = $menu_item->title;
				break;
			}
		}
		return $item_name;
	}
}
