<?php
/**
 * Licenses
 *
 * @package 	Ocean_Extra
 * @category 	Core
 * @author 		OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class Ocean_Extra_Licenses {

	/**
	 * Start things up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ), 99999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Add sub menu page
	 *
	 * @since 1.0.0
	 */
	public function add_page() {
		// If no premium extensions
		if ( true != apply_filters( 'oceanwp_licence_tab_enable', false ) ) {
			return;
		}

		add_submenu_page(
			'oceanwp-panel',
			esc_html__( 'Licenses', 'ocean-extra' ),
			esc_html__( 'Licenses', 'ocean-extra' ),
			'manage_options',
			'oceanwp-panel-licenses',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Admin page
	 *
	 * @since 1.0.0
	 */
	public function create_admin_page() { ?>

		<div class="wrap oceanwp-scripts-panel oceanwp-clr">

			<h1><?php esc_attr_e( 'Licenses Settings', 'ocean-extra' ); ?></h1>

			<p><?php echo sprintf(
				__( 'Enter your extension license keys here to receive updates for purchased extensions. If your license key has expired, please %1$srenew your license%2$s.', 'ocean-extra' ),
				'<a href="http://docs.oceanwp.org/article/26-license-renewal" target="_blank" title="License renewal FAQ">',
				'</a>'
			); ?></p>

			<form id="oceanwp-license-form" method="post" action="options.php">
				<?php settings_fields( 'oceanwp_options' ); ?>

				<?php do_action( 'oceanwp_licenses_tab_top' ); ?>

				<table id="oceanwp-licenses" class="form-table">
					<tbody>
						<?php do_action( 'oceanwp_licenses_tab_fields' ); ?>
					</tbody>
				</table>

				<p class="submit"><input type="submit" name="oceanwp_licensekey_activateall" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'ocean-extra' ); ?>"></p>
			</form>

		</div>

	<?php
	}

	/**
	 * Admin Scripts
	 *
	 * @since 1.0.0
	 */
	public static function admin_scripts( $hook ) {

		// Only load scripts when needed
		if ( OE_ADMIN_PANEL_HOOK_PREFIX . '-licenses' != $hook ) {
			return;
		}

		// CSS
		wp_enqueue_style( 'oceanwp-licenses-panel', plugins_url( '/assets/css/licenses.min.css', __FILE__ ) );

	}
}
new Ocean_Extra_Licenses();