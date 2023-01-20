<?php

// https://wordpress.org/plugins/default-featured-image/

add_action('wp', function () {
	if (! class_exists('DFI')) {
		return;
	}

	remove_filter('post_thumbnail_html', [DFI::instance(), 'show_dfi'], 20, 5);
});
