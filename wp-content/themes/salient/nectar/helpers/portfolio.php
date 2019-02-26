<?php
/**
 * Salient portfolio related functions
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



// -----------------------------------------------------------------#
// Create admin portfolio section
// -----------------------------------------------------------------#
function nectar_portfolio_register() {

	 $portfolio_labels = array(
		 'name'          => __( 'Portfolio', 'salient' ),
		 'singular_name' => __( 'Portfolio Item', 'salient' ),
		 'search_items'  => __( 'Search Portfolio Items', 'salient' ),
		 'all_items'     => __( 'Portfolio', 'salient' ),
		 'parent_item'   => __( 'Parent Portfolio Item', 'salient' ),
		 'edit_item'     => __( 'Edit Portfolio Item', 'salient' ),
		 'update_item'   => __( 'Update Portfolio Item', 'salient' ),
		 'add_new_item'  => __( 'Add New Portfolio Item', 'salient' ),
	 );

	global $nectar_options;
	$custom_slug = null;

	if ( ! empty( $nectar_options['portfolio_rewrite_slug'] ) ) {
		$custom_slug = $nectar_options['portfolio_rewrite_slug'];
	}

	 $portolfio_menu_icon = ( floatval( get_bloginfo( 'version' ) ) >= '3.8' ) ? 'dashicons-art' : NECTAR_FRAMEWORK_DIRECTORY . 'assets/img/icons/portfolio.png';

	 $args = array(
		 'labels'             => $portfolio_labels,
		 'rewrite'            => array(
			 'slug'       => $custom_slug,
			 'with_front' => false,
		 ),
		 'singular_label'     => esc_html__( 'Project', 'salient' ),
		 'public'             => true,
		 'publicly_queryable' => true,
		 'show_ui'            => true,
		 'hierarchical'       => false,
		 'menu_position'      => 9,
		 'menu_icon'          => $portolfio_menu_icon,
		 'supports'           => array( 'title', 'editor', 'thumbnail', 'comments', 'revisions' ),
	 );

	register_post_type( 'portfolio', $args );
}
add_action( 'init', 'nectar_portfolio_register', 0 );





// -----------------------------------------------------------------#
// Add taxonomys attached to portfolio
// -----------------------------------------------------------------#
if ( ! function_exists( 'nectar_add_portfolio_taxonomies' ) ) {

	function nectar_add_portfolio_taxonomies() {

		$category_labels = array(
			'name'          => __( 'Project Categories', 'salient' ),
			'singular_name' => __( 'Project Category', 'salient' ),
			'search_items'  => __( 'Search Project Categories', 'salient' ),
			'all_items'     => __( 'All Project Categories', 'salient' ),
			'parent_item'   => __( 'Parent Project Category', 'salient' ),
			'edit_item'     => __( 'Edit Project Category', 'salient' ),
			'update_item'   => __( 'Update Project Category', 'salient' ),
			'add_new_item'  => __( 'Add New Project Category', 'salient' ),
			'menu_name'     => __( 'Project Categories', 'salient' ),
		);

		register_taxonomy(
			'project-type',
			array( 'portfolio' ),
			array(
				'hierarchical' => true,
				'labels'       => $category_labels,
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array( 'slug' => 'project-type' ),
			)
		);

		$attributes_labels = array(
			'name'          => __( 'Project Attributes', 'salient' ),
			'singular_name' => __( 'Project Attribute', 'salient' ),
			'search_items'  => __( 'Search Project Attributes', 'salient' ),
			'all_items'     => __( 'All Project Attributes', 'salient' ),
			'parent_item'   => __( 'Parent Project Attribute', 'salient' ),
			'edit_item'     => __( 'Edit Project Attribute', 'salient' ),
			'update_item'   => __( 'Update Project Attribute', 'salient' ),
			'add_new_item'  => __( 'Add New Project Attribute', 'salient' ),
			'new_item_name' => __( 'New Project Attribute', 'salient' ),
			'menu_name'     => __( 'Project Attributes', 'salient' ),
		);

		register_taxonomy(
			'project-attributes',
			array( 'portfolio' ),
			array(
				'hierarchical' => true,
				'labels'       => $attributes_labels,
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array( 'slug' => 'project-attributes' ),
			)
		);

	}
}

nectar_add_portfolio_taxonomies();




// -----------------------------------------------------------------#
// Function to find page that contains string
// -----------------------------------------------------------------#
if ( ! function_exists( 'get_page_by_title_search' ) ) {
	function get_page_by_title_search( $string ) {
		global $wpdb;

		$string = sanitize_text_field( $string );
		$title  = esc_sql( $string );
		if ( ! $title ) {
			return;
		}
		$page = $wpdb->get_results(
			"
	        SELECT * 
	        FROM $wpdb->posts
	        WHERE post_title LIKE '%$title%'
	        AND post_type = 'page' 
	        AND post_status = 'publish'
	        LIMIT 1
	    "
		);
		return $page;
	}
}




// -----------------------------------------------------------------#
// Portfolio single page controls
// -----------------------------------------------------------------#
if ( ! function_exists( 'project_single_control' ) ) {

	function project_single_controls() {

			global $nectar_options;
			global $post;

			$back_to_all_override = get_post_meta( $post->ID, 'nectar-metabox-portfolio-parent-override', true );
		if ( empty( $back_to_all_override ) ) {
			$back_to_all_override = 'default';
		}

			// attempt to find parent portfolio page - if unsuccessful default to main portfolio page
			$terms          = get_the_terms( $post->id, 'project-type' );
			$project_cat    = null;
			$portfolio_link = null;
			$single_nav_pos = ( ! empty( $nectar_options['portfolio_single_nav'] ) ) ? $nectar_options['portfolio_single_nav'] : 'in_header';

		if ( empty( $terms ) ) {
			$terms = array(
				'1' => (object) array(
					'name' => 'nothing',
					'slug' => 'none',
				),
			);
		}

		foreach ( $terms as $term ) {
			$project_cat = strtolower( $term->name );
		}

			 $page = get_page_by_title_search( $project_cat );
		if ( empty( $page ) ) {
			$page = array( '0' => (object) array( 'ID' => 'nothing' ) );
		}

			 $page_link = verify_portfolio_page( $page[0]->ID );

			 // if a page has been found for the category
		if ( ! empty( $page_link ) && $back_to_all_override == 'default' && $single_nav_pos != 'after_project_2' ) {
			$portfolio_link = $page_link;

			?>
				 
				 <div id="portfolio-nav">
					<?php if ( $single_nav_pos != 'after_project_2' ) { ?>
						 <ul>
							 <li id="all-items"><a href="<?php echo esc_url( $portfolio_link ); ?>"><i class="icon-salient-back-to-all"></i></a></li>               
						 </ul>
					<?php } ?>
					<ul class="controls">                                 
				   <?php if ( $single_nav_pos == 'after_project' ) { ?>

							<li id="prev-link"><?php be_next_post_link( '%link', '<i class="icon-angle-left"></i> <span>' . esc_html__( 'Previous Project', 'salient' ) . '</span>', true, null, 'project-type' ); ?></li>
							<li id="next-link"><?php be_previous_post_link( '%link', '<span>' . esc_html__( 'Next Project', 'salient' ) . '</span><i class="icon-angle-right"></i>', true, null, 'project-type' ); ?></li> 
					
						<?php } else { ?>

							<li id="prev-link"><?php be_next_post_link( '%link', '<i class="icon-salient-left-arrow-thin"></i>', true, null, 'project-type' ); ?></li>
							<li id="next-link"><?php be_previous_post_link( '%link', '<i class="icon-salient-right-arrow-thin"></i>', true, null, 'project-type' ); ?></li> 

						<?php } ?>
						
					</ul>
				</div>
				 
			<?php
		}

			 // if no category page exists
		else {

			$portfolio_link = get_portfolio_page_link( get_the_ID() );
			if ( ! empty( $nectar_options['main-portfolio-link'] ) ) {
				$portfolio_link = $nectar_options['main-portfolio-link'];
			}

			if ( $back_to_all_override != 'default' ) {
				$portfolio_link = get_page_link( $back_to_all_override );
			}

			?>
				<div id="portfolio-nav">
					<?php if ( $single_nav_pos != 'after_project_2' ) { ?>
						<ul>
							<li id="all-items"><a href="<?php echo esc_url( $portfolio_link ); ?>" title="<?php echo esc_attr__( 'Back to all projects', 'salient' ); ?>"><i class="icon-salient-back-to-all"></i></a></li>  
						</ul>
					<?php } ?>

					<ul class="controls">    
				   <?php
					if ( ! empty( $nectar_options['portfolio_same_category_single_nav'] ) && $nectar_options['portfolio_same_category_single_nav'] == '1' ) {

							// get_posts in same custom taxonomy
							$terms       = get_the_terms( $post->id, 'project-type' );
							$project_cat = null;

						if ( empty( $terms ) ) {
							$terms = array(
								'1' => (object) array(
									'name' => 'nothing',
									'slug' => 'none',
								),
							);
						}

						foreach ( $terms as $term ) {
							$project_cat = strtolower( $term->slug );
						}

							$postlist_args = array(
								'posts_per_page' => -1,
								'orderby'        => 'menu_order title',
								'order'          => 'ASC',
								'post_type'      => 'portfolio',
								'project-type'   => $project_cat,
							);
							$postlist      = get_posts( $postlist_args );

							// get ids of posts retrieved from get_posts
							$ids = array();
						foreach ( $postlist as $thepost ) {
							$ids[] = $thepost->ID;
						}

							// get and echo previous and next post in the same taxonomy
							$thisindex = array_search( $post->ID, $ids );

							$previd = ( isset( $ids[ $thisindex - 1 ] ) ) ? $ids[ $thisindex - 1 ] : null;
							$nextid = ( isset( $ids[ $thisindex + 1 ] ) ) ? $ids[ $thisindex + 1 ] : null;
						if ( ! empty( $previd ) ) {
							if ( $single_nav_pos == 'after_project' ) {
								echo '<li id="prev-link" class="from-sing"><a href="' . esc_url( get_permalink( $previd ) ) . '"><i class="icon-angle-left"></i><span>' . esc_html__( 'Previous Project', 'salient' ) . '</span></a></li>';

							} elseif ( $single_nav_pos == 'after_project_2' ) {

								$hidden_class = ( empty( $previd ) ) ? 'hidden' : null;
								$only_class   = ( empty( $nextid ) ) ? ' only' : null;
								echo '<li class="previous-project ' . $hidden_class . $only_class . '">';

								if ( ! empty( $previd ) ) {
									$previous_post_id = $previd;
									$bg               = get_post_meta( $previous_post_id, '_nectar_header_bg', true );

									if ( ! empty( $bg ) ) {
										// page header
										echo '<div class="proj-bg-img" style="background-image: url(' . $bg . ');"></div>';
									} elseif ( has_post_thumbnail( $previous_post_id ) ) {
										// featured image
										$post_thumbnail_id  = get_post_thumbnail_id( $previous_post_id );
										$post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
										echo '<div class="proj-bg-img" style="background-image: url(' . $post_thumbnail_url . ');"></div>';
									}

									echo '<a href="' . esc_url( get_permalink( $previous_post_id ) ) . '"></a><h3><span>' . esc_html__( 'Previous Project', 'salient' ) . '</span><span class="text">' . get_the_title( $previous_post_id ) . '
						<svg class="next-arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 39 12"><line class="top" x1="23" y1="-0.5" x2="29.5" y2="6.5" stroke="#ffffff;"></line><line class="bottom" x1="23" y1="12.5" x2="29.5" y2="5.5" stroke="#ffffff;"></line></svg><span class="line"></span></span></h3>';
								}
								echo '</li>';

							} else {
								echo '<li id="prev-link" class="from-sing"><a href="' . esc_url( get_permalink( $previd ) ) . '"><i class="icon-salient-left-arrow-thin"></i></a></li>';
							}
						}
						if ( ! empty( $nextid ) ) {

							if ( $single_nav_pos == 'after_project' ) {
								  echo '<li id="next-link" class="from-sing"><a href="' . esc_url( get_permalink( $nextid ) ) . '"><span>' . esc_html__( 'Next Project', 'salient' ) . '</span><i class="icon-angle-right"></i></a></li>';

							} elseif ( $single_nav_pos == 'after_project_2' ) {

								$hidden_class = ( empty( $nextid ) ) ? 'hidden' : null;
								$only_class   = ( empty( $previd ) ) ? ' only' : null;

								echo '<li class="next-project ' . $hidden_class . $only_class . '">';

								if ( ! empty( $nextid ) ) {
									$next_post_id = $nextid;
									$bg           = get_post_meta( $next_post_id, '_nectar_header_bg', true );

									if ( ! empty( $bg ) ) {
										// page header
										echo '<div class="proj-bg-img" style="background-image: url(' . $bg . ');"></div>';
									} elseif ( has_post_thumbnail( $next_post_id ) ) {
										// featured image
										$post_thumbnail_id  = get_post_thumbnail_id( $next_post_id );
										$post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
										echo '<div class="proj-bg-img" style="background-image: url(' . $post_thumbnail_url . ');"></div>';
									}
								}

									echo '<a href="' . esc_url( get_permalink( $next_post_id ) ) . '"></a><h3><span>' . esc_html__( 'Next Project', 'salient' ) . '</span><span class="text">' . get_the_title( $next_post_id ) . '
							<svg class="next-arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 39 12"><line class="top" x1="23" y1="-0.5" x2="29.5" y2="6.5" stroke="#ffffff;"></line><line class="bottom" x1="23" y1="12.5" x2="29.5" y2="5.5" stroke="#ffffff;"></line></svg><span class="line"></span></span></h3>';

								echo '</li>';

							} else {
								echo '<li id="next-link" class="from-sing"><a href="' . esc_url( get_permalink( $nextid ) ) . '"><i class="icon-salient-right-arrow-thin"></i></a></li>';
							}
						}
					} else {
						?>
								<?php if ( $single_nav_pos == 'after_project' ) { ?>
								<li id="prev-link"><?php next_post_link( '%link', '<i class="icon-angle-left"></i><span>' . esc_html__( 'Previous Project', 'salient' ) . '</span>' ); ?></li>
								<li id="next-link"><?php previous_post_link( '%link', '<span>' . esc_html__( 'Next Project', 'salient' ) . '</span><i class="icon-angle-right"></i>' ); ?></li> 
									<?php
} elseif ( $single_nav_pos == 'after_project_2' ) {

	$previous_post = get_next_post();
	$next_post     = get_previous_post();

	$hidden_class = ( empty( $previous_post ) ) ? 'hidden' : null;
	$only_class   = ( empty( $next_post ) ) ? ' only' : null;
	echo '<li class="previous-project ' . $hidden_class . $only_class . '">';

	if ( ! empty( $previous_post ) ) {
		$previous_post_id = $previous_post->ID;
		$bg               = get_post_meta( $previous_post_id, '_nectar_header_bg', true );

		if ( ! empty( $bg ) ) {
			// page header
			echo '<div class="proj-bg-img" style="background-image: url(' . $bg . ');"></div>';
		} elseif ( has_post_thumbnail( $previous_post_id ) ) {
			// featured image
			$post_thumbnail_id  = get_post_thumbnail_id( $previous_post_id );
			$post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
			echo '<div class="proj-bg-img" style="background-image: url(' . $post_thumbnail_url . ');"></div>';
		}

			echo '<a href="' . esc_url( get_permalink( $previous_post_id ) ) . '"></a><h3><span>' . esc_html__( 'Previous Project', 'salient' ) . '</span><span class="text">' . $previous_post->post_title . '
						<svg class="next-arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 39 12"><line class="top" x1="23" y1="-0.5" x2="29.5" y2="6.5" stroke="#ffffff;"></line><line class="bottom" x1="23" y1="12.5" x2="29.5" y2="5.5" stroke="#ffffff;"></line></svg><span class="line"></span></span></h3>';
	}
	echo '</li>';

	$hidden_class = ( empty( $next_post ) ) ? 'hidden' : null;
	$only_class   = ( empty( $previous_post ) ) ? ' only' : null;

	echo '<li class="next-project ' . $hidden_class . $only_class . '">';

	if ( ! empty( $next_post ) ) {
								$next_post_id = $next_post->ID;
								$bg           = get_post_meta( $next_post_id, '_nectar_header_bg', true );

		if ( ! empty( $bg ) ) {
			// page header
			echo '<div class="proj-bg-img" style="background-image: url(' . $bg . ');"></div>';
		} elseif ( has_post_thumbnail( $next_post_id ) ) {
			// featured image
			$post_thumbnail_id  = get_post_thumbnail_id( $next_post_id );
			$post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
			echo '<div class="proj-bg-img" style="background-image: url(' . $post_thumbnail_url . ');"></div>';
		}
	}

		echo '<a href="' . esc_url( get_permalink( $next_post_id ) ) . '"></a><h3><span>' . esc_html__( 'Next Project', 'salient' ) . '</span><span class="text">' . $next_post->post_title . '
						<svg class="next-arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 39 12"><line class="top" x1="23" y1="-0.5" x2="29.5" y2="6.5" stroke="#ffffff;"></line><line class="bottom" x1="23" y1="12.5" x2="29.5" y2="5.5" stroke="#ffffff;"></line></svg><span class="line"></span></span></h3>';

								echo '</li>';

} else {
	?>
							 
								<li id="prev-link"><?php next_post_link( '%link', '<i class="icon-salient-left-arrow-thin"></i>' ); ?>
																		 <?php
																			if ( $single_nav_pos == 'after_project' ) {
																				echo esc_html__( 'Previous Project', 'salient' );}
																			?>
									</li>
								<li id="next-link">
								<?php
								if ( $single_nav_pos == 'after_project' ) {
									echo esc_html__( 'Next Project', 'salient' );}
								?>
									<?php previous_post_link( '%link', '<i class="icon-salient-right-arrow-thin"></i>' ); ?></li> 
							<?php } 

							 } ?>                                   
					</ul>
				</div>
			<?php
		}
	}
}




// -----------------------------------------------------------------#
// Portfolio Exclude External / Custom Grid Content Projects From Next/Prev
// -----------------------------------------------------------------#
if ( ! is_admin() ) {

	add_filter( 'get_previous_post_where', 'so16495117_mod_adjacent_bis' );
	add_filter( 'get_next_post_where', 'so16495117_mod_adjacent_bis' );

	function so16495117_mod_adjacent_bis( $where ) {
		global $wpdb;
			global $post;

			// if not on project exit early
		if ( ! is_singular( 'portfolio' ) ) {
			return $where; }

			$excluded_projects        = array();
			$exlcuded_projects_string = '';

			$portfolio = array(
				'post_type'      => 'portfolio',
				'posts_per_page' => '-1',
			);
			$the_query = new WP_Query( $portfolio );

		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$custom_project_link    = get_post_meta( $post->ID, '_nectar_external_project_url', true );
				$custom_content_project = get_post_meta( $post->ID, '_nectar_portfolio_custom_grid_item', true );

				if ( ! empty( $custom_project_link ) || ! empty( $custom_content_project ) && $custom_content_project == 'on' ) {
					$excluded_projects[] = $post->ID;
				}
			}

			$exlcuded_projects_string = implode( ',', $excluded_projects );

			wp_reset_postdata();

			if ( ! empty( $exlcuded_projects_string ) ) {
				return $where . " AND p.ID NOT IN ($exlcuded_projects_string)";
			} else {
				return $where;
			}
		}

	}
}




// -----------------------------------------------------------------#
// New category walker for portfolio filter
// -----------------------------------------------------------------#
class Walker_Portfolio_Filter extends Walker_Category {

	function start_el( &$output, $category, $depth = 0, $args = array(), $current_object_id = 0 ) {

		extract( $args );
		$cat_slug = esc_attr( $category->slug );
		$cat_slug = apply_filters( 'list_cats', $cat_slug, $category );

		$link = '<li><a href="#" data-filter=".' . strtolower( preg_replace( '/\s+/', '-', $cat_slug ) ) . '">';

		$cat_name = esc_attr( $category->name );
		$cat_name = apply_filters( 'list_cats', $cat_name, $category );

		$link .= $cat_name;

		if ( ! empty( $category->description ) ) {
			$link .= ' <span>' . $category->description . '</span>';
		}

		$link .= '</a>';

		$output .= $link;

	}
}


// -----------------------------------------------------------------#
// Function to get the page link back to all portfolio items
// -----------------------------------------------------------------#
if ( ! function_exists( 'get_portfolio_page_link' ) ) {
	function get_portfolio_page_link( $post_id ) {
		global $wpdb;

		$post_id = sanitize_text_field( $post_id );

		$results = $wpdb->get_results(
			"SELECT post_id FROM $wpdb->postmeta
	    WHERE meta_key='_wp_page_template' AND meta_value='template-portfolio.php'"
		);

		// safety net
		$page_id = null;

		foreach ( $results as $result ) {
			$page_id = $result->post_id;
		}

		return get_page_link( $page_id );
	}
}


// -----------------------------------------------------------------#
// Function to get verify that the page has the portfolio layout assigned
// -----------------------------------------------------------------#
if ( ! function_exists( 'verify_portfolio_page' ) ) {
	function verify_portfolio_page( $post_id ) {
		global $wpdb;

		$post_id = sanitize_text_field( $post_id );

		$result = $wpdb->get_results(
			"SELECT post_id FROM $wpdb->postmeta
	    WHERE meta_key='_wp_page_template' AND meta_value='template-portfolio.php' AND post_id='$post_id' LIMIT 1"
		);

		if ( ! empty( $result ) ) {
			return get_page_link( $result[0]->post_id );
		} else {
			return null;
		}
	}
}





/* portfolio video lightbox link helper */
if ( ! function_exists( 'nectar_portfolio_video_popup_link' ) ) {

	function nectar_portfolio_video_popup_link( $post, $project_style, $video_embed, $video_m4v ) {

		$project_video_src  = null;
		$project_video_link = null;
		$video_markup       = null;

		if ( $video_embed ) {

			$project_video_src = $video_embed;

			if ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $project_video_src, $video_match ) ) {

				// youtube
				$project_video_link = 'https://www.youtube.com/watch?v=' . $video_match[1];

			} elseif ( preg_match( '/player\.vimeo\.com\/video\/([0-9]*)/', $project_video_src, $video_match ) ) {

				// vimeo iframe
				$project_video_link = 'https://vimeo.com/' . $video_match[1] . '?iframe=true';

			} elseif ( preg_match( '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([‌​0-9]{6,11})[?]?.*/', $project_video_src, $video_match ) ) {

				// reg vimeo
				$project_video_link = 'https://vimeo.com/' . $video_match[5] . '?iframe=true';

			}
		} elseif ( $video_m4v ) {

			$project_video_src  = $video_m4v;
			$project_video_link = '#video-popup-' . $post->ID;

			$video_output = '[video preload="none" ';

			if ( ! empty( $video_m4v ) ) {
				$video_output .= 'mp4="' . $video_m4v . '" '; }

			$video_output .= ']';

			$video_markup = '<div id="video-popup-' . $post->ID . '" class="mfp-figure mfp-with-anim mfp-iframe-scaler"><div class="video">' . do_shortcode( $video_output ) . '</div></div>';

		}

		$popup_link_text = ( $project_style == '1' ) ? esc_html__( 'Watch Video', 'salient' ) : '';

		 return $video_markup . '<a href="' . $project_video_link . '" class="pretty_photo default-link" >' . $popup_link_text . '</a>';
	}
}

