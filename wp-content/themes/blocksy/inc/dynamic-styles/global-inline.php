<?php

$args = apply_filters('blocksy:header:dynamic-styles-args', [
	'section_id' => blocksy_manager()->header_builder->get_current_section_id(),
	'check_transparent_conditions' => true
]);

do_action(
	'blocksy:global-dynamic-css:enqueue:inline',
	[
		'context' => $context,
		'css' => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css
	]
);

if (
	! isset($args['has_transparent_header'])
	||
	! $args['has_transparent_header']
) {
	return;
}

$render = new Blocksy_Header_Builder_Render([
	'current_section_id' => $args['section_id']
]);

$root_selector = $render->get_root_selector();

$has_transparent_header = $args['has_transparent_header'];

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector($root_selector),
	'variableName' => 'has-transparent-header',
	'value' => [
		'desktop' => in_array('desktop', $has_transparent_header) ? '1' : '0',
		'tablet' => in_array('mobile', $has_transparent_header) ? '1' : '0',
		'mobile' => in_array('mobile', $has_transparent_header) ? '1' : '0'
	],
	'unit' => ''
]);

