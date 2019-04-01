<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * WPBakery Page Builder Shortcodes settings Lazy mapping
 *
 * @package VPBakeryVisualComposer
 *
 */
$vc_config_path = vc_path_dir( 'CONFIG_DIR' );

vc_lean_map( 'vc_row_inner', null, $vc_config_path . '/containers/shortcode-vc-row-inner.php' );
vc_lean_map( 'vc_column_text', null, $vc_config_path . '/content/shortcode-vc-column-text.php' );

vc_lean_map( 'vc_text_separator', null, $vc_config_path . '/content/shortcode-vc-text-separator.php' );

vc_lean_map( 'vc_single_image', null, $vc_config_path . '/content/shortcode-vc-single-image.php' );
vc_lean_map( 'vc_gallery', null, $vc_config_path . '/content/shortcode-vc-gallery.php' );

vc_lean_map( 'vc_custom_heading', null, $vc_config_path . '/content/shortcode-vc-custom-heading.php' );

vc_lean_map( 'vc_raw_html', null, $vc_config_path . '/structure/shortcode-vc-raw-html.php' );
vc_lean_map( 'vc_raw_js', null, $vc_config_path . '/structure/shortcode-vc-raw-js.php' );
vc_lean_map( 'vc_pie', null, $vc_config_path . '/content/shortcode-vc-pie.php' );
vc_lean_map( 'vc_video', null, $vc_config_path . '/content/shortcode-vc-video.php' );

vc_lean_map( 'vc_icon', null, $vc_config_path . '/content/shortcode-vc-icon.php' );
vc_lean_map( 'vc_zigzag', null, $vc_config_path . '/content/shortcode-vc-zigzag.php' );


