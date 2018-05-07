<?php
/**
 * View: Edit Notification
 *
 * Edit notification view class file.
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
 * Class WSAL_NP_EditNotification for Edit notification Page.
 *
 * @package wp-security-audit-log
 */
class WSAL_NP_EditNotification extends WSAL_AbstractView {

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
	 * @param WpSecurityAuditLog $wsal - Instance of WpSecurityAuditLog.
	 * @since 2.7.0
	 */
	public function __construct( WpSecurityAuditLog $wsal ) {
		// Set the paths.
		$this->_base_dir = WSAL_BASE_DIR . 'extensions/email-notifications';
		$this->_base_url = WSAL_BASE_URL . 'extensions/email-notifications';
		parent::__construct( $wsal );
	}

	/**
	 * Method: Get View Title.
	 */
	public function GetTitle() {
		return __( 'Edit Email Notification', 'wp-security-audit-log' );
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
		return __( 'Edit Notification', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Weight.
	 */
	public function GetWeight() {
		return 10;
	}

	/**
	 * Method: Get View Header.
	 */
	public function Header() {
		wp_enqueue_style( 'jquery-date-pick-css', $this->_base_url . '/js/jquery.datepick/smoothness.datepick.css' );
		wp_enqueue_style( 'jquery-time-pick-css', $this->_base_url . '/js/jquery.timeentry/jquery.timeentry.css' );
		wp_enqueue_style( 'triggers-css', $this->_base_url . '/css/styles.css', array(), filemtime( $this->_base_dir . '/css/styles.css' ) );
		wp_enqueue_script( 'markup-js', $this->_base_url . '/js/markup.js/src/markup.min.js', array( 'jquery' ) );
		echo "<script type='text/javascript'>";
		echo "var dateFormat = '" . esc_html( $this->_plugin->wsalCommon->DateValidFormat() ) . "';";
		echo "var show24Hours = '" . esc_html( $this->_plugin->wsalCommon->Show24Hours() ) . "';";
		echo '</script>';
		wp_enqueue_script( 'utils-js', $this->_base_url . '/js/wsal-notification-utils.js', array( 'jquery' ) );
		wp_enqueue_script( 'validator-js', $this->_base_url . '/js/wsal-form-validator.js', array( 'jquery' ) );
		wp_enqueue_script( 'wsal-groups-js', $this->_base_url . '/js/wsal-groups.js', array( 'jquery' ) );
		?>
		<script type="text/javascript">
			<?php
			include( realpath( dirname( __FILE__ ) . '/../' ) . '/js/wsal-translator.js' );

			// Get WP Post Types.
			$post_types = get_post_types( array( 'public' => true, '_builtin' => false ), 'names' );
			unset( $post_types['attachment'] );
			$post_types = implode( ', ', $post_types );
			$post_types = strtoupper( $post_types );

			// Get WP User Roles.
			$wp_user_roles = wp_roles()->roles;
			foreach ( $wp_user_roles as $role => $details ) {
				$user_roles[ $role ] = translate_user_role( $details['name'] );
			}
			$user_roles = implode( ', ', $user_roles );
			$user_roles = strtoupper( $user_roles );
			?>
			var WsalPostTypes = {
				post_types : "<?php echo esc_html( $post_types ); ?>"
			};
			var WsalUserRoles = {
				user_roles : "<?php echo esc_html( $user_roles ); ?>"
			};
		</script>
		<?php
		wp_enqueue_script( 'jquery-date-time-pick-plugin-js', $this->_base_url . '/js/jquery.datepick/jquery.plugin.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'jquery-datepick-js', $this->_base_url . '/js/jquery.datepick/jquery.datepick.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'jquery-timepick-js', $this->_base_url . '/js/jquery.timeentry/jquery.timeentry.min.js', array( 'jquery' ) );
	}

	/**
	 * Method: Get View Footer.
	 */
	public function Footer() {
	}

	/**
	 * Method: Get View.
	 */
	public function Render() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
		}

		if ( ! isset( $_REQUEST['action'] ) || ! isset( $_REQUEST['_wpnonce'] ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
		} else {
			$nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $nonce, 'nonce-edit-notification' ) ) {
				// This nonce is not valid.
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
			}
		}

