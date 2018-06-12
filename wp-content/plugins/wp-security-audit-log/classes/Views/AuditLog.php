<?php
/**
 * Audit Log View Class
 *
 * Class file for Audit Log View.
 *
 * @since 1.0.0
 * @package wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Audit Log Viewer Page
 *
 * @package Wsal
 */
class WSAL_Views_AuditLog extends WSAL_AbstractView {

	/**
	 * Listing view object (Instance of WSAL_AuditLogListView).
	 *
	 * @var object
	 */
	protected $_listview;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	protected $_version;

	/**
	 * Method: Constructor
	 *
	 * @param WpSecurityAuditLog $plugin - Instance of WpSecurityAuditLog.
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		parent::__construct( $plugin );
		add_action( 'wp_ajax_AjaxInspector', array( $this, 'AjaxInspector' ) );
		add_action( 'wp_ajax_AjaxRefresh', array( $this, 'AjaxRefresh' ) );
		add_action( 'wp_ajax_AjaxSetIpp', array( $this, 'AjaxSetIpp' ) );
		add_action( 'wp_ajax_AjaxSearchSite', array( $this, 'AjaxSearchSite' ) );
		add_action( 'wp_ajax_AjaxSwitchDB', array( $this, 'AjaxSwitchDB' ) );
		add_action( 'wp_ajax_wsal_download_failed_login_log', array( $this, 'wsal_download_failed_login_log' ) );
		add_action( 'wp_ajax_wsal_download_404_log', array( $this, 'wsal_download_404_log' ) );
		add_action( 'all_admin_notices', array( $this, 'AdminNoticesPremium' ) );
		// Check plugin version for to dismiss the notice only until upgrade.
		$this->_version = WSAL_VERSION;
		$this->RegisterNotice( 'premium-wsal-' . $this->_version );
	}

	/**
	 * Method: Add premium extensions notice.
	 *
	 * @author Ashar Irfan
	 * @since  1.0.0
	 */
	public function AdminNoticesPremium() {
		$is_current_view = $this->_plugin->views->GetActiveView() == $this;
		// Check if any of the extensions is activated.
		if ( ! class_exists( 'WSAL_NP_Plugin' )
			&& ! class_exists( 'WSAL_Ext_Plugin' )
			&& ! class_exists( 'WSAL_Rep_Plugin' )
			&& ! class_exists( 'WSAL_SearchExtension' )
			&& ! class_exists( 'WSAL_User_Management_Plugin' ) ) {
			if ( current_user_can( 'manage_options' ) && $is_current_view && ! $this->IsNoticeDismissed( 'premium-wsal-' . $this->_version ) ) { ?>
				<div class="updated wsal_notice" data-notice-name="premium-wsal-<?php echo esc_attr( $this->_version ); ?>">
					<div class="wsal_notice__wrapper">
						<img src="<?php echo esc_url( WSAL_BASE_URL ); ?>img/wsal-logo@2x.png">
						<p>
							<strong><?php esc_html_e( 'See who is logged in to your WordPress, create user productivity reports, get alerted via email of important changes and more!', 'wp-security-audit-log' ); ?></strong><br />
							<?php esc_html_e( 'Unlock these powerful features and much more with the premium edition of WP Security Audit Log.', 'wp-security-audit-log' ); ?>
						</p>
						<!-- /.wsal_notice__wrapper -->
						<div class="wsal_notice__btns">
							<?php
							// Buy Now button link.
							$buy_now = add_query_arg( 'page', 'wsal-auditlog-pricing', admin_url( 'admin.php' ) );

							// If user is not super admin and website is multisite then change the URL.
							if ( $this->_plugin->IsMultisite() && ! is_super_admin() ) {
								$buy_now = 'https://www.wpsecurityauditlog.com/pricing/';
							} elseif ( $this->_plugin->IsMultisite() && is_super_admin() ) {
								$buy_now = add_query_arg( 'page', 'wsal-auditlog-pricing', network_admin_url( 'admin.php' ) );
							}

							$more_info = add_query_arg(
								array(
									'utm_source' => 'plugin',
									'utm_medium' => 'banner',
									'utm_content' => 'audit+log+viewier+more+info',
									'utm_campaign' => 'upgrade+premium',
								),
								'https://www.wpsecurityauditlog.com/premium-features/'
							);
							?>
							<a href="<?php echo esc_url( $buy_now ); ?>" class="button button-primary buy-now"><?php esc_html_e( 'Buy Now', 'wp-security-audit-log' ); ?></a>
							<a href="<?php echo esc_url( $more_info ); ?>" target="_blank"><?php esc_html_e( 'More Information', 'wp-security-audit-log' ); ?></a>
						</div>
						<!-- /.wsal_notice__btns -->
					</div>
				</div>
				<?php
			}
		}
	}

