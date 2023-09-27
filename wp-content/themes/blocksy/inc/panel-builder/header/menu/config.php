<?php

$config = [
	'name' => __('Menu 1', 'blocksy'),
	'typography_keys' => ['headerMenuFont', 'headerDropdownFont'],
	'devices' => ['desktop'],
	'excluded_from' => ['offcanvas'],
	'selective_refresh' => [
		'menu',
		'stretch_menu',
		'dropdown_interaction',
		'dropdown_click_interaction'
	],
];