		if ( ! isset( $_REQUEST['id'] ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
		}

		// Validate Notification.
		$nid = intval( $_REQUEST['id'] );
		$row_data = $this->_plugin->wsalCommon->GetNotification( $nid );
		if ( ! $row_data ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page. - INVALID NOTIFICATION ID', 'wp-security-audit-log' ) );
		}

		$notif_builder = new WSAL_NP_NotificationBuilder();

		// Create empty object.
		$notif_builder->create();

		$rm = strtoupper( $_SERVER['REQUEST_METHOD'] );
		if ( 'POST' == $rm && isset( $_POST['wsal_edit_notification_field'] ) ) {
			if ( isset( $_POST['wsal_form_data'] ) ) {
				$notification = $notif_builder->decodeFromString( $_POST['wsal_form_data'] );
				if ( ! empty( $_POST['subject'] ) && ! empty( $_POST['body'] ) ) {
					$notification->info->subject = trim( $_POST['subject'] );
					$notification->info->body = $_POST['body'];
				}
				$this->_plugin->wsalCommon->SaveNotification( $notif_builder, $notification, true );
			} else {
				// Not a valid request.
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
			}
		} else {
			// A GET request - Display Notification data.
			$notification = unserialize( $row_data->option_value );

			// Update fields.
			$notif_builder->update( 'info', 'title', $notification->title );
			$notif_builder->update( 'info', 'email', $notification->email );
			// Add new fields.
			$notif_builder->update( 'special', 'owner', $notification->owner );
			$notif_builder->update( 'special', 'dateAdded', $notification->dateAdded );
			$notif_builder->update( 'special', 'status', $notification->status );
			$notif_builder->update( 'special', 'optName', $row_data->option_name );
			// Update triggers.
			foreach ( $notification->triggers as $entry ) {
				$tmp = $notif_builder->createDefaultTrigger();
				$tmp->select1->selected = $entry['select1'];
				$tmp->select2->selected = $entry['select2'];
				$tmp->select3->selected = $entry['select3'];
				$tmp->select4->selected = isset( $entry['select4'] ) ? $entry['select4'] : false;
				$tmp->select5->selected = isset( $entry['select5'] ) ? $entry['select5'] : false;
				$tmp->select6->selected = isset( $entry['select6'] ) ? $entry['select6'] : false;
				$tmp->input1 = $entry['input1'];

				// Checking if selected SITE DOMAIN(9).
				if ( 9 == $entry['select2'] ) {
					global $wpdb;
					$tmp->input1 = $wpdb->get_var( $wpdb->prepare( "SELECT domain FROM $wpdb->blogs WHERE blog_id = %d", $entry['input1'] ) );
				}
				$notif_builder->addTrigger( $tmp );
			}
			// Update view state.
			$notif_builder->UpdateViewState( isset( $notification->viewState ) ? $notification->viewState : array() );
			$this->_plugin->wsalCommon->CreateJsOutputEdit( $notif_builder );
		}
		?>
		<div class="wrap">
			<div id="wsal-error-container" class="invalid" style="display:none;"><p></p></div>
			<form id="wsal-trigger-form" method="post">
				<div id="wsal-section-title"></div>
				<div class="postbox wsal-helpbox">
					<div class="inside">
						<p>
							<?php esc_html_e( 'Configure triggers that should be matched for a notification email to be sent in this section. You can add up to 5 triggers and use the AND and OR operands to join them together.', 'wpsal-notifications' ); ?>
						</p>
					</div>
				</div>
				<div id="wsal-triggers-view">
					<h3 id="wsal-sub-heading" class="f-container">
						<span class="f-left" style="margin-top: 4px;"><?php esc_html_e( 'Triggers', 'wp-security-audit-log' ); ?></span>
						<span class="f-left" style="margin-left: 36px;"><input id="wsal-button-add-trigger" type="button" class="button-secondary" value="+ <?php esc_attr_e( 'Add Trigger', 'wp-security-audit-log' ); ?>"/></span>
						<span class="f-container f-right" style="margin-top: 4px;">
							<span class="f-right">
								<input type="checkbox" class="switch" id="cb_notificationStatus"/>
								<label for="cb_notificationStatus"></label>
							</span>
							<span class="f-left f-text"><span id="NotificationStatusText"></span></span>
						</span>
					</h3>
					<script type="text/javascript">
						jQuery(document).ready(function($){
							if(wsalModel.special != undefined){
								var cb = $('#cb_notificationStatus'),
									txtNot = $('#NotificationStatusText');
								function wsalUpdateNotificationStatus(checkbox, label){
									if(checkbox.prop('checked')){
										wsalModel.special.status = 1;
										label.text(WsalTranslator.enabledText);
									}
									else {
										wsalModel.special.status = 0;
										label.text(WsalTranslator.disabledText);
									}
								}
								// force enable
								if(wsalModel.special.status){ cb.prop('checked', true);}
								wsalUpdateNotificationStatus(cb, txtNot);
								cb.on('change', function(){ wsalUpdateNotificationStatus(cb, txtNot); });
							}
						});
					</script>

					<div id="wsal-header-top-bar"></div>

					<div style="overflow:hidden; min-height: 1px; clear: both;">
						<?php /*[ Content dynamically added here ]*/ ?>
						<div id="wsal_content_js"></div>
					</div>
				</div>
				<pre id="wsal_error_triggers" style="display: none;"></pre>
				<div id="wsal-section-email"></div>

				<div id="wsal-section-radio" >
					<input id="default" type="radio" name="template" value="default" checked> <label for="default"><?php esc_html_e( 'Use default email template', 'wp-security-audit-log' ); ?></label><br>
					<?php if ( ! empty( $notification->subject ) && ! empty( $notification->body ) ) { ?>
						<input id="specific" type="radio" name="template" value="specific" checked>
					<?php } else { ?>
						<input id="specific" type="radio" name="template" value="specific">
					<?php } ?>
					<label for="specific"><?php esc_html_e( 'Use alert specific email template', 'wp-security-audit-log' ); ?></label><br>
				</div>
				<div id="wsal-section-template" class="hidden">
					<?php
					$data['subject'] = ! empty( $notification->subject ) ? $notification->subject : null;
					$data['body'] = ! empty( $notification->body ) ? $notification->body : null;
					$this->_plugin->wsalCommon->SpecificTemplate( $data );
					?>
				</div>

				<input type="hidden" id="wsal-form-data" name="wsal_form_data"/>
				<?php wp_nonce_field( 'wsal_add_notification_action', 'wsal_edit_notification_field' ); ?>
			</form>

			<script type="text/javascript" id="wsalModel">
				// This object will only be populated on POST
				var wsalModelWp = (wsalModelWp ? JSON.parse(wsalModelWp) : null);
				<?php include( $this->_base_dir . '/js/wsal-notification-model.inc.js' ); ?>

				jQuery( document ).ready( function( $ ) {
					jQuery.WSAL_EDIT_VIEW = true;
					jQuery.WSAL_MULTISITE = <?php echo $this->_plugin->IsMultisite() ? 1 : 0; ?>;
					<?php include( $this->_base_dir . '/js/wsal-notifications-view.inc.js' ); ?>
					if ( jQuery.WSAL_EDIT_VIEW ) {
						<?php if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) : ?>
							// check for errors
							formValidator.validate(); // so we can display the errors
						<?php endif; ?>
					}
				} );
			</script>