/* nectar addition
vc_lean_map( 'vc_row', null, $vc_config_path . '/containers/shortcode-vc-row.php' );
vc_lean_map( 'vc_row_inner', null, $vc_config_path . '/containers/shortcode-vc-row-inner.php' );
vc_lean_map( 'vc_column', null, $vc_config_path . '/containers/shortcode-vc-column.php' );
vc_lean_map( 'vc_column_inner', null, $vc_config_path . '/containers/shortcode-vc-column-inner.php' );
vc_lean_map( 'vc_column_text', null, $vc_config_path . '/content/shortcode-vc-column-text.php' );
vc_lean_map( 'vc_section', null, $vc_config_path . '/containers/shortcode-vc-section.php' );
vc_lean_map( 'vc_icon', null, $vc_config_path . '/content/shortcode-vc-icon.php' );
vc_lean_map( 'vc_separator', null, $vc_config_path . '/content/shortcode-vc-separator.php' );
vc_lean_map( 'vc_zigzag', null, $vc_config_path . '/content/shortcode-vc-zigzag.php' );
vc_lean_map( 'vc_text_separator', null, $vc_config_path . '/content/shortcode-vc-text-separator.php' );
vc_lean_map( 'vc_message', null, $vc_config_path . '/content/shortcode-vc-message.php' );
vc_lean_map( 'vc_hoverbox', null, $vc_config_path . '/content/shortcode-vc-hoverbox.php' );

vc_lean_map( 'vc_facebook', null, $vc_config_path . '/social/shortcode-vc-facebook.php' );
vc_lean_map( 'vc_tweetmeme', null, $vc_config_path . '/social/shortcode-vc-tweetmeme.php' );
vc_lean_map( 'vc_googleplus', null, $vc_config_path . '/social/shortcode-vc-googleplus.php' );
vc_lean_map( 'vc_pinterest', null, $vc_config_path . '/social/shortcode-vc-pinterest.php' );

vc_lean_map( 'vc_toggle', null, $vc_config_path . '/content/shortcode-vc-toggle.php' );
vc_lean_map( 'vc_single_image', null, $vc_config_path . '/content/shortcode-vc-single-image.php' );
vc_lean_map( 'vc_gallery', null, $vc_config_path . '/content/shortcode-vc-gallery.php' );
vc_lean_map( 'vc_images_carousel', null, $vc_config_path . '/content/shortcode-vc-images-carousel.php' );

vc_lean_map( 'vc_tta_tabs', null, $vc_config_path . '/tta/shortcode-vc-tta-tabs.php' );
vc_lean_map( 'vc_tta_tour', null, $vc_config_path . '/tta/shortcode-vc-tta-tour.php' );
vc_lean_map( 'vc_tta_accordion', null, $vc_config_path . '/tta/shortcode-vc-tta-accordion.php' );
vc_lean_map( 'vc_tta_pageable', null, $vc_config_path . '/tta/shortcode-vc-tta-pageable.php' );
vc_lean_map( 'vc_tta_section', null, $vc_config_path . '/tta/shortcode-vc-tta-section.php' );

vc_lean_map( 'vc_custom_heading', null, $vc_config_path . '/content/shortcode-vc-custom-heading.php' );

vc_lean_map( 'vc_btn', null, $vc_config_path . '/buttons/shortcode-vc-btn.php' );
vc_lean_map( 'vc_cta', null, $vc_config_path . '/buttons/shortcode-vc-cta.php' );

vc_lean_map( 'vc_widget_sidebar', null, $vc_config_path . '/structure/shortcode-vc-widget-sidebar.php' );
vc_lean_map( 'vc_posts_slider', null, $vc_config_path . '/content/shortcode-vc-posts-slider.php' );
vc_lean_map( 'vc_video', null, $vc_config_path . '/content/shortcode-vc-video.php' );
vc_lean_map( 'vc_gmaps', null, $vc_config_path . '/content/shortcode-vc-gmaps.php' );
vc_lean_map( 'vc_raw_html', null, $vc_config_path . '/structure/shortcode-vc-raw-html.php' );
vc_lean_map( 'vc_raw_js', null, $vc_config_path . '/structure/shortcode-vc-raw-js.php' );
vc_lean_map( 'vc_flickr', null, $vc_config_path . '/content/shortcode-vc-flickr.php' );
vc_lean_map( 'vc_progress_bar', null, $vc_config_path . '/content/shortcode-vc-progress-bar.php' );
vc_lean_map( 'vc_pie', null, $vc_config_path . '/content/shortcode-vc-pie.php' );
vc_lean_map( 'vc_round_chart', null, $vc_config_path . '/content/shortcode-vc-round-chart.php' );
vc_lean_map( 'vc_line_chart', null, $vc_config_path . '/content/shortcode-vc-line-chart.php' );

vc_lean_map( 'vc_wp_search', null, $vc_config_path . '/wp/shortcode-vc-wp-search.php' );
vc_lean_map( 'vc_wp_meta', null, $vc_config_path . '/wp/shortcode-vc-wp-meta.php' );
vc_lean_map( 'vc_wp_recentcomments', null, $vc_config_path . '/wp/shortcode-vc-wp-recentcomments.php' );
vc_lean_map( 'vc_wp_calendar', null, $vc_config_path . '/wp/shortcode-vc-wp-calendar.php' );
vc_lean_map( 'vc_wp_pages', null, $vc_config_path . '/wp/shortcode-vc-wp-pages.php' );
vc_lean_map( 'vc_wp_tagcloud', null, $vc_config_path . '/wp/shortcode-vc-wp-tagcloud.php' );
vc_lean_map( 'vc_wp_custommenu', null, $vc_config_path . '/wp/shortcode-vc-wp-custommenu.php' );
vc_lean_map( 'vc_wp_text', null, $vc_config_path . '/wp/shortcode-vc-wp-text.php' );
vc_lean_map( 'vc_wp_posts', null, $vc_config_path . '/wp/shortcode-vc-wp-posts.php' );
vc_lean_map( 'vc_wp_links', null, $vc_config_path . '/wp/shortcode-vc-wp-links.php' );
vc_lean_map( 'vc_wp_categories', null, $vc_config_path . '/wp/shortcode-vc-wp-categories.php' );
vc_lean_map( 'vc_wp_archives', null, $vc_config_path . '/wp/shortcode-vc-wp-archives.php' );
vc_lean_map( 'vc_wp_rss', null, $vc_config_path . '/wp/shortcode-vc-wp-rss.php' );

vc_lean_map( 'vc_empty_space', null, $vc_config_path . '/content/shortcode-vc-empty-space.php' );

vc_lean_map( 'vc_basic_grid', null, $vc_config_path . '/grids/shortcode-vc-basic-grid.php' );
vc_lean_map( 'vc_media_grid', null, $vc_config_path . '/grids/shortcode-vc-media-grid.php' );
vc_lean_map( 'vc_masonry_grid', null, $vc_config_path . '/grids/shortcode-vc-masonry-grid.php' );
vc_lean_map( 'vc_masonry_media_grid', null, $vc_config_path . '/grids/shortcode-vc-masonry-media-grid.php' );

vc_lean_map( 'vc_tabs', null, $vc_config_path . '/deprecated/shortcode-vc-tabs.php' );
vc_lean_map( 'vc_tour', null, $vc_config_path . '/deprecated/shortcode-vc-tour.php' );
vc_lean_map( 'vc_tab', null, $vc_config_path . '/deprecated/shortcode-vc-tab.php' );
vc_lean_map( 'vc_accordion', null, $vc_config_path . '/deprecated/shortcode-vc-accordion.php' );
vc_lean_map( 'vc_accordion_tab', null, $vc_config_path . '/deprecated/shortcode-vc-accordion-tab.php' );
vc_lean_map( 'vc_posts_grid', null, $vc_config_path . '/deprecated/shortcode-vc-posts-grid.php' );
vc_lean_map( 'vc_carousel', null, $vc_config_path . '/deprecated/shortcode-vc-carousel.php' );
vc_lean_map( 'vc_button', null, $vc_config_path . '/deprecated/shortcode-vc-button.php' );
vc_lean_map( 'vc_button2', null, $vc_config_path . '/deprecated/shortcode-vc-button2.php' );
vc_lean_map( 'vc_cta_button', null, $vc_config_path . '/deprecated/shortcode-vc-cta-button.php' );
vc_lean_map( 'vc_cta_button2', null, $vc_config_path . '/deprecated/shortcode-vc-cta-button2.php' );

*/

