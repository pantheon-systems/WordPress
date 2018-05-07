<?php
/**
 * Extension: Email Notifications
 *
 * Email notifications extension for wsal.
 *
 * @since 2.7.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Holds the option prefix
 */
define( 'WSAL_OPT_PREFIX', 'notification-' );

/**
 * Holds the maximum number of notifications a user is allowed to add
 */
define( 'WSAL_MAX_NOTIFICATIONS', 50 );

/**
 * Holds the name of the cache key if cache available
 */
define( 'WSAL_CACHE_KEY', '__NOTIF_CACHE__' );

/**
 * Debugging true|false
 */
define( 'WSAL_DEBUG_NOTIFICATIONS', false );

/**
 * Class WSAL_NP_Plugin
 *
 * @package wp-security-audit-log
 */
class WSAL_NP_Plugin {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var object
	 */
	protected $wsal = null;

	// Cache.
	private $_notifications = null;
	private $_cacheExpire = 43200; // 12h (60*60*12).

	/**
	 * Method: Constructor.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		// Function to hook at `wsal_init`.
		add_action( 'wsal_init', array( $this, 'wsal_init' ) );

		// Listen for activation event.
		// register_activation_hook( __FILE__, array( $this, 'wizard_plugin_activate' ) );
		add_action( 'admin_init', array( $this, 'wizard_plugin_redirect' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_action_links' ) );

		add_action( 'wp_login_failed', array( $this, 'counter_login_failure' ) );
		add_filter( 'template_redirect', array( $this, 'counter_event_404' ) );

		// Uninstall event.
		// wsal_freemius()->add_action( 'after_uninstall', array( $this, 'email_notifications_uninstall_cleanup' ) );.
	}

	/**
	 * Triggered when the main plugin is loaded.
	 *
	 * @param WpSecurityAuditLog $wsal - Instance of WpSecurityAuditLog.
	 * @see WpSecurityAuditLog::load()
	 */
	public function wsal_init( WpSecurityAuditLog $wsal ) {
		// Autoload the files in `classes` foler.
		$wsal->autoloader->Register( 'WSAL_NP_', dirname( __FILE__ ) . '/classes' );
		$wsal_common = new WSAL_NP_Common( $wsal );
		$wsal->wsalCommon = $wsal_common;
		$wsal->views->AddFromClass( 'WSAL_NP_Notifications' );
		$c = new WSAL_NP_Wizard( $wsal );

		if ( isset( $_REQUEST['page'] ) ) {
			$a = new WSAL_NP_AddNotification( $wsal );
			$b = new WSAL_NP_EditNotification( $wsal );
			$add_notif_pagename = $a->GetSafeViewName();
			$edit_notif_pagename = $b->GetSafeViewName();
			$wizardf_pagename = $c->GetSafeViewName();

			switch ( $_REQUEST['page'] ) {
				case $add_notif_pagename:
					$wsal->views->AddFromClass( 'WSAL_NP_AddNotification' );
					break;
				case $edit_notif_pagename:
					$wsal->views->AddFromClass( 'WSAL_NP_EditNotification' );
					break;
				case $wizardf_pagename:
					$wsal->views->AddFromClass( 'WSAL_NP_Wizard' );
					break;
			}
		}

		$wsal->alerts->AddFromClass( 'WSAL_NP_Notifier' );
		$this->wsal = $wsal;
	}

	public function wizard_plugin_activate() {
		add_option( 'wizard_plugin_do_activation_redirect', true );
	}

	public function wizard_plugin_redirect() {
		if ( get_option( 'wizard_plugin_do_activation_redirect', false ) ) {
			delete_option( 'wizard_plugin_do_activation_redirect' );
			$wsal = WpSecurityAuditLog::GetInstance();
			$wsal->autoloader->Register( 'WSAL_NP_', dirname( __FILE__ ) . '/classes' );
			$wsal->views->AddFromClass( 'WSAL_NP_Wizard' );
			$wizard = new WSAL_NP_Wizard( $wsal );
			// wp_enqueue_script('jquery.modal-js', $pluginPath.'/js/jquery.modal/jquery.modal.js', array('jquery'));
			// wp_enqueue_style('jquery.modal-css', $pluginPath.'/js/jquery.modal/jquery.modal.css');
			?>
			<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet" />
			<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
			<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
			<style type="text/css">
			.modal-content {
				border-radius: 0;
			}
			.modal-footer {
				padding-top: 0;
				border-top: none;
			}
			.btn {
				border-radius: 0px;
			}
			</style>
			<div class="modal fade" id="msg_modal">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Email Notifications for WP Security Audit Log</h4>
						</div>
						<div class="modal-body">
							<p>Do you want to launch the wizard to configure your first email notification alerts? If you select no you can launch it later or configure the email alerts manually.</p>
						</div>
						<div class="modal-footer">
							<a href="<?php echo esc_attr( $wizard->GetUrl() . '&first-time=1' ); ?>" class="btn btn-primary">Launch Wizard</a>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">No thank you</button>
						</div>
					</div>
				</div>
			</div>
			<script type="text/javascript">
				jQuery('#msg_modal').modal('show');
			</script>
			<?php
			// exit(wp_redirect($wizard->GetUrl() . '&first-time=1'));
		}
	}

