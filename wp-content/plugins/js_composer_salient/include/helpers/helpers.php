<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder helpers functions
 *
 * @package WPBakeryPageBuilder
 *
 */

// Check if this file is loaded in js_composer
if ( ! defined( 'WPB_VC_VERSION' ) ) {
	die( '-1' );
}

/**
 * @param array $params
 *
 * @since 4.2
 * vc_filter: vc_wpb_getimagesize - to override output of this function
 * @return array|bool
 */
function wpb_getImageBySize( $params = array() ) {
	$params = array_merge( array(
		'post_id' => null,
		'attach_id' => null,
		'thumb_size' => 'thumbnail',
		'class' => '',
	), $params );

	if ( ! $params['thumb_size'] ) {
		$params['thumb_size'] = 'thumbnail';
	}

	if ( ! $params['attach_id'] && ! $params['post_id'] ) {
		return false;
	}

	$post_id = $params['post_id'];

	$attach_id = $post_id ? get_post_thumbnail_id( $post_id ) : $params['attach_id'];
	$attach_id = apply_filters( 'vc_object_id', $attach_id );
	$thumb_size = $params['thumb_size'];
	$thumb_class = ( isset( $params['class'] ) && '' !== $params['class'] ) ? $params['class'] . ' ' : '';

	global $_wp_additional_image_sizes;
	$thumbnail = '';

	if ( is_string( $thumb_size ) && ( ( ! empty( $_wp_additional_image_sizes[ $thumb_size ] ) && is_array( $_wp_additional_image_sizes[ $thumb_size ] ) ) || in_array( $thumb_size, array(
				'thumbnail',
				'thumb',
				'medium',
				'large',
				'full',
			) ) )
	) {
		$attributes = array( 'class' => $thumb_class . 'attachment-' . $thumb_size );
		$thumbnail = wp_get_attachment_image( $attach_id, $thumb_size, false, $attributes );
	} elseif ( $attach_id ) {
		if ( is_string( $thumb_size ) ) {
			preg_match_all( '/\d+/', $thumb_size, $thumb_matches );
			if ( isset( $thumb_matches[0] ) ) {
				$thumb_size = array();
				$count = count( $thumb_matches[0] );
				if ( $count > 1 ) {
					$thumb_size[] = $thumb_matches[0][0]; // width
					$thumb_size[] = $thumb_matches[0][1]; // height
				} elseif ( 1 === $count ) {
					$thumb_size[] = $thumb_matches[0][0]; // width
					$thumb_size[] = $thumb_matches[0][0]; // height
				} else {
					$thumb_size = false;
				}
			}
		}
		if ( is_array( $thumb_size ) ) {
			// Resize image to custom size
			$p_img = wpb_resize( $attach_id, null, $thumb_size[0], $thumb_size[1], true );
			$alt = trim( strip_tags( get_post_meta( $attach_id, '_wp_attachment_image_alt', true ) ) );
			$attachment = get_post( $attach_id );
			if ( ! empty( $attachment ) ) {
				$title = trim( strip_tags( $attachment->post_title ) );

				if ( empty( $alt ) ) {
					$alt = trim( strip_tags( $attachment->post_excerpt ) ); // If not, Use the Caption
				}
				if ( empty( $alt ) ) {
					$alt = $title;
				} // Finally, use the title
				if ( $p_img ) {

					$attributes = vc_stringify_attributes( array(
						'class' => $thumb_class,
						'src' => $p_img['url'],
						'width' => $p_img['width'],
						'height' => $p_img['height'],
						'alt' => $alt,
						'title' => $title,
					) );

					$thumbnail = '<img ' . $attributes . ' />';
				}
			}
		}
	}

	$p_img_large = wp_get_attachment_image_src( $attach_id, 'large' );

	return apply_filters( 'vc_wpb_getimagesize', array(
		'thumbnail' => $thumbnail,
		'p_img_large' => $p_img_large,
	), $attach_id, $params );
}

function vc_get_image_by_size( $id, $size ) {
	global $_wp_additional_image_sizes;

	if ( is_string( $size ) && ( ( ! empty( $_wp_additional_image_sizes[ $size ] ) && is_array( $_wp_additional_image_sizes[ $size ] ) ) || in_array( $size, array(
				'thumbnail',
				'thumb',
				'medium',
				'large',
				'full',
			) ) )
	) {
		return wp_get_attachment_image_src( $id, $size );
	} else {
		if ( is_string( $size ) ) {
			preg_match_all( '/\d+/', $size, $thumb_matches );
			if ( isset( $thumb_matches[0] ) ) {
				$size = array();
				$count = count( $thumb_matches[0] );
				if ( $count > 1 ) {
					$size[] = $thumb_matches[0][0]; // width
					$size[] = $thumb_matches[0][1]; // height
				} elseif ( 1 === $count ) {
					$size[] = $thumb_matches[0][0]; // width
					$size[] = $thumb_matches[0][0]; // height
				} else {
					$size = false;
				}
			}
		}
		if ( is_array( $size ) ) {
			// Resize image to custom size
			$p_img = wpb_resize( $id, null, $size[0], $size[1], true );

			return $p_img['url'];
		}
	}

	return '';
}

/* Convert vc_col-sm-3 to 1/4
---------------------------------------------------------- */
/**
 * @param $width
 *
 * @since 4.2
 * @return string
 */
