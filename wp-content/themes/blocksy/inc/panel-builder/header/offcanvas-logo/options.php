<?php

$options = [

	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'custom_logo' => [
				'label' => __( 'Logo', 'blocksy' ),
				'type' => 'ct-image-uploader',
				'value' => get_theme_mod('custom_logo', ''),
				'inline_value' => true,
				'attr' => [ 'data-type' => 'small' ],
			],

			'off_canvas_logo_max_height' => [
				'label' => __( 'Logo Height', 'blocksy' ),
				'type' => 'ct-slider',
				'divider' => 'top:full',
				'min' => 0,
				'max' => 300,
				'value' => 50,
				'responsive' => true,
			],

		],
	],

	blocksy_rand_md5() => [
		'title' => __( 'Design', 'blocksy' ),
		'type' => 'tab',
		'options' => [

			'off_canvas_logo_margin' => [
				'label' => __( 'Margin', 'blocksy' ),
				'type' => 'ct-spacing',
				'setting' => [ 'transport' => 'postMessage' ],
				'value' => blocksy_spacing_value([
					'linked' => true,
				]),
				'responsive' => true
			],

		],
	],

];
