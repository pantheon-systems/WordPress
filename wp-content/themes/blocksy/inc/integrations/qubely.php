<?php

add_filter('qubely_container_width', function () {
	return array(
		'sm' => 480,
		'md' => 690,
		'lg' => 1000,
		'xl' => 1200
	);
});

add_action('after_switch_theme', function () {
	do_action('qubely_active_theme_preset');
});