function wpb_translateColumnWidthToFractional( $width ) {
	switch ( $width ) {
		case 'vc_col-sm-2' :
			$w = '1/6';
			break;
		case 'vc_col-sm-3' :
			$w = '1/4';
			break;
		case 'vc_col-sm-4' :
			$w = '1/3';
			break;
		case 'vc_col-sm-6' :
			$w = '1/2';
			break;
		case 'vc_col-sm-8' :
			$w = '2/3';
			break;
		case 'vc_col-sm-9' :
			$w = '3/4';
			break;
		case 'vc_col-sm-12' :
			$w = '1/1';
			break;

		default :
			$w = is_string( $width ) ? $width : '1/1';
	}

	return $w;
}

/**
 * @param $width
 *
 * @since 4.2
 * @return bool|string
 */
function wpb_translateColumnWidthToSpan( $width ) {
	$output = $width;
	preg_match( '/(\d+)\/(\d+)/', $width, $matches );

	if ( ! empty( $matches ) ) {
		$part_x = (int) $matches[1];
		$part_y = (int) $matches[2];
		if ( $part_x > 0 && $part_y > 0 ) {
			$value = ceil( $part_x / $part_y * 12 );
			if ( $value > 0 && $value <= 12 ) {
				$output = 'vc_col-sm-' . $value;
			}
		}
	}
	if ( preg_match( '/\d+\/5$/', $width ) ) {
		$output = 'vc_col-sm-' . $width;
	}

	return apply_filters( 'vc_translate_column_width_class', $output, $width );
}

/**
 * @param $content
 * @param bool $autop
 *
 * @since 4.2
 * @return string
 */
function wpb_js_remove_wpautop( $content, $autop = false ) {

	if ( $autop ) { // Possible to use !preg_match('('.WPBMap::getTagsRegexp().')', $content)
		$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
	}

	return do_shortcode( shortcode_unautop( $content ) );
}

if ( ! function_exists( 'shortcode_exists' ) ) {
	/**
	 * Check if a shortcode is registered in WordPress.
	 *
	 * Examples: shortcode_exists( 'caption' ) - will return true.
	 * shortcode_exists( 'blah' ) - will return false.
	 *
	 * @param bool $shortcode
	 *
	 * @since 4.2
	 * @return bool
	 */
	function shortcode_exists( $shortcode = false ) {
		global $shortcode_tags;

		if ( ! $shortcode ) {
			return false;
		}

		if ( array_key_exists( $shortcode, $shortcode_tags ) ) {
			return true;
		}

		return false;
	}
}

/* Helper function which returns list of site attached images,
   and if image is attached to the current post it adds class
   'added'
---------------------------------------------------------- */
if ( ! function_exists( 'vc_siteAttachedImages' ) ) {
	/**
	 * @param array $att_ids
	 *
	 * @since 4.11
	 * @return string
	 */
	function vc_siteAttachedImages( $att_ids = array() ) {
		$output = '';

		$limit = (int) apply_filters( 'vc_site_attached_images_query_limit', - 1 );
		$media_images = get_posts( 'post_type=attachment&orderby=ID&numberposts=' . $limit );
		foreach ( $media_images as $image_post ) {
			$thumb_src = wp_get_attachment_image_src( $image_post->ID, 'thumbnail' );
			$thumb_src = $thumb_src[0];

			$class = ( in_array( $image_post->ID, $att_ids ) ) ? ' class="added"' : '';

			$output .= '<li' . $class . '>
						<img rel="' . esc_attr( $image_post->ID ) . '" src="' . esc_url( $thumb_src ) . '" />
						<span class="img-added">' . __( 'Added', 'js_composer' ) . '</span>
					</li>';
		}

		if ( '' !== $output ) {
			$output = '<ul class="gallery_widget_img_select">' . $output . '</ul>';
		}

		return $output;
	}
}

/**
 * @param array $images IDs or srcs of images
 *
 * @since 4.2
 * @return string
 */
function fieldAttachedImages( $images = array() ) {
	$output = '';

	foreach ( $images as $image ) {
		if ( is_numeric( $image ) ) {
			$thumb_src = wp_get_attachment_image_src( $image, 'thumbnail' );
			$thumb_src = isset( $thumb_src[0] ) ? $thumb_src[0] : '';
		} else {
			$thumb_src = $image;
		}

		if ( $thumb_src ) {
			$output .= '
			<li class="added">
				<img rel="' . esc_attr( $image ) . '" src="' . esc_url( $thumb_src ) . '" />
				<a href="#" class="vc_icon-remove"><i class="vc-composer-icon vc-c-icon-close"></i></a>
			</li>';
		}
	}

	return $output;
}



/* nectar addition */ 
function fjarrett_get_attachment_id_by_url( $url ) {
 
	// Split the $url into two parts with the wp-content directory as the separator.
	$parse_url = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );
	 
	// Get the host of the current site and the host of the $url, ignoring www.
	$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
	$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );
	 
	// Return nothing if there aren't any $url parts or if the current host and $url host do not match.
	if ( ! isset( $parse_url[1] ) || empty( $parse_url[1] ) || ( $this_host != $file_host ) )
	return;
	
	global $wpdb;
	 
	$prefix = $wpdb->prefix;
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . $prefix . "posts WHERE guid RLIKE %s;", $parse_url[1] ) );
	 
	// Returns null if no attachment is found.
	return $attachment[0];
}
/* nectar addition end */ 


/**
 * @param $param_value
 *
 * @since 4.2
 * @return array
 */
