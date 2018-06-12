<?php
/**
 * Sensor: Widgets
 *
 * Widgets sensor class file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widgets sensor.
 *
 * 2042 User added a new widget
 * 2043 User modified a widget
 * 2044 User deleted widget
 * 2045 User moved widget
 * 2071 User changed widget position
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_Widgets extends WSAL_AbstractSensor {

	/**
	 * Widget Move Data
	 *
	 * @var array
	 */
	protected $_widget_move_data = null;

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {
		if ( current_user_can( 'edit_theme_options' ) ) {
			add_action( 'admin_init', array( $this, 'EventWidgetMove' ) );
			add_action( 'admin_init', array( $this, 'EventWidgetPostMove' ) );
		}
		add_action( 'sidebar_admin_setup', array( $this, 'EventWidgetActivity' ) );
	}

	/**
	 * Triggered when a user accesses the admin area.
	 * Moved widget.
	 */
	public function EventWidgetMove() {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['savewidgets'] ) ) {
			check_ajax_referer( 'save-sidebar-widgets', 'savewidgets' );
		}

		if ( isset( $post_array ) && ! empty( $post_array['sidebars'] ) ) {
			$current_sidebars = $post_array['sidebars'];
			$sidebars = array();
			foreach ( $current_sidebars as $key => $val ) {
				$sb = array();
				if ( ! empty( $val ) ) {
					$val = explode( ',', $val );
					foreach ( $val as $k => $v ) {
						if ( strpos( $v, 'widget-' ) === false ) {
							continue;
						}
						$sb[ $k ] = substr( $v, strpos( $v, '_' ) + 1 );
					}
				}
				$sidebars[ $key ] = $sb;
			}
			$current_sidebars = $sidebars;
			$db_sidebars = get_option( 'sidebars_widgets' );
			$widget_name = $from_sidebar = $to_sidebar = '';
			foreach ( $current_sidebars as $sidebar_name => $values ) {
				if ( is_array( $values ) && ! empty( $values ) && isset( $db_sidebars[ $sidebar_name ] ) ) {
					foreach ( $values as $widget_name ) {
						if ( ! in_array( $widget_name, $db_sidebars[ $sidebar_name ] ) ) {
							$to_sidebar = $sidebar_name;
							$widget_name = $widget_name;
							foreach ( $db_sidebars as $name => $v ) {
								if ( is_array( $v ) && ! empty( $v ) && in_array( $widget_name, $v ) ) {
									$from_sidebar = $name;
									continue;
								}
							}
						}
					}
				}
			}

			if ( empty( $widget_name ) || empty( $from_sidebar ) || empty( $to_sidebar ) ) {
				return;
			}

			if ( preg_match( '/^sidebar-/', $from_sidebar ) || preg_match( '/^sidebar-/', $to_sidebar ) ) {
				// This option will hold the data needed to trigger the event 2045
				// as at this moment the $wp_registered_sidebars variable is not yet populated
				// so we cannot retrieve the name for sidebar-1 || sidebar-2
				// we will then check for this variable in the EventWidgetPostMove() event.
				$this->_widget_move_data = array(
					'widget' => $widget_name,
					'from' => $from_sidebar,
					'to' => $to_sidebar,
				);
				return;
			}

			$this->plugin->alerts->Trigger(
				2045, array(
					'WidgetName' => $widget_name,
					'OldSidebar' => $from_sidebar,
					'NewSidebar' => $to_sidebar,
				)
			);
		}
	}

	/**
	 * Triggered when a user accesses the admin area.
	 * Changed widget position or moved widget.
	 */
	public function EventWidgetPostMove() {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		// #!-- generates the event 2071
		if ( isset( $post_array['action'] ) && ('widgets-order' == $post_array['action']) ) {
			if ( isset( $post_array['sidebars'] ) ) {
				// Get the sidebars from $post_array.
				$request_sidebars = array();
				if ( $post_array['sidebars'] ) {
					foreach ( $post_array['sidebars'] as $key => &$value ) {
						if ( ! empty( $value ) ) {
							// Build the sidebars array.
							$value = explode( ',', $value );
							// Cleanup widgets' name.
							foreach ( $value as $k => &$widget_name ) {
								$widget_name = preg_replace( '/^([a-z]+-[0-9]+)+?_/i', '', $widget_name );
							}
							$request_sidebars[ $key ] = $value;
						}
					}
				}

				if ( $request_sidebars ) {
					// Get the sidebars from DATABASE.
					$sidebar_widgets = wp_get_sidebars_widgets();
					// Get global sidebars so we can retrieve the real name of the sidebar.
					global $wp_registered_sidebars;

					// Check in each array if there's any change.
					foreach ( $request_sidebars as $sidebar_name => $widgets ) {
						if ( isset( $sidebar_widgets[ $sidebar_name ] ) ) {
							foreach ( $sidebar_widgets[ $sidebar_name ] as $i => $widget_name ) {
								$index = array_search( $widget_name, $widgets );
								// Check to see whether or not the widget has been moved.
								if ( $i != $index ) {
									$sn = $sidebar_name;
									// Try to retrieve the real name of the sidebar, otherwise fall-back to id: $sidebar_name.
									if ( $wp_registered_sidebars && isset( $wp_registered_sidebars[ $sidebar_name ] ) ) {
										$sn = $wp_registered_sidebars[ $sidebar_name ]['name'];
									}
									$this->plugin->alerts->Trigger(
										2071, array(
											'WidgetName' => $widget_name,
											'OldPosition' => $i + 1,
											'NewPosition' => $index + 1,
											'Sidebar' => $sn,
										)
									);
								}
							}
						}
					}
				}
			}
		}
		// #!--
		if ( $this->_widget_move_data ) {
			$widget_name = $this->_widget_move_data['widget'];
			$from_sidebar = $this->_widget_move_data['from'];
			$to_sidebar = $this->_widget_move_data['to'];

			global $wp_registered_sidebars;

			if ( preg_match( '/^sidebar-/', $from_sidebar ) ) {
				$from_sidebar = isset( $wp_registered_sidebars[ $from_sidebar ] )
				? $wp_registered_sidebars[ $from_sidebar ]['name']
				: $from_sidebar
				;
			}

			if ( preg_match( '/^sidebar-/', $to_sidebar ) ) {
				$to_sidebar = isset( $wp_registered_sidebars[ $to_sidebar ] )
				? $wp_registered_sidebars[ $to_sidebar ]['name']
				: $to_sidebar
				;
			}

			$this->plugin->alerts->Trigger(
				2045, array(
					'WidgetName' => $widget_name,
					'OldSidebar' => $from_sidebar,
					'NewSidebar' => $to_sidebar,
				)
			);
		}
	}

	/**
	 * Widgets Activity (added, modified, deleted).
	 */
	public function EventWidgetActivity() {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( ! isset( $post_array ) || ! isset( $post_array['widget-id'] ) || empty( $post_array['widget-id'] ) ) {
			return;
		}

		if ( isset( $post_array['savewidgets'] ) ) {
			check_ajax_referer( 'save-sidebar-widgets', 'savewidgets' );
		}

		global $wp_registered_sidebars;
		$can_check_sidebar = (empty( $wp_registered_sidebars ) ? false : true);

		switch ( true ) {
			// Added widget.
			case isset( $post_array['add_new'] ) && 'multi' == $post_array['add_new']:
				$sidebar = isset( $post_array['sidebar'] ) ? $post_array['sidebar'] : null;
				if ( $can_check_sidebar && preg_match( '/^sidebar-/', $sidebar ) ) {
					$sidebar = $wp_registered_sidebars[ $sidebar ]['name'];
				}
				$this->plugin->alerts->Trigger(
					2042, array(
						'WidgetName' => $post_array['id_base'],
						'Sidebar' => $sidebar,
					)
				);
				break;
			// Deleted widget.
			case isset( $post_array['delete_widget'] ) && intval( $post_array['delete_widget'] ) == 1:
				$sidebar = isset( $post_array['sidebar'] ) ? $post_array['sidebar'] : null;
				if ( $can_check_sidebar && preg_match( '/^sidebar-/', $sidebar ) ) {
					$sidebar = $wp_registered_sidebars[ $sidebar ]['name'];
				}
				$this->plugin->alerts->Trigger(
					2044, array(
						'WidgetName' => $post_array['id_base'],
						'Sidebar' => $sidebar,
					)
				);
				break;
			// Modified widget.
			case isset( $post_array['id_base'] ) && ! empty( $post_array['id_base'] ):
				$widget_id = 0;
				if ( ! empty( $post_array['multi_number'] ) ) {
					$widget_id = intval( $post_array['multi_number'] );
				} elseif ( ! empty( $post_array['widget_number'] ) ) {
					$widget_id = intval( $post_array['widget_number'] );
				}
				if ( empty( $widget_id ) ) {
					return;
				}

				$widget_name = $post_array['id_base'];
				$sidebar = isset( $post_array['sidebar'] ) ? $post_array['sidebar'] : null;
				$widget_data = isset( $post_array[ "widget-$widget_name" ][ $widget_id ] )
					? $post_array[ "widget-$widget_name" ][ $widget_id ]
					: null;

				if ( empty( $widget_data ) ) {
					return;
				}
				// Get info from db.
				$widget_db_data = get_option( 'widget_' . $widget_name );
				if ( empty( $widget_db_data[ $widget_id ] ) ) {
					return;
				}

				// Transform 'on' -> 1.
				foreach ( $widget_data as $k => $v ) {
					if ( 'on' == $v ) {
						$widget_data[ $k ] = 1;
					}
				}

				// Compare - checks for any changes inside widgets.
				$diff = array_diff_assoc( $widget_data, $widget_db_data[ $widget_id ] );
				$count = count( $diff );
				if ( $count > 0 ) {
					if ( $can_check_sidebar && preg_match( '/^sidebar-/', $sidebar ) ) {
						$sidebar = $wp_registered_sidebars[ $sidebar ]['name'];
					}
					$this->plugin->alerts->Trigger(
						2043, array(
							'WidgetName' => $widget_name,
							'Sidebar' => $sidebar,
						)
					);
				}
				break;
		}
	}
}
