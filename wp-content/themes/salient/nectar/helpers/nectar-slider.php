<?php
/**
 * Nectar Slider helper functions
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


global $nectar_options;

$nectar_disable_nectar_slider = ( ! empty( $nectar_options['disable_nectar_slider_pt'] ) && $nectar_options['disable_nectar_slider_pt'] == '1' ) ? true : false;


// -----------------------------------------------------------------#
// Create nectar slider section
// -----------------------------------------------------------------#
function nectar_slider_register() {

	$labels = array(
		'name'          => __( 'Slides', 'salient' ),
		'singular_name' => __( 'Slide', 'salient' ),
		'search_items'  => __( 'Search Slides', 'salient' ),
		'all_items'     => __( 'All Slides', 'salient' ),
		'parent_item'   => __( 'Parent Slide', 'salient' ),
		'edit_item'     => __( 'Edit Slide', 'salient' ),
		'update_item'   => __( 'Update Slide', 'salient' ),
		'add_new_item'  => __( 'Add New Slide', 'salient' ),
		'menu_name'     => __( 'Nectar Slider', 'salient' ),
	);

	 $nectarslider_menu_icon = ( floatval( get_bloginfo( 'version' ) ) >= '3.8' ) ? 'dashicons-star-empty' : NECTAR_FRAMEWORK_DIRECTORY . 'assets/img/icons/nectar-slider.png';

	 $args = array(
		 'labels'              => $labels,
		 'singular_label'      => esc_html__( 'Nectar Slider', 'salient' ),
		 'public'              => false,
		 'show_ui'             => true,
		 'hierarchical'        => false,
		 'menu_position'       => 10,
		 'menu_icon'           => $nectarslider_menu_icon,
		 'exclude_from_search' => true,
		 'supports'            => false,
	 );

	register_post_type( 'nectar_slider', $args );
}


$slider_locations_labels = array(
	'name'          => __( 'Slider Locations', 'salient' ),
	'singular_name' => __( 'Slider Location', 'salient' ),
	'search_items'  => __( 'Search Slider Locations', 'salient' ),
	'all_items'     => __( 'All Slider Locations', 'salient' ),
	'edit_item'     => __( 'Edit Slider Location', 'salient' ),
	'update_item'   => __( 'Update Slider Location', 'salient' ),
	'add_new_item'  => __( 'Add New Slider Location', 'salient' ),
	'new_item_name' => __( 'New Slider Location', 'salient' ),
	'menu_name'     => __( 'Slider Locations', 'salient' ),
);

register_taxonomy(
	'slider-locations',
	array( 'nectar_slider' ),
	array(
		'hierarchical' => true,
		'labels'       => $slider_locations_labels,
		'show_ui'      => true,
		'public'       => false,
		'query_var'    => true,
		'rewrite'      => array( 'slug' => 'slider-locations' ),
	)
);



if ( $nectar_disable_nectar_slider != true ) {
	add_action( 'init', 'nectar_slider_register' );
}





if ( $nectar_disable_nectar_slider != true ) {
	add_filter( 'manage_edit-nectar_slider_columns', 'edit_columns_nectar_slider' );
}

function edit_columns_nectar_slider( $columns ) {
	$column_thumbnail = array( 'thumbnail' => 'Thumbnail' );
	$column_caption   = array( 'caption' => 'Caption' );
	$columns          = array_slice( $columns, 0, 1, true ) + $column_thumbnail + array_slice( $columns, 1, null, true );
	$columns          = array_slice( $columns, 0, 2, true ) + $column_caption + array_slice( $columns, 2, null, true );
	return $columns;
}


if ( $nectar_disable_nectar_slider != true ) {
	add_action( 'manage_nectar_slider_posts_custom_column', 'nectar_slider_custom_columns', 10, 2 );
}

function nectar_slider_custom_columns( $portfolio_columns, $post_id ) {

	switch ( $portfolio_columns ) {
		case 'thumbnail':
			$background_type = get_post_meta( $post_id, '_nectar_slider_bg_type', true );
			if ( $background_type == 'image_bg' ) {

				$thumbnail = get_post_meta( $post_id, '_nectar_slider_image', true );

				if ( ! empty( $thumbnail ) ) {
					echo '<a href="' . esc_url( get_admin_url() ) . 'post.php?post=' . $post_id . '&action=edit"><img class="slider-thumb" src="' . $thumbnail . '" /></a>';
				} else {
					echo '<a href="' . esc_url( get_admin_url() ) . 'post.php?post=' . $post_id . '&action=edit"><img class="slider-thumb" src="' . NECTAR_FRAMEWORK_DIRECTORY . 'assets/img/slider-default-thumb.jpg" /></a>' .
						 '<strong><a class="row-title" href="' . esc_url( get_admin_url() ) . 'post.php?post=' . $post_id . '&action=edit">No image added yet</a></strong>';
				}
			} else {
				 $thumbnail = get_post_meta( $post_id, '_nectar_slider_preview_image', true );

				if ( ! empty( $thumbnail ) ) {
					echo '<a href="' . esc_url( get_admin_url() ) . 'post.php?post=' . $post_id . '&action=edit"><img class="slider-thumb" src="' . $thumbnail . '" /></a>';
				} else {
					echo '<a href="' . esc_url( get_admin_url() ) . 'post.php?post=' . $post_id . '&action=edit"><img class="slider-thumb" src="' . NECTAR_FRAMEWORK_DIRECTORY . 'assets/img/slider-default-video-thumb.jpg" /></a>' .
						 '<strong><a class="row-title" href="' . esc_url( get_admin_url() ) . 'post.php?post=' . $post_id . '&action=edit">No video preview image added yet</a></strong>';
				}
			}

			break;

		case 'caption':
			$caption = get_post_meta( $post_id, '_nectar_slider_caption', true );
			$heading = get_post_meta( $post_id, '_nectar_slider_heading', true );
			echo '<h2>' . wp_kses_post( $heading ) . '</h2><p>' . wp_kses_post( $caption ) . '</p>';
			break;

		default:
			break;
	}
}


if ( $nectar_disable_nectar_slider != true ) {
	add_action( 'admin_menu', 'nectar_slider_ordering' );
}

function nectar_slider_ordering() {
	add_submenu_page(
		'edit.php?post_type=nectar_slider',
		'Order Slides',
		'Slide Ordering',
		'edit_pages',
		'nectar-slide-order',
		'nectar_slider_order_page'
	);
}

function nectar_slider_order_page(){ ?>
	
	<div class="wrap" data-base-url="<?php echo esc_url( admin_url( 'edit.php?post_type=nectar_slider&page=nectar-slide-order' ) ); ?>">
		<h2><?php echo esc_html__( 'Sort Slides', 'salient' ); ?></h2>
		<p><?php echo esc_html__( 'Choose your slider location below and simply drag your slides up or down - they will automatically be saved in that order.', 'salient' ); ?></p>
		
	<?php

	( isset( $_GET['slider-location'] ) ) ? $location = sanitize_text_field( $_GET['slider-location'] ) : $location = false;
	$slides = new WP_Query(
		array(
			'post_type'        => 'nectar_slider',
			'slider-locations' => $location,
			'posts_per_page'   => -1,
			'order'            => 'ASC',
			'orderby'          => 'menu_order',
		)
	);
	?>
	<?php if ( $slides->have_posts() ) : ?>
		
		<?php
		wp_nonce_field( basename( __FILE__ ), 'nectar_meta_box_nonce' );
		echo '<div class="slider-locations">';
		global $typenow;
		$args       = array(
			'public'   => false,
			'_builtin' => false,
		);
		$post_types = get_post_types( $args );
		if ( in_array( $typenow, $post_types ) ) {
			$filters = get_object_taxonomies( $typenow );
			foreach ( $filters as $tax_slug ) {
				$tax_obj = get_taxonomy( $tax_slug );
				wp_dropdown_categories(
					array(
						'show_option_all' => 'Slider Locations',
						'taxonomy'        => $tax_slug,
						'name'            => $tax_obj->name,
						// 'orderby' => 'term_order',
						'selected'        => isset( $location ) ? $location : false,
						'hierarchical'    => $tax_obj->hierarchical,
						'show_count'      => false,
						'hide_empty'      => true,
					)
				);
			}
		}
		echo '</div>';
		if ( isset( $location ) && $location != false ) {
			?>
		
		<table class="wp-list-table widefat fixed posts" id="sortable-table">
			<thead>
				<tr>
					<th class="column-order"><?php echo esc_html__( 'Order', 'salient' ); ?></th>
					<th class="manage-column column-thumbnail"><?php echo esc_html__( 'Image', 'salient' ); ?></th>
					<th class="manage-column column-caption"><?php echo esc_html__( 'Caption', 'salient' ); ?></th>
				</tr>
			</thead>
			<tbody data-post-type="nectar_slider">
			<?php
			while ( $slides->have_posts() ) :
				$slides->the_post();
				?>
				<tr id="post-<?php the_ID(); ?>">
					<td class="column-order"><img src="<?php echo NECTAR_FRAMEWORK_DIRECTORY . 'assets/img/sortable.png'; ?>" alt="Move Icon" width="25" height="25" class="" /></td>
					<td class="thumbnail column-thumbnail">
						<?php
						global $post;
						$post_id = $post->ID;

						$background_type = get_post_meta( $post_id, '_nectar_slider_bg_type', true );
						if ( $background_type == 'image_bg' ) {

							$thumbnail = get_post_meta( $post_id, '_nectar_slider_image', true );

							if ( ! empty( $thumbnail ) ) {
								echo '<a href="' . esc_url( get_admin_url() ) . 'post.php?post=' . $post_id . '&action=edit"><img class="slider-thumb" src="' . $thumbnail . '" /></a>';
							} else {
								echo '<a href="' . esc_url( get_admin_url() ) . 'post.php?post=' . $post_id . '&action=edit"><img class="slider-thumb" src="' . NECTAR_FRAMEWORK_DIRECTORY . 'assets/img/slider-default-thumb.jpg" /></a>' .
									 '<strong><a class="row-title" href="' . esc_url( get_admin_url() ) . 'post.php?post=' . $post_id . '&action=edit">No image added yet</a></strong>';
							}
						} else {
							 $thumbnail = get_post_meta( $post_id, '_nectar_slider_preview_image', true );

							if ( ! empty( $thumbnail ) ) {
								echo '<a href="' . esc_url( get_admin_url() ) . 'post.php?post=' . $post_id . '&action=edit"><img class="slider-thumb" src="' . $thumbnail . '" /></a>';
							} else {
								echo '<a href="' . esc_url( get_admin_url() ) . 'post.php?post=' . $post_id . '&action=edit"><img class="slider-thumb" src="' . NECTAR_FRAMEWORK_DIRECTORY . 'assets/img/slider-default-video-thumb.jpg" /></a>' .
									 '<strong><a class="row-title" href="' . esc_url( get_admin_url() ) . 'post.php?post=' . $post_id . '&action=edit">No video preview image added yet</a></strong>';
							}
						}
						?>
						
					</td>
					<td class="caption column-caption">
						<?php
						$caption = get_post_meta( $post->ID, '_nectar_slider_caption', true );
						echo wp_kses_post( $caption );
						?>
					</td>
				</tr>
			<?php endwhile; ?>
			</tbody>
			<tfoot>
				<tr>
					<th class="column-order"><?php echo esc_html__( 'Order', 'salient' ); ?></th>
					<th class="manage-column column-thumbnail"><?php echo esc_html__( 'Image', 'salient' ); ?></th>
					<th class="manage-column column-caption"><?php echo esc_html__( 'Caption', 'salient' ); ?></th>
				</tr>
			</tfoot>

		</table>
	<?php } ?>
	
	<?php else : ?>

		<p>No slides found, why not <a href="post-new.php?post_type=nectar_slider">create one?</a></p>

	<?php endif; ?>
	<?php wp_reset_postdata(); ?>

	</div><!-- .wrap -->
	
	<?php
}


if ( $nectar_disable_nectar_slider != true ) {
	add_action( 'admin_enqueue_scripts', 'nectar_slider_enqueue_scripts' );
}

function nectar_slider_enqueue_scripts() {
	global $typenow;
	global $nectar_get_template_directory_uri;
	if ( 'nectar_slider' == $typenow ) {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'nectar-reorder', NECTAR_FRAMEWORK_DIRECTORY . 'assets/js/nectar-reorder.js' );
	}

	wp_register_script( 'chosen', $nectar_get_template_directory_uri . '/nectar/tinymce/shortcode_generator/js/chosen/chosen.jquery.min.js', array( 'jquery' ), '8.0.1', true );
	wp_register_style( 'chosen', $nectar_get_template_directory_uri . '/nectar/tinymce/shortcode_generator/css/chosen/chosen.css', array(), '8.0.1', 'all' );

	wp_enqueue_style( 'chosen' );
	wp_enqueue_script( 'chosen' );

}


if ( $nectar_disable_nectar_slider != true ) {
	add_action( 'wp_ajax_nectar_update_slide_order', 'nectar_slider_update_order' );
}

// slide order ajax callback
function nectar_slider_update_order() {

		global $wpdb;

		$post_type = sanitize_text_field( $_POST['postType'] );
		$order     = $_POST['order'];

	if ( ! isset( $_POST['nectar_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['nectar_meta_box_nonce'], basename( __FILE__ ) ) ) {
		return;
	}

	foreach ( $order as $menu_order => $post_id ) {
		$post_id    = intval( str_ireplace( 'post-', '', $post_id ) );
		$menu_order = intval( $menu_order );

		wp_update_post(
			array(
				'ID'         => stripslashes( htmlspecialchars( $post_id ) ),
				'menu_order' => stripslashes( htmlspecialchars( $menu_order ) ),
			)
		);
	}

		die( '1' );
}


// order the default nectar slider page correctly
function set_nectar_slider_admin_order( $wp_query ) {

	$post_type = ( isset( $wp_query->query['post_type'] ) ) ? $wp_query->query['post_type'] : '';

	if ( $post_type == 'nectar_slider' ) {

		$wp_query->set( 'orderby', 'menu_order' );
		$wp_query->set( 'order', 'ASC' );
	}

}

if ( is_admin() && $nectar_disable_nectar_slider != true ) {
	add_filter( 'pre_get_posts', 'set_nectar_slider_admin_order' );
}


function nectar_my_restrict_manage_posts() {
	global $typenow;
	$args       = array(
		'public'   => false,
		'_builtin' => false,
	);
	$post_types = get_post_types( $args );
	if ( in_array( $typenow, $post_types ) ) {

		$filters = get_object_taxonomies( $typenow );
		if ( $typenow != 'product' ) {
			foreach ( $filters as $tax_slug ) {
				$tax_obj = get_taxonomy( $tax_slug );
				wp_dropdown_categories(
					array(
						'show_option_all' => esc_html__( 'Show All ', 'salient' ) . $tax_obj->label,
						'taxonomy'        => $tax_slug,
						'name'            => $tax_obj->name,
						// 'orderby' => 'term_order',
						'selected'        => isset( $_GET[ $tax_obj->query_var ] ) ? $_GET[ $tax_obj->query_var ] : false,
						'hierarchical'    => $tax_obj->hierarchical,
						'show_count'      => false,
						'hide_empty'      => true,
					)
				);
			}
		}
	}
}
function nectar_my_convert_restrict( $query ) {
	global $pagenow;
	global $typenow;
	if ( $pagenow == 'edit.php' ) {
		$filters = get_object_taxonomies( $typenow );
		foreach ( $filters as $tax_slug ) {
			$var = &$query->query_vars[ $tax_slug ];
			if ( isset( $var ) ) {
				$term = get_term_by( 'id', $var, $tax_slug );
				if ( $term ) {
					$var = $term->slug; }
			}
		}
	}
}

if ( $nectar_disable_nectar_slider != true && is_admin() ) {
	add_action( 'restrict_manage_posts', 'nectar_my_restrict_manage_posts' );
	add_filter( 'parse_query', 'nectar_my_convert_restrict' );
}






// -----------------------------------------------------------------#
// Nectar slider display
// -----------------------------------------------------------------#
$real_fs = 0;


if ( ! function_exists( 'nectar_slider_display' ) ) {

	function nectar_slider_display( $config_arr ) {
		 global $nectar_disable_nectar_slider;

		if ( $nectar_disable_nectar_slider == true ) {
			echo esc_html__( 'Nectar Slider Post Type Disabled - please reanble in the Salient options panel > General Settings > Toggle Theme Features tab.', 'salient' );
			return false;
		}
		
		//no location supplied.
		if( empty($config_arr['location']) ) {
			return false;
		}
		 global $post;
		 global $nectar_options;
		 global $real_fs;

		$midnight_parallax = null;
		$midnight_regular   = null;

		 $boxed = ( ! empty( $nectar_options['boxed_layout'] ) && $nectar_options['boxed_layout'] == '1' ) ? '1' : '0';
		if ( $config_arr['full_width'] == 'true' && $boxed != '1' ) {
			$fullwidth = 'true'; } elseif ( $config_arr['full_width'] == 'true' && $boxed == '1' ) {
			$fullwidth = 'boxed-full-width'; } else {
				$fullwidth = 'false'; 
			}

			$dynamic_height_style_markup = '';

			if ( ! empty( $config_arr['min_slider_height'] ) || ! empty( $config_arr['flexible_slider_height'] ) && $config_arr['flexible_slider_height'] == 'true' ) {
				$dynamic_height_style_markup .= 'style="';
			}
			// min height
			$dynamic_height_style_markup .= ( ! empty( $config_arr['min_slider_height'] ) ) ? 'min-height: ' . $config_arr['min_slider_height'] . 'px; ' : '';

			// flexible height
			if ( ! empty( $config_arr['flexible_slider_height'] ) && $config_arr['flexible_slider_height'] == 'true' && $fullwidth == 'true' && $config_arr['fullscreen'] != 'true' ) {
				$dynamic_height_style_markup .= ( ! empty( $config_arr['slider_height'] ) ) ? 'height: calc( ' . intval( $config_arr['slider_height'] ) . ' * 100vw / 1600 );' : '';
			}

			if ( ! empty( $config_arr['min_slider_height'] ) || ! empty( $config_arr['flexible_slider_height'] ) && $config_arr['flexible_slider_height'] == 'true' ) {
				$dynamic_height_style_markup .= '"';
			}

			// disable parallax for full page
			$page_full_screen_rows = ( isset( $post->ID ) ) ? get_post_meta( $post->ID, '_nectar_full_screen_rows', true ) : '';
			if ( $page_full_screen_rows == 'on' ) {
				$config_arr['parallax'] = 'false';
			}

			$animate_in_effect = ( ! empty( $nectar_options['header-animate-in-effect'] ) ) ? $nectar_options['header-animate-in-effect'] : 'none';

			// adding parallax wrapper if selected
			if ( $config_arr['parallax'] == 'true' ) {

				if ( stripos( $post->post_content, '[nectar_slider' ) !== false && stripos( $post->post_content, '[nectar_slider' ) === 0 && $real_fs == 0 ) {
					$first_section = '';
					$real_fs       = 1;
				} else {
					$first_section = '';
				}

				$midnight_parallax = 'data-midnight="nectar-slider"';
				$midnight_regular  = null;

				$slider = '<div ' . $midnight_parallax . ' class="parallax_slider_outer ' . $first_section . '" ' . $dynamic_height_style_markup . '>';

			} else {
				$slider = ''; }

			if ( $config_arr['parallax'] != 'true' ) {

				if ( stripos( $post->post_content, '[nectar_slider' ) !== false && stripos( $post->post_content, '[nectar_slider' ) === 0 && $real_fs == 0 ) {
					$first_section     = '';
					$real_fs           = 1;
					$midnight_parallax = null;
					$midnight_regular  = 'data-midnight="nectar-slider"';
				} else {
					$first_section = ''; }
			} else {
						$midnight_parallax = null;
						$midnight_regular  = null;
						$first_section     = '';
			}

			$text_overrides = null;
			if ( ! empty( $config_arr['tablet_header_font_size'] ) ) {
				$text_overrides .= ' data-tho="' . $config_arr['tablet_header_font_size'] . '"';
			}
			if ( ! empty( $config_arr['tablet_caption_font_size'] ) ) {
				$text_overrides .= ' data-tco="' . $config_arr['tablet_caption_font_size'] . '"';
			}
			if ( ! empty( $config_arr['phone_header_font_size'] ) ) {
				$text_overrides .= ' data-pho="' . $config_arr['phone_header_font_size'] . '"';
			}
			if ( ! empty( $config_arr['phone_caption_font_size'] ) ) {
				$text_overrides .= ' data-pco="' . $config_arr['phone_caption_font_size'] . '"';
			}

			$nectar_slider_unique_id = 'ns-id-' . uniqid();

			$slider .= '<div ' . $midnight_regular . ' data-transition="' . $config_arr['slider_transition'] . '" data-overall_style="' . $config_arr['overall_style'] . '" data-flexible-height="' . $config_arr['flexible_slider_height'] . '" data-animate-in-effect="' . $animate_in_effect . '" data-fullscreen="' . $config_arr['fullscreen'] . '" ';
			$slider .= 'data-button-sizing="' . $config_arr['button_sizing'] . '" data-button-styling="' . $config_arr['slider_button_styling'] . '" data-autorotate="' . $config_arr['autorotate'] . '" data-parallax="' . $config_arr['parallax'] . '" data-parallax-disable-mobile="' . $config_arr['disable_parallax_mobile'] . '" data-caption-trans="' . $config_arr['caption_transition'] . '" data-parallax-style="bg_only" data-bg-animation="' . $config_arr['bg_animation'] . '" data-full-width="' . $fullwidth . '" ';
			$slider .= 'class="nectar-slider-wrap ' . $first_section . '" id="' . $nectar_slider_unique_id . '" ' . $dynamic_height_style_markup . '>';
			$slider .= '<div class="swiper-container" ' . $dynamic_height_style_markup . ' ' . $text_overrides . ' data-loop="' . $config_arr['loop'] . '" data-height="' . $config_arr['slider_height'] . '" data-min-height="' . $config_arr['min_slider_height'] . '" data-arrows="' . $config_arr['arrow_navigation'] . '" data-bullets="' . $config_arr['bullet_navigation'] . '" ';
			$slider .= 'data-bullet_style="' . $config_arr['bullet_navigation_style'] . '" data-bullet_position="' . $config_arr['bullet_navigation_position'] . '" data-desktop-swipe="' . $config_arr['desktop_swipe'] . '" data-settings=""> <div class="swiper-wrapper">';

					  $slide_count = 0;

					  // get slider location by slug instead of raw name
					  $slider_terms = get_term_by( 'name', $config_arr['location'], 'slider-locations' );

					// loop through and get all the slides in selected location
					$slides = new WP_Query(
						array(
							'post_type'      => 'nectar_slider',
							'tax_query'      => array(
								array(
									'taxonomy' => 'slider-locations',
									'field'    => 'slug',
									'terms'    => $slider_terms->slug,
								),
							),
							'posts_per_page' => -1,
							'order'          => 'ASC',
							'orderby'        => 'menu_order',
						)
					);

		if ( $slides->have_posts() ) :
			while ( $slides->have_posts() ) :
				$slides->the_post();

						global $post;

						$background_type      = get_post_meta( $post->ID, '_nectar_slider_bg_type', true );
						$background_alignment = get_post_meta( $post->ID, '_nectar_slider_slide_bg_alignment', true );

						$slide_title = get_post_meta( $post->ID, '_nectar_slider_heading', true );

						$slide_description         = get_post_meta( $post->ID, '_nectar_slider_caption', true );
						$slide_description_wrapped = '<span>' . $slide_description . '</span>';
						$slide_description_bg      = get_post_meta( $post->ID, '_nectar_slider_caption_background', true );
						$caption_bg                = ( $slide_description_bg == 'on' ) ? 'class="transparent-bg"' : '';

						$down_arrow = get_post_meta( $post->ID, '_nectar_slider_down_arrow', true );

						$poster        = get_post_meta( $post->ID, '_nectar_slider_preview_image', true );
						$poster_markup = ( ! empty( $poster ) ) ? 'poster="' . $poster . '"' : null;

						$x_pos = get_post_meta( $post->ID, '_nectar_slide_xpos_alignment', true );
					  $y_pos   = get_post_meta( $post->ID, '_nectar_slide_ypos_alignment', true );

						$link_type = get_post_meta( $post->ID, '_nectar_slider_link_type', true );

						$full_slide_link = get_post_meta( $post->ID, '_nectar_slider_entire_link', true );

						$button_1_text  = get_post_meta( $post->ID, '_nectar_slider_button', true );
						$button_1_link  = get_post_meta( $post->ID, '_nectar_slider_button_url', true );
						$button_1_style = get_post_meta( $post->ID, '_nectar_slider_button_style', true );
						$button_1_color = get_post_meta( $post->ID, '_nectar_slider_button_color', true );

						$button_2_text  = get_post_meta( $post->ID, '_nectar_slider_button_2', true );
						$button_2_link  = get_post_meta( $post->ID, '_nectar_slider_button_url_2', true );
						$button_2_style = get_post_meta( $post->ID, '_nectar_slider_button_style_2', true );
						$button_2_color = get_post_meta( $post->ID, '_nectar_slider_button_color_2', true );

					  $video_mp4       = get_post_meta( $post->ID, '_nectar_media_upload_mp4', true );
						$video_webm    = get_post_meta( $post->ID, '_nectar_media_upload_webm', true );
						$video_ogv     = get_post_meta( $post->ID, '_nectar_media_upload_ogv', true );
						$video_texture = get_post_meta( $post->ID, '_nectar_slider_video_texture', true );
						  $muted       = 'on'; // get_post_meta($post->ID, '_nectar_slider_video_muted', true);

						  $desktop_content_width = get_post_meta( $post->ID, '_nectar_slider_slide_content_width_desktop', true );
						$tablet_content_width    = get_post_meta( $post->ID, '_nectar_slider_slide_content_width_tablet', true );

						$slide_image     = get_post_meta( $post->ID, '_nectar_slider_image', true );
						  $overlay_color = get_post_meta( $post->ID, '_nectar_slider_bg_overlay_color', true );

						  $overlay_markup = ( ! empty( $overlay_color ) ) ? '<div class="slide-bg-overlay" style="background-color: ' . $overlay_color . ';"> &nbsp; </div>' : '';
						$img_bg           = null;

						$slide_color = get_post_meta( $post->ID, '_nectar_slider_slide_font_color', true );

						$custom_class     = get_post_meta( $post->ID, '_nectar_slider_slide_custom_class', true );
						$custom_css_class = ( ! empty( $custom_class ) ) ? ' ' . $custom_class : null;

				if ( $background_type == 'image_bg' ) {
					$bg_img_markup = 'style="background-image: url(' . nectar_ssl_check( $slide_image ) . ');"';
				} else {
					$bg_img_markup = null;}

				( ! empty( $x_pos )) ? $x_pos_markup  = $x_pos : $x_pos_markup = 'center';
				( ! empty( $y_pos ) ) ? $y_pos_markup = $y_pos : $y_pos_markup = 'middle';

				$slider .= '<div class="swiper-slide' . $custom_css_class . '" data-desktop-content-width="' . $desktop_content_width . '" data-tablet-content-width="' . $tablet_content_width . '" data-bg-alignment="' . $background_alignment . '" data-color-scheme="' . $slide_color . '" data-x-pos="' . $x_pos_markup . '" data-y-pos="' . $y_pos_markup . '" ' . $dynamic_height_style_markup . '> 
								';

				if ( $background_type == 'image_bg' ) {
					$slider .= '<div class="slide-bg-wrap"><div class="image-bg" ' . $bg_img_markup . '> &nbsp; </div>' . $overlay_markup . '</div>';
				}

				if ( ! empty( $slide_title ) || ! empty( $slide_description ) || ! empty( $button_1_text ) || ! empty( $button_2_text ) ) {

					   $slider .= '<div class="container">
									<div class="content">';

					if ( ! empty( $slide_title ) ) {
						$slider .= '<h2>' . $slide_title . '</h2>'; }
					if ( ! empty( $slide_description ) ) {
						$slider .= '<p ' . $caption_bg . ' >' . $slide_description_wrapped . '</p>'; }

					if ( $link_type == 'button_links' && ! empty( $button_1_text ) || $link_type == 'button_links' && ! empty( $button_2_text ) ) {
						$slider .= '<div class="buttons">';

						if ( ! empty( $button_1_text ) ) {

						  $button_1_link = ! empty( $button_1_link ) ? $button_1_link : '#';

						  // check button link to see if it's a video or googlemap
						  $link_extra = null;

							if ( strpos( $button_1_link, 'youtube.com/watch' ) !== false ) {
								$link_extra = 'pp ';
							}
							if ( strpos( $button_1_link, 'vimeo.com/' ) !== false ) {
								$link_extra = 'pp ';
							}
							if ( strpos( $button_1_link, 'maps.google.com/maps' ) !== false ) {
								$link_extra = 'map-popup ';
							}

						   // wrapper for tilt button
						   $button_wrap_begin = ( $button_1_style == 'solid_color_2' ) ? "<div class='button-wrap'>" : null;
						   $button_wrap_end   = ( $button_1_style == 'solid_color_2' ) ? '</div>' : null;

						   $slider .=
						   '<div class="button ' . $button_1_style . '">
							 		' . $button_wrap_begin . ' <a class="' . $link_extra . $button_1_color . '" href="' . $button_1_link . '">' . $button_1_text . '</a>' . $button_wrap_end . '
							 	 </div>';
						}

						if ( ! empty( $button_2_text ) ) {

						   $button_2_link = ! empty( $button_2_link ) ? $button_2_link : '#';

						   // check button link to see if it's a video or googlemap
						   $link_extra = null;

							if ( strpos( $button_2_link, 'youtube.com/watch' ) !== false ) {
													   $link_extra = 'pp ';
							}
							if ( strpos( $button_2_link, 'vimeo.com/' ) !== false ) {
								$link_extra = 'pp ';
							}
							if ( strpos( $button_2_link, 'maps.google.com/maps' ) !== false ) {
								$link_extra = 'map-popup ';
							}

								$slider .=
								'<div class="button ' . $button_2_style . '">
					 		 <a class="' . $link_extra . $button_2_color . '" href="' . $button_2_link . '">' . $button_2_text . '</a>
					 	 </div>';
						 
						}

						$slider .= '</div>';
						
					}

					$slider .= '</div>
				</div><!--/container-->';

				}

				if ( ! empty( $down_arrow ) && $down_arrow == 'on' ) {

					$header_down_arrow_style = ( ! empty( $nectar_options['header-down-arrow-style'] ) ) ? $nectar_options['header-down-arrow-style'] : 'default';
					$theme_button_styling    = ( ! empty( $nectar_options['button-styling'] ) ) ? $nectar_options['button-styling'] : 'default';

					if ( $header_down_arrow_style == 'scroll-animation' || $theme_button_styling == 'slightly_rounded' || $theme_button_styling == 'slightly_rounded_shadow' ) {
						$slider .= '<a href="#" class="slider-down-arrow no-border"><svg class="nectar-scroll-icon" viewBox="0 0 30 45" enable-background="new 0 0 30 45">
					                			<path class="nectar-scroll-icon-path" fill="none" stroke="#ffffff" stroke-width="2" stroke-miterlimit="10" d="M15,1.118c12.352,0,13.967,12.88,13.967,12.88v18.76  c0,0-1.514,11.204-13.967,11.204S0.931,32.966,0.931,32.966V14.05C0.931,14.05,2.648,1.118,15,1.118z"></path>
					            			  </svg></a>';
					} else {

						$slider .= '<a href="#" class="slider-down-arrow"><i class="icon-salient-down-arrow icon-default-style"> <span class="ie-fix"></span> </i></a>';
					}
				}

				$active_texture = ( $video_texture == 'on' ) ? 'active_texture' : '';
				$slider        .= '<div class="video-texture ' . $active_texture . '"> <span class="ie-fix"></span> </div>';

				if ( $background_type == 'video_bg' ) {

						 $muted_markup = '';

					if ( $muted == 'on' ) {
						$muted_markup = ' autoplay muted playsinline';
					}
					$slider .= '
									<div class="mobile-video-image" style="background-image: url(' . $poster . ')"> <span class="ie-fix"></span>  </div>
									<div class="slide-bg-wrap">
									  <div class="video-wrap">
										
										
										<video class="slider-video" width="1800" height="700" preload="auto" loop' . $muted_markup . '>';

					if ( ! empty( $video_webm ) ) {
						  $slider .= '<source type="video/webm" src="' . $video_webm . '">'; }
					if ( ! empty( $video_mp4 ) ) {
						$slider .= '<source type="video/mp4" src="' . $video_mp4 . '">'; }
					if ( ! empty( $video_ogv ) ) {
						$slider .= '<source type="video/ogg" src="' . $video_ogv . '">'; }

													$slider .= '</video></div> ' . $overlay_markup . '</div>';

				}

				if ( $link_type == 'full_slide_link' && ! empty( $full_slide_link ) ) {
					$slider .= '<a href="' . $full_slide_link . '" class="entire-slide-link"> <span class="ie-fix"></span> </a>';
				}

					$slider .= '</div> <!--/swiper-slide-->';

					$slide_count ++;

					endwhile;
endif;

					wp_reset_postdata();

				   $slider .= '</div>';

		if ( $config_arr['arrow_navigation'] == 'true' && $slide_count > 1 && $config_arr['slider_button_styling'] != 'btn_with_preview' && $config_arr['overall_style'] != 'directional' ) {

			$slider .= '<a href="" class="slider-prev"><i class="icon-salient-left-arrow"></i> <div class="slide-count"> <span class="slide-current">1</span> <i class="icon-salient-right-line"></i> <span class="slide-total"></span> </div> </a>
				     		<a href="" class="slider-next"><i class="icon-salient-right-arrow"></i> <div class="slide-count"> <span class="slide-current">1</span> <i class="icon-salient-right-line"></i> <span class="slide-total"></span> </div> </a>';
		} elseif ( $config_arr['arrow_navigation'] == 'true' && $slide_count > 1 && $config_arr['slider_button_styling'] == 'btn_with_preview' || $config_arr['overall_style'] == 'directional' ) {
			$slider .= '<a href="" class="slider-prev"><i class="icon-angle-left"></i> </a>
				     		<a href="" class="slider-next"><i class="icon-angle-right"></i> </a>';
		}

		if ( $config_arr['bullet_navigation'] == 'true' && $slide_count > 1 ) {
			$slider .= '<div class="container normal-container slider-pagination-wrap"><div class="slider-pagination"></div></div>';
		}

				$loading_animation    = ( ! empty( $nectar_options['loading-image-animation'] ) && ! empty( $nectar_options['loading-image'] ) ) ? $nectar_options['loading-image-animation'] : null;
				$default_loader       = ( empty( $nectar_options['loading-image'] ) && ! empty( $nectar_options['theme-skin'] ) && $nectar_options['theme-skin'] == 'ascend' ) ? '<span class="default-loading-icon spin"></span>' : null;
				$default_loader_class = ( empty( $nectar_options['loading-image'] ) && ! empty( $nectar_options['theme-skin'] ) && $nectar_options['theme-skin'] == 'ascend' ) ? 'default-loader' : null;
				$slider              .= '<div class="nectar-slider-loading ' . $default_loader_class . '"> <span class="loading-icon ' . $loading_animation . '"> ' . $default_loader . '  </span> </div> </div> 
				
			</div>';

		if ( $config_arr['parallax'] == 'true' ) {
			$slider .= '</div>'; }

		return $slider;

	}
}
