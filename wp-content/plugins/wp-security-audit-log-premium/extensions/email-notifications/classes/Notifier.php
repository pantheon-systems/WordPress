<?php
/**
 * Class: Utility Class
 *
 * Check for current generated alert.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WSAL_OPT_PREFIX' ) ) {
	exit( 'Invalid request' );
}

/**
 * Class WSAL_Notifier
 *
 * Loop through notifications and check if any matches the current generated alert.
 *
 * @author wp.kytten
 * @package wp-security-audit-log
 */
class WSAL_NP_Notifier extends WSAL_AbstractLogger {

	/**
	 * Alert date.
	 *
	 * @var string
	 */
	private $_alert_date = null;

	/**
	 * Email.
	 *
	 * @var string
	 */
	private $_email_address = '';

	/**
	 * Alert ID.
	 *
	 * @var string
	 */
	private $_alert_id = null;

	/**
	 * Alert data.
	 *
	 * @var string
	 */
	private $_alert_data = null;

	/**
	 * Notification Select 1 data.
	 *
	 * @var string
	 */
	private $_s1_data = null;

	/**
	 * Notification Select 2 data.
	 *
	 * @var string
	 */
	private $_s2_data = null;

	/**
	 * Notification Select 3 data.
	 *
	 * @var string
	 */
	private $_s3_data = null;

	/**
	 * Notification Select 4 data.
	 *
	 * Post Status Select box.
	 *
	 * @var string
	 */
	private $_s4_data = null;

	/**
	 * Notification Select 5 data.
	 *
	 * Post Type Select box.
	 *
	 * @var string
	 */
	private $_s5_data = null;

	/**
	 * Notification Select 6 data.
	 *
	 * User Roles Select box.
	 *
	 * @var string
	 */
	private $_s6_data = null;

	/**
	 * Is built in?
	 *
	 * @var bool
	 */
	private $_is_built_in = false;

	/**
	 * Has template?
	 *
	 * @var bool
	 */
	protected $_has_template = false;

	/**
	 * Notifications.
	 *
	 * @var object
	 */
	private $_notifications = null;

	/**
	 * Cache Expire Time.
	 *
	 * Time = 12h (60*60*12).
	 *
	 * @var int
	 */
	private $_cache_expire = 43200;

	/**
	 * Log alert.
	 *
	 * @param integer $type - Alert code.
	 * @param array   $data - Metadata.
	 * @param integer $date (Optional) - created_on.
	 * @param integer $siteid (Optional) - site_id.
	 * @param bool    $migrated (Optional) - is_migrated.
	 */
	public function Log( $type, $data = array(), $date = null, $siteid = null, $migrated = false ) {
		$this->_alert_id = $type;
		$this->_alert_data = $data;
		$this->_alert_date = $date;

		$nb = new WSAL_NP_NotificationBuilder();

		$this->_s1_data = $nb->GetSelect1Data();
		$this->_s2_data = $nb->GetSelect2Data();
		$this->_s3_data = $nb->GetSelect3Data();
		$this->_s4_data = $nb->GetSelect4Data(); // Post status.
		$this->_s5_data = $nb->GetSelect5Data(); // Post types.
		$this->_s6_data = $nb->GetSelect6Data(); // User roles.

		/**
		 * Cache notifications.
		 *
		 * @see http://codex.wordpress.org/Class_Reference/WP_Object_Cache
		 */
		$this->_notifications = wp_cache_get( WSAL_CACHE_KEY );

		if ( false === $this->_notifications ) {
			$this->_notifications = $this->plugin->wsalCommon->GetNotifications();
			wp_cache_set( WSAL_CACHE_KEY, $this->_notifications, null, $this->_cache_expire );
		}
		$this->_notifyIfConditionMatch();
	}

