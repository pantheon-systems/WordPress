<?php
/**
 * Settings Page
 *
 * Settings page of the plugin.
 *
 * @since   1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enable/Disable Alerts Page.
 *
 * @package Wsal
 */
class WSAL_Views_ToggleAlerts extends WSAL_AbstractView {

	/**
	 * Method: Get View Title.
	 */
	public function GetTitle() {
		return __( 'Enable/Disable Alerts', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Icon.
	 */
	public function GetIcon() {
		return 'dashicons-forms';
	}

	/**
	 * Method: Get View Name.
	 */
	public function GetName() {
		return __( 'Enable/Disable Alerts', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Weight.
	 */
	public function GetWeight() {
		return 2;
	}

	/**
	 * Method: Get safe category name.
	 *
	 * @param string $name - Name of the category.
	 */
	protected function GetSafeCatgName( $name ) {
		return strtolower(
			preg_replace( '/[^A-Za-z0-9\-]/', '-', $name )
		);
	}

	/**
	 * Method: Get View.
	 */
	public function Render() {
		// Die if user does not have permission to view.
		if ( ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
		}

		$alert = new WSAL_Alert(); // IDE type hinting.
		$grouped_alerts = $this->_plugin->alerts->GetCategorizedAlerts();
		$safe_names = array_map( array( $this, 'GetSafeCatgName' ), array_keys( $grouped_alerts ) );
		$safe_names = array_combine( array_keys( $grouped_alerts ), $safe_names );

		// Filter $_POST array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['submit'] ) && isset( $post_array['alert'] ) ) {
			check_admin_referer( 'wsal-togglealerts' );
			try {
				$enabled = array_map( 'intval', $post_array['alert'] );
				$disabled = array();
				foreach ( $this->_plugin->alerts->GetAlerts() as $alert ) {
					if ( ! in_array( $alert->type, $enabled ) ) {
						$disabled[] = $alert->type;
					}
				}
				$this->_plugin->alerts->SetDisabledAlerts( $disabled );
				?>
				<div class="updated">
					<p><?php esc_html_e( 'Settings have been saved.', 'wp-security-audit-log' ); ?></p>
				</div>
				<?php
			} catch ( Exception $ex ) {
				?>
				<div class="error">
					<p><?php esc_html_e( 'Error: ', 'wp-security-audit-log' ); ?><?php echo esc_html( $ex->getMessage() ); ?></p>
				</div>
				<?php
			}
			$this->_plugin->SetGlobalOption( 'log-404', isset( $post_array['log_404'] ) ? 'on' : 'off' );
			$this->_plugin->SetGlobalOption( 'purge-404-log', isset( $post_array['purge_log'] ) ? 'on' : 'off' );
			$this->_plugin->SetGlobalOption( 'log-404-referrer', isset( $post_array['log_404_referrer'] ) ? 'on' : 'off' );

			$this->_plugin->SetGlobalOption( 'log-visitor-404', isset( $post_array['log_visitor_404'] ) ? 'on' : 'off' );
			$this->_plugin->SetGlobalOption( 'purge-visitor-404-log', isset( $post_array['purge_visitor_log'] ) ? 'on' : 'off' );
			$this->_plugin->SetGlobalOption( 'log-visitor-404-referrer', isset( $post_array['log_visitor_404_referrer'] ) ? 'on' : 'off' );

			$this->_plugin->settings->Set404LogLimit( $post_array['user_404Limit'] );
			$this->_plugin->settings->SetVisitor404LogLimit( $post_array['visitor_404Limit'] );

			$this->_plugin->settings->set_failed_login_limit( $post_array['log_failed_login_limit'] );
			$this->_plugin->settings->set_visitor_failed_login_limit( $post_array['log_visitor_failed_login_limit'] );
		}
		?>
		<h2 id="wsal-tabs" class="nav-tab-wrapper">
			<?php foreach ( $safe_names as $name => $safe ) : ?>
				<a href="#tab-<?php echo esc_attr( $safe ); ?>" class="nav-tab"><?php echo esc_html( $name ); ?></a>
			<?php endforeach; ?>
		</h2>
		<form id="audit-log-viewer" method="post">
			<input type="hidden" name="page" value="<?php echo filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ); ?>" />
			<?php wp_nonce_field( 'wsal-togglealerts' ); ?>

			<div class="nav-tabs">
				<?php foreach ( $grouped_alerts as $name => $group ) : ?>
					<div class="wsal-tab" id="tab-<?php echo esc_attr( $safe_names[ $name ] ); ?>">
						<h2 class="nav-tab-wrapper wsal-sub-tabs">
							<?php
							foreach ( $group as $subname => $alerts ) :
								$tab_id = $this->GetSafeCatgName( $subname );

								// Skip Pages and CPTs section.
								if ( 'custom-post-types' === $tab_id || 'pages' === $tab_id ) {
									continue;
								}
								?>
								<a href="#tab-<?php echo esc_attr( $tab_id ); ?>"
									class="nav-tab"
									data-parent="tab-<?php echo esc_attr( $safe_names[ $name ] ); ?>">
									<?php echo esc_html( $subname ); ?>
								</a>
							<?php endforeach; ?>
						</h2>
						<?php
						foreach ( $group as $subname => $alerts ) {
							$active = array();
							$allactive = true;
							foreach ( $alerts as $alert ) {
								if ( $alert->type <= 0006 ) {
									continue; // <- Ignore php alerts.
								}
								if ( 9999 === $alert->type ) {
									continue; // <- Ignore promo alerts.
								}
								$active[ $alert->type ] = $this->_plugin->alerts->IsEnabled( $alert->type );
								if ( ! $active[ $alert->type ] ) {
									$allactive = false;
								}
							}

							$tab_id = $this->GetSafeCatgName( $subname );

							// Skip Pages and CPTs section.
							if ( 'custom-post-types' === $tab_id || 'pages' === $tab_id ) {
								continue;
							}
							?>
							<table class="wp-list-table wsal-tab widefat fixed wsal-sub-tab" cellspacing="0" id="tab-<?php echo esc_attr( $this->GetSafeCatgName( $subname ) ); ?>">
								<thead>
									<tr>
										<th width="48"><input type="checkbox" <?php checked( $allactive ); ?> /></th>
										<th width="80"><?php esc_html_e( 'Code', 'wp-security-audit-log' ); ?></th>
										<th width="100"><?php esc_html_e( 'Type', 'wp-security-audit-log' ); ?></th>
										<th><?php esc_html_e( 'Description', 'wp-security-audit-log' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ( $alerts as $alert ) {
										if ( $alert->type <= 0006 ) {
											continue; // <- Ignore php alerts.
										}
										if ( 9999 === $alert->type ) {
											continue; // <- Ignore promo alerts.
										}
										$attrs = '';
										switch ( true ) {
											case ! $alert->mesg:
												$attrs = 'title="' . __( 'Not Implemented', 'wp-security-audit-log' ) . '" class="alert-incomplete"';
												break;
											case false:
												$attrs = 'title="' . __( 'Not Available', 'wp-security-audit-log' ) . '" class="alert-unavailable"';
												break;
										}
										?>
										<tr <?php echo esc_attr( $attrs ); ?>>
											<th>
												<input name="alert[]" type="checkbox"
													<?php checked( $active[ $alert->type ] ); ?>
													value="<?php echo (int) $alert->type; ?>">
											</th>
											<td><?php echo esc_html( str_pad( $alert->type, 4, '0', STR_PAD_LEFT ) ); ?></td>
											<td><?php echo esc_html( $this->_plugin->constants->GetConstantBy( 'value', $alert->code )->name ); ?></td>
											<td><?php echo esc_html( $alert->desc ); ?></td>
										</tr>
										<?php
										if ( 6007 === $alert->type ) {
											$log_404 = $this->_plugin->GetGlobalOption( 'log-404' );
											$purge_log = $this->_plugin->GetGlobalOption( 'purge-404-log' );
											$log_404_referrer = $this->_plugin->GetGlobalOption( 'log-404-referrer', 'on' );
											?>
											<tr>
												<td></td>
												<td>
													<input name="log_404" type="checkbox" class="check_log" value="1"
														<?php checked( $log_404, 'on' ); ?> />
												</td>
												<td colspan="2"><?php esc_html_e( 'Capture 404 requests to file (the log file are created in the /wp-content/uploads/wp-security-audit-log/404s/ directory)', 'wp-security-audit-log' ); ?></td>
											</tr>
											<tr>
												<td></td>
												<td>
													<input name="purge_log" type="checkbox" class="check_log" value="1"
														<?php checked( $purge_log, 'on' ); ?> />
												</td>
												<td colspan="2"><?php esc_html_e( 'Purge log files older than one month', 'wp-security-audit-log' ); ?></td>
											</tr>
											<tr>
												<td></td>
												<td colspan="1"><input type="number" id="user_404Limit" name="user_404Limit" value="<?php echo esc_attr( $this->_plugin->settings->Get404LogLimit() ); ?>" /></td>
												<td colspan="2"><?php esc_html_e( 'Number of 404 Requests to Log. By default the plugin keeps up to 99 requests to non-existing pages from the same IP address. Increase the value in this setting to the desired amount to keep a log of more or less requests.', 'wp-security-audit-log' ); ?></td>
											</tr>
											<tr>
												<td></td>
												<td><input name="log_404_referrer" type="checkbox" class="check_log" value="1" <?php checked( $log_404_referrer, 'on' ); ?>></td>
												<td colspan="2"><?php esc_html_e( 'Record the referrer that generated the 404 error.', 'wp-security-audit-log' ); ?></td>
											</tr>
											<?php
										}
										if ( 6023 === $alert->type ) {
											$log_visitor_404 = $this->_plugin->GetGlobalOption( 'log-visitor-404' );
											$purge_visitor_log = $this->_plugin->GetGlobalOption( 'purge-visitor-404-log' );
											$log_visitor_404_referrer = $this->_plugin->GetGlobalOption( 'log-visitor-404-referrer', 'on' );
											?>
											<tr>
												<td></td>
												<td>
													<input name="log_visitor_404" type="checkbox" class="check_visitor_log" value="1"
														<?php checked( $log_visitor_404, 'on' ); ?> />
												</td>
												<td colspan="2"><?php esc_html_e( 'Capture 404 requests to file (the log file are created in the /wp-content/uploads/wp-security-audit-log/404s/ directory)', 'wp-security-audit-log' ); ?></td>
											</tr>
											<tr>
												<td></td>
												<td>
													<input name="purge_visitor_log" type="checkbox" class="check_visitor_log" value="1"
														<?php checked( $purge_visitor_log, 'on' ); ?> />
												</td>
												<td colspan="2"><?php esc_html_e( 'Purge log files older than one month', 'wp-security-audit-log' ); ?></td>
											</tr>
											<tr>
												<td></td>
												<td colspan="1"><input type="number" id="visitor_404Limit" name="visitor_404Limit" value="<?php echo esc_attr( $this->_plugin->settings->GetVisitor404LogLimit() ); ?>" /></td>
												<td colspan="2"><?php esc_html_e( 'Number of 404 Requests to Log. By default the plugin keeps up to 99 requests to non-existing pages from the same IP address. Increase the value in this setting to the desired amount to keep a log of more or less requests. Note that by increasing this value to a high number, should your website be scanned the plugin will consume more resources to log all the requests.', 'wp-security-audit-log' ); ?></td>
											</tr>
											<tr>
												<td></td>
												<td><input name="log_visitor_404_referrer" type="checkbox" class="check_log" value="1" <?php checked( $log_visitor_404_referrer, 'on' ); ?>></td>
												<td colspan="2"><?php esc_html_e( 'Record the referrer that generated the 404 error.', 'wp-security-audit-log' ); ?></td>
											</tr>
											<?php
										}
										if ( 1002 === $alert->type ) {
											$log_failed_login_limit = (int) $this->_plugin->GetGlobalOption( 'log-failed-login-limit', 10 );
											$log_failed_login_limit = ( -1 === $log_failed_login_limit ) ? '0' : $log_failed_login_limit;
											?>
											<tr>
												<td></td>
												<td><input name="log_failed_login_limit" type="number" class="check_visitor_log" value="<?php echo esc_attr( $log_failed_login_limit ); ?>"></td>
												<td colspan="2">
													<?php esc_html_e( 'Number of login attempts to log. Enter 0 to log all failed login attempts. (By default the plugin only logs up to 10 failed login because the process can be very resource intensive in case of a brute force attack)', 'wp-security-audit-log' ); ?>
												</td>
											</tr>
											<?php
										}
										if ( 1003 === $alert->type ) {
											$log_visitor_failed_login_limit = (int) $this->_plugin->GetGlobalOption( 'log-visitor-failed-login-limit', 10 );
											$log_visitor_failed_login_limit = ( -1 === $log_visitor_failed_login_limit ) ? '0' : $log_visitor_failed_login_limit;
											?>
											<tr>
												<td></td>
												<td><input name="log_visitor_failed_login_limit" type="number" class="check_visitor_log" value="<?php echo esc_attr( $log_visitor_failed_login_limit ); ?>"></td>
												<td colspan="2">
													<p><?php esc_html_e( 'Number of login attempts to log. Enter 0 to log all failed login attempts. (By default the plugin only logs up to 10 failed login because the process can be very resource intensive in case of a brute force attack)', 'wp-security-audit-log' ); ?></p>
												</td>
											</tr>
											<?php
										}
									}
									?>
								</tbody>
							</table>
							<?php
						}
						?>
					</div>
				<?php endforeach; ?>
			</div>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr( __( 'Save Changes', 'wp-security-audit-log' ) ); ?>"></p>
		</form>
		<?php
	}

	/**
	 * Method: Get View Header.
	 */
	public function Header() {
		?>
		<style type="text/css">
			.wsal-tab {
				display: none;
			}
			.wsal-tab tr.alert-incomplete td {
				color: #9BE;
			}
			.wsal-tab tr.alert-unavailable td {
				color: #CCC;
			}
			.wsal-sub-tabs {
				padding-left: 20px;
			}
			.wsal-sub-tabs .nav-tab-active {
				background-color: #fff;
				border-bottom: 1px solid #fff;
			}
			.wsal-tab td input[type=number] {
				width: 100%;
			}
		</style>
		<?php
	}

	/**
	 * Method: Get View Footer.
	 */
	public function Footer() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				var scrollHeight = jQuery(document).scrollTop();
				// tab handling code
				jQuery('#wsal-tabs>a').click(function(){
					jQuery('#wsal-tabs>a').removeClass('nav-tab-active');
					jQuery('.wsal-tab').hide();
					jQuery(jQuery(this).addClass('nav-tab-active').attr('href')).show();
					jQuery(jQuery(this).attr('href')+' .wsal-sub-tabs>a:first').click();
					setTimeout(function() {
						jQuery(window).scrollTop(scrollHeight);
					}, 1);
				});
				// sub tab handling code
				jQuery('.wsal-sub-tabs>a').click(function(){
					jQuery('.wsal-sub-tabs>a').removeClass('nav-tab-active');
					jQuery('.wsal-sub-tab').hide();
					jQuery(jQuery(this).addClass('nav-tab-active').attr('href')).show();
					setTimeout(function() {
						jQuery(window).scrollTop(scrollHeight);
					}, 1);
				});
				// checkbox handling code
				jQuery('.wsal-tab>thead>tr>th>:checkbox').change(function(){
					jQuery(this).parents('table:first').find('tbody>tr>th>:checkbox').attr('checked', this.checked);
				});
				jQuery('.wsal-tab>tbody>tr>th>:checkbox').change(function(){
					var allchecked = jQuery(this).parents('tbody:first').find('th>:checkbox:not(:checked)').length === 0;
					jQuery(this).parents('table:first').find('thead>tr>th:first>:checkbox:first').attr('checked', allchecked);
				});

				var hashlink = jQuery('#wsal-tabs>a[href="' + location.hash + '"]');
				var hashsublink = jQuery('.wsal-sub-tabs>a[href="' + location.hash + '"]');
				if (hashlink.length) {
					// show relevant tab
					hashlink.click();
				} else if (hashsublink.length) {
					// show relevant sub tab
					jQuery('#wsal-tabs>a[href="#' + hashsublink.data('parent') + '"]').click();
					hashsublink.click();
				} else {
					jQuery('#wsal-tabs>a:first').click();
					jQuery('.wsal-sub-tabs>a:first').click();
				}

				// Specific for alert 6007
				jQuery("input[value=6007]").on("change", function(){
					var check = jQuery("input[value=6007]").is(":checked");
					if(check) {
						jQuery(".check_log").attr ( "checked" ,"checked" );
					} else {
						jQuery(".check_log").removeAttr('checked');
					}
				});

				// Specific for alert 6023
				jQuery("input[value=6023]").on("change", function(){
					var check = jQuery("input[value=6023]").is(":checked");
					if(check) {
						jQuery(".check_visitor_log").attr ( "checked" ,"checked" );
					} else {
						jQuery(".check_visitor_log").removeAttr('checked');
					}
				});
			});
		</script>
		<?php
	}
}
