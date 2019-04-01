<?php
/**
 * Salient search related functions
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! function_exists( 'nectar_add_ajax_to_search' ) ) {
	function nectar_add_ajax_to_search() {

		global $nectar_theme_skin;
		global $nectar_options;

		$ajax_search  = ( ! empty( $nectar_options['header-disable-ajax-search'] ) && $nectar_options['header-disable-ajax-search'] == '1' ) ? 'no' : 'yes';
		$headerSearch = ( ! empty( $nectar_options['header-disable-search'] ) && $nectar_options['header-disable-search'] == '1' ) ? 'false' : 'true';

		if ( $ajax_search == 'yes' && $headerSearch != 'false' && $nectar_theme_skin != 'material' ) {
			get_template_part( 'nectar/assets/functions/ajax-search/wp-search-suggest' );
		}
	}
}
nectar_add_ajax_to_search();


if ( ! function_exists( 'nectar_change_wp_search_size' ) ) {
	function nectar_change_wp_search_size( $query ) {
		if ( $query->is_search ) {
			$query->query_vars['posts_per_page'] = 12;
		}

		return $query;
	}
}
if ( ! is_admin() ) {
	add_filter( 'pre_get_posts', 'nectar_change_wp_search_size' );
}