			<script type="text/template" id="scriptTitle">
				<input type="text" size="30" autocomplete="off" id="wsal-notif-title" placeholder="<?php esc_attr_e( 'Title', 'wp-security-audit-log' ); ?> *" value="{{info.title|clean}}" maxlength="125"/>
				{{if errors.titleMissing}}<label class="error" for="wsal-notif-title">{{errors.titleMissing}}</label>{{/if}}
				{{if errors.titleInvalid}}<label class="error" for="wsal-notif-title">{{errors.titleInvalid}}</label>{{/if}}
			</script>

			<script type="text/template" id="scriptEmail">
				<p>
					<span>{{info.emailLabel}}</span>
					<input type="text" id="wsal-notif-email" placeholder="<?php esc_attr_e( 'Email', 'wp-security-audit-log' ); ?> *" value="{{info.email|clean}}" />
					{{if errors.emailMissing}}<label class="error" for="wsal-notif-email">{{errors.emailMissing}}</label>{{/if}}
					{{if errors.emailInvalid}}<label class="error" for="wsal-notif-email">{{errors.emailInvalid}}</label>{{/if}}
					<input type="submit" id="wsal-submit" name="wsal-submit" value="{{buttons.saveNotifButton}}" class="button-primary"/>
				</p>
				<p class="wsal-helptext"><?php esc_html_e( 'Specify the email address or WordPress usernames who should receive the notification once the trigger is matched. To specify multiple email addresses or usernames separate them with a comma (,).', 'wp-security-audit-log' ); ?></p>
			</script>

