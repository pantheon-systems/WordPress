<?php
/**
 * Import/Export
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class Ocean_Extra_Import_Export {

	/**
	 * Start things up
	 */
	public function __construct() {
		add_action( 'admin_menu', 				array( $this, 'add_page' ), 11 );
		add_action( 'admin_enqueue_scripts',	array( $this, 'css' ) );
		add_action( 'admin_init', 				array( $this, 'register_settings' ) );
		add_action( 'admin_notices', 			array( $this, 'register_notices' ) );
		add_action( 'load-theme-panel_page_oceanwp-panel-import-export', array( $this, 'send_export_file' ) );
		add_action( 'load-theme-panel_page_oceanwp-panel-import-export', array( $this, 'upload_import_file' ) );
	}

	/**
	 * Add sub menu page
	 *
	 * @since 1.0.5
	 */
	public function add_page() {
		add_submenu_page(
			'oceanwp-panel',
			esc_html__( 'Import/Export', 'ocean-extra' ),
			esc_html__( 'Import/Export', 'ocean-extra' ),
			'manage_options',
			'oceanwp-panel-import-export',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Register setting
	 *
	 * @since 1.0.5
	 */
	public static function register_settings() {
		register_setting( 'oceanwp_import_setting', 'oceanwp_import_setting', array( 'OceanWP_Import_Export', 'import_data' ) );
	}

	/**
	 * Register all messages
	 *
	 * @since 1.0.5
	 */
	public static function register_notices() {
		settings_errors( 'oceanwp-import-notices' );
	}

	/**
	 * Send export file to user
	 *
	 * @since 1.0.5
	 */
	public static function send_export_file() {

		// Export requested
		if ( ! empty( $_GET['export'] ) ) {

			$mods		= get_theme_mods();
			$data		= array(
				'mods'	  	=> $mods ? $mods : array(),
				'options' 	=> array()
			);

			// Build filename
			$site_url = site_url( '', 'http' );
			$site_url = trim( $site_url, '/\\' ); // remove trailing slash
			$filename = str_replace( 'http://', '', $site_url ); // remove http://
			$filename = str_replace( array( '/', '\\' ), '-', $filename ); // replace slashes with -
			$filename .= '-oceanwp-export'; // append
			$filename = apply_filters( 'ocean_export_filename', $filename );
				
			foreach ( $mods as $key => $value ) {

				// Don't save widget data.
				if ( 'widget_' === substr( strtolower( $key ), 0, 7 ) ) {
					continue;
				}

				// Don't save sidebar data.
				if ( 'sidebars_' === substr( strtolower( $key ), 0, 9 ) ) {
					continue;
				}

				$data['options'][ $key ] = $value;
			}

			if ( function_exists( 'wp_get_custom_css_post' ) ) {
				$data['wp_css'] = wp_get_custom_css();
			}

			// Set the download headers.
			header( 'Content-disposition: attachment; filename=' . $filename . '.dat' );
			header( 'Content-Type: application/octet-stream; charset=' . get_option( 'blog_charset' ) );

			// Serialize the export data.
			echo serialize( $data );

			// Start the download.
			die();

		}

	}

	/**
	 * Upload import file
	 *
	 * @since 1.0.5
	 */
	public static function upload_import_file() {

		// Check nonce for security since form was posted
		if ( ! empty( $_POST ) && ! empty( $_FILES['oceanwp_import_file'] )
			&& check_admin_referer( 'oceanwp_import', 'oceanwp_import_nonce' ) ) { // check_admin_referer prints fail page and dies

			// Check and move file to uploads dir, get file data
			// Will show die with WP errors if necessary (file too large, quota exceeded, etc.)
			$template	 = get_template();
			$overrides   = array( 'test_form' => false, 'test_type' => false, 'mimes' => array( 'dat' => 'text/plain' ) );
			$file        = wp_handle_upload( $_FILES['oceanwp_import_file'], $overrides );
			if ( isset( $file['error'] ) ) {
				wp_die(
					$file['error'],
					'',
					array( 'back_link' => true )
				);
			}

			// Process import file
			self::process_import_file( $file['file'] );

		}

	}

	/**
	 * Process import file
	 *
	 * @since 1.0.5
	 */
	public static function process_import_file( $file ) {

		// File exists?
		if ( ! file_exists( $file ) ) {
			wp_die(
				esc_html__( 'Import file could not be found. Please try again.', 'ocean-extra' ),
				'',
				array( 'back_link' => true )
			);
		}

		// Get file contents and decode
		$raw  = file_get_contents( $file );
		$data = @unserialize( $raw, [ 'allowed_classes' => false ]  );

		// Delete import file
		unlink( $file );

		// If wp_css is set then import it.
		if ( function_exists( 'wp_update_custom_css_post' ) && isset( $data['wp_css'] ) && '' !== $data['wp_css'] ) {
			wp_update_custom_css_post( $data['wp_css'] );
		}

		// Import data
		self::import_data( $data['mods'] );

	}

	/**
	 * Sanitization callback
	 *
	 * @since 1.0.5
	 */
	public static function import_data( $file ) {

		$msg  = null;
		$type = null;

		// Import the file
		if ( ! empty( $file ) ) {

			if ( '0' == json_last_error() ) {

				// Loop through mods and add them
				foreach ( $file as $mod => $value ) {
					set_theme_mod( $mod, $value );
				}

				// Success message
				$msg  = esc_attr__( 'Settings imported successfully.', 'ocean-extra' );
				$type = 'updated';

			}

			// Display invalid json data error
			else {

				$msg  = esc_attr__( 'Invalid Import Data.', 'ocean-extra' );
				$type = 'error';

			}

		}

		// No json data entered
		else {
			$error_msg = esc_attr__( 'No import data found.', 'ocean-extra' );
			$error_type = 'error';
		}

		// Display notice
		add_settings_error( 'oceanwp-import-notices', esc_attr( 'settings_updated' ), $msg, $type );

		// Return file
		return $file;

	}

	/**
	 * Settings page output
	 *
	 * @since 1.0.5
	 */
	public static function create_admin_page() {

		// Theme branding
		if ( function_exists( 'oceanwp_theme_branding' ) ) {
			$brand = oceanwp_theme_branding();
		} else {
			$brand = 'OceanWP';
		} ?>

		<div class="wrap oceanwp-import-export">

			<h2><?php echo esc_attr( $brand ); ?> <?php esc_html_e( 'Importer & Exporter', 'ocean-extra' ); ?></h2>

			<?php
			// Display notices
			settings_fields( 'oceanwp_import_setting' ); ?>

			<div class="metabox-holder clr">

				<div class="postbox oceanwp-import oceanwp-bloc col-2 clr">

					<h3 class="hndle"><?php esc_html_e( 'Import Settings', 'ocean-extra' ); ?></h3>

					<div class="inside">
						<p><?php echo wp_kses( __( 'Please select a <b>.dat</b> file generated by the export button.', 'ocean-extra' ), array( 'b' => array() ) ); ?></p>

						<form method="post" enctype="multipart/form-data">

							<?php wp_nonce_field( 'oceanwp_import', 'oceanwp_import_nonce' ); ?>

							<input type="file" name="oceanwp_import_file" id="oceanwp-import-file" />

							<p class="submit">
								<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Import Settings', 'ocean-extra' ) ?>" />
							</p>

						</form>

					</div>

				</div>

				<div class="postbox oceanwp-export oceanwp-bloc col-2 second clr">

					<h3 class="hndle"><?php esc_html_e( 'Export Settings', 'ocean-extra' ); ?></h3>

					<div class="inside">
						<p><?php esc_html_e( 'This will export all theme_mods that means if other plugins are adding settings in the customizer it will export those as well.', 'ocean-extra' ); ?></p>

						<p><?php echo wp_kses( __( 'Click below to generate a <b>.dat</b> file for all settings.', 'ocean-extra' ), array( 'b' => array() ) ); ?></p>

						<p class="submit">
							<a href="<?php echo esc_url( admin_url( basename( $_SERVER['PHP_SELF'] ) . '?page=' . $_GET['page'] . '&export=1' ) ); ?>" id="oceanwp-export-button" class="button button-primary"><?php echo esc_html_e( 'Export Settings', 'ocean-extra' ); ?></a>
						</p>

					</div>

				</div>

			</div>

		</div>

	<?php }

	/**
	 * Load css
	 *
	 * @since 1.0.6
	 */
	public static function css( $hook ) {

		// Only load scripts when needed
		if ( OE_ADMIN_PANEL_HOOK_PREFIX . '-import-export' != $hook ) {
			return;
		}

		// CSS
		wp_enqueue_style( 'oceanwp-import-export', plugins_url( '/assets/css/import-export.min.css', __FILE__ ) );

	}

}
new Ocean_Extra_Import_Export();