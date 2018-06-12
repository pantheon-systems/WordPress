<?php
/**
 * View: Reports Page
 *
 * WSAL reports page.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reports Add-On promo Page.
 * Used only if the plugin is not activated.
 *
 * @package Wsal
 */
class WSAL_Views_Reports extends WSAL_AbstractView {

	/**
	 * Method: Get View Title.
	 */
	public function GetTitle() {
		return __( 'Reports Add-On', 'wp-security-audit-log' );
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
		return __( 'Reports &#8682;', 'wp-security-audit-log' );
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
					<div class="icon" style='background-image:url("<?php echo esc_url( $this->_plugin->GetBaseUrl() ); ?>/img/file.jpg");'></div>
				</div>
				<!-- /.wsal-col -->

				<div class="wsal-col">
					<h3><?php esc_html_e( 'Reports', 'wp-security-audit-log' ); ?></h3>
					<p>
						<?php esc_html_e( 'Upgrade to Premium to:', 'wp-security-audit-log' ); ?>
					</p>
					<p>
						<ul class="wsal-features-list">
							<li><?php esc_html_e( 'Generate user activity, site (in multisite) and any other type of WordPress reports,', 'wp-security-audit-log' ); ?></li>
							<li><?php esc_html_e( 'Configure automated daily, weekly, monthly & quarterly reports,', 'wp-security-audit-log' ); ?></li>
							<li><?php esc_html_e( 'Receive reports automatically in your email,', 'wp-security-audit-log' ); ?></li>
							<li><?php esc_html_e( 'Generate statistics reports on commonly used IP addresses, views per user, etc,', 'wp-security-audit-log' ); ?></li>
							<li><?php esc_html_e( 'Export reports to HTML and CSV formats,', 'wp-security-audit-log' ); ?></li>
							<li><?php esc_html_e( '& much more.', 'wp-security-audit-log' ); ?></li>
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
							'utm_content' => 'reports+more+info',
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
								<?php esc_html_e( 'Generate any type of report and also configure daily, weekly, monthly and quarterly reports which are automatically sent to you via email.', 'wp-security-audit-log' ); ?><br />
								<a class="swipebox" title="<?php esc_attr_e( 'Generate any type of report and also configure daily, weekly, monthly and quarterly reports which are automatically sent to you via email.', 'wp-security-audit-log' ); ?>"
									href="<?php echo esc_url( $this->_plugin->GetBaseUrl() ); ?>/img/reports/reports_1.png">
									<img width="500" src="<?php echo esc_url( $this->_plugin->GetBaseUrl() ); ?>/img/reports/reports_1.png">
								</a>
							</li>
							<li>
								<?php esc_html_e( 'Generate statistical reports to get a better overview of what users are doing on your WordPress and WordPress multisite network are doing.', 'wp-security-audit-log' ); ?><br />
								<a class="swipebox" title="<?php esc_attr_e( 'Generate statistical reports to get a better overview of what users are doing on your WordPress and WordPress multisite network are doing.', 'wp-security-audit-log' ); ?>"
									href="<?php echo esc_url( $this->_plugin->GetBaseUrl() ); ?>/img/reports/reports_2.png">
									<img width="500" src="<?php echo esc_url( $this->_plugin->GetBaseUrl() ); ?>/img/reports/reports_2.png">
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
