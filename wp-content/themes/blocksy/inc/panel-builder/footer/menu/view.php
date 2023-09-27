<?php

if (! isset($location)) {
	$location = 'footer';
}

if (empty($class)) {
	$class = 'footer-menu-inline';
}

if (empty($id)) {
	$id = 'footer-menu';
}

$class .= ' ' . blocksy_visibility_classes(blocksy_default_akg(
	'footer_menu_visibility',
	$atts,
	[
		'desktop' => true,
		'tablet' => true,
		'mobile' => true,
	]
));

$items_direction = blocksy_expand_responsive_value(
	blocksy_default_akg('menu_items_direction', $atts, 'horizontal')
);

$stretch_output = '';

if (
	(
		$items_direction['desktop'] === 'horizontal'
		||
		$items_direction['tablet'] === 'horizontal'
		||
		$items_direction['mobile'] === 'horizontal'
	)
	&&
	blocksy_default_akg('stretch_menu', $atts, 'no') === 'yes'
) {
	$stretch_output = 'data-stretch';
}

$menu_args = [
	'container' => false,
	'menu_class' => 'menu',
	'depth' => 1,
	'fallback_cb' => 'blocksy_main_menu_fallback',
	'blocksy_advanced_item' => true,
	'theme_location' => $location
];

$menu = blocksy_default_akg('menu', $atts, 'blocksy_location');

if ($menu === 'blocksy_location') {
} else {
	$menu_args['menu'] = $menu;
}

ob_start();
wp_nav_menu($menu === 'blocksy_location' ? [
	'container' => false,
	'menu_class' => 'menu',
	'depth' => 1,
	'fallback_cb' => 'blocksy_main_menu_fallback',
	'blocksy_advanced_item' => true,
	'theme_location' => $location
] : array_merge([
	'container' => false,
	'menu_class' => 'menu',
	'depth' => 1,
	'fallback_cb' => 'blocksy_main_menu_fallback',
	'blocksy_advanced_item' => true,
], $menu_args));
$menu_content = ob_get_clean();

?>

<nav
	id="<?php echo $id?>"
	class="<?php echo esc_attr($class) ?>"
	<?php echo blocksy_attr_to_html($attr) ?>
	<?php echo $stretch_output ?>
	<?php echo blocksy_schema_org_definitions('navigation') ?>
	aria-label="<?php echo __('Footer Menu', 'blocksy')?>">

	<?php echo $menu_content; ?>
</nav>
