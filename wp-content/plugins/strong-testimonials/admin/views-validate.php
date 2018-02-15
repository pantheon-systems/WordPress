<?php

/**
 * Validate a View's name.
 *
 * @since 2.11.14
 *
 * @param $name
 * @param $view_id
 *
 * @return string
 */
function wpmtst_validate_view_name( $name, $view_id ){
	if ( '' == $name ) {
		$name = "Testimonial View $view_id";
	}
	else {
		$name = sanitize_text_field( stripslashes( $name ) );
	}
	return $name;
}


/**
 * Sanitize and validate a View.
 * TODO break down into separate validators
 *
 * @since 1.21.0
 * @since 2.5.7 Strip CSS from CSS Class Names field.
 * @since 2.10.0 Provide both more_post and more_page.
 * @since 2.11.0 More slideshow options: effect, slideshow_nav, stretch
 * @since 2.11.4 more_full_post for manual excerpts
 * @since 2.11.5 more_page_hook
 *
 * @param $input
 *
 * @return array
 */
function wpmtst_sanitize_view( $input ) {
	ksort( $input );

	$default_view = apply_filters( 'wpmtst_view_default', get_option( 'wpmtst_view_default' ) );

	$data         = array();
	$data['mode'] = sanitize_text_field( $input['mode'] );

	$data['form_ajax'] = isset( $input['form_ajax'] ) ? 1 : 0;

	$data = wpmtst_sanitize_view_post_id( $data, $input );

	$data = wpmtst_sanitize_view_template( $data, $input );

	// Category
	if ( 'form' == $data['mode'] ) {
		if ( isset( $input['category-form'] ) ) {
			$data['category'] = sanitize_text_field( implode( ',', $input['category-form'] ) );
		} else {
			$data['category'] = '';
		}
	} else {
		if ( 'allcats' == $input['category_all'] ) {
			$data['category'] = 'all';
		} elseif ( ! isset( $input['category'] ) ) {
			$data['category'] = 'all';
		} elseif ( 'somecats' == $input['category_all'] && ! isset( $input['category'] ) ) {
			$data['category'] = 'all';
		} else {
			$data['category'] = sanitize_text_field( implode( ',', $input['category'] ) );
		}
	}

	$data['order'] = sanitize_text_field( $input['order'] );

	// Limit
	if ( isset( $input['all'] ) && $input['all'] ) {
		$data['count'] = -1;
	}
	else {
	$data['count'] = (int) sanitize_text_field( $input['count'] );
	}

	// Pagination
	$data['pagination']          = isset( $input['pagination'] ) ? 1 : 0;
	$data['pagination_settings'] = wpmtst_sanitize_view_pagination( $input['pagination_settings'] );

	$data['title']              = isset( $input['title'] ) ? 1 : 0;
	$data['title_link']         = isset( $input['title_link'] ) ? 1 : 0;

	$data['content']            = sanitize_text_field( $input['content'] );
	$data['excerpt_length']     = (int) sanitize_text_field( $input['excerpt_length'] );
	$data['use_default_length'] = sanitize_text_field( $input['use_default_length'] );

	// Read more --> post
	$data['more_full_post']          = $input['more_full_post'];
	if ( 'truncated' == $data['content'] || 'excerpt' == $data['content'] ) {
		$data['more_post'] = 1;
	} else {
		$data['more_post'] = 0;
	}
	$data['more_post_ellipsis']      = isset( $input['more_post_ellipsis'] ) ? 1 : 0;
	$data['more_post_text']          = sanitize_text_field( $input['more_post_text'] );
	$data['use_default_more']        = $input['use_default_more'];

	// Read more --> page
	if ( isset( $input['more_page'] ) && $input['more_page'] ) {

		// Check the "ID or slug" field first
		if ( $input['more_page_id2'] ) {

			// is post ID?
			$id = sanitize_text_field( $input['more_page_id2'] );
			if ( is_numeric( $id ) ) {
				if ( ! get_posts( array( 'p' => $id, 'post_type' => array( 'page', 'post' ), 'post_status' => 'publish' ) ) ) {
					$id = null;
				}
			} else {
				// is post slug?
				$target = get_posts( array( 'name' => $id, 'post_type' => array( 'page', 'post' ), 'post_status' => 'publish' ) );
				if ( $target ) {
					$id = $target[0]->ID;
				}
			}

			if ( $id ) {
				$data['more_page_id'] = $id;
				unset( $data['more_page_id2'] );
			}

		} else {

			if ( $input['more_page_id'] ) {
				if ( is_numeric( $input['more_page_id'] ) ) {
					$data['more_page_id'] = (int) sanitize_text_field( $input['more_page_id'] );
				} else {
					$data['more_page_id'] = sanitize_text_field( $input['more_page_id'] );
				}
			}

		}

		// Only enable more_page if a page was selected by either method.
		if ( isset( $data['more_page_id'] ) && $data['more_page_id'] ) {
			$data['more_page'] = 1;
		}
	}
	if ( ! $input['more_page_text'] ) {
		$data['more_page_text'] = $default_view['more_page_text'];
	} else {
		$data['more_page_text'] = sanitize_text_field( $input['more_page_text'] );
	}
	$data['more_page_hook'] = sanitize_text_field( $input['more_page_hook'] );

	// Thumbnail
	$data['thumbnail']        = isset( $input['thumbnail'] ) ? 1 : 0;
	$data['thumbnail_size']   = sanitize_text_field( $input['thumbnail_size'] );
	$data['thumbnail_width']  = sanitize_text_field( $input['thumbnail_width'] );
	$data['thumbnail_height'] = sanitize_text_field( $input['thumbnail_height'] );
	$data['lightbox']         = isset( $input['lightbox'] ) ? 1 : 0;
	$data['lightbox_class']   = sanitize_text_field( $input['lightbox_class'] );
	$data['gravatar']         = sanitize_text_field( $input['gravatar'] );

	/**
	 * CSS Class Names
	 * This field is being confused with custom CSS rules like `.testimonial { border: none; }`
	 * so strip periods and declarations.
	 */
	$data['class'] = sanitize_text_field( trim( preg_replace( '/\{.*?\}|\./', '', $input['class'] ) ) );

	// Background
	$data['background'] = wpmtst_get_background_defaults();
	if ( ! isset( $input['background']['type'] ) ) {
		$data['background']['type'] = '';
	}
	else {
		$data['background']['type'] = sanitize_text_field( $input['background']['type'] );
	}
	$data['background']['color']     = sanitize_hex_color( $input['background']['color'] );
	$data['background']['gradient1'] = sanitize_hex_color( $input['background']['gradient1'] );
	$data['background']['gradient2'] = sanitize_hex_color( $input['background']['gradient2'] );
	$data['background']['preset']    = sanitize_text_field( $input['background']['preset'] );

	// Font color
	if ( ! isset( $input['font-color']['type'] ) ) {
		$data['font-color']['type'] = '';
	}
	else {
		$data['font-color']['type'] = sanitize_text_field( $input['font-color']['type'] );
	}
	$data['font-color']['color'] = sanitize_hex_color( $input['font-color']['color'] );

	// Layout input may have been disabled by selecting the widget template so no value is posted.
	if ( ! isset( $input['layout'] ) ) {
		$data['layout'] = '';
	}
	else {
		// pagination and Masonry are incompatible
		$data['layout'] = sanitize_text_field( $input['layout'] );
		if ( isset( $input['pagination'] ) && 'masonry' == $data['layout'] ) {
			$data['layout'] = '';
		}
	}

	$data['column_count'] = sanitize_text_field( $input['column_count'] );

	$data['slideshow_settings'] = wpmtst_sanitize_view_slideshow( $input['slideshow_settings'] );

	if ( isset( $input['client_section'] ) ) {
		$data['client_section'] = wpmtst_sanitize_view_client_section( $input['client_section'] );
	} else {
		$data['client_section'] = null;
	}

	// Multiple Forms add-on
	if ( isset( $input['form_id'] ) ) {
		$data['form_id'] = $input['form_id'];
	}
	else {
		// hidden
		$data['form_id'] = $input['_form_id'];
	}

	// Divi Builder
	$data['divi_builder'] = isset( $input['divi_builder'] ) ? 1 : 0;

	$data = apply_filters( 'wpmtst_sanitized_view', $data, $input );
	ksort( $data );

	return $data;
}

