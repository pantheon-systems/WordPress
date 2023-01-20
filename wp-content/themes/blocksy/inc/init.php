<?php
/**
 * Blocksy functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Blocksy
 */
add_action('after_setup_theme', function () {
	$i18n_manager = new Blocksy_Translations_Manager();
	$i18n_manager->register_translation_keys();

	/**
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Word, use a find and replace
	 * to change 'blocksy' to the name of your theme in all the template files.
	 */
	load_theme_textdomain('blocksy', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');
	add_theme_support('responsive-embeds');

	add_theme_support('html5', ['script', 'style']);

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support('title-tag');
	add_theme_support('custom-logo');
	add_theme_support('lifterlms-sidebars');
	add_theme_support('boostify-header-footer');

	add_theme_support('fl-theme-builder-headers');
	add_theme_support('fl-theme-builder-footers');
	add_theme_support('fl-theme-builder-parts');

	add_theme_support('editor-styles');
	// Trick editor into loading inline styles. See my_theme_pre_http_request_block_editor_customizer_styles()
	add_editor_style('https://blocksy-block-editor-customizer-styles');
	add_editor_style('static/bundle/editor-styles.min.css');

	$paletteColors = blocksy_get_colors(
		get_theme_mod('colorPalette'),
		[
			'color1' => [ 'color' => '#2872fa' ],
			'color2' => [ 'color' => '#1559ed' ],
			'color3' => [ 'color' => '#3A4F66' ],
			'color4' => [ 'color' => '#192a3d' ],
			'color5' => [ 'color' => '#e1e8ed' ],
			'color6' => [ 'color' => '#f2f5f7' ],
			'color7' => [ 'color' => '#FAFBFC' ],
			'color8' => [ 'color' => '#ffffff' ],
		]
	);

	add_theme_support('editor-color-palette', apply_filters('blocksy:editor-color-palette', [
		[
			'name' => __( 'Palette Color 1', 'blocksy' ),
			'slug' => 'palette-color-1',
			'color' => 'var(--paletteColor1, ' . $paletteColors['color1'] . ')',
			// 'color' => $paletteColors['color1']
		],

		[
			'name' => __( 'Palette Color 2', 'blocksy' ),
			'slug' => 'palette-color-2',
			'color' => 'var(--paletteColor2, ' . $paletteColors['color2'] . ')',
			// 'color' => $paletteColors['color2']
		],

		[
			'name' => __( 'Palette Color 3', 'blocksy' ),
			'slug' => 'palette-color-3',
			'color' => 'var(--paletteColor3, '. $paletteColors['color3'] . ')',
			// 'color' => $paletteColors['color3']
		],

		[
			'name' => __( 'Palette Color 4', 'blocksy' ),
			'slug' => 'palette-color-4',
			'color' => 'var(--paletteColor4, ' . $paletteColors['color4'] . ')',
			// 'color' => $paletteColors['color4']
		],

		[
			'name' => __( 'Palette Color 5', 'blocksy' ),
			'slug' => 'palette-color-5',
			'color' => 'var(--paletteColor5, ' . $paletteColors['color5'] . ')',
			// 'color' => $paletteColors['color5']
		],

		[
			'name' => __( 'Palette Color 6', 'blocksy' ),
			'slug' => 'palette-color-6',
			'color' => 'var(--paletteColor6, ' . $paletteColors['color6'] . ')',
			// 'color' => $paletteColors['color6']
		],

		[
			'name' => __( 'Palette Color 7', 'blocksy' ),
			'slug' => 'palette-color-7',
			'color' => 'var(--paletteColor7, ' . $paletteColors['color7'] . ')',
			// 'color' => $paletteColors['color7']
		],

		[
			'name' => __( 'Palette Color 8', 'blocksy' ),
			'slug' => 'palette-color-8',
			'color' => 'var(--paletteColor8, ' . $paletteColors['color8'] . ')',
			// 'color' => $paletteColors['color8']
		]
	]));

	add_theme_support(
		'editor-gradient-presets',
		apply_filters('blocksy:editor-gradient-presets', [
			[
				'name' => 'Vivid cyan blue to vivid purple',
				'gradient' => 'linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%)',
				'slug' => 'vivid-cyan-blue-to-vivid-purple',
			],

			[
				'name' => 'Light green cyan to vivid green cyan',
				'gradient' => 'linear-gradient(135deg,rgb(122,220,180) 0%,rgb(0,208,130) 100%)',
				'slug' => 'light-green-cyan-to-vivid-green-cyan',
			],

			[
				'name' => 'Luminous vivid amber to luminous vivid orange',
				'gradient' => 'linear-gradient(135deg,rgba(252,185,0,1) 0%,rgba(255,105,0,1) 100%)',
				'slug' => 'luminous-vivid-amber-to-luminous-vivid-orange',
			],

			[
				'name' => 'Luminous vivid orange to vivid red',
				'gradient' => 'linear-gradient(135deg,rgba(255,105,0,1) 0%,rgb(207,46,46) 100%)',
				'slug' => 'luminous-vivid-orange-to-vivid-red',
			],

			[
				'name' => 'Cool to warm spectrum',
				'gradient' => 'linear-gradient(135deg,rgb(74,234,220) 0%,rgb(151,120,209) 20%,rgb(207,42,186) 40%,rgb(238,44,130) 60%,rgb(251,105,98) 80%,rgb(254,248,76) 100%)',
				'slug' => 'cool-to-warm-spectrum',
			],

			[
				'name' => 'Blush light purple',
				'gradient' => 'linear-gradient(135deg,rgb(255,206,236) 0%,rgb(152,150,240) 100%)',
				'slug' => 'blush-light-purple',
			],

			[
				'name' => 'Blush bordeaux',
				'gradient' => 'linear-gradient(135deg,rgb(254,205,165) 0%,rgb(254,45,45) 50%,rgb(107,0,62) 100%)',
				'slug' => 'blush-bordeaux',
			],

			[
				'name' => 'Luminous dusk',
				'gradient' => 'linear-gradient(135deg,rgb(255,203,112) 0%,rgb(199,81,192) 50%,rgb(65,88,208) 100%)',
				'slug' => 'luminous-dusk',
			],

			[
				'name' => 'Pale ocean',
				'gradient' => 'linear-gradient(135deg,rgb(255,245,203) 0%,rgb(182,227,212) 50%,rgb(51,167,181) 100%)',
				'slug' => 'pale-ocean',
			],

			[
				'name' => 'Electric grass',
				'gradient' => 'linear-gradient(135deg,rgb(202,248,128) 0%,rgb(113,206,126) 100%)',
				'slug' => 'electric-grass',
			],

			[
				'name' => 'Midnight',
				'gradient' => 'linear-gradient(135deg,rgb(2,3,129) 0%,rgb(40,116,252) 100%)',
				'slug' => 'midnight',
			],

			[
				'name' => 'Juicy Peach',
				'gradient' => 'linear-gradient(to right, #ffecd2 0%, #fcb69f 100%)',
				'slug' => 'juicy-peach',
			],

			[
				'name' => 'Young Passion',
				'gradient' => 'linear-gradient(to right, #ff8177 0%, #ff867a 0%, #ff8c7f 21%, #f99185 52%, #cf556c 78%, #b12a5b 100%)',
				'slug' => 'young-passion',
			],

			[
				'name' => 'True Sunset',
				'gradient' => 'linear-gradient(to right, #fa709a 0%, #fee140 100%)',
				'slug' => 'true-sunset',
			],

			[
				'name' => 'Morpheus Den',
				'gradient' => 'linear-gradient(to top, #30cfd0 0%, #330867 100%)',
				'slug' => 'morpheus-den',
			],

			[
				'name' => 'Plum Plate',
				'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
				'slug' => 'plum-plate',
			],

			[
				'name' => 'Aqua Splash',
				'gradient' => 'linear-gradient(15deg, #13547a 0%, #80d0c7 100%)',
				'slug' => 'aqua-splash',
			],

			[
				'name' => 'Love Kiss',
				'gradient' => 'linear-gradient(to top, #ff0844 0%, #ffb199 100%)',
				'slug' => 'love-kiss',
			],

			[
				'name' => 'New Retrowave',
				'gradient' => 'linear-gradient(to top, #3b41c5 0%, #a981bb 49%, #ffc8a9 100%)',
				'slug' => 'new-retrowave',
			],

			[
				'name' => 'Plum Bath',
				'gradient' => 'linear-gradient(to top, #cc208e 0%, #6713d2 100%)',
				'slug' => 'plum-bath',
			],

			[
				'name' => 'High Flight',
				'gradient' => 'linear-gradient(to right, #0acffe 0%, #495aff 100%)',
				'slug' => 'high-flight',
			],

			[
				'name' => 'Teen Party',
				'gradient' => 'linear-gradient(-225deg, #FF057C 0%, #8D0B93 50%, #321575 100%)',
				'slug' => 'teen-party',
			],

			[
				'name' => 'Fabled Sunset',
				'gradient' => 'linear-gradient(-225deg, #231557 0%, #44107A 29%, #FF1361 67%, #FFF800 100%)',
				'slug' => 'fabled-sunset',
			],

			[
				'name' => 'Arielle Smile',
				'gradient' => 'radial-gradient(circle 248px at center, #16d9e3 0%, #30c7ec 47%, #46aef7 100%)',
				'slug' => 'arielle-smile',
			],

			[
				'name' => 'Itmeo Branding',
				'gradient' => 'linear-gradient(180deg, #2af598 0%, #009efd 100%)',
				'slug' => 'itmeo-branding',
			],

			[
				'name' => 'Deep Blue',
				'gradient' => 'linear-gradient(to right, #6a11cb 0%, #2575fc 100%)',
				'slug' => 'deep-blue',
			],

			[
				'name' => 'Strong Bliss',
				'gradient' => 'linear-gradient(to right, #f78ca0 0%, #f9748f 19%, #fd868c 60%, #fe9a8b 100%)',
				'slug' => 'strong-bliss',
			],

			[
				'name' => 'Sweet Period',
				'gradient' => 'linear-gradient(to top, #3f51b1 0%, #5a55ae 13%, #7b5fac 25%, #8f6aae 38%, #a86aa4 50%, #cc6b8e 62%, #f18271 75%, #f3a469 87%, #f7c978 100%)',
				'slug' => 'sweet-period',
			],

			[
				'name' => 'Purple Division',
				'gradient' => 'linear-gradient(to top, #7028e4 0%, #e5b2ca 100%)',
				'slug' => 'purple-division',
			],

			[
				'name' => 'Cold Evening',
				'gradient' => 'linear-gradient(to top, #0c3483 0%, #a2b6df 100%, #6b8cce 100%, #a2b6df 100%)',
				'slug' => 'cold-evening',
			],

			[
				'name' => 'Mountain Rock',
				'gradient' => 'linear-gradient(to right, #868f96 0%, #596164 100%)',
				'slug' => 'mountain-rock',
			],

			[
				'name' => 'Desert Hump',
				'gradient' => 'linear-gradient(to top, #c79081 0%, #dfa579 100%)',
				'slug' => 'desert-hump',
			],

			[
				'name' => 'Eternal Constance',
				'gradient' => 'linear-gradient(to top, #09203f 0%, #537895 100%)',
				'slug' => 'ethernal-constance',
			],

			[
				'name' => 'Happy Memories',
				'gradient' => 'linear-gradient(-60deg, #ff5858 0%, #f09819 100%)',
				'slug' => 'happy-memories',
			],

			[
				'name' => 'Grown Early',
				'gradient' => 'linear-gradient(to top, #0ba360 0%, #3cba92 100%)',
				'slug' => 'grown-early',
			],

			[
				'name' => 'Morning Salad',
				'gradient' => 'linear-gradient(-225deg, #B7F8DB 0%, #50A7C2 100%)',
				'slug' => 'morning-salad',
			],

			[
				'name' => 'Night Call',
				'gradient' => 'linear-gradient(-225deg, #AC32E4 0%, #7918F2 48%, #4801FF 100%)',
				'slug' => 'night-call',
			],

			[
				'name' => 'Mind Crawl',
				'gradient' => 'linear-gradient(-225deg, #473B7B 0%, #3584A7 51%, #30D2BE 100%)',
				'slug' => 'mind-crawl',
			],

			[
				'name' => 'Angel Care',
				'gradient' => 'linear-gradient(-225deg, #FFE29F 0%, #FFA99F 48%, #FF719A 100%)',
				'slug' => 'angel-care',
			],

			[
				'name' => 'Juicy Cake',
				'gradient' => 'linear-gradient(to top, #e14fad 0%, #f9d423 100%)',
				'slug' => 'juicy-cake',
			],

			[
				'name' => 'Rich Metal',
				'gradient' => 'linear-gradient(to right, #d7d2cc 0%, #304352 100%)',
				'slug' => 'rich-metal',
			],

			[
				'name' => 'Mole Hall',
				'gradient' => 'linear-gradient(-20deg, #616161 0%, #9bc5c3 100%)',
				'slug' => 'mole-hall',
			],

			[
				'name' => 'Cloudy Knoxville',
				'gradient' => 'linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%)',
				'slug' => 'cloudy-knoxville',
			],

			[
				'name' => 'Very light gray to cyan bluish gray',
				'gradient' => 'linear-gradient(135deg,rgb(238,238,238) 0%,rgb(169,184,195) 100%)',
				'slug' => 'very-light-gray-to-cyan-bluish-gray',
			],

			[
				'name' => 'Soft Grass',
				'gradient' => 'linear-gradient(to top, #c1dfc4 0%, #deecdd 100%)',
				'slug' => 'soft-grass',
			],

			[
				'name' => 'Saint Petersburg',
				'gradient' => 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)',
				'slug' => 'saint-petersburg',
			],

			[
				'name' => 'Everlasting Sky',
				'gradient' => 'linear-gradient(135deg, #fdfcfb 0%, #e2d1c3 100%)',
				'slug' => 'everlasting-sky',
			],

			[
				'name' => 'Kind Steel',
				'gradient' => 'linear-gradient(-20deg, #e9defa 0%, #fbfcdb 100%)',
				'slug' => 'kind-steel',
			],

			[
				'name' => 'Over Sun',
				'gradient' => 'linear-gradient(60deg, #abecd6 0%, #fbed96 100%)',
				'slug' => 'over-sun',
			],

			[
				'name' => 'Premium White',
				'gradient' => 'linear-gradient(to top, #d5d4d0 0%, #d5d4d0 1%, #eeeeec 31%, #efeeec 75%, #e9e9e7 100%)',
				'slug' => 'premium-white',
			],

			[
				'name' => 'Clean Mirror',
				'gradient' => 'linear-gradient(45deg, #93a5cf 0%, #e4efe9 100%)',
				'slug' => 'clean-mirror',
			],

			[
				'name' => 'Wild Apple',
				'gradient' => 'linear-gradient(to top, #d299c2 0%, #fef9d7 100%)',
				'slug' => 'wild-apple',
			],

			[
				'name' => 'Snow Again',
				'gradient' => 'linear-gradient(to top, #e6e9f0 0%, #eef1f5 100%)',
				'slug' => 'snow-again',
			],

			[
				'name' => 'Confident Cloud',
				'gradient' => 'linear-gradient(to top, #dad4ec 0%, #dad4ec 1%, #f3e7e9 100%)',
				'slug' => 'confident-cloud',
			],

			[
				'name' => 'Glass Water',
				'gradient' => 'linear-gradient(to top, #dfe9f3 0%, white 100%)',
				'slug' => 'glass-water',
			],

			[
				'name' => 'Perfect White',
				'gradient' => 'linear-gradient(-225deg, #E3FDF5 0%, #FFE6FA 100%)',
				'slug' => 'perfect-white',
			],
		], $paletteColors)
	);

	// remove_theme_support('widgets-block-editor');

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support('post-thumbnails');

	add_post_type_support('page', 'excerpt');

	$all_menus = [];

	$all_menus['footer'] = esc_html__('Footer Menu', 'blocksy');
	$all_menus['menu_1'] = esc_html__('Header Menu 1', 'blocksy');
	$all_menus['menu_2'] = esc_html__('Header Menu 2', 'blocksy');
	$all_menus['menu_mobile'] = esc_html__('Mobile Menu', 'blocksy');

	$all_menus = apply_filters('blocksy:register_nav_menus:input', $all_menus);

	// This theme uses wp_nav_menu in one location.
	if (! empty($all_menus)) {
		register_nav_menus($all_menus);
	}

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		[
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		]
	);

	// Registers support for Gutenberg wide images
	add_theme_support('align-wide');

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');
	add_theme_support('header-footer-elementor');
});


