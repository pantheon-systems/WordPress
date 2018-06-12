<?php
/**
 * View: Sessions Management View
 *
 * Class file for users sessions managemnent.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WSAL_User_Management_Plugin' ) ) {
	exit( 'You are not allowed to view this page.' );
}

/**
 * Class WSAL_User_Management_Views of the page Users Sessions & Management
 *
 * @package Wsal
 */
class WSAL_User_Management_Views extends WSAL_AbstractView {

	/**
	 * GMT Offset.
	 *
	 * @var integer
	 */
	protected $_gmt_offset = 0;

	/**
	 * Extension directory path.
	 *
	 * @var string
	 */
	public $_base_dir;

	/**
	 * Extension directory url.
	 *
	 * @var string
	 */
	public $_base_url;

	/**
	 * Current User Sessions.
	 *
	 * @var array
	 */
	protected $user_sessions = array();

	/**
	 * Sessions Query Offset.
	 *
	 * @var int
	 */
	protected $sessions_query_offset = 0;

	/**
	 * Transient Prefix.
	 *
	 * @var string
	 */
	private $trans_prefix = 'wsal-';

	/**
	 * Method: Constructor
	 *
	 * @param object $plugin - Instance of WpSecurityAuditLog.
	 * @since  1.0.0
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		// Call to the parent class.
		parent::__construct( $plugin );

		// Ajax call for session auto refresh.
		add_action( 'wp_ajax_SessionAutoRefresh', array( $this, 'SessionAutoRefresh' ) );

		// Set the GMT offset.
		$this->_gmt_offset = $this->_plugin->usermanagement->common->GetGmtOffset();

		// Set paths for plugin.
		$this->_base_dir = WSAL_BASE_DIR . 'extensions/users-sessions-management';
		$this->_base_url = WSAL_BASE_URL . 'extensions/users-sessions-management';

		// Ajax call to destroy user sessions.
		add_action( 'wp_ajax_AjaxSessionsDestroy', array( $this, 'ajax_sessions_destroy' ) );
		add_action( 'wp_ajax_destroy_session', array( $this, 'ajax_destroy_user_session' ) );
		add_action( 'wp_ajax_wsal_terminate_all_sessions', array( $this, 'terminate_all_sessions' ) );

		// Query and save users sessions.
		// $this->query_users_sessions();

		// Listen for session search submit.
		add_action( 'admin_post_wsal_sessions_search', array( $this, 'sessions_search_form' ) );
	}

	/**
	 * Method: Search sessions form submit redirect.
	 *
	 * @since 3.1.2
	 */
	public function sessions_search_form() {
		// Get $_GET array.
		$filter_input_args = array(
			'type' => FILTER_SANITIZE_STRING,
			'keyword' => FILTER_SANITIZE_STRING,
			'wsal_session_search__nonce' => FILTER_SANITIZE_STRING,
		);
		$get_array = filter_input_array( INPUT_GET, $filter_input_args );

		// Get redirect URL.
		$redirect = filter_input( INPUT_GET, '_wp_http_referer' );

		// Verify nonce.
		if ( isset( $get_array['wsal_session_search__nonce'] )
			&& wp_verify_nonce( $get_array['wsal_session_search__nonce'], 'wsal_session_search__nonce' ) ) {
			$redirect = add_query_arg( array(
				'type' => $get_array['type'],
				'keyword' => $get_array['keyword'],
			), $redirect );
		}

		wp_safe_redirect( $redirect );
		die();
	}

	/**
	 * Method: Query and save users sessions.
	 *
	 * @since 3.1.0
	 */
	private function query_users_sessions() {
		// Check user sessions transients.
		$get_fn = $this->_plugin->IsMultisite() ? 'get_site_transient' : 'get_transient'; // Check for multisite.
		$user_sessions = $get_fn( $this->trans_prefix . 'users_sessions' );
		$user_sessions_offset = $get_fn( $this->trans_prefix . 'users_sessions_offset' );

		if ( false === $user_sessions || empty( $user_sessions ) || false === $user_sessions_offset ) {
			// Get current blog ID.
			$current_blog_id = (int) $this->get_view_site_id();

			// Query and set current user sessions.
			$this->user_sessions = $this->_plugin->usermanagement->common->GetAllSessions( $current_blog_id );

			// Set sessions query offset.
			$sessions = count( $this->user_sessions );
			$this->sessions_query_offset = (int) floor( $sessions / 10 ) * 10;

			// Set sessions transients.
			$set_fn = $this->_plugin->IsMultisite() ? 'set_site_transient' : 'set_transient';
			$set_fn( $this->trans_prefix . 'users_sessions', $this->user_sessions, DAY_IN_SECONDS );
			$set_fn( $this->trans_prefix . 'users_sessions_offset', $this->sessions_query_offset, DAY_IN_SECONDS );
		} else {
			// Set user sessions class members.
			$this->user_sessions = $user_sessions;
			$this->sessions_query_offset = $user_sessions_offset;
		}
	}

