<?php
/**
 * Plugin Name:         Ocean Extra
 * Plugin URI:          https://oceanwp.org/extension/ocean-extra/
 * Description:         Add extra features and flexibility to your OceanWP theme for a turbocharged premium experience and full control over every aspect of your website.
 * Version:             2.1.1
 * Author:              OceanWP
 * Author URI:          https://oceanwp.org/
 * Requires at least:   5.6
 * Tested up to:        6.1.1
 * Text Domain: ocean-extra
 * Domain Path: /languages
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of Ocean_Extra to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Ocean_Extra
 */
function Ocean_Extra() {
	return Ocean_Extra::instance();
} // End Ocean_Extra()

Ocean_Extra();

/**
 * Main Ocean_Extra Class
 *
 * @class Ocean_Extra
 * @version 1.0.0
 * @since 1.0.0
 * @package Ocean_Extra
 */
final class Ocean_Extra {
	/**
	 * Ocean_Extra The single instance of Ocean_Extra.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct( $widget_areas = array() ) {
		$this->token       = 'ocean-extra';
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->version     = '2.1.1';

		define( 'OE_URL', $this->plugin_url );
		define( 'OE_PATH', $this->plugin_path );
		define( 'OE_VERSION', $this->version );
		define( 'OE_FILE_PATH', __FILE__ );
		define( 'OE_ADMIN_PANEL_HOOK_PREFIX', 'theme-panel_page_oceanwp-panel' );

		// WooCommerce Wishlist partner ID
		if ( class_exists( 'TInvWL_Wishlist' ) ) {
			define( 'TINVWL_PARTNER', 'oceanwporg' );
			define( 'TINVWL_CAMPAIGN', 'oceanwp_theme' );
		}

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Setup all the things
		add_action( 'init', array( $this, 'setup' ) );

		// Menu icons
		$theme = wp_get_theme();
		if ( 'OceanWP' == $theme->name || 'oceanwp' == $theme->template ) {

			if ( get_template_directory() == get_stylesheet_directory() ) {
				$current_theme_version  = theme_version();
			} else {
				$parent = wp_get_theme()->parent(); 
				// get parent version 
				if ( ! empty( $parent) ) {
					$current_theme_version = $parent->Version;
				}
			}
			$required_theme_version = '3.3.3';


			require_once OE_PATH . '/includes/panel/theme-panel.php';
			require_once OE_PATH . '/includes/panel/integrations-tab.php';
			$oe_library_active_status = get_option( 'oe_library_active_status', 'yes' );
			if( $oe_library_active_status == 'yes' ) {
				require_once OE_PATH . '/includes/panel/library.php';
			}
			require_once OE_PATH . '/includes/panel/library-shortcode.php';
			require_once OE_PATH . '/includes/menu-icons/menu-icons.php';
			// require_once OE_PATH . '/includes/wizard/wizard.php';

			require_once OE_PATH . '/includes/themepanel/theme-panel.php';


			if ( ! empty( $current_theme_version ) && ! empty( $required_theme_version ) && version_compare( $current_theme_version, $required_theme_version , '>' ) ) {
				require_once OE_PATH . '/includes/compatibility/ocean.php';
			}

			require_once OE_PATH . '/includes/preloader/preloader.php';

			// Outputs custom JS to the footer
			add_action( 'wp_footer', array( $this, 'custom_js' ), 9999 );

			// Register Custom JS file
			add_action( 'init', array( $this, 'register_custom_js' ) );

			// Move the Custom CSS section into the Custom CSS/JS section
			add_action( 'customize_register', array( $this, 'customize_register' ), 11 );

			// Remove customizer unnecessary sections
			add_action( 'customize_register', array( $this, 'remove_customize_sections' ), 11 );

			// Load custom widgets
			add_action( 'widgets_init', array( $this, 'custom_widgets' ), 10 );

			// Add meta tags
			add_filter( 'wp_head', array( $this, 'meta_tags' ), 1 );
		}

		// Allow shortcodes in text widgets
		add_filter( 'widget_text', 'do_shortcode' );

		// Allow for the use of shortcodes in the WordPress excerpt
		add_filter( 'the_excerpt', 'shortcode_unautop' );
		add_filter( 'the_excerpt', 'do_shortcode' );
	}

	/**
	 * Main Ocean_Extra Instance
	 *
	 * Ensures only one instance of Ocean_Extra is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Ocean_Extra()
	 * @return Main Ocean_Extra instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'ocean-extra', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Deserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 *
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Return the correct icon
	 *
	 * @param string  $icon        Icon class.
	 * @param bool    $echo        Print string.
	 * @param string  $class       Icon class.
	 * @param string  $title       Optional SVG title.
	 * @param string  $desc        Optional SVG description.
	 * @param string  $aria_hidden Optional SVG description.
	 * @param boolean $fallback    Fallback icon.
	 *
	 * @since 1.7.6
	 * @return string OceanWP Icon.
	 */
	public static function oe_svg_icon( $icon, $echo = true, $class = '', $title = '', $desc = '', $aria_hidden = true, $fallback = false ) {

		// Get icon class.
		$theme_icons = oceanwp_theme_icons();

		if ( function_exists( 'oceanwp_icon' ) ) {
			return oceanwp_icon( $icon, $echo, $class, $title, $desc, $aria_hidden, $fallback );
		} else {

			if ( true === $echo ) {
				echo '<i class="' . $class . ' ' . $theme_icons[ $icon ]['fai'] . '"' . $aria_hidden . ' role="img"></i>';
			} else {
				return '<i class="' . $class . ' ' . $theme_icons[ $icon ]['fai'] . '"' . $aria_hidden . ' role="img"></i>';
			}

			return;

		}
	}

