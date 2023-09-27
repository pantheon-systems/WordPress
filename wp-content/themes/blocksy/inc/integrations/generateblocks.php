<?php

add_filter('generateblocks_dynamic_css_priority', function ($current) {
	return 60;
});
