<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Registers styles and scripts, adds the custom administration page,
 * and processes user input on the "search/replace" form.
 *
 * @link       https://bettersearchreplace.com
 * @since      1.0.0
 *
 * @package    Better_Search_Replace
 * @subpackage Better_Search_Replace/includes
 */

// Prevent direct access.
if ( ! defined( 'BSR_PATH' ) ) exit;

class BSR_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $better_search_replace    The ID of this plugin.
	 */
	private $better_search_replace;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $better_search_replace       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $better_search_replace, $version ) {
		$this->better_search_replace = $better_search_replace;
		$this->version = $version;
	}

	/**
	 * Register any CSS and JS used by the plugin.
	 * @since    1.0.0
	 * @access 	 public
	 * @param    string $hook Used for determining which page(s) to load our scripts.
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'tools_page_better-search-replace' === $hook ) {
			wp_enqueue_style( 'better-search-replace', BSR_URL . 'assets/css/better-search-replace.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'jquery-style', BSR_URL . 'assets/css/jquery-ui.min.css', array(), $this->version, 'all' );
			wp_enqueue_script( 'jquery-ui-slider' );
			wp_enqueue_script( 'better-search-replace', BSR_URL . 'assets/js/better-search-replace.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );

			wp_localize_script( 'better-search-replace', 'bsr_object_vars', array(
				'page_size' 	=> get_option( 'bsr_page_size' ) ? absint( get_option( 'bsr_page_size' ) ) : 20000,
				'endpoint' 		=> BSR_AJAX::get_endpoint(),
				'ajax_nonce' 	=> wp_create_nonce( 'bsr_ajax_nonce' ),
				'no_search' 	=> __( 'No search string was defined, please enter a URL or string to search for.', 'better-search-replace' ),
				'no_tables' 	=> __( 'Please select the tables that you want to update.', 'better-search-replace' ),
				'unknown' 		=> __( 'An error occurred processing your request. Try decreasing the "Max Page Size", or contact support.', 'better-search-replace' ),
				'processing'	=> __( 'Processing...', 'better-search-replace' )
			) );
		}
	}

	/**
	 * Register any menu pages used by the plugin.
	 * @since  1.0.0
	 * @access public
	 */
	public function bsr_menu_pages() {
		$cap = apply_filters( 'bsr_capability', 'install_plugins' );
		add_submenu_page( 'tools.php', __( 'Better Search Replace', 'better-search-replace' ), __( 'Better Search Replace', 'better-search-replace' ), $cap, 'better-search-replace', array( $this, 'bsr_menu_pages_callback' ) );
	}

	/**
	 * The callback for creating a new submenu page under the "Tools" menu.
	 * @access public
	 */
	public function bsr_menu_pages_callback() {
		require_once BSR_PATH . 'templates/bsr-dashboard.php';
	}

	/**
	 * Renders the result or error onto the better-search-replace admin page.
	 * @access public
	 */
	public static function render_result() {

		if ( isset( $_GET['result'] ) && $result = get_transient( 'bsr_results' ) ) {

			if ( isset( $result['dry_run'] ) && $result['dry_run'] === 'on' ) {
				$msg = sprintf( __( '<p><strong>DRY RUN:</strong> <strong>%d</strong> tables were searched, <strong>%d</strong> cells were found that need to be updated, and <strong>%d</strong> changes were made.</p><p><a href="%s" class="thickbox" title="Dry Run Details">Click here</a> for more details, or use the form below to run the search/replace.</p>', 'better-search-replace' ),
					$result['tables'],
					$result['change'],
					$result['updates'],
					get_admin_url() . 'admin-post.php?action=bsr_view_details&TB_iframe=true&width=800&height=500'
				);
			} else {
				$msg = sprintf( __( '<p>During the search/replace, <strong>%d</strong> tables were searched, with <strong>%d</strong> cells changed in <strong>%d</strong> updates.</p><p><a href="%s" class="thickbox" title="Search/Replace Details">Click here</a> for more details.</p>', 'better-search-replace' ),
					$result['tables'],
					$result['change'],
					$result['updates'],
					get_admin_url() . 'admin-post.php?action=bsr_view_details&TB_iframe=true&width=800&height=500'
				);
			}

			echo '<div class="updated">' . $msg . '</div>';

		}

	}

	/**
	 * Prefills the given value on the search/replace page (dry run, live run, from profile).
	 * @access public
	 * @param  string $value The value to check for.
	 * @param  string $type  The type of the value we're filling.
	 */
	public static function prefill_value( $value, $type = 'text' ) {

		// Grab the correct data to prefill.
		if ( isset( $_GET['result'] ) && get_transient( 'bsr_results' ) ) {
			$values = get_transient( 'bsr_results' );
		} else {
			$values = array();
		}

		// Prefill the value.
		if ( isset( $values[$value] ) ) {

			if ( 'checkbox' === $type && 'on' === $values[$value] ) {
				echo 'checked';
			} else {
				echo str_replace( '#BSR_BACKSLASH#', '\\', esc_attr( $values[$value] ) );
			}

		}

	}

	/**
	 * Loads the tables available to run a search replace, prefilling if already
	 * selected the tables.
	 * @access public
	 */
	public static function load_tables() {

		// Get the tables and their sizes.
		$tables 	= BSR_DB::get_tables();
		$sizes 		= BSR_DB::get_sizes();

		echo '<select id="bsr-table-select" name="select_tables[]" multiple="multiple" style="width:25em;">';

		foreach ( $tables as $table ) {

			// Try to get the size for this specific table.
			$table_size = isset( $sizes[$table] ) ? $sizes[$table] : '';

			if ( isset( $_GET['result'] ) && get_transient( 'bsr_results' ) ) {

				$result = get_transient( 'bsr_results' );

				if ( isset( $result['table_reports'][$table] ) ) {
					echo "<option value='$table' selected>$table $table_size</option>";
				} else {
					echo "<option value='$table'>$table $table_size</option>";
				}

			} else {
				echo "<option value='$table'>$table $table_size</option>";
			}

		}

		echo '</select>';

	}

	/**
	 * Loads the result details (via Thickbox).
	 * @access public
	 */
	public function load_details() {

		if ( get_transient( 'bsr_results' ) ) {

			$results 		= get_transient( 'bsr_results' );
			$min 			= ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min';
			$bsr_styles 	= BSR_URL . 'assets/css/better-search-replace.css?v=' . BSR_VERSION;

			?>
			<link href="<?php echo esc_url( get_admin_url( null, 'css/common' . $min . '.css' ) ); ?>" rel="stylesheet" type="text/css" />
			<link href="<?php echo esc_url( $bsr_styles ); ?>" rel="stylesheet" type="text/css">

			<div class="container" style="padding:10px;">

				<table id="bsr-results-table" class="widefat">
					<thead>
						<tr><th class="bsr-first"><?php _e( 'Table', 'better-search-replace' ); ?></th><th class="bsr-second"><?php _e( 'Changes Found', 'better-search-replace' ); ?></th><th class="bsr-third"><?php _e( 'Rows Updated', 'better-search-replace' ); ?></th><th class="bsr-fourth"><?php _e( 'Time', 'better-search-replace' ); ?></th></tr>
					</thead>
					<tbody>
					<?php
						foreach ( $results['table_reports'] as $table_name => $report ) {
							$time = $report['end'] - $report['start'];

							if ( $report['change'] !== 0 ) {
								$report['change'] = '<strong>' . $report['change'] . '</strong>';
							}

							if ( $report['updates'] !== 0 ) {
								$report['updates'] = '<strong>' . $report['updates'] . '</strong>';
							}

							printf(
								'<tr><td class="bsr-first">%s</td><td class="bsr-second">%s</td><td class="bsr-third">%s</td><td class="bsr-fourth">%s %s</td></tr>',
								$table_name,
								$report['change'],
								$report['updates'],
								round( $time, 3 ),
								__( 'seconds', 'better-search-replace' )
							);

						}
					?>
					</tbody>
				</table>

				<p style="text-align:center;"><strong><?php _e( 'Want even more details, easy database migrations, and saved search/replace profiles?', 'better-search-replace' ); ?><br>
				<a href="https://bettersearchreplace.com/?utm_source=insideplugin&utm_medium=web&utm_content=results-modal&utm_campaign=pro-upsell" target="_blank"><?php _e( 'Learn more about the pro version', 'better-search-replace' ); ?></a></strong></p>

			</div>
			<?php
		}
	}

	/**
	 * Registers our settings in the options table.
	 * @access public
	 */
	public function register_option() {
		register_setting( 'bsr_settings_fields', 'bsr_page_size', 'absint' );
	}

	/**
	 * Downloads the system info file for support.
	 * @access public
	 */
	public function download_sysinfo() {
		$cap = apply_filters( 'bsr_capability', 'install_plugins' );
		if ( ! current_user_can( $cap ) ) {
			return;
		}

		nocache_headers();

		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="bsr-system-info.txt"' );

		echo wp_strip_all_tags( $_POST['bsr-sysinfo'] );
		die();
	}

	/**
	 * Displays the link to upgrade to BSR Pro
	 * @access public
	 * @param array $links The links assigned to the plugin.
	 */
	public function meta_upgrade_link( $links, $file ) {
		$plugin = plugin_basename( BSR_FILE );

		if ( $file == $plugin ) {
			return array_merge(
				$links,
				array( '<a href="https://bettersearchreplace.com/?utm_source=insideplugin&utm_medium=web&utm_content=plugins-page&utm_campaign=pro-upsell">' . __( 'Upgrade to Pro', 'better-search-replace' ) . '</a>' )
			);
		}

  		return $links;
	}

}