	/**
	 * Method: Get View Title.
	 */
	public function GetTitle() {
		return __( 'Users Sessions & Management', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Icon.
	 */
	public function GetIcon() {
		return 'dashicons-admin-generic';
	}

	/**
	 * Method: Get View Name.
	 */
	public function GetName() {
		return __( 'Logged In Users', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Weight.
	 */
	public function GetWeight() {
		return 7;
	}

	/**
	 * Method: Get View Header.
	 */
	public function Header() {
		// Sessions styles.
		wp_enqueue_style(
			'wsal-security-css',
			$this->_base_url . '/css/style.css',
			array(),
			filemtime( $this->_base_dir . '/css/style.css' )
		);

		// Remodal styles.
		wp_enqueue_style( 'wsal-remodal', $this->_base_url . '/css/remodal.css', array(), '1.1.1' );
		wp_enqueue_style( 'wsal-remodal-theme', $this->_base_url . '/css/remodal-default-theme.css', array(), '1.1.1' );

		// Darktooltip styles.
		wp_enqueue_style(
			'darktooltip',
			$this->_plugin->GetBaseUrl() . '/css/darktooltip.css',
			array(),
			'0.4.0'
		);
	}

	/**
	 * Method: Get View Footer.
	 */
	public function Footer() {
		// Remodal script.
		wp_enqueue_script(
			'wsal-remodal-js',
			$this->_base_url . '/js/remodal.min.js',
			array(),
			'1.1.1'
		);

		// Darktooltip js.
		wp_enqueue_script(
			'darktooltip', // Identifier.
			$this->_plugin->GetBaseUrl() . '/js/jquery.darktooltip.js', // Script location.
			array( 'jquery' ), // Depends on jQuery.
			'0.4.0' // Script version.
		);

		// Sessions script.
		wp_enqueue_script(
			'wsal-security-js',
			$this->_base_url . '/js/script.js',
			array( 'jquery' )
		);
		$script_data = array(
			'script_nonce' => wp_create_nonce( 'script_nonce' ),
		);
		wp_localize_script( 'wsal-security-js', 'script_data', $script_data );
	}

	/**
	 * Auto refresh of the page if check session count changes.
	 */
	public function SessionAutoRefresh() {
		if ( ! isset( $_REQUEST['sessions_count'] ) ) {
			die( 'Session count parameter expected.' );
		}
		if ( ! isset( $_REQUEST['blog_id'] ) ) {
			die( 'Session count parameter expected.' );
		}

		$old = (int) $_REQUEST['sessions_count'];

		$current_blog_id = (int) $_REQUEST['blog_id'];
		$results = $this->_plugin->usermanagement->common->GetAllSessions( $current_blog_id, $this->sessions_query_offset );
		$new = count( $results );

		if ( $old == $new ) {
			echo 'false';
		} else {
			// Delete sessions transient to reset.
			$delete_fn = $this->_plugin->IsMultisite() ? 'delete_site_transient' : 'delete_transient'; // Check for multisite.
			$delete_fn( $this->trans_prefix . 'users_sessions' );
			$delete_fn( $this->trans_prefix . 'users_sessions_offset' );
			echo $new;
		}
		die;
	}

	/**
	 * Method: Search Sessions.
	 *
	 * @return array - Array of search results.
	 * @throws Exception - Throw exception if sessions don't exist.
	 * @since 3.1.2
	 */
	protected function sessions_search() {
		// Get post array.
		$filter_input_args = array(
			'type' => FILTER_SANITIZE_STRING,
			'keyword' => FILTER_SANITIZE_STRING,
		);
		$get_array = filter_input_array( INPUT_GET, $filter_input_args );

		// Verify user sessions exists.
		if ( ! is_array( $this->user_sessions ) || empty( $this->user_sessions ) ) {
			throw new Exception( esc_html__( 'User sessions do not exist.', 'wp-security-audit-log' ) );
		}

		// Search results.
		$results = array();

		// Get the type of search made.
		if ( isset( $get_array['type'] ) ) {
			switch ( $get_array['type'] ) {
				case 'username':
					// Search by username.
					if ( isset( $get_array['keyword'] ) ) {
						// Get user from WP.
						$user = get_user_by( 'login', $get_array['keyword'] );

						// If user exists then search in sessions.
						if ( $user && $user instanceof WP_User ) {
							// If user id match then add the sessions array to results array.
							if ( isset( $this->user_sessions[ $user->ID ] ) ) {
								$results[ $user->ID ] = $this->user_sessions[ $user->ID ];
							}
						}
					}
					break;

				case 'email':
					// Search by email.
					if ( isset( $get_array['keyword'] ) && is_email( $get_array['keyword'] ) ) {
						// Get user from WP.
						$user = get_user_by( 'email', $get_array['keyword'] );

						// If user exists then search in sessions.
						if ( $user && $user instanceof WP_User ) {
							// If user id match then add the sessions array to results array.
							if ( isset( $this->user_sessions[ $user->ID ] ) ) {
								$results[ $user->ID ] = $this->user_sessions[ $user->ID ];
							}
						}
					}
					break;

				case 'firstname':
					// Search by user first name.
					if ( isset( $get_array['keyword'] ) ) {
						// Ensure that incoming keyword is string.
						$name = (string) $get_array['keyword'];

						// Get users.
						$users_array = get_users(
							array(
								'meta_key'      => 'first_name',
								'meta_value'    => $name,
								'fields'        => array( 'ID', 'user_login' ),
								'meta_compare'  => 'LIKE',
							)
						);

						// Extract user id.
						$user_ids = array();
						foreach ( $users_array as $user ) {
							$user_ids[] = $user->ID;
						}

						// If user_ids array is not empty then.
						if ( ! empty( $user_ids ) ) {
							// Search sessions by user id.
							foreach ( $user_ids as $user_id ) {
								// If user id match then add the sessions array to results array.
								if ( isset( $this->user_sessions[ $user_id ] ) ) {
									$results[ $user_id ] = $this->user_sessions[ $user_id ];
								}
							}
						}
					}
					break;

				case 'lastname':
					// Search by user last name.
					if ( isset( $get_array['keyword'] ) ) {
						// Ensure that incoming keyword is string.
						$name = (string) $get_array['keyword'];

						// Get users.
						$users_array = get_users(
							array(
								'meta_key'      => 'last_name',
								'meta_value'    => $name,
								'fields'        => array( 'ID', 'user_login' ),
								'meta_compare'  => 'LIKE',
							)
						);

						// Extract user id.
						$user_ids = array();
						foreach ( $users_array as $user ) {
							$user_ids[] = $user->ID;
						}

						// If user_ids array is not empty then.
						if ( ! empty( $user_ids ) ) {
							// Search sessions by user id.
							foreach ( $user_ids as $user_id ) {
								// If user id match then add the sessions array to results array.
								if ( isset( $this->user_sessions[ $user_id ] ) ) {
									$results[ $user_id ] = $this->user_sessions[ $user_id ];
								}
							}
						}
					}
					break;

				case 'ip':
					// Search by ip.
					if ( isset( $get_array['keyword'] ) ) {
						// Search sessions by ip.
						foreach ( $this->user_sessions as $user_id => $sessions ) {
							// Search for matching IPs in $sessions.
							foreach ( $sessions as $session ) {
								if ( $get_array['keyword'] === $session['ip'] ) {
									$results[ $user_id ][] = $session;
								}
							}
						}
					}
					break;

				case 'user-role':
					// Search by user-role.
					if ( isset( $get_array['keyword'] ) ) {
						// Search sessions by user role.
						foreach ( $this->user_sessions as $user_id => $sessions ) {
							// Search for matching user role in $sessions.
							foreach ( $sessions as $session ) {
								// User roles.
								$user_roles = $session['role'];

								// Single user role.
								if ( ! strpos( $user_roles, ',' ) ) {
									if ( $get_array['keyword'] === $session['role'] ) {
										$results[ $user_id ][] = $session;
									}
								} else {
									$user_roles = explode( ', ', $user_roles );
									if ( in_array( $get_array['keyword'], $user_roles, true ) ) {
										$results[ $user_id ][] = $session;
									}
								}
							}
						}
					}
					break;

				default:
					// Default case.
					break;
			}
		}

		// Return results.
		return $results;
	}

	/**
	 * Method: Save Settings.
	 *
	 * @throws Exception - Exception on nonce verification failed.
	 * @return void|object
	 */
	protected function Save() {
		// Get filtered $_POST array.
		$post_array = filter_input_array( INPUT_POST );

		// Verify nonce.
		if ( isset( $post_array['_wpnonce'] ) && ! wp_verify_nonce( $post_array['_wpnonce'], 'wsal-users-sessions-save' ) ) {
			return new Exception( esc_html__( 'Nonce verification failed', 'wp-security-audit-log' ) );
		}

		// Allow Multiple Sessions.
		$this->_plugin->usermanagement->common->AddGlobalOption( 'user-management-allow-multi-sessions', $post_array['MultiSessions'] );

		/**
		 * 1. Save number of sessions allowed.
		 * 2. Blocked sessions override option.
		 * 3. Sessions override password.
		 *
		 * @since 3.1.4
		 */
		$this->_plugin->usermanagement->common->AddGlobalOption( 'user-management-allowed-sessions-number', $post_array['multi_sessions_limit'] );
		$this->_plugin->usermanagement->common->AddGlobalOption( 'user-management-blocked-sessions-override', isset( $post_array['session_override'] ) ? $post_array['session_override'] : false );

		// If password field is set then save it.
		if ( isset( $post_array['session_override_pass'] ) && ! empty( $post_array['session_override_pass'] ) ) {
			// Hash it.
			$override_password = wp_hash_password( $post_array['session_override_pass'] );

			// Save the password.
			$this->_plugin->usermanagement->common->AddGlobalOption( 'user-management-sessions-override-password', $override_password );
		}

		// Emails to alert on multiple sessions.
		if ( isset( $post_array['AlertMultiSessions'] ) ) {
			// Remove whitespaces.
			$emails = trim( $post_array['AlertMultiSessionsEmails'] );

			// Convert email string to array.
			$emails = explode( ', ', $emails );

			// Apply `is_email` to each element.
			$result_emails = array_map( 'is_email', $emails );

			// Check if there is any false value in result array.
			if ( in_array( false, $result_emails, true ) ) {
				// Search for the false value in result array.
				$key = array_search( false, $result_emails, true );

				// Remove the false value from the result array.
				unset( $result_emails[ $key ] );

				// Convert the result email array back to string.
				$emails = implode( ', ', $result_emails );

				// Save it.
				$this->_plugin->usermanagement->common->SetMultiSessions( 1, $emails );
			} else {
				// Convert the result email array back to string.
				$emails = implode( ', ', $result_emails );

				// Save it.
				$this->_plugin->usermanagement->common->SetMultiSessions( 1, $emails );
			}
		} else {
			$this->_plugin->usermanagement->common->SetMultiSessions( 0 );
		}

		// Emails to alert when a user is blocked.
		if ( isset( $post_array['AlertBlocked'] ) ) {
			// Remove whitespaces.
			$emails = trim( $post_array['AlertBlockedEmails'] );

			// Convert email string to array.
			$emails = explode( ', ', $emails );

			// Apply `is_email` to each element.
			$result_emails = array_map( 'is_email', $emails );

			// Check if there is any false value in result array.
			if ( in_array( false, $result_emails, true ) ) {
				// Search for the false value in result array.
				$key = array_search( false, $result_emails, true );

				// Remove the false value from the result array.
				unset( $result_emails[ $key ] );

				// Convert the result email array back to string.
				$emails = implode( ', ', $result_emails );

				// Save it.
				$this->_plugin->usermanagement->common->SetBlocked( 1, $emails );
			} else {
				// Convert the result email array back to string.
				$emails = implode( ', ', $result_emails );

				// Save it.
				$this->_plugin->usermanagement->common->SetBlocked( 1, $emails );
			}
		} else {
			$this->_plugin->usermanagement->common->SetBlocked( 0 );
		}

		// Users Sessions Error Message.
		if ( isset( $post_array['sessions_error_message'] ) ) {
			$error_message = wp_kses( $post_array['sessions_error_message'], $this->_plugin->allowed_html_tags );
			$this->_plugin->usermanagement->common->AddGlobalOption( 'user-management-sessions-error-message', $error_message );
		}

		// Auto terminate sessions.
		if ( isset( $post_array['auto_terminate'] ) ) {
			$this->_plugin->usermanagement->common->set_auto_sessions_terminate( (int) $post_array['auto_terminate'], $post_array['auto_terminate_hours'] );
		} else {
			$this->_plugin->usermanagement->common->set_auto_sessions_terminate( 0 );
		}
	}

	public function Render() {
		// Get post array via global array filter.
		$post_array = filter_input_array( INPUT_POST );

		// Query and save users sessions.
		$this->query_users_sessions();

		if ( ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
			$network_admin = get_site_option( 'admin_email' );
			$message = esc_html__( 'To know who is logged in to this website please contact the multisite network administrator – ', 'wp-security-audit-log' );
			$message .= '<a href="mailto:' . esc_attr( $network_admin ) . '" target="_blank">' . esc_html( $network_admin ) . '</a>';
			wp_die( wp_kses( $message, $this->_plugin->allowed_html_tags ) );
		}

		/**
		 * Search sessions form.
		 *
		 * @since 3.1.2
		 */
		$search_results = array();
		if ( filter_input( INPUT_GET, 'keyword', FILTER_SANITIZE_STRING ) ) {
			try {
				$search_results = $this->sessions_search();

				if ( empty( $search_results ) ) :
					?>
					<div class="updated">
						<p><?php esc_html_e( 'No search results were found.', 'wp-security-audit-log' ); ?></p>
					</div>
					<?php
				elseif ( ! empty( $search_results ) ) :
					?>
					<div class="updated">
						<p>
							<?php esc_html_e( 'Showing results for ', 'wp-security-audit-log' ); ?>
							<?php echo filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING ); ?>
							<strong><?php echo filter_input( INPUT_GET, 'keyword', FILTER_SANITIZE_STRING ); ?></strong>
						</p>
					</div>
					<?php
				endif;
			} catch ( Exception $ex ) {
				?>
				<div class="error"><p><?php esc_html_e( 'Error: ', 'wp-security-audit-log' ); ?><?php echo $ex->getMessage(); ?></p></div>
				<?php
			}
		}

		// Save settings form.
		if ( isset( $post_array['submit'] ) ) {
			try {
				$this->Save();
				?>
				<div class="updated">
					<p><?php esc_html_e( 'Settings have been saved.', 'wp-security-audit-log' ); ?></p>
				</div>
				<?php
			} catch ( Exception $ex ) {
				?>
				<div class="error"><p><?php esc_html_e( 'Error: ', 'wp-security-audit-log' ); ?><?php echo $ex->getMessage(); ?></p></div>
				<?php
			}
		}

		// Get the type of name to display from settings.
		$type_name = $this->_plugin->settings->get_type_username();
		if ( 'display_name' === $type_name ) {
			$name_column = __( 'User', 'wp-security-audit-log' );
		} elseif ( 'username' === $type_name ) {
			$name_column = __( 'Username', 'wp-security-audit-log' );
		}

		$columns = array(
			'username'   => $name_column,
			'created'    => esc_html__( 'Created', 'wp-security-audit-log' ),
			'expiration' => esc_html__( 'Expires', 'wp-security-audit-log' ),
			'ip'         => esc_html__( 'Source IP', 'wp-security-audit-log' ),
			'alert'      => esc_html__( 'Last Alert', 'wp-security-audit-log' ),
			'action'     => esc_html__( 'Action', 'wp-security-audit-log' ),
		);

		$current_blog_id = (int) $this->get_view_site_id();

		// $sorted  = array();
		$spp     = ! empty( $_GET['sessions_per_page'] ) ? absint( $_GET['sessions_per_page'] ) : 10;
		$paged   = ! empty( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
		$offset  = absint( ($paged - 1) * $spp );
		$orderby = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'created';
		$order   = ! empty( $_GET['order'] ) ? $_GET['order'] : 'desc';

		if ( empty( $search_results ) ) {
			$results = array_slice( $this->user_sessions, $offset, $spp );
		} else {
			$results = array_slice( $search_results, $offset, $spp );
		}

		// foreach ( $results as $user_id => $user_session ) {
		// 	foreach ( $user_session as $session ) {
		// 		if ( 'ip' === $orderby ) {
		// 			$sorted[] = str_replace( '.', '', $session[ $orderby ] );
		// 		} else {
		// 			$sorted[] = $session[ $orderby ];
		// 		}
		// 	}
		// }

		if ( 'asc' == $order ) {
			// array_multisort( $sorted, SORT_ASC, $results );
		} else {
			// array_multisort( $sorted, SORT_DESC, $results );
		}

		$total_sessions = empty( $search_results ) ? count( $this->user_sessions ) : count( $search_results );
		$current_admins = $this->_plugin->usermanagement->common->CountAdministratorRole( $this->user_sessions );
		$pages          = absint( ceil( $total_sessions / $spp ) );

		switch ( $order ) {
			case 'asc':
				$order_flip = 'desc';
				break;
			case 'desc':
				$order_flip = 'asc';
				break;
			default:
				$order_flip = 'desc';
		}

		$users = $this->_plugin->usermanagement->common->GetUsersWithSessions( $current_blog_id );

		ob_start();

		// Selected type.
		$type = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING );

		// Searched keyword.
		$keyword = filter_input( INPUT_GET, 'keyword', FILTER_SANITIZE_STRING );

		// Pagination first page link.
		$first_link_args['page'] = 'wsal-user-management-views';
		if ( ! empty( $type ) && ! empty( $keyword ) ) {
			$first_link_args['type'] = $type;
			$first_link_args['keyword'] = $keyword;
		}
		$first_link = add_query_arg( $first_link_args, admin_url( 'admin.php' ) );
		$base_link  = $first_link;

		// Pagination last link.
		$last_link_args['paged'] = $pages;
		if ( ! empty( $type ) && ! empty( $keyword ) ) {
			$last_link_args['type'] = $type;
			$last_link_args['keyword'] = $keyword;
		}
		$last_link  = add_query_arg( $last_link_args, $first_link );

		// Previous link.
		if ( $paged > 2 ) {
			$prev_link_args = array(
				'paged' => absint( $paged - 1 ),
				'sessions_per_page' => $spp,
			);
			if ( ! empty( $type ) && ! empty( $keyword ) ) {
				$prev_link_args['type'] = $type;
				$prev_link_args['keyword'] = $keyword;
			}
			$prev_link = add_query_arg( $prev_link_args, $first_link );
		} else {
			$prev_link = $first_link;
		}

		// Next link.
		if ( $pages > $paged ) {
			$next_link_args = array(
				'paged' => absint( $paged + 1 ),
				'sessions_per_page' => $spp,
			);
			if ( ! empty( $type ) && ! empty( $keyword ) ) {
				$next_link_args['type'] = $type;
				$next_link_args['keyword'] = $keyword;
			}
			$next_link = add_query_arg( $next_link_args, $first_link );
		} else {
			$next_link = $last_link;
		}

		$datetimeFormat = $this->_plugin->usermanagement->common->GetDatetimeFormat();

		// Calculate the number of sessions after offset.
		$session_token = $total_sessions % 10;

		if ( empty( $search_results ) ) :
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					SessionAutoRefresh('<?php echo json_encode( array( 'token' => $session_token, 'blog_id' => $current_blog_id ) ); ?>');
				});
			</script>
		<?php endif; ?>
		<div class="tablenav-pages">
			<span class="displaying-num"><?php printf( __( '%s items', 'wp-security-audit-log' ), number_format( $total_sessions ) ); ?></span>
			<?php
			if ( $pages > 1 ) {
			?>
				<span class="pagination-links">
					<a class="first-page<?php echo (1 === $paged) ? ' disabled' : null; ?>" title="<?php esc_attr_e( 'Go to the first page' ); ?>" href="<?php echo esc_url( $first_link ); ?>">«</a>
					<a class="prev-page<?php echo (1 === $paged) ? ' disabled' : null; ?>" title="<?php esc_attr_e( 'Go to the previous page' ); ?>" href="<?php echo esc_url( $prev_link ); ?>">‹</a>
					<span class="paging-input">
						<?php echo absint( $paged ); ?> <?php esc_html_e( 'of', 'wp-security-audit-log' ); ?> <span class="total-pages"><?php echo absint( $pages ); ?></span>
					</span>
					<a class="next-page<?php echo ($pages === $paged) ? ' disabled' : null; ?>" title="<?php esc_attr_e( 'Go to the next page' ); ?>" href="<?php echo esc_url( $next_link ); ?>">›</a>
					<a class="last-page<?php echo ($pages === $paged) ? ' disabled' : null; ?>" title="<?php esc_attr_e( 'Go to the last page' ); ?>" href="<?php echo esc_url( $last_link ); ?>">»</a>
				</span>
				<?php
			}
			?>
		</div>
		<?php $pagination = ob_get_clean(); ?>
		<a href="#wsal-terminate-sessions" id="wsal_terminate_all" class="button alignright" data-tooltip="<?php esc_attr_e( 'By clicking this button you will terminate all sessions but yours.', 'wp-security-audit-log' ); ?>">
			<span class="dashicons dashicons-no"></span>
			<?php esc_html_e( 'Terminate All Sessions', 'wp-security-audit-log' ); ?>
		</a>
		<div class="wrap">
			<h2 id="wsal-tabs" class="nav-tab-wrapper">
				<a href="#tab-list" class="nav-tab"><?php esc_html_e( 'Logged In Users', 'wp-security-audit-log' ); ?></a>
				<?php if ( $this->_plugin->settings->CurrentUserCan( 'edit' ) ) : ?>
					<a href="#tab-rules" class="nav-tab"><?php esc_html_e( 'Users Sessions Management', 'wp-security-audit-log' ); ?></a>
				<?php endif; ?>
			</h2>
			<div class="nav-tabs">
				<div class="wsal-tab" id="tab-list">
					<p><?php esc_html_e( 'Total number of sessions with Administrator Role: ', 'wp-security-audit-log' ); ?> <strong><?php echo number_format( $current_admins ); ?></strong></p>

					<!-- Sessions Search -->
					<form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="wsal_sessions__search" class="wsal_sessions_search">
						<?php
						// Options array.
						$options_arr = array(
							'username'  => __( 'Username', 'wp-security-audit-log' ),
							'email'     => __( 'Email', 'wp-security-audit-log' ),
							'firstname' => __( 'First Name', 'wp-security-audit-log' ),
							'lastname'  => __( 'Last Name', 'wp-security-audit-log' ),
							'ip'        => __( 'IP Address', 'wp-security-audit-log' ),
							'user-role' => __( 'User Role', 'wp-security-audit-log' ),
						);
						?>
						<select name="type" id="type">
							<?php foreach ( $options_arr as $option_value => $option_text ) : ?>
								<option value="<?php echo esc_attr( $option_value ); ?>"
									<?php echo ( $option_value === $type ) ? ' selected' : false; ?>>
									<?php echo esc_html( $option_text ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<input type="text" name="keyword" id="keyword" value="<?php echo esc_attr( $keyword ); ?>">
						<input type="hidden" name="action" value="wsal_sessions_search">
						<?php wp_nonce_field( 'wsal_session_search__nonce', 'wsal_session_search__nonce' ); ?>
						<input type="submit" class="button" name="wsal_session_search__btn" id="wsal_session_search__btn" value="<?php esc_attr_e( 'Search', 'wp-security-audit-log' ); ?>">
					</form>
					<!-- / Sessions Search -->

					<form id="sessionsForm" method="post">
						<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
						<input type="hidden" id="wsal-cbid" name="wsal-cbid" value="<?php echo esc_attr( isset( $_REQUEST['wsal-cbid'] ) ? $_REQUEST['wsal-cbid'] : '0' ); ?>" />
						<div class="tablenav top">
							<?php
							// Show site alerts widget.
							if ( $this->is_multisite() && $this->is_main_blog() ) {
								$curr = $this->get_view_site_id();
								?>
								Show:
								<div class="wsal-ssa">
									<?php if ( $this->get_site_count() > 15 ) : ?>
										<?php $curr = $curr ? get_blog_details( $curr ) : null; ?>
										<?php $curr = $curr ? ($curr->blogname . ' (' . $curr->domain . ')') : 'Network-wide Logins'; ?>
										<input type="text" value="<?php echo esc_attr( $curr ); ?>"/>
									<?php else : ?>
										<select onchange="WsalSsasChange(value);">
											<option value="0"><?php esc_html_e( 'Network-wide Logins', 'wp-security-audit-log' ); ?></option>
											<?php foreach ( $this->get_sites() as $info ) : ?>
												<option value="<?php echo $info->blog_id; ?>" <?php echo ( $info->blog_id == $curr ) ? 'selected="selected"' : false; ?>>
													<?php echo esc_html( $info->blogname ) . ' (' . esc_html( $info->domain ) . ')'; ?>
												</option>
											<?php endforeach; ?>
										</select>
									<?php endif; ?>
								</div>
								<?php
							}
							?>
							<?php echo $pagination; // xss ok. ?>
							<br class="clear">
						</div>
						<?php if ( empty( $results ) ) { ?>
							<p><?php esc_html_e( 'Currently there are no active user sessions on this site.', 'wp-security-audit-log' ); ?></p>
						<?php } else { ?>
							<table class="wp-list-table widefat fixed users">
								<thead>
									<tr>
										<?php foreach ( $columns as $slug => $name ) : ?>
											<?php if ( $slug == 'action' ) { ?>
												<th scope="col" class="manage-column column-<?php echo esc_attr( $slug ); ?>"><span><?php echo esc_html( $name ); ?></span></th>
											<?php } else { ?>
												<th scope="col" class="manage-column column-<?php echo esc_attr( $slug ); ?> <?php echo ($slug === $orderby) ? 'sorted' : 'sortable'; ?> <?php echo ($slug === $orderby && $order) ? esc_attr( strtolower( $order ) ) : 'desc'; ?>">
													<?php
													$sort_url = add_query_arg( array(
														'orderby' => $slug,
														'order' => ( $slug === $orderby ) ? esc_attr( $order_flip ) : 'asc',
													) );
													?>
													<a href="<?php echo esc_url( $sort_url ); ?>">
														<span><?php echo esc_html( $name ); ?></span>
														<span class="sorting-indicator"></span>
													</a>
												</th>
											<?php } ?>
										<?php endforeach; ?>
									</tr>
								</thead>
								<tbody id="the-list">
									<?php
									$i = 0;
									foreach ( $results as $user_id => $result_session ) {
										$i++;
										?>
										<tr <?php echo ( 0 !== $i % 2 ) ? 'class="alternate"' : ''; ?>>
											<td colspan="6">
												<table class="wp-list-table widefat fixed users">
													<?php
													foreach ( $result_session as $result ) :
														$user_id     = absint( $result['user_id'] );
														$edit_link   = add_query_arg(
															array(
																'wp_http_referer' => urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
															),
															self_admin_url( sprintf( 'user-edit.php?user_id=%d', $user_id ) )
														);
														$created = str_replace( '$$$', substr( number_format( fmod( $result['created'] + $this->_gmt_offset, 1 ), 3 ), 2 ), date( $datetimeFormat, $result['created'] + $this->_gmt_offset ) );
														$expiration = str_replace( '$$$', substr( number_format( fmod( $result['expiration'] + $this->_gmt_offset, 1 ), 3 ), 2 ), date( $datetimeFormat, $result['expiration'] + $this->_gmt_offset ) );
														$user = get_user_by( 'id', $user_id );
														?>
														<tr>
															<td class="username column-username" data-colname="Username">
																<?php echo get_avatar( $user_id, 32 ); ?>
																<a href="<?php echo esc_url( $edit_link ); ?>" target="_blank">
																	<?php
																	if ( 'display_name' === $type_name && ! empty( $user->first_name ) ) {
																		echo esc_html( $user->first_name . ' ' . $user->last_name );
																	} else {
																		echo esc_html( $user->user_login );
																	}
																	?>
																</a>
																<br>
																<?php echo $this->_plugin->usermanagement->common->GetUserRoles( $user_id, $result['role'], $current_blog_id ); ?>
																<br><br>
																<span><strong><?php esc_html_e( 'Session ID: ', 'wp-security-audit-log' ); ?></strong><?php echo esc_html( $result['token_hash'] ); ?></span>
															</td>
															<td class="created column-created" data-colname="Created">
																<?php echo $created; ?>
															</td>
															<td class="expiration column-expiration" data-colname="Expires">
																<?php echo $expiration; ?>
															</td>
															<td class="ip column-ip" data-colname="Source IP">
																<?php
																if ( ! empty( $result['last_alert']['client_ip'] ) ) :
																	$ip_address_args = array(
																		'utm_source'   => 'plugin',
																		'utm_medium'   => 'referral',
																		'utm_campaign' => 'WPSAL',
																	);
																	$ip_address = add_query_arg( $ip_address_args, 'http://whatismyipaddress.com/ip/' . $result['last_alert']['client_ip'] );
																	?>
																	<a target="_blank" href="<?php echo esc_attr( $ip_address ); ?>">
																		<?php echo esc_html( $result['last_alert']['client_ip'] ); ?>
																	</a>
																<?php else : ?>
																	<?php echo esc_html__( 'IP not found.', 'wp-security-audit-log' ); ?>
																<?php endif; ?>
															</td>
															<td class="alert column-alert" data-colname="Last Alert">
																<?php echo ( isset( $result['last_alert'] ) ) ? $result['last_alert']['message'] : false; ?>
															</td>
															<td class="action column-action" data-colname="Action">
																<?php
																if ( wp_get_session_token() != $result['token_hash'] ) {
																	?>
																	<a href="#"
																		data-action="destroy_session"
																		data-user-id="<?php echo esc_attr( $user_id ); ?>"
																		data-token="<?php echo esc_attr( $result['token_hash'] ); ?>"
																		data-wpnonce="<?php echo esc_attr( wp_create_nonce( sprintf( 'destroy_session_nonce-%d', $user_id ) ) ); ?>"
																		class="button wsal_destroy_session">
																		<?php esc_html_e( 'Destroy Session', 'wp-security-audit-log' ); ?>
																	</a>
																	<?php
																}
																?>
															</td>
														</tr>
													<?php endforeach; ?>
												</table>
											</td>
										</tr>
										<?php
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<?php foreach ( $columns as $slug => $name ) : ?>
											<?php if ( $slug == 'action' ) { ?>
												<th scope="col" class="manage-column column-<?php echo esc_attr( $slug ); ?>"><span><?php echo esc_html( $name ); ?></span></th>
											<?php } else { ?>
												<th scope="col" class="manage-column column-<?php echo esc_attr( $slug ); ?> <?php echo ($slug === $orderby) ? 'sorted' : 'sortable'; ?> <?php echo ($slug === $orderby && $order) ? esc_attr( strtolower( $order ) ) : 'desc'; ?>">
													<a href="
														<?php
														echo esc_url(
															add_query_arg(
																array(
																	'orderby' => $slug,
																	'order' => ($slug === $orderby) ? esc_attr( $order_flip ) : 'asc',
																)
															)
														);
														?>
														">
														<span><?php echo esc_html( $name ); ?></span>
														<span class="sorting-indicator"></span>
													</a>
												</th>
											<?php } ?>
										<?php endforeach; ?>
									</tr>
								</tfoot>
							</table>
						<?php } ?>
						<div class="tablenav bottom">
							<?php if ( ! $this->_plugin->usermanagement->common->is_users_sessions_in_limit( $current_blog_id ) ) : ?>
								<div class="alignleft actions">
									<p>
										<?php echo wp_kses( 'There are more than 100 sessions but the plugin did not retrieve them all since the server might not have enough resources.<br />Please use the button below to log out all sessions but yours.', $this->_plugin->allowed_html_tags ); ?>
									</p>
									<input type="button" id="delete_all_sessions" class="button button-primary" value="<?php esc_attr_e( 'Logout all sessions but mine', 'wp-security-audit-log' ); ?>">
								</div>
							<?php endif; ?>
							<?php echo $pagination; // xss ok. ?>
							<br class="clear">
						</div>
					</form>
				</div>

				<?php if ( $this->_plugin->settings->CurrentUserCan( 'edit' ) ) : ?>
					<!-- Tab Logins Management -->
					<div class="wsal-tab" id="tab-rules">
						<form id="wsal-rules" method="POST" autocomplete="off">
							<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
							<?php wp_nonce_field( 'wsal-users-sessions-save' ); ?>
							<table class="form-table widefat">
								<tbody>
									<tr>
										<th><label for="allow"><?php esc_html_e( 'Multiple sessions with the same user', 'wp-security-audit-log' ); ?></label></th>
										<td>
											<fieldset>
												<?php
												// Allow multiple sessions option.
												$is_allow = $this->_plugin->usermanagement->common->GetOptionByName( 'user-management-allow-multi-sessions' );

												// Show blocked sessions override option.
												$show_blocked_session = ( '1' !== $is_allow ) ? 'disabled' : false;
												?>
												<label for="allow">
													<input type="radio" name="MultiSessions" id="allow" <?php checked( $is_allow, '0' ); ?> value="0">
													<span><?php esc_html_e( 'Allow', 'wp-security-audit-log' ); ?></span>
												</label>
												<br/>
												<label for="allow-limited">
													<input type="radio" name="MultiSessions" id="allow-limited" <?php checked( $is_allow, 'allow-limited' ); ?> value="allow-limited">
													<span>
														<?php
														$allowed_sessions = (int) $this->_plugin->usermanagement->common->GetOptionByName( 'user-management-allowed-sessions-number', 3 );
														$allow_limited = '<input type="number" name="multi_sessions_limit" id="multi-sessions-limit" value="' . esc_attr( $allowed_sessions ) . '" />';
														printf( __( 'Allow up to %s sessions', 'wp-security-audit-log' ), $allow_limited );
														?>
													</span>
												</label>
												<br/>
												<label for="block">
													<input type="radio" name="MultiSessions" id="block" <?php checked( $is_allow, '1' ); ?> value="1">
													<span><?php esc_html_e( 'Block', 'wp-security-audit-log' ); ?></span>
												</label>
												<br/>
												<span class="description">
													<?php esc_html_e( 'By allowing multiple sessions two or more people can login to WordPress using the same username. By blocking them, once a person is logged in with a username, if another person tries to login with the same username they will be blocked.', 'wp-security-audit-log' ); ?>
												</span>
											</fieldset>
										</td>
									</tr>
									<tr id="wsal_blocked_session_override">
										<th><label for="without_warning"><?php esc_html_e( 'Allow blocked sessions to override existing sessions', 'wp-security-audit-log' ); ?></label></th>
										<?php $session_override = $this->_plugin->usermanagement->common->GetOptionByName( 'user-management-blocked-sessions-override', 'override_block' ); ?>
										<td>
											<fieldset <?php echo ( $show_blocked_session ) ? esc_attr( $show_blocked_session ) : false; ?>>
												<label for="override_block">
													<input type="radio" name="session_override" id="override_block" <?php checked( $session_override, 'override_block' ); ?> value="override_block">
													<span><?php esc_html_e( 'No, do not allow override', 'wp-security-audit-log' ); ?></span>
												</label>
												<br/>
												<label for="without_warning">
													<input type="radio" name="session_override" id="without_warning" <?php checked( $session_override, 'without_warning' ); ?> value="without_warning">
													<span><?php esc_html_e( 'Yes and terminate the existing session without warning', 'wp-security-audit-log' ); ?></span>
												</label>
												<br/>
												<label for="with_warning">
													<input type="radio" name="session_override" id="with_warning" <?php checked( $session_override, 'with_warning' ); ?> value="with_warning">
													<span>
														<?php esc_html_e( 'Yes, as long as the user knows the override password: ', 'wp-security-audit-log' ); ?>
														<input type="password" name="session_override_pass" id="session_override_pass" />
													</span>
												</label>
											</fieldset>
										</td>
									</tr>
									<tr>
										<th>
											<label for="sessions-error-message">
												<?php esc_html_e( 'Multiple Users Sessions Error Message', 'wp-security-audit-log' ); ?>
											</label>
										</th>
										<td>
											<fieldset>
												<?php $error_message = $this->_plugin->usermanagement->common->GetOptionByName( 'user-management-sessions-error-message', __( '<strong>ERROR</strong>: Your session was blocked with the <a href="https://en-gb.wordpress.org/plugins/wp-security-audit-log" target="_blank">WP Security Audit Log plugin</a> because there is already another user logged in with the same username. Please contact the site administrator for more information.', 'wp-security-audit-log' ) ); ?>
												<label for="sessions-error-message">
													<textarea rows="5" cols="50" name="sessions_error_message" id="sessions-error-message" style="margin-top: 2px;"><?php echo wp_kses( $error_message, $this->_plugin->allowed_html_tags ); ?></textarea>
												</label>
												<br/>
												<span class="description">
													<?php esc_html_e( 'This is the error message that is shown when a user tries to login with a username that already has a session. You can change this message by editing the text in the above placeholder.', 'wp-security-audit-log' ); ?>
												</span>
											</fieldset>
										</td>
									</tr>
									<tr>
										<th><label for="AlertMultiSessions"><?php esc_html_e( 'Alert the following email addresses when there are multiple sessions with the same user', 'wp-security-audit-log' ); ?></label></th>
										<td>
											<fieldset>
												<?php $multiSessions = $this->_plugin->usermanagement->common->GetMultiSessions(); ?>
												<label for="AlertMultiSessions">
													<input type="checkbox" name="AlertMultiSessions" value="1" id="AlertMultiSessions" <?php echo ! empty( $multiSessions->status ) ? ' checked="checked"' : ''; ?>> <?php esc_html_e( 'Alert the following users', 'wp-security-audit-log' ); ?>
												</label>
												<br/>
												<?php $multiSessionsEmails = ! empty( $multiSessions->emails ) ? $multiSessions->emails : ''; ?>
												<input type="text" class="emailsAlert" id="AlertMultiSessionsEmails" name="AlertMultiSessionsEmails" value="<?php echo $multiSessionsEmails; ?>" style="display: block; width: 250px;" placeholder="Email *">
												<span class="description"><?php esc_html_e( 'Should you allow multiple same user sessions, and multiple people log in with the same WordPress user, an email alert is sent to the specified email addresses.', 'wp-security-audit-log' ); ?></span>
											</fieldset>
										</td>
									</tr>
									<tr>
										<th><label for="AlertBlocked"><?php esc_html_e( 'Alert the following email addresses when a user session is blocked', 'wp-security-audit-log' ); ?></label></th>
										<td>
											<fieldset>
												<?php $blocked = $this->_plugin->usermanagement->common->GetBlocked(); ?>
												<label for="AlertBlocked">
													<input type="checkbox" name="AlertBlocked" value="1" id="AlertBlocked" <?php echo ! empty( $blocked->status ) ? ' checked="checked"' : ''; ?>> <?php esc_html_e( 'Alert the following users', 'wp-security-audit-log' ); ?>
												</label>
												<br/>
												<?php $blocked_emails = ! empty( $blocked->emails ) ? $blocked->emails : ''; ?>
												<input type="text" class="emailsAlert" id="AlertBlockedEmails" name="AlertBlockedEmails" value="<?php echo esc_attr( $blocked_emails ); ?>" style="display: block; width: 250px;" placeholder="Email *">
												<span class="description"><?php esc_html_e( 'Should you deny multiple same user sessions, when a user login with same username is blocked, an email alert is sent to the specified email addresses.', 'wp-security-audit-log' ); ?></span>
											</fieldset>
										</td>
									</tr>
									<tr>
										<th><label for="auto_terminate"><?php esc_html_e( 'Automatically Destroy Idle Sessions', 'wp-security-audit-log' ); ?></label></th>
										<td>
											<fieldset>
												<?php $auto_terminate = $this->_plugin->usermanagement->common->get_auto_sessions_terminate(); ?>
												<label for="auto_terminate">
													<input type="checkbox" name="auto_terminate" value="1" id="auto_terminate" <?php echo ! empty( $auto_terminate->status ) ? ' checked="checked"' : ''; ?>>
												</label>
												<br/>
												<?php
												// Get stored number of hours.
												$auto_terminate_hours = ! empty( $auto_terminate->hours ) ? (int) $auto_terminate->hours : '';

												// Predefined hours to select from.
												$hour_options = array(
													8  => esc_html__( '8', 'wp-security-audit-log' ),
													12 => esc_html__( '12', 'wp-security-audit-log' ),
													24 => esc_html__( '24', 'wp-security-audit-log' ),
													32 => esc_html__( '32', 'wp-security-audit-log' ),
													48 => esc_html__( '48', 'wp-security-audit-log' ),
												);
												?>
												<span><?php esc_html_e( 'Destroy a session if it has been idle for more than', 'wp-security-audit-log' ); ?></span>
												<select name="auto_terminate_hours" id="auto_terminate_hours">
													<?php foreach ( $hour_options as $hour_attr => $hour_html ) : ?>
														<option value="<?php echo esc_attr( $hour_attr ); ?>" <?php echo ( $hour_attr === $auto_terminate_hours ) ? 'selected' : false; ?>>
															<?php echo esc_html( $hour_html ); ?>
															<?php esc_html_e( ' hours', 'wp-security-audit-log' ); ?>
														</option>
													<?php endforeach; ?>
												</select>
												<span><?php esc_html_e( ' hours', 'wp-security-audit-log' ); ?></span>
												<span class="description"><?php esc_html_e( 'If a session has been idle for more than the configured number of hours, it will be automatically destroyed by the plugin.', 'wp-security-audit-log' ); ?></span>
											</fieldset>
										</td>
									</tr>
								</tbody>
							</table>
							<p class="submit">
								<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
							</p>
						</form>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Terminal all sessions modal -->
		<div class="remodal" data-remodal-id="wsal-terminate-sessions">
			<button data-remodal-action="close" class="remodal-close"></button>
			<h3><?php esc_html_e( 'Terminate all logged in sessions', 'wp-security-audit-log' ); ?></h3>
			<p>
				<?php esc_html_e( 'This will terminate all user sessions which potentially could result in unsaved work. Would you like to proceed?', 'wp-security-audit-log' ); ?>
			</p>
			<br>
			<input type="hidden" id="wsal-terminate-all-sessions" value="<?php echo esc_attr( wp_create_nonce( 'wsal-terminate-all-sessions' ) ); ?>">
			<button data-remodal-action="confirm" class="remodal-confirm"><?php esc_html_e( 'YES', 'wp-security-audit-log' ); ?></button>
			<button data-remodal-action="cancel" class="remodal-cancel"><?php esc_html_e( 'NO', 'wp-security-audit-log' ); ?></button>
		</div>
		<?php
	}

	/**
	 * @param int|null $limit Maximum number of sites to return (null = no limit).
	 * @return object Object with keys: blog_id, blogname, domain
	 */
	public function get_sites( $limit = null ) {
		global $wpdb;

		$sql = 'SELECT blog_id, domain FROM ' . $wpdb->blogs;
		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . $limit;
		}
		$res = $wpdb->get_results( $sql );
		foreach ( $res as $row ) {
			$row->blogname = get_blog_option( $row->blog_id, 'blogname' );
		}
		return $res;
	}

	/**
	 * The number of sites on the network.
	 *
	 * @return int
	 */
	public function get_site_count() {
		global $wpdb;
		$sql = 'SELECT COUNT(*) FROM ' . $wpdb->blogs;
		return (int) $wpdb->get_var( $sql );
	}

	protected function is_multisite() {
		return $this->_plugin->IsMultisite();
	}

	protected function is_main_blog() {
		return get_current_blog_id() == 1;
	}

	protected function is_specific_view() {
		return isset( $_REQUEST['wsal-cbid'] ) && $_REQUEST['wsal-cbid'] != '0';
	}

	protected function get_specific_view() {
		return isset( $_REQUEST['wsal-cbid'] ) ? (int) $_REQUEST['wsal-cbid'] : 0;
	}

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
	 * Method: Destory User Session.
	 *
	 * @since 3.1
	 */
	public function ajax_destroy_user_session() {
		// Check if current user can manage options.
		if ( ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
			$response = array(
				'success' => false,
				'message' => esc_html__( 'You do not have sufficient permissions.', 'wp-security-audit-log' ),
			);
			echo wp_json_encode( $response );
			exit;
		}

		// Set filter input args.
		$filter_input_args = array(
			'action' => FILTER_SANITIZE_STRING,
			'user_id' => FILTER_VALIDATE_INT,
			'token' => FILTER_SANITIZE_STRING,
			'nonce' => FILTER_SANITIZE_STRING,
		);

		// Get $_POST array & Verify nonce.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		if ( ! empty( $post_array['nonce'] )
			&& ! empty( $post_array['action'] )
			&& 'destroy_session' === $post_array['action']
			&& ! empty( $post_array['user_id'] )
			&& ! empty( $post_array['token'] ) ) {
			$user_id = absint( $post_array['user_id'] );
			if ( false === wp_verify_nonce( $post_array['nonce'], sprintf( 'destroy_session_nonce-%d', $user_id ) ) ) {
				$response = array(
					'success' => false,
					'message' => esc_html__( 'No sessions.', 'wp-security-audit-log' ),
				);
				echo wp_json_encode( $response );
				exit;
			}

			$this->_plugin->usermanagement->common->DestroyUserSession( $user_id, $post_array['token'] );
			$response = array(
				'success' => true,
				'message' => esc_html__( 'Session destroyed.', 'wp-security-audit-log' ),
			);
			echo wp_json_encode( $response );
			exit;
		}

		$response = array(
			'success' => false,
			'message' => esc_html__( 'User session data is not set.', 'wp-security-audit-log' ),
		);
		echo wp_json_encode( $response );
		exit;
	}

	/**
	 * Method: Destory all users sessions.
	 *
	 * @since 3.0
	 */
	public function ajax_sessions_destroy() {
		// Check if current user can manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			$response = array(
				'success' => false,
				'message' => esc_html__( 'User do not have sufficient permissions.', 'wp-security-audit-log' ),
			);
			echo wp_json_encode( $response );
			exit;
		}