	/**
	 * Add action links in the plugins page.
	 *
	 * @param array $links - Existing links.
	 * @return array all the links after merging the new
	 */
	public function add_action_links( $links ) {
		$new_links = array(
			'<a href="' . admin_url( 'admin.php?page=wsal-np-notifications' ) . '">Configure Email Alerts</a>',
		);
		return array_merge( $new_links, $links );
	}

	/**
	 * Triggered by Failed Login Hook.
	 *
	 * Increase the limit changes the max value when you call: $Notifications->CreateSelect().
	 *
	 * @param string $username - Username.
	 */
	public function counter_login_failure( $username ) {
		$alert_code = 1003;
		$username = array_key_exists( 'log', $_POST ) ? $_POST['log'] : $username;
		$user = get_user_by( 'login', $username );
		if ( $user ) {
			$alert_code = 1002;
		}
		if ( ! $this->wsal->alerts->IsEnabled( $alert_code ) ) {
			return;
		}
		$site_id = (function_exists( 'get_current_blog_id' ) ? get_current_blog_id() : 0);
		$ip = $this->wsal->settings->GetMainClientIP();

		$this->_notifications = wp_cache_get( WSAL_CACHE_KEY );

		if ( false === $this->_notifications ) {
			$this->_notifications = $this->wsal->wsalCommon->GetNotifications();
			wp_cache_set( WSAL_CACHE_KEY, $this->_notifications, null, $this->_cacheExpire );
		}
		if ( ! empty( $this->_notifications ) ) {
			foreach ( $this->_notifications as $k => $v ) {
				$not_info = unserialize( $v->option_value );
				$enabled = intval( $not_info->status );
				if ( 0 == $enabled ) {
					continue;
				}
				if ( ! empty( $not_info->failUser ) && $user ) {
					if ( $this->wsal->wsalCommon->IsLoginFailureLimit( $not_info->failUser, $ip, $site_id, $user, true ) ) {
						break;
					}
					$this->wsal->wsalCommon->CounterLoginFailure( $ip, $site_id, $user );

					if ( $this->wsal->wsalCommon->IsLoginFailureLimit( $not_info->failUser, $ip, $site_id, $user ) ) {
						$this->sendSuspiciousActivity( $not_info, $ip, $site_id, $alert_code, $username );
					}
				}
				if ( ! empty( $not_info->failNotUser ) && ! $user ) {
					if ( $this->wsal->wsalCommon->IsLoginFailureLimit( $not_info->failNotUser, $ip, $site_id, null, true ) ) {
						break;
					}
					$this->wsal->wsalCommon->CounterLoginFailure( $ip, $site_id, $user );

					if ( $this->wsal->wsalCommon->IsLoginFailureLimit( $not_info->failNotUser, $ip, $site_id, null ) ) {
						$this->sendSuspiciousActivity( $not_info, $ip, $site_id, $alert_code, $username );
					}
				}
			}
		}
	}