	/**
	 * Method: Check if view has shortcut link.
	 */
	public function HasPluginShortcutLink() {
		return true;
	}

	/**
	 * Method: Get View Title.
	 */
	public function GetTitle() {
		return __( 'Audit Log Viewer', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Icon.
	 */
	public function GetIcon() {
		return $this->_wpversion < 3.8
			? $this->_plugin->GetBaseUrl() . '/img/logo-main-menu.png'
			: $this->_plugin->GetBaseUrl() . '/img/wsal-menu-icon.svg';
	}

	/**
	 * Method: Get View Name.
	 */
	public function GetName() {
		return __( 'Audit Log Viewer', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Weight.
	 */
	public function GetWeight() {
		return 1;
	}

	/**
	 * Method: Get View.
	 */
	protected function GetListView() {
		if ( is_null( $this->_listview ) ) {
			$this->_listview = new WSAL_AuditLogListView( $this->_plugin );
		}
		return $this->_listview;
	}

	/**
	 * Render view table of Audit Log.
	 *
	 * @since  1.0.0
	 */
	public function Render() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'view' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
		}

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		// Verify nonce for security.
		if ( isset( $post_array['_wpnonce'] ) && ! wp_verify_nonce( $post_array['_wpnonce'], 'bulk-logs' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'wp-security-audit-log' ) );
		}

		$this->GetListView()->prepare_items();
		$occ = new WSAL_Models_Occurrence();

		?>
		<form id="audit-log-viewer" method="post">
			<div id="audit-log-viewer-content">
				<input type="hidden" name="page" value="<?php echo filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ); ?>" />
				<input type="hidden" id="wsal-cbid" name="wsal-cbid" value="<?php echo esc_attr( isset( $post_array['wsal-cbid'] ) ? $post_array['wsal-cbid'] : '0' ); ?>" />
				<?php do_action( 'wsal_auditlog_before_view', $this->GetListView() ); ?>
				<?php $this->GetListView()->display(); ?>
				<?php do_action( 'wsal_auditlog_after_view', $this->GetListView() ); ?>
			</div>
		</form>

		<?php
		if ( class_exists( 'WSAL_SearchExtension' ) &&
			( isset( $post_array['Filters'] ) || ( isset( $post_array['s'] ) && trim( $post_array['s'] ) ) ) ) :
			?>
			<script type="text/javascript">
				jQuery(document).ready( function() {
					WsalAuditLogInit(
						<?php
						echo json_encode(
							array(
								'ajaxurl'   => admin_url( 'admin-ajax.php' ),
								'tr8n'      => array(
									'numofitems' => __( 'Please enter the number of alerts you would like to see on one page:', 'wp-security-audit-log' ),
									'searchback' => __( 'All Sites', 'wp-security-audit-log' ),
									'searchnone' => __( 'No Results', 'wp-security-audit-log' ),
								),
								'autorefresh'   => array(
									'enabled'   => false,
									'token'     => (int) $occ->Count(),
								),
							)
						);
						?>
					);
				} );
			</script>
		<?php else : ?>
			<script type="text/javascript">
				jQuery(document).ready( function() {
					WsalAuditLogInit(
						<?php
						echo json_encode(
							array(
								'ajaxurl' => admin_url( 'admin-ajax.php' ),
								'tr8n' => array(
									'numofitems' => __( 'Please enter the number of alerts you would like to see on one page:', 'wp-security-audit-log' ),
									'searchback' => __( 'All Sites', 'wp-security-audit-log' ),
									'searchnone' => __( 'No Results', 'wp-security-audit-log' ),
								),
								'autorefresh' => array(
									'enabled' => $this->_plugin->settings->IsRefreshAlertsEnabled(),
									'token' => (int) $occ->Count(),
								),
							)
						);
						?>
					);
				} );
			</script>
		<?php
		endif;
	}

	/**
	 * Ajax callback to display meta data inspector.
	 */
	public function AjaxInspector() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'view' ) ) {
			die( 'Access Denied.' );
		}

		// Filter $_GET array for security.
		$get_array = filter_input_array( INPUT_GET );

		if ( ! isset( $get_array['occurrence'] ) ) {
			die( 'Occurrence parameter expected.' );
		}
		$occ = new WSAL_Models_Occurrence();
		$occ->Load( 'id = %d', array( (int) $get_array['occurrence'] ) );

		echo '<!DOCTYPE html><html><head>';
		echo '<link rel="stylesheet" id="open-sans-css" href="' . esc_url( $this->_plugin->GetBaseUrl() ) . '/css/nice_r.css" type="text/css" media="all">';
		echo '<script type="text/javascript" src="' . esc_url( $this->_plugin->GetBaseUrl() ) . '/js/nice_r.js"></script>';
		echo '<style type="text/css">';
		echo 'html, body { margin: 0; padding: 0; }';
		echo '.nice_r { position: absolute; padding: 8px; }';
		echo '.nice_r a { overflow: visible; }';
		echo '</style>';
		echo '</head><body>';
		$nicer = new WSAL_Nicer( $occ->GetMetaArray() );
		$nicer->render();
		echo '</body></html>';
		die;
	}

	/**
	 * Ajax callback to refrest the view.
	 */
	public function AjaxRefresh() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'view' ) ) {
			die( 'Access Denied.' );
		}

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( ! isset( $post_array['logcount'] ) ) {
			die( 'Log count parameter expected.' );
		}

		$old = (int) $post_array['logcount'];
		$max = 40; // 40*500msec = 20sec

		$is_archive = false;
		if ( $this->_plugin->settings->IsArchivingEnabled() ) {
			$selected_db = get_transient( 'wsal_wp_selected_db' );
			if ( $selected_db && 'archive' == $selected_db ) {
				$is_archive = true;
			}
		}

		do {
			$occ = new WSAL_Models_Occurrence();
			$new = $occ->Count();
			usleep( 500000 ); // 500msec
		} while ( ($old == $new) && (--$max > 0) );

		if ( $is_archive ) {
			echo 'false';
		} else {
			echo $old == $new ? 'false' : esc_html( $new );
		}
		die;
	}

	/**
	 * Ajax callback to set number of alerts to
	 * show on a single page.
	 */
	public function AjaxSetIpp() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'view' ) ) {
			die( 'Access Denied.' );
		}

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( ! isset( $post_array['count'] ) ) {
			die( 'Count parameter expected.' );
		}
		$this->_plugin->settings->SetViewPerPage( (int) $post_array['count'] );
		die;
	}

	/**
	 * Ajax callback to search.
	 */
	public function AjaxSearchSite() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'view' ) ) {
			die( 'Access Denied.' );
		}

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( ! isset( $post_array['search'] ) ) {
			die( 'Search parameter expected.' );
		}
		$grp1 = array();
		$grp2 = array();

		$search = $post_array['search'];

		foreach ( $this->GetListView()->get_sites() as $site ) {
			if ( stripos( $site->blogname, $search ) !== false ) {
				$grp1[] = $site;
			} elseif ( stripos( $site->domain, $search ) !== false ) {
				$grp2[] = $site;
			}
		}
		die( json_encode( array_slice( $grp1 + $grp2, 0, 7 ) ) );
	}

	/**
	 * Ajax callback to switch database.
	 */
	public function AjaxSwitchDB() {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['selected_db'] ) ) {
			set_transient( 'wsal_wp_selected_db', $post_array['selected_db'], HOUR_IN_SECONDS );
		}
	}

	/**
	 * Ajax callback to download failed login log.
	 */
	public function wsal_download_failed_login_log() {
		// Get post array through filter.
		$download_nonce = filter_input( INPUT_POST, 'download_nonce', FILTER_SANITIZE_STRING );
		$alert_id = filter_input( INPUT_POST, 'alert_id', FILTER_SANITIZE_NUMBER_INT );

		// Verify nonce.
		if ( ! empty( $download_nonce ) && wp_verify_nonce( $download_nonce,  'wsal-download-failed-logins' ) ) {
			// Get alert by id.
			$alert = new WSAL_Models_Occurrence();
			$alert->id = (int) $alert_id;

			// Get users using alert meta.
			$users = $alert->GetMetaValue( 'Users', array() );

			// Check if there are any users.
			if ( ! empty( $users ) && is_array( $users ) ) {
				// Prepare content.
				$content = implode( ',', $users );
				echo esc_html( $content );
			} else {
				echo esc_html__( 'No users found.', 'wp-security-audit-log' );
			}
		} else {
			echo esc_html__( 'Nonce verification failed.', 'wp-security-audit-log' );
		}
		die();
	}

	/**
	 * Ajax callback to download 404 log.
	 */
	public function wsal_download_404_log() {
		// Get post array through filter.
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
		$filename = filter_input( INPUT_POST, 'log_file', FILTER_SANITIZE_STRING );
		$site_id = filter_input( INPUT_POST, 'site_id', FILTER_SANITIZE_NUMBER_INT );

		// If file name is empty then return error.
		if ( empty( $filename ) ) {
			// Nonce verification failed.
			echo wp_json_encode( array(
				'success' => false,
				'message' => esc_html__( 'Log file does not exist.', 'wp-security-audit-log' ),
			) );
			die();
		}

		// Verify nonce.
		if ( ! empty( $filename ) && ! empty( $nonce ) && wp_verify_nonce( $nonce,  'wsal-download-404-log-' . $filename ) ) {
			// Set log file path.
			$uploads_dir   = wp_upload_dir();

			if ( ! $site_id ) {
				$position = strpos( $filename, '/sites/' );

				if ( $position ) {
					$filename = substr( $filename, $position );
				} else {
					$position = strpos( $filename, '/wp-security-audit-log/' );
					$filename = substr( $filename, $position );
				}
				$log_file_path = trailingslashit( $uploads_dir['basedir'] ) . $filename;
			} else {
				$position = strpos( $filename, '/wp-security-audit-log/' );
				$filename = substr( $filename, $position );
				$log_file_path = trailingslashit( $uploads_dir['basedir'] ) . $filename;
			}

			// Request the file.
			$response = file_get_contents( $log_file_path, true );

			// Check if the response is valid.
			if ( $response ) {
				// Return the file body.
				echo wp_json_encode( array(
					'success' => true,
					'filename' => $filename,
					'file_content' => $response,
				) );
			} else {
				// Request failed.
				echo wp_json_encode( array(
					'success' => false,
					'message' => esc_html__( 'Request to get log file failed.', 'wp-security-audit-log' ),
				) );
			}
		} else {
			// Nonce verification failed.
			echo wp_json_encode( array(
				'success' => false,
				'message' => esc_html__( 'Nonce verification failed!', 'wp-security-audit-log' ),
			) );
		}
		die();
	}

	/**
	 * Method: Render header of the view.
	 */
	public function Header() {
		add_thickbox();

		// Darktooltip styles.
		wp_enqueue_style(
			'darktooltip',
			$this->_plugin->GetBaseUrl() . '/css/darktooltip.css',
			array(),
			'0.4.0'
		);

		// Audit log styles.
		wp_enqueue_style(
			'auditlog',
			$this->_plugin->GetBaseUrl() . '/css/auditlog.css',
			array(),
			filemtime( $this->_plugin->GetBaseDir() . '/css/auditlog.css' )
		);
	}

	/**
	 * Method: Render footer of the view.
	 */
	public function Footer() {
		wp_enqueue_script( 'jquery' );

		// Darktooltip js.
		wp_enqueue_script(
			'darktooltip', // Identifier.
			$this->_plugin->GetBaseUrl() . '/js/jquery.darktooltip.js', // Script location.
			array( 'jquery' ), // Depends on jQuery.
			'0.4.0' // Script version.
		);

		wp_enqueue_script( 'suggest' );

		// Audit log script.
		wp_enqueue_script(
			'auditlog',
			$this->_plugin->GetBaseUrl() . '/js/auditlog.js',
			array(),
			filemtime( $this->_plugin->GetBaseDir() . '/js/auditlog.js' )
		);
	}
}
