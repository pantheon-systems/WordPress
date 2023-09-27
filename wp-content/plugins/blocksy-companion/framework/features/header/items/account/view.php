<?php

if (! isset($device)) {
	$device = 'desktop';
}

$current_user_id = get_current_user_id();

if (is_customize_preview()) {
	if (blocksy_akg('account_state', $atts, 'in') === 'out') {
		$current_user_id = null;
	}
}

$icon = apply_filters('blocksy:header:account:icons', [
	'type-1' => '<svg class="ct-icon" aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M7.5,0C3.4,0,0,3.4,0,7.5c0,1.7,0.5,3.2,1.5,4.5c1.4,1.9,3.6,3,6,3s4.6-1.1,6-3c1-1.3,1.5-2.9,1.5-4.5C15,3.4,11.6,0,7.5,0zM7.5,13.5c-1.4,0-2.8-0.5-3.8-1.4c1.1-0.9,2.4-1.4,3.8-1.4s2.8,0.5,3.8,1.4C10.3,13,8.9,13.5,7.5,13.5z M12.3,11c-1.3-1.1-3-1.8-4.8-1.8S4,9.9,2.7,11c-0.8-1-1.2-2.2-1.2-3.5c0-3.3,2.7-6,6-6s6,2.7,6,6C13.5,8.8,13.1,10,12.3,11zM7.5,3C6.1,3,5,4.1,5,5.5S6.1,8,7.5,8S10,6.9,10,5.5S8.9,3,7.5,3zM7.5,6.5c-0.5,0-1-0.5-1-1s0.5-1,1-1s1,0.5,1,1S8,6.5,7.5,6.5z"/></svg>',

	'type-2' => '<svg class="ct-icon" aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M13,7V6.1h0V3.9c0-1.4-1.1-2.6-2.6-2.6H9.7c0,0-0.6-1.3-3.2-1.3C4.1,0,1.9,2,2,4.5V7C1.4,7.4,1,8.1,1,8.8c0,0.9,0.6,1.8,1.4,2.1c0.8,2.1,2.7,3.7,4.9,4l0.1,0l0.1,0c2.3-0.4,4.1-1.9,5-4c0.8-0.3,1.4-1.2,1.4-2.1C14,8.1,13.6,7.4,13,7zM11.9,9.6l-0.5,0.1l-0.1,0.4c-0.5,1.7-2,3-3.8,3.3c-1.8-0.3-3.2-1.6-3.8-3.4L3.6,9.6L3.1,9.6C2.8,9.5,2.5,9.2,2.5,8.8c0-0.3,0.2-0.6,0.5-0.7L3.5,8V6.5h2.2c1.1,0,2-0.5,2.4-1.3h1.6c0.8,0,1.6,0.5,1.8,1.3V8L12,8.2c0.3,0.1,0.5,0.4,0.5,0.7C12.5,9.3,12.2,9.6,11.9,9.6z"/></svg>',

	'type-3' => '<svg class="ct-icon" aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M10.5,9h-6c-2.1,0-3.8,1.7-3.8,3.8v1.5c0,0.4,0.3,0.8,0.8,0.8s0.8-0.3,0.8-0.8v-1.5c0-1.2,1-2.2,2.2-2.2h6c1.2,0,2.2,1,2.2,2.2v1.5c0,0.4,0.3,0.8,0.8,0.8s0.8-0.3,0.8-0.8v-1.5C14.2,10.7,12.6,9,10.5,9zM7.5,7C9.4,7,11,5.4,11,3.5
	S9.4,0,7.5,0S4,1.6,4,3.5S5.6,7,7.5,7zM7.5,1.5c1.1,0,2,0.9,2,2s-0.9,2-2,2s-2-0.9-2-2S6.4,1.5,7.5,1.5z"/></svg>',

	'type-4' => '<svg class="ct-icon" aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M7.5 0C3.4 0 0 3.4 0 7.5S3.4 15 7.5 15 15 11.6 15 7.5 11.6 0 7.5 0zm0 2.1c1.4 0 2.5 1.1 2.5 2.4S8.9 7 7.5 7 5 5.9 5 4.5s1.1-2.4 2.5-2.4zm0 11.4c-2.1 0-3.9-1-5-2.6C3.4 9.6 6 9 7.5 9s4.1.6 5 1.9c-1.1 1.6-2.9 2.6-5 2.6z"/></svg>',

	'type-5' => '<svg class="ct-icon" aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M7.5,0C3.4,0,0,3.4,0,7.5S3.4,15,7.5,15S15,11.6,15,7.5S11.6,0,7.5,0z M11.6,11.9c-0.5-0.6-1.5-1-2.7-1.3c0,0-0.6-0.2-0.4-0.7c0.6-0.6,0.7-1.1,0.7-1.2c0,0,0.6-0.5,0.6-1.1C10,7,9.8,6.9,9.8,6.9c0.2-0.7,0.3-3.3-1.4-3C8.1,3.4,6.4,3,5.7,4.4C5.3,5.1,5.1,6.2,5.5,6.9c0,0-0.1-0.1-0.2,0.3c0,0.4,0.2,0.9,0.4,1.1c0.1,0.1,0.2,0.2,0.3,0.2c0,0,0.1,0.6,0.6,1.2c0.1,0.6-0.4,0.8-0.4,0.8c-1.2,0.2-2.3,0.7-2.7,1.4c-1.2-1.1-1.9-2.6-1.9-4.4c0-3.3,2.7-6,6-6s6,2.7,6,6C13.5,9.2,12.8,10.8,11.6,11.9z"/></svg>',

	'type-6' => '<svg class="ct-icon" aria-hidden="true" width="15" height="15" viewBox="0 0 15 15"><path d="M14.2,5.2l-6.3-5C7.6-0.1,7.2-0.1,7,0.2l-6.2,5C0.6,5.3,0.5,5.5,0.5,5.7v7.5c0,1,0.8,1.8,1.8,1.8h10.5c1,0,1.8-0.8,1.8-1.8V5.7C14.5,5.5,14.4,5.3,14.2,5.2z M8.8,13.5H6.3V9h2.5V13.5zM13,13.2c0,0.1-0.1,0.2-0.2,0.2h-2.5V8.2c0-0.4-0.3-0.8-0.8-0.8h-4c-0.4,0-0.8,0.3-0.8,0.8v5.2H2.3c-0.1,0-0.2-0.1-0.2-0.2V6.1l5.4-4.4L13,6.1V13.2z"/></svg>',
]);

$path = 'login';

if (! $current_user_id) {
	$path = 'logout';
}

$account_user_visibility = blocksy_default_akg(
	'account_user_visibility',
	$atts,
	[
		'logged_in' => true,
		'logged_out' => true,
	]
);

if (
	$path === 'login' && ! $account_user_visibility['logged_in']
	||
	$path === 'logout' && ! $account_user_visibility['logged_out']
) {
	return;
}

$attr['class'] = trim('ct-header-account ' . blocksy_visibility_classes(
	blocksy_default_akg(
		'header_account_visibility',
		$atts,
		[
			'tablet' => true,
			'mobile' => true,
		]
	)
));

echo blocksy_render_view(
	dirname(__FILE__) . '/views/' . $path . '.php',
	[
		'atts' => $atts,
		'attr' => $attr,
		'icon' => $icon,
		'device' => $device,
		'current_user_id' => $current_user_id,
		'section_id' => $section_id,
		'item_id' => $item_id
	]
);

