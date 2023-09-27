<?php
/**
 * Sidebar helpers
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

add_filter('widget_nav_menu_args', function ($nav_menu_args, $nav_menu, $args, $instance) {
	$nav_menu_args['menu_class'] = 'widget-menu';
	return $nav_menu_args;
}, 10, 4);

add_action(
	'dynamic_sidebar_before',
	function () {
		ob_start();
	}
);

add_action(
	'dynamic_sidebar_after',
	function () {
		$text = str_replace(
			'textwidget',
			'textwidget entry-content',
			ob_get_clean()
		);

		echo $text;
	}
);

if (! function_exists('blocksy_get_sidebar_to_render')) {
	function blocksy_get_sidebar_to_render() {
		if (class_exists('BlocksySidebarsManager')) {
			$manager = new BlocksySidebarsManager();

			$maybe_sidebar = $manager->maybe_get_sidebar_that_matches();

			if ($maybe_sidebar) {
				return $maybe_sidebar;
			}
		}

		$prefix = blocksy_manager()->screen->get_prefix();

		if ($prefix === 'product' || $prefix === 'woo_categories') {
			return 'sidebar-woocommerce';
		}

		return 'sidebar-1';
	}
}

if (! function_exists('blocksy_sidebar_position_attr')) {
	function blocksy_sidebar_position_attr($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'attr_id' => 'data-sidebar',
				'array' => false
			]
		);

		if ($args['array']) {
			if (blocksy_sidebar_position() !== 'none') {
				return [
					$args['attr_id'] => blocksy_sidebar_position()
				];
			} else {
				return [];
			}
		}

		return (
			blocksy_sidebar_position() === 'none'
		) ? '' : $args['attr_id'] . '="' . blocksy_sidebar_position() . '"';
	}
}

if (! function_exists('blocksy_get_single_page_structure')) {
	function blocksy_get_single_page_structure() {
		$default_page_structure = blocksy_default_akg(
			'page_structure_type',
			blocksy_get_post_options(),
			'default'
		);

		if ($default_page_structure !== 'default') {
			return $default_page_structure;
		}

		$prefix = blocksy_manager()->screen->get_prefix();

		$result = 'none';

		if (
			! is_singular()
			&&
			$prefix !== 'bbpress_single'
			&&
			$prefix !== 'buddypress_single'
			&&
			(
				$prefix !== 'courses_archive'
				&&
				function_exists('tutor')
				||
				! function_exists('tutor')
			)
		) {
			$result = 'none';
		} else {
			$default_structure = ($prefix === 'single_blog_post') ? 'type-3' : 'type-4';

			if ($prefix === 'courses_single' && function_exists('tutor')) {
				$default_structure = 'type-1';
			}

			$result = get_theme_mod($prefix . '_structure', $default_structure);

			if ($prefix === 'courses_single' && function_exists('tutor')) {
				$current_template = blocksy_manager()->get_current_template();

				if ($current_template !== tutor_get_template('single-course')) {
					$result = 'type-4';
				}
			}
		}

		return apply_filters('blocksy:global:page_structure', $result);
	}
}

if (! function_exists('blocksy_sidebar_position')) {
	function blocksy_sidebar_position() {
		return apply_filters(
			'blocksy:general:sidebar-position',
			blocksy_sidebar_position_unfiltered()
		);
	}
}

if (! function_exists('blocksy_sidebar_position_unfiltered')) {
	function blocksy_sidebar_position_unfiltered($prefix = null) {
		$prefix = blocksy_manager()->screen->get_prefix();

		if ($prefix === 'lms') {
			return 'right';
		}

		global $blocksy_template_output;

		if (
			isset($blocksy_template_output)
			&&
			$blocksy_template_output
		) {
			$page_structure_type = blocksy_get_single_page_structure();

			if ('type-1' === $page_structure_type) {
				return 'right';
			}

			if ('type-2' === $page_structure_type) {
				return 'left';
			}

			return 'none';
		}

		$is_dokan_store = class_exists('WeDevs_Dokan') && function_exists('dokan_is_store_page') && dokan_is_store_page();

		if ($is_dokan_store) {
			return 'none';
		}

		$blog_post_structure = blocksy_listing_page_structure([
			'prefix' => $prefix
		]);

		if (
			strpos($prefix, '_archive') !== false
			||
			$prefix === 'search'
			||
			$prefix === 'categories'
			||
			$prefix === 'author'
			||
			$prefix === 'blog'
			||
			$prefix === 'woo_categories'
		) {
			if (
				$prefix !== 'courses_archive'
				&&
				function_exists('tutor')
				||
				! function_exists('tutor')
			) {
				if (
					get_theme_mod($prefix . '_has_sidebar', 'no') === 'no'
					||
					$blog_post_structure === 'gutenberg'
				) {
					return 'none';
				}

				return get_theme_mod($prefix . '_sidebar_position', 'right');
			}
		}

		if (
			$prefix !== 'single_page'
			&&
			$prefix !== 'single_blog_post'
			&&
			$prefix !== 'product'
			&&
			strpos($prefix, '_single') === false
			&&
			(
				$prefix !== 'courses_archive'
				&&
				function_exists('tutor')
				||
				! function_exists('tutor')
			)
		) {
			return 'right';
		}

		$page_structure_type = blocksy_get_single_page_structure();

		if ('type-1' === $page_structure_type) {
			return 'right';
		}

		if ('type-2' === $page_structure_type) {
			return 'left';
		}

		return 'none';
	}
}

if (! function_exists('blocksy_get_page_structure')) {
	function blocksy_get_page_structure() {
		$page_structure_type = blocksy_get_single_page_structure();

		if ('type-3' === $page_structure_type) {
			return 'narrow';
		}

		if (
			$page_structure_type === 'type-4'
			||
			$page_structure_type === 'type-5'
		) {
			return 'normal';
		}

		return 'none';
	}
}

