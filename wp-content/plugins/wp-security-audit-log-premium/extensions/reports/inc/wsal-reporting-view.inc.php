<?php
/**
 * View: Reports Main
 *
 * Main reports view.
 *
 * @since 1.0.0
 * @package Wsal
 * @subpackage reports
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WSAL_Rep_Plugin' ) ) {
	return;
}

// Class mapping.
$wsal_common = $this->_plugin->reporting->common;

// Get available roles.
$roles = $wsal_common->GetRoles();

// Get available alert catogories.
$alerts = $this->_plugin->alerts->GetCategorizedAlerts();

// Get the Request method.
$rm = strtoupper( $_SERVER['REQUEST_METHOD'] );

// region >>>  PREPARE DATA FOR JS
// ## SITES
// Limit 0f 20.
$wsal_a = WSAL_Rep_Common::GetSites( 20 );
$wsal_rep_sites = array();
foreach ( $wsal_a as $entry ) {
	// entry.blog_id, entry.domain.
	$c = new stdClass();
	$c->id = $entry->blog_id;
	$c->text = $entry->blogname;
	array_push( $wsal_rep_sites, $c );
}
$wsal_rep_sites = json_encode( $wsal_rep_sites );

// ## ROLES
$wp_roles = array();
foreach ( $roles as $i => $entry ) {
	// entry.blog_id, entry.domain.
	$c = new stdClass();
	$c->id = $entry;
	$c->text = $entry;
	array_push( $wp_roles, $c );
}
$wsal_rep_roles = json_encode( $wp_roles );

// ## IPs
// limit 0f 20
$wsal_ips = WSAL_Rep_Common::GetIPAddresses( 20 );
$wsal_rep_ips = array();
foreach ( $wsal_ips as $entry ) {
	$c = new stdClass();
	$c->id = $entry;
	$c->text = $entry;
	array_push( $wsal_rep_ips, $c );
}
$wsal_rep_ips = json_encode( $wsal_rep_ips );

// ## ALERT GROUPS
$_alerts = array();
foreach ( $alerts as $cname => $group ) {
	foreach ( $group as $subname => $_entries ) {
		$_alerts[ $subname ] = $_entries;
	}
}
$ag = array();
foreach ( $_alerts as $cname => $_entries ) {
	$t = new stdClass();
	$t->text = $cname;
	$t->children = array();
	foreach ( $_entries as $i => $_arr_obj ) {
		$c = new stdClass();
		$c->id = $_arr_obj->type;
		$c->text = $c->id . ' (' . $_arr_obj->desc . ')';
		array_push( $t->children, $c );
	}
	array_push( $ag, $t );
}
$wsal_rep_alert_groups = json_encode( $ag );

// Post Types.
$post_types_args = array(
	'public' => true,
);
$post_types = get_post_types( $post_types_args, 'names' );
$post_types_arr = array();
foreach ( $post_types as $post_type ) {
	// Skip attachment post type.
	if ( 'attachment' === $post_type ) {
		continue;
	}

	$type = new stdClass();
	$type->id = $post_type;
	$type->text = ucfirst( $post_type );
	array_push( $post_types_arr, $type );
}
$wsal_rep_post_types = wp_json_encode( $post_types_arr );

// Post Statuses.
$post_statuses = get_post_statuses();
$post_statuses['future'] = 'Future';
$post_status_arr = array();
foreach ( $post_statuses as $key => $post_status ) {
	$status = new stdClass();
	$status->id = $key;
	$status->text = $post_status;
	array_push( $post_status_arr, $status );
}
$wsal_rep_post_statuses = wp_json_encode( $post_status_arr );

// Endregion >>>  PREPARE DATA FOR JS.
// The final filter array to use to filter alerts.
$filters = array(
	// Option #1 - By Site(s)
	'sites' => array(), // by default, all sites
	// Option #2 - By user(s)
	'users' => array(), // by default, all users
	// Option #3 - By Role(s)
	'roles' => array(), // by default, all roles
	// Option #4 - By IP Address(es)
	'ip-addresses' => array(), // by default, all IPs
	// Option #5 - By Alert Code(s).
	'alert_codes' => array(
		'groups' => array(),
		'alerts' => array(),
	),
	// Option #6 - Date range.
	'date_range' => array(
		'start' => null,
		'end' => null,
	),
	// Option #7 - Report format (HTML || CSV).
	'report_format' => $wsal_common::REPORT_HTML,
);

if ( 'POST' == $rm && isset( $_POST['wsal_reporting_view_field'] ) ) {
	// Verify nonce.
	if ( ! wp_verify_nonce( $_POST['wsal_reporting_view_field'], 'wsal_reporting_view_action' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page - rep plugin.', 'reports-wsal' ) );
	}

	// The default error message to display if the form is not valid.
	$message_form_not_valid = __( 'Invalid Request. Please refresh the page and try again.', 'reports-wsal' );

	// Inspect the form data.
	$form_data = $_POST;

	// Region >>>> By Site(s).
	if ( isset( $form_data['wsal-rb-sites'] ) ) {
		$rbs = intval( $form_data['wsal-rb-sites'] );
		if ( 1 == $rbs ) {
			/*[ already implemented in the $filters array ]*/
		} elseif ( 2 == $rbs ) {
			// The textbox must be here and have values - these will be validated later on.
			if ( ! isset( $form_data['wsal-rep-sites'] ) || empty( $form_data['wsal-rep-sites'] ) ) {
				?><div class="error"><p><?php esc_html_e( 'Error (TODO - error message): Please select SITES', 'reports-wsal' ); ?></p></div>
				<?php
			} else {
				$filters['sites'] = explode( ',', $form_data['wsal-rep-sites'] );
			}
		}
	} else {
		?>
		<div class="error"><p><?php echo esc_html( $message_form_not_valid ); ?></p></div>
		<?php
	}
	// endregion >>>> By Site(s)
	// Region >>>> By User(s).
	if ( isset( $form_data['wsal-rb-users'] ) ) {
		$rbs = intval( $form_data['wsal-rb-users'] );
		if ( 1 == $rbs ) {
			/*[ already implemented in the $filters array ]*/
		} elseif ( 2 == $rbs ) {
			// The textbox must be here and have values - these will be validated later on.
			if ( ! isset( $form_data['wsal-rep-users'] ) || empty( $form_data['wsal-rep-users'] ) ) {
				?>
				<div class="error"><p><?php esc_html_e( 'Error (TODO - error message): Please select USERS', 'reports-wsal' ); ?></p></div>
				<?php
			} else {
				$filters['users'] = explode( ',', $form_data['wsal-rep-users'] );
			}
		}
	} else {
		?>
		<div class="error"><p><?php echo esc_html( $message_form_not_valid ); ?></p></div>
		<?php
	}
	// endregion >>>> By User(s)
	// Region >>>> By Role(s).
	if ( isset( $form_data['wsal-rb-roles'] ) ) {
		$rbs = intval( $form_data['wsal-rb-roles'] );
		if ( 1 == $rbs ) { /*[ already implemented in the $filters array ]*/
		} elseif ( 2 == $rbs ) {
			// The textbox must be here and have values - these will be validated later on.
			if ( ! isset( $form_data['wsal-rep-roles'] ) || empty( $form_data['wsal-rep-roles'] ) ) {
				?>
				<div class="error"><p><?php esc_html_e( 'Error: Please select at least one role', 'reports-wsal' ); ?></p></div>
				<?php
			} else {
				$filters['roles'] = explode( ',', $form_data['wsal-rep-roles'] );
			}
		}
	} else {
		?>
		<div class="error"><p><?php echo esc_html( $message_form_not_valid ); ?></p></div>
		<?php
	}
	// endregion >>>> By Role(s)
	// Region >>>> By IP(s).gw.
	if ( isset( $form_data['wsal-rb-ip-addresses'] ) ) {
		$rbs = intval( $form_data['wsal-rb-ip-addresses'] );
		if ( 1 == $rbs ) { /*[ already implemented in the $filters array ]*/
		} elseif ( 2 == $rbs ) {
			// The textbox must be here and have values - these will be validated later on.
			if ( ! isset( $form_data['wsal-rep-ip-addresses'] ) || empty( $form_data['wsal-rep-ip-addresses'] ) ) {
				?>
				<div class="error"><p><?php esc_html_e( 'Error: Please select at least one IP address', 'reports-wsal' ); ?></p></div>
				<?php
			} else {
				$filters['ip-addresses'] = explode( ',', $form_data['wsal-rep-ip-addresses'] );
			}
		}
	} else {
		?>
		<div class="error"><p><?php echo esc_html( $message_form_not_valid ); ?></p></div>
		<?php
	}
	// endregion >>>> By IP(s)
	// Region >>>> By Alert Code(s).
	$_select_all_groups = (isset( $form_data['wsal-rb-groups'] ) ? true : false);

	// Check alert groups.
	if ( $_select_all_groups ) {
		$filters['alert_codes']['groups'] = array_keys( $_alerts );
	} else {
		// Check for selected alert groups.
		if ( isset( $form_data['wsal-rb-alerts'] ) && ! empty( $form_data['wsal-rb-alerts'] ) ) {
			$filters['alert_codes']['groups'] = $form_data['wsal-rb-alerts'];
		}

		// Check for selected post types.
		if ( isset( $form_data['wsal-rb-post-types'] ) && isset( $form_data['wsal-rep-post-types'] ) && ! empty( $form_data['wsal-rep-post-types'] ) ) {
			// Get selected post types.
			$filters['alert_codes']['post_types'] = explode( ',', $form_data['wsal-rep-post-types'] );
		}

		// Check for selected post statuses.
		if ( isset( $form_data['wsal-rb-post-status'] ) && isset( $form_data['wsal-rep-post-status'] ) && ! empty( $form_data['wsal-rep-post-status'] ) ) {
			// Get selected post status.
			$filters['alert_codes']['post_statuses'] = explode( ',', $form_data['wsal-rep-post-status'] );
		}

		// Check for individual alerts.
		if ( isset( $form_data['wsal-rb-alert-codes'] ) && isset( $form_data['wsal-rep-alert-codes'] ) && ! empty( $form_data['wsal-rep-alert-codes'] ) ) {
			$filters['alert_codes']['alerts'] = explode( ',', $form_data['wsal-rep-alert-codes'] );
		}
	}

	// Report Number of logins.
	if ( isset( $form_data['number_logins'] ) ) {
		$filters['number_logins'] = true;
		$filters['alert_codes']['alerts'] = array( 1000 );
	}

	// Report Number and list of unique IP.
	if ( isset( $form_data['unique_ip'] ) ) {
		$filters['unique_ip'] = true;
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

	// Region >>>> By Date Range(s).
	if ( isset( $form_data['wsal-start-date'] ) ) {
		$filters['date_range']['start'] = trim( $form_data['wsal-start-date'] );
	}
	if ( isset( $form_data['wsal-end-date'] ) ) {
		$filters['date_range']['end'] = trim( $form_data['wsal-end-date'] );
	}
	// endregion >>>> By Date Range(s)
	// Region >>>> Reporting Format.
	if ( isset( $form_data['wsal-rb-report-type'] ) ) {
		if ( $form_data['wsal-rb-report-type'] == $wsal_common::REPORT_HTML ) {
			$filters['report_format'] = $wsal_common::REPORT_HTML;
		} elseif ( $form_data['wsal-rb-report-type'] == $wsal_common::REPORT_CSV ) {
			$filters['report_format'] = $wsal_common::REPORT_CSV;
		} else {
			?>
			<div class="error"><p><?php _e( 'Please select the report format.', 'reports-wsal' ); ?></p></div>
			<?php
		}
	} else {
		?>
		<div class="error"><p><?php echo esc_html( $message_form_not_valid ); ?></p></div>
		<?php
	}
	// Endregion >>>> Reporting Format.
	if ( isset( $form_data['wsal-reporting-submit'] ) ) {
		// Button Generate Report Now.
		?>
		<script type="text/javascript">
			var filters = <?php echo json_encode( $filters ); ?>;
			jQuery(document).ready(function(){
				AjaxCheckArchiveMatch(filters);
				AjaxGenerateReport(filters);
			});
		</script>
		<div class="updated">
			<p id="ajax-response">
				<img src="<?php echo esc_url( WSAL_BASE_URL ) . 'extensions/reports'; ?>/css/loading.gif">
				<?php esc_html_e( ' Generating report. Please do not close this window', 'reports-wsal' ); ?>
				<span id="ajax-response-counter"></span>
			</p>
		</div>
		<?php
		/* Delete the JSON file if exist */
		$upload_dir = wp_upload_dir();
		$this->_uploadsDirPath = $upload_dir['basedir'] . '/wp-security-audit-log/reports/';
		$filename = $this->_uploadsDirPath . 'report-user' . get_current_user_id() . '.json';
		if ( file_exists( $filename ) ) {
			@unlink( $filename );
		}
	} elseif ( isset( $form_data['wsal-periodic'] ) ) {
		// Buttons Configure Periodic Reports.
		$filters['frequency'] = $form_data['wsal-periodic'];
		if ( isset( $form_data['wsal-notif-email'] ) && isset( $form_data['wsal-notif-name'] ) ) {
			$filters['email'] = '';
			$arr_emails = explode( ',', $form_data['wsal-notif-email'] );
			foreach ( $arr_emails as $email ) {
				$filters['email'] .= filter_var( trim( $email ), FILTER_SANITIZE_EMAIL ) . ',';
			}
			$filters['email'] = rtrim( $filters['email'], ',' );
			$filters['name'] = filter_var( trim( $form_data['wsal-notif-name'] ), FILTER_SANITIZE_STRING );
			// By Criteria.
			if ( isset( $form_data['unique_ip'] ) ) {
				$filters['unique_ip'] = true;
			}
			if ( isset( $form_data['number_logins'] ) ) {
				$filters['number_logins'] = true;
			}
			$this->SavePeriodicReport( $filters );
			?>
			<div class="updated">
				<p><?php esc_html_e( 'Periodic Report successfully saved.', 'reports-wsal' ); ?></p>
			</div>
			<?php
		}
	}
}
// Send Now Periodic Report button.
if ( 'POST' == $rm && isset( $_POST['report-send-now'] ) ) {
	if ( isset( $_POST['report-name'] ) ) {
		$report_name = str_replace( 'wsal-', '', $_POST['report-name'] );
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				AjaxSendPeriodicReport( "<?php echo $report_name; ?>" );
			});
		</script>
		<div class="updated">
			<?php $plugin_path = plugins_url( basename( realpath( dirname( __FILE__ ) . '/../' ) ) ); ?>
			<p id="ajax-response">
				<img src="<?php echo esc_url( $plugin_path ); ?>/css/loading.gif">
				<?php esc_html_e( ' Generating report. Please do not close this window', 'reports-wsal' ); ?>
				<span id="ajax-response-counter"></span>
			</p>
		</div>
		<?php
	}
}

