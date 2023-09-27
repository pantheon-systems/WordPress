<?php

if (! function_exists('blocksy_menu_get_child_svgs')) {
	function blocksy_menu_get_child_svgs() {
		return [
			'default' => '<svg class="ct-icon" width="8" height="8" viewBox="0 0 15 15"><path d="M2.1,3.2l5.4,5.4l5.4-5.4L15,4.3l-7.5,7.5L0,4.3L2.1,3.2z"/></svg>',

			'mobile-toggle-type-1' => '<svg class="ct-icon toggle-icon-1" width="15" height="15" viewBox="0 0 15 15"><path d="M3.9,5.1l3.6,3.6l3.6-3.6l1.4,0.7l-5,5l-5-5L3.9,5.1z"/></svg>',

			'mobile-toggle-type-2' => '<svg class="ct-icon toggle-icon-2" width="15" height="15" viewBox="0 0 15 15"><path d="M14.1,6.6H8.4V0.9C8.4,0.4,8,0,7.5,0S6.6,0.4,6.6,0.9v5.7H0.9C0.4,6.6,0,7,0,7.5s0.4,0.9,0.9,0.9h5.7v5.7C6.6,14.6,7,15,7.5,15s0.9-0.4,0.9-0.9V8.4h5.7C14.6,8.4,15,8,15,7.5S14.6,6.6,14.1,6.6z"/></svg>',

			'mobile-toggle-type-3' => '<svg class="ct-icon toggle-icon-3" width="12" height="12" viewBox="0 0 15 15"><path d="M2.6,5.8L2.6,5.8l4.3,5C7,11,7.3,11.1,7.5,11.1S8,11,8.1,10.8l4.2-4.9l0.1-0.1c0.1-0.1,0.1-0.2,0.1-0.3c0-0.3-0.2-0.5-0.5-0.5l0,0H3l0,0c-0.3,0-0.5,0.2-0.5,0.5C2.5,5.7,2.5,5.8,2.6,5.8z"/></svg>',
		];
	}
}

if (! function_exists('blocksy_main_menu_fallback')) {
	function blocksy_main_menu_fallback($args) {
		extract($args);

		$list_pages_args = [
			'sort_column' => 'menu_order, post_title',
			'menu_id' => 'primary-menu',
			'menu_class' => 'primary-menu menu',
			'container' => 'ul',
			'echo' => false,
			'link_before' => '',
			'link_after' => '',
			'before' => '<ul>',
			'after' => '</ul>',
			'item_spacing' => 'discard',
			'walker' => new Blocksy_Walker_Page(),
			'title_li' => ''
		];

		if (isset($args['blocksy_mega_menu'])) {
			$list_pages_args['blocksy_mega_menu'] = $args['blocksy_mega_menu'];
		}

		if (isset($args['blocksy_advanced_item'])) {
			$list_pages_args['blocksy_advanced_item'] = $args['blocksy_advanced_item'];
		}

		if (isset($args['skip_ghost'])) {
			$list_pages_args['skip_ghost'] = $args['skip_ghost'];
		}

		$menu = wp_list_pages($list_pages_args);

		if (! isset($child_indicator_type)) {
			$child_indicator_type = 'default';
		}

		$svg = '';
		$before_link = '';
		$link_button = '';

		if ($child_indicator_type !== 'skip') {
			if (
				! (
					isset($args['child_indicator_wrapper'])
					&&
					$args['child_indicator_wrapper']
				)
			) {
				$svg = blocksy_html_tag(
					'span',
					[
						'class' => 'ct-toggle-dropdown-desktop',
						'role' => 'button'
					],
					blocksy_menu_get_child_svgs()[$child_indicator_type]
				);

				if (! isset($args['skip_ghost'])) {
					$link_button = blocksy_html_tag(
						'button',
						[
							'class' => 'ct-toggle-dropdown-desktop-ghost',
							'aria-label' => __('Expand dropdown menu', 'blocksy'),
							'aria-haspopup' => 'true',
							'aria-expanded' => 'false'
						],
						''
					);
				}
			} else {
				$link_button = blocksy_html_tag(
					'button',
					[
						'class' => 'ct-toggle-dropdown-mobile',
						'aria-label' => __('Expand dropdown menu', 'blocksy'),
						'aria-haspopup' => 'true',
						'aria-expanded' => 'false'
					],
					blocksy_menu_get_child_svgs()['mobile-toggle-' . $child_indicator_type]
				) . '</span>';

				$before_link = '<span class="ct-sub-menu-parent">';
			}
		}

		if ($args['depth'] === 1) {
			$svg = '';
			$link_button = '';
			$before_link = '';
		}

		$menu = str_replace(
			'~',
			$svg,
			$menu
		);

		$menu = str_replace(
			'^^',
			$before_link,
			$menu
		);

		$menu = str_replace(
			'^',
			$link_button,
			$menu
		);

		if (empty(trim($menu))) {
			$args['echo'] = false;
			$menu = blocksy_link_to_menu_editor($args);
		} else {
			$attrs = '';

			if (! empty($args['menu_id'])) {
				$attrs .= ' id="' . esc_attr($args['menu_id']) . '"';
			}

			if (! empty($args['menu_class'])) {
				$attrs .= ' class="' . esc_attr($args['menu_class']) . '"';
			}

			$menu = "<ul{$attrs}>" . $menu . "</ul>";
		}

		if ($echo) {
			echo $menu;
		}

		return $menu;
	}
}

