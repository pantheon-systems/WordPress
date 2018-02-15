<?php
/**
 * Widget Manager
 *
 * Plugin Widget used in the WordPress Dashboard.
 *
 * @package Wsal
 */
class WSAL_WidgetManager {

	/**
	 * Instance of WpSecurityAuditLog.
	 *
	 * @var WpSecurityAuditLog
	 */
	protected $_plugin;

	/**
	 * Method: Constructor.
	 *
	 * @param WpSecurityAuditLog $plugin - Instance of WpSecurityAuditLog.
	 */
	public function __construct( WpSecurityAuditLog $plugin ) {
		$this->_plugin = $plugin;
		add_action( 'wp_dashboard_setup', array( $this, 'AddWidgets' ) );
	}

	/**
	 * Method: Add widgets.
	 */
	public function AddWidgets() {
		if ( $this->_plugin->settings->IsWidgetsEnabled()
		&& $this->_plugin->settings->CurrentUserCan( 'view' ) ) {
			wp_add_dashboard_widget(
				'wsal',
				__( 'Latest Alerts', 'wp-security-audit-log' ) . ' | WP Security Audit Log',
				array( $this, 'RenderWidget' )
			);
		}
	}

	/**
	 * Method: Render widget.
	 */
	public function RenderWidget() {
		$query = new WSAL_Models_OccurrenceQuery();

		$bid = (int) $this->get_view_site_id();
		if ( $bid ) {
			$query->addCondition( 'site_id = %s ', $bid );
		}
		$query->addOrderBy( 'created_on', true );
		$query->setLimit( $this->_plugin->settings->GetDashboardWidgetMaxAlerts() );
		$results = $query->getAdapter()->Execute( $query );

		?><div>
		<?php
		if ( ! count( $results ) ) {
			?>
				<p><?php esc_html_e( 'No alerts found.', 'wp-security-audit-log' ); ?></p>
				<?php
		} else {
			?>
			<table class="wp-list-table widefat" cellspacing="0" cellpadding="0"
			   style="display: block; overflow-x: auto;">
				<thead>
					<th class="manage-column" style="width: 15%;" scope="col"><?php esc_html_e( 'User', 'wp-security-audit-log' ); ?></th>
					<th class="manage-column" style="width: 85%;" scope="col"><?php esc_html_e( 'Description', 'wp-security-audit-log' ); ?></th>
				</thead>
				<tbody>
					<?php
					$url = 'admin.php?page=' . $this->_plugin->views->views[0]->GetSafeViewName();
					$fmt = array( new WSAL_AuditLogListView( $this->_plugin ), 'meta_formatter' );
					foreach ( $results as $entry ) {
						?>
						<tr>
							<td>
								<?php
								echo ($un = $entry->GetUsername()) ? esc_html( $un ) : '<i>unknown</i>';
								?>
							</td>
							<td>
								<a href="<?php echo esc_url( $url ) . '#Event' . esc_attr( $entry->id ); ?>">
									<?php echo wp_kses( $entry->GetMessage( $fmt ), $this->_plugin->allowed_html_tags ); ?>
								</a>
							</td>
						</tr>
						<?php
					}
				?>
					</tbody>
			</table>
			<?php
		}
		?>
		</div>
		<?php
	}

	/**
	 * Method: Get view site id.
	 */
	protected function get_view_site_id() {
		if ( is_super_admin() ) {
			return 0;
		} else {
			return get_current_blog_id();
		}
	}
}