/**
 * Single testimonial
 *
 * @since 2.30.0 As separate function.
 *
 * @param $data array
 * @param $input array
 *
 * @return array
 */
function wpmtst_sanitize_view_post_id( $data, $input ) {
	// Clear single ID if "multiple" selected
	if ( 'multiple' == $input['select'] ) {
		$data['id'] = 0;  // must be zero not empty or false
		return $data;
	}

	// Clear single ID if mode:slideshow selected
	if ( 'slideshow' == $input['mode'] ) {
		$data['id'] = 0;  // must be zero not empty or false
		return $data;
	}

	// Check the "ID or slug" field first
	if ( ! $input['post_id'] ) {
		$data['id'] = (int) sanitize_text_field( $input['id'] );
		return $data;
	}

	// Is post ID?
	$id = (int) $input['post_id'];
	if ( $id ) {
		$args = array(
			'p'           => $id,
			'post_type'   => 'wpm-testimonial',
			'post_status' => 'publish',
		);
		if ( ! get_posts( $args ) ) {
			$id = null;
		}
	}
	else {
		// Is post slug?
		$args = array(
			'name'        => $input['post_id'],
			'post_type'   => 'wpm-testimonial',
			'post_status' => 'publish',
		);
		$target = get_posts( $args );
		if ( $target ) {
			$id = $target[0]->ID;
		}
	}

	$data['id']      = $id;
	$data['post_id'] = '';

	return $data;
}

