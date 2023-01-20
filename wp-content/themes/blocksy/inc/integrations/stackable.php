<?php

add_filter('stackable_responsive_breakpoints', function ($breakpoints) {
	return [
		'tablet' => '1000',
		'mobile' => '690'
	];
});