	/**
	 * Notify if Condition Matches.
	 */
	private function _notifyIfConditionMatch() {
		if ( empty( $this->_notifications ) ) {
			return;
		}
		// Go through each notification.
		foreach ( $this->_notifications as $k => $v ) {
			$not_info = unserialize( $v->option_value );
			$enabled = intval( $not_info->status );

			if ( 0 == $enabled ) {
				continue;
			}

			$skip = false;
			if ( ! empty( $not_info->firstTimeLogin ) && 1000 == $this->_alert_id ) {
				$users_login_list = $this->plugin->GetGlobalOption( 'users_login_list' );
				if ( ! empty( $users_login_list ) ) {
					if ( in_array( $this->_alert_data['Username'], $users_login_list ) ) {
						$skip = true;
					} else {
						array_push( $users_login_list, $this->_alert_data['Username'] );
						$this->plugin->SetGlobalOption( 'users_login_list', $users_login_list );
					}
				} else {
					$users_login_list = array();
					array_push( $users_login_list, $this->_alert_data['Username'] );
					$this->plugin->SetGlobalOption( 'users_login_list', $users_login_list );
				}
			}
			// Skip Suspicious Activity.
			if ( ! empty( $not_info->failUser ) && 1002 == $this->_alert_id ) {
				$skip = true;
			}
			if ( ! empty( $not_info->failNotUser ) && 1003 == $this->_alert_id ) {
				$skip = true;
			}
			if ( ! empty( $not_info->error404 ) && 6007 == $this->_alert_id ) {
				$skip = true;
			}
			if ( ! empty( $not_info->error404_visitor ) && 6023 == $this->_alert_id ) {
				$skip = true;
			}

			if ( $skip ) {
				continue;
			}

			$conditions = $not_info->triggers;
			$num = count( $conditions );
			$title = $not_info->title;
			$this->_email_address = $not_info->email;

			if ( ! empty( $not_info->built_in ) ) {
				$this->_is_built_in = true;
			} else {
				$this->_is_built_in = false;
			}

			if ( ! empty( $not_info->subject ) && ! empty( $not_info->body ) ) {
				$this->_has_template['subject'] = $not_info->subject;
				$this->_has_template['body'] = $not_info->body;
			} else {
				$this->_has_template = false;
			}

			// #! one condition
			if ( 1 == $num ) {
				$condition = $conditions[0];

				// Handle PAGE ID AND CUSTOM POST ID deprecation.
				if ( 7 === $condition['select2'] || 8 === $condition['select2'] ) {
					$condition['select2'] = 6;
				}

				$s1 = $this->_s1_data[ $condition['select1'] ];
				$s2 = $this->_s2_data[ $condition['select2'] ];
				$s3 = $this->_s3_data[ $condition['select3'] ];
				$s4 = isset( $condition['select4'] ) ? $this->_s4_data[ $condition['select4'] ] : false; // Post status select.
				$s5 = isset( $condition['select5'] ) ? $this->_s5_data[ $condition['select5'] ] : false; // Post type select.
				$s6 = isset( $condition['select6'] ) ? $this->_s6_data[ $condition['select6'] ] : false; // User roles select.
				$i1 = $condition['input1'];
				$this->_checkIfConditionMatch( $s1, $s2, $s3, $s4, $s5, $s6, $i1, $title, true );
			} else {
				// #! n conditions
				$test_array = array();
				$groups = $not_info->viewState;
				$last_id = 0;
				foreach ( $groups as $i => $entry ) {
					$i = $last_id;
					if ( is_string( $entry ) ) {
						array_push( $test_array, $conditions[ $i ] );
						$last_id++;
					} elseif ( is_array( $entry ) ) {
						$new = array();
						foreach ( $entry as $k => $item ) {
							array_push( $new, $conditions[ $last_id ] );
							$last_id++;
						}
						array_push( $test_array, $new );
					}
				}
				// Validate conditions.
				$exp = new WSAL_NP_Expression( $this, $this->_s1_data, $this->_s2_data, $this->_s3_data, $title, $this->_s4_data, $this->_s5_data, $this->_s6_data );
				$result = $exp->EvaluateConditions( $test_array );
				if ( $result ) {
					$this->_sendNotificationEmail( $title );
				}
			}
			/* Trigger Critical alert*/
			$alert = $this->plugin->alerts->GetAlert( $this->_alert_id );
			if ( ! empty( $not_info->isCritical ) && 'E_CRITICAL' == $alert->code ) {
				$this->_sendNotificationEmail( $title );
			}
		}
	}