/**
 * Sanitize slideshow settings.
 *
 * @since 2.28.0
 *
 * @param $in
 *
 * @return array
 */
function wpmtst_sanitize_view_pagination( $in ) {
	$out['type']               = sanitize_text_field( $in['type'] );
	$out['nav']                = str_replace( ' ', '', sanitize_text_field( $in['nav'] ) );
	$out['show_all']           = $in['show_all'] === 'on';
	$out['prev_next']          = wpmtst_sanitize_checkbox( $in, 'prev_next' );
	$out['prev_text']          = sanitize_text_field( $in['prev_text'] );
	$out['next_text']          = sanitize_text_field( $in['next_text'] );
	$out['before_page_number'] = sanitize_text_field( $in['before_page_number'] );
	$out['after_page_number']  = sanitize_text_field( $in['after_page_number'] );

	/**
	 * Attempt to repair bug from 2.28.2
	 */
	if ( isset( $in['end_size'] ) && intval( $in['end_size'] ) ) {
		$out['end_size'] = (int) sanitize_text_field( $in['end_size'] );
	}
	else {
		$out['end_size'] = 1;
	}

	if ( isset( $in['mid_size'] ) && intval( $in['mid_size'] ) ) {
		$out['mid_size'] = (int) sanitize_text_field( $in['mid_size'] );
	}
	else {
		$out['mid_size'] = 2;
	}

	if ( isset( $in['per_page'] ) && intval( $in['per_page'] ) ) {
		$out['per_page'] = (int) sanitize_text_field( $in['per_page'] );
	}
	else {
		$out['per_page'] = 5;
	}

	return $out;
}

/**
 * Sanitize slideshow settings.
 *
 * @param $in
 * @since 2.15.0
 *
 * @return array
 */