	/**
	 * Triggered by 404 Redirect Hook.
	 *
	 * To increase the limit changes the max value when you call: $Notifications->CreateSelect()
	 */
	public function counter_event_404() {
		global $wp_query;
		if ( ! $wp_query->is_404 ) {
			return;
		}

		$alert_code = 6007; // 404 alert code for logged in user.

		// Check if user is logged in.
		if ( ! is_user_logged_in() ) {
			$username = 'Website Visitor';
			$alert_code = 6023;
		} else {
			$username = wp_get_current_user()->user_login;
		}

		// Check if alert is enabled.
		if ( ! $this->wsal->alerts->IsEnabled( $alert_code ) ) {
			return;
		}

		// Get site ID and IP of the visitor.
		$site_id = (function_exists( 'get_current_blog_id' ) ? get_current_blog_id() : 0);
		$ip = $this->wsal->settings->GetMainClientIP();

		$this->_notifications = wp_cache_get( WSAL_CACHE_KEY );

		if ( false === $this->_notifications ) {
			$this->_notifications = $this->wsal->wsalCommon->GetNotifications();
			wp_cache_set( WSAL_CACHE_KEY, $this->_notifications, null, $this->_cacheExpire );
		}
		if ( ! empty( $this->_notifications ) ) {
			foreach ( $this->_notifications as $k => $v ) {
				$not_info = unserialize( $v->option_value );
				$enabled = intval( $not_info->status );
				if ( 0 == $enabled ) {
					continue;
				}
				if ( ! empty( $not_info->error404 ) && 6007 === $alert_code ) {
					if ( $this->wsal->wsalCommon->Is404Limit( $not_info->error404, $site_id, $username, $ip, true, $alert_code ) ) {
						break;
					}
					$this->wsal->wsalCommon->Counter404( $site_id, $username, $ip, $alert_code );

					if ( $this->wsal->wsalCommon->Is404Limit( $not_info->error404, $site_id, $username, $ip, false, $alert_code ) ) {
						$this->sendSuspiciousActivity( $not_info, $ip, $site_id, $alert_code, $username );
					}
				} elseif ( ! empty( $not_info->error404_visitor ) && 6023 === $alert_code ) {
					if ( $this->wsal->wsalCommon->Is404Limit( $not_info->error404_visitor, $site_id, $username, $ip, true, $alert_code ) ) {
						break;
					}
					$this->wsal->wsalCommon->Counter404( $site_id, $username, $ip, $alert_code );

					if ( $this->wsal->wsalCommon->Is404Limit( $not_info->error404_visitor, $site_id, $username, $ip, false, $alert_code ) ) {
						$this->sendSuspiciousActivity( $not_info, $ip, $site_id, $alert_code, $username );
					}
				}
			}
		}
	}

	/**
	 * Send Suspicious Activity email.
	 * Load the template and replace the tags with tha arguments passed.
	 *
	 * @param object $not_info - Info object.
	 * @param string $ip - IP Address.
	 * @param int    $site_id - Site ID.
	 * @param int    $alert_code - Alert code.
	 * @param string $username - Username.
	 */
	private function sendSuspiciousActivity( $not_info, $ip, $site_id, $alert_code, $username ) {
		$title = $not_info->title;
		$email_address = $not_info->email;

		$alert = $this->wsal->alerts->GetAlert( $alert_code );
		$user = get_user_by( 'login', $username );
		$user_role = '';
		if ( ! empty( $user ) ) {
			$user_info = get_userdata( $user->ID );
			$user_role = implode( ', ', $user_info->roles );
		}
		$date = $this->wsal->wsalCommon->GetEmailDatetime();
		$blogname = $this->wsal->wsalCommon->GetBlogname();

		$count = 1;
		$search = array( '%Attempts%', '%Msg%', '%LinkFile%', '%LogFileLink%', '%LogFileText%' );
		if ( ! empty( $not_info->failUser ) ) {
			$replace = array( $not_info->failUser, '', '', '', '' );
		} elseif ( ! empty( $not_info->failNotUser ) ) {
			$replace = array( $not_info->failNotUser, '', '', '', '' );
		} elseif ( ! empty( $not_info->error404 ) ) {
			$replace = array( $not_info->error404, 'times', '', '', '' );
		} elseif ( ! empty( $not_info->error404_visitor ) ) {
			$replace = array( $not_info->error404_visitor, 'times', '', '', '' );
		}
		$message = str_replace( $search, $replace, $alert->mesg );

		$search = array( '{title}', '{source_ip}', '{alert_id}', '{date_time}', '{message}', '{username}', '{user_role}', '{site}' );
		$replace = array( $title, $ip, $alert_code, $date, $message, $username, $user_role, $blogname );

		$template = $this->wsal->wsalCommon->GetEmailTemplate( 'built-in' );
		$subject = str_replace( $search, $replace, $template['subject'] );
		$content = str_replace( $search, $replace, stripslashes( $template['body'] ) );

		$result = $this->wsal->wsalCommon->SendNotificationEmail( $email_address, $subject, $content, $alert_code );
	}

	/**
	 * Method: Uninstall routine.
	 *
	 * @since 2.7.0
	 */
	public function email_notifications_uninstall_cleanup() {
		$this->wsal->DeleteByPrefix( WSAL_OPT_PREFIX );
	}
}
