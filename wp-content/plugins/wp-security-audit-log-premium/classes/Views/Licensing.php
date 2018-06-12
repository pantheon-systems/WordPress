<?php
/**
 * View: Licensing
 *
 * WSAL licensing page.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class: Licensing View.
 *
 * Licensing Page for all the Add-Ons enabled.
 *
 * @package Wsal
 */
class WSAL_Views_Licensing extends WSAL_AbstractView {

	/**
	 * Method: Get View Title.
	 */
	public function GetTitle() {
		return __( 'Licensing', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Icon.
	 */
	public function GetIcon() {
		return 'dashicons-cart';
	}

	/**
	 * Method: Get View Name.
	 */
	public function GetName() {
		return __( 'Licensing', 'wp-security-audit-log' );
	}

	/**
	 * Method: Get View Weight.
	 */
	public function GetWeight() {
		return 13;
	}

	/**
	 * Check if the view is accessible.
	 */
	public function IsAccessible() {
		return ! ! $this->_plugin->licensing->CountPlugins();
	}

	/**
	 * Method: Save.
	 */
	protected function Save() {
		// Clear licenses.
		$this->_plugin->settings->ClearLicenses();

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		// Save and activate license.
		if ( isset( $post_array['license'] ) ) {
			foreach ( $post_array['license'] as $name => $key ) {
				$this->_plugin->licensing->ActivateLicense( $name, $key );
			}
		}
	}

	/**
	 * Method: Get View.
	 */
	public function Render() {
		if ( ! $this->_plugin->settings->CurrentUserCan( 'edit' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-security-audit-log' ) );
		}

		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		// Verify nonce for security.
		if ( isset( $post_array['_wpnonce'] ) && ! wp_verify_nonce( $post_array['_wpnonce'], 'wsal-licensing' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'wp-security-audit-log' ) );
		}

		if ( isset( $post_array['submit'] ) ) {
			try {
				$this->Save();
				?><div class="updated"><p><?php esc_html_e( 'Settings have been saved.', 'wp-security-audit-log' ); ?></p></div>
				<?php
			} catch ( Exception $ex ) {
				?>
				<div class="error">
					<p><?php esc_html_e( 'Error: ', 'wp-security-audit-log' ); ?><?php echo esc_html( $ex->getMessage() ); ?></p>
				</div>
				<?php
			}
		}
		?>
		<form id="audit-log-licensing" method="post">
			<input type="hidden" name="page" value="<?php echo filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ); ?>" />
			<?php wp_nonce_field( 'wsal-licensing' ); ?>
			<table class="wp-list-table widefat fixed">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Plugin', 'wp-security-audit-log' ); ?></th>
						<th><?php esc_html_e( 'License', 'wp-security-audit-log' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php $counter = 0; ?>
					<?php foreach ( $this->_plugin->licensing->Plugins() as $name => $plugin ) : ?>
						<?php $license_key = trim( $this->_plugin->settings->GetLicenseKey( $name ) ); ?>
						<?php $license_status = trim( $this->_plugin->settings->GetLicenseStatus( $name ) ); ?>
						<?php $license_errors = trim( $this->_plugin->settings->GetLicenseErrors( $name ) ); ?>
						<tr class="<?php echo ( 0 === $counter++ % 2 ) ? 'alternate' : ''; ?>">
							<td>
								<a href="<?php echo esc_attr( $plugin['PluginData']['PluginURI'] ); ?>" target="_blank">
									<?php echo esc_html( $plugin['PluginData']['Name'] ); ?>
								</a><br/><small><b>
									<?php esc_html_e( 'Version', 'wp-security-audit-log' ); ?>
									<?php echo esc_html( $plugin['PluginData']['Version'] ); ?>
								</b></small>
							</td>
							<td>
								<input type="text" style="width: 100%; margin: 6px 0;"
									   name="license[<?php echo esc_attr( $name ); ?>]"
									   value="<?php echo esc_attr( $license_key ); ?>"/>
							</td>
							<td style="vertical-align: middle;">
								<?php if ( $license_key ) : ?>
									<?php if ( 'valid' === $license_status ) : ?>
										<?php esc_html_e( 'Active', 'wp-security-audit-log' ); ?>
									<?php else : ?>
										<?php esc_html_e( 'Inactive', 'wp-security-audit-log' ); ?><br/>
										<small><?php echo esc_html( $license_errors ); ?></small>
									<?php endif; ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<th><?php esc_html_e( 'Plugin', 'wp-security-audit-log' ); ?></th>
						<th><?php esc_html_e( 'License', 'wp-security-audit-log' ); ?></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
			<?php submit_button(); ?>
		</form>
		<?php
	}
}