	/**
	 * Check whether or not a condition matches anything in the Request $data
	 *
	 * @param string      $s1 - Select 1.
	 * @param string      $s2 - Select 2.
	 * @param string      $s3 - Select 3.
	 * @param string      $s4 - Select 4 / Post status.
	 * @param string      $s5 - Select 5 / Post type.
	 * @param string      $s6 - Select 6 / User role.
	 * @param string      $i1 - Input 1.
	 * @param null|string $title - The title of the alert.
	 * @param bool        $send_email - Whether or not to send the notification email. Defaults to false.
	 * @return bool
	 */
	function _checkIfConditionMatch( $s1, $s2, $s3, $s4, $s5, $s6, $i1, $title = null, $send_email = false ) {
		$date_format = $this->plugin->settings->GetDateFormat();
		$time_format = $this->plugin->settings->GetTimeFormat();
		$gmt_offset_sec = $this->plugin->wsalCommon->GetTimezone();

		if ( 'IS EQUAL' == $s3 ) {
			// Default - $type == ALERT CODE.
			$value = $this->_alert_id;

			if ( 'DATE' == $s2 ) {
				$value = date( $date_format );
			} elseif ( 'TIME' == $s2 ) {
				$value = date( $time_format );
			} elseif ( 'USERNAME' == $s2 ) {
				$uid = (isset( $this->_alert_data['CurrentUserID'] ) ? intval( $this->_alert_data['CurrentUserID'] ) : null);
				if ( empty( $uid ) ) { // will happen "on login"
					// This will be populated.
					if ( isset( $this->_alert_data['Username'] ) && ! empty( $this->_alert_data['Username'] ) ) {
						$value = $this->_alert_data['Username'];
					}
				} else {
					$user = get_user_by( 'id', $uid );
					if ( false === $user ) {
						$value = '';
					} else {
						$value = $user->user_login;
					}
				}
			} elseif ( 'USER ROLE' == $s2 ) {
				$roles = (isset( $this->_alert_data['CurrentUserRoles'] ) ? $this->_alert_data['CurrentUserRoles'] : null);

				// Convert value of $s6 to lowercase.
				$s6 = strtolower( $s6 );

				foreach ( $roles as $role ) {
					if ( strcasecmp( $s6, $role ) == 0 ) {
						if ( $send_email ) {
							return $this->_sendNotificationEmail( $title );
						} else {
							return true;
						}
					}
				}
			} elseif ( 'SOURCE IP' == $s2 ) {
				$value = $this->_alert_data['ClientIP'];
			} elseif ( 'PAGE ID' == $s2 || 'POST ID' == $s2 || 'CUSTOM POST ID' == $s2 ) {
				$pid = intval( $i1 );
				if ( empty( $pid ) || ! isset( $this->_alert_data['PostID'] ) ) {
					return false;
				}
				$dpid = intval( $this->_alert_data['PostID'] );

				if ( $pid <> $dpid ) {
					return false;
				}

				$post_type = strtolower( $this->_alert_data['PostType'] );

				if ( 'POST ID' == $s2 ) {
					if ( $send_email ) {
						return $this->_sendNotificationEmail( $title );
					} else {
						return true;
					}
				} elseif ( 'PAGE ID' == $s2 && 'page' == $post_type ) {
					if ( $send_email ) {
						return $this->_sendNotificationEmail( $title );
					} else {
						return true;
					}
				} elseif ( 'CUSTOM POST ID' == $s2 && ( 'post' != $post_type && 'page' != $post_type ) ) {
					if ( $send_email ) {
						return $this->_sendNotificationEmail( $title );
					} else {
						return true;
					}
				}
			} elseif ( 'SITE DOMAIN' == $s2 ) {
				$sid = intval( $i1 );
				$blog_id = get_current_blog_id();
				if ( empty( $sid ) ) {
					return false;
				}

				if ( $sid <> $blog_id ) {
					return false;
				}
				if ( $send_email ) {
					return $this->_sendNotificationEmail( $title );
				} else {
					return true;
				}
			} elseif ( 'POST TYPE' == $s2 ) {
				$post_type = (isset( $this->_alert_data['PostType'] ) ? strtolower( $this->_alert_data['PostType'] ) : null);

				if ( ! $this->plugin->IsMultisite() ) {
					// Convert value of $s5 to lowercase.
					$s5 = strtolower( $s5 );
				} else {
					$s5 = strtolower( $i1 );
				}

				if ( ! empty( $post_type ) && $s5 == $post_type ) {
					if ( $send_email ) {
						return $this->_sendNotificationEmail( $title );
					} else {
						return true;
					}
				}
			} elseif ( 'POST STATUS' === $s2 ) {
				// Get Post ID from alert data.
				$post_id = isset( $this->_alert_data['PostID'] ) ? intval( $this->_alert_data['PostID'] ) : false;

				// Get post status.
				$post_status = get_post_status( $post_id );

				// Return if post status is empty.
				if ( empty( $post_status ) ) {
					return false;
				}

				// Convert value of $s4 to lowercase.
				$s4 = strtolower( $s4 );

				// Check for publish post status.
				$post_status = ( 'publish' === $post_status ) ? 'published' : $post_status;

				// Send notification if the selected status matches with the post status.
				if ( ! empty( $s4 ) && $post_status === $s4 ) {
					if ( $send_email ) {
						return $this->_sendNotificationEmail( $title );
					} else {
						return true;
					}
				}
			}

			// Equality test - except user role.
			if ( $value == $i1 ) {
				if ( $send_email ) {
					return $this->_sendNotificationEmail( $title );
				} else {
					return true;
				}
			}
		} // Valid only for: SOURCE IP.
		elseif ( 'CONTAINS' == $s3 ) {
			if ( 'SOURCE IP' == $s2 ) {
				if ( false !== strpos( $this->_alert_data['ClientIP'], $i1 ) ) {
					if ( $send_email ) {
						return $this->_sendNotificationEmail( $title );
					} else {
						return true;
					}
				}
			}
		} // DATE & TIME ONLY.
		elseif ( 'IS AFTER' == $s3 ) {
			if ( 'DATE' == $s2 ) {
				$today = date( $date_format );
				$tstr = strtotime( $today );
				$value = strtotime( str_replace( '-', '/', $i1 ) );
				if ( $tstr > $value ) {
					if ( $send_email ) {
						return $this->_sendNotificationEmail( $title );
					} else {
						return true;
					}
				}
			} elseif ( 'TIME' == $s2 ) {
				$today = date( $time_format );
				$tstr = strtotime( $today ) + $gmt_offset_sec;
				$value = strtotime( $i1 );
				if ( $tstr > $value ) {
					if ( $send_email ) {
						return $this->_sendNotificationEmail( $title );
					} else {
						return true;
					}
				}
			}
		} // TIME ONLY
		elseif ( 'IS BEFORE' == $s3 ) {
			if ( 'TIME' == $s2 ) {
				$today = date( $time_format );
				$tstr = strtotime( $today ) + $gmt_offset_sec;
				$value = strtotime( $i1 );
				if ( $tstr < $value ) {
					if ( $send_email ) {
						return $this->_sendNotificationEmail( $title );
					} else {
						return true;
					}
				}
			}
		} // USERNAME && USER ROLE && SOURCE IP
		elseif ( 'IS NOT' == $s3 ) {
			if ( 'USERNAME' == $s2 ) {
				$uid = isset( $this->_alert_data['CurrentUserID'] ) ? $this->_alert_data['CurrentUserID'] : false;
				if ( false === $uid ) {
					$user = get_user_by( 'login', $i1 );
				} else {
					$user = get_user_by( 'id', $uid );
				}
				if ( false === $user ) {
					return false;
				}
				$value = $user->user_login;
				if ( $value != $i1 ) {
					if ( $send_email ) {
						return $this->_sendNotificationEmail( $title );
					} else {
						return true;
					}
				}
			} elseif ( 'USER ROLE' == $s2 ) {
				$role_found = false;
				$roles = $this->_alert_data['CurrentUserRoles'];

				// Convert value of $s6 to lowercase.
				$s6 = strtolower( $s6 );

				foreach ( $roles as $role ) {
					if ( strcasecmp( $s6, $role ) == 0 ) {
						$role_found = true;
					}
				}
				if ( ! $role_found ) {
					if ( $send_email ) {
						return $this->_sendNotificationEmail( $title );
					} else {
						return true;
					}
				}
			} elseif ( 'SOURCE IP' == $s2 ) {
				$value = $this->_alert_data['ClientIP'];
				if ( $i1 != $value ) {
					if ( $send_email ) {
						return $this->_sendNotificationEmail( $title );
					} else {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Send the notification email
	 *
	 * @param string $title - The Notification title.
	 * @return bool
	 */
	public function _sendNotificationEmail( $title = '' ) {
		if ( empty( $this->_email_address ) ) {
			return false;
		}

		$alert = $this->plugin->alerts->GetAlert( $this->_alert_id );
		$alert_message = $alert->GetMessage( (array) $this->_alert_data, array( $this, 'meta_formatter' ) );
		$uid = isset( $this->_alert_data['CurrentUserID'] ) ? $this->_alert_data['CurrentUserID'] : null;
		$username = __( 'System', 'wp-security-audit-log' );
		if ( empty( $uid ) ) { // will happen "on login"
			// This will be populated.
			if ( isset( $this->_alert_data['Username'] ) && ! empty( $this->_alert_data['Username'] ) ) {
				$username = $this->_alert_data['Username'];
			}
		} else {
			$user = get_user_by( 'id', $uid );
			if ( false !== $user ) {
				$username = $user->user_login;
			}
		}

		if ( $this->_alert_date ) {
			$date = $this->_alert_date;
		} else {
			$date = $this->plugin->wsalCommon->GetEmailDatetime();
		}

		$_user_roles = isset( $this->_alert_data['CurrentUserRoles'] ) ? $this->_alert_data['CurrentUserRoles'] : null;
		$user_role = '';
		if ( isset( $_user_roles[0] ) && ! empty( $_user_roles[0] ) ) {
			if ( count( $_user_roles ) > 1 ) {
				$user_role = implode( ', ', $_user_roles );
			} else {
				$user_role = $_user_roles[0];
			}
		}
		$blogname = $this->plugin->wsalCommon->GetBlogname();

		$search = array( '{title}', '{source_ip}', '{alert_id}', '{date_time}', '{message}', '{username}', '{user_role}', '{site}' );
		$replace = array( $title, $this->_alert_data['ClientIP'], $this->_alert_id, $date, $alert_message, $username, $user_role, $blogname );

		if ( $this->_has_template ) {
			$subject = str_replace( $search, $replace, $this->_has_template['subject'] );
			$content = str_replace( $search, $replace, stripslashes( $this->_has_template['body'] ) );
		} else {
			$name = ($this->_is_built_in) ? 'built-in' : 'builder';
			$template = $this->plugin->wsalCommon->GetEmailTemplate( $name );

			$subject = str_replace( $search, $replace, $template['subject'] );
			$content = str_replace( $search, $replace, stripslashes( $template['body'] ) );
		}
		$result = $this->plugin->wsalCommon->SendNotificationEmail( $this->_email_address, $subject, $content, $this->_alert_id );

		return $result;
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

			case '%CommentLink%' == $name:
			case '%CommentMsg%' == $name:
				return $value;

			case '%MetaLink%' == $name:
				return '';

			case '%RevisionLink%' == $name:
				if ( ! empty( $value ) && 'NULL' != $value ) {
					return esc_html( ' Navigate to this URL to view the changes ' . $value );
				} else {
					return '';
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

			case '%LinkFile%' == $name:
				return '<br>To view the requests open the log file ' . esc_url( $value );

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
				if ( $this->plugin->IsMultisite() && $value ) {
					$site_info = get_blog_details( $value, true );
					if ( $site_info ) {
						return ' on site <a href="' . esc_url( $site_info->siteurl ) . '">' . esc_html( $site_info->blogname ) . '</a>';
					}
					return;
				}
				return;

			case '%ReportText%' === $name:
				if ( ! empty( $value ) && 'NULL' != $value ) {
					$report_text = explode( '|', $value );
					if ( isset( $report_text[0] ) && isset( $report_text[1] ) ) {
						$report_text[0] = str_replace( '"', '', $report_text[0] );
						$report_text[1] = str_replace( '"', '', $report_text[1] );
						$report_str = ' from ';
						$report_str .= '<strong>';
						$report_str .= ( ! empty( $report_text[0] ) ) ? esc_html( $report_text[0] ) : 'NULL';
						$report_str .= '</strong>';
						$report_str .= ' to ';
						$report_str .= '<strong>';
						$report_str .= ( ! empty( $report_text[1] ) ) ? esc_html( $report_text[1] ) : 'NULL';
						$report_str .= '</strong>';
						return $report_str;
					}
				}
				return '';

			case '%ChangeText%' === $name:
				return;

			default:
				return '<strong>' . esc_html( $value ) . '</strong>';
		}
	}
}