		// Set filter input args.
		$filter_input_args = array(
			'action' => FILTER_SANITIZE_STRING,
			'nonce' => FILTER_SANITIZE_STRING,
			'offset' => FILTER_VALIDATE_INT,
		);

		// Get $_POST array & Verify nonce.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		if ( ! isset( $post_array['nonce'] ) || ! wp_verify_nonce( $post_array['nonce'], 'script_nonce' ) ) {
			$response = array(
				'success' => false,
				'message' => esc_html__( 'Nonce verification failed.', 'wp-security-audit-log' ),
			);
			echo wp_json_encode( $response );
			exit;
		}

		// Get offset.
		$offset = isset( $post_array['offset'] ) ? $post_array['offset'] : 0;

		// Get up to 50 users. IMPORTANT: Increasing the limit will result in slow query.
		$limit = 50;

		// No need to logout current user i.e. admin.
		$excluded_id = get_current_user_id();

		// WP_User_Query arguments.
		$query_args = array(
			'exclude' => array( $excluded_id ),
			'fields' => array( 'ID' ),
			'number' => $limit,
			'offset' => $offset,
		);

		// Query the users.
		$users_query = new WP_User_Query( $query_args );

		// Get array of users from the query results.
		$users_array = $users_query->get_results();

		// Check if users array is not empty and is array then continue.
		if ( ! empty( $users_array ) && is_array( $users_array ) ) {
			foreach ( $users_array as $key => $user ) {
				// Delete session tokens of the specific user in array.
				delete_user_meta( $user->ID, 'session_tokens' );
			}
			// Calculate offset.
			$response = $offset + $limit;
			echo wp_json_encode( $response );
			exit;
		}

		// Return 0 if user array is empty.
		echo wp_json_encode( 0 );
		exit;
	}

	/**
	 * Method: Destory all sessions except the
	 * current one.
	 *
	 * @since 3.1.4
	 */
	public function terminate_all_sessions() {
		// Check if current user can manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			$response = array(
				'success' => false,
				'message' => esc_html__( 'User do not have sufficient permissions.', 'wp-security-audit-log' ),
			);
			echo wp_json_encode( $response );
			exit;
		}

		// Set filter input args.
		$filter_input_args = array(
			'nonce' => FILTER_SANITIZE_STRING,
		);

		// Get $_POST array.
		$post_array = filter_input_array( INPUT_POST, $filter_input_args );

		// Get nonce and verify it.
		if ( ! isset( $post_array['nonce'] ) || ! wp_verify_nonce( $post_array['nonce'], 'wsal-terminate-all-sessions' ) ) {
			$response = array(
				'success' => false,
				'message' => esc_html__( 'Nonce verification failed.', 'wp-security-audit-log' ),
			);
			echo wp_json_encode( $response );
			exit;
		}

		// Get current user id.
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;

		// Get current sessions via transient.
		$get_fn = $this->_plugin->IsMultisite() ? 'get_site_transient' : 'get_transient'; // Check for multisite.
		$users_sessions = $get_fn( 'wsal-users_sessions' );

		// Return if transient does not exists.
		if ( false === $users_sessions ) {
			$response = array(
				'success' => false,
				'message' => esc_html__( 'No active sessions!', 'wp-security-audit-log' ),
			);
			echo wp_json_encode( $response );
			exit;
		}

		if ( ! empty( $users_sessions ) ) {
			// Go through the sessions and destroy them.
			foreach ( $users_sessions as $user_id => $sessions ) {
				// Skip current user's session.
				if ( $current_user_id !== $user_id ) {
					foreach ( $sessions as $session ) {
						// Destroy session.
						$this->_plugin->usermanagement->common->DestroyUserSession( $user_id, $session['token_hash'] );
					}
				}
			}
		}

		$response = array(
			'success' => true,
			'message' => esc_html__( 'Sessions destroyed!', 'wp-security-audit-log' ),
		);
		echo wp_json_encode( $response );
		exit;
	}
}
