<?php
/**
 * Class: Notifications Page
 *
 * View class for notification settings page.
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
 * Class WSAL_NP_Notifications for Notifications Page.
 *
 * @package wp-security-audit-log
 */
class WSAL_NP_Notifications extends WSAL_AbstractView {

	// @internal
	const WPSALP_NOTIF_ERROR = 1;

	private $_searchView = false;

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
	 * Method: Constructor.
	 *
	 * @param WpSecurityAuditLog $plugin - Instance of WpSecurityAuditLog.
	 * @since 2.7.0
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		// Call to parent class.
		parent::__construct( $plugin );

		// Set the paths.
		$this->_base_dir = WSAL_BASE_DIR . 'extensions/email-notifications';
		$this->_base_url = WSAL_BASE_URL . 'extensions/email-notifications';
	}

	/**
	 * Method: Get View Title.
	 */
	public function GetTitle() {
		return __( 'Email Notifications', 'wp-security-audit-log' );
	}

	/**
	 * Method: `Add New` Notifications Button.
	 */
	private function _addTitleButton() {
		$class = $this->_plugin->views->FindByClassName( 'WSAL_NP_AddNotification' );
		if ( false === $class ) {
			$class = new WSAL_NP_AddNotification( $this->_plugin );
		}

		$wizard = $this->_plugin->views->FindByClassName( 'WSAL_NP_Wizard' );
		if ( false === $wizard ) {
			$wizard = new WSAL_NP_Wizard( $this->_plugin );
		}
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$('.wsal-tab h2:first').append('<a href="<?php echo esc_attr( $wizard->GetUrl() . '#tab-second' ); ?>" class="add-new-h2"><?php esc_html_e( 'Launch Wizard', 'wpsal-notifications' ); ?></a> &nbsp; <a href="<?php echo esc_attr( $class->GetUrl() ); ?>" class="add-new-h2"><?php esc_html_e( 'Add New', 'wpsal-notifications' ); ?></a>');
			});
		</script>
		<?php
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
		return __( 'Email Notifications', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Weight.
	 */
	public function GetWeight() {
		return 9;
	}

	/**
	 * Method: Get View Header.
	 */
	public function Header() {
		wp_enqueue_style( 'wsal-notif-css', $this->_base_url . '/css/styles.css' );
		echo "<script type='text/javascript'> var dateFormat = '" . esc_html( $this->_plugin->wsalCommon->DateValidFormat() ) . "'; </script>";
		wp_enqueue_script( 'wsal-notif-utils-js', $this->_base_url . '/js/wsal-notification-utils.js', array( 'jquery' ) );
	}

	/**
	 * Method: Get View Footer.
	 */
	public function Footer() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				// tab handling code
				jQuery('#wsal-tabs>a').click(function(){
					jQuery('#wsal-tabs>a').removeClass('nav-tab-active');
					jQuery('table.wsal-tab').hide();
					jQuery(jQuery(this).addClass('nav-tab-active').attr('href')).show();
				});
				// show relevant tab
				var hashlink = jQuery('#wsal-tabs>a[href="' + location.hash + '"]');
				if (hashlink.length) {
					hashlink.click();
				} else {
					jQuery('#wsal-tabs>a:first').click();
				}

				jQuery('#wsal-trigger-form input[type=checkbox]').unbind('change').change(function() {
					current = this.name+'-email';
					count = this.name+'-count';
					if (jQuery(this).is(':checked')) {
						jQuery('#'+current).prop('required', true);
						if (jQuery('#'+count).length) {
							jQuery('#'+count).prop('required', true);
						}
					} else {
						jQuery('#'+current).removeProp('required');
						if (jQuery('#'+count).length) {
							jQuery('#'+count).removeProp('required');
						}
					}
				});
			});
		</script>
		<?php
	}

	/**
	 * Inspect the REQUEST and detect the requested view
	 *
	 * @author Ashar Irfan
	 * @since  1.0.0
	 */
	private function PrepareView() {
		// Default view.
		if ( ! isset( $_REQUEST['action'] ) ) {
			return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
		}

		// From here on, all requests must be signed.
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'nonce-notifications-view' ) ) {
			return self::WPSALP_NOTIF_ERROR;
		}

		$valid_actions = array(
			'disable_notification',
			'enable_notification',
			'delete_notification',
			'view_disabled',
			'search',
			'bulk',
		);
		$action = sanitize_text_field( $_REQUEST['action'] );
		$id = isset( $_REQUEST['id'] ) ? sanitize_text_field( $_REQUEST['id'] ) : null; // The notification's ID.

		if ( ! in_array( $action, $valid_actions ) ) {
			return self::WPSALP_NOTIF_ERROR;
		}

		switch ( $action ) {
			case 'disable_notification':
			{
				if ( empty( $id ) ) {
					return self::WPSALP_NOTIF_ERROR;
				}
				if ( ! $this->_disableNotification( $id ) ) {
					return self::WPSALP_NOTIF_ERROR;
				}
				return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
			}

			case 'enable_notification':
			{
				if ( empty( $id ) ) {
					return self::WPSALP_NOTIF_ERROR;
				}
				if ( ! $this->_enableNotification( $id ) ) {
					return self::WPSALP_NOTIF_ERROR;
				}
				return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
			}

			case 'delete_notification':
			{
				if ( empty( $id ) ) {
					return self::WPSALP_NOTIF_ERROR;
				}
				if ( ! $this->_deleteNotification( $id ) ) {
					return self::WPSALP_NOTIF_ERROR;
				}
				return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
			}

			case 'view_disabled':
			{
				return $this->_plugin->wsalCommon->GetDisabledNotifications();
			}

			case 'search':
			{
				$search = isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : null; // search term
				if ( empty( $search ) ) {
					// Display the default view.
					return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
				}
				$this->_searchView = true;
				return $this->_plugin->wsalCommon->GetSearchResults( $search );
			}

			case 'bulk':
			{
				// This is coming through POST.
				$rm = strtoupper( $_SERVER['REQUEST_METHOD'] );
				if ( $rm != 'POST' ) {
					return self::WPSALP_NOTIF_ERROR;
				}

				if ( isset( $_POST['bulk'] ) || isset( $_POST['bulk2'] ) ) {
					$entries = (isset( $_POST['entries'] ) && ! empty( $_POST['entries'] )) ? $_POST['entries'] : null;
					if ( empty( $entries ) ) {
						// Noting to do; display the default view.
						return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
					}

					$b1 = strtolower( $_POST['bulk'] );
					$b2 = strtolower( $_POST['bulk2'] );

					// Invalid request.
					if ( $b1 == -1 && $b2 == -1 ) {
						return self::WPSALP_NOTIF_ERROR;
					} elseif ( $b1 == -1 ) {
						// b2 must have valid values.
						if ( $b2 == 'enable' ) {
							$this->_bulkEnable( $entries );
						} elseif ( $b2 == 'disable' ) {
							$this->_bulkDisable( $entries );
						} elseif ( $b2 == 'delete' ) {
							$this->_bulkDelete( $entries );
						}
						return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
					} elseif ( $b2 == -1 ) {
						// b1 must have valid values.
						if ( $b1 == 'enable' ) {
							$this->_bulkEnable( $entries );
						} elseif ( $b1 == 'disable' ) {
							$this->_bulkDisable( $entries );
						} elseif ( $b1 == 'delete' ) {
							$this->_bulkDelete( $entries );
						}
						return $this->_plugin->wsalCommon->GetNotBuiltInNotifications();
					}
				}
				// Invalid request.
				return self::WPSALP_NOTIF_ERROR;
			}
		}
		return self::WPSALP_NOTIF_ERROR;
	}

	private function _disableNotification( $id ) {
		$notif = $this->_plugin->wsalCommon->GetNotification( $id );
		if ( $notif === false ) {
			return false;
		}
		$opt_name = $notif->option_name;
		$optData = unserialize( $notif->option_value );
		$optData->status = 0;
		return $this->_plugin->wsalCommon->UpdateGlobalOption( $opt_name, $optData );
	}

	private function _enableNotification( $id ) {
		$notif = $this->_plugin->wsalCommon->GetNotification( $id );
		if ( $notif === false ) {
			return false;
		}
		$opt_name = $notif->option_name;
		$optData = unserialize( $notif->option_value );
		$optData->status = 1;
		return $this->_plugin->wsalCommon->UpdateGlobalOption( $opt_name, $optData );
	}

	private function _deleteNotification( $id ) {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
			return false;
		}
		$notif = $this->_plugin->wsalCommon->GetNotification( $id );
		if ( $notif === false ) {
			return false;
		}
		return $this->_plugin->wsalCommon->DeleteGlobalOption( $notif->option_name );
	}

	private function _bulkEnable( array $entries ) {
		foreach ( $entries as $i => $id ) {
			$this->_enableNotification( $id );
		}
	}

	private function _bulkDisable( array $entries ) {
		foreach ( $entries as $i => $id ) {
			$this->_disableNotification( $id );
		}
	}

	private function _bulkDelete( array $entries ) {
		foreach ( $entries as $i => $id ) {
			$this->_deleteNotification( $id );
		}
	}

	private function createBuilt_in() {
		$alert_errors = array();
		$emails = array();
		$titles = array(
			1 => 'User logs in',
			2 => 'New user is created',
			3 => 'User changed password',
			4 => 'User changed the password of another user',
			5 => "User's role has changed",
			6 => 'Published content is modified',
			7 => 'Content is published',
			8 => 'First time user logs in',
			9 => 'New plugin is installed',
			10 => 'Installed plugin is activated',
			11 => 'Plugin file is modified',
			12 => 'New theme is installed',
			13 => 'Installed theme is activated',
			14 => 'Theme file is modified',
			15 => 'Critical Alert is Generated',
			16 => 'Failed login for WordPress users',
			17 => 'Failed login for non existing WordPress users',
			18 => '404 HTTP errors are generated by a user',
			19 => '404 HTTP errors are generated by the same IP address',
		);
		$events = array(
			1 => '1000',
			2 => array( '4000', '4001', '4012' ),
			3 => '4003',
			4 => '4004',
			5 => '4002',
			6 => array( '2065', '2066', '2067' ),
			7 => array( '2001', '2005', '2030' ),
			8 => '1000',
			9 => '5000',
			10 => '5001',
			11 => '2051',
			12 => '5005',
			13 => '5006',
			14 => '2046',
			15 => '2046',
			16 => '1002',
			17 => '1003',
			18 => '6007',
			19 => '6023',
		);
		$msg = __( 'Notification could not be saved.', 'wp-security-audit-log' );
		for ( $i = 1; $i <= count( $events ); $i++ ) {
			if ( isset( $_POST[ 'built-in_' . $i ] ) && ! empty( $_POST[ 'built-in-email_' . $i ] ) ) {
				// Validate email or username.
				if ( $this->_plugin->wsalCommon->CheckEmailOrUsername( $_POST[ 'built-in-email_' . $i ] ) ) {
					$emails[ $i ] = trim( $_POST[ 'built-in-email_' . $i ] );
				} else {
					$alert_errors[ $i ] = 2;
					$msg = __( 'Email Address or Username not valid.', 'wp-security-audit-log' );
				}

				if ( empty( $alert_errors[ $i ] ) ) {
					$count = ( ! empty( $_POST[ 'built-in-count_' . $i ] ) ? $_POST[ 'built-in-count_' . $i ] : 0);
					$alert_errors[ $i ] = $this->saveBuilt_in( $i, $titles[ $i ], $emails[ $i ], $events[ $i ], true, $count );
				}
			} else {
				$alert_errors[ $i ] = $this->saveBuilt_in( $i, null, null, null );
			}
		}
		if ( in_array( 2, $alert_errors ) ) {
			?>
			<div class="error">
				<p><?php echo esc_html( $msg ); ?></p>
			</div>
			<?php
		} elseif ( in_array( 1, $alert_errors ) ) {
			?>
			<div class="updated">
				<p><?php esc_html_e( 'Notification successfully saved.', 'wp-security-audit-log' ); ?></p>
			</div>
			<?php
		}
		return $alert_errors;
	}

	public function saveBuilt_in( $id, $title, $email, $events, $built_in = true, $count = 0 ) {
		$opt_name = WSAL_OPT_PREFIX . 'built-in-' . $id;
		$data = new stdClass();
		$data->title = $title;
		$data->email = $email;
		$data->owner = get_current_user_id();
		$data->dateAdded = time();
		$data->status = 1;
		$data->viewState = array();
		$data->triggers = array();
		$data->id = $id;
		if ( $built_in ) {
			$data->built_in = 1;
		}
		if ( 'First time user logs in' == $title ) {
			$data->firstTimeLogin = 1;
		}
		if ( 'Critical Alert is Generated' == $title ) {
			$data->isCritical = 1;
		}
		if ( 'Failed login for WordPress users' == $title ) {
			$data->failUser = $count;
		}
		if ( 'Failed login for non existing WordPress users' == $title ) {
			$data->failNotUser = $count;
		}
		if ( '404 HTTP errors are generated by a user' == $title ) {
			$data->error404 = $count;
		}
		if ( '404 HTTP errors are generated by the same IP address' == $title ) {
			$data->error404_visitor = $count;
		}
		if ( isset( $events ) ) {
			if ( is_array( $events ) ) {
				foreach ( $events as $key => $event ) {
					$data->viewState[] = 'trigger_id_' . $id;
					$data->triggers[] = array(
						'select1' => (0 == $key ? 0 : 1),
						'select2' => 0,
						'select3' => 0,
						'input1' => $event,
					);
				}
			} else {
				$data->viewState[] = 'trigger_id_' . $id;
				$data->triggers[] = array(
					'select1' => 0,
					'select2' => 0,
					'select3' => 0,
					'input1' => $events,
				);
			}
		}
		if ( count( $data->triggers ) > 0 ) {
			$result = $this->_plugin->wsalCommon->AddGlobalOption( $opt_name, $data );
			if ( false === $result ) {
				return 2;
			} else {
				return 1;
			}
		} else {
			$this->_plugin->wsalCommon->DeleteGlobalOption( 'wsal-' . $opt_name );
			return 0;
		}
	}

	public function Render() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
			$network_admin = get_site_option( 'admin_email' );
			$message = esc_html__( 'To configure email notifications please contact the administrator of this multisite network on ', 'wp-security-audit-log' );
			$message .= '<a href="mailto:' . esc_attr( $network_admin ) . '" target="_blank">' . esc_html( $network_admin ) . '</a>';
			wp_die( $message );
		}
		// Update title.
		$this->_addTitleButton();

		$notifications = $this->PrepareView();

		if ( self::WPSALP_NOTIF_ERROR == $notifications ) {
			?>
			<div class="error"><p><?php esc_html_e( 'Invalid request.', 'wp-security-audit-log' ); ?></p></div>
			<?php
		}

		$all_notifications_count = count( $notifications );
		if ( isset( $_REQUEST['action'] ) ) {
			if ( 'view_disabled' == $_REQUEST['action'] ) {
				$disabled_notifications_count = $all_notifications_count;
			} else {
				$disabled_notifications_count = count( $this->_plugin->wsalCommon->GetDisabledNotifications() );
			}
		} else {
			$disabled_notifications_count = count( $this->_plugin->wsalCommon->GetDisabledNotifications() );
		}

		$nonce = wp_create_nonce( 'nonce-notifications-view' );
		$view_all_url      = $this->GetUrl();
		$disable_url       = $view_all_url . '&action=disable_notification&_wpnonce=' . $nonce;
		$enable_url        = $view_all_url . '&action=enable_notification&_wpnonce=' . $nonce;
		$delete_url        = $view_all_url . '&action=delete_notification&_wpnonce=' . $nonce;
		$view_disabled_url = $view_all_url . '&action=view_disabled&_wpnonce=' . $nonce;
		$search_url        = $view_all_url . '&action=search&_wpnonce=' . $nonce;
		$bulk_action_url   = $view_all_url . '&action=bulk&_wpnonce=' . $nonce;
		$edit_notif_class  = $this->_plugin->views->FindByClassName( 'WSAL_NP_EditNotification' );
		if ( false === $edit_notif_class ) {
			$edit_notif_class = new WSAL_NP_EditNotification( $this->_plugin );
		}
		$edit_url = $edit_notif_class->GetUrl() . '&action=wsal_edit_notification&_wpnonce=' . wp_create_nonce( 'nonce-edit-notification' );
		// Save the Built-in Notifications.
		if ( isset( $_POST['wsal-submit'] ) ) {
			$alert_errors = $this->createBuilt_in();
		}
		// Save the Email Templates.
		if ( isset( $_POST['wsal-template'] ) ) {
			$this->saveTemplate();
		}
		$alert_built_in = $this->_plugin->wsalCommon->GetBuiltIn();
		?>
		<h2 id="wsal-tabs" class="nav-tab-wrapper">
			<a href="#tab-builder" class="nav-tab"><?php esc_html_e( 'Email Notifications Trigger Builder', 'wp-security-audit-log' ); ?></a>
			<a href="#tab-built-in" class="nav-tab"><?php esc_html_e( 'Recommended Email Security Notifications', 'wp-security-audit-log' ); ?></a>
			<a href="#tab-templates" class="nav-tab"><?php esc_html_e( 'Email Templates', 'wp-security-audit-log' ); ?></a>
		</h2>
		<div class="nav-tabs">
			<table class="wsal-tab widefat" id="tab-builder">
				<tbody>
					<tr>
						<td>
							<h2></h2>
						</td>
					</tr>
					<tr>
						<td>
							<div>
								<?php
								// Check to see if there are any notifications.
								if ( ! empty( $notifications ) ) {
									?>
									<script type="text/javascript">
										jQuery(document).ready(function($){
											$('.wsal_js_no_click').on('click',function(e){e.preventDefault();return false;});
											// Disable the "view disabled" link if there are no disabled notifications.
											<?php if ( ! $disabled_notifications_count ) : ?>
												$('#wsal-view-disabled-link').on('click', function(){return false;});
											<?php endif; ?>
										});
									</script>
									<div class="wrap">
										<ul class="subsubsub" id="wsal-top-notif-menu">
											<li class="all"><a class="current" href="<?php echo esc_url( $view_all_url ); ?>"><?php esc_html_e( 'All', 'wp-security-audit-log' ); ?> <span class="count">(<?php echo esc_html( $all_notifications_count ); ?>)</span></a> |</li>
											<li class="disabled"><a href="<?php echo esc_url( $view_disabled_url ); ?>" id="wsal-view-disabled-link"><?php esc_html_e( 'Disabled', 'wp-security-audit-log' ); ?> <span class="count">(<?php echo esc_html( $disabled_notifications_count ); ?>)</span></a></li>
										</ul>
										<form method="get" action="" onsubmit="javascript:return false;" id="notifications-filter">
											<p class="search-box">
												<label for="notification-search-input" class="screen-reader-text"><?php esc_html_e( 'Search Notifications', 'wp-security-audit-log' ); ?>:</label>
												<input type="search" value="" name="" id="notification-search-input" maxlength="125"/>
												<input type="submit" value="<?php esc_attr_e( 'Search Notifications', 'wp-security-audit-log' ); ?>" class="button" id="search-submit" name=""/>
												<script type="text/javascript">
													jQuery(document).ready(function($){
														var searchInput = $('#notification-search-input');
														$('#search-submit').on('click', function(e){
															var val = wsalSanitize(searchInput.val().trim(), true);
															if(!val.length){ e.preventDefault(); }
															else { location.href = "<?php echo $search_url; ?>&s="+val; }
															return false;
														});
													});
												</script>
											</p>
											<div class="tablenav top">
												<div class="alignleft actions bulkactions">
													<select id="bulk" name="bulk">
														<option selected="selected" value="-1"><?php esc_html_e( 'Bulk actions', 'wp-security-audit-log' ); ?></option>
														<option class="hide-if-no-js" value="enable"><?php esc_html_e( 'Enable', 'wp-security-audit-log' ); ?></option>
														<option class="hide-if-no-js" value="disable"><?php esc_html_e( 'Disable', 'wp-security-audit-log' ); ?></option>
														<option value="delete"><?php esc_html_e( 'Delete', 'wp-security-audit-log' ); ?></option>
													</select>
													<input type="submit" value="<?php esc_attr_e( 'Apply', 'wp-security-audit-log' ); ?>" class="button action" id="doaction" name=""/>
												</div>
												<br class="clear">
											</div>
											<table id="wsal-notif-table" class="wp-list-table widefat fixed plugins">
												<thead>
													<tr>
														<th class="manage-column column-cb check-column" id="cb" scope="col">
															<label for="cb-select-all-1" class="screen-reader-text"><?php esc_html_e( 'Select All', 'wp-security-audit-log' ); ?></label>
															<input type="checkbox" id="cb-select-all-1"></th>
														<th class="manage-column column-title" scope="col"><span><?php esc_html_e( 'Title', 'wp-security-audit-log' ); ?></span></th>
														<th class="manage-column column-author" scope="col"><?php esc_html_e( 'Author', 'wp-security-audit-log' ); ?></th>
														<th class="manage-column column-date" scope="col"><span><?php esc_html_e( 'Date', 'wp-security-audit-log' ); ?></span></th>
													</tr>
												</thead>
												<tfoot>
													<tr>
														<th class="manage-column column-cb check-column" scope="col">
															<label for="cb-select-all-2" class="screen-reader-text"><?php esc_html_e( 'Select All', 'wp-security-audit-log' ); ?></label>
															<input type="checkbox" id="cb-select-all-2">
														</th>
														<th class="manage-column column-title" scope="col"><span><?php esc_html_e( 'Title', 'wp-security-audit-log' ); ?></span></th>
														<th class="manage-column column-author" scope="col"><?php esc_html_e( 'Author', 'wp-security-audit-log' ); ?></th>
														<th class="manage-column column-date" scope="col"><span><?php esc_html_e( 'Date', 'wp-security-audit-log' ); ?></span></th>
													</tr>
												</tfoot>

												<tbody id="the-list">
													<?php
													$datetime_format = $this->_plugin->wsalCommon->GetDatetimeFormat();
													$date_format = $this->_plugin->wsalCommon->GetDateFormat();
													// ================================
													// SHOW NOTIFICATIONS
													// ================================
													foreach ( $notifications as $k => $entry ) :
														$entryID = $entry->id;
														$optValue = unserialize( $entry->option_value );

														$title = $optValue->title;
														$enabled = $optValue->status;
														$userID = $optValue->owner;
														$user = get_user_by( 'id', $userID );
														$userName = $user->user_nicename;
														$dateAdded = $optValue->dateAdded;
														$dateFull = date( $datetime_format, $dateAdded );
														$dateOnly = date( $date_format, $dateAdded );
														$edit_url .= '&id=' . $entryID;
														$userPageUrl = get_author_posts_url( $userID );

														?>
														<tr class="entry-<?php echo $entryID; ?> <?php echo ($enabled) ? 'active' : ''; ?>" id="entry-<?php echo $entryID; ?>">
															<th class="check-column" scope="row">
																<label for="cb-select-1" class="screen-reader-text"><?php echo __( 'Select', 'wp-security-audit-log' ) . ' ' . $title; ?></label>
																<input type="checkbox" value="<?php echo $entryID; ?>" name="entries[]" id="cb-select-1">
															</th>
															<td class="post-title page-title column-title">
																<strong><a title="<?php esc_attr_e( 'Edit this notification', 'wp-security-audit-log' ); ?>" href="<?php echo $edit_url; ?>" class="row-title"><?php echo $title; ?></a></strong>
																<div class="row-actions">
																	<span class="edit"><a title="<?php esc_attr_e( 'Edit this notification', 'wp-security-audit-log' ); ?>" href="<?php echo $edit_url; ?>"><?php esc_html_e( 'Edit', 'wp-security-audit-log' ); ?></a> |
																	<span class="view">
																		<?php
																		if ( $enabled ) :
																			echo sprintf(
																				'<a title="%s" href="%s" >%s</a>',
																				__( 'Disable this notification', 'wp-security-audit-log' ), $disable_url . '&id=' . $entryID, __( 'Disable', 'wp-security-audit-log' )
																			);
																			?>
																		<?php
																		else :
																			echo sprintf(
																				'<a title="%s" href="%s" >%s</a>',
																				__( 'Enable this notification', 'wp-security-audit-log' ), $enable_url . '&id=' . $entryID, __( 'Enable', 'wp-security-audit-log' )
																			);
																			?>
																		<?php endif; ?>
																	| </span>
																	<span class="trash"><?php echo sprintf( '<a href="%s" title="%s" class="submitdelete">%s</a>', $delete_url . '&id=' . $entryID, __( 'Delete this notification', 'wp-security-audit-log' ), __( 'Delete', 'wp-security-audit-log' ) ); ?></span>
																</div>
															</td>
															<td class="author column-author"><a href="<?php echo $userPageUrl; ?>"><?php echo $userName; ?></a></td>
															<td class="date column-date"><abbr title="<?php echo $dateFull; ?>"><?php echo $dateOnly; ?></abbr><br><?php esc_html_e( 'Published', 'wp-security-audit-log' ); ?></td>
														</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
											<div class="tablenav bottom">
												<div class="alignleft actions bulkactions">
													<select id="bulk2" name="bulk2">
														<option selected="selected" value="-1"><?php esc_html_e( 'Bulk actions', 'wp-security-audit-log' ); ?></option>
														<option class="hide-if-no-js" value="enable"><?php esc_html_e( 'Enable', 'wp-security-audit-log' ); ?></option>
														<option class="hide-if-no-js" value="disable"><?php esc_html_e( 'Disable', 'wp-security-audit-log' ); ?></option>
														<option value="delete"><?php esc_html_e( 'Delete', 'wp-security-audit-log' ); ?></option>
													</select>
													<input type="submit" value="<?php esc_attr_e( 'Apply', 'wp-security-audit-log' ); ?>" class="button action" id="doaction2" name=""/>
												</div>
												<div class="alignleft actions"></div>

												<br class="clear">
											</div>
											<script type="text/javascript">
												jQuery(document).ready(function($){
													// Register click event for bulk actions
													$('#doaction, #doaction2').on('click', function(){
														// Avoid sending both dropdowns with the same value
														var dd = $(this).prev();
														// make sure there's a valid option selected
														if(dd.val() == -1){ return false; }
														// clear the other dropdown
														else {
															var idd = dd.attr('id');
															if(idd == 'bulk'){$('#bulk2').val(-1);}
															else {$('#bulk').val(-1);}
														}
														$('#notifications-filter')
															.removeAttr('onsubmit')
															.attr('action', "<?php echo $bulk_action_url; ?>")
															.attr('method', "post")
															.submit();
														return true;
													});
												});
											</script>
										</form>
										<br class="clear">
									</div>
								<?php
								} elseif ( ! empty( $alert_built_in ) && count( $alert_built_in ) > 0 ) {
									// Do nothing.
								} else {
									// Display the search form.
									if ( $this->_searchView ) {
									?>
										<form method="get" action="" onsubmit="javascript:return false;" id="notifications-filter">
											<p class="search-box">
												<label for="notification-search-input" class="screen-reader-text"><?php esc_html_e( 'Search Notifications', 'wp-security-audit-log' ); ?>:</label>
												<input type="search" value="" name="" id="notification-search-input" maxlength="125"/>
												<input type="submit" value="<?php esc_attr_e( 'Search Notifications', 'wp-security-audit-log' ); ?>" class="button" id="search-submit" name=""/>
												<script type="text/javascript">
													jQuery(document).ready(function($){
														var searchInput = $('#notification-search-input');
														$('#search-submit').on('click', function(e){
															var val = wsalSanitize(searchInput.val().trim(), true);
															if(!val.length){ e.preventDefault(); }
															else { location.href = "<?php echo $search_url; ?>&s="+val; }
															return false;
														});
													});
												</script>
											</p>
										</form>
										<div class="no-notifications-msg" style="clear: left; display: block;"><p><?php esc_html_e( 'No notifications found to match your search.', 'wp-security-audit-log' ); ?></p></div>
									<?php
									} else {
										echo '<div class="no-notifications-msg">' . __( '<p>No notifications found. Click the <code>Add New</code> button above to create one.</p>', 'wp-security-audit-log' ) . '</div>';
									}
								} // End else.
								?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<!-- Tab Built-in Notifications-->

			<form id="wsal-trigger-form" action="<?php echo esc_url( admin_url( 'admin.php?page=wsal-np-notifications' ) ); ?>#tab-built-in" method="post">
				<?php wp_nonce_field( 'wsal-built-in-notifications' ); ?>
				<table class="form-table wsal-tab" id="tab-built-in">
					<?php
					$checked = array();
					$email = array();
					if ( ! empty( $alert_built_in ) && count( $alert_built_in ) > 0 ) {
						foreach ( $alert_built_in as $k => $v ) {
							$opt_value = unserialize( $v->option_value );
							$checked[] = $opt_value->viewState[0];
							$email[ $opt_value->id ] = $opt_value->email;
							if ( ! empty( $opt_value->failUser ) ) {
								$fail_user_count = $opt_value->failUser;
							}
							if ( ! empty( $opt_value->failNotUser ) ) {
								$fail_not_user_count = $opt_value->failNotUser;
							}
							if ( ! empty( $opt_value->error404 ) ) {
								$error404_count = $opt_value->error404;
							}
							if ( ! empty( $opt_value->error404_visitor ) ) {
								$error404_visitor_count = $opt_value->error404_visitor;
							}
						}
					}
					?>
					<tbody class="widefat">
						<tr>
							<td colspan="2" style="padding-left:20px;">
								<p>
									<span class="description"><?php esc_html_e( 'Tick the check box to enable a built-in notification. To specify multiple email addresses or usernames separate them with a comma (,).', 'wp-security-audit-log' ); ?></span>
								</p>
							</td>
						</tr>
						<tr>
							<th><label for="columns"><?php esc_html_e( 'Suspicious Activity', 'wp-security-audit-log' ); ?></label></th>
							<td>
								<fieldset>
									<label for="built-in_16" class="built-in-row">
										<input type="checkbox" name="built-in_16" id="built-in_16" style="margin-top: 2px;" <?php echo(in_array( 'trigger_id_16', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title">
											<?php esc_html_e( 'Alert me when there are more than', 'wp-security-audit-log' ); ?>
											<?php $this->create_input( 16, ! empty( $fail_user_count ) ? $fail_user_count : 10 ); ?>
											<?php esc_html_e( 'failed WordPress logins for a WordPress user (1002)', 'wp-security-audit-log' ); ?>
										</span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[16] ) && 2 == $alert_errors[16] ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo esc_attr( $class ); ?>" name="built-in-email_16" id="built-in_16-email" placeholder="Email *" value="<?php echo( ! empty( $email[16] ) ? esc_attr( $email[16] ) : null); ?>">
									</label>
									<br/>
									<label for="built-in_17" class="built-in-row">
										<input type="checkbox" name="built-in_17" id="built-in_17" style="margin-top: 2px;" <?php echo(in_array( 'trigger_id_17', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title">
											<?php esc_html_e( 'Alert me when there are more than', 'wp-security-audit-log' ); ?>
											<?php $this->create_input( 17, ! empty( $fail_not_user_count ) ? $fail_not_user_count : 10 ); ?>
											<?php esc_html_e( 'failed logins of non existing users (1003)', 'wp-security-audit-log' ); ?>
										</span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[17] ) && 2 == $alert_errors[17] ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo esc_attr( $class ); ?>" name="built-in-email_17" id="built-in_17-email" placeholder="Email *" value="<?php echo( ! empty( $email[17] ) ? esc_attr( $email[17] ) : null); ?>">
									</label>
									<br/>
									<label for="built-in_18" class="built-in-row">
										<input type="checkbox" name="built-in_18" id="built-in_18" style="margin-top: 2px;" <?php echo(in_array( 'trigger_id_18', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title">
											<?php esc_html_e( 'Alert me when a user generates more than', 'wp-security-audit-log' ); ?>
											<?php $this->create_input( 18, ! empty( $error404_count ) ? $error404_count : 10 ); ?>
											<?php esc_html_e( '404 HTTP errors (6007)', 'wp-security-audit-log' ); ?>
										</span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[18] ) && 2 == $alert_errors[18] ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo esc_attr( $class ); ?>" name="built-in-email_18" id="built-in_18-email" placeholder="Email *" value="<?php echo( ! empty( $email[18] ) ? esc_attr( $email[18] ) : null); ?>">
									</label>
									<br/>
									<label for="built-in_19" class="built-in-row">
										<input type="checkbox" name="built-in_19" id="built-in_19" style="margin-top: 2px;" <?php echo(in_array( 'trigger_id_19', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title">
											<?php esc_html_e( 'Alert me when more than', 'wp-security-audit-log' ); ?>
											<?php $this->create_input( 19, ! empty( $error404_visitor_count ) ? $error404_visitor_count : 10 ); ?>
											<?php esc_html_e( '404 HTTP errors are generated by the same IP address (6023)', 'wp-security-audit-log' ); ?>
										</span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[19] ) && 2 == $alert_errors[19] ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo esc_attr( $class ); ?>" name="built-in-email_19" id="built-in_19-email" placeholder="Email *" value="<?php echo( ! empty( $email[19] ) ? esc_attr( $email[19] ) : null); ?>">
									</label>
									<br/>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th><label for="columns"><?php esc_html_e( 'Instant User Changes and Actions', 'wp-security-audit-log' ); ?></label></th>
							<td>
								<fieldset>
									<label for="built-in_1" class="built-in-row">
										<input type="checkbox" name="built-in_1" id="built-in_1" class="built-in" <?php echo(in_array( 'trigger_id_1', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'User logs in (1000)', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[1] ) && $alert_errors[1] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_1" id="built-in_1-email" placeholder="Email *" value="<?php echo( ! empty( $email[1] ) ? $email[1] : null); ?>">
									</label>
									<br/>
									<label for="built-in_2" class="built-in-row">
										<input type="checkbox" name="built-in_2" id="built-in_2" class="built-in" <?php echo(in_array( 'trigger_id_2', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'New user is created (alerts 4000, 4001, 4012)', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[2] ) && $alert_errors[2] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_2" id="built-in_2-email" placeholder="Email *" value="<?php echo( ! empty( $email[2] ) ? $email[2] : null); ?>">
									</label>
									<br/>
									<label for="built-in_3" class="built-in-row">
										<input type="checkbox" name="built-in_3" id="built-in_3" class="built-in" <?php echo(in_array( 'trigger_id_3', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'User changed password (4003)', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[3] ) && $alert_errors[3] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_3" id="built-in_3-email" placeholder="Email *" value="<?php echo( ! empty( $email[3] ) ? $email[3] : null); ?>">
									</label>
									<br/>
									<label for="built-in_4" class="built-in-row">
										<input type="checkbox" name="built-in_4" id="built-in_4" class="built-in" <?php echo(in_array( 'trigger_id_4', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'User changed the password of another user (4004)', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[4] ) && $alert_errors[4] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_4" id="built-in_4-email" placeholder="Email *" value="<?php echo( ! empty( $email[4] ) ? $email[4] : null); ?>">
									</label>
									<br/>
									<label for="built-in_5" class="built-in-row">
										<input type="checkbox" name="built-in_5" id="built-in_5" class="built-in" <?php echo(in_array( 'trigger_id_5', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( "User's role has changed (4002)", 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[5] ) && $alert_errors[5] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_5" id="built-in_5-email" placeholder="Email *" value="<?php echo( ! empty( $email[5] ) ? $email[5] : null); ?>">
									</label>
									<br/>
									<label for="built-in_6" class="built-in-row">
										<input type="checkbox" name="built-in_6" id="built-in_6" class="built-in" <?php echo(in_array( 'trigger_id_6', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'Published content is modified (alerts 2065, 2066, 2067)', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[6] ) && $alert_errors[6] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_6" id="built-in_6-email" placeholder="Email *" value="<?php echo( ! empty( $email[6] ) ? $email[6] : null); ?>">
									</label>
									<br/>
									<label for="built-in_7" class="built-in-row">
										<input type="checkbox" name="built-in_7" id="built-in_7" class="built-in" <?php echo(in_array( 'trigger_id_7', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'Content is published (alerts 2001, 2005, 2030)', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[7] ) && $alert_errors[7] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_7" id="built-in_7-email" placeholder="Email *" value="<?php echo( ! empty( $email[7] ) ? $email[7] : null); ?>">
									</label>
									<br/>
									<label for="built-in_8" class="built-in-row">
										<input type="checkbox" name="built-in_8" id="built-in_8" class="built-in" <?php echo(in_array( 'trigger_id_8', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'First time user logs in', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[8] ) && $alert_errors[8] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_8" id="built-in_8-email" placeholder="Email *" value="<?php echo( ! empty( $email[8] ) ? $email[8] : null); ?>">
									</label>
									<br/>
									<span class="description"><?php esc_html_e( 'When you enable this option you will receive an email notification for the first time each of the existing users login.', 'wp-security-audit-log' ); ?></span>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th><label for="columns"><?php esc_html_e( 'Plugin Changes Notifications', 'wp-security-audit-log' ); ?></label></th>
							<td>
								<fieldset>
									<label for="built-in_9" class="built-in-row">
										<input type="checkbox" name="built-in_9" id="built-in_9" style="margin-top: 2px;" <?php echo(in_array( 'trigger_id_9', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'New plugin is installed (5000)', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[9] ) && $alert_errors[9] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_9" id="built-in_9-email" placeholder="Email *" value="<?php echo( ! empty( $email[9] ) ? $email[9] : null); ?>">
									</label>
									<br/>
									<label for="built-in_10" class="built-in-row">
										<input type="checkbox" name="built-in_10" id="built-in_10" style="margin-top: 2px;" <?php echo(in_array( 'trigger_id_10', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'Installed plugin is activated (5001)', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[10] ) && $alert_errors[10] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_10" id="built-in_10-email" placeholder="Email *" value="<?php echo( ! empty( $email[10] ) ? $email[10] : null); ?>">
									</label>
									<br/>
									<label for="built-in_11" class="built-in-row">
										<input type="checkbox" name="built-in_11" id="built-in_11" style="margin-top: 2px;" <?php echo(in_array( 'trigger_id_11', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'Plugin file is modified (2051)', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[11] ) && $alert_errors[11] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_11" id="built-in_11-email" placeholder="Email *" value="<?php echo( ! empty( $email[11] ) ? $email[11] : null); ?>">
									</label>
									<br/>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th><label for="columns"><?php esc_html_e( 'Themes Changes Notifications', 'wp-security-audit-log' ); ?></label></th>
							<td>
								<fieldset>
									<label for="built-in_12" class="built-in-row">
										<input type="checkbox" name="built-in_12" id="built-in_12" style="margin-top: 2px;" <?php echo(in_array( 'trigger_id_12', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'New theme is installed (5005)', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[12] ) && $alert_errors[12] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_12" id="built-in_12-email" placeholder="Email *" value="<?php echo( ! empty( $email[12] ) ? $email[12] : null); ?>">
									</label>
									<br/>
									<label for="built-in_13" class="built-in-row">
										<input type="checkbox" name="built-in_13" id="built-in_13" style="margin-top: 2px;" <?php echo(in_array( 'trigger_id_13', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'Installed theme is activated (5006)', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[13] ) && $alert_errors[13] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_13" id="built-in_13-email" placeholder="Email *" value="<?php echo( ! empty( $email[13] ) ? $email[13] : null); ?>">
									</label>
									<br/>
									<label for="built-in_14" class="built-in-row">
										<input type="checkbox" name="built-in_14" id="built-in_14" style="margin-top: 2px;" <?php echo(in_array( 'trigger_id_14', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'Theme file is modified (2046)', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[14] ) && $alert_errors[14] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_14" id="built-in_14-email" placeholder="Email *" value="<?php echo( ! empty( $email[14] ) ? $email[14] : null); ?>">
									</label>
									<br/>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th><label for="columns"><?php esc_html_e( 'Generic Notifications', 'wp-security-audit-log' ); ?></label></th>
							<td>
								<fieldset>
									<label for="built-in_15" class="built-in-row">
										<input type="checkbox" name="built-in_15" id="built-in_15" style="margin-top: 2px;" <?php echo(in_array( 'trigger_id_15', $checked ) ? 'checked' : ''); ?>>
										<span class="built-in-title"><?php esc_html_e( 'Critical Alert is Generated', 'wp-security-audit-log' ); ?></span>
										<?php
										$class = '';
										if ( ! empty( $alert_errors[15] ) && $alert_errors[15] == 2 ) {
											$class = ' invalid';
										}
										?>
										<input type="text" class="built-in-email<?php echo $class; ?>" name="built-in-email_15" id="built-in_15-email" placeholder="Email *" value="<?php echo( ! empty( $email[15] ) ? $email[15] : null); ?>">
									</label>
									<br/>
								</fieldset>
							</td>
						</tr>
					</tbody>
					<tbody>
						<tr>
							<td colspan="2" style="padding:10px 0px;">
								<div id="wsal-section-email">
									<p>
										<input type="submit" id="wsal-submit" name="wsal-submit" value="Save Notification" class="button-primary">
									</p>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</form>

			<!-- Tab Email Templates -->
			<table class="form-table wsal-tab" id="tab-templates">
				<tbody class="widefat">
					<tr>
						<td colspan="2" style="padding-left:20px;">
							<p>
								<span class="description"><?php esc_html_e( 'From here you can modify the notification email template.', 'wp-security-audit-log' ); ?></span>
							</p>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="padding-left:20px;">
							<ul class="subsubsub">
								<li><a class="<?php echo (empty( $_GET['section'] ) || $_GET['section'] == 'builder') ? 'current' : null; ?>" href="<?php echo admin_url( 'admin.php?page=wsal-np-notifications' ); ?>&amp;section=builder#tab-templates">Default Email Template for User Built Notifications</a> | </li>
								<li><a class="<?php echo (isset( $_GET['section'] ) && $_GET['section'] == 'built-in') ? 'current' : null; ?>" href="<?php echo admin_url( 'admin.php?page=wsal-np-notifications' ); ?>&amp;section=built-in#tab-templates">Default Template for Built-in Email Alerts</a></li>
							</ul>
						</td>
					</tr>
				</tbody>
				<?php
				$data = array();
				if ( empty( $_GET['section'] ) || $_GET['section'] == 'builder' ) {
					$type = ( ! empty( $_GET['section'] ) ? $_GET['section'] : 'builder');
					$data = $this->_plugin->wsalCommon->GetEmailTemplate( $type );
					$this->formTemplate( $type, $data );
				} else {
					$data = $this->_plugin->wsalCommon->GetEmailTemplate( $_GET['section'] );
					$this->formTemplate( $_GET['section'], $data );
				}
				?>
			</table>
		</div>
		<?php
	}

	private function formTemplate( $type, $data = null ) {
		?>
		<form action="<?php echo admin_url( 'admin.php?page=wsal-np-notifications' ); ?>&amp;section=<?php echo $type; ?>#tab-templates" method="post">
			<tbody class="widefat" id="email-template">
				<?php if ( $type == 'builder' ) { ?>
					<input type="hidden" name="email_template" value="builder" />
					<tr>
						<td colspan="2" style="padding-left:20px;">
							<h3>Email Template for User Built Notifications</h3>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="padding-left:20px;">
							<span class="description"><?php esc_html_e( 'This is the default template. You can override this default template with notification specific template which you can modify when using the Trigger Builder.', 'wp-security-audit-log' ); ?></span>
						</td>
					</tr>
				<?php } else { ?>
					<input type="hidden" name="email_template" value="built-in" />
					<tr>
						<td colspan="2" style="padding-left:20px;">
							<h3>Template for Built-in Email Alerts</h3>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<th><label for="columns"><?php esc_html_e( 'Subject ', 'wp-security-audit-log' ); ?></label></th>
					<td>
						<fieldset>
							<input class="field" type="text" name="subject" placeholder="Subject *" value="<?php echo( ! empty( $data['subject'] ) ? $data['subject'] : null); ?>">
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><label for="columns"><?php esc_html_e( 'Body ', 'wp-security-audit-log' ); ?></label></th>
					<td>
						<fieldset>
							<?php
							$content = ( ! empty( $data['body'] ) ? stripslashes( $data['body'] ) : '');
							$editor_id = 'body';
							$settings = array(
								'media_buttons' => false,
								'editor_height' => 400,
							);

							wp_editor( $content, $editor_id, $settings );
							?>
						</fieldset>
						<br>
						<label for="body" class="tags">
							HTML is accepted. Available template tags:<br>
							{title} - Notification title<br>
							{source_ip} - Client IP address<br>
							{alert_id} - The alert code<br>
							{date_time} - Alert generated on Date and time<br>
							{message} - The alert message<br>
							{username} - User login name<br>
							{user_role} - Role(s) of the user<br>
							{site} - Website name<br>
						</label>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td style="padding:10px 0px;">
						<input type="submit" name="wsal-template" value="Save Template" class="button-primary">
					</td>
				</tr>
			</tbody>
		</form>
		<?php
	}

	public function saveTemplate() {
		if ( isset( $_POST['email_template'] ) ) {
			$opt_name = 'email-template-' . $_POST['email_template'];

			if ( ! empty( $_POST['subject'] ) && ! empty( $_POST['body'] ) ) {
				$data = new stdClass();
				$data->subject = trim( $_POST['subject'] );
				$data->body = $_POST['body'];
				$data->date_added = time();
				$result = $this->_plugin->wsalCommon->AddGlobalOption( $opt_name, $data );
				if ( $result === false ) {
					?>
					<div class="error"><p><?php esc_html_e( 'Template could not be saved.', 'wp-security-audit-log' ); ?></p></div>
					<?php
				} else {
					?>
					<div class="updated"><p><?php esc_html_e( 'Template successfully saved.', 'wp-security-audit-log' ); ?></p></div>
					<?php
				}
			} else {
				$this->_plugin->wsalCommon->DeleteGlobalOption( 'wsal-' . $opt_name );
			}
		}
	}

	public function CreateSelect( $id, $max, $selectedNum ) {
		?>
		<select name="built-in-count_<?php echo $id; ?>" id="built-in_<?php echo $id; ?>-count" >
			<?php
			for ( $num = 1; $num <= $max; $num++ ) {
				$selected = '';
				if ( ! empty( $selectedNum ) && $selectedNum == $num ) {
					$selected = ' selected';
				}
				?>
				<option value="<?php echo $num; ?>"<?php echo $selected; ?>><?php echo $num; ?></option>
				<?php
			}
			?>
		</select>
		<?php
	}

	/**
	 * Method: Generate Input tag for setting.
	 *
	 * @param int $id - Input ID.
	 * @param int $value - Input value.
	 */
	public function create_input( $id, $value ) {
		// Make sure both parameters are not empty.
		if ( ! empty( $id ) && ! empty( $value ) ) {
			?>
			<input type="text" name="built-in-count_<?php echo esc_attr( $id ); ?>"
				id="built-in_<?php echo esc_attr( $id ); ?>-count"
				value="<?php echo esc_attr( $value ); ?>" />
			<?php
		}
	}
}