if (! function_exists('blocksy_handle_nav_menu_item_title')) {
	function blocksy_handle_nav_menu_item_title($item_output, $item, $args, $depth) {
		$classes = empty($item->classes) ? [] : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join(' ', array_filter($classes));

		$child_indicator_type = 'default';

		if (isset($args->child_indicator_type)) {
			$child_indicator_type = $args->child_indicator_type;
		}

		if (
			$child_indicator_type !== 'skip'
			&&
			! (
				isset($args->child_indicator_wrapper)
				&&
				$args->child_indicator_wrapper
			)
		) {
			$svg = blocksy_menu_get_child_svgs()[$child_indicator_type];

			if (
				strpos($class_names, 'has-children') !== false
				||
				strpos($class_names, 'has_children') !== false
			) {
				if (! empty($item_output)) {
					return $item_output . '<span class="ct-toggle-dropdown-desktop">' . $svg . '</span>';
				}
			}
		}

		return $item_output;
	}
}

if (! function_exists('blocksy_handle_nav_menu_start_el')) {
	function blocksy_handle_nav_menu_start_el($item_output, $item, $depth, $args) {
		$classes = empty($item->classes) ? [] : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join(' ', array_filter($classes));

		if (
			isset($args->uber_instance)
			&&
			$args->uber_instance
		) {
			return $item_output;
		}

		$child_indicator_type = 'default';

		if (isset($args->child_indicator_type)) {
			$child_indicator_type = $args->child_indicator_type;
		}

		if (
			$child_indicator_type !== 'skip'
			&&
			(
				strpos($class_names, 'has-children') !== false
				||
				strpos($class_names, 'has_children') !== false
			)
		) {
			$toggle_ghost_content = '';
			$toggle_ghost_class = 'ct-toggle-dropdown-desktop-ghost';

			if (
				isset($args->child_indicator_wrapper)
				&&
				$args->child_indicator_wrapper
			) {
				$svg = blocksy_menu_get_child_svgs()['mobile-toggle-' . $child_indicator_type];
				$toggle_ghost_content = $svg;
				$toggle_ghost_class = 'ct-toggle-dropdown-mobile';
			}

			$toggle_ghost = '';

			if (! isset($args->skip_ghost)) {
				$toggle_ghost = blocksy_html_tag(
					'button',
					[
						'class' => $toggle_ghost_class,
						'aria-label' => __('Expand dropdown menu', 'blocksy'),
						'aria-haspopup' => 'true',
						'aria-expanded' => 'false'
					],
					$toggle_ghost_content
				);
			}

			if (
				isset($args->child_indicator_wrapper)
				&&
				$args->child_indicator_wrapper
			) {
				return '<span class="ct-sub-menu-parent">' . $item_output . $toggle_ghost . '</span>';
			}

			return $item_output . $toggle_ghost;
		}

		return $item_output;
	}
}

add_filter(
	'page_css_class',
	function ($css_class, $page, $depth, $args, $current_page) {
		if (isset($args['pages_with_children'][$page->ID])) {
			$css_class[] = 'menu-item-has-children';
		}

		if (! empty($current_page)) {
			$_current_page = get_post($current_page);

			if (
				$_current_page
				&&
				in_array($page->ID, $_current_page->ancestors)
			) {
				$css_class[] = 'current-menu-ancestor';
			}

			if ($page->ID === $current_page) {
				$css_class[] = 'current-menu-item';
			} elseif (
				$_current_page
				&&
				$page->ID === $_current_page->post_parent
			) {
				$css_class[] = 'current-menu-parent';
			}
		} elseif (get_option('page_for_posts') === $page->ID) {
			$css_class[] = 'current-menu-parent';
		}

		if (
			! isset($args['blocksy_mega_menu'])
			||
			! $args['blocksy_mega_menu']
		) {
			return $css_class;
		}

		$classes_str = implode(' ', $css_class);

		if (
			strpos($classes_str, 'has-children') === false
			&&
			strpos($classes_str, 'has_children') === false
		) {
			return $css_class;
		}

		$css_class[] = 'animated-submenu';

		return $css_class;
	},
	10, 5
);