function wpb_removeNotExistingImgIDs( $param_value ) {
	$tmp = explode( ',', $param_value );
	$return_ar = array();
	foreach ( $tmp as $id ) {
		if ( wp_get_attachment_image( $id ) ) {
			$return_ar[] = $id;
		}
	}
	$tmp = implode( ',', $return_ar );

	return $tmp;
}

/*
* Resize images dynamically using wp built in functions
* Victor Teixeira
*
* php 5.2+
*
* Exemplo de uso:
*
* <?php
* $thumb = get_post_thumbnail_id();
* $image = vt_resize( $thumb, '', 140, 110, true );
* ?>
* <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
*
*/
if ( ! function_exists( 'wpb_resize' ) ) {
	/**
	 * @param int $attach_id
	 * @param string $img_url
	 * @param int $width
	 * @param int $height
	 * @param bool $crop
	 *
	 * @since 4.2
	 * @return array
	 */
	function wpb_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {
		// this is an attachment, so we have the ID
		$image_src = array();
		if ( $attach_id ) {
			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$actual_file_path = get_attached_file( $attach_id );
			// this is not an attachment, let's use the image url
		} elseif ( $img_url ) {
			$file_path = parse_url( $img_url );
			$actual_file_path = rtrim( ABSPATH, '/' ) . $file_path['path'];
			$orig_size = getimagesize( $actual_file_path );
			$image_src[0] = $img_url;
			$image_src[1] = $orig_size[0];
			$image_src[2] = $orig_size[1];
		}
		if ( ! empty( $actual_file_path ) ) {
			$file_info = pathinfo( $actual_file_path );
			$extension = '.' . $file_info['extension'];

			// the image path without the extension
			$no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];

			$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;

			// checking if the file size is larger than the target size
			// if it is smaller or the same size, stop right here and return
			if ( $image_src[1] > $width || $image_src[2] > $height ) {

				// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
				if ( file_exists( $cropped_img_path ) ) {
					$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
					$vt_image = array(
						'url' => $cropped_img_url,
						'width' => $width,
						'height' => $height,
					);

					return $vt_image;
				}

				if ( false == $crop ) {
					// calculate the size proportionaly
					$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
					$resized_img_path = $no_ext_path . '-' . $proportional_size[0] . 'x' . $proportional_size[1] . $extension;

					// checking if the file already exists
					if ( file_exists( $resized_img_path ) ) {
						$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

						$vt_image = array(
							'url' => $resized_img_url,
							'width' => $proportional_size[0],
							'height' => $proportional_size[1],
						);

						return $vt_image;
					}
				}

				// no cache files - let's finally resize it
				$img_editor = wp_get_image_editor( $actual_file_path );

				if ( is_wp_error( $img_editor ) || is_wp_error( $img_editor->resize( $width, $height, $crop ) ) ) {
					return array(
						'url' => '',
						'width' => '',
						'height' => '',
					);
				}

				$new_img_path = $img_editor->generate_filename();

				if ( is_wp_error( $img_editor->save( $new_img_path ) ) ) {
					return array(
						'url' => '',
						'width' => '',
						'height' => '',
					);
				}
				if ( ! is_string( $new_img_path ) ) {
					return array(
						'url' => '',
						'width' => '',
						'height' => '',
					);
				}

				$new_img_size = getimagesize( $new_img_path );
				$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

				// resized output
				$vt_image = array(
					'url' => $new_img,
					'width' => $new_img_size[0],
					'height' => $new_img_size[1],
				);

				return $vt_image;
			}

			// default output - without resizing
			$vt_image = array(
				'url' => $image_src[0],
				'width' => $image_src[1],
				'height' => $image_src[2],
			);

			return $vt_image;
		}

		return false;
	}
}

if ( ! function_exists( 'wpb_debug' ) ) {
	/**
	 * Returns bool if wpb_debug is provided in url - set WPBakery Page Builder debug mode.
	 * Used for example in shortcodes (end block comment for example)
	 * @since 4.2
	 * @deprecated 5.5 - use xdebug for debugging.
	 * @return bool
	 */
	function wpb_debug() {
		return false;
	}
}

/**
 * Method adds css class to body tag.
 *
 * Hooked class method by body_class WP filter. Method adds custom css class to body tag of the page to help
 * identify and build design specially for VC shortcodes.
 * Used in wp-content/plugins/js_composer/include/classes/core/class-vc-base.php\Vc_Base\bodyClass
 *
 * @param $classes
 *
 * @since 4.2
 * @return array
 */
function js_composer_body_class( $classes ) {
	$classes[] = 'wpb-js-composer js-comp-ver-' . WPB_VC_VERSION;
	$disable_responsive = vc_settings()->get( 'not_responsive_css' );
	if ( '1' !== $disable_responsive ) {
		$classes[] = 'vc_responsive';
	} else {
		$classes[] = 'vc_non_responsive';
	}

	return $classes;
}

