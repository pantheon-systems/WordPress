<?php
/**
 * View: Reports
 *
 * Generate reports view.
 *
 * @since 2.7.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WSAL_Rep_Plugin' ) ) {
	exit( 'You are not allowed to view this page.' );
}

/**
 * Class WSAL_Rep_Views_Main for the page Reporting.
 *
 * @package report-wsal
 */
class WSAL_Rep_Views_Main extends WSAL_AbstractView {

	const REPORT_LIMIT = 100;

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
	 * Method: Constructor
	 *
	 * @param object $plugin - Instance of WpSecurityAuditLog.
	 * @since  1.0.0
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		// Call to parent class.
		parent::__construct( $plugin );

		// Ajax events for the report functions.
		add_action( 'wp_ajax_AjaxGenerateReport', array( $this, 'AjaxGenerateReport' ) );
		add_action( 'wp_ajax_AjaxCheckArchiveMatch', array( $this, 'AjaxCheckArchiveMatch' ) );
		add_action( 'wp_ajax_AjaxSummaryUniqueIPs', array( $this, 'AjaxSummaryUniqueIPs' ) );
		add_action( 'wp_ajax_AjaxSendPeriodicReport', array( $this, 'AjaxSendPeriodicReport' ) );

		// Select2 ajax call.
		add_action( 'wp_ajax_AjaxGetUserID', array( $this, 'AjaxGetUserID' ) );

