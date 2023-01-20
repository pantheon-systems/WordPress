<?php

$options = [
	'footer_general_section_options' => [
		'type' => 'ct-options',
		'setting' => [ 'transport' => 'postMessage' ],
		'customizer_section' => 'layout',
		'inner-options' => [
			'footer_placements' => [
				'type' => 'ct-footer-builder',
				'setting' => ['transport' => 'postMessage'],
				'value' => Blocksy_Manager::instance()->footer_builder->get_default_value(),
				'selective_refresh' => apply_filters('blocksy:footer:selective_refresh', [
					[
						'id' => 'footer_placements_1',
						'fallback_refresh' => false,
						'container_inclusive' => true,
						'selector' => '#main-container > footer.ct-footer',
						'settings' => ['footer_placements'],
						'render_callback' => function () {
							echo Blocksy_Manager::instance()->footer_builder->render();
						}
					],

					[
						'id' => 'footer_placements_item:menu',
						'fallback_refresh' => false,
						'container_inclusive' => true,
						'selector' => '#main-container > footer.ct-footer',
						'loader_selector' => '.footer-menu',
						'settings' => ['footer_placements'],
						'render_callback' => function () {
							echo Blocksy_Manager::instance()->footer_builder->render();
						}
					],

					[
						'id' => 'footer_placements_item:button',
						'fallback_refresh' => false,
						'container_inclusive' => true,
						'selector' => '#main-container > footer.ct-footer',
						'loader_selector' => '[data-id="button"]',
						'settings' => ['footer_placements'],
						'render_callback' => function () {
							echo Blocksy_Manager::instance()->footer_builder->render();
						}
					],

					[
						'id' => 'footer_placements_item:logo',
						'fallback_refresh' => false,
						'container_inclusive' => true,
						'selector' => '.ct-footer [data-id="logo"]',
						'settings' => ['footer_placements'],
						'render_callback' => function () {
							$b = new Blocksy_Footer_Builder_Render();
							echo $b->render_single_item('logo');
						}
					],

					[
						'id' => 'footer_placements_item:socials',
						'fallback_refresh' => false,
						'container_inclusive' => true,
						'selector' => '.ct-footer [data-id="socials"]',
						'settings' => ['footer_placements'],
						'render_callback' => function () {
							$b = new Blocksy_Footer_Builder_Render();
							echo $b->render_single_item('socials');
						}
					],
				])
			],
		]
	],
];

