<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Build css classes from terms of the post.
 *
 * @param $value
 * @param $data
 *
 * @since 4.4
 *
 * @return string
 */
function vc_gitem_template_attribute_filter_terms_css_classes( $value, $data ) {
	$output = '';
	/**
	 * @var null|Wp_Post $post ;
	 */
	extract( array_merge( array(
		'post' => null,
	), $data ) );
	if ( isset( $post->filter_terms ) && is_array( $post->filter_terms ) ) {
		foreach ( $post->filter_terms as $t ) {
			$output .= ' vc_grid-term-' . $t; // @todo fix #106154391786878 $t is array
		}
	}

	return $output;
}

/**
 * Get image for post
 *
 * @param $data
 * @return mixed|string|void
 */
function vc_gitem_template_attribute_post_image( $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );
	if ( 'attachment' === $post->post_type ) {
		return wp_get_attachment_image( $post->ID, 'large' );
	}
	$html = get_the_post_thumbnail( $post->ID );

	return apply_filters( 'vc_gitem_template_attribute_post_image_html', $html );
}

function vc_gitem_template_attribute_featured_image( $value, $data ) {
	/**
	 * @var Wp_Post $post
	 * @var string $data
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );

	return vc_include_template( 'params/vc_grid_item/attributes/featured_image.php', array(
		'post' => $post,
		'data' => $data,
	) );
}

/**
 * Create new btn
 *
 * @param $value
 * @param $data
 *
 * @since 4.5
 *
 * @return mixed
 */
function vc_gitem_template_attribute_vc_btn( $value, $data ) {
	/**
	 * @var Wp_Post $post
	 * @var string $data
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );

	return vc_include_template( 'params/vc_grid_item/attributes/vc_btn.php', array(
		'post' => $post,
		'data' => $data,
	) );

}

/**
 * Get post image url
 *
 * @param $value
 * @param $data
 *
 * @return string
 */
function vc_gitem_template_attribute_post_image_url( $value, $data ) {
	$output = '';
	/**
	 * @var null|Wp_Post $post ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );
	$extraImageMeta = explode( ':', $data );
	$size = 'large'; // default size
	if ( isset( $extraImageMeta[1] ) ) {
		$size = $extraImageMeta[1];
	}
	if ( 'attachment' === $post->post_type ) {
		$src = vc_get_image_by_size( $post->ID, $size );
	} else {
		$attachment_id = get_post_thumbnail_id( $post->ID );
		$src = vc_get_image_by_size( $attachment_id, $size );
	}
	if ( empty( $src ) && ! empty( $data ) ) {
		$output = esc_attr( rawurldecode( $data ) );
	} elseif ( ! empty( $src ) ) {
		$output = is_array( $src ) ? $src[0] : $src;
	} else {
		$output = vc_asset_url( 'vc/vc_gitem_image.png' );
	}

	return apply_filters( 'vc_gitem_template_attribute_post_image_url_value', $output );
}

/**
 * Get post image url with href for a dom element
 *
 * @param $value
 * @param $data
 *
 * @return string
 */
function vc_gitem_template_attribute_post_image_url_href( $value, $data ) {
	$link = vc_gitem_template_attribute_post_image_url( $value, $data );

	return strlen( $link ) ? ' href="' . esc_attr( $link ) . '"' : '';
}

/**
 * Add image url as href with css classes for PrettyPhoto js plugin.
 *
 * @param $value
 * @param $data
 *
 * @return string
 */
function vc_gitem_template_attribute_post_image_url_attr_prettyphoto( $value, $data ) {
	$data_default = $data;
	/**
	 * @var Wp_Post $post ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );
	$href = vc_gitem_template_attribute_post_image_url_href( $value, array( 'post' => $post, 'data' => '' ) );
	$rel = ' data-rel="' . esc_attr( 'prettyPhoto[rel-' . md5( vc_request_param( 'shortcode_id' ) ) . ']' ) . '"';
	return $href . $rel . ' class="' . esc_attr( $data . ( strlen( $href ) ? ' prettyphoto' : '' ) )
	       . '" title="' . esc_attr(
		   apply_filters( 'vc_gitem_template_attribute_post_title', $post->post_title, $data_default ) ) . '"';
}

/**
 * Get post image alt
 *
 * @param $value
 * @param $data
 * @return string
 */
function vc_gitem_template_attribute_post_image_alt( $value, $data ) {
	if ( empty( $data['post']->ID ) ) {
		return '';
	}

	if ( 'attachment' === $data['post']->post_type ) {
		$attachment_id = $data['post']->ID;
	} else {
		$attachment_id = get_post_thumbnail_id( $data['post']->ID );
	}

	if ( ! $attachment_id ) {
		return '';
	}

	$alt = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

	return apply_filters( 'vc_gitem_template_attribute_post_image_url_value', $alt );
}

/**
 * Get post image url
 *
 * @param $value
 * @param $data
 * @return string
 */
function vc_gitem_template_attribute_post_image_background_image_css( $value, $data ) {
	$output = '';
	/**
	 * @var null|Wp_Post $post ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );
	$size = 'large'; // default size
	if ( ! empty( $data ) ) {
		$size = $data;
	}
	if ( 'attachment' === $post->post_type ) {
		$src = vc_get_image_by_size( $post->ID, $size );
	} else {
		$attachment_id = get_post_thumbnail_id( $post->ID );
		$src = vc_get_image_by_size( $attachment_id, $size );
	}
	if ( ! empty( $src ) ) {
		$output = 'background-image: url(\'' . ( is_array( $src ) ? $src[0] : $src ) . '\') !important;';
	} else {
		$output = 'background-image: url(\'' . vc_asset_url( 'vc/vc_gitem_image.png' ) . '\') !important;';
	}

	return apply_filters( 'vc_gitem_template_attribute_post_image_background_image_css_value', $output );
}

/**
 * Get post link
 *
 * @param $value
 * @param $data
 * @return bool|string
 */
function vc_gitem_template_attribute_post_link_url( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 */
	extract( array_merge( array(
		'post' => null,
	), $data ) );

	return get_permalink( $post->ID );
}

/**
 * Get post date
 *
 * @param $value
 * @param $data
 * @return bool|int|string
 */
function vc_gitem_template_attribute_post_date( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 */
	extract( array_merge( array(
		'post' => null,
	), $data ) );

	return get_the_date( '', $post->ID );
}

/**
 * Get post date time
 *
 * @param $value
 * @param $data
 * @return bool|int|string
 */
function vc_gitem_template_attribute_post_datetime( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 */
	extract( array_merge( array(
		'post' => null,
	), $data ) );

	return get_the_time( 'F j, Y g:i', $post->ID );
}

/**
 * Get custom fields.
 *
 * @param $value
 * @param $data
 * @return mixed|string
 */
function vc_gitem_template_attribute_post_meta_value( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 * @var string $data ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );

	return strlen( $data ) > 0 ? get_post_meta( $post->ID, $data, true ) : $value;
}

/**
 * Get post data. Used as wrapper for others post data attributes.
 *
 * @param $value
 * @param $data
 * @return mixed|string
 */
function vc_gitem_template_attribute_post_data( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 * @var string $data ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );

	return strlen( $data ) > 0 ? apply_filters( 'vc_gitem_template_attribute_' . $data, (
		isset( $post->$data ) ? $post->$data : ''
	), array( 'post' => $post, 'data' => '' ) ) : $value;
}

/**
 * Get post excerpt. Used as wrapper for others post data attributes.
 *
 * @param $value
 * @param $data
 * @return mixed|string
 */
function vc_gitem_template_attribute_post_excerpt( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 * @var string $data ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );

	return apply_filters( 'the_excerpt', apply_filters( 'get_the_excerpt', $value ) );
}

/**
 * Get post excerpt. Used as wrapper for others post data attributes.
 *
 * @param $value
 * @param $data
 * @return mixed|string
 */
function vc_gitem_template_attribute_post_title( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 * @var string $data ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );

	return the_title( '', '', false );
}

function vc_gitem_template_attribute_post_author( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 * @var string $data ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );

	return get_the_author();
}

