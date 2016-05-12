<?php

/**
 * Gets option value from the single theme option, stored as an array in the database
 * if all options stored in one row.
 * Stores the serialized array with theme options into the global variable on the first function run on the page.
 *
 * If options are stored as separate rows in database, it simply uses get_option() function.
 *
 * @param string $option_name Theme option name.
 * @param string $default_value Default value that should be set if the theme option isn't set.
 * @param string $used_for_object "Object" name that should be translated into corresponding "object" if WPML is activated.
 * @return mixed Theme option value or false if not found.
 */
if ( ! function_exists( 'et_get_option' ) ) :
function et_get_option( $option_name, $default_value = '', $used_for_object = '', $force_default_value = false ) {
	global $et_divi_builder_plugin_options;

	$shortname = 'divi_builder_plugin';

	$et_theme_options_name = 'et_' . $shortname;

	if ( ! isset( $et_divi_builder_plugin_options ) ) {
		$et_divi_builder_plugin_options = get_option( $et_theme_options_name );
	}
	$option_value = isset ( $et_divi_builder_plugin_options[$option_name] ) ? $et_divi_builder_plugin_options[$option_name] : false;

	// option value might be equal to false, so check if the option is not set in the database
	if ( ! isset( $et_divi_builder_plugin_options[ $option_name ] ) && ( '' != $default_value || $force_default_value ) ) {
		$option_value = $default_value;
	}

	if ( '' != $used_for_object && in_array( $used_for_object, array( 'page', 'category' ) ) && is_array( $option_value ) ) {
		$option_value = et_generate_wpml_ids( $option_value, $used_for_object );
	}

	return $option_value;
}
endif;

if ( ! function_exists( 'et_update_option' ) ) :
function et_update_option( $option_name, $new_value ){
	global $et_divi_builder_plugin_options;

	$shortname = 'divi_builder_plugin';

	$et_theme_options_name = 'et_' . $shortname;

	if ( ! isset( $et_divi_builder_plugin_options ) ) $et_divi_builder_plugin_options = get_option( $et_theme_options_name );
	$et_divi_builder_plugin_options[$option_name] = $new_value;

	$option_name = $et_theme_options_name;
	$new_value = $et_divi_builder_plugin_options;

	update_option( $option_name, $new_value );
}
endif;

if ( ! function_exists( 'et_delete_option' ) ) :
function et_delete_option( $option_name ){
	global $et_divi_builder_plugin_options;

	$shortname = 'divi_builder_plugin';

	$et_theme_options_name = 'et_' . $shortname;

	if ( ! isset( $et_divi_builder_plugin_options ) ) $et_divi_builder_plugin_options = get_option( $et_theme_options_name );

	unset( $et_divi_builder_plugin_options[$option_name] );
	update_option( $et_theme_options_name, $et_divi_builder_plugin_options );
}
endif;

/* this function gets thumbnail from Post Thumbnail or Custom field or First post image */
if ( ! function_exists( 'get_thumbnail' ) ) :
function get_thumbnail($width=100, $height=100, $class='', $alttext='', $titletext='', $fullpath=false, $custom_field='', $post='') {
	if ( $post == '' ) global $post;
	global $shortname;

	$thumb_array['thumb'] = '';
	$thumb_array['use_timthumb'] = true;
	if ($fullpath) $thumb_array['fullpath'] = ''; //full image url for lightbox

	$new_method = true;

	if ( has_post_thumbnail( $post->ID ) ) {
		$thumb_array['use_timthumb'] = false;

		$et_fullpath = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		$thumb_array['fullpath'] = $et_fullpath[0];
		$thumb_array['thumb'] = $thumb_array['fullpath'];
	}

	if ($thumb_array['thumb'] == '') {
		if ($custom_field == '') $thumb_array['thumb'] = esc_attr( get_post_meta($post->ID, 'Thumbnail', $single = true) );
		else {
			$thumb_array['thumb'] = esc_attr( get_post_meta($post->ID, $custom_field, $single = true) );
			if ($thumb_array['thumb'] == '') $thumb_array['thumb'] = esc_attr( get_post_meta($post->ID, 'Thumbnail', $single = true) );
		}

		#if custom field used for small pre-cropped image, open Thumbnail custom field image in lightbox
		if ($fullpath) {
			$thumb_array['fullpath'] = $thumb_array['thumb'];
			if ($custom_field == '') $thumb_array['fullpath'] = apply_filters('et_fullpath', et_path_reltoabs(esc_attr($thumb_array['thumb'])));
			elseif ( $custom_field <> '' && get_post_meta($post->ID, 'Thumbnail', $single = true) ) $thumb_array['fullpath'] = apply_filters( 'et_fullpath', et_path_reltoabs(esc_attr(get_post_meta($post->ID, 'Thumbnail', $single = true))) );
		}
	}

	return $thumb_array;
}
endif;

/* this function prints thumbnail from Post Thumbnail or Custom field or First post image */
if ( ! function_exists( 'print_thumbnail' ) ) :
function print_thumbnail($thumbnail = '', $use_timthumb = true, $alttext = '', $width = 100, $height = 100, $class = '', $echoout = true, $forstyle = false, $resize = true, $post='', $et_post_id = '' ) {
	if ( is_array( $thumbnail ) ){
		extract( $thumbnail );
	}

	if ( $post == '' ) global $post, $et_theme_image_sizes;

	$output = '';

	$et_post_id = '' != $et_post_id ? (int) $et_post_id : $post->ID;

	if ( has_post_thumbnail( $et_post_id ) ) {
		$thumb_array['use_timthumb'] = false;

		$image_size_name = $width . 'x' . $height;
		$et_size = isset( $et_theme_image_sizes ) && array_key_exists( $image_size_name, $et_theme_image_sizes ) ? $et_theme_image_sizes[$image_size_name] : array( $width, $height );

		$et_attachment_image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id( $et_post_id ), $et_size );
		$thumbnail = $et_attachment_image_attributes[0];
	}

	if ( false === $forstyle ) {
		$output = '<img src="' . esc_url( $thumbnail ) . '"';

		if ($class <> '') $output .= " class='" . esc_attr( $class ) . "' ";

		$dimensions = apply_filters( 'et_print_thumbnail_dimensions', " width='" . esc_attr( $width ) . "' height='" .esc_attr( $height ) . "'" );

		$output .= " alt='" . esc_attr( strip_tags( $alttext ) ) . "'{$dimensions} />";

		if ( ! $resize ) $output = $thumbnail;
	} else {
		$output = $thumbnail;
	}

	if ($echoout) echo $output;
	else return $output;
}
endif;

