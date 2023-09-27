<?php

if (! isset($location)) {
	$location = 'menu_mobile';
}

$render_args = [
	'attr' => $attr,
	'atts' => $atts,
	'location' => $location
];

if ($row_id === 'offcanvas') {
	echo blocksy_render_view(
		dirname(__FILE__) . '/views/offcanvas.php',
		$render_args
	);
}


if ($row_id !== 'offcanvas') {
	echo blocksy_render_view(
		dirname(__FILE__) . '/views/inline.php',
		$render_args
	);
}

