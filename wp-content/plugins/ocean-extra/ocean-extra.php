<?php
/**
 * Plugin Name:			Ocean Extra
 * Plugin URI:			https://oceanwp.org/extension/ocean-extra/
 * Description:			Add extra features like widgets, metaboxes, import/export and a panel to activate the premium extensions.
 * Version:				1.6.3
 * Author:				OceanWP
 * Author URI:			https://oceanwp.org/
 * Requires at least:	5.3
 * Tested up to:		5.4.1
 *
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
 * @version	1.0.0
 * @since 1.0.0
 * @package	Ocean_Extra
 */
final class Ocean_Extra {
	/**
	 * Ocean_Extra The single instance of Ocean_Extra.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct( $widget_areas = array() ) {
		$this->token 			= 'ocean-extra';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.6.3';

		define( 'OE_URL', $this->plugin_url );
		define( 'OE_PATH', $this->plugin_path );
		define( 'OE_VERSION', $this->version );
        define( 'OE_FILE_PATH', __FILE__ );
		define( 'OE_ADMIN_PANEL_HOOK_PREFIX', 'theme-panel_page_oceanwp-panel' );

		// WPForms partner ID
		add_filter( 'wpforms_upgrade_link', array( $this, 'wpforms_upgrade_link' ) );

		// WooCommerce Wishlist partner ID
		if ( class_exists( 'TInvWL_Wishlist' ) ) {
			define( 'TINVWL_PARTNER', 'oceanwporg' );
			define( 'TINVWL_CAMPAIGN', 'oceanwp_theme' );
		}

		// WooCommerce Variation Swatches partner ID
		add_filter( 'gwp_affiliate_id', array( $this, 'gwp_affiliate_id' ) );

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Setup all the things
		add_action( 'init', array( $this, 'setup' ) );

		// Menu icons
		$theme = wp_get_theme();
		if ( 'OceanWP' == $theme->name || 'oceanwp' == $theme->template ) {
			require_once( OE_PATH .'/includes/panel/theme-panel.php' );
			require_once( OE_PATH .'/includes/panel/integrations-tab.php' );
			require_once( OE_PATH .'/includes/panel/library.php' );
			require_once( OE_PATH .'/includes/panel/library-shortcode.php' );
			require_once( OE_PATH .'/includes/panel/updater.php' );
			require_once( OE_PATH .'/includes/menu-icons/menu-icons.php' );
			require_once( OE_PATH .'/includes/wizard/wizard.php' );

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
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * WPForms partner ID
	 *
	 * @since 1.0.0
	 */
	public function wpforms_upgrade_link() {
		$url = 'https://wpforms.com/lite-upgrade/?discount=LITEUPGRADE&amp;utm_source=WordPress&amp;utm_medium=' . sanitize_key( apply_filters( 'wpforms_upgrade_link_medium', 'link' ) ) . '&amp;utm_campaign=liteplugin';

		// Build final URL
		$final_url = sprintf( 'http://www.shareasale.com/r.cfm?B=837827&U=%s&M=64312&urllink=%s', '1591020', $url );

		// Return URL.
		return esc_url( $final_url );
	}

	/**
	 * WooCommerce Variation Swatches partner ID
	 *
	 * @since 1.0.0
	 */
	public function gwp_affiliate_id() {

		// Return if the plugin is not active
		if ( ! class_exists( 'Woo_Variation_Swatches' ) ) {
			return;
		}

		return 69;
	}