/**
 * @param $m
 *
 * @since 4.2
 * @return string
 */
 /* nectar addition */  
 function vc_convert_shortcode( $m ) {
  list($output, $m_one, $tag, $attr_string, $m_four, $content) = $m;
 	$result = $width = $el_position = '';
 	$shortcode_attr = shortcode_parse_atts( $attr_string );
 	extract(shortcode_atts(array(
 			'width' => '1/1',
 			'el_class' => '',
 			'el_position' => ''
 	), $shortcode_attr));
 	if($tag == 'vc_row' || $tag == 'full_width_section') return $output;

 	// Start
 	if(preg_match('/first/', $el_position) || empty($shortcode_attr['width']) || $shortcode_attr['width']==='1/1')  {
 	 if(!empty($output)) $result = '[vc_row]';
  }
  
  /*if($tag == 'one_half' || $tag == 'one_third' || $tag == 'one_fourth' 
 	|| $tag == 'one_sixth' || $tag == 'two_thirds' || $tag == 'three_fourths' || $tag == 'fixth_sixths' || $tag == 'one_whole') {
  
 	
 	if($rowStart == 0) {
 			$rowStart = 1;
 				$result = '[vc_row]';
 	}
 	
  }*/
 	
 	if($tag!='vc_column' && $tag != 'one_half' && $tag != 'one_half_last' && $tag != 'one_third' && $tag != 'one_third_last' && $tag != 'one_fourth' && $tag != 'one_fourth_last' && $tag != 'one_sixth' && $tag != 'one_sixth_last' && $tag != 'two_thirds' && $tag != 'two_thirds_last' && $tag != 'three_fourths' && $tag != 'three_fourtsh_last' && $tag != 'fixth_sixths' && $tag != 'five_sixths_last' && $tag != 'one_whole') $result .= "\n".'[vc_column width="'.$width.'"]';
  
  
  
 	// Tag
 	$pattern = get_shortcode_regex();
 	if($tag == 'vc_column' || $tag == 'one_half' || $tag == 'one_half_last' || $tag == 'one_third' || $tag == 'one_third_last' || $tag == 'one_fourth' || $tag == 'one_fourth_last' 
 	|| $tag == 'one_sixth' || $tag == 'one_sixth_last' || $tag == 'two_thirds' || $tag == 'two_thirds_last' || $tag == 'three_fourths' || $tag == 'three_fourtsh_last' || $tag == 'fixth_sixths'
 	|| $tag == 'five_sixths_last' || $tag == 'one_whole') {
 			$result .= "[{$m_one}{$tag} {$attr_string}]".preg_replace_callback( "/{$pattern}/s", 'vc_convert_inner_shortcode', $content)."[/{$tag}{$m_four}]";
 	} elseif( $tag == 'vc_tabs' || $tag == 'vc_accordion' || $tag == 'vc_tour' || $tag == 'toggle' || $tag == 'tabbed_section' ||  $tag == 'testimonial_slider' ||  $tag == 'clients' ||  $tag == 'pricing_table' ) {
 	 
 			$result .= "[{$m_one}{$tag} {$attr_string}]".preg_replace_callback( "/{$pattern}/s", 'vc_convert_tab_inner_shortcode', $content)."[/{$tag}{$m_four}]";
 	} else {
 			$result .= preg_replace('/(\"\d\/\d\")/', '"1/1"', $output);
 	}

 	// $content = preg_replace_callback( "/{$pattern}/s", 'vc_convert_inner_shortcode', $content );

 	// End
 	if($tag!='vc_column' && $tag != 'one_half' && $tag != 'one_half_last' && $tag != 'one_third' && $tag != 'one_third_last' && $tag != 'one_fourth' && $tag != 'one_fourth_last' && $tag != 'one_sixth' && $tag != 'one_sixth_last' && $tag != 'two_thirds' && $tag != 'two_thirds_last' && $tag != 'three_fourths' && $tag != 'three_fourtsh_last' && $tag != 'fixth_sixths' && $tag != 'five_sixths_last' && $tag != 'one_whole') $result .= '[/vc_column]';
 	
 	if(preg_match('/last/', $el_position) || empty($shortcode_attr['width']) || $shortcode_attr['width']==='1/1') {
 	 /*if($tag != 'one_half' && $tag != 'one_half_last' && $tag != 'one_third' && $tag != 'one_third_last' && $tag != 'one_fourth' && $tag != 'one_fourth_last' 
 		&& $tag != 'one_sixth' && $tag != 'one_sixth_last' && $tag != 'two_thirds' && $tag != 'two_thirds_last' && $tag != 'three_fourths' && $tag != 'three_fourtsh_last' && $tag != 'fixth_sixths'
 		&& $tag != 'five_sixths_last' && $tag != 'one_whole') {*/
 		 if(!empty($output)) $result .= '[/vc_row]'."\n";
 	/*}*/
 	}
  
  
  /*if($tag == 'one_half_last' || $tag == 'one_third_last'  || $tag == 'one_fourth_last' 
 	|| $tag == 'one_sixth_last' || $tag == 'two_thirds_last' || $tag == 'three_fourtsh_last' || $tag !== 'five_sixths_last') {
 			$result .= '[/vc_row]'."\n";
 	 $rowStart = 0;
  }*/
  
 	return $result;
 }

 /* nectar addition end */ 

/**
 * @param $m
 *
 * @since 4.2
 * @return string
 */
function vc_convert_tab_inner_shortcode( $m ) {
	list( $output, $m_one, $tag, $attr_string, $m_four, $content ) = $m;
	$result = $width = $el_position = '';
	extract( shortcode_atts( array(
		'width' => '1/1',
		'el_class' => '',
		'el_position' => '',
	), shortcode_parse_atts( $attr_string ) ) );
	$pattern = get_shortcode_regex();
	$result .= "[{$m_one}{$tag} {$attr_string}]" . preg_replace_callback( "/{$pattern}/s", 'vc_convert_inner_shortcode', $content ) . "[/{$tag}{$m_four}]";

	return $result;
}

