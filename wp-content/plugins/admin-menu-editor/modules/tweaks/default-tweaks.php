<?php
return array(
	'hide-screen-meta-links' => array(
		'label' => 'Hide screen meta links',
		'selector' => '#screen-meta-links'
	),
	'hide-screen-options' => array(
		'label' => 'Hide the "Screen Options" button',
		'selector' => '#screen-options-link-wrap',
		'parent' => 'hide-screen-meta-links',
	),
	'hide-help-panel' => array(
		'label' => 'Hide the "Help" button',
		'selector' => '#contextual-help-link-wrap',
		'parent' => 'hide-screen-meta-links',
	),
	'hide-all-admin-notices' => array(
		'label' => 'Hide ALL admin notices',
		'selector' => '.wrap .notice, .wrap .updated',
	),
);