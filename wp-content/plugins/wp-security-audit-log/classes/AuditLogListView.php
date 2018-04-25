<?php
/**
 * Audit Log List View
 *
 * CLass file for audit log list view.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( ABSPATH . 'wp-admin/includes/admin.php' );
require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

/**
 * This view is included in Audit Log Viewer Page.
 *
 * @see Views/AuditLog.php
 * @package Wsal
 */
class WSAL_AuditLogListView extends WP_List_Table {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var object
	 */
	protected $_plugin;

	/**
	 * GMT Offset
	 *
	 * @var string
	 */
	protected $_gmt_offset_sec = 0;

	/**
	 * Method: Constructor.
	 *
	 * @param object $plugin - Instance of WpSecurityAuditLog.
	 */
	public function __construct( $plugin ) {
		$this->_plugin = $plugin;

		$timezone = $this->_plugin->settings->GetTimezone();
		if ( $timezone ) {
			$this->_gmt_offset_sec = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
		} else {
			$this->_gmt_offset_sec = date( 'Z' );
		}

		parent::__construct(
			array(
				'singular'  => 'log',
				'plural'    => 'logs',
				'ajax'      => true,
				'screen'    => 'interval-list',
			)
		);
	}

	/**
	 * Empty View.
	 */
	public function no_items() {
		esc_html_e( 'No events so far.', 'wp-security-audit-log' );
	}

	/**
	 * Table navigation.
	 *
	 * @param string $which - Position of the nav.
	 */
	public function extra_tablenav( $which ) {
		// Items-per-page widget.
		$p = $this->_plugin->settings->GetViewPerPage();
		$items = array( 5, 10, 15, 30, 50 );
		if ( ! in_array( $p, $items ) ) {
			$items[] = $p;
		}

		?>
		<div class="wsal-ipp wsal-ipp-<?php echo esc_attr( $which ); ?>">
			<?php esc_html_e( 'Show ', 'wp-security-audit-log' ); ?>
			<select class="wsal-ipps" onfocus="WsalIppsFocus(value);" onchange="WsalIppsChange(value);">
				<?php foreach ( $items as $item ) { ?>
					<option
						value="<?php echo is_string( $item ) ? '' : esc_attr( $item ); ?>"
						<?php echo ( $item == $p ) ? 'selected="selected"' : false; ?>>
						<?php echo esc_html( $item ); ?>
					</option>
				<?php } ?>
			</select>
			<?php esc_html_e( ' Items', 'wp-security-audit-log' ); ?>
		</div>

		<?php
		// Show site alerts widget.
		if ( $this->is_multisite() && $this->is_main_blog() ) {
			$curr = $this->get_view_site_id();
			?>
			<div class="wsal-ssa wsal-ssa-<?php echo esc_attr( $which ); ?>">
				<?php if ( $this->get_site_count() > 15 ) { ?>
					<?php $curr = $curr ? get_blog_details( $curr ) : null; ?>
					<?php $curr = $curr ? ($curr->blogname . ' (' . $curr->domain . ')') : 'All Sites'; ?>
					<input type="text" class="wsal-ssas" value="<?php echo esc_attr( $curr ); ?>"/>
				<?php } else { ?>
					<select class="wsal-ssas" onchange="WsalSsasChange(value);">
						<option value="0"><?php esc_html_e( 'All Sites', 'wp-security-audit-log' ); ?></option>
						<?php foreach ( $this->get_sites() as $info ) { ?>
							<option value="<?php echo esc_attr( $info->blog_id ); ?>"
								<?php echo ( $info->blog_id == $curr ) ? 'selected="selected"' : false; ?>>
								<?php echo esc_html( $info->blogname ) . ' (' . esc_html( $info->domain ) . ')'; ?>
							</option>
						<?php } ?>
					</select>
				<?php } ?>
			</div>
			<?php
		}

		// Switch to live or archive DB.
		if ( $this->_plugin->settings->IsArchivingEnabled() ) {
			$selected = 'live';
			$selected_db = get_transient( 'wsal_wp_selected_db' );
			if ( $selected_db && 'archive' == $selected_db ) {
				$selected = 'archive';
			}
			?>
			<div class="wsal-ssa wsal-db">
				<select class="wsal-db" onchange="WsalDBChange(value);">
					<option value="live" <?php echo ( 'live' == $selected ) ? 'selected="selected"' : false; ?>>
						<?php esc_html_e( 'Live Database', 'wp-security-audit-log' ); ?>
					</option>
					<option value="archive" <?php echo ( 'archive' == $selected ) ? 'selected="selected"' : false; ?>>
						<?php esc_html_e( 'Archive Database', 'wp-security-audit-log' ); ?>
					</option>
				</select>
			</div>
			<?php
		}
	}