if ( ! function_exists( 'et_path_reltoabs' ) ) :
function et_path_reltoabs( $imageurl ){
	if ( strpos(strtolower($imageurl), 'http://') !== false || strpos(strtolower($imageurl), 'https://') !== false ) return $imageurl;

	if ( strpos( strtolower($imageurl), $_SERVER['HTTP_HOST'] ) !== false )
		return $imageurl;
	else {
		$imageurl = esc_url( apply_filters( 'et_path_relative_image', site_url() . '/' ) . $imageurl );
	}

	return $imageurl;
}
endif;

/*this function allows for the auto-creation of post excerpts*/
if ( ! function_exists( 'truncate_post' ) ) :
function truncate_post( $amount, $echo = true, $post = '' ) {
	if ( '' == $post ) {
		global $post;
	}

	$post_excerpt = '';
	$post_excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );

	// get the post content
	$truncate = $post->post_content;

	// remove caption shortcode from the post content
	$truncate = preg_replace('@\[caption[^\]]*?\].*?\[\/caption]@si', '', $truncate);

	// apply content filters
	$truncate = apply_filters( 'the_content', $truncate );

	// decide if we need to append dots at the end of the string
	if ( strlen( $truncate ) <= $amount ) {
		$echo_out = '';
	} else {
		$echo_out = '...';
		// $amount = $amount - 3;
	}

	// trim text to a certain number of characters, also remove spaces from the end of a string ( space counts as a character )
	$truncate = rtrim( et_wp_trim_words( $truncate, $amount, '' ) );

	// remove the last word to make sure we display all words correctly
	if ( '' != $echo_out ) {
		$new_words_array = (array) explode( ' ', $truncate );
		array_pop( $new_words_array );

		$truncate = implode( ' ', $new_words_array );

		// append dots to the end of the string
		$truncate .= $echo_out;
	}

	if ( $echo ) {
		echo $truncate;
	} else {
		return $truncate;
	}
}
endif;

if ( ! function_exists( 'et_wp_trim_words' ) ) :
function et_wp_trim_words( $text, $num_words = 55, $more = null ) {
	if ( null === $more )
		$more = esc_html__( '&hellip;' );
	$original_text = $text;
	$text = wp_strip_all_tags( $text );

	$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
	preg_match_all( '/./u', $text, $words_array );
	$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
	$sep = '';

	if ( count( $words_array ) > $num_words ) {
		array_pop( $words_array );
		$text = implode( $sep, $words_array );
		$text = $text . $more;
	} else {
		$text = implode( $sep, $words_array );
	}

	return apply_filters( 'wp_trim_words', $text, $num_words, $more, $original_text );
}
endif;

if ( ! function_exists( 'et_get_safe_localization' ) ) :
	function et_get_safe_localization( $string ) {
		return wp_kses( $string, et_get_allowed_localization_html_elements() );
	}
endif;

if ( ! function_exists( 'et_get_allowed_localization_html_elements' ) ) :
	function et_get_allowed_localization_html_elements() {
		$whitelisted_attributes = array(
			'id'    => array(),
			'class' => array(),
			'style' => array(),
		);

		$elements = array(
			'a'      => array(
				'href'  => array(),
				'title' => array(),
				'target' => array(),
			),
			'b'      => array(),
			'em'     => array(),
			'p'      => array(),
			'span'   => array(),
			'div'    => array(),
			'strong' => array(),
		);

		foreach ( $elements as $tag => $attributes ) {
			$elements[ $tag ] = array_merge( $attributes, $whitelisted_attributes );
		}

		return $elements;
	}
endif;

if ( ! function_exists( 'et_sanitize_alpha_color' ) ) :
	/**
	 * Sanitize RGBA color
	 * @param string
	 * @return string|bool
	 */
	function et_sanitize_alpha_color( $color ) {
		// Trim unneeded whitespace
		$color = str_replace( ' ', '', $color );

		// If this is hex color, validate and return it
		if ( 1 === preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}

		// If this is rgb, validate and return it
		elseif ( 'rgb(' === substr( $color, 0, 4 ) ) {
			sscanf( $color, 'rgb(%d,%d,%d)', $red, $green, $blue );

			if ( ( $red >= 0 && $red <= 255 ) &&
				 ( $green >= 0 && $green <= 255 ) &&
				 ( $blue >= 0 && $blue <= 255 )
				) {
				return "rgb({$red},{$green},{$blue})";
			}
		}

		// If this is rgba, validate and return it
		elseif ( 'rgba(' === substr( $color, 0, 5 ) ) {
			sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

			if ( ( $red >= 0 && $red <= 255 ) &&
				 ( $green >= 0 && $green <= 255 ) &&
				 ( $blue >= 0 && $blue <= 255 ) &&
				   $alpha >= 0 && $alpha <= 1
				) {
				return "rgba({$red},{$green},{$blue},{$alpha})";
			}
		}

		return false;
	}
endif;