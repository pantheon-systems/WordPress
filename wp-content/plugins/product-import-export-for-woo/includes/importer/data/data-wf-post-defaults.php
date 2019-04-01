<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// New post defaults
return array(
	'post_type' 	=> $this->post_type,
	'menu_order' 	=> '',
	'postmeta'		=> array(),
	'post_status'	=> 'publish',
	'post_title'	=> '',
	'post_name'		=> '',
	'post_date'		=> '',
	'post_date_gmt'	=> '',
	'post_content'	=> '',
	'post_excerpt'	=> '',
	'post_parent'	=> '',
	'post_password'	=> '',
	'post_author'   => '',
	'comment_status'=> 'open'
);