/**
 * @param $m
 *
 * @since 4.2
 * @return string
 */
function vc_convert_inner_shortcode( $m ) {
	list( $output, $m_one, $tag, $attr_string, $m_four, $content ) = $m;
	$result = $width = $el_position = '';
	extract( shortcode_atts( array(
		'width' => '1/1',
		'el_class' => '',
		'el_position' => '',
	), shortcode_parse_atts( $attr_string ) ) );
	if ( '1/1' !== $width ) {
		if ( preg_match( '/first/', $el_position ) ) {
			$result .= '[vc_row_inner]';
		}
		$result .= "\n" . '[vc_column_inner width="' . esc_attr( $width ) . '" el_position="' . esc_attr( $el_position ) . '"]';
		$attr = '';
		foreach ( shortcode_parse_atts( $attr_string ) as $key => $value ) {
			if ( 'width' === $key ) {
				$value = '1/1';
			} elseif ( 'el_position' === $key ) {
				$value = 'first last';
			}
			$attr .= ' ' . $key . '="' . $value . '"';
		}
		$result .= "[{$m_one}{$tag} {$attr}]" . $content . "[/{$tag}{$m_four}]";
		$result .= '[/vc_column_inner]';
		if ( preg_match( '/last/', $el_position ) ) {
			$result .= '[/vc_row_inner]' . "\n";
		}
	} else {
		$result = $output;
	}

	return $result;
}

global $vc_row_layouts;
$vc_row_layouts = array(
	/*
 * How to count mask?
 * mask = column_count . sum of all numbers. Example layout 12_12 mask = (column count=2)(1+2+1+2=6)= 26
*/
	array(
		'cells' => '11',
		'mask' => '12',
		'title' => '1/1',
		'icon_class' => '1-1',
	),
	array(
		'cells' => '12_12',
		'mask' => '26',
		'title' => '1/2 + 1/2',
		'icon_class' => '1-2_1-2',
	),
	array(
		'cells' => '23_13',
		'mask' => '29',
		'title' => '2/3 + 1/3',
		'icon_class' => '2-3_1-3',
	),
	array(
		'cells' => '13_13_13',
		'mask' => '312',
		'title' => '1/3 + 1/3 + 1/3',
		'icon_class' => '1-3_1-3_1-3',
	),
	array(
		'cells' => '14_14_14_14',
		'mask' => '420',
		'title' => '1/4 + 1/4 + 1/4 + 1/4',
		'icon_class' => '1-4_1-4_1-4_1-4',
	),
	array(
		'cells' => '14_34',
		'mask' => '212',
		'title' => '1/4 + 3/4',
		'icon_class' => '1-4_3-4',
	),
	array(
		'cells' => '14_12_14',
		'mask' => '313',
		'title' => '1/4 + 1/2 + 1/4',
		'icon_class' => '1-4_1-2_1-4',
	),
	array(
		'cells' => '56_16',
		'mask' => '218',
		'title' => '5/6 + 1/6',
		'icon_class' => '5-6_1-6',
	),
	array(
		'cells' => '16_16_16_16_16_16',
		'mask' => '642',
		'title' => '1/6 + 1/6 + 1/6 + 1/6 + 1/6 + 1/6',
		'icon_class' => '1-6_1-6_1-6_1-6_1-6_1-6',
	),
	array(
		'cells' => '16_23_16',
		'mask' => '319',
		'title' => '1/6 + 4/6 + 1/6',
		'icon_class' => '1-6_2-3_1-6',
	),
	array(
		'cells' => '16_16_16_12',
		'mask' => '424',
		'title' => '1/6 + 1/6 + 1/6 + 1/2',
		'icon_class' => '1-6_1-6_1-6_1-2',
	),
	array(
		'cells' => '15_15_15_15_15',
		'mask' => '530',
		'title' => '1/5 + 1/5 + 1/5 + 1/5 + 1/5',
		'icon_class' => 'l_15_15_15_15_15',
	),
);

/**
 * @param $width
 *
 * @since 4.2
 * @return string
 */
function wpb_vc_get_column_width_indent( $width ) {
	$identy = '11';
	if ( 'vc_col-sm-6' === $width ) {
		$identy = '12';
	} elseif ( 'vc_col-sm-3' === $width ) {
		$identy = '14';
	} elseif ( 'vc_col-sm-4' === $width ) {
		$identy = '13';
	} elseif ( 'vc_col-sm-8' === $width ) {
		$identy = '23';
	} elseif ( 'vc_col-sm-9' === $width ) {
		$identy = '34';
	} elseif ( 'vc_col-sm-2' === $width ) {
		$identy = '16'; // TODO: check why there is no "vc_col-sm-1, -5, -6, -7, -11, -12.
	} elseif ( 'vc_col-sm-10' === $width ) {
		$identy = '56';
	}

	return $identy;
}

/* Make any HEX color lighter or darker
---------------------------------------------------------- */
/**
 * @param $colour
 * @param $per
 *
 * @since 4.2
 * @return string
 */