	/**
	 * Load the localisation file.
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
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * All theme functions hook into the oceanwp_footer_js filter for this function.
	 *
	 * @since 1.3.8
	 */
	public static function custom_js( $output = NULL ) {

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
		$dir = OE_PATH .'/includes/';

		// File
		if ( Ocean_Extra_Theme_Panel::get_setting( 'oe_custom_code_panel' ) ) {
			require_once( $dir . 'custom-code.php' );
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
	 * @return void
	 */
	public function setup() {
		$theme = wp_get_theme();

		if ( 'OceanWP' == $theme->name || 'oceanwp' == $theme->template ) {
			require_once( OE_PATH .'/includes/metabox/butterbean/butterbean.php' );
			require_once( OE_PATH .'/includes/metabox/metabox.php' );
			require_once( OE_PATH .'/includes/metabox/shortcodes.php' );
			require_once( OE_PATH .'/includes/metabox/gallery-metabox/gallery-metabox.php' );
			require_once( OE_PATH .'/includes/shortcodes/shortcodes.php' );
			require_once( OE_PATH .'/includes/image-resizer.php' );
			require_once( OE_PATH .'/includes/jsmin.php' );
			require_once( OE_PATH .'/includes/panel/notice.php' );
			require_once( OE_PATH .'/includes/walker.php' );
			require_once( OE_PATH .'/includes/dashboard.php' );
			require_once( OE_PATH .'/includes/panel/demos.php' );

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
		$widgets = apply_filters( 'ocean_custom_widgets', array(
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
		) );

		// Loop through widgets and load their files
		if ( $widgets && is_array( $widgets ) ) {
			foreach ( $widgets as $widget ) {
				$file = OE_PATH .'/includes/widgets/' . $widget .'.php';
				if ( file_exists ( $file ) ) {
					require_once( $file );
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
		} else if ( is_singular() ) {
			$type = 'article';
		} else {
			// We use "object" for archives etc. as article doesn't apply there.
			$type = 'object';
		}

		// Title
		if ( is_singular() ) {
			$title = get_the_title();
		} else {
			$title = oceanwp_title();
		}

		// Description
		if ( is_category() || is_tag() || is_tax() ) {
			$description = strip_shortcodes( wp_strip_all_tags( term_description() ) );
		} else {
			$description = html_entity_decode( htmlspecialchars_decode( oceanwp_excerpt( 40 ) ) );
		}

		// Image
		$image = '';
		$has_img = false;
		if ( OCEANWP_WOOCOMMERCE_ACTIVE
			&& is_product_category() ) {
		    global $wp_query;
		    $cat = $wp_query->get_queried_object();
		    $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
		    $get_image = wp_get_attachment_url( $thumbnail_id );
		    if ( $get_image ) {
				$image = $get_image;
				$has_img = true;
			}
		} else {
			$get_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
			$image = $get_image[0];
			$has_img = true;
		}

		// Post author
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
		$twitter_handle = '@' . str_replace( '@' , '' , get_theme_mod( 'ocean_twitter_handle' ) );

		// Output
		$output = self::opengraph_tag( 'property', 'og:type', trim( $type ) );
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
		wp_enqueue_style( 'oe-widgets-style', plugins_url( '/assets/css/widgets.css', __FILE__ ) );

		// If rtl
		if ( is_RTL() ) {
			wp_enqueue_style( 'oe-widgets-style-rtl', plugins_url( '/assets/css/rtl.css', __FILE__ ) );
		}

	}

} // End Class

#--------------------------------------------------------------------------------
#region Freemius
#--------------------------------------------------------------------------------

function owp_include_client_migration() {
	require_once dirname( __FILE__ ) . '/includes/client-migration/edd.php';

	owp_fs()->add_filter( 'has_paid_plan_account', '__return_false' );
	owp_fs()->add_filter( 'is_submenu_visible', 'owp_fs_is_submenu_visible', 10, 2 );
}

add_action( 'owp_fs_loaded', 'owp_include_client_migration' );

function owp_fs_is_submenu_visible( $is_visible, $submenu_id ) {
	if ( 'pricing' === $submenu_id ) {
		$show_pricing_transient = false; //get_transient( 'oceanwp_show_pricing' );

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

//			set_transient(
//				'oceanwp_show_pricing',
//				$show_pricing ? 'yes' : 'no',
//				WP_FS__TIME_5_MIN_IN_SEC
//			);
		}

		return $show_pricing;
	}

	return $is_visible;
}

//function owp_fs_after_client_migration( $license_accessor ) {
//	if ('OceanWP_EDD_License_Key' !== get_class($license_accessor)) {
//		return;
//	}
//
//	delete_transient( 'oceanwp_show_pricing' );
//}
//
//add_action( 'fs_after_client_migration', 'owp_fs_after_client_migration' );

#endregion