	/**
	 * All theme functions hook into the oceanwp_footer_js filter for this function.
	 *
	 * @since 1.3.8
	 */
	public static function custom_js( $output = null ) {

		// Add filter for adding custom js via other functions
		$output = apply_filters( 'ocean_footer_js', $output );

		// Minify and output JS in the wp_footer
		if ( ! empty( $output ) ) { ?>

			<script type="text/javascript">

				/* OceanWP JS */
				<?php echo Ocean_Extra_JSMin::minify( $output ); ?>

			</script>

			<?php
		}

	}

	/**
	 * Adds customizer options
	 *
	 * @since 1.3.8
	 */
	public function register_custom_js() {

		// Var
		$dir = OE_PATH . '/includes/';

		// File
		if ( Ocean_Extra_Theme_Panel::get_setting( 'oe_custom_code_panel' ) ) {
			require_once $dir . 'custom-code.php';
		}

	}

	/**
	 * Move the Custom CSS section into the Custom CSS/JS section
	 *
	 * @since 1.3.8
	 */
	public static function customize_register( $wp_customize ) {

		// Move custom css setting
		$wp_customize->get_control( 'custom_css' )->section = 'ocean_custom_code_panel';

	}

	/**
	 * Remove customizer unnecessary sections
	 *
	 * @since 1.0.0
	 */
	public static function remove_customize_sections( $wp_customize ) {

		// Remove core sections
		$wp_customize->remove_section( 'colors' );
		$wp_customize->remove_section( 'themes' );
		$wp_customize->remove_section( 'background_image' );

		// Remove core controls
		$wp_customize->remove_control( 'header_textcolor' );
		$wp_customize->remove_control( 'background_color' );
		$wp_customize->remove_control( 'background_image' );
		$wp_customize->remove_control( 'display_header_text' );

		// Remove default settings
		$wp_customize->remove_setting( 'background_color' );
		$wp_customize->remove_setting( 'background_image' );

	}

