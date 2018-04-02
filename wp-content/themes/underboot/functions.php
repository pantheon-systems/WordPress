<?php
/**
 * underboot functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package underboot
 */
function pfran_setup() {
	
//	register_nav_menus( array(
//		'primary' => __( 'Main Nav', 'pfran' ),
//	) );
	
	

	
	
	
	
	
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );
	
	add_theme_support( 'title-tag' );
	
	add_theme_support( 'post-thumbnails' );
	
	wp_register_script( 'site-js', get_template_directory_uri().'/js/site.js', array('jquery'), time(), false);
	
	wp_enqueue_script( 'site-js');
	
	wp_register_script( 'doubletaptogo-js', get_template_directory_uri().'/js/doubletaptogo.js', array('jquery'), time(), false);
	
	wp_enqueue_script( 'doubletaptogo-js');
	
	add_filter('widget_text', 'do_shortcode');
	
	
}
add_action( 'after_setup_theme', 'pfran_setup' );
add_image_size( 'title_bg', 1400, 192 );



if ( ! function_exists( 'underboot_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function underboot_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on underboot, use a find and replace
	 * to change 'underboot' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'underboot', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	//register_nav_menus( array(
		//'menu-1' => esc_html__( 'Primary', 'underboot' ),
	//) );
	
	// Register Bootstrap Navigation Walker, allows BS to work with WP menus
	//include '/wp_bootstrap_navwalker.php';
	include get_template_directory() . '/wp-bootstrap-navwalker-master/wp_bootstrap_navwalker.php';
	register_nav_menus( array(
		'primary-menu' => __( 'Default', 'underboot' ),
	) );
	
	

	

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'underboot_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );
}
endif;
add_action( 'after_setup_theme', 'underboot_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function underboot_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'underboot_content_width', 640 );
}
add_action( 'after_setup_theme', 'underboot_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function underboot_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'underboot' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'underboot' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'underboot_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function underboot_scripts() {	
	wp_enqueue_script('jquery');

	wp_enqueue_script( 'underboot-bootscript', get_template_directory_uri() . '/js/bootstrap.js');	
	//TO DO: before production, change this to the minified version (do not alter this file):
	wp_enqueue_style( 'underboot-boot', get_stylesheet_directory_uri().'/css/bootstrap.css' );	
	//the WP stylesheet, put all theme specific styles HERE:
	wp_enqueue_style( 'underboot-style', get_stylesheet_uri() );

	//from the underscores starter theme.  just use Bootstrap with navwalker.
	//wp_enqueue_script( 'underboot-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );
	
	
	wp_enqueue_script( 'underboot-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );


	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'underboot_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

//start whitelabeling
// https://premium.wpmudev.org/blog/white-labeling-wordpress/
//* Do NOT include the opening php tag
add_filter('gettext', 'change_howdy', 10, 3);
function change_howdy($translated, $text, $domain) {
    if (!is_admin() || 'default' != $domain)
        return $translated;
    if (false !== strpos($translated, 'Howdy'))
        return str_replace('Howdy', 'Unlock Your Digital Potential ', $translated);
    return $translated;
}
function change_footer_admin () {  
  echo 'Unlock Your Digital Potential with Qiigo.';  
}  
add_filter('admin_footer_text', 'change_footer_admin');
//* Change the URL of the WordPress login logo
function b3m_url_login_logo(){
    return get_bloginfo( 'wpurl' );
}
add_filter('login_headerurl', 'b3m_url_login_logo');


//remove wp logo from toolbar
//https://codex.wordpress.org/Function_Reference/remove_node
function remove_wp_logo( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'wp-logo' );
}
add_action( 'admin_bar_menu', 'remove_wp_logo', 999 );

//minimal branding to login page:
//https://codex.wordpress.org/Customizing_the_Login_Form
function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/img/logo.png);
            padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );
function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Unlock Your Digital Potential';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );
// https://codex.wordpress.org/Function_Reference/remove_meta_box
function remove_dashboard_widgets() {
	remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );   // Right Now
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' ); // Recent Comments
	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );  // Incoming Links
	remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );   // Plugins
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );  // Quick Press
	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );  // Recent Drafts
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );   // WordPress blog
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );   // Other WordPress News
	// use 'dashboard-network' as the second parameter to remove widgets from a network dashboard.
}
add_action( 'wp_dashboard_setup', 'remove_dashboard_widgets' );

// http://www.wpbeginner.com/wp-tutorials/how-to-remove-the-welcome-panel-in-wordpress-dashboard/
remove_action('welcome_panel', 'wp_welcome_panel');