function vc_colorCreator( $colour, $per = 10 ) {
	require_once 'class-vc-color-helper.php';
	$color = $colour;
	if ( stripos( $colour, 'rgba(' ) !== false ) {
		$rgb = str_replace( array(
			'rgba',
			'rgb',
			'(',
			')',
		), '', $colour );
		$rgb = explode( ',', $rgb );
		$rgb_array = array(
			'R' => $rgb[0],
			'G' => $rgb[1],
			'B' => $rgb[2],
		);
		$alpha = $rgb[3];
		try {
			$color = Vc_Color_Helper::rgbToHex( $rgb_array );
			$color_obj = new Vc_Color_Helper( $color );
			if ( $per >= 0 ) {
				$color = $color_obj->lighten( $per );
			} else {
				$color = $color_obj->darken( abs( $per ) );
			}
			$rgba = $color_obj->hexToRgb( $color );
			$rgba[] = $alpha;
			$css_rgba_color = 'rgba(' . implode( ', ', $rgba ) . ')';

			return $css_rgba_color;
		} catch ( Exception $e ) {
			// In case of error return same as given
			return $colour;
		}
	} else if ( stripos( $colour, 'rgb(' ) !== false ) {
		$rgb = str_replace( array(
			'rgba',
			'rgb',
			'(',
			')',
		), '', $colour );
		$rgb = explode( ',', $rgb );
		$rgb_array = array(
			'R' => $rgb[0],
			'G' => $rgb[1],
			'B' => $rgb[2],
		);
		try {
			$color = Vc_Color_Helper::rgbToHex( $rgb_array );
		} catch ( Exception $e ) {
			// In case of error return same as given
			return $colour;
		}
	}

	try {
		$color_obj = new Vc_Color_Helper( $color );
		if ( $per >= 0 ) {
			$color = $color_obj->lighten( $per );
		} else {
			$color = $color_obj->darken( abs( $per ) );
		}

		return '#' . $color;
	} catch ( Exception $e ) {
		return $colour;
	}
}


/* nectar addition */ 
function hex2rgba($color, $opacity = false) {

	$default = 'rgb(0,0,0)';

	//Return default if no color provided
	if(empty($color))
          return $default; 

	//Sanitize $color if "#" is provided 
        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity){
        	if(abs($opacity) > 1)
        		$opacity = 1.0;
        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
        	$output = 'rgb('.implode(",",$rgb).')';
        }

        //Return rgb(a) color string
        return $output;
}

/* nectar addition end */ 


/* HEX to RGB converter
---------------------------------------------------------- */
/**
 * @param $color
 *
 * @since 4.2
 * @return array|bool
 */
function vc_hex2rgb( $color ) {
	$color = str_replace( '#', '', $color );

	if ( strlen( $color ) === 6 ) {
		list( $r, $g, $b ) = array(
			$color[0] . $color[1],
			$color[2] . $color[3],
			$color[4] . $color[5],
		);
	} elseif ( strlen( $color ) === 3 ) {
		list( $r, $g, $b ) = array(
			$color[0] . $color[0],
			$color[1] . $color[1],
			$color[2] . $color[2],
		);
	} else {
		return false;
	}

	$r = hexdec( $r );
	$g = hexdec( $g );
	$b = hexdec( $b );

	return array(
		$r,
		$g,
		$b,
	);
}

/**
 * Parse string like "title:Hello world|weekday:Monday" to array('title' => 'Hello World', 'weekday' => 'Monday')
 *
 * @param $value
 * @param array $default
 *
 * @since 4.2
 * @return array
 */
function vc_parse_multi_attribute( $value, $default = array() ) {
	$result = $default;
	$params_pairs = explode( '|', $value );
	if ( ! empty( $params_pairs ) ) {
		foreach ( $params_pairs as $pair ) {
			$param = preg_split( '/\:/', $pair );
			if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
				$result[ $param[0] ] = rawurldecode( $param[1] );
			}
		}
	}

	return $result;
}

/**
 * @param $v
 *
 * @since 4.2
 * @return string
 */
function vc_param_options_parse_values( $v ) {
	return rawurldecode( $v );
}

/**
 * @param $name
 * @param $settings
 *
 * @since 4.2
 * @return bool
 */
function vc_param_options_get_settings( $name, $settings ) {
	if ( is_array( $settings ) ) {
		foreach ( $settings as $params ) {
			if ( isset( $params['name'] ) && $params['name'] === $name && isset( $params['type'] ) ) {
				return $params;
			}
		}
	}

	return false;
}

/**
 * @param $atts
 *
 * @since 4.2
 * @return string
 */
function vc_convert_atts_to_string( $atts ) {
	$output = '';
	foreach ( $atts as $key => $value ) {
		$output .= ' ' . $key . '="' . $value . '"';
	}

	return $output;
}

/**
 * @param $string
 * @param $tag
 * @param $param
 *
 * @since 4.2
 * @return array
 */