		// Set paths.
		$this->_base_dir = WSAL_BASE_DIR . 'extensions/reports';
		$this->_base_url = WSAL_BASE_URL . 'extensions/reports';
	}

	/**
	 * Method: Get View Title.
	 */
	public function GetTitle() {
		return __( 'Reporting', 'wp-security-audit-log' );
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
		return __( 'Reports', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Weight.
	 */
	public function GetWeight() {
		return 8;
	}

	/**
	 * Method: Get View Header.
	 */
	public function Header() {
		wp_enqueue_style( 'wsal-rep-select2-css', $this->_base_url . '/js/select2/select2.css' );
		wp_enqueue_style( 'wsal-rep-select2-bootstrap-css', $this->_base_url . '/js/select2/select2-bootstrap.css' );
		wp_enqueue_style( 'wsal-jq-ui-css', $this->_base_url . '/js/jquery.datepick/smoothness.datepick.css' );
		wp_enqueue_style( 'wsal-reporting-css', $this->_base_url . '/css/styles.css' );

		wp_enqueue_script( 'wsal-jq-datepick-plugin-js', $this->_base_url . '/js/jquery.datepick/jquery.plugin.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'wsal-jq-datepick-js', $this->_base_url . '/js/jquery.datepick/jquery.datepick.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'wsal-reporting-select2-js', $this->_base_url . '/js/select2/select2.min.js', array( 'jquery' ) );

		$date_format = $this->_plugin->reporting->common->GetDateFormat();
		?>
		<script type="text/javascript">
			var dateFormat = "<?php echo $date_format; ?>";

			function wsal_CreateDatePicker($, $input, date) {
				$input.val(''); // clear
				var WsalDatePick_onSelect = function(date){
					date = date || new Date();
					var v = $.datepick.formatDate(dateFormat, date[0]);
					$input.val(v);
					$(this).change();
				};
				$input.datepick({
					dateFormat: dateFormat,
					selectDefaultDate: true,
					rangeSelect: false,
					multiSelect: 0,
					onSelect: WsalDatePick_onSelect
				}).datepick('setDate', date);
			}

			function checkDate( field ) {
				if ( dateFormat == 'mm-dd-yyyy' || dateFormat == 'dd-mm-yyyy' ) {
					// regular expression to match date format mm-dd-yyyy or dd-mm-yyyy
					re = /^(\d{1,2})-(\d{1,2})-(\d{4})$/;
				} else if ( dateFormat == 'dd.-mm-yyyy' ) {
					re = /^(\d{1,2}).-(\d{1,2})-(\d{4})$/;
				} else {
					// regular expression to match date format yyyy-mm-dd
					re = /^(\d{4})-(\d{1,2})-(\d{1,2})$/;
				}

				if ( field.val() != '' && ! field.val().match(re) ) {
					field.val('');
					return false;
				}
				return true;
			}
		</script>
		<?php
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
					jQuery('div.wsal-tab').hide();
					jQuery(jQuery(this).addClass('nav-tab-active').attr('href')).show();
				});
				// show relevant tab
				var hashlink = jQuery('#wsal-tabs>a[href="' + location.hash + '"]');
				if (hashlink.length) {
					hashlink.click();
				} else {
					jQuery('#wsal-tabs>a:first').click();
				}
				// Add required to Report email and name
				jQuery('input[name=wsal-periodic]').click(function(){
					var valid = true;
					jQuery('#wsal-notif-email').attr("required", true);
					jQuery('#wsal-notif-name').attr("required", true);
					var report_email = jQuery('#wsal-notif-email').val();
					var report_name = jQuery('#wsal-notif-name').val();

					if (!validateEmail(report_email)) {
						//The report_email is illegal
						jQuery('#wsal-notif-email').css('border-color', '#dd3d36');
						valid = false;
					} else {
						jQuery('#wsal-notif-email').css('border-color', '#aaa');
					}

					if (!report_name.match(/^[A-Za-z0-9_\s\-]{1,32}$/)) {
						//The report_name is illegal
						jQuery('#wsal-notif-name').css('border-color', '#dd3d36');
						valid = false;
					} else {
						jQuery('#wsal-notif-name').css('border-color', '#aaa');
					}
					return valid;
				});
				jQuery('input[name=wsal-reporting-submit]').click(function(){
					jQuery('#wsal-notif-email').removeAttr("required");
					jQuery('#wsal-notif-name').removeAttr("required");
				});
			});

			function validateEmail(email) {
				var atpos = email.indexOf("@");
				var dotpos = email.lastIndexOf(".");
				if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) {
					return false;
				} else {
					return true;
				}
			}
		</script>
		<script type="text/javascript">
			var addArchive = false;
			var nextDate = null;

			function AjaxGenerateReport(filters) {
				var limit = <?php echo self::REPORT_LIMIT; ?>;
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					async: true,
					dataType: 'json',
					data: {
						action: 'AjaxGenerateReport',
						filters: filters,
						nextDate: nextDate,
						limit: limit,
						addArchive: addArchive
					},
					success: function(response) {
						nextDate = response[0];
						if (nextDate != 0) {
							var dateString = nextDate;
							dateString = dateString.split(".");
							var d = new Date(dateString[0]*1000);
							jQuery("#ajax-response-counter").html(' Last day examined: '+d.toDateString()+' last day.');
							AjaxGenerateReport(filters);
						} else {
							if (response[1] !== null) {
								jQuery("#ajax-response").html("Process completed.");
								window.setTimeout(function(){ window.location.href = response[1]; }, 300);
							} else {
								jQuery("#ajax-response").html("There are no alerts that match your filtering criteria.");
							}
						}
					},
					error: function(xhr, textStatus, error) {
						console.log(xhr.statusText);
						console.log(textStatus);
						console.log(error);
					}
				});
			}

			function AjaxCheckArchiveMatch(filters) {
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					async: false,
					dataType: 'json',
					data: {
						action: 'AjaxCheckArchiveMatch',
						filters: filters
					},
					success: function(response) {
						if (response) {
							var r = confirm('There are alerts in the archive database that match your report criteria.\nShould these alerts be included in the report?');
							if (r == true) {
								addArchive = true;
							} else {
								addArchive = false;
							}
						}
					}
				});
			}

			function AjaxSummaryUniqueIPs(filters) {
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					async: true,
					dataType: 'json',
					data: {
						action: 'AjaxSummaryUniqueIPs',
						filters: filters
					},
					success: function(response) {
						if (response !== null) {
							jQuery("#ajax-response").html("Process completed.");
							window.setTimeout(function(){ window.location.href = response; }, 300);
						} else {
							jQuery("#ajax-response").html("There are no alerts that match your filtering criteria.");
						}
					}
				});
			}

			function AjaxSendPeriodicReport(name) {
				var limit = <?php echo self::REPORT_LIMIT; ?>;
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					async: true,
					dataType: 'json',
					data: {
						action: 'AjaxSendPeriodicReport',
						name: name,
						nextDate: nextDate,
						limit: limit
					},
					success: function(response) {
						nextDate = response;
						if (nextDate != 0) {
							var dateString = nextDate;
							dateString = dateString.split(".");
							var d = new Date(dateString[0]*1000);
							jQuery("#ajax-response-counter").html(' Last day examined: '+d.toDateString()+' last day.');
							AjaxSendPeriodicReport(name);
						} else {
							jQuery("#ajax-response").html("Email sent.");
						}
					},
					error: function(xhr, textStatus, error) {
						console.log(xhr.statusText);
						console.log(textStatus);
						console.log(error);
					}
				});
			}
		</script>
		<?php
	}

	/**
	 * Generate report through Ajax call.
	 */
	public function AjaxGenerateReport() {
		$selected_db = get_transient( 'wsal_wp_selected_db' );
		if ( ! empty( $selected_db ) && 'archive' == $selected_db ) {
			$this->_plugin->reporting->common->SwitchToArchiveDB();
		}
		$filters                = $_POST['filters'];
		$filters['nextDate']    = $_POST['nextDate'];
		$filters['limit']       = $_POST['limit'];

		$report = $this->_plugin->reporting->common->GenerateReport( $filters, false );
		// Append to the JSON file.
		$this->_plugin->reporting->common->generateReportJsonFile( $report );

		$response[0] = ( ! empty( $report['lastDate'] ) ) ? $report['lastDate'] : 0;

		if ( null == $response[0] ) {
			// Switch to Archive DB.
			if ( isset( $_POST['addArchive'] ) && 'true' === $_POST['addArchive'] ) {
				if ( 'archive' != $selected_db ) {
					// First time.
					$this->_plugin->reporting->common->SwitchToArchiveDB();
					$filters['nextDate'] = null;
					$report = $this->_plugin->reporting->common->GenerateReport( $filters, false );
					// Append to the JSON file.
					$this->_plugin->reporting->common->generateReportJsonFile( $report );
					if ( ! empty( $report['lastDate'] ) ) {
						set_transient( 'wsal_wp_selected_db', 'archive' );
						$response[0] = $report['lastDate'];
					}
				} else {
					// Last time.
					delete_transient( 'wsal_wp_selected_db' );
				}
			}
			if ( null == $response[0] ) {
				$response[1] = $this->_plugin->reporting->common->downloadReportFile();
				$this->_plugin->reporting->common->CloseArchiveDB();
			}
		}
		echo json_encode( $response );
		exit;
	}

	/**
	 * Send the periodic report email through Ajax call.
	 */
	public function AjaxSendPeriodicReport() {
		$report_name = $_POST['name'];
		$next_date = $_POST['nextDate'];
		$limit = $_POST['limit'];
		$last_date = $this->_plugin->reporting->common->sendNowPeriodic( $report_name, $next_date, $limit );
		$response = ( ! empty( $last_date ) ? $last_date : 0);
		echo json_encode( $response );
		exit;
	}

	/**
	 * Check if the Archive is matching the filters, through Ajax call.
	 */
	public function AjaxCheckArchiveMatch() {
		$response = false;
		if ( $this->_plugin->reporting->common->IsArchivingEnabled() ) {
			$filters = $_POST['filters'];
			$this->_plugin->reporting->common->SwitchToArchiveDB();
			$response = $this->_plugin->reporting->common->IsMatchingReportCriteria( $filters );
		}
		echo json_encode( $response );
		exit;
	}

	/**
	 * Generate summary unique IP report through Ajax call.
	 */
	public function AjaxSummaryUniqueIPs() {
		$response = false;
		$filters = $_POST['filters'];
		$response = $this->_plugin->reporting->common->StatisticsUniqueIPS( $filters );
		echo json_encode( $response );
		exit;
	}

	/**
	 * Add/Edit Periodic Report.
	 *
	 * @param array $post_data - Post data array.
	 */
	public function SavePeriodicReport( $post_data ) {
		if ( isset( $post_data ) ) {
			$wsalCommon = $this->_plugin->reporting->common;
			$optName = $wsalCommon::WSAL_PR_PREFIX . strtolower( str_replace( array( ' ', '_' ), '-', $post_data['name'] ) );
			$data = new stdClass();
			$data->title = $post_data['name'];
			$data->email = $post_data['email'];
			$data->type = $post_data['report_format'];
			$data->frequency = $post_data['frequency'];
			$data->sites = array();
			if ( ! empty( $post_data['sites'] ) ) {
				$data->sites = $post_data['sites'];
			}
			if ( ! empty( $post_data['users'] ) ) {
				$data->users = $post_data['users'];
			}
			if ( ! empty( $post_data['roles'] ) ) {
				$data->roles = $post_data['roles'];
			}
			if ( ! empty( $post_data['ip-addresses'] ) ) {
				$data->ipAddresses = $post_data['ip-addresses'];
			}
			$data->owner = get_current_user_id();
			$data->dateAdded = time();
			$data->status = 1;
			$data->viewState = array();
			$data->triggers = array();
			if ( ! empty( $post_data['alert_codes']['alerts'] ) ) {
				$data->viewState[] = 'codes';
				$data->triggers[] = array(
					'alert_id' => $post_data['alert_codes']['alerts'],
				);
			}
			if ( ! empty( $post_data['alert_codes']['post_types'] ) ) {
				$data->viewState[] = 'post_types';
				$data->triggers[] = array(
					'post_types' => $post_data['alert_codes']['post_types'],
				);
			}
			if ( ! empty( $post_data['alert_codes']['post_statuses'] ) ) {
				$data->viewState[] = 'post_statuses';
				$data->triggers[] = array(
					'post_statuses' => $post_data['alert_codes']['post_statuses'],
				);
			}
			if ( ! empty( $post_data['alert_codes']['groups'] ) ) {
				foreach ( $post_data['alert_codes']['groups'] as $key => $group ) {
					$_codes = $this->_plugin->reporting->common->GetCodesByGroup( $group );
					$data->viewState[] = $group;
					$data->triggers[] = array(
						'alert_id' => $_codes,
					);
				}
			}
			// By Criteria
			if ( ! empty( $post_data['unique_ip'] ) ) {
				$data->viewState[] = 'unique_ip';
				$data->triggers[] = array(
					'alert_id' => 1000,
				);
				$data->enableUniqueIps = true;
			}
			if ( ! empty( $post_data['number_logins'] ) ) {
				$data->viewState[] = 'number_logins';
				$data->triggers[] = array(
					'alert_id' => 1000,
				);
				$data->enableNumberLogins = true;
			}
			$this->_plugin->reporting->common->AddGlobalOption( $optName, $data );
		}
	}

	/**
	 * Generate Statistics Report.
	 *
	 * @param array $filters
	 */
	private function generateStatisticsReport( $filters ) {
		$wsalCommon = $this->_plugin->reporting->common;
		if ( isset( $_POST['wsal-criteria'] ) ) {
			$field = trim( $_POST['wsal-criteria'] );
			$filters['type_statistics'] = $field;
			if ( isset( $_POST[ 'wsal-summary-field_' . $field ] ) ) {
				switch ( $field ) {
					case $wsalCommon::LOGIN_BY_USER:
						$filters['users'] = explode( ',', $_POST[ 'wsal-summary-field_' . $field ] );
						// Logins alert
						$filters['alert_codes']['alerts'] = array( 1000 );
						break;
					case $wsalCommon::LOGIN_BY_ROLE:
						$filters['roles'] = explode( ',', $_POST[ 'wsal-summary-field_' . $field ] );
						// Logins alert
						$filters['alert_codes']['alerts'] = array( 1000 );
						break;
					case $wsalCommon::VIEWS_BY_USER:
						$filters['users'] = explode( ',', $_POST[ 'wsal-summary-field_' . $field ] );
						// Viewed content alerts
						$filters['alert_codes']['alerts'] = array( 2101, 2103, 2105 );
						break;
					case $wsalCommon::VIEWS_BY_ROLE:
						$filters['roles'] = explode( ',', $_POST[ 'wsal-summary-field_' . $field ] );
						// Viewed content alerts
						$filters['alert_codes']['alerts'] = array( 2101, 2103, 2105 );
						break;
					case $wsalCommon::PUBLISHED_BY_USER:
						$filters['users'] = explode( ',', $_POST[ 'wsal-summary-field_' . $field ] );
						// Published content alerts
						$filters['alert_codes']['alerts'] = array( 2001, 2005, 2030, 9001 );
						break;
					case $wsalCommon::PUBLISHED_BY_ROLE:
						$filters['roles'] = explode( ',', $_POST[ 'wsal-summary-field_' . $field ] );
						// Published content alerts
						$filters['alert_codes']['alerts'] = array( 2001, 2005, 2030, 9001 );
						break;
				}
			}
			if ( $field == $wsalCommon::DIFFERENT_IP ) {
				if ( isset( $_POST['only_login'] ) ) {
					$filters['alert_codes']['alerts'] = array( 1000 );
				} else {
					$filters['alert_codes']['groups'] = array(
						'Blog Posts',
						'Comments',
						'Custom Post Types',
						'Pages',
						'BBPress Forum',
						'WooCommerce',
						'Other User Activity',
						'User Profiles',
						'Database',
						'MultiSite',
						'Plugins & Themes',
						'System Activity',
						'Menus',
						'Widgets',
						'Site Settings',
					);
				}
			}
		}
		if ( ! empty( $_POST['wsal-from-date'] ) ) {
			$filters['date_range']['start'] = trim( $_POST['wsal-from-date'] );
		}
		if ( ! empty( $_POST['wsal-to-date'] ) ) {
			$filters['date_range']['end'] = trim( $_POST['wsal-to-date'] );
		}

		if ( isset( $_POST['include-archive'] ) ) {
			$this->_plugin->reporting->common->AddGlobalOption( 'include-archive', true );
		} else {
			$result = $this->_plugin->reporting->common->DeleteGlobalOption( 'wsal-include-archive' );
		}
		?>
		<script type="text/javascript">
			var filters = <?php echo json_encode( $filters ); ?>;
		</script>
		<?php
		if ( ! empty( $field ) && $field == $wsalCommon::DIFFERENT_IP ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					AjaxSummaryUniqueIPs(filters);
				});
			</script>
			<?php
		} else {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					//AjaxCheckArchiveMatch(filters);
					AjaxGenerateReport(filters);
				});
			</script>
			<?php
		}
		?>
		<div class="updated">
			<p id="ajax-response">
				<img src="<?php echo esc_url( $this->_base_url ); ?>/css/loading.gif">
				<?php _e( ' Generating reports. Please do not close this window', 'wp-security-audit-log' ); ?>
				<span id="ajax-response-counter"></span>
			</p>
		</div>
		<?php
	}

	/**
	 * Method: Get View.
	 */
	public function Render() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
			$network_admin = get_site_option( 'admin_email' );
			$message = esc_html__( 'To generate a report or configure automated scheduled report please contact the administrator of this multisite network on ', 'wp-security-audit-log' );
			$message .= '<a href="mailto:' . esc_attr( $network_admin ) . '" target="_blank">' . esc_html( $network_admin ) . '</a>';
			wp_die( $message );
		}
		// Verify the uploads directory.
		$uploadsDirObj = wp_upload_dir();
		$wpsalRepUploadsDir = trailingslashit( $uploadsDirObj['basedir'] ) . 'wp-security-audit-log/reports/';
		$pluginDir = realpath( dirname( __FILE__ ) . '/../../' );

		if ( $this->_plugin->reporting->common->CheckDirectory( $wpsalRepUploadsDir ) ) {
			include( $pluginDir . '/inc/wsal-reporting-view.inc.php' );
		} else {
			if ( ! wp_mkdir_p( $wpsalRepUploadsDir ) ) {
			?>
				<div class="error">
					<?php
					echo sprintf( __( 'The %s directory which the Reports plugin uses to create reports in was either not found or is not accessible.', 'wp-security-audit-log' ), 'uploads' ) . '<br><br>';
					echo sprintf( __( 'In order for the plugin to function, the directory %s must be created and the plugin should have ', 'wp-security-audit-log' ), $wpsalRepUploadsDir ) . '<br>';
					echo sprintf( __( 'access to write to this directory, so please configure the following permissions: 0755. If you have any questions or need further assistance please %s', 'wp-security-audit-log' ), '<a href="mailto:support@wpwhitesecurity.com">contact us</a>' );
					?>
				</div>
			<?php
			} else {
				include( $pluginDir . '/inc/wsal-reporting-view.inc.php' );
			}
		}
	}

	/**
	 * Get the user id through ajax, used in 'select2'.
	 */
	public function AjaxGetUserID() {
		$data = array();
		if ( isset( $_GET['term'] ) ) {
			$user = get_user_by( 'login', trim( $_GET['term'] ) );
			if ( $user ) {
				array_push(
					$data, array(
						'id' => $user->ID,
						'name' => $user->user_login,
					)
				);
			}
		}
		echo json_encode( $data );
		die();
	}
}