	/**
	 * Setup all the things.
	 * Only executes if OceanWP or a child theme using OceanWP as a parent is active and the extension specific filter returns true.
	 *
	 * @return void
	 */
	public function setup() {
		$theme = wp_get_theme();

		if ( 'OceanWP' == $theme->name || 'oceanwp' == $theme->template ) {
			require_once OE_PATH . '/includes/metabox/butterbean/butterbean.php';
			require_once OE_PATH . '/includes/metabox/metabox.php';
			require_once OE_PATH . '/includes/metabox/shortcodes.php';
			require_once OE_PATH . '/includes/metabox/gallery-metabox/gallery-metabox.php';
			require_once OE_PATH . '/includes/shortcodes/shortcodes.php';
			require_once OE_PATH . '/includes/image-resizer.php';
			require_once OE_PATH . '/includes/jsmin.php';
			require_once OE_PATH . '/includes/panel/notice.php';
			require_once OE_PATH . '/includes/walker.php';
			require_once OE_PATH . '/includes/ocean-extra-strings.php';
			require_once OE_PATH . '/includes/dashboard.php';
			require_once OE_PATH . '/includes/panel/demos.php';
			$oe_notification_active_status = get_option( 'oe_notification_active_status', 'no' );
			if( $oe_notification_active_status == 'no' ) {
				require_once OE_PATH . '/includes/admin-bar/admin-bar.php';
				require_once OE_PATH . '/includes/admin-bar/notifications.php';
			}
			require_once OE_PATH . '/includes/adobe-font.php';

			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 999 );
		}
	}

	/**
	 * Include flickr widget class
	 *
	 * @since   1.0.0
	 */
	public static function custom_widgets() {

		if ( ! version_compare( PHP_VERSION, '5.6', '>=' ) ) {
			return;
		}

		// Define array of custom widgets for the theme
		$widgets = apply_filters(
			'ocean_custom_widgets',
			array(
				'about-me',
				'contact-info',
				'custom-links',
				'custom-menu',
				'facebook',
				'flickr',
				'instagram',
				'mailchimp',
				'recent-posts',
				'social',
				'social-share',
				'tags',
				'twitter',
				'video',
				'custom-header-logo',
				'custom-header-nav',
			)
		);

		// Loop through widgets and load their files
		if ( $widgets && is_array( $widgets ) ) {
			foreach ( $widgets as $widget ) {
				$file = OE_PATH . '/includes/widgets/' . $widget . '.php';
				if ( file_exists( $file ) ) {
					require_once $file;
				}
			}
		}

	}

	/**
	 * Add meta tags
	 *
	 * @since 1.5.1
	 */
	public static function meta_tags() {

		// Return if disabled or if Yoast SEO enabled as they have their own meta tags
		if ( false == get_theme_mod( 'ocean_open_graph', false )
			|| defined( 'WPSEO_VERSION' )
			|| defined( 'RANK_MATH_FILE' ) ) {
			return;
		}

		// Facebook URL
		$facebook_url = get_theme_mod( 'ocean_facebook_page_url' );

		// Disable Jetpack's Open Graph tags
		add_filter( 'jetpack_enable_opengraph', '__return_false', 99 );
		add_filter( 'jetpack_enable_open_graph', '__return_false', 99 );
		add_filter( 'jetpack_disable_twitter_cards', '__return_true', 99 );

		// Type
		if ( is_front_page() || is_home() ) {
			$type = 'website';
		} elseif ( is_singular() ) {
			$type = 'article';
		} else {
			// We use "object" for archives etc. as article doesn't apply there.
			$type = 'object';
		}

		// Title
		if ( is_singular() ) {
			$title = get_the_title();
		} else {
			$title = oceanwp_has_page_title();
		}

		// Description
		if ( is_category() || is_tag() || is_tax() ) {
			$description = strip_shortcodes( wp_strip_all_tags( term_description() ) );
		} else {
			$description = html_entity_decode( htmlspecialchars_decode( oceanwp_excerpt( 40 ) ) );
		}

		// Image.
		$image   = '';
		$has_img = false;
		if ( OCEANWP_WOOCOMMERCE_ACTIVE
			&& is_product_category() ) {
			global $wp_query;
			$cat          = $wp_query->get_queried_object();
			$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
			$get_image    = wp_get_attachment_url( $thumbnail_id );
			if ( $get_image ) {
				$image   = $get_image;
				$has_img = true;
			}
		} else {
			$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );

			if ( is_array( $get_image ) ) {
				$image   = $get_image[0];
				$has_img = true;
			}
		}

		// Post author.
		if ( $facebook_url ) {
			$author = $facebook_url;
		}

		// Facebook publisher URL
		if ( ! empty( $facebook_url ) ) {
			$publisher = $facebook_url;
		}

		// Facebook APP ID
		$facebook_appid = get_theme_mod( 'ocean_facebook_appid' );
		if ( ! empty( $facebook_appid ) ) {
			$fb_app_id = $facebook_appid;
		}

		// Twiiter handle
		$twitter_handle = '@' . str_replace( '@', '', get_theme_mod( 'ocean_twitter_handle' ) );

		// Output
		$output  = self::opengraph_tag( 'property', 'og:type', trim( $type ) );
		$output .= self::opengraph_tag( 'property', 'og:title', trim( $title ) );

		if ( isset( $description ) && ! empty( $description ) ) {
			$output .= self::opengraph_tag( 'property', 'og:description', trim( $description ) );
		}

		if ( has_post_thumbnail( oceanwp_post_id() ) && true == $has_img ) {
			$output .= self::opengraph_tag( 'property', 'og:image', trim( $image ) );
			$output .= self::opengraph_tag( 'property', 'og:image:width', absint( $get_image[1] ) );
			$output .= self::opengraph_tag( 'property', 'og:image:height', absint( $get_image[2] ) );
		}

		$output .= self::opengraph_tag( 'property', 'og:url', trim( get_permalink() ) );
		$output .= self::opengraph_tag( 'property', 'og:site_name', trim( get_bloginfo( 'name' ) ) );

		if ( is_singular() && ! is_front_page() ) {

			if ( isset( $author ) && ! empty( $author ) ) {
				$output .= self::opengraph_tag( 'property', 'article:author', trim( $author ) );
			}

			if ( is_singular( 'post' ) ) {
				$output .= self::opengraph_tag( 'property', 'article:published_time', trim( get_post_time( 'c' ) ) );
				$output .= self::opengraph_tag( 'property', 'article:modified_time', trim( get_post_modified_time( 'c' ) ) );
				$output .= self::opengraph_tag( 'property', 'og:updated_time', trim( get_post_modified_time( 'c' ) ) );
			}
		}

		if ( is_singular() ) {

			$tags = get_the_tags();
			if ( ! is_wp_error( $tags ) && ( is_array( $tags ) && $tags !== array() ) ) {
				foreach ( $tags as $tag ) {
					$output .= self::opengraph_tag( 'property', 'article:tag', trim( $tag->name ) );
				}
			}

			$terms = get_the_category();
			if ( ! is_wp_error( $terms ) && ( is_array( $terms ) && $terms !== array() ) ) {
				// We can only show one section here, so we take the first one.
				$output .= self::opengraph_tag( 'property', 'article:section', trim( $terms[0]->name ) );
			}
		}

		if ( isset( $publisher ) && ! empty( $publisher ) ) {
			$output .= self::opengraph_tag( 'property', 'article:publisher', trim( $publisher ) );
		}

		if ( isset( $fb_app_id ) && ! empty( $fb_app_id ) ) {
			$output .= self::opengraph_tag( 'property', 'fb:app_id', trim( $fb_app_id ) );
		}

		// Twitter
		$output .= self::opengraph_tag( 'name', 'twitter:card', 'summary_large_image' );
		$output .= self::opengraph_tag( 'name', 'twitter:title', trim( $title ) );

		if ( isset( $description ) && ! empty( $description ) ) {
			$output .= self::opengraph_tag( 'name', 'twitter:description', trim( $description ) );
		}

		if ( has_post_thumbnail( get_the_ID() ) && true == $has_img ) {
			$output .= self::opengraph_tag( 'name', 'twitter:image', trim( $image ) );
		}

		if ( isset( $twitter_handle ) && ! empty( $twitter_handle ) ) {
			$output .= self::opengraph_tag( 'name', 'twitter:site', trim( $twitter_handle ) );
			$output .= self::opengraph_tag( 'name', 'twitter:creator', trim( $twitter_handle ) );
		}

		echo $output;

	}

	/**
	 * Get meta tags
	 *
	 * @since 1.5.1
	 */
	public static function opengraph_tag( $attr, $property, $content ) {
		echo '<meta ', esc_attr( $attr ), '="', esc_attr( $property ), '" content="', esc_attr( $content ), '" />', "\n";
	}

	/**
	 * Enqueue scripts
	 *
	 * @since   1.0.0
	 */
	public function scripts() {

		// Load main stylesheet

		if ( get_theme_mod( 'ocean_load_widgets_stylesheet', 'enabled' ) === 'disabled' ) {
			return;
		}

		wp_enqueue_style( 'oe-widgets-style', plugins_url( '/assets/css/widgets.css', __FILE__ ) );

		// If rtl
		if ( is_RTL() ) {
			wp_enqueue_style( 'oe-widgets-style-rtl', plugins_url( '/assets/css/rtl.css', __FILE__ ) );
		}

	}

} // End Class.

