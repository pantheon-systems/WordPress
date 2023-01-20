<?php

$mobile_menu_type = blocksy_default_akg('mobile_menu_type', $atts, 'type-1');

$mobile_menu_interactive = blocksy_akg('mobile_menu_interactive', $atts, 'yes');

if ($mobile_menu_interactive === 'yes') {
	$attr['data-interaction'] = 'click';
	$attr['data-toggle-type'] = blocksy_akg(
		'mobile_menu_toggle_shape',
		$atts,
		'type-1'
	);
}


ob_start();

$menu_args = [];

$menu = blocksy_default_akg('menu', $atts, 'blocksy_location');

if ($menu === 'blocksy_location') {
} else {
	$menu_args['menu'] = $menu;
}

$menu_args['child_indicator_wrapper'] = 'yes';

$menu_args['child_indicator_type'] = $mobile_menu_type;

if ($mobile_menu_interactive !== 'yes') {
	$menu_args['child_indicator_type'] = 'skip';
}


add_filter(
	'walker_nav_menu_start_el',
	'blocksy_handle_nav_menu_start_el',
	10, 4
);

add_filter(
	'nav_menu_item_title',
	'blocksy_handle_nav_menu_item_title',
	10, 4
);

wp_nav_menu($menu === 'blocksy_location' ? array_merge([
	'container' => false,
	'menu_class' => false,
	'fallback_cb' => 'blocksy_main_menu_fallback',
	'blocksy_advanced_item' => true,
	'theme_location' => $location
], $menu_args) : array_merge([
	'container' => false,
	'menu_class' => false,
	'fallback_cb' => 'blocksy_main_menu_fallback',
	'blocksy_advanced_item' => true
], $menu_args));

remove_filter(
	'nav_menu_item_title',
	'blocksy_handle_nav_menu_item_title',
	10, 4
);

remove_filter(
	'walker_nav_menu_start_el',
	'blocksy_handle_nav_menu_start_el',
	10, 4
);

$menu_output = ob_get_clean();

$class = 'mobile-menu';

if (strpos($menu_output, 'sub-menu')) {
	$class .= ' has-submenu';
}

?>

<nav 
	class="<?php echo $class ?>" 
	<?php echo blocksy_attr_to_html($attr) ?>
	aria-label="<?php echo __('Off Canvas Menu', 'blocksy')?>">
	<?php echo $menu_output ?>
</nav>

