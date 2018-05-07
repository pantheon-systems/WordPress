<?php
/**
 * View: Add Notification
 *
 * Add notification view class file.
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
 * Class WSAL_NP_AddNotification for Add notification Page.
 *
 * @package wp-security-audit-log
 */
class WSAL_NP_AddNotification extends WSAL_AbstractView {

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
		return __( 'Add New Email Notification', 'wp-security-audit-log' );
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
		return __( 'Add notification', 'wp-security-audit-log' );
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
		wp_enqueue_style( 'wsal-jq-datepick-css', $this->_base_url . '/js/jquery.datepick/smoothness.datepick.css' );
		wp_enqueue_style( 'wsal-jq-timepick-css', $this->_base_url . '/js/jquery.timeentry/jquery.timeentry.css' );
		wp_enqueue_style( 'wsal-notif-css', $this->_base_url . '/css/styles.css', array(), filemtime( $this->_base_dir . '/css/styles.css' ) );
		wp_enqueue_script( 'wsal-markup-js', $this->_base_url . '/js/markup.js/src/markup.min.js', array( 'jquery' ) );
		echo "<script type='text/javascript'>";
		echo "var dateFormat = '" . esc_html( $this->_plugin->wsalCommon->DateValidFormat() ) . "';";
		echo "var show24Hours = '" . esc_html( $this->_plugin->wsalCommon->Show24Hours() ) . "';";
		echo '</script>';
		wp_enqueue_script( 'wsal-notif-utils-js', $this->_base_url . '/js/wsal-notification-utils.js', array( 'jquery' ) );
		wp_enqueue_script( 'wsal-validator-js', $this->_base_url . '/js/wsal-form-validator.js', array( 'jquery' ) );
		wp_enqueue_script( 'wsal-groups-js', $this->_base_url . '/js/wsal-groups.js', array( 'jquery' ) );
		?>
		<script type="text/javascript">
			<?php
			include( realpath( dirname( __FILE__ ) . '/../' ) . '/js/wsal-translator.js' );