/**
 * Check link rel and return correct aria label
 *
 * @since 1.6.4
 */

if ( ! function_exists( 'ocean_link_rel' ) ) {

	function ocean_link_rel( $ocean_srt, $nofollow, $target ) {

		if ( $nofollow === 'yes' ) {
			if ( $target === 'blank' ) {
				$link_rel = 'rel="nofollow noopener noreferrer"';
				$ocean_sr = $ocean_srt;
			} else {
				$link_rel = 'rel="nofollow"';
				$ocean_sr = '';
			}
		} elseif ( $nofollow === 'no' || $nofollow === '' ) {
			if ( $target === 'blank' ) {
				$link_rel = 'rel="noopener noreferrer"';
				$ocean_sr = $ocean_srt;
			} else {
				$link_rel = '';
				$ocean_sr = '';
			}
		}

		return array( $ocean_sr, $link_rel );
	}
}

/**
 * Returns current theme version
 *
 * @since   2.0.0
 */
function theme_version() {

	// Get theme data.
	$theme = wp_get_theme();

	// Return theme version.
	return $theme->get( 'Version' );

}

/**
 * Display Notice when Ocean Extra is outdated.
 *
 *  @since 2.0.0
 * 
 * @return void
 */

if ( ! function_exists( 'ocean_theme_is_outdated_admin_notice' ) ) {
	function ocean_theme_is_outdated_admin_notice() {
		$theme = wp_get_theme();
		if ( current_user_can( 'install_plugins' ) ) {
			if ( 'OceanWP' == $theme->name || 'oceanwp' == $theme->template ) {
				if ( ! defined( 'OCEANWP_THEME_VERSION' ) ) {
					define( 'OCEANWP_THEME_VERSION', theme_version() );
				}
				if ( ! is_child_theme() ) {
					$current_theme_version  = OCEANWP_THEME_VERSION;
				} else {
					$current_theme_version  = '3.3.0';
				}
				$required_theme_version = '3.3.0';

				if ( ! empty( $current_theme_version ) && ! empty( $required_theme_version ) && version_compare( $current_theme_version, $required_theme_version , '<' ) ) :
				?>
				<div class="notice notice-warning is-dismissible">
					<p><?php esc_html_e( 'We made changes to our Theme Panel. To complete the installation and enjoy both old and new features, please make sure the OceanWP theme and all Ocean plugins are up to date.', 'oceanwp' ); ?></p>
					<a href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>"><?php esc_html_e( 'Update and get the new Theme Panel', 'oceanwp' ); ?></a>
					<br><br>
				</div>
				<?php
				endif;
			}
		}
	}
}
add_action( 'admin_notices', 'ocean_theme_is_outdated_admin_notice' );