add_action('customize_save_after', function () {
	$i18n_manager = new Blocksy_Translations_Manager();
	$i18n_manager->register_wpml_translation_keys();
});

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
add_action('after_setup_theme', function () {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters(
		'blocksy_content_width',
		get_theme_mod('maxSiteWidth', 1290)
	);
}, 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
add_action(
	'widgets_init',
	function () {
		$sidebar_title_tag = get_theme_mod('widgets_title_wrapper', 'h2');

		register_sidebar(
			[
				'name' => esc_html__( 'Main Sidebar', 'blocksy' ),
				'id' => 'sidebar-1',
				'description' => esc_html__( 'Add widgets here.', 'blocksy' ),
				'before_widget' => '<div class="ct-widget %2$s" id="%1$s">',
				'after_widget' => '</div>',
				'before_title' => '<' . $sidebar_title_tag . ' class="widget-title">',
				'after_title' => '</' . $sidebar_title_tag . '>',
			]
		);

		do_action('blocksy:widgets_init', $sidebar_title_tag);

		$number_of_sidebars = 6;

		for ($i = 1; $i <= $number_of_sidebars; $i++) {
			register_sidebar(
				[
					'id' => 'ct-footer-sidebar-' . $i,
					'name' => esc_html__('Footer Widget Area ', 'blocksy') . $i,
					'before_widget' => '<div class="ct-widget %2$s" id="%1$s">',
					'after_widget' => '</div>',
					'before_title' => '<' . $sidebar_title_tag . ' class="widget-title">',
					'after_title' => '</' . $sidebar_title_tag . '>',
				]
			);
		}
	}
);

require get_template_directory() . '/inc/classes/print.php';
require get_template_directory() . '/inc/helpers.php';
require get_template_directory() . '/inc/helpers/html.php';
require get_template_directory() . '/inc/classes/hooks-manager.php';
require get_template_directory() . '/inc/classes/blocksy-walker-page.php';
require get_template_directory() . '/inc/classes/translations-manager.php';
require get_template_directory() . '/inc/classes/screen-manager.php';
require get_template_directory() . '/inc/classes/blocksy-blocks-parser.php';
require get_template_directory() . '/inc/classes/theme-db-versioning.php';
require get_template_directory() . '/inc/components/search.php';
require get_template_directory() . '/inc/components/global-attrs.php';
require get_template_directory() . '/inc/components/breadcrumbs.php';
require get_template_directory() . '/inc/components/vertical-spacing.php';
require get_template_directory() . '/inc/components/customizer-builder.php';
require get_template_directory() . '/inc/components/emoji-scripts.php';
require get_template_directory() . '/inc/schema-org.php';
require get_template_directory() . '/inc/classes/class-ct-css-injector.php';
require get_template_directory() . '/inc/classes/class-ct-attributes-parser.php';

require get_template_directory() . '/inc/css/fundamentals.php';
require get_template_directory() . '/inc/css/static-files.php';
require get_template_directory() . '/inc/css/colors.php';
require get_template_directory() . '/inc/css/selectors.php';
require get_template_directory() . '/inc/css/helpers.php';
require get_template_directory() . '/inc/css/box-shadow-option.php';
require get_template_directory() . '/inc/css/typography.php';
require get_template_directory() . '/inc/css/backgrounds.php';
require get_template_directory() . '/inc/dynamic-css.php';
require get_template_directory() . '/inc/sidebar.php';
require get_template_directory() . '/inc/sidebar-render.php';
require get_template_directory() . '/inc/single/single-helpers.php';
require get_template_directory() . '/inc/single/content-helpers.php';

require get_template_directory() . '/inc/components/menus.php';
require get_template_directory() . '/inc/components/post-meta.php';
require get_template_directory() . '/inc/components/pagination.php';
require get_template_directory() . '/inc/components/back-to-top.php';
require get_template_directory() . '/inc/components/hero-section.php';
require get_template_directory() . '/inc/components/social-box.php';

require get_template_directory() . '/inc/css/visibility.php';
require get_template_directory() . '/inc/meta-boxes.php';
require get_template_directory() . '/inc/components/posts-listing.php';

require get_template_directory() . '/inc/components/images.php';
require get_template_directory() . '/inc/components/gallery.php';

require get_template_directory() . '/inc/integrations/dfi.php';
require get_template_directory() . '/inc/integrations/yith.php';
require get_template_directory() . '/inc/integrations/avatars.php';
require get_template_directory() . '/inc/integrations/cdn.php';
require get_template_directory() . '/inc/integrations/stackable.php';
require get_template_directory() . '/inc/integrations/simply-static.php';
require get_template_directory() . '/inc/integrations/elementor.php';
require get_template_directory() . '/inc/integrations/zion.php';
require get_template_directory() . '/inc/integrations/generateblocks.php';
require get_template_directory() . '/inc/integrations/qubely.php';
require get_template_directory() . '/inc/integrations/tutorlms.php';
require get_template_directory() . '/inc/integrations/beaver-themer.php';
require get_template_directory() . '/inc/integrations/theme-builders.php';
require get_template_directory() . '/inc/integrations/custom-post-types.php';
require get_template_directory() . '/inc/integrations/cartflows.php';

require get_template_directory() . '/inc/archive/helpers.php';
require get_template_directory() . '/inc/archive/archive-card.php';

if (class_exists('WooCommerce')) {
	require get_template_directory() . '/inc/components/woocommerce-integration.php';
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-actions.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/footer.php';

require get_template_directory() . '/admin/helpers/all.php';

/**
 * Customizer additions.
 */

do_action('blocksy:customizer:load:before');

global $wp_customize;

if (isset($wp_customize)) {
	require get_template_directory() . '/inc/customizer/init.php';
}

if (is_admin()) {
	require get_template_directory() . '/admin/init.php';
}

require get_template_directory() . '/inc/manager.php';

if (!is_admin()) {
	add_filter('script_loader_tag', function ($tag, $handle) {
		$scripts = apply_filters('blocksy-async-scripts-handles', [
		]);

		if (in_array($handle, $scripts)) {
			return str_replace('<script ', '<script async ', $tag);
		}

		return $tag;

		// if the unique handle/name of the registered script has 'async' in it
		if (strpos($handle, 'async') !== false) {
			// return the tag with the async attribute
			return str_replace( '<script ', '<script async ', $tag );
		} else if (
			// if the unique handle/name of the registered script has 'defer' in it
			strpos($handle, 'defer') !== false
		) {
			// return the tag with the defer attribute
			return str_replace( '<script ', '<script defer ', $tag );
		} else {
			return $tag;
		}
	}, 10, 2);
}

Blocksy_Manager::instance();

