<?php

echo blocksy_render_view(
	get_template_directory() . '/inc/panel-builder/footer/widget-area-1/view.php',
	[
		'atts' => $atts,
		'attr' => $attr,
		'class' => 'widget-area-3',
		'sidebar' => 'ct-footer-sidebar-3'
	]
);