// --------------------------------------------------------------------------------
// region Freemius
// --------------------------------------------------------------------------------

function owp_include_client_migration() {
	require_once dirname( __FILE__ ) . '/includes/client-migration/edd.php';

	owp_fs()->add_filter( 'has_paid_plan_account', '__return_false' );
	owp_fs()->add_filter( 'is_submenu_visible', 'owp_fs_is_submenu_visible', 10, 2 );
}

add_action( 'owp_fs_loaded', 'owp_include_client_migration' );

function owp_fs_is_submenu_visible( $is_visible, $submenu_id ) {
	if ( 'pricing' === $submenu_id ) {
		$show_pricing_transient = false; // get_transient( 'oceanwp_show_pricing' );

		if ( is_string( $show_pricing_transient ) ) {
			$show_pricing = ( 'yes' === $show_pricing_transient );
		} else {
			$show_pricing = true;

			foreach ( OceanWP_EDD_License_Key::$paid_addons as $class_name => $data ) {
				if ( ! class_exists( $class_name ) ) {
					continue;
				}

				if ( ! function_exists( $data['fs_shortcode'] ) ) {
					continue;
				}

				/**
				 * Initiate the Freemius instance before migrating.
				 *
				 * @var Freemius $addon_fs
				 */
				$addon_fs = call_user_func( $data['fs_shortcode'] );

				if ( $addon_fs->has_active_valid_license() ) {
					$licenses = $addon_fs->_get_license();

					if ( is_object( $licenses ) &&
						 FS_Plugin_License::is_valid_id( $licenses->parent_license_id )
					) {
						$show_pricing = false;
						break;
					}
				}
			}

			if( property_exists( 'OceanWP_EDD_License_Key', 'separate_addons' ) && !empty( OceanWP_EDD_License_Key::$separate_addons ) ) {
				foreach ( OceanWP_EDD_License_Key::$separate_addons as $class_name => $data ) {
					if ( ! class_exists( $class_name ) ) {
						continue;
					}
	
					if ( ! function_exists( $data['fs_shortcode'] ) ) {
						continue;
					}
	
					/**
					 * Initiate the Freemius instance before migrating.
					 *
					 * @var Freemius $addon_fs
					 */
					$addon_fs = call_user_func( $data['fs_shortcode'] );
	
					if ( $addon_fs->has_active_valid_license() ) {
						$licenses = $addon_fs->_get_license();
	
						if ( is_object( $licenses ) &&
							 FS_Plugin_License::is_valid_id( $licenses->id )
						) {
							$show_pricing = false;
							break;
						}
					}
				}
			}
			
			// set_transient(
			// 'oceanwp_show_pricing',
			// $show_pricing ? 'yes' : 'no',
			// WP_FS__TIME_5_MIN_IN_SEC
			// );
		}

		return $show_pricing;
	}

	return $is_visible;
}

// function owp_fs_after_client_migration( $license_accessor ) {
// if ('OceanWP_EDD_License_Key' !== get_class($license_accessor)) {
// return;
// }
//
// delete_transient( 'oceanwp_show_pricing' );
// }
//
// add_action( 'fs_after_client_migration', 'owp_fs_after_client_migration' );

// endregion
