<?php
/**
 * View: Help
 *
 * WSAL help page.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Help Page.
 *
 * - Plugin Support
 * - Plugin Documentation
 *
 * @package Wsal
 */
class WSAL_Views_Help extends WSAL_AbstractView {

	/**
	 * Method: Get View Title.
	 */
	public function GetTitle() {
		return __( 'Help', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Icon.
	 */
	public function GetIcon() {
		return 'dashicons-sos';
	}

	/**
	 * Method: Get View Name.
	 */
	public function GetName() {
		return __( 'Help', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Weight.
	 */
	public function GetWeight() {
		return 14;
	}

	/**
	 * Method: Get View Header.
	 */
	public function Header() {
		wp_enqueue_style(
			'extensions',
			$this->_plugin->GetBaseUrl() . '/css/extensions.css',
			array(),
			filemtime( $this->_plugin->GetBaseDir() . '/css/extensions.css' )
		);
	}

	/**
	 * Method: Get View.
	 */
	public function Render() {
		?>
		<div class="metabox-holder" style="position: relative;">
			<div class="postbox">
				<div class="inside wsal-block">
					<div class="activity-block">
						<h2><?php esc_html_e( 'Getting Started', 'wp-security-audit-log' ); ?></h2>
						<p>
							<?php esc_html_e( 'Getting started with WP Security Audit Log is really easy; once the plugin is installed it will automatically keep a log of everything that is happening on your website and you do not need to do anything. Watch the video below for a quick overview of the plugin.', 'wp-security-audit-log' ); ?>
						</p>
						<p>
							<iframe class="wsal-youtube-embed" width="560" height="315" src="https://www.youtube.com/embed/1nopATCS-CQ?rel=0" frameborder="0" allowfullscreen></iframe>
						</p>
					</div>
					<!-- /.activity-block -->

					<div class="activity-block">
						<h2><?php esc_html_e( 'Plugin Support', 'wp-security-audit-log' ); ?></h2>
						<p>
							<?php esc_html_e( 'Have you encountered or noticed any issues while using WP Security Audit Log plugin?', 'wp-security-audit-log' ); ?>
							<?php esc_html_e( 'Or you want to report something to us? Click any of the options below to post on the plugin\'s forum or contact our support directly.', 'wp-security-audit-log' ); ?>
						</p><p>
							<a class="button" href="https://wordpress.org/support/plugin/wp-security-audit-log" target="_blank"><?php esc_html_e( 'Free Support Forum', 'wp-security-audit-log' ); ?></a>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<a class="button" href="http://www.wpsecurityauditlog.com/contact/" target="_blank"><?php esc_html_e( 'Free Support Email', 'wp-security-audit-log' ); ?></a>
						</p>
					</div>
					<!-- /.activity-block -->

					<div class="activity-block">
						<h2><?php esc_html_e( 'Plugin Documentation', 'wp-security-audit-log' ); ?></h2>
						<p>
							<?php esc_html_e( 'For more technical information about the WP Security Audit Log plugin please visit the plugin’s knowledge base.', 'wp-security-audit-log' ); ?>
							<?php esc_html_e( 'Refer to the list of WordPress security alerts for a complete list of Alerts and IDs that the plugin uses to keep a log of all the changes in the WordPress audit log.', 'wp-security-audit-log' ); ?>
						</p><p>
							<a class="button" href="http://www.wpsecurityauditlog.com/?utm_source=plugin&amp;utm_medium=helppage&amp;utm_campaign=support" target="_blank"><?php esc_html_e( 'Plugin Website', 'wp-security-audit-log' ); ?></a>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<a class="button" href="https://www.wpsecurityauditlog.com/support-documentation/?utm_source=plugin&amp;utm_medium=helppage&amp;utm_campaign=support" target="_blank"><?php esc_html_e( 'Knowledge Base', 'wp-security-audit-log' ); ?></a>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<a class="button" href="http://www.wpsecurityauditlog.com/documentation/list-monitoring-wordpress-security-alerts-audit-log/?utm_source=plugin&amp;utm_medium=helppage&amp;utm_campaign=support" target="_blank"><?php esc_html_e( 'List of WordPress Security Alerts', 'wp-security-audit-log' ); ?></a>
						</p>
					</div>
					<!-- /.activity-block -->

					<div class="activity-block">
						<h2><?php esc_html_e( 'Rate WP Security Audit Log', 'wp-security-audit-log' ); ?></h2>
						<p>
							<?php esc_html_e( 'We work really hard to deliver a plugin that enables you to keep a record of all the changes that are happening on your WordPress.', 'wp-security-audit-log' ); ?>
							<?php esc_html_e( 'It takes thousands of man-hours every year and endless amount of dedication to research, develop and maintain the free edition of WP Security Audit Log.', 'wp-security-audit-log' ); ?>
							<?php esc_html_e( 'Therefore if you like what you see, and find WP Security Audit Log useful we ask you nothing more than to please rate our plugin.', 'wp-security-audit-log' ); ?>
							<?php esc_html_e( 'We appreciate every star!', 'wp-security-audit-log' ); ?>
						</p>
						<p>
							<a class="rating-link" href="https://en-gb.wordpress.org/plugins/wp-security-audit-log/#reviews" target="_blank">
								<span class="dashicons dashicons-star-filled"></span>
								<span class="dashicons dashicons-star-filled"></span>
								<span class="dashicons dashicons-star-filled"></span>
								<span class="dashicons dashicons-star-filled"></span>
								<span class="dashicons dashicons-star-filled"></span>
							</a>
							<a class="button" href="https://en-gb.wordpress.org/plugins/wp-security-audit-log/#reviews" target="_blank"><?php esc_html_e( 'Rate Plugin', 'wp-security-audit-log' ); ?></a>
						</p>
					</div>
					<!-- /.activity-block -->
				</div>
			</div>

		<?php
		$is_current_view = $this->_plugin->views->GetActiveView() == $this;
		// Check if any of the extensions is activated.
		if ( wsal_freemius()->is_not_paying() ) :
			if ( current_user_can( 'manage_options' ) && $is_current_view ) :
				?>
				<div class="wsal-sidebar-advert">
					<div class="postbox">
						<h3 class="hndl"><span><?php esc_html_e( 'Upgrade to Premium', 'wp-security-audit-log' ); ?></span></h3>
						<div class="inside">
							<ul class="wsal-features-list">
								<li>
									<?php esc_html_e( 'See who is logged in', 'wp-security-audit-log' ); ?><br />
									<?php esc_html_e( 'And remotely terminate sessions', 'wp-security-audit-log' ); ?>
								</li>
								<li>
									<?php esc_html_e( 'Generate reports', 'wp-security-audit-log' ); ?><br />
									<?php esc_html_e( 'Or configure automated email reports', 'wp-security-audit-log' ); ?>
								</li>
								<li>
									<?php esc_html_e( 'Configure email notifications', 'wp-security-audit-log' ); ?><br />
									<?php esc_html_e( 'Get instantly notified of important changes', 'wp-security-audit-log' ); ?>
								</li>
								<li>
									<?php esc_html_e( 'Add Search', 'wp-security-audit-log' ); ?><br />
									<?php esc_html_e( 'Easily track down suspicious behaviour', 'wp-security-audit-log' ); ?>
								</li>
								<li>
									<?php esc_html_e( 'Integrate & Centralise', 'wp-security-audit-log' ); ?><br />
									<?php esc_html_e( 'Export the logs to your centralised logging system', 'wp-security-audit-log' ); ?>
								</li>
							</ul>
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
									'utm_content' => 'update+more+info',
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
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		</div>
		<?php
	}
}
