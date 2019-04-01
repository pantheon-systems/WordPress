<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.5.2
 *
 * @param $term
 *
 * @return array|bool
 */
function vc_autocomplete_taxonomies_field_render( $term ) {
	$vc_taxonomies_types = vc_taxonomies_types();
	$terms = get_terms( array_keys( $vc_taxonomies_types ), array(
		'include' => array( $term['value'] ),
		'hide_empty' => false,
	) );
	$data = false;
	if ( is_array( $terms ) && 1 === count( $terms ) ) {
		$term = $terms[0];
		$data = vc_get_term_object( $term );
	}

	return $data;
}

/**
 * @since 4.5.2
 *
 * @param $search_string
 *
 * @return array|bool
 */
function vc_autocomplete_taxonomies_field_search( $search_string ) {
	$data = array();
	$vc_filter_by = vc_post_param( 'vc_filter_by', '' );
	$vc_taxonomies_types = strlen( $vc_filter_by ) > 0 ? array( $vc_filter_by ) : array_keys( vc_taxonomies_types() );
	$vc_taxonomies = get_terms( $vc_taxonomies_types, array(
		'hide_empty' => false,
		'search' => $search_string,
	) );
	if ( is_array( $vc_taxonomies ) && ! empty( $vc_taxonomies ) ) {
		foreach ( $vc_taxonomies as $t ) {
			if ( is_object( $t ) ) {
				$data[] = vc_get_term_object( $t );
			}
		}
	}

	return $data;
}

/**
 * @param $search
 * @param $wp_query
 *
 * @return string
 */
function vc_search_by_title_only( $search, &$wp_query ) {
	global $wpdb;
	if ( empty( $search ) ) {
		return $search;
	} // skip processing - no search term in query
	$q = $wp_query->query_vars;
	if ( isset( $q['vc_search_by_title_only'] ) && true == $q['vc_search_by_title_only'] ) {
		$n = ! empty( $q['exact'] ) ? '' : '%';
		$search = $searchand = '';
		foreach ( (array) $q['search_terms'] as $term ) {
			$term = $wpdb->esc_like( $term );
			$like = $n . $term . $n;
			$search .= $wpdb->prepare( "{$searchand}($wpdb->posts.post_title LIKE %s)", $like );
			$searchand = ' AND ';
		}
		if ( ! empty( $search ) ) {
			$search = " AND ({$search}) ";
			if ( ! is_user_logged_in() ) {
				$search .= " AND ($wpdb->posts.post_password = '') ";
			}
		}
	}

	return $search;
}

/**
 * @param $search_string
 *
 * @return array
 */
function vc_include_field_search( $search_string ) {
	$query = $search_string;
	$data = array();
	$args = array(
		's' => $query,
		'post_type' => 'any',
	);
	$args['vc_search_by_title_only'] = true;
	$args['numberposts'] = - 1;
	if ( 0 === strlen( $args['s'] ) ) {
		unset( $args['s'] );
	}
	add_filter( 'posts_search', 'vc_search_by_title_only', 500, 2 );
	$posts = get_posts( $args );
	if ( is_array( $posts ) && ! empty( $posts ) ) {
		foreach ( $posts as $post ) {
			$data[] = array(
				'value' => $post->ID,
				'label' => $post->post_title,
				'group' => $post->post_type,
			);
		}
	}

	return $data;
}

/**
 * @param $value
 *
 * @return array|bool
 */
function vc_include_field_render( $value ) {
	$post = get_post( $value['value'] );

	return is_null( $post ) ? false : array(
		'label' => $post->post_title,
		'value' => $post->ID,
		'group' => $post->post_type,
	);
}

/**
 * @param $data_arr
 *
 * @return array
 */
function vc_exclude_field_search( $data_arr ) {
	$query = isset( $data_arr['query'] ) ? $data_arr['query'] : null;
	$term = isset( $data_arr['term'] ) ? $data_arr['term'] : '';
	$data = array();
	$args = ! empty( $query ) ? array(
		's' => $term,
		'post_type' => $query,
	) : array(
		's' => $term,
		'post_type' => 'any',
	);
	$args['vc_search_by_title_only'] = true;
	$args['numberposts'] = - 1;
	if ( 0 === strlen( $args['s'] ) ) {
		unset( $args['s'] );
	}
	add_filter( 'posts_search', 'vc_search_by_title_only', 500, 2 );
	$posts = get_posts( $args );
	if ( is_array( $posts ) && ! empty( $posts ) ) {
		foreach ( $posts as $post ) {
			$data[] = array(
				'value' => $post->ID,
				'label' => $post->post_title,
				'group' => $post->post_type,
			);
		}
	}

	return $data;
}

/**
 * @param $value
 *
 * @return array|bool
 */
function vc_exclude_field_render( $value ) {
	$post = get_post( $value['value'] );

	return is_null( $post ) ? false : array(
		'label' => $post->post_title,
		'value' => $post->ID,
		'group' => $post->post_type,
	);
}
