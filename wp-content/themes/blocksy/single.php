<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Blocksy
 */

get_header();

if (
	! function_exists('elementor_theme_do_location')
	||
	! elementor_theme_do_location('single')
) {
	get_template_part('template-parts/single');
}

get_footer();