function vc_parse_options_string( $string, $tag, $param ) {
	$options = $option_settings_list = array();
	$settings = WPBMap::getParam( $tag, $param );

	foreach ( preg_split( '/\|/', $string ) as $value ) {
		if ( preg_match( '/\:/', $value ) ) {
			$split = preg_split( '/\:/', $value );
			$option_name = $split[0];
			$option_settings = $option_settings_list[ $option_name ] = vc_param_options_get_settings( $option_name, $settings['options'] );
			if ( isset( $option_settings['type'] ) && 'checkbox' === $option_settings['type'] ) {
				$option_value = array_map( 'vc_param_options_parse_values', preg_split( '/\,/', $split[1] ) );
			} else {
				$option_value = rawurldecode( $split[1] );
			}
			$options[ $option_name ] = $option_value;
		}
	}
	if ( isset( $settings['options'] ) ) {
		foreach ( $settings['options'] as $setting_option ) {
			if ( 'separator' !== $setting_option['type'] && isset( $setting_option['value'] ) && empty( $options[ $setting_option['name'] ] ) ) {
				$options[ $setting_option['name'] ] = 'checkbox' === $setting_option['type'] ? preg_split( '/\,/', $setting_option['value'] ) : $setting_option['value'];
			}
			if ( isset( $setting_option['name'] ) && isset( $options[ $setting_option['name'] ] ) && isset( $setting_option['value_type'] ) ) {
				if ( 'integer' === $setting_option['value_type'] ) {
					$options[ $setting_option['name'] ] = (int) $options[ $setting_option['name'] ];
				} elseif ( 'float' === $setting_option['value_type'] ) {
					$options[ $setting_option['name'] ] = (float) $options[ $setting_option['name'] ];
				} elseif ( 'boolean' === $setting_option['value_type'] ) {
					$options[ $setting_option['name'] ] = (boolean) $options[ $setting_option['name'] ];
				}
			}
		}
	}

	return $options;
}

/**
 * Convert string to a valid css class name.
 *
 * @since 4.3
 *
 * @param string $class
 *
 * @return string
 */
function vc_build_safe_css_class( $class ) {
	return preg_replace( '/\W+/', '', strtolower( str_replace( ' ', '_', strip_tags( $class ) ) ) );
}

/**
 * Include template from templates dir.
 *
 * @since 4.3
 *
 * @param $template
 * @param array $variables - passed variables to the template.
 *
 * @param bool $once
 *
 * @return mixed
 */
function vc_include_template( $template, $variables = array(), $once = false ) {
	is_array( $variables ) && extract( $variables );
	if ( $once ) {
		return require_once vc_template( $template );
	} else {
		return require vc_template( $template );
	}
}

/**
 * Output template from templates dir.
 *
 * @since 4.4
 *
 * @param $template
 * @param array $variables - passed variables to the template.
 *
 * @param bool $once
 *
 * @return string
 */
function vc_get_template( $template, $variables = array(), $once = false ) {
	ob_start();
	vc_include_template( $template, $variables, $once );

	return ob_get_clean();
}

/**
 * if php version < 5.3 this function is required.
 */
if ( ! function_exists( 'lcfirst' ) ) {
	/**
	 * @param $str
	 *
	 * @since 4.3, fix #1093
	 * @return mixed
	 */
	function lcfirst( $str ) {
		$str[0] = function_exists( 'mb_strtolower' ) ? mb_strtolower( $str[0] ) : strtolower( $str[0] );

		return $str;
	}
}
/**
 * VC Convert a value to studly caps case.
 *
 * @since 4.3
 *
 * @param  string $value
 *
 * @return string
 */
function vc_studly( $value ) {
	$value = ucwords( str_replace( array(
		'-',
		'_',
	), ' ', $value ) );

	return str_replace( ' ', '', $value );
}

/**
 * VC Convert a value to camel case.
 *
 * @since 4.3
 *
 * @param  string $value
 *
 * @return string
 */
function vc_camel_case( $value ) {
	return lcfirst( vc_studly( $value ) );
}

/**
 * Enqueue icon element font
 * @todo move to separate folder
 * @since 4.4
 *
 * @param $font
 */
function vc_icon_element_fonts_enqueue( $font ) {
	switch ( $font ) {
		case 'fontawesome':
			wp_enqueue_style( 'font-awesome' );
			break;
		case 'openiconic':
			wp_enqueue_style( 'vc_openiconic' );
			break;
		case 'typicons':
			wp_enqueue_style( 'vc_typicons' );
			break;
		case 'entypo':
			wp_enqueue_style( 'vc_entypo' );
			break;
		case 'linecons':
			wp_enqueue_style( 'vc_linecons' );
			break;
		case 'monosocial':
			wp_enqueue_style( 'vc_monosocialiconsfont' );
			break;
			/*nectar addition */
			/*
			case 'material':
				wp_enqueue_style( 'vc_material' );
				break;
				*/
				/*nectar addition end */
		default:
			do_action( 'vc_enqueue_font_icon_element', $font ); // hook to custom do enqueue style
	}
}

/**
 * Function merges defaults attributes in attributes by keeping it values
 *
 * Example
 *      array defaults     |   array attributes     |    result array
 *      'color'=>'black',         -                   'color'=>'black',
 *      'target'=>'_self',      'target'=>'_blank',   'target'=>'_blank',
 *             -                'link'=>'google.com'  'link'=>'google.com'
 *
 * @since 4.4
 *
 * @param array $defaults
 * @param array $attributes
 *
 * @return array - merged attributes
 *
 * @see vc_map_get_attributes
 */
function vc_shortcode_attribute_parse( $defaults = array(), $attributes = array() ) {
	$atts = $attributes + shortcode_atts( $defaults, $attributes );

	return $atts;
}