// Modify Periodic Report button.
if ( 'POST' == $rm && isset( $_POST['report-modify'] ) ) {
	if ( isset( $_POST['report-name'] ) ) {
		$report_name = str_replace( 'wsal-', '', $_POST['report-name'] );
		$current_report = $wsal_common->GetOptionByName( $report_name );
	}
}

// Delete Periodic Report button.
if ( 'POST' == $rm && isset( $_POST['report-delete'] ) ) {
	if ( isset( $_POST['report-name'] ) ) {
		$wsal_common->DeleteGlobalOption( $_POST['report-name'] );
		?>
		<div class="updated">
			<p><?php esc_html_e( 'Periodic Report successfully Deleted.', 'reports-wsal' ); ?></p>
		</div>
		<?php
	}
}

if ( 'POST' == $rm && isset( $_POST['wsal-statistics-submit'] ) ) {
	if ( isset( $_POST['wsal-summary-type'] ) ) {
		if ( $_POST['wsal-summary-type'] == $wsal_common::REPORT_HTML ) {
			$filters['report_format'] = $wsal_common::REPORT_HTML;
		} else {
			$filters['report_format'] = $wsal_common::REPORT_CSV;
		}
	}
	// Statistics report generator.
	$this->generateStatisticsReport( $filters );
}
?>
<style type="text/css">
	#wsal-rep-container label input[type="checkbox"]+span {
		margin-left: 3px;
	}
	#wsal-rep-container #label-xps:after {
		content: ' ';
		display:block;
		clear: both;
		margin-top: 3px;
	}