	/**
	 * Method: Object with keys: blog_id, blogname, domain.
	 *
	 * @param int|null $limit - Maximum number of sites to return (null = no limit).
	 * @return object
	 */
	public function get_sites( $limit = null ) {
		global $wpdb;
		// Build query.
		$sql = 'SELECT blog_id, domain FROM ' . $wpdb->blogs;
		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . $limit;
		}

		// Execute query.
		$res = $wpdb->get_results( $sql );

		// Modify result.
		foreach ( $res as $row ) {
			$row->blogname = get_blog_option( $row->blog_id, 'blogname' );
		}

		// Return result.
		return $res;
	}

	/**
	 * Method: The number of sites on the network.
	 *
	 * @return int
	 */
	public function get_site_count() {
		global $wpdb;
		$sql = 'SELECT COUNT(*) FROM ' . $wpdb->blogs;
		return (int) $wpdb->get_var( $sql );
	}

	/**
	 * Method: Get View Columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		$type_name = $this->_plugin->settings->get_type_username();
		if ( 'display_name' === $type_name ) {
			$name_column = __( 'User', 'wp-security-audit-log' );
		} elseif ( 'username' === $type_name ) {
			$name_column = __( 'Username', 'wp-security-audit-log' );
		}
		$cols = array(
			'type' => __( 'Alert ID', 'wp-security-audit-log' ),
			'code' => __( 'Severity', 'wp-security-audit-log' ),
			'crtd' => __( 'Date', 'wp-security-audit-log' ),
			'user' => $name_column,
			'scip' => __( 'Source IP', 'wp-security-audit-log' ),
		);
		if ( $this->is_multisite() && $this->is_main_blog() && ! $this->is_specific_view() ) {
			$cols['site'] = __( 'Site', 'wp-security-audit-log' );
		}
		$cols['mesg'] = __( 'Message', 'wp-security-audit-log' );
		$sel_columns = $this->_plugin->settings->GetColumnsSelected();
		if ( ! empty( $sel_columns ) ) {
			unset( $cols );
			$sel_columns = (array) json_decode( $sel_columns );
			foreach ( $sel_columns as $key => $value ) {
				switch ( $key ) {
					case 'alert_code':
						$cols['type'] = __( 'Alert ID', 'wp-security-audit-log' );
						break;
					case 'type':
						$cols['code'] = __( 'Severity', 'wp-security-audit-log' );
						break;
					case 'date':
						$cols['crtd'] = __( 'Date', 'wp-security-audit-log' );
						break;
					case 'username':
						$cols['user'] = $name_column;
						break;
					case 'source_ip':
						$cols['scip'] = __( 'Source IP', 'wp-security-audit-log' );
						break;
					case 'site':
						$cols['site'] = __( 'Site', 'wp-security-audit-log' );
						break;
					case 'message':
						$cols['mesg'] = __( 'Message', 'wp-security-audit-log' );
						break;
				}
			}
		}
		if ( $this->_plugin->settings->IsDataInspectorEnabled() ) {
			$cols['data'] = '';
		}
		return $cols;
	}

	/**
	 * Method: Get checkbox column.
	 *
	 * @param object $item - Item.
	 * @return string
	 */
	public function column_cb( $item ) {
		return '<input type="checkbox" value="' . $item->id . '" '
			. 'name="' . esc_attr( $this->_args['singular'] ) . '[]"/>';
	}

	/**
	 * Method: Get Sortable Columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'read' => array( 'is_read', false ),
			'type' => array( 'alert_id', false ),
			'crtd' => array( 'created_on', true ),
			'user' => array( 'user', true ),
			'scip' => array( 'scip', false ),
		);
	}

	/**
	 * Method: Get default column values.
	 *
	 * @param object $item - Column item.
	 * @param string $column_name - Name of the column.
	 */
	public function column_default( $item, $column_name ) {
		// Get date format.
		$datetime_format = $this->_plugin->settings->GetDatetimeFormat();

		switch ( $column_name ) {
			case 'read':
				return '<span class="log-read log-read-'
					. ($item->is_read ? 'old' : 'new')
					. '" title="' . __( 'Click to toggle.', 'wp-security-audit-log' ) . '"></span>';
			case 'type':
				$code = $this->_plugin->alerts->GetAlert( $item->alert_id );
				$extra_msg = '';
				$data_link = '';
				$modification_alerts = array( 1002, 1003, 6007, 6023 );
				if ( in_array( $item->alert_id, $modification_alerts, true ) ) {
					$extra_msg = '. Modify this alert.';
					if ( 1002 === $item->alert_id || 1003 === $item->alert_id ) {
						$data_link = add_query_arg( 'page', 'wsal-togglealerts#tab-users-profiles---activity', admin_url( 'admin.php' ) );
					} elseif ( 6007 === $item->alert_id || 6023 === $item->alert_id ) {
						$data_link = add_query_arg( 'page', 'wsal-togglealerts#tab-system-activity', admin_url( 'admin.php' ) );
					}
				}

				if ( ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
					return '<span class="log-disable">' . str_pad( $item->alert_id, 4, '0', STR_PAD_LEFT ) . ' </span>';
				}

				return '<span class="log-disable" data-disable-alert-nonce="' . wp_create_nonce( 'disable-alert-nonce' . $item->alert_id ) . '" data-tooltip="' . __( 'Disable this type of alerts.', 'wp-security-audit-log' ) . '<br>' . $item->alert_id . ' - ' . esc_html( $code->desc ) . $extra_msg . '" data-alert-id="' . $item->alert_id . '" ' . esc_attr( 'data-link=' . $data_link ) . ' >'
					. str_pad( $item->alert_id, 4, '0', STR_PAD_LEFT ) . ' </span>';
			case 'code':
				$code = $this->_plugin->alerts->GetAlert( $item->alert_id );
				$code = $code ? $code->code : 0;
				$const = (object) array(
					'name' => 'E_UNKNOWN',
					'value' => 0,
					'description' => __( 'Unknown error code.', 'wp-security-audit-log' ),
				);
				$const = $this->_plugin->constants->GetConstantBy( 'value', $code, $const );
				if ( 'E_CRITICAL' == $const->name ) {
					$const->name = 'Critical';
				} elseif ( 'E_WARNING' == $const->name ) {
					$const->name = 'Warning';
				} elseif ( 'E_NOTICE' == $const->name ) {
					$const->name = 'Notification';
				}
				return '<a class="tooltip" href="#" data-tooltip="' . esc_html( $const->name ) . '"><span class="log-type log-type-' . $const->value
					. '"></span></a>';
			case 'crtd':
				return $item->created_on ? (
						str_replace(
							'$$$',
							substr( number_format( fmod( $item->created_on + $this->_gmt_offset_sec, 1 ), 3 ), 2 ),
							date( $datetime_format, $item->created_on + $this->_gmt_offset_sec )
						)
					) : '<i>unknown</i>';
			case 'user':
				$username   = $item->GetUsername(); // Get username.
				$type_name  = $this->_plugin->settings->get_type_username(); // Get the data to display.
				$user = get_user_by( 'login', $username ); // Get user.

				// Check if the username and user exists.
				if ( $username && $user ) {
					$image = get_avatar( $user->ID, 32 );
					if ( 'display_name' === $type_name && ! empty( $user->first_name ) ) {
						$display_name = $user->first_name . ' ' . $user->last_name;
					} else {
						$display_name = $user->user_login;
					}

					if ( class_exists( 'WSAL_SearchExtension' ) ) {
						$tooltip = esc_attr__( 'Show me all activity by this User', 'wp-security-audit-log' );

						$uhtml = '<a class="search-user" data-tooltip="' . $tooltip . '" data-user="' . $user->user_login . '" href="' . admin_url( 'user-edit.php?user_id=' . $user->ID )
							. '" target="_blank">' . esc_html( $display_name ) . '</a>';
					} else {
						$uhtml = '<a href="' . admin_url( 'user-edit.php?user_id=' . $user->ID )
						. '" target="_blank">' . esc_html( $display_name ) . '</a>';
					}

					$roles = $item->GetUserRoles();
					if ( is_array( $roles ) && count( $roles ) ) {
						$roles = esc_html( ucwords( implode( ', ', $roles ) ) );
					} elseif ( is_string( $roles ) && '' != $roles ) {
						$roles = esc_html( ucwords( str_replace( array( '"', '[', ']' ), ' ', $roles ) ) );
					} else {
						$roles = '<i>' . __( 'Unknown', 'wp-security-audit-log' ) . '</i>';
					}
				} elseif ( 'Plugin' == $username ) {
					$image = '<img src="' . $this->_plugin->GetBaseUrl() . '/img/plugin-logo.png" class="avatar avatar-32 photo" width="32" height="32" alt=""/>';
					$uhtml = '<i>' . __( 'Plugin', 'wp-security-audit-log' ) . '</i>';
					$roles = '';
				} elseif ( 'Plugins' == $username ) {
					$image = '<img src="' . $this->_plugin->GetBaseUrl() . '/img/wordpress-logo-32.png" class="avatar avatar-32 photo" width="32" height="32" alt=""/>';
					$uhtml = '<i>' . __( 'Plugins', 'wp-security-audit-log' ) . '</i>';
					$roles = '';
				} elseif ( 'Website Visitor' == $username ) {
					$image = '<img src="' . $this->_plugin->GetBaseUrl() . '/img/wordpress-logo-32.png" class="avatar avatar-32 photo" width="32" height="32" alt=""/>';
					$uhtml = '<i>' . __( 'Website Visitor', 'wp-security-audit-log' ) . '</i>';
					$roles = '';
				} else {
					$image = '<img src="' . $this->_plugin->GetBaseUrl() . '/img/wordpress-logo-32.png" class="avatar avatar-32 photo" width="32" height="32" alt=""/>';
					$uhtml = '<i>' . __( 'System', 'wp-security-audit-log' ) . '</i>';
					$roles = '';
				}
				return $image . $uhtml . '<br/>' . $roles;
			case 'scip':
				$scip = $item->GetSourceIP();
				if ( is_string( $scip ) ) {
					$scip = str_replace( array( '"', '[', ']' ), '', $scip );
				}

				$oips = array(); // $item->GetOtherIPs();

				// If there's no IP...
				if ( is_null( $scip ) || '' == $scip ) {
					return '<i>unknown</i>';
				}

				// If there's only one IP...
				$link = 'http://whatismyipaddress.com/ip/' . $scip . '?utm_source=plugin&utm_medium=referral&utm_campaign=WPSAL';
				if ( class_exists( 'WSAL_SearchExtension' ) ) {
					$tooltip = esc_attr__( 'Show me all activity originating from this IP Address', 'wp-security-audit-log' );

					if ( count( $oips ) < 2 ) {
						return "<a class='search-ip' data-tooltip='$tooltip' data-ip='$scip' target='_blank' href='$link'>" . esc_html( $scip ) . '</a>';
					}
				} else {
					if ( count( $oips ) < 2 ) {
						return "<a target='_blank' href='$link'>" . esc_html( $scip ) . '</a>';
					}
				}

				// If there are many IPs...
				if ( class_exists( 'WSAL_SearchExtension' ) ) {
					$tooltip = esc_attr__( 'Show me all activity originating from this IP Address', 'wp-security-audit-log' );

					$html  = "<a class='search-ip' data-tooltip='$tooltip' data-ip='$scip' target='_blank' href='http://whatismyipaddress.com/ip/$scip'>" . esc_html( $scip ) . '</a> <a href="javascript:;" onclick="jQuery(this).hide().next().show();">(more&hellip;)</a><div style="display: none;">';
					foreach ( $oips as $ip ) {
						if ( $scip != $ip ) {
							$html .= '<div>' . $ip . '</div>';
						}
					}
					$html .= '</div>';
					return $html;
				} else {
					$html  = "<a target='_blank' href='http://whatismyipaddress.com/ip/$scip'>" . esc_html( $scip ) . '</a> <a href="javascript:;" onclick="jQuery(this).hide().next().show();">(more&hellip;)</a><div style="display: none;">';
					foreach ( $oips as $ip ) {
						if ( $scip != $ip ) {
							$html .= '<div>' . $ip . '</div>';
						}
					}
					$html .= '</div>';
					return $html;
				}

			case 'site':
				$info = get_blog_details( $item->site_id, true );
				return ! $info ? ('Unknown Site ' . $item->site_id)
					: ('<a href="' . esc_attr( $info->siteurl ) . '">' . esc_html( $info->blogname ) . '</a>');
			case 'mesg':
				return '<div id="Event' . $item->id . '">' . $item->GetMessage( array( $this, 'meta_formatter' ) ) . '</div>';
			case 'data':
				$url = admin_url( 'admin-ajax.php' ) . '?action=AjaxInspector&amp;occurrence=' . $item->id;
				return '<a class="more-info thickbox" title="' . __( 'Alert Data Inspector', 'wp-security-audit-log' ) . '"'
					. ' href="' . $url . '&amp;TB_iframe=true&amp;width=600&amp;height=550">&hellip;</a>';
			default:
				return isset( $item->$column_name )
					? esc_html( $item->$column_name )
					: 'Column "' . esc_html( $column_name ) . '" not found';
		}
	}

	/**
	 * Method: Reorder string items.
	 *
	 * @param object $a - Item to compare.
	 * @param object $b - Item to compare.
	 * @return int
	 */
	public function reorder_items_str( $a, $b ) {
		$result = strcmp( $a->{$this->_orderby}, $b->{$this->_orderby} );
		return ( 'asc' === $this->_order ) ? $result : -$result;
	}

	/**
	 * Method: Reorder items.
	 *
	 * @param object $a - Item to compare.
	 * @param object $b - Item to compare.
	 * @return int
	 */
	public function reorder_items_int( $a, $b ) {
		$result = $a->{$this->_orderby} - $b->{$this->_orderby};
		return ( 'asc' === $this->_order ) ? $result : -$result;
	}

	/**
	 * Method: Meta data formater.
	 *
	 * @param string $name - Name of the data.
	 * @param mix    $value - Value of the data.
	 * @return string
	 */
	public function meta_formatter( $name, $value ) {
		switch ( true ) {
			case '%Message%' == $name:
				return esc_html( $value );

			case '%PromoMessage%' == $name:
				return '<p class="promo-alert">' . $value . '</p>';

			case '%PromoLink%' == $name:
			case '%CommentLink%' == $name:
			case '%CommentMsg%' == $name:
				return $value;

			case '%MetaLink%' == $name:
				if ( ! empty( $value ) ) {
					return "<a href=\"#\" data-disable-custom-nonce='" . wp_create_nonce( 'disable-custom-nonce' . $value ) . "' onclick=\"WsalDisableCustom(this, '" . $value . "');\"> Exclude Custom Field from the Monitoring</a>";
				} else {
					return '';
				}

			case '%RevisionLink%' === $name:
				$check_value = (string) $value;
				if ( 'NULL' !== $check_value ) {
					return ' Click <a target="_blank" href="' . esc_url( $value ) . '">here</a> to see the content changes.';
				} else {
					return false;
				}

			case '%EditorLinkPost%' == $name:
				return ' <a target="_blank" href="' . esc_url( $value ) . '">View the post</a>';

			case '%EditorLinkPage%' == $name:
				return ' <a target="_blank" href="' . esc_url( $value ) . '">View the page</a>';

			case '%CategoryLink%' == $name:
				return ' <a target="_blank" href="' . esc_url( $value ) . '">View the category</a>';

			case '%TagLink%' == $name:
				return ' <a target="_blank" href="' . esc_url( $value ) . '">View the tag</a>';

			case '%EditorLinkForum%' == $name:
				return ' <a target="_blank" href="' . esc_url( $value ) . '">View the forum</a>';

			case '%EditorLinkTopic%' == $name:
				return ' <a target="_blank" href="' . esc_url( $value ) . '">View the topic</a>';

			case in_array( $name, array( '%MetaValue%', '%MetaValueOld%', '%MetaValueNew%' ) ):
				return '<strong>' . (
					strlen( $value ) > 50 ? (esc_html( substr( $value, 0, 50 ) ) . '&hellip;') : esc_html( $value )
				) . '</strong>';

			case '%ClientIP%' == $name:
				if ( is_string( $value ) ) {
					return '<strong>' . str_replace( array( '"', '[', ']' ), '', $value ) . '</strong>';
				} else {
					return '<i>unknown</i>';
				}

			case '%LinkFile%' === $name:
				if ( 'NULL' != $value ) {
					$site_id  = $this->get_view_site_id(); // Site id for multisite.
					return '<a href="javascript:;" onclick="download_404_log( this )" data-log-file="' . esc_attr( $value ) . '" data-site-id="' . esc_attr( $site_id ) . '" data-nonce-404="' . esc_attr( wp_create_nonce( 'wsal-download-404-log-' . $value ) ) . '" title="' . esc_html__( 'Download the log file.', 'wp-security-audit-log' ) . '">' . esc_html__( 'Download the log file.', 'wp-security-audit-log' ) . '</a>';
				} else {
					return 'Click <a href="' . esc_url( admin_url( 'admin.php?page=wsal-togglealerts#tab-system-activity' ) ) . '">here</a> to log such requests to file.';
				}

			case '%LogFileLink%' === $name: // Failed login file link.
				return '';

			case '%Attempts%' === $name: // Failed login attempts.
				$check_value = (int) $value;
				if ( 0 === $check_value ) {
					return '';
				} else {
					return $value;
				}

			case '%LogFileText%' === $name: // Failed login file text.
				return '<a href="javascript:;" onclick="download_failed_login_log( this )" data-download-nonce="' . esc_attr( wp_create_nonce( 'wsal-download-failed-logins' ) ) . '" title="' . esc_html__( 'Download the log file.', 'wp-security-audit-log' ) . '">' . esc_html__( 'Download the log file.', 'wp-security-audit-log' ) . '</a>';

			case strncmp( $value, 'http://', 7 ) === 0:
			case strncmp( $value, 'https://', 7 ) === 0:
				return '<a href="' . esc_html( $value ) . '" title="' . esc_html( $value ) . '" target="_blank">' . esc_html( $value ) . '</a>';

			case '%PostStatus%' === $name:
				if ( ! empty( $value ) && 'publish' === $value ) {
					return '<strong>' . esc_html__( 'published', 'wp-security-audit-log' ) . '</strong>';
				} else {
					return '<strong>' . esc_html( $value ) . '</strong>';
				}

			case '%multisite_text%' === $name:
				if ( $this->is_multisite() && $value ) {
					$site_info = get_blog_details( $value, true );
					if ( $site_info ) {
						return ' on site <a href="' . esc_url( $site_info->siteurl ) . '">' . esc_html( $site_info->blogname ) . '</a>';
					}
					return;
				}
				return;

			default:
				return '<strong>' . esc_html( $value ) . '</strong>';
		}
	}

	/**
	 * Method: Check if multisite.
	 *
	 * @return bool
	 */
	protected function is_multisite() {
		return $this->_plugin->IsMultisite();
	}

	/**
	 * Method: Check if the blog is main blog.
	 *
	 * @return bool
	 */
	protected function is_main_blog() {
		return get_current_blog_id() == 1;
	}

	/**
	 * Method: Check if it is a specific view.
	 *
	 * @return bool
	 */
	protected function is_specific_view() {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		return isset( $post_array['wsal-cbid'] ) && '0' != $post_array['wsal-cbid'];
	}

	/**
	 * Method: Get a specific view.
	 *
	 * @return int
	 */
	protected function get_specific_view() {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		return isset( $post_array['wsal-cbid'] ) ? (int) $post_array['wsal-cbid'] : 0;
	}

	/**
	 * Method: Get view site id.
	 *
	 * @return int
	 */
	protected function get_view_site_id() {
		switch ( true ) {
			// Non-multisite.
			case ! $this->is_multisite():
				return 0;
			// Multisite + main site view.
			case $this->is_main_blog() && ! $this->is_specific_view():
				return 0;
			// Multisite + switched site view.
			case $this->is_main_blog() && $this->is_specific_view():
				return $this->get_specific_view();
			// Multisite + local site view.
			default:
				return get_current_blog_id();
		}
	}

	/**
	 * Method: Prepare items.
	 */
	public function prepare_items() {
		if ( $this->_plugin->settings->IsArchivingEnabled() ) {
			// Switch to Archive DB.
			$selected_db = get_transient( 'wsal_wp_selected_db' );
			if ( $selected_db && 'archive' == $selected_db ) {
				$this->_plugin->settings->SwitchToArchiveDB();
			}
		}

		$per_page = $this->_plugin->settings->GetViewPerPage();

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		// $this->process_bulk_action();
		// TO DO: Get rid of OccurrenceQuery and use the Occurence Model.
		$query = new WSAL_Models_OccurrenceQuery();

		$bid = (int) $this->get_view_site_id();
		if ( $bid ) {
			$query->addCondition( 'site_id = %s ', $bid );
		}

		$query = apply_filters( 'wsal_auditlog_query', $query );

		$total_items = $query->getAdapter()->Count( $query );

		// Filter $_GET and $_POST arrays for security.
		$get_array = filter_input_array( INPUT_GET );

		if ( empty( $get_array['orderby'] ) ) {
			$query->addOrderBy( 'created_on', true );
		} else {
			$order_by_field = $get_array['orderby'];

			$is_descending = true;
			if ( ! empty( $get_array['order'] ) && 'asc' == $get_array['order'] ) {
				$is_descending = false;
			}

			// TO DO: Allow order by meta values.
			if ( 'scip' == $order_by_field ) {
				$query->addMetaJoin();
				$query->addOrderBy( 'CASE WHEN meta.name = "ClientIP" THEN meta.value END', $is_descending );
			} elseif ( 'user' == $order_by_field ) {
				$query->addMetaJoin();
				$query->addOrderBy( 'CASE WHEN meta.name = "CurrentUserID" THEN meta.value END', $is_descending );
			} else {
				$tmp = new WSAL_Models_Occurrence();
				// Making sure the field exists to order by.
				if ( isset( $tmp->{$order_by_field} ) ) {
					// TODO: We used to use a custom comparator ... is it safe to let MySQL do the ordering now?.
					$query->addOrderBy( $get_array['orderby'], $is_descending );

				} else {
					$query->addOrderBy( 'created_on', true );
				}
			}
		}

		// @todo Modify $query instead
		// @deprecated
		// $data = array_slice($data, ($this->get_pagenum() - 1) * $per_page, $per_page);
		$query->setOffset( ($this->get_pagenum() - 1) * $per_page );
		$query->setLimit( $per_page );

		$this->items = $query->getAdapter()->Execute( $query );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Method: Output Single row.
	 *
	 * @param object $item - Item.
	 */
	public function single_row( $item ) {
		if ( 9999 == $item->alert_id ) {
			echo '<tr style="background-color: #D5E46E">';
			$this->single_row_columns( $item );
			echo '</tr>';
		} else {
			parent::single_row( $item );
		}
	}
}