function vc_get_shortcode_regex( $tagregexp = '' ) {
	if ( 0 === strlen( $tagregexp ) ) {
		return get_shortcode_regex();
	}

	return '\\['                              // Opening bracket
		. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
		. "($tagregexp)"                     // 2: Shortcode name
		. '(?![\\w-])'                       // Not followed by word character or hyphen
		. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
		. '[^\\]\\/]*'                   // Not a closing bracket or forward slash
		. '(?:' . '\\/(?!\\])'               // A forward slash not followed by a closing bracket
		. '[^\\]\\/]*'               // Not a closing bracket or forward slash
		. ')*?' . ')' . '(?:' . '(\\/)'                        // 4: Self closing tag ...
		. '\\]'                          // ... and closing bracket
		. '|' . '\\]'                          // Closing bracket
		. '(?:' . '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
		. '[^\\[]*+'             // Not an opening bracket
		. '(?:' . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
		. '[^\\[]*+'         // Not an opening bracket
		. ')*+' . ')' . '\\[\\/\\2\\]'             // Closing shortcode tag
		. ')?' . ')' . '(\\]?)';
}

/**
 * Used to send warning message
 *
 * @since 4.5
 *
 * @param $message
 *
 * @return string
 */
function vc_message_warning( $message ) {
	return '<div class="wpb_element_wrapper"><div class="vc_message_box vc_message_box-standard vc_message_box-rounded vc_color-warning">
	<div class="vc_message_box-icon"><i class="fa fa-exclamation-triangle"></i>
	</div><p class="messagebox_text">' . $message . '</p>
</div></div>';
}

/**
 * Extract video ID from youtube url
 *
 * @param string $url Youtube url
 *
 * @return string
 */
function vc_extract_youtube_id( $url ) {
	parse_str( parse_url( $url, PHP_URL_QUERY ), $vars );

	if ( ! isset( $vars['v'] ) ) {
		return '';
	}

	return $vars['v'];
}

function vc_taxonomies_types() {
	global $vc_taxonomies_types;
	if ( is_null( $vc_taxonomies_types ) ) {
		$vc_taxonomies_types = get_taxonomies( array( 'public' => true ), 'objects' );
	}

	return $vc_taxonomies_types;
}

/**
 * Since
 *
 * @since 4.5.3
 *
 * @param $term
 *
 * @return array
 */
function vc_get_term_object( $term ) {
	$vc_taxonomies_types = vc_taxonomies_types();

	return array(
		'label' => $term->name,
		'value' => $term->term_id,
		'group_id' => $term->taxonomy,
		'group' => isset( $vc_taxonomies_types[ $term->taxonomy ], $vc_taxonomies_types[ $term->taxonomy ]->labels, $vc_taxonomies_types[ $term->taxonomy ]->labels->name ) ? $vc_taxonomies_types[ $term->taxonomy ]->labels->name : __( 'Taxonomies', 'js_composer' ),
	);
}

/**
 * Extract width/height from string
 *
 * @param string $dimensions WxH
 *
 * @since 4.7
 *
 * @return mixed array(width, height) or false
 */
function vcExtractDimensions( $dimensions ) {
	$dimensions = str_replace( ' ', '', $dimensions );
	$matches = null;

	if ( preg_match( '/(\d+)x(\d+)/', $dimensions, $matches ) ) {
		return array(
			$matches[1],
			$matches[2],
		);
	}

	return false;
}

/**
 * Check if element has specific class
 *
 * E.g. f('foo', 'foo bar baz') -> true
 *
 * @param string $class Class to check for
 * @param string $classes Classes separated by space(s)
 *
 * @return bool
 */
function vc_has_class( $class, $classes ) {
	return in_array( $class, explode( ' ', strtolower( $classes ) ) );
}

/**
 * Remove specific class from classes string
 *
 * E.g. f('foo', 'foo bar baz') -> 'bar baz'
 *
 * @param string $class Class to remove
 * @param string $classes Classes separated by space(s)
 *
 * @return string
 */
function vc_remove_class( $class, $classes ) {
	$list_classes = explode( ' ', strtolower( $classes ) );

	$key = array_search( $class, $list_classes, true );

	if ( false === $key ) {
		return $classes;
	}

	unset( $list_classes[ $key ] );

	return implode( ' ', $list_classes );
}

/**
 * Convert array of named params to string version
 * All values will be escaped
 *
 * E.g. f(array('name' => 'foo', 'id' => 'bar')) -> 'name="foo" id="bar"'
 *
 * @param $attributes
 *
 * @return string
 */
function vc_stringify_attributes( $attributes ) {
	$atts = array();
	foreach ( $attributes as $name => $value ) {
		$atts[] = $name . '="' . esc_attr( $value ) . '"';
	}

	return implode( ' ', $atts );
}

function vc_is_responsive_disabled() {
	$disable_responsive = vc_settings()->get( 'not_responsive_css' );

	return '1' === $disable_responsive;
}

/**
 * Do shortcode single render point
 *
 * @param $atts
 * @param null $content
 * @param null $tag
 *
 * @return string
 */
function vc_do_shortcode( $atts, $content = null, $tag = null ) {
	return Vc_Shortcodes_Manager::getInstance()->getElementClass( $tag )->output( $atts, $content );
}

/**
 * Return random string
 *
 * @param int $length
 *
 * @return string
 */
function vc_random_string( $length = 10 ) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$len = strlen( $characters );
	$str = '';
	for ( $i = 0; $i < $length; $i ++ ) {
		$str .= $characters[ rand( 0, $len - 1 ) ];
	}

	return $str;
}

function vc_slugify( $str ) {
	$str = strtolower( $str );
	$str = html_entity_decode( $str );
	$str = preg_replace( '/[^\w ]+/', '', $str );
	$str = preg_replace( '/ +/', '-', $str );

	return $str;
}