if ( is_admin() ) {
	add_action( 'admin_print_scripts-post.php', array(
		Vc_Shortcodes_Manager::getInstance(),
		'buildShortcodesAssets',
	), 1 );
	add_action( 'admin_print_scripts-post-new.php', array(
		Vc_Shortcodes_Manager::getInstance(),
		'buildShortcodesAssets',
	), 1 );
	add_action( 'vc-render-templates-preview-template', array(
		Vc_Shortcodes_Manager::getInstance(),
		'buildShortcodesAssets',
	), 1 );
} elseif ( vc_is_page_editable() ) {
	add_action( 'wp_head', array(
		Vc_Shortcodes_Manager::getInstance(),
		'buildShortcodesAssetsForEditable',
	) ); // @todo where these icons are used in iframe?
}

/**
 * @deprecated 4.12
 * @return mixed|void
 */
function vc_add_css_animation() {
	return vc_map_add_css_animation();
}

function vc_target_param_list() {
	return array(
		__( 'Same window', 'js_composer' ) => '_self',
		__( 'New window', 'js_composer' ) => '_blank',
	);
}

function vc_layout_sub_controls() {
	return array(
		array(
			'link_post',
			__( 'Link to post', 'js_composer' ),
		),
		array(
			'no_link',
			__( 'No link', 'js_composer' ),
		),
		array(
			'link_image',
			__( 'Link to bigger image', 'js_composer' ),
		),
	);
}

