<?php
/**
 * View: Settings
 *
 * External DB settings view.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WSAL_Ext_Plugin' ) ) {
	exit( 'You are not allowed to view this page.' );
}

/**
 * Class WSAL_Ext_Settings for the plugin view.
 *
 * @package Wsal
 */
class WSAL_Ext_Settings extends WSAL_AbstractView {

	const QUERY_LIMIT = 100;

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
	 * @param WpSecurityAuditLog $plugin - Instance of WpSecurityAuditLog.
	 * @since  1.0.0
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		// Call to parent class.
		parent::__construct( $plugin );

		// Ajax events for meta tables of WSAL.
		add_action( 'wp_ajax_MigrateOccurrence', array( $this, 'MigrateOccurrence' ) );
		add_action( 'wp_ajax_MigrateMeta', array( $this, 'MigrateMeta' ) );
		add_action( 'wp_ajax_MigrateBackOccurrence', array( $this, 'MigrateBackOccurrence' ) );
		add_action( 'wp_ajax_MigrateBackMeta', array( $this, 'MigrateBackMeta' ) );

		// Ajax events for mirror and archive events.
		add_action( 'wp_ajax_MirroringNow', array( $this, 'MirroringNow' ) );
		add_action( 'wp_ajax_ArchivingNow', array( $this, 'ArchivingNow' ) );