</style>
<div id="wsal-rep-container">
	<h2 id="wsal-tabs" class="nav-tab-wrapper">
		<a href="#tab-reports" class="nav-tab"><?php esc_html_e( 'Generate & Configure Periodic Reports', 'reports-wsal' ); ?></a>
		<!-- <a href="#tab-archives" class="nav-tab"><?php // esc_html_e( 'Generate Archives', 'reports-wsal' ); ?></a> -->
		<a href="#tab-summary" class="nav-tab"><?php esc_html_e( 'Statistics Reports', 'reports-wsal' ); ?></a>
	</h2>
	<div class="nav-tabs">
		<div class="wsal-tab wrap" id="tab-reports">
			<p style="clear:both; margin-top: 30px"></p>

			<form id="wsal-rep-form" action="<?php echo esc_url( $this->GetUrl() ); ?>" method="post">
				<h4><?php esc_html_e( 'Generate a report', 'reports-wsal' ); ?></h4>

			<!-- SECTION #1 -->
				<h4 class="wsal-reporting-subheading"><?php esc_html_e( 'Step 1: Select the type of report', 'reports-wsal' ); ?></h4>

				<div class="wsal-rep-form-wrapper">

					<!--// BY SITE -->
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl"><?php esc_html_e( 'By Site(s)', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<p class="wsal-rep-clear">
								<input type="radio" name="wsal-rb-sites" id="wsal-rb-sites-1" value="1" checked="checked" />
								<label for="wsal-rb-sites-1"><?php esc_html_e( 'All Sites', 'reports-wsal' ); ?></label>
							</p>
							<p class="wsal-rep-clear">
								<input type="radio" name="wsal-rb-sites" id="wsal-rb-sites-2" value="2"/>
								<label for="wsal-rb-sites-2"><?php esc_html_e( 'Specify sites', 'reports-wsal' ); ?></label>
								<input type="hidden" name="wsal-rep-sites" id="wsal-rep-sites"/>
							</p>
						</div>
					</div>

					<!--// BY USER -->
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl"><?php esc_html_e( 'By User(s)', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<p class="wsal-rep-clear">
								<input type="radio" name="wsal-rb-users" id="wsal-rb-users-1" value="1" checked="checked" />
								<label for="wsal-rb-users-1"><?php esc_html_e( 'All Users', 'reports-wsal' ); ?></label>
							</p>
							<p class="wsal-rep-clear">
								<input type="radio" name="wsal-rb-users" id="wsal-rb-users-2" value="2"/>
								<label for="wsal-rb-users-2"><?php esc_html_e( 'Specify users', 'reports-wsal' ); ?></label>
								<input type="hidden" name="wsal-rep-users" id="wsal-rep-users"/>
							</p>
						</div>
					</div>

					<!--// BY ROLE -->
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl"><?php esc_html_e( 'By Role(s)', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<p class="wsal-rep-clear">
								<input type="radio" name="wsal-rb-roles" id="wsal-rb-roles-1" value="1" checked="checked" />
								<label for="wsal-rb-roles-1"><?php esc_html_e( 'All Roles', 'reports-wsal' ); ?></label>
							</p>
							<p class="wsal-rep-clear">
								<input type="radio" name="wsal-rb-roles" id="wsal-rb-roles-2" value="2"/>
								<label for="wsal-rb-roles-2"><?php esc_html_e( 'Specify roles', 'reports-wsal' ); ?></label>
								<input type="hidden" name="wsal-rep-roles" id="wsal-rep-roles"/>
							</p>
						</div>
					</div>

					<!--// BY IP ADDRESS -->
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl"><?php esc_html_e( 'By IP Address(es)', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<p class="wsal-rep-clear">
								<input type="radio" name="wsal-rb-ip-addresses" id="wsal-rb-ip-addresses-1" value="1" checked="checked" />
								<label for="wsal-rb-ip-addresses-1"><?php esc_html_e( 'All IP Addresses', 'reports-wsal' ); ?></label>
							</p>
							<p class="wsal-rep-clear">
								<input type="radio" name="wsal-rb-ip-addresses" id="wsal-rb-ip-addresses-2" value="2"/>
								<label for="wsal-rb-ip-addresses-2"><?php esc_html_e( 'Specify IP Addresses', 'reports-wsal' ); ?></label>
								<input type="hidden" name="wsal-rep-ip-addresses" id="wsal-rep-ip-addresses"/>
							</p>
						</div>
					</div>

					<!--// BY ALERT GROUPS/CODE -->
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl"><?php esc_html_e( 'By Alert Code(s)', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<p id="wsal-rep-js-groups" class="wsal-rep-clear">
								<?php
								$checked = array();
								if ( ! empty( $current_report ) ) {
									$checked = $current_report->viewState;
								}
								?>
								<!-- Select All -->
								<label for="wsal-rb-groups" class="wsal-rep-clear" id="label-xps">
									<input type="radio" name="wsal-rb-groups" id="wsal-rb-groups" value="0" <?php echo ( empty( $current_report ) || count( $checked ) == 15 ) ? ' checked="checked"' : false; ?> />
									<span style="margin-left: 0"><?php esc_html_e( 'Select All', 'reports-wsal' ); ?></span>
								</label>
								<!-- / Select All -->

								<?php
								if ( empty( $_alerts ) ) {
									echo '<span>' . esc_html__( 'No alerts were found', 'reports-wsal' ) . '</span>';
								} else {
									$arr_alerts = array_keys( $_alerts );
									foreach ( $arr_alerts as $i => $alert ) {
										$id = 'wsal-rb-alert-' . $i;
										$class = 'wsal-rb-alert-' . str_replace( ' ', '-', strtolower( $alert ) );
										echo '<label for="' . esc_attr( $id ) . '" class="wsal-rep-clear ' . esc_attr( $class ) . '">';
										echo '<input type="checkbox" name="wsal-rb-alerts[]" id="' . esc_attr( $id ) . '" class="wsal-js-groups"';
										if ( in_array( $alert, $checked ) && count( $checked ) < 15 ) {
											echo ' checked';
										}
										echo ' value="' . esc_attr( $alert ) . '"/>';
										echo '<span>' . esc_html( $alert ) . '</span>';
										echo '</label>';
										$i++;

										if ( 'content' === strtolower( $alert ) ) :
											?>
											<!-- Post Types -->
											<label for="wsal-rb-post-types" class="wsal-rep-clear" id="label-cpts">
												<input type="checkbox" name="wsal-rb-post-types" id="wsal-rb-post-types" class="wsal-js-groups" />
												<?php esc_html_e( 'Post Type', 'wp-security-audit-log' ); ?>
												<input type="hidden" name="wsal-rep-post-types" id="wsal-rep-post-types"/>
											</label>
											<!-- / Post Types -->

											<!-- Post Statuses -->
											<label for="wsal-rb-post-status" class="wsal-rep-clear" id="label-statuses">
												<input type="checkbox" name="wsal-rb-post-status" id="wsal-rb-post-status" class="wsal-js-groups" />
												<?php esc_html_e( 'Post Status', 'wp-security-audit-log' ); ?>
												<input type="hidden" name="wsal-rep-post-status" id="wsal-rep-post-status"/>
											</label>
											<!-- / Post Statuses -->
											<?php
										endif;
									}
								}
								?>
								<input type="checkbox" name="wsal-rb-alert-codes" id="wsal-rb-alert-codes-1"/>
								<label for="wsal-rb-alert-codes-1"><?php esc_html_e( 'Specify Alert Codes', 'reports-wsal' ); ?></label>
								<input type="hidden" name="wsal-rep-alert-codes" id="wsal-rep-alert-codes"/>
							</p>
						</div>
					</div>

					<!--// By the Below Criteria -->
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl"><?php _e( 'By the Below Criteria', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							 <p class="wsal-rep-clear">
								<br/>
								<label for="unique_ip">
									<input type="checkbox" name="unique_ip" id="unique_ip" class="wsal-criteria" <?php echo ( in_array( 'unique_ip', $checked ) ) ? 'checked' : false; ?> value="1">
								   <span><?php _e( 'Number & List of unique IP addresses per user', 'reports-wsal' ); ?></span>
								</label>
								<br/>
								<label for="number_logins">
									<input type="checkbox" name="number_logins" id="number_logins" class="wsal-criteria" <?php echo ( in_array( 'number_logins', $checked ) ) ? 'checked' : false; ?> value="1">
									<span><?php _e( 'Number of Logins per user', 'reports-wsal' ); ?></span>
								</label>
								<br/>
							</p>
						</div>
					</div>
				</div>
				<script id="wpsal_rep_s2" type="text/javascript">
					jQuery( document ).ready( function( $ ) {

						// Toggle Post Type and Post Status.
						var content_filter_toggle = function() {
							var cpt_filter = $( '#label-cpts' );
							var status_filter = $( '#label-statuses' );

							cpt_filter.hide();
							status_filter.hide();

							if ( $( '.wsal-rb-alert-content input' ).is( ':checked' ) ) {
								cpt_filter.show();
								status_filter.show();
							}
						}
						content_filter_toggle();

						// Toggle post type and status filters visibility.
						$( '.wsal-rb-alert-content input' ).on( 'change', function() {
							content_filter_toggle();
						} );

						// Alert groups
						var wsalAlertGroups = $('.wsal-js-groups');
						$("#wsal-rep-sites").select2({
							data: JSON.parse('<?php echo $wsal_rep_sites; ?>'),
							placeholder: "<?php _e( 'Select site(s)' ); ?>",
							minimumResultsForSearch: 10,
							multiple: true
						}).on('select2-open',function(e){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-sites-2').prop('checked', true);
							}
						}).on('select2-removed', function(){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-sites-1').prop('checked',true);
							}
						}).on('select2-close', function(){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-sites-1').prop('checked',true);
							}
						});

						$("#wsal-rep-users").select2({
							placeholder: "<?php _e( 'Select user(s)' ); ?>",
							multiple: true,
							ajax: {
								url: ajaxurl + '?action=AjaxGetUserID',
								dataType: 'json',
								type: "GET",
								data: function (term) {
									return {
										term: term
									};
								},
								results: function (data) {
									return {
										results: $.map(data, function (item) {
											return {
												text: item.name,
												id: item.id
											}
										})
									};
								}
							}
						}).on('select2-open',function(e){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-users-2').prop('checked', true);
							}
						}).on('select2-removed', function(){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-users-1').prop('checked',true);
							}
						}).on('select2-close', function(){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-users-1').prop('checked',true);
							}
						});
						$("#wsal-rep-roles").select2({
							data: JSON.parse('<?php echo $wsal_rep_roles; ?>'),
							placeholder: "<?php _e( 'Select role(s)' ); ?>",
							minimumResultsForSearch: 10,
							multiple: true
						}).on('select2-open',function(e){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-roles-2').prop('checked', true);
							}
						}).on('select2-removed', function(){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-roles-1').prop('checked',true);
							}
						}).on('select2-close', function(){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-roles-1').prop('checked',true);
							}
						});

						$("#wsal-rep-ip-addresses").select2({
							data: JSON.parse('<?php echo $wsal_rep_ips; ?>'),
							placeholder: "<?php _e( 'Select IP address(es)' ); ?>",
							minimumResultsForSearch: 10,
							multiple: true
						}).on('select2-open',function(e){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-ip-addresses-2').prop('checked', true);
							}
						}).on('select2-removed', function(){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-ip-addresses-1').prop('checked',true);
							}
						}).on('select2-close', function(){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-ip-addresses-1').prop('checked',true);
							}
						});

						$("#wsal-rep-alert-codes").select2({
							data: <?php echo $wsal_rep_alert_groups; ?>,
							placeholder: "<?php _e( 'Select Alert Code(s)' ); ?>",
							minimumResultsForSearch: 10,
							multiple: true,
							width: '500px'
						}).on('select2-open', function(e){
							var v = $(e).val;
							if(v.length){
								$('#wsal-rb-alert-codes-1').prop('checked', true);
								$('#wsal-rb-groups').prop('checked', false);
							}
						}).on('select2-selecting', function(e){
							var v = $(e).val;
							if(v.length){
								$('#wsal-rb-alert-codes-1').prop('checked', true);
								$('#wsal-rb-groups').prop('checked', false);
							}
						}).on('select2-removed', function(e){
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-alert-codes-1').prop('checked', false);
								// if none is checked, check the Select All input
								var checked = $('.wsal-js-groups:checked');
								if(!checked.length){
									$('#wsal-rb-groups').prop('checked', true);
								}
							}
						});

						$( '#wsal-rep-post-types' ).select2( {
							data: <?php echo $wsal_rep_post_types; ?>,
							placeholder: "<?php esc_html_e( 'Select Post Type(s)' ); ?>",
							minimumResultsForSearch: 10,
							multiple: true,
							width: '500px',
						} ).on( 'select2-open', function( e ) {
							var v = $(e).val;
							if ( v.length ) {
								$('#wsal-rb-post-types').prop('checked', true);
								$('#wsal-rb-groups').prop('checked', false);
							}
						} ).on( 'select2-selecting', function( e ) {
							var v = $(e).val;
							if(v.length){
								$('#wsal-rb-post-types').prop('checked', true);
								$('#wsal-rb-groups').prop('checked', false);
							}
						} ).on( 'select2-removed', function( e ) {
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-post-types').prop('checked', false);
								// if none is checked, check the Select All input
								var checked = $('.wsal-js-groups:checked');
								if(!checked.length){
									$('#wsal-rb-groups').prop('checked', true);
								}
							}
						} );

						$( '#wsal-rep-post-status' ).select2( {
							data: <?php echo $wsal_rep_post_statuses; ?>,
							placeholder: "<?php esc_html_e( 'Select Post Status(es)' ); ?>",
							minimumResultsForSearch: 10,
							multiple: true,
							width: '500px',
						} ).on( 'select2-open', function( e ) {
							var v = $(e).val;
							if ( v.length ) {
								$('#wsal-rb-post-status').prop('checked', true);
								$('#wsal-rb-groups').prop('checked', false);
							}
						} ).on( 'select2-selecting', function( e ) {
							var v = $(e).val;
							if(v.length){
								$('#wsal-rb-post-status').prop('checked', true);
								$('#wsal-rb-groups').prop('checked', false);
							}
						} ).on( 'select2-removed', function( e ) {
							var v = $(this).val();
							if(!v.length){
								$('#wsal-rb-post-status').prop('checked', false);
								// if none is checked, check the Select All input
								var checked = $('.wsal-js-groups:checked');
								if(!checked.length){
									$('#wsal-rb-groups').prop('checked', true);
								}
							}
						} );

						function _deselectGroups(){
							wsalAlertGroups.each(function(){
								$(this).prop('checked', false);
							});
						}
						$('#wsal-rb-groups').on('change', function(){
							if ($(this).is(':checked')) {
								// deselect all
								_deselectGroups();
								// Deselect the alert codes checkbox if selected and no alert codes are provided.
								if ( $( '#wsal-rb-alert-codes-1' ).is( ':checked' ) ) {
									if ( ! $( '#wsal-rep-alert-codes' ).val().length ) {
										$( '#wsal-rb-alert-codes-1' ).prop( 'checked', false );
									}
								}
								if ( $( '#wsal-rb-post-types' ).is( ':checked' ) ) {
									if ( ! $( '#wsal-rep-post-types' ).val().length ) {
										$( '#wsal-rb-post-types' ).prop( 'checked', false );
									}
								}

								if ( $( '#wsal-rb-post-status' ).is( ':checked' ) ) {
									if ( ! $( '#wsal-rep-post-status' ).val().length ) {
										$( '#wsal-rb-post-status' ).prop( 'checked', false );
									}
								}
							} else {
								$(this).prop('checked', false);
								// select first
								$('.wsal-js-groups').get(0).prop('checked', true);
							}
						});
						$('#wsal-rb-alert-codes-1').on('change', function(){
							if ($(this).prop('checked') == true) {
								$('#wsal-rb-groups').prop('checked', false);
							} else {
								// if none is checked, check the Select All input
								var checked = $('.wsal-js-groups:checked');
								if(!checked.length){
									$('#wsal-rb-groups').prop('checked', true);
								}
							}
						});
						$( '#wsal-rb-post-types' ).on( 'change', function() {
							if ( $( this ).prop( 'checked' ) == true ) {
								$( '#wsal-rb-groups' ).prop( 'checked', false );
							} else {
								// If none is checked, check the Select All input.
								var checked = $( '.wsal-js-groups:checked' );
								if ( ! checked.length ) {
									$( '#wsal-rb-groups' ).prop( 'checked', true );
								}
							}
						} );
						$( '#wsal-rb-post-status' ).on( 'change', function() {
							if ( $( this ).prop( 'checked' ) == true ) {
								$( '#wsal-rb-groups' ).prop( 'checked', false );
							} else {
								// If none is checked, check the Select All input.
								var checked = $( '.wsal-js-groups:checked' );
								if ( ! checked.length ) {
									$( '#wsal-rb-groups' ).prop( 'checked', true );
								}
							}
						} );
						wsalAlertGroups.on( 'change', function() {
							if ( $( this ).is( ':checked' ) ) {
								$( '#wsal-rb-groups' ).prop( 'checked', false );
							} else {
								// If none is checked, check the Select All input.
								var checked = $( '.wsal-js-groups:checked' );
								var post_type_check = $( '#wsal-rb-post-types:checked' );
								var post_status_check = $( '#wsal-rb-post-status:checked' );
								if ( ! checked.length && ! post_type_check && ! post_status_check ) {
									$( '#wsal-rb-groups' ).prop( 'checked', true );
									var e = $( "#wsal-rep-alert-codes" ).select2( 'val' );
									var post_types = $( '#wsal-rep-post-types' ).select2( 'val' );
									var post_status = $( '#wsal-rep-post-status' ).select2( 'val' );
									if ( ! e.length ) {
										$( '#wsal-rb-alert-codes-1' ).prop( 'checked', false );
									}
									if ( ! post_types.length ) {
										$( '#wsal-rb-post-types' ).prop( 'checked', false );
									}
									if ( ! post_status.length ) {
										$( '#wsal-rb-post-status' ).prop( 'checked', false );
									}
								}
							}
						});
						// Validation date format
						$('.date-range').on('change', function(){
							if (checkDate($(this))) {
								jQuery(this).css('border-color', '#aaa');
							} else {
								jQuery(this).css('border-color', '#dd3d36');
							}
						});
						// Criteria disables all the alert codes
						function _disableGroups(){
							var checked = $('.wsal-criteria:checked');
							if(checked.length){
								$('#wsal-rep-js-groups').find('input').each(function(){
									$(this).attr('disabled', true);
								});
							} else {
								$('#wsal-rep-js-groups').find('input').each(function(){
									$(this).attr('disabled', false);
								});
							}
						}

						_disableGroups();
						// By Criteria changes
						$('.wsal-criteria').on('change', function(){
							if ($(this).is(':checked')) {
								$('#wsal-rb-groups').prop('checked', false);
								// deselect all
								_deselectGroups();
							}
							_disableGroups();
							// Allows to select only one
							$('input[type="checkbox"]').not(this).prop('checked', false);
						});

						<?php
						// Set the the values for the Select2.
						if ( ! empty( $current_report ) && ! empty( $current_report->sites ) ) {
							$sSites = '[';
							foreach ( $current_report->sites as $site ) {
								$sSites .= $site . ',';
							}
							$sSites = rtrim( $sSites, ',' );
							$sSites .= ']';
							?>
							$("#wsal-rep-sites").select2("val", <?php echo $sSites; ?>);
							$('#wsal-rb-sites-2').prop('checked', true);
							<?php
						}
						if ( ! empty( $current_report ) && ! empty( $current_report->users ) ) {
							$sUsers = '[';
							foreach ( $current_report->users as $user_id ) {
								$user = get_user_by( 'ID', $user_id );
								if ( $user ) {
									$sUsers .= '{id: ' . $user->ID . ', text: "' . $user->user_login . '"},';
								}
							}
							$sUsers = rtrim( $sUsers, ',' );
							$sUsers .= ']';
							?>
							$("#wsal-rep-users").select2('data', <?php echo $sUsers; ?>);
							$('#wsal-rb-users-2').prop('checked', true);
							<?php
						}
						if ( ! empty( $current_report ) && ! empty( $current_report->roles ) ) {
							$sRoles = '[';
							foreach ( $current_report->roles as $role ) {
								$sRoles .= '"' . $role . '",';
							}
							$sRoles = rtrim( $sRoles, ',' );
							$sRoles .= ']';
							?>
							$("#wsal-rep-roles").select2("val", <?php echo $sRoles; ?>);
							$('#wsal-rb-roles-2').prop('checked', true);
							<?php
						}
						if ( ! empty( $current_report ) && ! empty( $current_report->ipAddresses ) ) {
							$sIPs = '[';
							foreach ( $current_report->ipAddresses as $ip ) {
								$sIPs .= '"' . $ip . '",';
							}
							$sIPs = rtrim( $sIPs, ',' );
							$sIPs .= ']';
							?>
							$("#wsal-rep-ip-addresses").select2("val", <?php echo $sIPs; ?>);
							$('#wsal-rb-ip-addresses-2').prop('checked', true);
							<?php
						}
						if ( ! empty( $current_report ) && ! empty( $current_report->viewState ) ) {
							$arr_alerts = array();
							$post_type_alerts = array();
							$post_status_alerts = array();

							// Extract selected alerts or post types in the current report.
							foreach ( $current_report->viewState as $key => $state ) {
								if ( $state == 'codes' ) {
									$arr_alerts = $current_report->triggers[ $key ]['alert_id'];
								}
								if ( 'post_types' === $state ) {
									$post_type_alerts = $current_report->triggers[ $key ]['post_types'];
								}
								if ( 'Blog Posts' === $state ) {
									$post_type_alerts[] = 'post';
								}
								if ( 'Pages' === $state ) {
									$post_type_alerts[] = 'page';
								}
								if ( 'post_statuses' === $state ) {
									$post_status_alerts = $current_report->triggers[ $key ]['post_statuses'];
								}
							}

							// Selected alerts.
							$selected_alerts = '[';
							foreach ( $arr_alerts as $alert_id ) {
								$selected_alerts .= $alert_id . ',';
							}
							$selected_alerts = rtrim( $selected_alerts, ',' );
							$selected_alerts .= ']';

							// Selected post types.
							$selected_cpts = '[';
							foreach ( $post_type_alerts as $post_type ) {
								$selected_cpts .= '"' . $post_type . '",';
							}
							$selected_cpts = rtrim( $selected_cpts, ',' );
							$selected_cpts .= ']';

							// Selected post statuses.
							$selected_statuses = '[';
							foreach ( $post_status_alerts as $post_status ) {
								$selected_statuses .= '"' . $post_status . '",';
							}
							$selected_statuses = rtrim( $selected_statuses, ',' );
							$selected_statuses .= ']';

							if ( ! empty( $arr_alerts ) ) :
								?>
								// Add to select box.
								$("#wsal-rep-alert-codes").select2("val", <?php echo $selected_alerts; ?>);
								$('#wsal-rb-alert-codes-1').prop('checked', true);
								<?php
							endif;
							if ( ! empty( $post_type_alerts ) ) :
								?>
								$("#wsal-rep-post-types").select2("val", <?php echo $selected_cpts; ?>);
								$('#wsal-rb-post-types').prop('checked', true);
							<?php
							endif;
							if ( ! empty( $post_status_alerts ) ) :
								?>
								$("#wsal-rep-post-status").select2("val", <?php echo $selected_statuses; ?>);
								$('#wsal-rb-post-status').prop('checked', true);
							<?php
							endif;
						}
						?>
					});
				</script>

				<!-- SECTION #2 -->
				<?php $date_format = $wsal_common->GetDateFormat(); ?>
				<h4 class="wsal-reporting-subheading"><?php _e( 'Step 2: Select the date range', 'reports-wsal' ); ?></h4>

				<div class="wsal-note"><?php _e( 'Note: Do not specify any dates if you are creating a scheduled report or if you want to generate a report from when you started the audit trail.', 'reports-wsal' ); ?></div>

				<div class="wsal-rep-form-wrapper">
					<!--// BY DATE -->
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl label-datepick"><?php _e( 'Start Date', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<p class="wsal-rep-clear">
								<input type="text" class="date-range" id="wsal-start-date" name="wsal-start-date" placeholder="<?php _e( 'Select start date', 'reports-wsal' ); ?>"/>
								<span class="description"> (<?php echo $date_format; ?>)</span>
							</p>
						</div>
					</div>
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl label-datepick"><?php _e( 'End Date', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<p class="wsal-rep-clear">
								<input type="text" class="date-range" id="wsal-end-date" name="wsal-end-date" placeholder="<?php _e( 'Select end date', 'reports-wsal' ); ?>"/>
								<span class="description"> (<?php echo $date_format; ?>)</span>
							</p>
						</div>
					</div>
					<script type="text/javascript">
						jQuery(document).ready(function($){
							wsal_CreateDatePicker($, $('#wsal-start-date'), null);
							wsal_CreateDatePicker($, $('#wsal-end-date'), null);
						});
					</script>
				</div>

			<!-- SECTION #3 -->
				<h4 class="wsal-reporting-subheading"><?php _e( 'Step 3: Select Report Format', 'reports-wsal' ); ?></h4>

				<div class="wsal-rep-form-wrapper">
					<div class="wsal-rep-section">
						<div class="wsal-rep-section-fl">
							<p class="wsal-rep-clear">
								<input type="radio" name="wsal-rb-report-type" id="wsal-rb-type-1" value="<?php echo $wsal_common::REPORT_HTML; ?>" checked="checked" />
								<label for="wsal-rb-type-1"><?php _e( 'HTML', 'reports-wsal' ); ?></label>
							</p>
							<p class="wsal-rep-clear">
								<input type="radio" name="wsal-rb-report-type" id="wsal-rb-type-2" value="<?php echo $wsal_common::REPORT_CSV; ?>"
									<?php echo ( ! empty( $current_report ) && ($wsal_common::REPORT_CSV == $current_report->type) ) ? 'checked="checked"' : false ; ?> />
								<label for="wsal-rb-type-2"><?php _e( 'CSV', 'reports-wsal' ); ?></label>
							</p>
						</div>
					</div>
				</div>

			<!-- SECTION #4 -->
				<h4 class="wsal-reporting-subheading"><?php _e( 'Step 4: Generate Report Now or Configure Periodic Reports', 'reports-wsal' ); ?></h4>
				<div class="wsal-rep-form-wrapper">
					<div class="wsal-rep-section">
						<input type="submit" name="wsal-reporting-submit" id="wsal-reporting-submit" class="button-primary" value="<?php _e( 'Generate Report Now', 'reports-wsal' ); ?>">
					</div>
					<div class="wsal-rep-section">
						<span class="description"><?php _e( ' Use the buttons below to use the above criteria for a daily, weekly and monthly summary report which is sent automatically via email.', 'reports-wsal' ); ?></span>
					</div>
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl"><?php _e( 'Email address(es)', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<input type="text" id="wsal-notif-email" style="min-width:270px;border: 1px solid #aaa;" name="wsal-notif-email" placeholder="Email *" value="<?php echo ! empty( $current_report ) ? esc_html( $current_report->email ) : false; ?>">
						</div>
					</div>
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl"><?php _e( 'Report Name', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<input type="text" id="wsal-notif-name" style="min-width:270px;border: 1px solid #aaa;" name="wsal-notif-name" placeholder="Name" value="<?php echo ! empty( $current_report ) ? esc_html( $current_report->title ) : false; ?>">
						</div>
					</div>
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl"><?php _e( 'Frequency', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<input type="submit" name="wsal-periodic" class="button-primary" value="<?php _e( 'Daily', 'reports-wsal' ); ?>">
							<input type="submit" name="wsal-periodic" class="button-primary" value="<?php _e( 'Weekly', 'reports-wsal' ); ?>">
							<input type="submit" name="wsal-periodic" class="button-primary" value="<?php _e( 'Monthly', 'reports-wsal' ); ?>">
							<input type="submit" name="wsal-periodic" class="button-primary" value="<?php _e( 'Quarterly', 'reports-wsal' ); ?>">
						</div>
					</div>
				</div>

				<?php wp_nonce_field( 'wsal_reporting_view_action', 'wsal_reporting_view_field' ); ?>
			</form>

			<!-- SECTION Configured Periodic Reports -->
			<?php
			$periodic_reports = $wsal_common->GetPeriodicReports();
			if ( ! empty( $periodic_reports ) ) {
				?>
				<h4 class="wsal-reporting-subheading"><?php _e( 'Configured Periodic Reports', 'reports-wsal' ); ?></h4>
				<div class="wsal-rep-form-wrapper">
					<div class="wsal-rep-section">
						<span class="description"><?php esc_html_e( 'Below is the list of configured periodic reports. Click on Modify to load the criteria and configure it above. To save the new criteria as a new report change the report name and save it. Do not change the report name to overwrite the existing periodic report.', 'reports-wsal' ); ?></span>
						<br />
						<br />
						<span class="description"><?php esc_html_e( 'Note: Use the Send Now button to generate a report with data from the last 90 days if a quarterly report is configured, 30 days if monthly report is configured and 7 days if weekly report is configured.', 'reports-wsal' ); ?></span>
					</div>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr><th>Name</th><th>Email address(es)</th><th>Frequency</th><th></th><th></th><th></th></tr>
						</thead>
						<tbody>
							<?php
							foreach ( $periodic_reports as $key => $report ) {
								$arr_emails = explode( ',', $report->email );
								?>
								<tr>
									<form action="<?php echo $this->GetUrl(); ?>" method="post">
										<input type="hidden" name="report-name" value="<?php echo $key; ?>">
										<td><?php echo $report->title; ?></td>
										<td>
											<?php
											foreach ( $arr_emails as $email ) {
												echo $email . '<br>';
											}
											?>
										</td>
										<td><?php echo $report->frequency; ?></td>
										<td><input type="submit" name="report-send-now" class="button-secondary" value="Send Now"></td>
										<td><input type="submit" name="report-modify" class="button-secondary" value="Modify"></td>
										<td><input type="submit" name="report-delete" class="button-secondary" value="Delete"></td>
									</form>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			<?php } ?>
		</div>
		<!-- Tab Built-in Archives
		<div class="wsal-tab wrap" id="tab-archives">
		</div>-->
		<!-- Tab Built-in Summary-->
		<div class="wsal-tab wrap" id="tab-summary">
			<p style="clear:both; margin-top: 30px"></p>

			<form id="wsal-summary-form" method="post">
				<!-- SECTION #1 -->
				<?php
					$date_format = $wsal_common->GetDateFormat();
				?>
				<h4 class="wsal-reporting-subheading"><?php _e( 'Step 1: Choose Date Range', 'reports-wsal' ); ?></h4>
				<div class="wsal-rep-form-wrapper">
					<!--// BY DATE -->
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl label-datepick"><?php _e( 'From', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<p class="wsal-rep-clear">
								<input type="text" class="date-range" id="wsal-from-date" name="wsal-from-date" placeholder="<?php _e( 'Select start date', 'reports-wsal' ); ?>"/>
								<span class="description"> (<?php echo $date_format; ?>)</span>
							</p>
						</div>
					</div>
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl label-datepick"><?php _e( 'To', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<p class="wsal-rep-clear">
								<input type="text" class="date-range" id="wsal-to-date" name="wsal-to-date" placeholder="<?php _e( 'Select end date', 'reports-wsal' ); ?>"/>
								<span class="description"> (<?php echo $date_format; ?>)</span>
							</p>
						</div>
					</div>
					<script type="text/javascript">
						jQuery(document).ready(function($){
							wsal_CreateDatePicker($, $('#wsal-from-date'), null);
							wsal_CreateDatePicker($, $('#wsal-to-date'), null);
						});
					</script>
				</div>
				<!-- SECTION #2 -->
				<h4 class="wsal-reporting-subheading"><?php _e( 'Step 2: Choose Criteria', 'reports-wsal' ); ?></h4>
				<div class="wsal-rep-form-wrapper">
					<div class="wsal-rep-section">
						<label class="wsal-rep-label-fl"><?php _e( 'Report for', 'reports-wsal' ); ?></label>
						<div class="wsal-rep-section-fl">
							<fieldset>
								<label for="criteria_1">
									<input type="radio" name="wsal-criteria" id="criteria_1" style="margin-top: 2px;" value="<?php echo $wsal_common::LOGIN_BY_USER; ?>" checked="checked">
									<span class="name-criteria"><?php _e( 'Number of logins for user', 'reports-wsal' ); ?></span>
									<input type="hidden" name="wsal-summary-field_1" class="wsal-summary-users"/>
								</label><br><br>
								<label for="criteria_2">
									<input type="radio" name="wsal-criteria" id="criteria_2" style="margin-top: 2px;" value="<?php echo $wsal_common::LOGIN_BY_ROLE; ?>">
									<span class="name-criteria"><?php _e( 'Number of logins for users with the role of', 'reports-wsal' ); ?></span>
									<input type="hidden" name="wsal-summary-field_2" class="wsal-summary-roles"/>
								</label><br><br>
								<label for="criteria_3">
									<input type="radio" name="wsal-criteria" id="criteria_3" style="margin-top: 2px;" value="<?php echo $wsal_common::VIEWS_BY_USER; ?>">
									<span class="name-criteria"><?php _e( 'Number of views for user', 'reports-wsal' ); ?></span>
									<input type="hidden" name="wsal-summary-field_3" class="wsal-summary-users"/>
								</label><br><br>
								<label for="criteria_4">
									<input type="radio" name="wsal-criteria" id="criteria_4" style="margin-top: 2px;" value="<?php echo $wsal_common::VIEWS_BY_ROLE; ?>">
									<span class="name-criteria"><?php _e( 'Number of views for users with the role of', 'reports-wsal' ); ?></span>
									<input type="hidden" name="wsal-summary-field_4" class="wsal-summary-roles"/>
								</label><br><br>
								<label for="criteria_5">
									<input type="radio" name="wsal-criteria" id="criteria_5" style="margin-top: 2px;" value="<?php echo $wsal_common::PUBLISHED_BY_USER; ?>">
									<span class="name-criteria"><?php _e( 'Number of published content for user', 'reports-wsal' ); ?></span>
									<input type="hidden" name="wsal-summary-field_5" class="wsal-summary-users"/>
								</label><br><br>
								<label for="criteria_6">
									<input type="radio" name="wsal-criteria" id="criteria_6" style="margin-top: 2px;" value="<?php echo $wsal_common::PUBLISHED_BY_ROLE; ?>">
									<span class="name-criteria"><?php _e( 'Number of published content for users with the role of', 'reports-wsal' ); ?></span>
									<input type="hidden" name="wsal-summary-field_6" class="wsal-summary-roles"/>
								</label><br><br>
								<label for="criteria_7">
									<input type="radio" name="wsal-criteria" id="criteria_7" style="margin-top: 2px;" value="<?php echo $wsal_common::DIFFERENT_IP; ?>">
									<span><?php _e( 'Different IP addresses for Usernames', 'reports-wsal' ); ?></span>
								</label><br>
								<div class="sub-options">
									<label for="only_login">
										<input type="checkbox" name="only_login" id="only_login" style="margin: 2px;">
										<span><?php _e( 'List only IP addresses used during login', 'reports-wsal' ); ?></span>
									</label><br>
									<span class="description"><?php _e( 'If the above option is enabled the report will only include the IP addresses from where the user logged in. If it is disabled it will list all the IP addresses from where the plugin recorded activity originating from the user.', 'reports-wsal' ); ?></span>
								</div>
							</fieldset>
						</div>
					</div>
				</div>
				<!--// BY SITE -->
				<?php
				/*
				<div class="wsal-rep-section">
					<label class="wsal-rep-label-fl"><?php _e('By Site(s)', 'reports-wsal');?></label>
					<div class="wsal-rep-section-fl">
						<p class="wsal-rep-clear">
							<input type="radio" name="wsal-sum-sites" id="wsal-sum-sites-1" value="1" checked="checked">
							<label for="wsal-sum-sites-1"><?php _e('All Sites', 'reports-wsal');?></label>
						</p>
						<p class="wsal-rep-clear">
							<input type="radio" name="wsal-sum-sites" id="wsal-sum-sites-2" value="2">
							<label for="wsal-sum-sites-2"><?php _e('Specify sites', 'reports-wsal');?></label>
							<input type="hidden" name="wsal-summary-sites" id="wsal-summary-sites"/>
						</p>
					</div>
				</div>
				*/
				?>
				<!-- SECTION #3 -->
				<h4 class="wsal-reporting-subheading"><?php _e( 'Step 3: Select Report Format', 'reports-wsal' ); ?></h4>
				<div class="wsal-rep-form-wrapper">
					<div class="wsal-rep-section">
						<div class="wsal-rep-section-fl">
							<p class="wsal-rep-clear">
								<input type="radio" name="wsal-summary-type" id="wsal-summary-type-1" value="<?php echo $wsal_common::REPORT_HTML; ?>" checked="checked">
								<label for="wsal-summary-type-1"><?php _e( 'HTML', 'reports-wsal' ); ?></label>
							</p>
							<p class="wsal-rep-clear">
								<input type="radio" name="wsal-summary-type" id="wsal-summary-type-2" value="<?php echo $wsal_common::REPORT_CSV; ?>">
								<label for="wsal-summary-type-2"><?php _e( 'CSV', 'reports-wsal' ); ?></label>
							</p>
						</div>
					</div>
				</div>
				<div class="wsal-rep-form-wrapper">
					<div class="wsal-rep-section">
						<div class="wsal-rep-section-fl">
							<input type="submit" id="wsal-submit-now" name="wsal-statistics-submit" value="Generate Report" class="button-primary">
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#wsal-rep-form').on('submit', function(){
			//#! Sites
			var e = $('#wsal-rep-sites').val();
			if(!$('#wsal-rb-sites-1').is(':checked')){
				if(!e.length){
					alert("<?php _e( 'Please specify at least one site', 'reports-wsal' ); ?>");
					return false;
				}
			}

			//#! Users
			if(!$('#wsal-rb-users-1').is(':checked')){
				e = $('#wsal-rep-users').val();
				if(!e.length){
					alert("<?php _e( 'Please specify at least one user', 'reports-wsal' ); ?>");
					return false;
				}
			}

			//#! Roles
			if(!$('#wsal-rb-roles-1').is(':checked')){
				e = $('#wsal-rep-roles').val();
				if(!e.length){
					alert("<?php _e( 'Please specify at least one role', 'reports-wsal' ); ?>");
					return false;
				}
			}

			//#! IP addresses
			if(!$('#wsal-rb-ip-addresses-1').is(':checked')){
				e = $('#wsal-rep-ip-addresses').val();
				if(!e.length){
					alert("<?php _e( 'Please specify at least one IP address', 'reports-wsal' ); ?>");
					return false;
				}
			}

			//#! Alert groups
			if ( ( ! $( '#wsal-rb-groups' ).is( ':checked' ) && ! $( '.wsal-js-groups:checked' ).length ) ) {
				if ( ! $( '#wsal-rep-alert-codes' ).val().length ) {
					if ( ! $( '.wsal-criteria:checked' ).length ) {
						alert( "<?php esc_html_e( 'Please specify at least one Alert group or specify an Alert code', 'reports-wsal' ); ?>" );
						return false;
					}
				}
			}

			return true;
		});

		$("#wsal-summary-sites").select2({
			data: JSON.parse('<?php echo $wsal_rep_sites; ?>'),
			placeholder: "<?php _e( 'Select site(s)' ); ?>",
			minimumResultsForSearch: 10,
			multiple: true,
		}).on('select2-open',function(e){
			var v = $(this).val();
			if(!v.length){
				$('#wsal-sum-sites-2').prop('checked', true);
			}
		}).on('select2-removed', function(){
			var v = $(this).val();
			if(!v.length){
				$('#wsal-sum-sites-1').prop('checked',true);
			}
		}).on('select2-close', function(){
			var v = $(this).val();
			if(!v.length){
				$('#wsal-sum-sites-1').prop('checked',true);
			}
		});

		$(".wsal-summary-users").select2({
			placeholder: "<?php _e( 'Select user' ); ?>",
			multiple: false,
			ajax: {
				url: ajaxurl + '?action=AjaxGetUserID',
				dataType: 'json',
				type: "GET",
				data: function (term) {
					return {
						term: term
					};
				},
				results: function (data) {
					return {
						results: $.map(data, function (item) {
							return {
								text: item.name,
								id: item.id
							}
						})
					};
				}
			}
		});

		$(".wsal-summary-roles").select2({
			data: JSON.parse('<?php echo $wsal_rep_roles; ?>'),
			placeholder: "<?php _e( 'Select role' ); ?>",
			minimumResultsForSearch: 10,
			multiple: false
		});

		$('#wsal-summary-form').on('submit', function(){
			var sel = $("input[name='wsal-criteria']:checked").val();
			var field = $("input[name='wsal-summary-field_"+sel+"']").val();
			// field required
			if (field != '') {
				return true;
			} else {
				alert("Add User(s)/Role(s) for the report.");
				return false;
			}
		});
	});
</script>