function vc_pixel_icons() {
	return array(
		array( 'vc_pixel_icon vc_pixel_icon-alert' => __( 'Alert', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-info' => __( 'Info', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-tick' => __( 'Tick', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-explanation' => __( 'Explanation', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-address_book' => __( 'Address book', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-alarm_clock' => __( 'Alarm clock', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-anchor' => __( 'Anchor', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-application_image' => __( 'Application Image', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-arrow' => __( 'Arrow', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-asterisk' => __( 'Asterisk', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-hammer' => __( 'Hammer', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-balloon' => __( 'Balloon', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-balloon_buzz' => __( 'Balloon Buzz', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-balloon_facebook' => __( 'Balloon Facebook', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-balloon_twitter' => __( 'Balloon Twitter', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-battery' => __( 'Battery', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-binocular' => __( 'Binocular', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_excel' => __( 'Document Excel', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_image' => __( 'Document Image', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_music' => __( 'Document Music', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_office' => __( 'Document Office', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_pdf' => __( 'Document PDF', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_powerpoint' => __( 'Document Powerpoint', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_word' => __( 'Document Word', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-bookmark' => __( 'Bookmark', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-camcorder' => __( 'Camcorder', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-camera' => __( 'Camera', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-chart' => __( 'Chart', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-chart_pie' => __( 'Chart pie', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-clock' => __( 'Clock', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-fire' => __( 'Fire', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-heart' => __( 'Heart', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-mail' => __( 'Mail', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-play' => __( 'Play', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-shield' => __( 'Shield', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-video' => __( 'Video', 'js_composer' ) ),
	);
}

function vc_colors_arr() {
	return array(
		__( 'Grey', 'js_composer' ) => 'wpb_button',
		__( 'Blue', 'js_composer' ) => 'btn-primary',
		__( 'Turquoise', 'js_composer' ) => 'btn-info',
		__( 'Green', 'js_composer' ) => 'btn-success',
		__( 'Orange', 'js_composer' ) => 'btn-warning',
		__( 'Red', 'js_composer' ) => 'btn-danger',
		__( 'Black', 'js_composer' ) => 'btn-inverse',
	);
}

// Used in "Button" and "Call to Action" blocks
function vc_size_arr() {
	return array(
		__( 'Regular', 'js_composer' ) => 'wpb_regularsize',
		__( 'Large', 'js_composer' ) => 'btn-large',
		__( 'Small', 'js_composer' ) => 'btn-small',
		__( 'Mini', 'js_composer' ) => 'btn-mini',
	);
}

function vc_icons_arr() {
	return array(
		__( 'None', 'js_composer' ) => 'none',
		__( 'Address book icon', 'js_composer' ) => 'wpb_address_book',
		__( 'Alarm clock icon', 'js_composer' ) => 'wpb_alarm_clock',
		__( 'Anchor icon', 'js_composer' ) => 'wpb_anchor',
		__( 'Application Image icon', 'js_composer' ) => 'wpb_application_image',
		__( 'Arrow icon', 'js_composer' ) => 'wpb_arrow',
		__( 'Asterisk icon', 'js_composer' ) => 'wpb_asterisk',
		__( 'Hammer icon', 'js_composer' ) => 'wpb_hammer',
		__( 'Balloon icon', 'js_composer' ) => 'wpb_balloon',
		__( 'Balloon Buzz icon', 'js_composer' ) => 'wpb_balloon_buzz',
		__( 'Balloon Facebook icon', 'js_composer' ) => 'wpb_balloon_facebook',
		__( 'Balloon Twitter icon', 'js_composer' ) => 'wpb_balloon_twitter',
		__( 'Battery icon', 'js_composer' ) => 'wpb_battery',
		__( 'Binocular icon', 'js_composer' ) => 'wpb_binocular',
		__( 'Document Excel icon', 'js_composer' ) => 'wpb_document_excel',
		__( 'Document Image icon', 'js_composer' ) => 'wpb_document_image',
		__( 'Document Music icon', 'js_composer' ) => 'wpb_document_music',
		__( 'Document Office icon', 'js_composer' ) => 'wpb_document_office',
		__( 'Document PDF icon', 'js_composer' ) => 'wpb_document_pdf',
		__( 'Document Powerpoint icon', 'js_composer' ) => 'wpb_document_powerpoint',
		__( 'Document Word icon', 'js_composer' ) => 'wpb_document_word',
		__( 'Bookmark icon', 'js_composer' ) => 'wpb_bookmark',
		__( 'Camcorder icon', 'js_composer' ) => 'wpb_camcorder',
		__( 'Camera icon', 'js_composer' ) => 'wpb_camera',
		__( 'Chart icon', 'js_composer' ) => 'wpb_chart',
		__( 'Chart pie icon', 'js_composer' ) => 'wpb_chart_pie',
		__( 'Clock icon', 'js_composer' ) => 'wpb_clock',
		__( 'Fire icon', 'js_composer' ) => 'wpb_fire',
		__( 'Heart icon', 'js_composer' ) => 'wpb_heart',
		__( 'Mail icon', 'js_composer' ) => 'wpb_mail',
		__( 'Play icon', 'js_composer' ) => 'wpb_play',
		__( 'Shield icon', 'js_composer' ) => 'wpb_shield',
		__( 'Video icon', 'js_composer' ) => 'wpb_video',
	);
}

require_once vc_path_dir( 'CONFIG_DIR', 'grids/vc-grids-functions.php' );
if ( 'vc_get_autocomplete_suggestion' === vc_request_param( 'action' ) || 'vc_edit_form' === vc_post_param( 'action' ) ) {
	add_filter( 'vc_autocomplete_vc_basic_grid_include_callback', 'vc_include_field_search', 10, 1 ); // Get suggestion(find). Must return an array
	add_filter( 'vc_autocomplete_vc_basic_grid_include_render', 'vc_include_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)
	add_filter( 'vc_autocomplete_vc_masonry_grid_include_callback', 'vc_include_field_search', 10, 1 ); // Get suggestion(find). Must return an array
	add_filter( 'vc_autocomplete_vc_masonry_grid_include_render', 'vc_include_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)

	// Narrow data taxonomies
	add_filter( 'vc_autocomplete_vc_basic_grid_taxonomies_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1 );
	add_filter( 'vc_autocomplete_vc_basic_grid_taxonomies_render', 'vc_autocomplete_taxonomies_field_render', 10, 1 );

	add_filter( 'vc_autocomplete_vc_masonry_grid_taxonomies_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1 );
	add_filter( 'vc_autocomplete_vc_masonry_grid_taxonomies_render', 'vc_autocomplete_taxonomies_field_render', 10, 1 );

	// Narrow data taxonomies for exclude_filter
	add_filter( 'vc_autocomplete_vc_basic_grid_exclude_filter_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1 );
	add_filter( 'vc_autocomplete_vc_basic_grid_exclude_filter_render', 'vc_autocomplete_taxonomies_field_render', 10, 1 );

	add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_filter_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1 );
	add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_filter_render', 'vc_autocomplete_taxonomies_field_render', 10, 1 );

	add_filter( 'vc_autocomplete_vc_basic_grid_exclude_callback', 'vc_exclude_field_search', 10, 1 ); // Get suggestion(find). Must return an array
	add_filter( 'vc_autocomplete_vc_basic_grid_exclude_render', 'vc_exclude_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)
	add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_callback', 'vc_exclude_field_search', 10, 1 ); // Get suggestion(find). Must return an array
	add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_render', 'vc_exclude_field_render', 10, 1 ); // Render exact product. Must return an array (label,value);
}