		// Set the paths.
		$this->_base_dir = WSAL_BASE_DIR . 'extensions/external-db';
		$this->_base_url = WSAL_BASE_URL . 'extensions/external-db';
	}

	/**
	 * Method: Get View Title.
	 */
	public function GetTitle() {
		return __( 'External Database Configuration', 'wp-security-audit-log' );
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
		return __( 'DB & Integrations', 'wp-security-audit-log' );
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
		wp_enqueue_style( 'wsal-jq-timepick-css', $this->_base_url . '/js/jquery.timeentry/jquery.timeentry.css' );
		wp_enqueue_style( 'wsal-external-css', $this->_base_url . '/css/styles.css' );
		wp_enqueue_script( 'wsal-jq-plugin-js', $this->_base_url . '/js/jquery.timeentry/jquery.plugin.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'wsal-jq-timepick-js', $this->_base_url . '/js/jquery.timeentry/jquery.timeentry.min.js', array( 'jquery' ) );
	}

	/**
	 * Method: Get View Footer.
	 */
	public function Footer() {
		?>
		<script type="text/javascript">
			var query_limit = <?php echo self::QUERY_LIMIT; ?>;
			var is_24_hours = <?php echo json_encode( $this->_plugin->wsalCommonClass->IsTime24Hours() ); ?>;

			jQuery(document).ready(function() {
				var archivingConfig = <?php echo json_encode( $this->_plugin->wsalCommonClass->IsArchivingEnabled() ); ?>;
				var archiving_status = jQuery('#archiving_status');
				var archivingTxtNot = jQuery('#archiving_status_text');

				function wsalArchivingStatus(checkbox, label){
					if (checkbox.prop('checked')) {
						label.text('On');
						jQuery('#ArchiveName').prop('required', true);
						jQuery('#ArchiveUser').prop('required', true);
						jQuery('#ArchiveHostname').prop('required', true);
					} else {
						label.text('Off');
						jQuery('#ArchiveName').prop('required', false);
						jQuery('#ArchiveUser').prop('required', false);
						jQuery('#ArchiveHostname').prop('required', false);
					}
				}
				// Set On
				if (archivingConfig) {
					archiving_status.prop('checked', true);
				}
				wsalArchivingStatus(archiving_status, archivingTxtNot);

				archiving_status.on('change', function() {
					wsalArchivingStatus(archiving_status, archivingTxtNot);
				});

				var mirroringConfig = <?php echo json_encode( $this->_plugin->wsalCommonClass->IsMirroringEnabled() ); ?>;
				var mirroring_status = jQuery('#mirroring_status');
				var mirroringTxtNot = jQuery('#mirroring_status_text');

				function wsalMirroringStatus(checkbox, label){
					if (checkbox.prop('checked')) {
						label.text('On');
					} else {
						label.text('Off');
					}
				}
				// Set On
				if (mirroringConfig) {
					mirroring_status.prop('checked', true);
				}
				wsalMirroringStatus(mirroring_status, mirroringTxtNot);

				mirroring_status.on('change', function() {
					wsalMirroringStatus(mirroring_status, mirroringTxtNot);
				});

				// Show/Hide Mirroring type
				var checked = jQuery('input:radio[name=MirroringType]:checked').val();
				jQuery("#" + checked).show();
				setRequired(checked);

								jQuery('input:radio[name=MirroringType]').click(function() {
					var selected = jQuery(this).val();
					jQuery("tbody.desc").hide();
					jQuery("#" + selected).show(200);
					setRequired(selected);
				});

				function setRequired(mirroring_type){
					if (mirroring_type == "database") {
						jQuery('#MirrorName').prop('required', true);
						jQuery('#MirrorUser').prop('required', true);
						jQuery('#MirrorHostname').prop('required', true);
						jQuery('#Papertrail').prop('required', false);
					} else if (mirroring_type == "papertrail") {
						jQuery('#MirrorName').prop('required', false);
						jQuery('#MirrorUser').prop('required', false);
						jQuery('#MirrorHostname').prop('required', false);
						jQuery('#Papertrail').prop('required', true);
					} else {
						jQuery('#MirrorName').prop('required', false);
						jQuery('#MirrorUser').prop('required', false);
						jQuery('#MirrorHostname').prop('required', false);
						jQuery('#Papertrail').prop('required', false);
					}
				}
			});
		</script>
		<?php
		wp_enqueue_script( 'wsal-external-js', $this->_base_url . '/js/wsal-external.js', array( 'jquery' ) );
	}

	/**
	 * Method: Save settings.
	 */
	protected function Save() {
		// Get global $_POST array.
		$post_array = filter_input_array( INPUT_POST );

		// Save External Adapter config.
		if ( ! empty( $post_array['AdapterUser'] )
		&& ( '' != $post_array['AdapterUser'] )
		&& ( '' != $post_array['AdapterName'] )
		&& ( '' != $post_array['AdapterHostname'] )
		&& wp_verify_nonce( $post_array['_wpnonce'], 'external-db-form' ) ) {
			$adapter_type        = trim( $post_array['AdapterType'] );
			$adapter_user        = trim( $post_array['AdapterUser'] );
			$adapter_name        = trim( $post_array['AdapterName'] );
			$adapter_hostname    = trim( $post_array['AdapterHostname'] );
			$adapter_base_prefix = isset( $post_array['AdapterBasePrefix'] ) ? trim( $post_array['AdapterBasePrefix'] ) : false;
			$adapter_url_prefix  = isset( $post_array['AdapterUrlBasePrefix'] ) ? trim( $post_array['AdapterUrlBasePrefix'] ) : false;
			$password            = $this->_plugin->wsalCommonClass->EncryptPassword( trim( $post_array['AdapterPassword'] ) );

			// Check for URL base prefix.
			if ( 'on' === $adapter_url_prefix ) {
				$adapter_base_prefix = $this->get_url_base_prefix();
			}

			// Check if the config is working.
			WSAL_Connector_ConnectorFactory::CheckConfig( $adapter_type, $adapter_user, $password, $adapter_name, $adapter_hostname, $adapter_base_prefix );

			/* Setting External Adapter DB config */
			$this->_plugin->wsalCommonClass->AddGlobalOption( 'adapter-type', $adapter_type );
			$this->_plugin->wsalCommonClass->AddGlobalOption( 'adapter-user', $adapter_user );
			$this->_plugin->wsalCommonClass->AddGlobalOption( 'adapter-password', $password );
			$this->_plugin->wsalCommonClass->AddGlobalOption( 'adapter-name', $adapter_name );
			$this->_plugin->wsalCommonClass->AddGlobalOption( 'adapter-hostname', $adapter_hostname );
			$this->_plugin->wsalCommonClass->AddGlobalOption( 'adapter-base-prefix', $adapter_base_prefix );
			$this->_plugin->wsalCommonClass->AddGlobalOption( 'adapter-url-base-prefix', $adapter_url_prefix );

			$plugin = new WpSecurityAuditLog();
			$config = WSAL_Connector_ConnectorFactory::GetConfigArray( $adapter_type, $adapter_user, $password, $adapter_name, $adapter_hostname, $adapter_base_prefix );

			// Create tables in the database.
			$plugin->getConnector( $config )->installAll( true );
		} elseif ( isset( $post_array['Archiving'] )
		&& wp_verify_nonce( $post_array['_wpnonce'], 'archive-db-form' ) ) {
			// Save Archiving.
			$this->_plugin->wsalCommonClass->SetArchivingEnabled( isset( $post_array['SetArchiving'] ) );
			$this->_plugin->wsalCommonClass->SetArchivingStop( isset( $post_array['StopArchiving'] ) );
			if ( isset( $post_array['RunArchiving'] ) ) {
				$this->_plugin->wsalCommonClass->SetArchivingRunEvery( $post_array['RunArchiving'] );
				// Reset old archiving cron job.
				wp_clear_scheduled_hook( 'run_archiving' );
			}

			if ( ! empty( $post_array['ArchiveUser'] )
			&& ( '' != $post_array['ArchiveUser'] )
			&& ( '' != $post_array['ArchiveName'] )
			&& ( '' != $post_array['ArchiveHostname'] ) ) {
				$archive_type        = trim( $post_array['ArchiveType'] );
				$archive_user        = trim( $post_array['ArchiveUser'] );
				$archive_name        = trim( $post_array['ArchiveName'] );
				$archive_hostname    = trim( $post_array['ArchiveHostname'] );
				$archive_base_prefix = isset( $post_array['ArchiveBasePrefix'] ) ? trim( $post_array['ArchiveBasePrefix'] ) : false;
				$archive_url_prefix  = isset( $post_array['ArchiveUrlBasePrefix'] ) ? trim( $post_array['ArchiveUrlBasePrefix'] ) : false;
				$password            = $this->_plugin->wsalCommonClass->EncryptPassword( trim( $post_array['ArchivePassword'] ) );

				// Check for URL base prefix.
				if ( 'on' === $archive_url_prefix ) {
					$archive_base_prefix = $this->get_url_base_prefix( 'archive' );
				}

				// Check if the config is working.
				WSAL_Connector_ConnectorFactory::CheckConfig( $archive_type, $archive_user, $password, $archive_name, $archive_hostname, $archive_base_prefix );

				/* Setting Archive DB config */
				$this->_plugin->wsalCommonClass->AddGlobalOption( 'archive-type', $archive_type );
				$this->_plugin->wsalCommonClass->AddGlobalOption( 'archive-user', $archive_user );
				$this->_plugin->wsalCommonClass->AddGlobalOption( 'archive-password', $password );
				$this->_plugin->wsalCommonClass->AddGlobalOption( 'archive-name', $archive_name );
				$this->_plugin->wsalCommonClass->AddGlobalOption( 'archive-hostname', $archive_hostname );
				$this->_plugin->wsalCommonClass->AddGlobalOption( 'archive-base-prefix', $archive_base_prefix );
				$this->_plugin->wsalCommonClass->AddGlobalOption( 'archive-url-base-prefix', $archive_url_prefix );

				$this->_plugin->wsalCommonClass->SetArchivingDateEnabled( 'date' == $post_array['ArchiveBy'] );
				$this->_plugin->wsalCommonClass->SetArchivingLimitEnabled( 'limit' == $post_array['ArchiveBy'] );
				if ( 'date' == $post_array['ArchiveBy'] ) {
					$this->_plugin->wsalCommonClass->SetArchivingDate( $post_array['ArchivingDate'] );
					$this->_plugin->wsalCommonClass->SetArchivingDateType( $post_array['DateType'] );
				} else {
					$this->_plugin->wsalCommonClass->SetArchivingLimit( $post_array['ArchivingLimit'] );
				}

				$plugin = new WpSecurityAuditLog();
				$config = WSAL_Connector_ConnectorFactory::GetConfigArray( $archive_type, $archive_user, $password, $archive_name, $archive_hostname, $archive_base_prefix );

				$plugin->getConnector( $config )->installAll( true );
			}
		} elseif ( isset( $post_array['Mirroring'] )
		&& wp_verify_nonce( $post_array['_wpnonce'], 'mirror-db-form' ) ) {
			/* Save Mirroring */
			$this->_plugin->wsalCommonClass->SetMirroringEnabled( isset( $post_array['SetMirroring'] ) );
			$this->_plugin->wsalCommonClass->SetMirroringStop( isset( $post_array['StopMirroring'] ) );
			if ( isset( $post_array['RunMirroring'] ) ) {
				$this->_plugin->wsalCommonClass->SetMirroringRunEvery( $post_array['RunMirroring'] );
				// Reset old mirroring cron job.
				wp_clear_scheduled_hook( 'run_mirroring' );
			}
			if ( isset( $post_array['MirroringType'] ) && $post_array['MirroringType'] == 'database' ) {
				if ( ! empty( $post_array['MirrorUser'] ) && ($post_array['MirrorUser'] != '') && ($post_array['MirrorName'] != '') && ($post_array['MirrorHostname'] != '') ) {
					$mirror_type        = trim( $post_array['MirrorType'] );
					$mirror_user        = trim( $post_array['MirrorUser'] );
					$mirror_name        = trim( $post_array['MirrorName'] );
					$mirror_hostname    = trim( $post_array['MirrorHostname'] );
					$mirror_base_prefix = isset( $post_array['MirrorBasePrefix'] ) ? trim( $post_array['MirrorBasePrefix'] ) : false;
					$mirror_url_prefix  = isset( $post_array['MirrorUrlBasePrefix'] ) ? trim( $post_array['MirrorUrlBasePrefix'] ) : false;
					$password           = $this->_plugin->wsalCommonClass->EncryptPassword( trim( $post_array['MirrorPassword'] ) );

					// Check for URL base prefix.
					if ( 'on' === $mirror_url_prefix ) {
						$mirror_base_prefix = $this->get_url_base_prefix( 'mirror' );
					}

					// Check if the config is working.
					WSAL_Connector_ConnectorFactory::CheckConfig( $mirror_type, $mirror_user, $password, $mirror_name, $mirror_hostname, $mirror_base_prefix );

					/* Setting Archive DB config */
					$this->_plugin->wsalCommonClass->AddGlobalOption( 'mirror-type', $mirror_type );
					$this->_plugin->wsalCommonClass->AddGlobalOption( 'mirror-user', $mirror_user );
					$this->_plugin->wsalCommonClass->AddGlobalOption( 'mirror-password', $password );
					$this->_plugin->wsalCommonClass->AddGlobalOption( 'mirror-name', $mirror_name );
					$this->_plugin->wsalCommonClass->AddGlobalOption( 'mirror-hostname', $mirror_hostname );
					$this->_plugin->wsalCommonClass->AddGlobalOption( 'mirror-base-prefix', $mirror_base_prefix );
					$this->_plugin->wsalCommonClass->AddGlobalOption( 'mirror-url-base-prefix', $mirror_url_prefix );

					$this->_plugin->wsalCommonClass->SetMirroringType( $post_array['MirroringType'] );

					$plugin = new WpSecurityAuditLog();
					$config = WSAL_Connector_ConnectorFactory::GetConfigArray( $mirror_type, $mirror_user, $password, $mirror_name, $mirror_hostname, $mirror_base_prefix );

					$plugin->getConnector( $config )->installAll( true );
				}
			} elseif ( isset( $post_array['MirroringType'] ) && 'papertrail' == $post_array['MirroringType'] ) {
				if ( ! empty( $post_array['Papertrail'] ) && ( $post_array['Papertrail'] != '' ) ) {
					$this->_plugin->wsalCommonClass->SetMirroringType( $post_array['MirroringType'] );
					$papertrail = trim( $post_array['Papertrail'] );
					$this->_plugin->wsalCommonClass->SetPapertrailDestination( $papertrail );
					$this->_plugin->wsalCommonClass->SetPapertrailColorization( isset( $post_array['Colorization'] ) );
				}
			} elseif ( isset( $post_array['MirroringType'] ) && 'syslog' == $post_array['MirroringType'] ) {
				$this->_plugin->wsalCommonClass->SetMirroringType( $post_array['MirroringType'] );
			}
		}
	}

	/**
	 * Method: Return URL based prefix for DB.
	 *
	 * @param string $name - Name of the DB type.
	 * @return string - URL based prefix.
	 */
	public function get_url_base_prefix( $name = '' ) {
		// Get home URL.
		$home_url  = get_home_url();
		$protocols = array( 'http://', 'https://' ); // URL protocols.
		$home_url  = str_replace( $protocols, '', $home_url ); // Replace URL protocols.
		$home_url  = str_replace( array( '.', '-' ), '_', $home_url ); // Replace `.` with `_` in the URL.

		// Concat name of the DB type at the end.
		if ( ! empty( $name ) ) {
			$home_url .= '_';
			$home_url .= $name;
			$home_url .= '_';
		} else {
			$home_url .= '_';
		}

		// Return the prefix.
		return $home_url;
	}

	/**
	 * Checks if the necessary tables are available.
	 *
	 * @return bool true|false
	 */
	protected function CheckIfTableExist() {
		return $this->_plugin->wsalCommonClass->IsInstalled();
	}

	/**
	 * Checks if there is the adapter setting.
	 *
	 * @return bool true|false
	 */
	protected function CheckSetting() {
		$config = $this->_plugin->settings->GetAdapterConfig( 'adapter-type' );
		if ( ! empty( $config ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Migrate to external database (Metadata table)
	 */
	public function MigrateMeta() {
		$limit = self::QUERY_LIMIT;
		$index = intval( $_POST['index'] );
		$response = $this->_plugin->wsalCommonClass->MigrateMeta( $index, $limit );
		echo json_encode( $response );
		exit;
	}

	/**
	 * Migrate to external database (Occurrences table)
	 */
	public function MigrateOccurrence() {
		$limit = self::QUERY_LIMIT;
		$index = intval( $_POST['index'] );
		$response = $this->_plugin->wsalCommonClass->MigrateOccurrence( $index, $limit );
		echo json_encode( $response );
		exit;
	}

	/**
	 * Migrate back to WP database (Metadata table)
	 */
	public function MigrateBackMeta() {
		$limit = self::QUERY_LIMIT;
		$index = intval( $_POST['index'] );
		$response = $this->_plugin->wsalCommonClass->MigrateBackMeta( $index, $limit );
		echo json_encode( $response );
		exit;
	}

	/**
	 * Migrate back to WP database (Occurrences table)
	 */
	public function MigrateBackOccurrence() {
		$limit = self::QUERY_LIMIT;
		$index = intval( $_POST['index'] );
		$response = $this->_plugin->wsalCommonClass->MigrateBackOccurrence( $index, $limit );
		echo json_encode( $response );
		exit;
	}

	/**
	 * Mirroring alerts Now.
	 */
	public function MirroringNow() {
		$this->_plugin->wsalCommonClass->mirroring_alerts();
		exit;
	}

	/**
	 * Archiving alerts Now.
	 */
	public function ArchivingNow() {
		$this->_plugin->wsalCommonClass->archiving_alerts();
		exit;
	}

	/**
	 * Method: Render view.
	 */
	public function Render() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
		}

		if ( isset( $_POST['submit'] ) ) {
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
		} else {
			?>
			<div id="ajax-response" class="notice hidden">
				<img src="<?php echo esc_url( $this->_base_url ); ?>/css/default.gif">
				<p>
					<?php esc_html_e( 'Please do not close this window while migrating alerts.', 'wp-security-audit-log' ); ?>
					<span id="ajax-response-counter"></span>
				</p>
			</div>
			<?php
		}
		?>
		<div id="wsal-external-db">
			<h2 id="wsal-tabs" class="nav-tab-wrapper">
				<a href="#external" class="nav-tab"><?php esc_html_e( 'External Database', 'wp-security-audit-log' ); ?></a>
				<a href="#mirroring" class="nav-tab"><?php esc_html_e( 'Mirroring', 'wp-security-audit-log' ); ?></a>
				<a href="#archiving" class="nav-tab"><?php esc_html_e( 'Archiving', 'wp-security-audit-log' ); ?></a>
			</h2>
			<div class="nav-tabs">
				<!-- Tab External Database -->
				<table class="form-table wsal-tab" id="external">
					<form method="post" autocomplete="off">
						<input type="hidden" name="page" value="<?php echo filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ); ?>" />
						<?php wp_nonce_field( 'external-db-form' ); ?>
						<tbody class="widefat">
							<tr>
								<td colspan="2"><?php esc_html_e( 'Configure the database connection details below to store the WordPress audit log in an external database and not in the WordPress database.', 'wp-security-audit-log' ); ?></td>
							</tr>
							<!-- Adapter Database Configuration -->
							<?php $this->get_database_fields( 'adapter' ); ?>
							<tr>
								<th><label for="Current"><?php esc_html_e( 'Current Connection Details', 'wp-security-audit-log' ); ?></label></th>
								<td>
									<?php $adapter_name = $this->_plugin->settings->GetAdapterConfig( 'adapter-name' ); ?>
									<?php $adapter_hostname = $this->_plugin->settings->GetAdapterConfig( 'adapter-hostname' ); ?>
									<span class="current-connection"><?php esc_html_e( 'Currently Connected to database', 'wp-security-audit-log' ); ?>
									<strong><?php echo ( ! empty( $adapter_name ) ? esc_html( $adapter_name ) : 'Default' ); ?></strong>
									on server <strong><?php echo ( ! empty( $adapter_hostname ) ? esc_html( $adapter_hostname ) : 'Current' ); ?></strong></span>
								</td>
							</tr>
						</tbody>
						<tbody>
							<tr>
								<td colspan="2">
									<input type="submit" name="submit" id="submit" class="button button-primary" value="Save &amp; Test Changes">
								</td>
							</tr>
							<?php
							if ( $this->CheckIfTableExist() && $this->CheckSetting() ) {
								$disabled = '';
							} else {
								$disabled = 'disabled';
							}
							?>
							<tr>
								<td colspan="2">
									<input type="button" name="wsal-migrate" id="wsal-migrate" class="button button-primary" value="Migrate Alerts from WordPress Database" <?php echo $disabled; ?>>
									<span class="description">
										<?php esc_html_e( 'Migrate existing WordPress Security Alerts from the WordPress database to the new external database.', 'wp-security-audit-log' ); ?>
									</span>
								</td>
							</tr>
							<?php
							if ( ! $this->CheckSetting() ) {
								$disabled = 'disabled';
							} else {
								$disabled = '';
							}
							?>
							<tr>
								<td colspan="2">
									<input type="button" name="wsal-migrate-back" id="wsal-migrate-back" class="button button-primary" value="Switch to WordPress Database" <?php echo esc_attr( $disabled ); ?>>
									<span class="description">
										<?php esc_html_e( 'Remove the external database and start using the WordPress database again. In the process the alerts will be automatically migrated to the WordPress database.', 'wp-security-audit-log' ); ?>
									</span>
								</td>
							</tr>
						</tbody>
					</form>
				</table>
				<!-- Tab Mirroring -->
				<table class="form-table wsal-tab" id="mirroring">
					<form method="post" autocomplete="off">
						<input type="hidden" name="Mirroring" value="1">
						<?php wp_nonce_field( 'mirror-db-form' ); ?>
						<tbody class="widefat">
							<tr>
								<td colspan="2">
								<?php esc_html_e( 'When you enable this option the WordPress audit trail will be mirrored to the configured database / data source.', 'wp-security-audit-log' ); ?><br>
								<?php esc_html_e( 'By mirroring the audit trail you ensure that you always have a backup copy of the audit trail and also ensure that the audit trail is not tampered with in the unfortunate event of an attack.', 'wp-security-audit-log' ); ?></td>
							</tr>
							<tr>
								<th><label for="Mirroring"><?php esc_html_e( 'Enable Mirroring', 'wp-security-audit-log' ); ?></label></th>
								<td>
									<fieldset>
										<label for="Mirroring">
											<span class="f-container">
												<span class="f-left">
													<input type="checkbox" name="SetMirroring" value="1" class="switch" id="mirroring_status"/>
													<label for="mirroring_status"></label>
												</span>
												<span class="f-right f-text"><span id="mirroring_status_text"></span></span>
											</span>
										</label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th><label for="options"><?php esc_html_e( 'Mirroring options', 'wp-security-audit-log' ); ?></label></th>
								<td>
									<?php $type = $this->_plugin->wsalCommonClass->GetMirroringType(); ?>
									<fieldset>
										<p>
											<input id="mirroring_db" type="radio" name="MirroringType" value="database" <?php echo ( 'database' == $type ) ? 'checked="checked"' : false; ?> />
											<label for="mirroring_db"><?php esc_html_e( 'Database', 'wp-security-audit-log' ); ?></label>
											<?php
											if ( 'database' == $type ) {
												esc_html_e( '(Configured and working)', 'wp-security-audit-log' );
											}
											?>
										</p>
										<p>
											<input id="mirroring_papertrail" type="radio" name="MirroringType" value="papertrail" <?php echo ( 'papertrail' == $type ) ? 'checked="checked"' : false; ?> />
											<label for="mirroring_papertrail"><?php esc_html_e( 'Papertrail', 'wp-security-audit-log' ); ?></label>
											<?php
											if ( 'papertrail' == $type ) {
												esc_html_e( '(Configured and working)', 'wp-security-audit-log' );
											}
											?>
										</p>
										<p>
											<input id="mirroring_syslog" type="radio" name="MirroringType" value="syslog" <?php echo ( 'syslog' == $type ) ? 'checked="checked"' : false; ?> />
											<label for="mirroring_syslog"><?php esc_html_e( 'SysLog', 'wp-security-audit-log' ); ?></label>
											<?php
											if ( 'syslog' == $type ) {
												esc_html_e( '(Configured and working)', 'wp-security-audit-log' );
											}
											?>
										</p>
									</fieldset>
								</td>
						</tbody>
						<tbody id="database" class="widefat desc" style="display:none">
							<!-- Mirroring Database Configuration -->
							<?php $this->get_database_fields( 'mirror' ); ?>
						</tbody>
						<tbody id="papertrail" class="widefat desc" style="display:none">
							<!-- Papertrail Configuration -->
							<tr>
								<td colspan="2">
									<?php esc_html_e( 'Configure the below options to mirror the WordPress audit trail to Papertrail.', 'wp-security-audit-log' ); ?>
								</td>
							</tr>
							<tr>
								<th><label for="Papertrail"><?php esc_html_e( 'Destination', 'wp-security-audit-log' ); ?></label></th>
								<td>
									<fieldset>
										<?php $destination = $this->_plugin->wsalCommonClass->GetPapertrailDestination(); ?>
										<input type="text" id="Papertrail" name="Papertrail" value="<?php echo $destination; ?>" style="display: block; width: 250px;">
										<span class="description">
											<?php esc_html_e( 'Specify your destination. You can find your Papertrail Destination in the', 'wp-security-audit-log' ); ?>
											&nbsp;<a href="https://papertrailapp.com/account/destinations" target="_blank">Log Destinations</a>&nbsp;
											<?php esc_html_e( 'section of your Papertrail account page. ', 'wp-security-audit-log' ); ?><br>
											<?php esc_html_e( 'It should have the following format:', 'wp-security-audit-log' ); ?>
											&nbsp;<strong>logs4.papertrailapp.com:54321</strong>
										</span>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th><label for="Colorization"><?php esc_html_e( 'Colorization', 'wp-security-audit-log' ); ?></label></th>
								<td>
									<fieldset>
										<label for="Colorization">
											<input type="checkbox" name="Colorization" value="1" id="Colorization"
											<?php
											if ( $this->_plugin->wsalCommonClass->IsPapertrailColorizationEnabled() ) {
												echo ' checked="checked"';
											}
											?>
											/> <?php esc_html_e( 'Enable', 'wp-security-audit-log' ); ?>
										</label>
									</fieldset>
								</td>
							</tr>
						</tbody>
						<tbody id="syslog" class="widefat desc" style="display:none">
							<!-- SysLog Nothing to config -->
						</tbody>
						<tbody class="widefat">
							<?php $this->get_schedule_fields( 'mirroring' ); ?>
						</tbody>
						<tbody>
							<?php
							if ( ! $this->_plugin->wsalCommonClass->IsMirroringEnabled() ) {
								$disabled = 'disabled';
							} else {
								$disabled = '';
							}
							?>
							<tr>
								<td colspan="2">
									<input type="submit" name="submit" class="button button-primary" value="Save Changes">
									<input type="button" style="margin-left: 20px;" id="wsal-mirroring" class="button button-primary" value="Execute Mirroring Now" <?php echo esc_attr( $disabled ); ?>>
								</td>
							</tr>
						</tbody>
					</form>
				</table>
				<!-- Tab Archiving -->
				<table class="form-table wsal-tab" id="archiving">
					<form method="post" autocomplete="off">
						<input type="hidden" name="Archiving" value="1">
						<?php wp_nonce_field( 'archive-db-form' ); ?>
						<tbody class="widefat">
							<tr>
								<td colspan="2">
								<?php esc_html_e( 'When you enable archiving you can archive a number of alerts from the main database to the archiving database.', 'wp-security-audit-log' ); ?><br>
								<?php esc_html_e( 'This means that there will be less alerts in the main database, therefore tasks such as searching will be much faster and the database will be easier to manage.', 'wp-security-audit-log' ); ?></td>
							</tr>
							<tr>
								<th><label for="Archiving"><?php esc_html_e( 'Enable Archiving', 'wp-security-audit-log' ); ?></label></th>
								<td>
									<fieldset>
										<label for="Archiving">
											<span class="f-container">
												<span class="f-left">
													<input type="checkbox" name="SetArchiving" value="1" class="switch" id="archiving_status"/>
													<label for="archiving_status"></label>
												</span>
												<span class="f-right f-text"><span id="archiving_status_text"></span></span>
											</span>
										</label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th><label for="options"><?php esc_html_e( 'Archiving options', 'wp-security-audit-log' ); ?></label></th>
								<td>
									<fieldset>
										<?php $nbld = $this->_plugin->wsalCommonClass->IsArchivingDateEnabled(); ?>
										<label for="archive_date">
											<input type="radio" id="archive_date" name="ArchiveBy" value="date" <?php echo ( $nbld ) ? 'checked="checked"' : false; ?>>
											<?php esc_html_e( 'Archive alerts older than', 'wp-security-audit-log' ); ?>
										</label>
										<input type="number" name="ArchivingDate" value="<?php echo esc_attr( $this->_plugin->wsalCommonClass->GetArchivingDate() ); ?>">
										<?php $date_type = strtolower( $this->_plugin->wsalCommonClass->GetArchivingDateType() ); ?>
										<select name="DateType" class="age-type">
											<option value="weeks" <?php echo ( 'weeks' == $date_type ) ? 'selected="selected"' : false; ?>>
												<?php esc_html_e( 'weeks', 'wp-security-audit-log' ); ?>
											</option>
											<option value="months" <?php echo ( 'months' == $date_type ) ? 'selected="selected"' : false; ?>>
												<?php esc_html_e( 'months', 'wp-security-audit-log' ); ?>
											</option>
											<option value="years" <?php echo ( 'years' == $date_type ) ? 'selected="selected"' : false; ?>>
												<?php esc_html_e( 'years', 'wp-security-audit-log' ); ?>
											</option>
										</select>
									</fieldset>
									<fieldset>
										<?php $nbld = $this->_plugin->wsalCommonClass->IsArchivingLimitEnabled(); ?>
										<label for="archive_limit">
											<input type="radio" id="archive_limit" name="ArchiveBy" value="limit" <?php echo ( $nbld ) ? 'checked="checked"' : false; ?>>
											<?php echo esc_html__( 'Archive when audit log has more than', 'wp-security-audit-log' ); ?>
										</label>
										<input type="number" name="ArchivingLimit" value="<?php echo esc_attr( $this->_plugin->wsalCommonClass->GetArchivingLimit() ); ?>">
										<?php esc_html_e( 'alerts', 'wp-security-audit-log' ); ?>
									</fieldset>
									<span class="description">
										<?php esc_html_e( 'The configured archiving options will override the Security Alerts Pruning settings configured in the plugin’s settings.', 'wp-security-audit-log' ); ?>
									</span>
								</td>
							</tr>
							<!-- Archive Database Configuration -->
							<?php $this->get_database_fields( 'archive' ); ?>
						</tbody>
						<tbody class="widefat">
							<?php $this->get_schedule_fields( 'archiving' ); ?>
						</tbody>
						<tbody>
							<?php
							if ( ! $this->_plugin->wsalCommonClass->IsArchivingEnabled() ) {
								$disabled = 'disabled';
							} else {
								$disabled = '';
							}
							?>
							<tr>
								<td colspan="2">
									<input type="submit" name="submit" class="button button-primary" value="Save Changes">
									<input type="button" style="margin-left: 20px;" id="wsal-archiving" class="button button-primary" value="Execute Archiving Now" <?php echo esc_attr( $disabled ); ?>>
								</td>
							</tr>
						</tbody>
					</form>
				</table>
			</div>
		</div>
		<?php
	}

	/**
	 * Common function for the Database fields.
	 *
	 * @param string $name - Name of DB Type.
	 */
	private function get_database_fields( $name ) {
		$label_name  = ucfirst( $name );
		$option_name = strtolower( $name );
		?>
		<tr>
			<th><label for="<?php echo esc_attr( $label_name ); ?>Type"><?php esc_html_e( 'Database Type', 'wp-security-audit-log' ); ?></label></th>
			<td>
				<fieldset>
					<?php $type = strtolower( $this->_plugin->wsalCommonClass->GetOptionByName( $option_name . '-type' ) ); ?>
					<select name="<?php echo esc_attr( $label_name ); ?>Type" id="<?php echo esc_attr( $label_name ); ?>Type">
						<option value="MySQL" <?php echo ( 'mysql' === $type ) ? 'selected="selected"' : false; ?>>
							<?php esc_html_e( 'DB MySQL', 'wp-security-audit-log' ); ?>
						</option>
					</select>
					<br/>
					<span class="description">
						<?php esc_html_e( 'At the moment only MySQL server is support. Support for other different SQL sever types will be available in the future.', 'wp-security-audit-log' ); ?>
					</span>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo esc_attr( $label_name ); ?>Name"><?php esc_html_e( 'Database Name', 'wp-security-audit-log' ); ?></label></th>
			<td>
				<fieldset>
					<?php $name = $this->_plugin->wsalCommonClass->GetOptionByName( $option_name . '-name' ); ?>
					<input type="text" id="<?php echo esc_attr( $label_name ); ?>Name" name="<?php echo esc_attr( $label_name ); ?>Name" value="<?php echo esc_attr( $name ); ?>" style="display: block; width: 250px;">
					<span class="description">
						<?php esc_html_e( 'Specify the name of the database where you will store the WordPress Audit Log.', 'wp-security-audit-log' ); ?>
					</span>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo esc_attr( $label_name ); ?>User"><?php esc_html_e( 'Database User', 'wp-security-audit-log' ); ?></label></th>
			<td>
				<fieldset>
					<?php $user = $this->_plugin->wsalCommonClass->GetOptionByName( $option_name . '-user' ); ?>
					<input type="text" id="A<?php echo esc_attr( $label_name ); ?>User" name="<?php echo esc_attr( $label_name ); ?>User" value="<?php echo esc_attr( $user ); ?>" style="display: block; width: 250px;">
					<span class="description">
						<?php esc_html_e( 'Specify the username to be used to connect to the database.', 'wp-security-audit-log' ); ?>
					</span>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo esc_attr( $label_name ); ?>Password"><?php esc_html_e( 'Database Password', 'wp-security-audit-log' ); ?></label></th>
			<td>
				<fieldset>
					<input type="password" id="<?php echo esc_attr( $label_name ); ?>Password" name="<?php echo esc_attr( $label_name ); ?>Password" style="display: block; width: 250px;">
					<span class="description">
						<?php esc_html_e( 'Specify the password each time you want to submit new changes. For security reasons, the plugin does not store the password in this form.', 'wp-security-audit-log' ); ?>
					</span>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo esc_attr( $label_name ); ?>Hostname"><?php esc_html_e( 'Database Hostname', 'wp-security-audit-log' ); ?></label></th>
			<td>
				<fieldset>
					<?php $hostname = $this->_plugin->wsalCommonClass->GetOptionByName( $option_name . '-hostname' ); ?>
					<input type="text" id="<?php echo esc_attr( $label_name ); ?>Hostname" name="<?php echo esc_attr( $label_name ); ?>Hostname" value="<?php echo esc_attr( $hostname ); ?>" style="display: block; width: 250px;">
					<span class="description">
						<?php esc_html_e( 'Specify the hostname or IP address of the database server.', 'wp-security-audit-log' ); ?>
					</span>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th><label for="<?php echo esc_attr( $label_name ); ?>BasePrefix"><?php esc_html_e( 'Database Base prefix', 'wp-security-audit-log' ); ?></label></th>
			<td>
				<fieldset>
					<?php
					$base_prefix = $this->_plugin->wsalCommonClass->GetOptionByName( $option_name . '-base-prefix' );
					$url_base_prefix = $this->_plugin->wsalCommonClass->GetOptionByName( $option_name . '-url-base-prefix', false );
					if ( empty( $base_prefix ) ) {
						$base_prefix = $this->_plugin->wsalCommonClass->GetOptionByName( 'adapter-base-prefix' );
						if ( empty( $base_prefix ) ) {
							$base_prefix = $GLOBALS['wpdb']->base_prefix;
						}
					}
					?>
					<input type="text" id="<?php echo esc_attr( $label_name ); ?>BasePrefix" name="<?php echo esc_attr( $label_name ); ?>BasePrefix" value="<?php echo esc_attr( $base_prefix ); ?>" style="display: block; width: 250px;">
					<span class="description">
						<?php esc_html_e( 'Specify a prefix for the database tables of the audit log. Ideally this prefix should be different from the one you use for WordPress so it is not guessable.', 'wp-security-audit-log' ); ?>
					</span>
					<br />
					<input type="checkbox" id="<?php echo esc_attr( $label_name ); ?>UrlBasePrefix" name="<?php echo esc_attr( $label_name ); ?>UrlBasePrefix" <?php checked( $url_base_prefix, 'on' ); ?>>
					<label for="<?php echo esc_attr( $label_name ); ?>UrlBasePrefix"><?php esc_html_e( 'Use website URL as table prefix', 'wp-security-audit-log' ); ?></label>
				</fieldset>
			</td>
		</tr>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				// Enable/disable login notification textarea.
				function wsal_update_<?php echo esc_attr( $label_name ); ?>( checkbox, input ) {
					if ( checkbox.prop( 'checked' ) ) {
						input.prop( 'disabled', 'disabled' );
					} else {
						input.removeProp( 'disabled' );
					}
				}

				// Login page notification settings.
				var <?php echo esc_attr( $label_name ); ?>UrlBasePrefix = jQuery( '#<?php echo esc_attr( $label_name ); ?>UrlBasePrefix' );
				var <?php echo esc_attr( $label_name ); ?>BasePrefix = jQuery( '#<?php echo esc_attr( $label_name ); ?>BasePrefix' );
				wsal_update_<?php echo esc_attr( $label_name ); ?>( <?php echo esc_attr( $label_name ); ?>UrlBasePrefix, <?php echo esc_attr( $label_name ); ?>BasePrefix );

				// Check the change event on checkbox.
				<?php echo esc_attr( $label_name ); ?>UrlBasePrefix.on( 'change', function() {
					wsal_update_<?php echo esc_attr( $label_name ); ?>( <?php echo esc_attr( $label_name ); ?>UrlBasePrefix, <?php echo esc_attr( $label_name ); ?>BasePrefix );
				} );
			});
		</script>
		<?php
	}

	/**
	 * Common function to schedule cron job.
	 *
	 * @param string $name - Name of DB Type.
	 */
	private function get_schedule_fields( $name ) {
		$label_name = ucfirst( $name );
		$option_name = strtolower( $name );
		$configName = 'Is' . $label_name . 'Stop';
		?>
		<tr>
			<th><label for="Run<?php echo esc_attr( $label_name ); ?>">Run <?php echo esc_html( $option_name ); ?> process every</label></th>
			<td>
				<fieldset>
					<?php
					$name = 'Get' . $label_name . 'RunEvery';
					$every = strtolower( $this->_plugin->wsalCommonClass->$name() );
					?>
					<select name="Run<?php echo esc_attr( $label_name ); ?>" id="Run<?php echo esc_attr( $label_name ); ?>">
						<option value="tenminutes" <?php echo ( 'tenminutes' == $every ) ? 'selected="selected"' : false; ?>>
							<?php esc_html_e( '10 minutes', 'wp-security-audit-log' ); ?>
						</option>
						<option value="thirtyminutes" <?php echo ( 'thirtyminutes' == $every ) ? 'selected="selected"' : false; ?>>
							<?php esc_html_e( '30 minutes', 'wp-security-audit-log' ); ?>
						</option>
						<option value="fortyfiveminutes" <?php echo ( 'fortyfiveminutes' == $every ) ? 'selected="selected"' : false; ?>>
							<?php esc_html_e( '45 minutes', 'wp-security-audit-log' ); ?>
						</option>
						<option value="hourly" <?php echo ( 'hourly' == $every ) ? 'selected="selected"' : false; ?>>
							<?php esc_html_e( '1 hour', 'wp-security-audit-log' ); ?>
						</option>
					</select>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th><label for="Stop<?php echo( $label_name ); ?>">Stop <?php echo( $label_name ); ?></label></th>
			<td>
				<fieldset>
					<label for="Stop<?php echo( $label_name ); ?>" class="no-margin">
						<span class="f-container">
							<span class="f-left">
								<input type="checkbox" name="Stop<?php echo esc_attr( $label_name ); ?>" value="1" class="switch" id="<?php echo esc_attr( $option_name ); ?>_stop"/>
								<label for="<?php echo esc_attr( $option_name ); ?>_stop" class="no-margin orange"></label>
							</span>
						</span>
					</label>
					<span class="description">Current status: <strong><span id="<?php echo esc_attr( $option_name ); ?>_stop_text"></span></strong></span>
				</fieldset>
			</td>
		</tr>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				var <?php echo $option_name; ?>Stop = <?php echo json_encode( $this->_plugin->wsalCommonClass->$configName() ); ?>;
				var <?php echo $option_name; ?>_stop = jQuery('#<?php echo $option_name; ?>_stop');
				var <?php echo $option_name; ?>TxtNot = jQuery('#<?php echo $option_name; ?>_stop_text');

				function wsal<?php echo $label_name; ?>Stop(checkbox, label){
					if (checkbox.prop('checked')) {
						label.text('Stopped');
					} else {
						label.text('Running');
					}
				}
				// Set On
				if (<?php echo $option_name; ?>Stop) {
					<?php echo $option_name; ?>_stop.prop('checked', true);
				}
				wsal<?php echo $label_name; ?>Stop(<?php echo $option_name; ?>_stop, <?php echo $option_name; ?>TxtNot);

				<?php echo $option_name; ?>_stop.on('change', function() {
					wsal<?php echo $label_name; ?>Stop(<?php echo $option_name; ?>_stop, <?php echo $option_name; ?>TxtNot);
				});
			});
		</script>
		<?php
	}
}
