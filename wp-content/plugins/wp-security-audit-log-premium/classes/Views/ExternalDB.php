<?php
/**
 * View: External DB Page
 *
 * WSAL external db page.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * External DB Add-On promo Page.
 * Used only if the plugin is not activated.
 *
 * @package Wsal
 */
class WSAL_Views_ExternalDB extends WSAL_AbstractView {

	/**
	 * Method: Get View Title.
	 */
	public function GetTitle() {
		return __( 'External DB Add-On', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Icon.
	 */
	public function GetIcon() {
		return 'dashicons-external';
	}

	/**
	 * Method: Get View Name.
	 */
	public function GetName() {
		return __( 'DB & Integrations &#8682;', 'wp-security-audit-log' );
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
		// Extension Page CSS.
		wp_enqueue_style(
			'extensions',
			$this->_plugin->GetBaseUrl() . '/css/extensions.css',
			array(),
			filemtime( $this->_plugin->GetBaseDir() . '/css/extensions.css' )
		);

		// Swipebox CSS.
		wp_enqueue_style(
			'wsal-swipebox-css',
			$this->_plugin->GetBaseUrl() . '/css/swipebox.min.css',
			array(),
			filemtime( $this->_plugin->GetBaseDir() . '/css/swipebox.min.css' )
		);
	}

	/**
	 * Method: Get View Footer.
	 */
	public function Footer() {
		// jQuery.
		wp_enqueue_script( 'jquery' );

		// Swipebox JS.
		wp_register_script(
			'wsal-swipebox-js',
			$this->_plugin->GetBaseUrl() . '/js/jquery.swipebox.min.js',
			array( 'jquery' ),
			filemtime( $this->_plugin->GetBaseDir() . '/js/jquery.swipebox.min.js' )
		);
		wp_enqueue_script( 'wsal-swipebox-js' );

		// Extensions JS.
		wp_register_script(
			'wsal-extensions-js',
			$this->_plugin->GetBaseUrl() . '/js/extensions.js',
			array( 'wsal-swipebox-js' ),
			filemtime( $this->_plugin->GetBaseDir() . '/js/extensions.js' )
		);
		wp_enqueue_script( 'wsal-extensions-js' );
	}

	/**
	 * Method: Get View.
	 */
	public function Render() {
		?>
		<div class="wrap-advertising-page-single">
			<div class="wsal-row">
				<div class="wsal-col">
					<div class="icon" style='background-image:url("<?php echo esc_url( $this->_plugin->GetBaseUrl() ); ?>/img/database.jpg");'></div>
				</div>
				<!-- /.wsal-col -->

				<div class="wsal-col">
					<h3><?php esc_html_e( 'External DB', 'wp-security-audit-log' ); ?></h3>
					<p>
						<?php esc_html_e( 'Upgrade to Premium to:', 'wp-security-audit-log' ); ?>
					</p>
					<p>
						<ul class="wsal-features-list">
							<li><?php esc_html_e( 'Move the audit log to an external database for improved security & performance,', 'wp-security-audit-log' ); ?></li>
							<li><?php esc_html_e( 'Centralize the audit log in your centralized logging system,', 'wp-security-audit-log' ); ?></li>
							<li><?php esc_html_e( 'Mirror the audit trail to Syslog, Papertrail etc,', 'wp-security-audit-log' ); ?></li>
							<li><?php esc_html_e( 'Configure archiving rules to archive old alerts in an archiving database,', 'wp-security-audit-log' ); ?></li>
							<li><?php esc_html_e( '& more.', 'wp-security-audit-log' ); ?></li>
						</ul>
					</p>
					<?php
					// Buy Now button link.
					$buy_now = add_query_arg( 'page', 'wsal-auditlog-pricing', admin_url( 'admin.php' ) );
					$buy_now_target = '';

					// If user is not super admin and website is multisite then change the URL.
					if ( $this->_plugin->IsMultisite() && ! is_super_admin() ) {
						$buy_now = 'https://www.wpsecurityauditlog.com/pricing/';
						$buy_now_target = 'target="_blank"';
					} elseif ( $this->_plugin->IsMultisite() && is_super_admin() ) {
						$buy_now = add_query_arg( 'page', 'wsal-auditlog-pricing', network_admin_url( 'admin.php' ) );
					} elseif ( ! $this->_plugin->IsMultisite() && ! current_user_can( 'manage_options' ) ) {
						$buy_now = 'https://www.wpsecurityauditlog.com/pricing/';
						$buy_now_target = 'target="_blank"';
					}

					$more_info = add_query_arg(
						array(
							'utm_source' => 'plugin',
							'utm_medium' => 'page',
							'utm_content' => 'db+more+info',
							'utm_campaign' => 'upgrade+premium',
						),
						'https://www.wpsecurityauditlog.com/premium-features/'
					);
					?>
					<p>
						<a class="button-primary wsal-extension-btn" href="<?php echo esc_attr( $buy_now ); ?>" <?php echo esc_attr( $buy_now_target ); ?>><?php esc_html_e( 'Upgrade to Premium', 'wp-security-audit-log' ); ?></a>
						<a class="button-primary wsal-extension-btn" href="<?php echo esc_attr( $more_info ); ?>" target="_blank"><?php esc_html_e( 'More Information', 'wp-security-audit-log' ); ?></a>
					</p>
				</div>
				<!-- /.wsal-col -->
			</div>
			<!-- /.wsal-row -->

			<div class="wsal-row">
				<div class="wsal-col">
					<h3><?php esc_html_e( 'Screenshots', 'wp-security-audit-log' ); ?></h3>

					<p>
						<ul class="wsal-features-list">
							<li>
								<?php esc_html_e( 'Configure an external database so the WordPress audit trail is stored on it instead of the WordPress database.', 'wp-security-audit-log' ); ?><br />
								<a class="swipebox" title="<?php esc_attr_e( 'Configure an external database so the WordPress audit trail is stored on it instead of the WordPress database.', 'wp-security-audit-log' ); ?>"
									href="<?php echo esc_url( $this->_plugin->GetBaseUrl() ); ?>/img/external-db/external_database_1.png">
									<img width="500" src="<?php echo esc_url( $this->_plugin->GetBaseUrl() ); ?>/img/external-db/external_database_1.png">
								</a>
							</li>
							<li>
								<?php esc_html_e( 'Configure mirroring to keep a secondary copy of the WordPress audit trail on Syslog, Papertrail etc.', 'wp-security-audit-log' ); ?><br />
								<a class="swipebox" title="<?php esc_attr_e( 'Configure mirroring to keep a secondary copy of the WordPress audit trail on Syslog, Papertrail etc.', 'wp-security-audit-log' ); ?>"
									href="<?php echo esc_url( $this->_plugin->GetBaseUrl() ); ?>/img/external-db/extermal_database_mirroring_options.png">
									<img width="500" src="<?php echo esc_url( $this->_plugin->GetBaseUrl() ); ?>/img/external-db/extermal_database_mirroring_options.png">
								</a>
							</li>
						</ul>
					</p>

					<p>
					<a class="button-primary wsal-extension-btn" href="<?php echo esc_attr( $buy_now ); ?>" <?php echo esc_attr( $buy_now_target ); ?>><?php esc_html_e( 'Upgrade to Premium', 'wp-security-audit-log' ); ?></a>
						<a class="button-primary wsal-extension-btn" href="<?php echo esc_attr( $more_info ); ?>" target="_blank"><?php esc_html_e( 'More Information', 'wp-security-audit-log' ); ?></a>
					</p>
				</div>
			</div>
			<!-- /.wsal-row -->
		</div>
		<?php
	}
}