function vc_gitem_template_attribute_post_author_href( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 * @var string $data ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );

	return get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) );
}
function vc_gitem_template_attribute_post_categories( $value, $data ) {
	/**
	 * @var null|Wp_Post $post ;
	 * @var string $data ;
	 */
	extract( array_merge( array(
		'post' => null,
		'data' => '',
	), $data ) );
	$atts_extended = array();
	parse_str( $data, $atts_extended );

	return vc_include_template( 'params/vc_grid_item/attributes/post_categories.php', array(
		'post' => $post,
		'atts' => $atts_extended['atts'],
	) );
}

/**
 * Adding filters to parse grid template.
 */
add_filter( 'vc_gitem_template_attribute_filter_terms_css_classes', 'vc_gitem_template_attribute_filter_terms_css_classes', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_image', 'vc_gitem_template_attribute_post_image', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_image_url', 'vc_gitem_template_attribute_post_image_url', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_image_url_href', 'vc_gitem_template_attribute_post_image_url_href', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_image_url_attr_prettyphoto', 'vc_gitem_template_attribute_post_image_url_attr_prettyphoto', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_image_alt', 'vc_gitem_template_attribute_post_image_alt', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_link_url', 'vc_gitem_template_attribute_post_link_url', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_date', 'vc_gitem_template_attribute_post_date', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_datetime', 'vc_gitem_template_attribute_post_datetime', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_meta_value', 'vc_gitem_template_attribute_post_meta_value', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_data', 'vc_gitem_template_attribute_post_data', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_image_background_image_css', 'vc_gitem_template_attribute_post_image_background_image_css', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_excerpt', 'vc_gitem_template_attribute_post_excerpt', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_title', 'vc_gitem_template_attribute_post_title', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_author', 'vc_gitem_template_attribute_post_author', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_author_href', 'vc_gitem_template_attribute_post_author_href', 10, 2 );
add_filter( 'vc_gitem_template_attribute_post_categories', 'vc_gitem_template_attribute_post_categories', 10, 2 );
add_filter( 'vc_gitem_template_attribute_featured_image', 'vc_gitem_template_attribute_featured_image', 10, 2 );
add_filter( 'vc_gitem_template_attribute_vc_btn', 'vc_gitem_template_attribute_vc_btn', 10, 2 );
