<?php
/**
 * View: Wizard
 *
 * Notification Wizard.
 *
 * @since 2.7.0
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
 * Class WSAL_NP_Wizard for the wizard.
 *
 * @package wp-security-audit-log
 */
class WSAL_NP_Wizard extends WSAL_AbstractView {

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
	 * @param object $plugin - Instance of WpSecurityAuditLog.
	 * @since 2.7.0
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		parent::__construct( $plugin );
		add_action( 'wp_ajax_SaveFirstStep', array( $this, 'SaveFirstStep' ) );
		add_action( 'wp_ajax_SaveChanges', array( $this, 'SaveChanges' ) );
		add_action( 'wp_ajax_ShowAlertByType', array( $this, 'ShowAlertByType' ) );

		// Set the paths.
		$this->_base_dir = WSAL_BASE_DIR . 'extensions/email-notifications';
		$this->_base_url = WSAL_BASE_URL . 'extensions/email-notifications';
	}

	public function GetTitle() {
		return __( 'Email Notifications Wizard', 'wp-security-audit-log' );
	}

	public function GetIcon() {
		return 'dashicons-admin-generic';
	}

	public function GetName() {
		return __( 'Wizard', 'wp-security-audit-log' );
	}

	public function GetWeight() {
		return 8;
	}

	protected function GetSafeCatgName( $name ) {
		return strtolower(
			preg_replace( '/[^A-Za-z0-9\-]/', '-', $name )
		);
	}

	public function Header() {
		wp_enqueue_style( 'wsal-notif-css', $this->_base_url . '/css/wizard.css' );
	}

	public function Footer() {
		wp_enqueue_script( 'wizard-js', $this->_base_url . '/js/wizard.js', array( 'jquery' ) );
	}

	public function SaveFirstStep() {
		$notifBuilder = new WSAL_NP_Notifications( $this->_plugin );
		$result = 0;
		$results = array();

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
		);

		if ( isset( $_POST['builtIn'] ) ) {
			$aBuiltIn = (array) json_decode( stripslashes( $_POST['builtIn'] ) );
			foreach ( $aBuiltIn as $key => $value ) {
				if ( ! empty( $value ) ) {
					$email = (array) $value;
					$results[] = $notifBuilder->saveBuilt_in( $key, $titles[ $key ], $email['email'], $events[ $key ] );
				} else {
					$results[] = $notifBuilder->saveBuilt_in( $key, null, null, null );
				}
			}
		}
		if ( in_array( 2, $results ) ) {
			$result = 2;
		} elseif ( in_array( 1, $results ) ) {
			$result = 1;
		}

		echo json_encode( $result );
		exit;
	}

	public function SaveChanges() {
		$notifBuilder = new WSAL_NP_Notifications( $this->_plugin );
		$result = false;

		if ( isset( $_POST['alerts'] ) && isset( $_POST['email'] ) && isset( $_POST['name'] ) ) {
			$email = trim( $_POST['email'] );
			$title = trim( $_POST['name'] );
			$name = $this->GetSafeCatgName( $title );
			$events = json_decode( stripslashes( $_POST['alerts'] ) );
			$result = $notifBuilder->saveBuilt_in( $name, $title, $email, $events, false );
		}

		echo json_encode( $result );
		exit;
	}

	public function ShowAlertByType() {
		$post_array = $_POST;
		if ( isset( $post_array['type'] ) && isset( $post_array['parentType'] ) ) {
			$alert = new WSAL_Alert();
			$grouped_alerts = $this->_plugin->alerts->GetCategorizedAlerts();
			foreach ( $grouped_alerts as $group_name => $group_alerts ) {
				$friendly_name = $this->GetSafeCatgName( $group_name );
				if ( $post_array['parentType'] == $friendly_name ) {
					echo '<tr><td colspan="2" style="padding-bottom: 0;"><h2>' . esc_html( $group_name ) . '</h2></td></tr>';
					echo '<tr><th><label for="columns">Alerts:</label></th>';
					echo "<td><fieldset><label><input type=\"checkbox\" onclick=\"checkAll(this, 'alerts');\" class=\"option\">";
					echo '<span class="title">' . esc_html__( 'Check All', 'wp-security-audit-log' ) . '</span></label></fieldset>';
					echo '<fieldset><input type="hidden" id="category-name" value="' . esc_attr( $friendly_name ) . '">';
					$alerts = array();
					foreach ( $group_alerts as $alert_name => $alert ) {
						$safe_alert_name = $this->GetSafeCatgName( $alert_name );
						if ( $post_array['type'] == $safe_alert_name ) {
							$alerts = $alert;
						}
					}

					if ( ! empty( $alerts ) && is_array( $alerts ) ) {
						foreach ( $alerts as $alert ) {
							echo '<label for="' . esc_attr( $alert->type ) . '">';
							echo '<input type="checkbox" name="alerts[]" id="' . esc_attr( $alert->type ) . '" class="option" value="' . esc_attr( $alert->type ) . '">';
							echo '<span class="title">' . esc_html( $alert->type ) . ' (' . esc_html( $alert->desc ) . ')' . '</span></label><br/>';
						}
					}
					echo '</fieldset></td></tr>';
				}
			}
		}
		exit;
	}

	public function Render() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
		}

		$oNP = $this->_plugin->views->FindByClassName( 'WSAL_NP_Notifications' );
		if ( false === $oNP ) {
			$oNP = new WSAL_NP_Notifications( $this->_plugin );
		}
		?>
		<div class="wrap">
			<h2 id="wsal-tabs" class="nav-tab-wrapper">
				<a href="#tab-first" class="nav-tab"><?php esc_html_e( 'Enable Recommended' ); ?><br><?php esc_html_e( 'Security Notifications' ); ?></a>
				<a href="#tab-second" class="nav-tab disabled"><?php esc_html_e( 'Select Alerts' ); ?><br><?php esc_html_e( 'Categories' ); ?></a>
				<a href="#tab-third" class="nav-tab disabled"><?php esc_html_e( 'Select the' ); ?><br><?php esc_html_e( 'specific change' ); ?></a>
				<a href="#tab-fourth" class="nav-tab disabled"><br><?php esc_html_e( 'Save changes' ); ?><br></a>
				<a href="#tab-fifth" class="nav-tab disabled"><br><?php esc_html_e( 'Finish' ); ?><br></a>
			</h2>
			<div class="nav-tabs">
				<!-- Tab Enable Recommended Security Notifications -->
				<table class="form-table wsal-tab" id="tab-first">
					<tbody class="widefat">
						<tr>
							<td colspan="2">
								<h2><?php esc_html_e( 'Enable Pre-built Notifications', 'wp-security-audit-log' ); ?></h2>
								<p>
									<span class="description"><?php esc_html_e( 'In this first step of the wizard you can enable the recommended security related email notifications. If you do not want to enable any of these alerts click Skip this Step.', 'wp-security-audit-log' ); ?></span><br>
									<span class="description"><?php esc_html_e( 'Tick any of the alerts below to enable them.', 'wp-security-audit-log' ); ?></span>
								</p>
							</td>
						</tr>
						<?php
						$checked = array();
						$email = array();

						$aBuilt_in = $this->_plugin->wsalCommon->GetBuiltIn();
						if ( ! empty( $aBuilt_in ) && count( $aBuilt_in ) > 0 ) {
							foreach ( $aBuilt_in as $k => $v ) {
								$optValue = unserialize( $v->option_value );
								$checked[] = $optValue->viewState[0];
								$email[ $optValue->id ] = $optValue->email;
							}
						}
						?>
						<tr>
							<th><label for="columns"><?php esc_html_e( 'Alert me when:', 'wp-security-audit-log' ); ?></label></th>
							<td>
								<fieldset>
									<label for="built-in">
										<input type="checkbox" id="built-in" onclick="checkAll(this, 'built-in');" class="option">
										<span class="option-title"><?php esc_html_e( 'Check All', 'wp-security-audit-log' ); ?></span>
									</label>
									<br/>
									<label for="1">
										<input type="checkbox" name="built-in[]" id="1" class="built-in" <?php echo (in_array( 'trigger_id_1', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'User logs in', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-1" id="email-1" placeholder="Email *" value="<?php echo ( ! empty( $email[1] ) ? $email[1] : null); ?>">
									</label>
									<br/>
									<label for="2">
										<input type="checkbox" name="built-in[]" id="2" class="built-in" <?php echo (in_array( 'trigger_id_2', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'New user is created', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-2" id="email-2" placeholder="Email *" value="<?php echo ( ! empty( $email[2] ) ? $email[2] : null); ?>">
									</label>
									<br/>
									<label for="3">
										<input type="checkbox" name="built-in[]" id="3" class="built-in" <?php echo (in_array( 'trigger_id_3', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'User changed password', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-3" id="email-3" placeholder="Email *" value="<?php echo ( ! empty( $email[3] ) ? $email[3] : null); ?>">
									</label>
									<br/>
									<label for="4">
										<input type="checkbox" name="built-in[]" id="4" class="built-in" <?php echo (in_array( 'trigger_id_4', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'User changed the password of another user', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-4" id="email-4" placeholder="Email *" value="<?php echo ( ! empty( $email[4] ) ? $email[4] : null); ?>">
									</label>
									<br/>
									<label for="5">
										<input type="checkbox" name="built-in[]" id="5" class="built-in" <?php echo (in_array( 'trigger_id_5', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( "User's role has changed", 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-5" id="email-5" placeholder="Email *" value="<?php echo ( ! empty( $email[5] ) ? $email[5] : null); ?>">
									</label>
									<br/>
									<label for="6">
										<input type="checkbox" name="built-in[]" id="6" class="built-in" <?php echo (in_array( 'trigger_id_6', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'Published content is modified', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-6" id="email-6" placeholder="Email *" value="<?php echo ( ! empty( $email[6] ) ? $email[6] : null); ?>">
									</label>
									<br/>
									<label for="7">
										<input type="checkbox" name="built-in[]" id="7" class="built-in" <?php echo (in_array( 'trigger_id_7', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'Content is published', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-7" id="email-7" placeholder="Email *" value="<?php echo ( ! empty( $email[7] ) ? $email[7] : null); ?>">
									</label>
									<br/>
									<label for="8">
										<input type="checkbox" name="built-in[]" id="8" class="built-in" <?php echo (in_array( 'trigger_id_8', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'First time user logs in', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-8" id="email-8" placeholder="Email *" value="<?php echo ( ! empty( $email[8] ) ? $email[8] : null); ?>">
									</label>
									<br/>
									<label for="9">
										<input type="checkbox" name="built-in[]" id="9" class="built-in" <?php echo (in_array( 'trigger_id_9', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'New plugin is installed', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-9" id="email-9" placeholder="Email *" value="<?php echo ( ! empty( $email[9] ) ? $email[9] : null); ?>">
									</label>
									<br/>
									<label for="10">
										<input type="checkbox" name="built-in[]" id="10" class="built-in" <?php echo (in_array( 'trigger_id_10', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'Installed plugin is activated', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-10" id="email-10" placeholder="Email *" value="<?php echo ( ! empty( $email[10] ) ? $email[10] : null); ?>">
									</label>
									<br/>
									<label for="11">
										<input type="checkbox" name="built-in[]" id="11" class="built-in" <?php echo (in_array( 'trigger_id_11', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'Plugin file is modified', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-11" id="email-11" placeholder="Email *" value="<?php echo ( ! empty( $email[11] ) ? $email[11] : null); ?>">
									</label>
									<br/>
									<label for="12">
										<input type="checkbox" name="built-in[]" id="12" class="built-in" <?php echo (in_array( 'trigger_id_12', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'New theme is installed', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-12" id="email-12" placeholder="Email *" value="<?php echo ( ! empty( $email[12] ) ? $email[12] : null); ?>">
									</label>
									<br/>
									<label for="13">
										<input type="checkbox" name="built-in[]" id="13" class="built-in" <?php echo (in_array( 'trigger_id_13', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'Installed theme is activated', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-13" id="email-13" placeholder="Email *" value="<?php echo ( ! empty( $email[13] ) ? $email[13] : null); ?>">
									</label>
									<br/>
									<label for="14">
										<input type="checkbox" name="built-in[]" id="14" class="built-in" <?php echo (in_array( 'trigger_id_14', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'Theme file is modified', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-14" id="email-14" placeholder="Email *" value="<?php echo ( ! empty( $email[14] ) ? $email[14] : null); ?>">
									</label>
									<br/>
									<label for="15">
										<input type="checkbox" name="built-in[]" id="15" class="built-in" <?php echo (in_array( 'trigger_id_15', $checked ) ? 'checked' : ''); ?>>
										<span class="option-title"><?php esc_html_e( 'Critical Alert is Generated', 'wp-security-audit-log' ); ?></span>
										<input type="text" class="option-email" name="email-15" id="email-15" placeholder="Email *" value="<?php echo ( ! empty( $email[15] ) ? $email[15] : null); ?>">
									</label>
									<br/>
								</fieldset>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<p>
									<span class="description"><?php esc_html_e( 'To specify multiple email addresses separate them with a comma.', 'wp-security-audit-log' ); ?></span>
								</p>
							</td>
						</tr>
					</tbody>
					<tbody class="widefat">
						<tr>
							<td colspan="2" class="buttons">
								<button type="button" class="button-primary" id="save-first-step">Next</button>
								<button type="button" class="button-secondary" onclick="nextStep('second');">Skip Step</button>
								<a class="button-secondary" href="<?php echo esc_attr( $oNP->GetUrl() ); ?>">Cancel Wizard</a>
							</td>
						</tr>
					</tbody>
				</table>
				<!-- Tab Select Alerts Categories -->
				<table class="form-table wsal-tab" id="tab-second">
					<tbody class="widefat">
						<tr>
							<td colspan="2">
								<h2><?php esc_html_e( 'Step 1:', 'wp-security-audit-log' ); ?></h2>
								<p>
									<span class="description"><?php esc_html_e( 'Select for which alert categories you would like to setup email notifications.', 'wp-security-audit-log' ); ?></span>
								</p>
							</td>
						</tr>
						<tr>
							<th><label for="columns"><?php esc_html_e( 'Type of changes:', 'wp-security-audit-log' ); ?></label></th>
							<td>
								<fieldset>
									<label for="type_1">
										<input type="radio" name="types[]" id="type_1" data-group-parent="users-profiles---activity" value="other-user-activity" class="option">
										<span class="title"><?php esc_html_e( 'User Changes', 'wp-security-audit-log' ); ?></span>
									</label>
									<br/>
									<label for="type_2">
										<input type="radio" name="types[]" id="type_2" data-group-parent="users-profiles---activity" value="user-profiles" class="option">
										<span class="title"><?php esc_html_e( 'User Profile', 'wp-security-audit-log' ); ?></span>
									</label>
									<br/>
									<label for="type_3">
										<input type="radio" name="types[]" id="type_3" data-group-parent="wordpress---multisite-management" value="plugins---themes" class="option">
										<span class="title"><?php esc_html_e( 'Plugin & Theme', 'wp-security-audit-log' ); ?></span>
									</label>
									<br/>
									<label for="type_4">
										<input type="radio" name="types[]" id="type_4" data-group-parent="content---comments" value="blog-posts" class="option">
										<span class="title"><?php esc_html_e( 'Posts', 'wp-security-audit-log' ); ?></span>
									</label>
									<br/>
									<label for="type_5">
										<input type="radio" name="types[]" id="type_5" data-group-parent="content---comments" value="pages" class="option">
										<span class="title"><?php esc_html_e( 'Pages', 'wp-security-audit-log' ); ?></span>
									</label>
									<br/>
									<label for="type_6">
										<input type="radio" name="types[]" id="type_6" data-group-parent="content---comments" value="custom-post-types" class="option">
										<span class="title"><?php esc_html_e( 'Custom Post Types', 'wp-security-audit-log' ); ?></span>
									</label>
									<br/>
									<label for="type_7">
										<input type="radio" name="types[]" id="type_7" data-group-parent="wordpress---multisite-management" value="menus" class="option">
										<span class="title"><?php esc_html_e( 'Menus', 'wp-security-audit-log' ); ?></span>
									</label>
									<br/>
									<label for="type_8">
										<input type="radio" name="types[]" id="type_8" data-group-parent="wordpress---multisite-management" value="widgets" class="option">
										<span class="title"><?php esc_html_e( 'Widgets', 'wp-security-audit-log' ); ?></span>
									</label>
									<br/>
									<label for="type_9">
										<input type="radio" name="types[]" id="type_9" data-group-parent="wordpress---multisite-management" value="system-activity" class="option">
										<span class="title"><?php esc_html_e( 'WordPress Settings', 'wp-security-audit-log' ); ?></span>
									</label>
									<br/>
									<label for="type_10">
										<input type="radio" name="types[]" id="type_10" data-group-parent="third-party-support" value="bbpress-forum" class="option">
										<span class="title"><?php esc_html_e( 'bbPress', 'wp-security-audit-log' ); ?></span>
									</label>
								</fieldset>
							</td>
						</tr>
					</tbody>
					<tbody class="widefat">
						<tr>
							<td colspan="2" class="buttons">
								<button type="button" class="button-primary" id="button-types" onclick="goToThirdStep();">Next</button>
								<a class="button-secondary" href="<?php echo esc_attr( $oNP->GetUrl() ); ?>">Cancel Wizard</a>
							</td>
						</tr>
					</tbody>
				</table>
				<!-- Tab Select the specific change -->
				<table class="form-table wsal-tab" id="tab-third">
					<tbody class="widefat">
						<tr>
							<td colspan="2">
								<h2><?php esc_html_e( 'Step 2:', 'wp-security-audit-log' ); ?></h2>
								<p>
									<span class="description"><?php esc_html_e( 'Select the specific change for which you would like to be alerted.', 'wp-security-audit-log' ); ?></span><br>
								</p>
							</td>
						</tr>
						<tr id="loading">
							<td colspan="2" style="text-align: center;">
								<img src="<?php echo esc_url( $this->_base_url ); ?>/img/loading.gif">
							</td>
						</tr>
					</tbody>
					<tbody class="widefat" id="category-alerts">
						<!-- Generate from the ajax -->
					</tbody>
					<tbody class="widefat">
						<tr>
							<td colspan="2" class="buttons">
								<button type="button" class="button-secondary" id="backToCategory">Back</button>
								<button type="button" id="save-button" class="button-primary" onclick="nextStep('fourth');">Next</button>
								<a class="button-secondary" href="<?php echo esc_attr( $oNP->GetUrl() ); ?>">Cancel Wizard</a>
							</td>
						</tr>
					</tbody>
				</table>
				<!-- Tab Save changes -->
				<table class="form-table wsal-tab" id="tab-fourth">
					<tbody class="widefat">
						<tr>
							<td colspan="2">
								<h2><?php esc_html_e( 'Step 3:', 'wp-security-audit-log' ); ?></h2>
							</td>
						</tr>
						<tr>
							<th style="padding-bottom: 5px;"><label for="columns"><?php esc_html_e( 'Send an email to:', 'wp-security-audit-log' ); ?></label></th>
							<td style="padding-bottom: 5px;">
								<fieldset>
									<input type="email" class="option-email" placeholder="Email *" id="notifications-email" value="">
								</fieldset>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="padding-top: 0;">
								<p>
									<span class="description"><?php esc_html_e( 'To specify multiple email addresses separate them with a comma.', 'wp-security-audit-log' ); ?></span>
								</p>
							</td>
						</tr>
						<tr>
							<th><label for="columns"><?php esc_html_e( 'Email Notification Name:', 'wp-security-audit-log' ); ?></label></th>
							<td>
								<fieldset>
									<input type="text" class="option-name" placeholder="Name" id="notifications-name">
								</fieldset>
							</td>
						</tr>
					</tbody>
					<tbody class="widefat">
						<tr>
							<td colspan="2" class="buttons">
								<button type="button" class="button-secondary" id="backToAlerts">Back</button>
								<button type="button" id="save-button" class="button-primary" onclick="saveNotifications();">Save Changes</button>
								<a class="button-secondary" href="<?php echo esc_attr( $oNP->GetUrl() ); ?>">Cancel Wizard</a>
							</td>
						</tr>
					</tbody>
				</table>
				<!-- Tab Finish -->
				<table class="form-table wsal-tab" id="tab-fifth">
					<tbody class="widefat">
						<tr>
							<td>
								<div class="finish-box">
									<h2><?php esc_html_e( 'To configure more alerts launch this wizard again or click the Add New button in the Email Notifications Trigger Builder tab to manually build a notification with the Trigger Builder.', 'wp-security-audit-log' ); ?></h2>
								</div>
							</td>
						</tr>
					</tbody>
					<tbody class="widefat">
						<tr>
							<td colspan="2" class="buttons">
								<a class="button-secondary" href="#tab-second" onclick="window.location.reload(true);">Configure Another Alert</a>
								<a class="button-primary" href="<?php echo esc_attr( $oNP->GetUrl() ); ?>">Exit Wizard</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
}