function wpmtst_sanitize_view_slideshow( $in ) {
	$out = array();

	$out['effect']             = sanitize_text_field( $in['effect'] );
	$out['pause']              = floatval( sanitize_text_field( $in['pause'] ) );
	$out['speed']              = floatval( sanitize_text_field( $in['speed'] ) );
	$out['auto_hover']         = isset( $in['auto_hover'] ) ? 1 : 0;
	$out['stop_auto_on_click'] = isset( $in['stop_auto_on_click'] ) ? 1 : 0;

	if ( 'dynamic' == $in['height'] ) {
		$out['adapt_height'] = 1;
	} else {
		$out['adapt_height'] = 0;
	}
	$out['adapt_height_speed'] = floatval( sanitize_text_field( $in['adapt_height_speed'] ) );
	$out['stretch']            = isset( $in['stretch'] ) ? 1 : 0;

	// If no navigation, must start automatically.
	if ( 'none' == $in['pager_type'] && 'none' == $in['controls_type'] ) {
		$out['auto_start'] = 1;
	} else {
		$out['auto_start'] = isset( $in['auto_start'] ) ? 1 : 0;
	}

	// Controls
	$out['controls_type'] = sanitize_text_field( $in['controls_type'] );
	$out['controls_style'] = sanitize_text_field( $in['controls_style'] );

	// Pagination
	$out['pager_type']  = sanitize_text_field( $in['pager_type'] );
	$out['pager_style'] = sanitize_text_field( $in['pager_style'] );

	// Position is shared by Controls and Pagination
	if ( $out['controls_type'] || $out['pager_type'] ) {
		$out['nav_position'] = sanitize_text_field( $in['nav_position'] );
	}

	ksort( $out );

	return $out;
}


/**
 * Sanitize client section (custom fields).
 *
 * @param $in
 * @since 2.17.0
 *
 * @return array
 */
function wpmtst_sanitize_view_client_section( $in ) {
	$out = array();

	foreach ( $in as $key => $field ) {
		if ( empty( $field['field'] ) ) {
			continue;
		}

		$out[ $key ]['field'] = sanitize_text_field( $field['field'] );

		if ( isset( $field['type'] ) ) {
			$type = sanitize_text_field( $field['type'] );
		} else {
			$type = sanitize_text_field( $field['save-type'] );
		}
		$out[ $key ]['type'] = $type;

		$out[ $key ]['before'] = sanitize_text_field( $field['before'] );

		$out[ $key ]['class'] = sanitize_text_field( $field['class'] );

		switch ( $type ) {
			case 'link':
			case 'link2':
				/**
				 * If no URL, change field type to 'text'. This happens when a URL field
				 * (e.g. company_name) is removed from Custom Fields.
				 * @since 2.10.0
				 */
				if ( ! isset( $field['url'] ) ) {
					$out[ $key ]['type'] = 'text';
					unset( $out[ $key ]['link_text'] );
					unset( $out[ $key ]['link_text_custom'] );
					unset( $out[ $key ]['new_tab'] );
				}
				else {
					$out[ $key ]['url'] = sanitize_text_field( $field['url'] );

					$out[ $key ]['link_text'] = isset( $field['link_text'] ) ? sanitize_text_field( $field['link_text'] ) : '';

					$out[ $key ]['link_text_custom'] = isset( $field['link_text_custom'] ) ? sanitize_text_field( $field['link_text_custom'] ) : '';

					$out[ $key ]['new_tab'] = isset( $field['new_tab'] ) ? 1 : 0;
				}
				break;
			case 'date':
				$format = isset( $field['format'] ) ? sanitize_text_field( $field['format'] ) : '';
				$out[ $key ]['format'] = $format;
				break;
			default:
		}

	}

	return $out;
}

/**
 * Template settings.
 *
 * @param $data
 * @param $input
 *
 * @return array
 */
function wpmtst_sanitize_view_template( $data, $input ) {
	if ( 'form' == $data['mode'] ) {
		$data['template'] = isset( $input['form-template'] ) ? sanitize_text_field( $input['form-template'] ) : '';
	} else {
		$data['template'] = isset( $input['template'] ) ? sanitize_text_field( $input['template'] ) : '';
	}

	// To save all template settings:
	foreach ( $input['template_settings'] as $template => $settings ) {
		foreach ( $settings as $key => $setting ) {
			// This does not work for checkboxes yet.
			$data['template_settings'][ $template ][ $key ] = sanitize_text_field( $setting );
		}
	}

	return $data;
}