			// Get WP Post Types.
			$post_types_args = array(
				'public' => true,
			);
			$post_types = get_post_types( $post_types_args, 'names' );
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
		wp_enqueue_script( 'wsal-jq-datetime-pick-plugin-js', $this->_base_url . '/js/jquery.datepick/jquery.plugin.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'wsal-jq-datepick-js', $this->_base_url . '/js/jquery.datepick/jquery.datepick.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'wsal-jq-timepick-js', $this->_base_url . '/js/jquery.timeentry/jquery.timeentry.min.js', array( 'jquery' ) );
	}

	/**
	 * Method: Get View Footer.
	 */
	public function Footer() {}

	/**
	 * Method: Get View.
	 */
	public function Render() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
		}

		if ( ! $this->_plugin->wsalCommon->CanAddNotification() ) {
			?>
			<div class="error">
				<p><?php esc_html_e( 'You have reached the maximum number of notifications you can add.', 'wp-security-audit-log' ); ?></p>
			</div>
			<?php
			return;
		}
		// Flag for postbacks.
		$__wsal_is_postback = false;

		$rm = strtoupper( $_SERVER['REQUEST_METHOD'] );
		if ( 'POST' == $rm && isset( $_POST['wsal_add_notification_field'] ) ) {
			// Verify nonce.
			if ( ! wp_verify_nonce( $_POST['wsal_add_notification_field'], 'wsal_add_notification_action' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
			}
			if ( isset( $_POST['wsal_form_data'] ) ) {
				$notif_builder = new WSAL_NP_NotificationBuilder();
				$notification = $notif_builder->decodeFromString( $_POST['wsal_form_data'] );
				if ( ! empty( $_POST['subject'] ) && ! empty( $_POST['body'] ) ) {
					$notification->info->subject = trim( $_POST['subject'] );
					$notification->info->body = $_POST['body'];
				}
				$this->_plugin->wsalCommon->SaveNotification( $notif_builder, $notification );
				$__wsal_is_postback = true;
			} else {
				// Not a valid request.
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
			}
		}
		// $pluginDirPath = realpath( dirname( __FILE__ ) . '/../' ) . '/';
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
					</h3>

					<div id="wsal-header-top-bar"></div>

					<div style="overflow:hidden; min-height: 1px; clear: both;">
						<?php /*[ Content dynamically added here ]*/ ?>
						<div id="wsal_content_js"></div>
					</div>
				</div>
				<pre id="wsal_error_triggers" style="display: none;"></pre>

				<div id="wsal-section-email"></div>
				<div id="wsal-section-radio">
					<input id="default" type="radio" name="template" value="default" checked> <label for="default">Use default email template</label><br>
					<input id="specific" type="radio" name="template" value="specific"> <label for="specific">Use alert specific email template</label><br>
				</div>
				<div id="wsal-section-template" class="hidden">
					<?php $this->_plugin->wsalCommon->SpecificTemplate(); ?>
				</div>

				<input type="hidden" id="wsal-form-data" name="wsal_form_data"/>
				<?php wp_nonce_field( 'wsal_add_notification_action', 'wsal_add_notification_field' ); ?>
			</form>

			<script type="text/javascript" id="wsalModel">
				// This object will only be populated on POST
				var wsalModelWp = (wsalModelWp ? JSON.parse(wsalModelWp) : null);
				<?php include( $this->_base_dir . '/js/wsal-notification-model.inc.js' ); ?>

				jQuery(document).ready(function($){
					// so we can repopulate fields in case of errors
					jQuery.WSAL_EDIT_VIEW = <?php echo $__wsal_is_postback ? 1 : 0; ?>;
					jQuery.WSAL_MULTISITE = <?php echo $this->_plugin->IsMultisite() ? 1 : 0; ?>;
					<?php include( $this->_base_dir . '/js/wsal-notifications-view.inc.js' ); ?>
				});
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
					<input type="submit" id="wsal-submit" name="wsal-submit" value="{{buttons.addNotifButton}}" class="button-primary"/>
				</p>
				<p class="wsal-helptext"><?php _e( 'Specify the email address or WordPress usernames who should receive the notification once the trigger is matched. To specify multiple email addresses or usernames separate them with a comma (,).', 'wpsal-notifications' ); ?></p>
			</script>

			<script type="text/template" id="scriptTrigger">
				<div id="trigger_id_{{lastId}}" class="wsal_trigger">
					<div class="wsal-fly">
						<div class="wsal-s1">
							{{if numTriggers|ormore>2}}
								<span class="custom-dropdown custom-dropdown--default custom-dropdown--small">
									<select id="select_1_{{lastId}}" class="js_s1 custom-dropdown__select custom-dropdown__select--default">
										{{select1.data}}<option value="{{.|upcase|clean}}" {{if #|first}}selected="selected"{{/if}}>{{.|upcase|clean}}</option>{{/select1.data}}
									</select>
									<input type="hidden" id="select_1_{{lastId}}_hidden" value="0"/>
								</span>
							{{/if}}
						</div>

						<div class="wsal-s2">
							<span class="custom-dropdown custom-dropdown--default custom-dropdown--small">
								<select id="select_2_{{lastId}}" class="js_s2 custom-dropdown__select custom-dropdown__select--default">
									{{select2.data}}<option value="{{.|upcase|clean}}" {{if #|first}}selected="selected"{{/if}}>{{.|upcase|clean}}</option>{{/select2.data}}
								</select>
							   <input type="hidden" id="select_2_{{lastId}}_hidden" value="0"/>
							</span>
						</div>

						<div class="wsal-s3">
							<span class="custom-dropdown custom-dropdown--default custom-dropdown--small">
								<select id="select_3_{{lastId}}" class="js_s3 custom-dropdown__select custom-dropdown__select--default">
									{{select3.data}}<option value="{{.|upcase|clean}}" {{if #|first}}selected="selected"{{/if}}>{{.|upcase|clean}}</option>{{/select3.data}}
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
