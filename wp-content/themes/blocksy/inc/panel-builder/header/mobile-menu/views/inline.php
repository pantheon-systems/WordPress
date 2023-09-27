<?php

if (empty($class)) {
	$class = 'mobile-menu-inline';
}

$class .= ' ' . blocksy_visibility_classes(blocksy_default_akg(
	'inline_menu_visibility',
	$atts,
	[
		'tablet' => true,
		'mobile' => true,
	]
));

$stretch_output = '';

if (blocksy_default_akg('inline_menu_stretch_menu', $atts, 'no') === 'yes') {
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
	class="<?php echo esc_attr($class) ?>"
	<?php echo blocksy_attr_to_html($attr) ?>
	<?php echo $stretch_output ?>
	<?php echo blocksy_schema_org_definitions('navigation') ?>
	aria-label="<?php echo __('Mobile Menu', 'blocksy')?>">
	<?php echo $menu_content; ?>
</nav>