add_filter('wp_nav_menu_items', function ($item_output, $args) {
	if (
		! isset($args->blocksy_advanced_item)
		||
		! $args->blocksy_advanced_item
	) {
		return $item_output;
	}

	return preg_replace(
		'/(<li\b[^><]*)>/i',
		'$1 role="none">',
		$item_output
	);
}, 10, 2);

add_filter(
	'nav_menu_css_class',
	function ($classes, $item, $args, $depth) {
		if (
			! isset($args->blocksy_mega_menu)
			||
			! $args->blocksy_mega_menu
		) {
			return $classes;
		}

		$classes_str = implode(' ', $classes);

		if (
			strpos($classes_str, 'has-children') === false
			&&
			strpos($classes_str, 'has_children') === false
		) {
			return $classes;
		}

		if (
			apply_filters('blocksy:menu:has_animated_submenu', true, $item, $args)
			||
			$depth === 0
		) {
			$classes[] = 'animated-submenu';
		}

		return $classes;
	},
	50, 4
);

add_filter('wp_nav_menu', function ($nav_menu, $args) {
	if (
		! isset($args->blocksy_advanced_item)
		||
		! $args->blocksy_advanced_item
	) {
		return $nav_menu;
	}

	$nav_menu = str_replace(
		'class="sub-menu"',
		'class="sub-menu" role="menu"',
		$nav_menu
	);

	$nav_menu = str_replace(
		'class="menu"',
		'class="menu" role="menubar"',
		$nav_menu
	);

	$nav_menu = preg_replace(
		'/(<ul\b[^><]*) class="">/i',
		'$1 role="menubar">',
		$nav_menu
	);

	return $nav_menu;
}, 10, 2);

add_filter(
	'nav_menu_link_attributes',
	function ($attr, $item, $args, $depth) {
		if (
			! isset($args->blocksy_advanced_item)
			||
			! $args->blocksy_advanced_item
		) {
			return $attr;
		}

		if (
			isset($args->uber_instance)
			&&
			$args->uber_instance
		) {
			return $attr;
		}

		$class = 'ct-menu-link';

		if (! isset($attr['class'])) {
			$attr['class'] = '';
		}

		$attr['class'] .= ' ' . $class;

		$attr['class'] = trim($attr['class']);

		$attr['role'] = 'menuitem';

		if (isset($args->skip_ghost)) {
			$item_classes = '';

			if ($item && isset($item->classes) && is_array($item->classes)) {
				$item_classes = implode(' ', $item->classes);
			}

			if (
				strpos($item_classes, 'has-children') !== false
				||
				strpos($item_classes, 'has_children') !== false
			) {
				$attr['aria-haspopup'] = 'true';
				$attr['aria-expanded'] = 'false';
			}
		}

		return $attr;
	},
	5, 4
);

add_filter(
	'page_menu_link_attributes',
	function ($attr, $item, $depth, $args) {
		if (
			! isset($args['blocksy_advanced_item'])
			||
			! $args['blocksy_advanced_item']
		) {
			return $attr;
		}

		$class = 'ct-menu-link';

		if (! isset($attr['class'])) {
			$attr['class'] = '';
		}

		$attr['class'] .= ' ' . $class;

		$attr['class'] = trim($attr['class']);

		if (isset($args['skip_ghost'])) {
			$attr['aria-haspopup'] = 'true';
			$attr['aria-expanded'] = 'false';
		}

		return $attr;
	},
	5, 4
);

if (! function_exists('blocksy_get_menus_items')) {
	function blocksy_get_menus_items($location = '') {
		$menus = [
			// 'blocksy_location' => $location
			'blocksy_location' => __('Default', 'blocksy')
		];

		$all_menus = get_terms('nav_menu', ['hide_empty' => true]);

		if (is_array($all_menus) && count($all_menus)) {
			foreach($all_menus as $row) {
				$menus[$row->term_id] = $row->name;
			}
		}

		$result = [];

		foreach ($menus as $id => $menu){
			$result[$id] = $menu;
		}

		return $result;
	}
}

