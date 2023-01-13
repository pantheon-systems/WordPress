<?php
/**
 * Theme Panel
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
class Ocean_Extra_Theme_Panel {

	/**
	 * Start things up
	 */
	public function __construct() {

		// Display notice if the Sticky Header is not activated
		add_action( 'admin_notices', array( 'Ocean_Extra_Theme_Panel', 'sticky_notice' ) );
		add_action( 'admin_init', array( 'Ocean_Extra_Theme_Panel', 'dismiss_sticky_notice' ) );
		add_action( 'admin_enqueue_scripts', array( 'Ocean_Extra_Theme_Panel', 'sticky_notice_css' ) );

		// Add panel menu
		add_action( 'admin_menu', array( 'Ocean_Extra_Theme_Panel', 'add_page' ), 0 );

		// Add panel submenu
		add_action( 'admin_menu', array( 'Ocean_Extra_Theme_Panel', 'add_menu_subpage' ) );

		// Add custom CSS for the theme panel
		add_action( 'admin_enqueue_scripts', array( 'Ocean_Extra_Theme_Panel', 'css' ) );

		// Register panel settings
		add_action( 'admin_init', array( 'Ocean_Extra_Theme_Panel', 'register_settings' ) );
		

		// Load addon files
		self::load_addons();
	}

	/**
	 * Display notice if the Sticky Header is not activated
	 *
	 * @since 1.4.12
	 */
	public static function sticky_notice() {
		global $pagenow;
		global $owp_fs;
		$need_to_upgrade = ! empty( $owp_fs ) ? $owp_fs->is_pricing_page_visible() : false;

		if ( ! $need_to_upgrade
			|| '1' === get_option( 'owp_dismiss_sticky_notice' )
			|| true == apply_filters( 'oceanwp_licence_tab_enable', false ) 
			|| ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// Display on the plugins and Theme Panel pages
		if ( 'plugins.php' == $pagenow || ( 'admin.php' == $pagenow && 'oceanwp' == $_GET['page'] ) ) {
			wp_enqueue_style( 'oe-admin-notice', plugins_url( '/assets/css/notice.min.css', __FILE__ ) );

			$dismiss = wp_nonce_url( add_query_arg( 'owp_sticky_notice', 'dismiss_btn' ), 'dismiss_btn' );
			?>

			<div class="notice notice-success ocean-extra-notice owp-sticky-notice">
				<div class="notice-inner">
					<span class="icon-side">
						<span class="owp-notification-icon">
							<img src="<?php echo esc_attr ( OE_URL . 'includes/themepanel/assets/img/themepanel-icon.svg'); ?>">
						</span>
					</span>
					<div class="notice-content">
					<h2><?php echo esc_html__( 'Lovely jubbly! Your website is starting to look fabulous!','ocean-extra' ); ?></h2>
					<h3 class="notice-subheading">
					<?php
					echo sprintf(
						esc_html__( 'But you know what would make your website look stunning and leave your visitors in awe? The  %1$sOcean Core Extensions Bundle%2$s features.', 'ocean-extra' ),
						'<a href="https://oceanwp.org/core-extensions-bundle/" target="_blank">',
						'</a>'
					);
					?>
					</h3>
					<p><?php echo esc_html__( 'You\'ll get:', 'ocean-extra' ); ?></p>

							<ul>
								<li> <?php echo esc_html__('access to premium website template demos,','ocean-extra' ); ?> </li>
								<li> <?php echo esc_html__('sticky header,','ocean-extra' ); ?> </li>
								<li> <?php echo esc_html__('royalty free images and icons with templates,','ocean-extra' ); ?> </li>
								<li> <?php echo esc_html__('Elementor widgets','ocean-extra' ); ?> </li>
								<li> <?php echo esc_html__('Gutenberg blocks,','ocean-extra' ); ?> </li>
								<li> <?php echo esc_html__('images and icons library,','ocean-extra' ); ?> </li>
								<li> <?php echo esc_html__('and so much more.','ocean-extra' ); ?> </li>
							</ul>
						<p><a href="<?php echo esc_url('https://oceanwp.org/core-extensions-bundle/' ); ?>" class="btn button-primary" target="_blank"><span class="dashicons dashicons-external"></span><span><?php _e( 'Yes! I want the Upgrade', 'ocean-extra' ); ?></span></a></p>
					</div>
					<a href="<?php echo $dismiss; ?>" class="dismiss"><span class="dashicons dashicons-dismiss"></span></a>
				</div>
			</div>

			<?php
		}
	}

	/**
	 * Dismiss Sticky Header admin notice
	 *
	 * @since 1.4.12
	 */
	public static function dismiss_sticky_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( ! isset( $_GET['owp_sticky_notice'] ) ) {
			return;
		}

		if ( 'dismiss_btn' === $_GET['owp_sticky_notice'] ) {
			check_admin_referer( 'dismiss_btn' );
			update_option( 'owp_dismiss_sticky_notice', '1' );
		}

		wp_redirect( remove_query_arg( 'owp_sticky_notice' ) );
		exit;
	}

	/**
	 * Sticky Header CSS
	 *
	 * @since 1.4.19
	 */
	public static function sticky_notice_css( $hook ) {
		global $pagenow;
		global $owp_fs;
		$need_to_upgrade = ! empty( $owp_fs ) ? $owp_fs->is_pricing_page_visible() : false;

		if ( ! $need_to_upgrade
			|| '1' === get_option( 'owp_dismiss_sticky_notice' ) 
			|| true == apply_filters( 'oceanwp_licence_tab_enable', false ) 
			|| ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( 'toplevel_page_oceanwp' != $hook && 'plugins.php' != $pagenow ) {
			return;
		}

		// CSS
		wp_enqueue_style( 'oe-rating-notice', plugins_url( '/assets/css/notice.min.css', __FILE__ ) );
	}

	/**
	 * Return customizer panels
	 *
	 * @since 1.0.8
	 */
	private static function get_panels() {

		$panels = array(
			'oe_general_panel'        => array(
				'label' => esc_html__( 'General Panel', 'ocean-extra' ),
			),
			'oe_typography_panel'     => array(
				'label' => esc_html__( 'Typography Panel', 'ocean-extra' ),
			),
			'oe_topbar_panel'         => array(
				'label' => esc_html__( 'Top Bar Panel', 'ocean-extra' ),
			),
			'oe_header_panel'         => array(
				'label' => esc_html__( 'Header Panel', 'ocean-extra' ),
			),
			'oe_blog_panel'           => array(
				'label' => esc_html__( 'Blog Panel', 'ocean-extra' ),
			),
			'oe_sidebar_panel'        => array(
				'label' => esc_html__( 'Sidebar Panel', 'ocean-extra' ),
			),
			'oe_footer_widgets_panel' => array(
				'label' => esc_html__( 'Footer Widgets Panel', 'ocean-extra' ),
			),
			'oe_footer_bottom_panel'  => array(
				'label' => esc_html__( 'Footer Bottom Panel', 'ocean-extra' ),
			),
			'oe_custom_code_panel'    => array(
				'label' => esc_html__( 'Custom CSS/JS Panel', 'ocean-extra' ),
			),
		);

		// Apply filters and return
		return apply_filters( 'oe_theme_panels', $panels );
	}

	/**
	 * Return customizer options
	 *
	 * @since 1.0.8
	 */
	private static function get_options() {

		$options = array(
			'custom_logo'            => array(
				'label' => esc_html__( 'Upload your logo', 'ocean-extra' ),
				'desc'  => esc_html__( 'Add your own logo and retina logo used for retina screens.', 'ocean-extra' ),
			),
			'site_icon'              => array(
				'label' => esc_html__( 'Add your favicon', 'ocean-extra' ),
				'desc'  => esc_html__( 'The favicon is used as a browser and app icon for your website.', 'ocean-extra' ),
			),
			'ocean_primary_color'    => array(
				'label' => esc_html__( 'Choose your primary color', 'ocean-extra' ),
				'desc'  => esc_html__( 'Replace the default primary and hover color by your own colors.', 'ocean-extra' ),
			),
			'ocean_typography_panel' => array(
				'label' => esc_html__( 'Choose your typography', 'ocean-extra' ),
				'desc'  => esc_html__( 'Choose your own typography for any parts of your website.', 'ocean-extra' ),
				'panel' => true,
			),
			'ocean_top_bar'          => array(
				'label' => esc_html__( 'Top bar options', 'ocean-extra' ),
				'desc'  => esc_html__( 'Enable/Disable the top bar, add your own paddings and colors.', 'ocean-extra' ),
			),
			'ocean_header_style'     => array(
				'label' => esc_html__( 'Header options', 'ocean-extra' ),
				'desc'  => esc_html__( 'Choose the style, the height and the colors for your site header.', 'ocean-extra' ),
			),
			'ocean_footer_widgets'   => array(
				'label' => esc_html__( 'Footer widgets options', 'ocean-extra' ),
				'desc'  => esc_html__( 'Choose the columns number, paddings and colors for the footer widgets.', 'ocean-extra' ),
			),
			'ocean_footer_bottom'    => array(
				'label' => esc_html__( 'Footer bottom options', 'ocean-extra' ),
				'desc'  => esc_html__( 'Add your copyright, paddings and colors for the footer bottom.', 'ocean-extra' ),
			),
		);

		// Apply filters and return
		return apply_filters( 'oe_customizer_options', $options );
	}

	/**
	 * Registers a new menu page
	 *
	 * @since 1.0.0
	 */
	public static function add_page() {
		add_menu_page(
			esc_html__( 'Theme Panel', 'ocean-extra' ),
			'Theme Panel', // This menu cannot be translated because it's used for the $hook prefix
			apply_filters( 'ocean_theme_panel_capabilities', 'manage_options' ),
			'oceanwp-panel',
			'',
			'dashicons-admin-generic',
			null
		);
		// here can be some condition
		remove_menu_page( 'oceanwp-panel' );
	}

	/**
	 * Registers a new submenu page
	 *
	 * @since 1.0.0
	 */
	public static function add_menu_subpage() {
		add_submenu_page(
			'oceanwp-general',
			esc_html__( 'General', 'ocean-extra' ),
			esc_html__( 'General', 'ocean-extra' ),
			apply_filters( 'ocean_theme_panel_capabilities', 'manage_options' ),
			'oceanwp-panel',
			array( 'Ocean_Extra_Theme_Panel', 'create_admin_page' )
		);
	}

	/**
	 * Register a setting and its sanitization callback.
	 *
	 * @since 1.0.0
	 */
	public static function register_settings() {
		register_setting( 'oe_panels_settings', 'oe_panels_settings', array( 'Ocean_Extra_Theme_Panel', 'validate_panels' ) );
		register_setting( 'oceanwp_options', 'oceanwp_options', array( 'Ocean_Extra_Theme_Panel', 'admin_sanitize_license_options' ) );
	}

	/**
	 * Validate Settings Options
	 *
	 * @since 1.0.0
	 */
	public static function admin_sanitize_license_options( $input ) {
		if ( current_user_can( 'manage_options' ) ) {
			// filter to save all settings to database
			$oceanwp_options = get_option( 'oceanwp_options' );
			if ( isset( $input['licenses'] ) && ! empty( $input['licenses'] ) ) {
				foreach ( $input['licenses'] as $key => $value ) {
					if ( $oceanwp_options['licenses'][ $key ] ) {
						if ( strpos( $value, 'XXX' ) !== false && isset( $oceanwp_options['licenses'][ $key ] ) ) {
							$input['licenses'][ $key ] = $oceanwp_options['licenses'][ $key ];
						}
					}
				}
			}

			return $input;
		}
	}

	/**
	 * Main Sanitization callback
	 *
	 * @since 1.2.2
	 */
	public static function validate_panels( $settings ) {

		// Get panels array
		$panels = self::get_panels();
		if ( current_user_can( 'manage_options' ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'oe_panels_settings-options' ) ) {

			foreach ( $panels as $key => $val ) {

				$settings[ $key ] = ! empty( $settings[ $key ] ) ? true : false;
			}
		}

		// Return the validated/sanitized settings
		return $settings;
	}

	/**
	 * Get settings.
	 *
	 * @since 1.2.2
	 */
	public static function get_setting( $option = '' ) {

		$defaults = self::get_default_settings();

		$settings = wp_parse_args( get_option( 'oe_panels_settings', $defaults ), $defaults );

		return isset( $settings[ $option ] ) ? $settings[ $option ] : false;
	}

	/**
	 * Get default settings value.
	 *
	 * @since 1.2.2
	 */
	public static function get_default_settings() {

		// Get panels array
		$panels = self::get_panels();

		// Add array
		$default = array();

		foreach ( $panels as $key => $val ) {
			$default[ $key ] = 1;
		}

		// Return
		return apply_filters( 'oe_default_panels', $default );
	}

	/**
	 * Settings page sidebar
	 *
	 * @since 1.4.0
	 */
	public static function admin_page_sidebar() {

		// Image url
		$facebook = OE_URL . 'includes/panel/assets/img/facebook.svg';

		// Bundle link
		$bundle_link = 'https://oceanwp.org/core-extensions-bundle/?utm_source=dash&utm_medium=theme-panel&utm_campaign=bundle';

		// If bundle box
		$class = '';
		if ( true != apply_filters( 'oceanwp_licence_tab_enable', false ) ) {
			$class = ' has-bundle';
		}

		// Setup Wizard button
		if ( ! get_option( 'owp_wizard' ) ) {
			?>

			<div class="oceanwp-wizard">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=owp_setup' ) ); ?>" class="button owp-button"><?php esc_html_e( 'Run the Setup Wizard', 'ocean-extra' ); ?></a>
			</div>

			<?php
		}

		// if no premium extensions activated
		if ( true != apply_filters( 'oceanwp_licence_tab_enable', false ) ) {
			?>

			<div class="oceanwp-bloc oceanwp-bundle">
				<p class="owp-text">
					<a href="<?php echo esc_url( $bundle_link ); ?>" class="logo-text" target="_blank">OceanWP<span class="circle"></span></a>
				</p>
				<div class="content-wrap">
					<p class="content">
					<?php
					echo sprintf( esc_html__( 'Take your website to the next level.%1$sGain access to all premium extensions with a single purchase. %2$sClick here%3$s for more information.', 'ocean-extra' ), '<br>', '<a href="' . esc_url( $bundle_link ) . '" target="_blank">', '</a>' );
					?>
			</p>
					<a href="<?php echo esc_url( $bundle_link ); ?>" class="button owp-button" target="_blank"><?php esc_html_e( 'Read More', 'ocean-extra' ); ?></a>
				</div>
				<i class="dashicons dashicons-admin-appearance"></i>
			</div>

			<?php
		}
		?>

		<div class="oceanwp-bloc oceanwp-facebook<?php echo esc_attr( $class ); ?>">
			<div class="owp-ribbon"><div><?php esc_html_e( 'VIP', 'ocean-extra' ); ?></div></div>
			<p class="owp-img">
				<a href="https://www.facebook.com/groups/oceanwptheme/" target="_blank">
					<img src="<?php echo esc_url( $facebook ); ?>" alt="Facebook Group" />
				</a>
			</p>
			<div class="content-wrap">
				<p class="content"><?php esc_html_e( 'Become part of the OceanWP VIP Community on Facebook. You will get access to the latest beta releases, get help with issues or simply meet like-minded people.', 'ocean-extra' ); ?></p>
				<a href="https://www.facebook.com/groups/oceanwptheme/" class="button owp-button" target="_blank"><?php esc_html_e( 'Join the Group', 'ocean-extra' ); ?></a>
			</div>
			<i class="dashicons dashicons-facebook-alt"></i>
		</div>

		<div class="oceanwp-buttons<?php echo esc_attr( $class ); ?>">
			<a href="https://www.youtube.com/c/OceanWP" class="button owp-button owp-yt-btn" target="_blank"><?php esc_html_e( 'How-to Videos', 'ocean-extra' ); ?></a>
			<a href="http://docs.oceanwp.org/" class="button owp-button owp-doc-btn" target="_blank"><?php esc_html_e( 'Documentation', 'ocean-extra' ); ?></a>
			<a href="https://oceanwp.org/support/" class="button owp-button owp-support-btn" target="_blank"><?php esc_html_e( 'Open a Support Ticket', 'ocean-extra' ); ?></a>
		</div>

		<?php
	}

	/**
	 * Settings page output
	 *
	 * @since 1.0.0
	 */
	public static function create_admin_page() {

		// Get panels array
		$theme_panels = self::get_panels();

		// Get options array
		$options = self::get_options();
		?>

		<div class="wrap oceanwp-theme-panel clr">

			<h1><?php esc_attr_e( 'Theme Panel', 'ocean-extra' ); ?></h1>

			<h2 class="nav-tab-wrapper">
		<?php
		// Get current tab
		$curr_tab = ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'features';

		// Feature url
		$feature_url = add_query_arg(
			array(
				'page' => 'oceanwp-panel',
				'tab'  => 'features',
			),
			'admin.php'
		);
		?>

		<?php do_action( 'ocean_theme_panel_before_tab' ); ?>

				<a href="<?php echo esc_url( $feature_url ); ?>" class="nav-tab <?php echo $curr_tab == 'features' ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e( 'Features', 'ocean-extra' ); ?></a>

		<?php do_action( 'ocean_theme_panel_after_tab' ); ?>
			</h2>

		<?php do_action( 'ocean_theme_panel_before_content' ); ?>

			<div class="oceanwp-settings clr" <?php echo $curr_tab == 'features' ? '' : 'style="display:none;"'; ?>>

				<?php if ( true != apply_filters( 'oceanwp_theme_panel_sidebar_enabled', false ) ) { ?>

					<div class="oceanwp-sidebar right clr">

					<?php self::admin_page_sidebar(); ?>

					<?php do_action( 'oe_panels_sidebar_after' ); ?>

					</div>

				<?php } ?>

				<div class="left clr">

					<form id="oceanwp-theme-panel-form" method="post" action="options.php">
				<?php // wp_nonce_field('oe_panels_settings_nounce', 'oe_panels_settings_nounce'); ?>
				<?php settings_fields( 'oe_panels_settings' ); ?>

						<div class="oceanwp-panels clr">

						<h2 class="oceanwp-title"><?php esc_html_e( 'Customizer Search', 'ocean-extra' ); ?></h2>

							<p class="oceanwp-desc"><?php esc_html_e( 'Disable or Enable the Customizer Search. ', 'ocean-extra' ); ?></p>
							<div id="ocean-customizer-search" class="column-wrap clr">
								<label for="oceanwp-switch-customizer-search" class="column-name clr">
									<h3 class="title"><?php esc_html_e( 'Customizer Search', 'ocean-extra' ); ?></h3>
									<input type="checkbox" name="oe_panels_settings[customizer-search]" value="true" id="oceanwp-switch[customizer-search]" <?php checked( (bool) self::get_setting( 'customizer-search' ) ); ?>>
							</label>

							</div>
						</div>

						<div class="oceanwp-panels clr">

							<h2 class="oceanwp-title"><?php esc_html_e( 'Customizer Sections', 'ocean-extra' ); ?></h2>

							<p class="oceanwp-desc"><?php esc_html_e( 'Disable the Customizer panels that you do not have or need anymore to load it quickly. Your settings are saved, so do not worry.', 'ocean-extra' ); ?></p>

				<?php
				// Loop through theme pars and add checkboxes
				foreach ( $theme_panels as $key => $val ) :

					// Var
					$label = isset( $val['label'] ) ? $val['label'] : '';
					$desc  = isset( $val['desc'] ) ? $val['desc'] : '';

					// Get settings
					$settings = self::get_setting( $key );
					?>

								<div id="<?php echo esc_attr( $key ); ?>" class="column-wrap clr">

									<label for="oceanwp-switch-[<?php echo esc_attr( $key ); ?>]" class="column-name clr">
										<h3 class="title"><?php echo esc_attr( $label ); ?></h3>
										<input type="checkbox" name="oe_panels_settings[<?php echo esc_attr( $key ); ?>]" value="true" id="oceanwp-switch-[<?php echo esc_attr( $key ); ?>]" <?php checked( $settings ); ?>>
					<?php if ( $desc ) { ?>
											<div class="desc"><?php echo esc_attr( $desc ); ?></div>
			<?php } ?>
									</label>

								</div>

							<?php endforeach; ?>

							<?php submit_button(); ?>

						</div>

					</form>

							<?php do_action( 'oe_theme_panel_after' ); ?>

					<div class="divider clr"></div>

					<div class="oceanwp-options clr">

						<h2 class="oceanwp-title"><?php esc_html_e( 'Getting started', 'ocean-extra' ); ?></h2>

						<p class="oceanwp-desc"><?php esc_html_e( 'Take a look in the options of the Customizer and see yourself how easy and quick to customize your website as you wish.', 'ocean-extra' ); ?></p>

						<div class="options-inner clr">

		<?php
		// Loop through options
		foreach ( $options as $key => $val ) :

			// Var
			$label = isset( $val['label'] ) ? $val['label'] : '';
			$desc  = isset( $val['desc'] ) ? $val['desc'] : '';
			$panel = isset( $val['panel'] ) ? $val['panel'] : false;
			$id    = $key;

			if ( true == $panel ) {
				$focus = 'panel';
			} else {
				$focus = 'control';
			}
			?>

								<div class="column-wrap">

									<div class="column-inner clr">

										<h3 class="title"><?php echo esc_attr( $label ); ?></h3>
								<?php if ( $desc ) { ?>
											<p class="desc"><?php echo esc_attr( $desc ); ?></p>
								<?php } ?>

										<div class="bottom-column">
											<a class="option-link" href="<?php echo esc_url( admin_url( 'customize.php?autofocus[' . $focus . ']=' . $id . '' ) ); ?>" target="_blank"><?php esc_html_e( 'Go to the option', 'ocean-extra' ); ?></a>
										</div>

									</div>

								</div>

							<?php endforeach; ?>

						</div><!-- .options-inner -->

					</div>

				</div>

			</div><!-- .oceanwp-settings -->

									<?php do_action( 'ocean_theme_panel_after_content' ); ?>

		</div>

		<?php
	}

	/**
	 * Include addons
	 *
	 * @since 1.0.0
	 */
	private static function load_addons() {

		// Addons directory location
		$dir = OE_PATH . '/includes/panel/';

		if ( is_admin() ) {

			// Import/Export
			require_once $dir . 'import-export.php';

			/**
			 * Since the SDK is initiated within the functions.php of the theme, make sure to check if the SDK is set only after the theme's setup.
			 *
			 * @author Vova Feldman
			 */
			add_action( 'after_setup_theme', 'Ocean_Extra_Theme_Panel::load_addons_after_theme_setup' );
		}

		// Scripts panel - if minimum PHP 5.6
		if ( version_compare( PHP_VERSION, '5.6', '>=' ) ) {
			require_once $dir . 'scripts.php';
		}
	}

	/**
	 * Since the SDK is initiated within the functions.php of the theme, make sure to check if the SDK is set only after the theme's setup.
	 *
	 * @author Vova Feldman
	 */
	public static function load_addons_after_theme_setup() {
		if ( function_exists( 'owp_fs' ) ) {
			// Don't add extensions and licenses when Freemius is in place.
			return;
		}

		// Addons directory location
		$dir = OE_PATH . '/includes/panel/';

		// Extensions
		require_once $dir . 'extensions.php';

		// Licenses
		require_once $dir . 'licenses.php';
	}

	/**
	 * Theme panel CSS
	 *
	 * @since 1.0.0
	 */
	public static function css( $hook ) {

		// Only load scripts when needed
		if ( 'toplevel_page_oceanwp-panel' != $hook ) {
			return;
		}

		// CSS
		wp_enqueue_style( 'oceanwp-theme-panel', plugins_url( '/assets/css/panel.min.css', __FILE__ ) );
	}

}

new Ocean_Extra_Theme_Panel();
