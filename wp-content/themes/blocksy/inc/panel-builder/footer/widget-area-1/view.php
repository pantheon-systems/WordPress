<?php

if (! isset($class)) {
	$class = 'widget-area-1';
}

if (! isset($sidebar)) {
	$sidebar = 'ct-footer-sidebar-1';
}

dynamic_sidebar($sidebar);