			<script type="text/template" id="scriptTrigger">
				<div id="trigger_id_{{lastId}}" class="wsal_trigger">
					<div class="wsal-fly">
						<div class="wsal-s1">
							{{if numTriggers|ormore>2}}
							<span class="custom-dropdown custom-dropdown--default custom-dropdown--small">
								<select id="select_1_{{lastId}}" class="js_s1 custom-dropdown__select custom-dropdown__select--default">
									{{select1.data}}<option value="{{.|upcase|clean}}">{{.|upcase|clean}}</option>{{/select1.data}}
								</select>
								<input type="hidden" id="select_1_{{lastId}}_hidden" value="0"/>
							</span>
							{{else}}
								<input type="hidden" id="select_1_{{lastId}}_hidden" value="0"/>
							{{/if}}
						</div>

						<div class="wsal-s2">
							<span class="custom-dropdown custom-dropdown--default custom-dropdown--small">
								<select id="select_2_{{lastId}}" class="js_s2 custom-dropdown__select custom-dropdown__select--default">
									{{select2.data}}<option value="{{.|upcase|clean}}">{{.|upcase|clean}}</option>{{/select2.data}}
								</select>
								<input type="hidden" id="select_2_{{lastId}}_hidden" value="0"/>
							</span>
						</div>

						<div class="wsal-s3">
							<span class="custom-dropdown custom-dropdown--default custom-dropdown--small">
								<select id="select_3_{{lastId}}" class="js_s3 custom-dropdown__select custom-dropdown__select--default">
									{{select3.data}}<option value="{{.|upcase|clean}}">{{.|upcase|clean}}</option>{{/select3.data}}
								</select>
								<input type="hidden" id="select_3_{{lastId}}_hidden" value="0"/>
							</span>
						</div>

						<div class="wsal-s4">
							<span class="custom-dropdown custom-dropdown--default custom-dropdown--small custom-dropdown__status">
								<select id="select_4_{{lastId}}" class="js_s4 custom-dropdown__select custom-dropdown__select--default custom-dropdown__hide">
									{{select4.data}}<option value="{{.|upcase|clean}}" {{if #|first}}selected="selected"{{/if}}>{{.|upcase|clean}}</option>{{/select4.data}}
								</select>
								<input type="hidden" id="select_4_{{lastId}}_hidden" value="0"/>
							</span>
						</div>
						<!-- /.wsal-s4 -->

						<div class="wsal-s5">
							<span class="custom-dropdown custom-dropdown--default custom-dropdown--small custom-dropdown__post_type">
								<select id="select_5_{{lastId}}" class="js_s5 custom-dropdown__select custom-dropdown__select--default custom-dropdown__hide">
									{{select5.data}}<option value="{{.|upcase|clean}}" {{if #|first}}selected="selected"{{/if}}>{{.|upcase|clean}}</option>{{/select5.data}}
								</select>
								<input type="hidden" id="select_5_{{lastId}}_hidden" value="0"/>
							</span>
						</div>
						<!-- /.wsal-s5 -->

						<div class="wsal-s6">
							<span class="custom-dropdown custom-dropdown--default custom-dropdown--small custom-dropdown__user_role">
								<select id="select_6_{{lastId}}" class="js_s6 custom-dropdown__select custom-dropdown__select--default custom-dropdown__hide">
									{{select6.data}}<option value="{{.|upcase|clean}}" {{if #|first}}selected="selected"{{/if}}>{{.|upcase|clean}}</option>{{/select6.data}}
								</select>
								<input type="hidden" id="select_6_{{lastId}}_hidden" value="0"/>
							</span>
						</div>
						<!-- /.wsal-s6 -->
					</div>
					<div class="wsal-fly dd">
						<input id="input_1_{{lastId}}" class="wsal-trigger-input" value="{{input1|clean}}" placeholder="Required *" maxlength="50"/>
						<input type="button" id="deleteButton_{{lastId}}"
							   value="{{deleteButton}}"
							   data-removeid = "trigger_id_{{lastId}}"
							   class="button-secondary"/>
						{{if numTriggers|ormore>2}}
						<div class="wsal_options_dd">
							<div>
							<span class="custom-dropdown custom-dropdown--default custom-dropdown--small">
								<select id="wsal_options_{{lastId}}" class="custom-dropdown__select custom-dropdown__select--default wsal_dd_options"></select>
							</span>
							</div>
						</div>
						{{/if}}
					</div>
				</div>
			</script>
		</div>
		<?php
	}
}
