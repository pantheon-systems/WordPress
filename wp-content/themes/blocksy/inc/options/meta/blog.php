<?php

$options = [
	[
		'disable_header' => [
			'label' => __( 'Disable Header', 'blocksy' ),
			'type' => 'ct-switch',
			'value' => 'no',
		],

		'disable_footer' => [
			'label' => __( 'Disable Footer', 'blocksy' ),
			'type' => 'ct-switch',
			'value' => 'no',
		],
	],

	apply_filters(
		'blocksy_extensions_metabox_page_bottom',
		[]
	)
];

