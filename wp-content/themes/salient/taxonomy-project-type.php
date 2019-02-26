<?php get_header();

$options = get_nectar_theme_options();

// calculate cols
if ( ! empty( $options['main_portfolio_layout'] ) ) {

	switch ( $options['main_portfolio_layout'] ) {
		case '2':
			$cols = 'cols-2';
			break;
		case '3':
			$cols = 'cols-3';
			break;
		case '4':
			$cols = 'cols-4';
			break;
		case 'fullwidth':
			$cols = 'elastic';
			break;
	}
} else {
	$cols = 'cols-3';
}

if ( ! empty( $cols ) ) {

	switch ( $cols ) {
		case 'cols-2':
			$span_num = 'span_6';
			break;
		case 'cols-3':
			$span_num = 'span_4';
			break;
		case 'cols-4':
			$span_num = 'span_3';
			break;
		case 'elastic':
			$span_num = 'elastic-portfolio-item';
			break;

	}
}

$project_style       = ( ! empty( $options['main_portfolio_project_style'] ) ) ? $options['main_portfolio_project_style'] : '1';
$item_spacing        = ( ! empty( $options['main_portfolio_item_spacing'] ) ) ? $options['main_portfolio_item_spacing'] : 'default';
$masonry_layout      = ( ! empty( $options['portfolio_use_masonry'] ) && $options['portfolio_use_masonry'] == '1' ) ? 'true' : 'false';
$masonry_sizing_type = ( ! empty( $options['portfolio_masonry_grid_sizing'] ) && $options['portfolio_masonry_grid_sizing'] == 'photography' ) ? 'photography' : 'default';
$load_in_animation   = ( ! empty( $options['portfolio_loading_animation'] ) ) ? $options['portfolio_loading_animation'] : 'none';

$infinite_scroll_class = ( ! empty( $options['portfolio_pagination_type'] ) && $options['portfolio_pagination_type'] == 'infinite_scroll' ) ? ' infinite_scroll' : null;

// disable masonry for default project style fullwidtrh
if ( $project_style == '1' && $cols == 'elastic' ) {
	$masonry_layout = 'false';
}

$display_sortable = get_post_meta( $post->ID, 'nectar-metabox-portfolio-display-sortable', true );
$inline_filters   = ( ! empty( $options['portfolio_inline_filters'] ) && $options['portfolio_inline_filters'] == '1' ) ? '1' : '0';
$filters_id       = ( ! empty( $options['portfolio_inline_filters'] ) && $options['portfolio_inline_filters'] == '1' ) ? 'portfolio-filters-inline' : 'portfolio-filters';
$bg               = get_post_meta( $post->ID, '_nectar_header_bg', true );
$lightbox_only    = false;
?>

<style>
	<?php if ( $span_num == 'elastic-portfolio-item' ) { ?> 
		.container-wrap { padding-bottom: 0px!important; }
		#call-to-action .triangle { display: none; }
	<?php } ?>
	
	<?php if ( $span_num == 'elastic-portfolio-item' && ! empty( $bg ) ) { ?> 
		.container-wrap { padding-top: 0px!important; }
	<?php } ?>
</style>



 <div class="row page-header-no-bg">
		<div class="container">	
			<div class="col span_12 section-title">
				
				<h1>
				<?php
				$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
				if ( $term ) {
					echo esc_html( $term->name );
				} else {
					wp_title( '', true );
				}
				?>
				</h1>

			</div>
		</div>
	</div>



<div class="container-wrap">
	
	<div class="container main-content" data-col-num="cols-<?php echo esc_html( $cols ); ?>">

		<div class="portfolio-wrap 
		<?php
		if ( $project_style == '1' && $span_num == 'elastic-portfolio-item' ) {
			echo 'default-style';
		}  if ( $project_style == '6' && $span_num == 'elastic-portfolio-item' ) {
			echo 'spaced';}
		?>
		">
			
		<span class="portfolio-loading"></span>
		
		<div id="portfolio" class="row portfolio-items <?php if ( $masonry_layout == 'true' ) { echo 'masonry-items'; } else { echo 'no-masonry'; } ?> <?php echo esc_attr( $infinite_scroll_class ); ?>"  data-categories-to-show="" data-starting-filter=""  data-gutter="<?php echo esc_attr( $item_spacing ); ?>" data-masonry-type="<?php echo esc_attr( $masonry_sizing_type ); ?>" data-ps="<?php echo esc_attr( $project_style ); ?>" data-col-num="<?php echo esc_attr( $cols ); ?>">
			<?php

			$posts_per_page = '-1';
			if ( ! empty( $options['portfolio_pagination'] ) && $options['portfolio_pagination'] == '1' ) {
				$posts_per_page = ( ! empty( $options['portfolio_pagination_number'] ) ) ? $options['portfolio_pagination_number'] : '-1';
			}


			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					?>
				
				
					<?php

					$terms        = get_the_terms( $post->id, 'project-type' );
					$project_cats = null;

					if ( ! empty( $terms ) ) {
						foreach ( $terms as $term ) {
							$project_cats .= strtolower( $term->slug ) . ' ';
						}
					}



					global $masonry_layout;
					$masonry_item_sizing = ( $masonry_layout == 'true' ) ? get_post_meta( $post->ID, '_portfolio_item_masonry_sizing', true ) : null;
					if ( empty( $masonry_item_sizing ) && $masonry_layout == 'true' ) {
						$masonry_item_sizing = 'regular';
					}

					$masonry_sizing_type = ( ! empty( $options['portfolio_masonry_grid_sizing'] ) && $options['portfolio_masonry_grid_sizing'] == 'photography' ) ? 'photography' : 'default';

					// no tall size for photography
					if ( $masonry_sizing_type == 'photography' && $masonry_item_sizing == 'tall' ) {
						$masonry_item_sizing = 'wide_tall';
					}

					$project_accent_color   = get_post_meta( $post->ID, '_nectar_project_accent_color', true );
					$project_title_color    = get_post_meta( $post->ID, '_nectar_project_title_color', true );
					$project_subtitle_color = get_post_meta( $post->ID, '_nectar_project_subtitle_color', true );

					$customProjectClass = get_post_meta( $post->ID, '_nectar_project_css_class', true );
					if ( ! empty( $customProjectClass ) ) {
						$customProjectClass = ' ' . sanitize_text_field( $customProjectClass );
					}

					$custom_project_link = get_post_meta( $post->ID, '_nectar_external_project_url', true );
					$the_project_link    = ( ! empty( $custom_project_link ) ) ? $custom_project_link : esc_url( get_permalink() );

					$project_excerpt = get_post_meta( $post->ID, '_nectar_project_excerpt', true );

					?>
				
				<div class="col <?php echo esc_attr( $span_num ) . ' ' . esc_attr( $masonry_item_sizing ) . esc_attr( $customProjectClass ); ?> element" data-project-cat="<?php echo esc_attr( $project_cats ); ?>" <?php if ( ! empty( $project_accent_color ) ) { echo 'data-project-color="' . esc_attr( $project_accent_color ) . '"'; } else { echo 'data-default-color="true"';} ?> data-title-color="<?php echo esc_attr( $project_title_color ); ?>" data-subtitle-color="<?php echo esc_attr( $project_subtitle_color ); ?>">
					
					<div class="inner-wrap animated" data-animation="<?php echo esc_attr( $load_in_animation ); ?>">

									<?php
									// project style 1
									if ( $project_style == '1' ) {
										?>
								
							<div class="work-item">
								 
												<?php

												$thumb_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
												if ( $masonry_sizing_type == 'photography' && ! empty( $masonry_item_sizing ) ) {
													$thumb_size = $thumb_size . '_photography';

													// no tall size in photography
													if ( $thumb_size == 'tall_photography' ) {
														$thumb_size = 'wide_tall_photography';
													}
												}

												// custom thumbnail
												$custom_thumbnail = get_post_meta( $post->ID, '_nectar_portfolio_custom_thumbnail', true );

												if ( ! empty( $custom_thumbnail ) ) {
													echo '<img class="custom-thumbnail" src="' . esc_url( $custom_thumbnail ) . '" alt="' . get_the_title() . '" />';
												} else {

													if ( has_post_thumbnail() ) {
														 echo get_the_post_thumbnail( $post->ID, $thumb_size, array( 'title' => '' ) );
													}
													// no image added
													else {
														switch ( $thumb_size ) {
															case 'wide_photography':
																$no_image_size = 'no-portfolio-item-photography-wide.jpg';
																break;
															case 'regular_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide_tall_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide':
																$no_image_size = 'no-portfolio-item-wide.jpg';
																break;
															case 'tall':
																$no_image_size = 'no-portfolio-item-tall.jpg';
																break;
															case 'regular':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															case 'wide_tall':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															default:
																$no_image_size = 'no-portfolio-item-small.jpg';
																break;
														}
														 echo '<img src="' . get_template_directory_uri() . '/img/' . esc_attr( $no_image_size ) . '" alt="no image added yet." />'; // WPCS: XSS ok.
													}
												}
												?>
								
								<div class="work-info-bg"></div>
								<div class="work-info"> 
									
									<div class="vert-center">
												<?php

												$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
												$video_embed    = get_post_meta( $post->ID, '_nectar_video_embed', true );
												$video_m4v      = get_post_meta( $post->ID, '_nectar_video_m4v', true );

												// video
												if ( ! empty( $video_embed ) || ! empty( $video_m4v ) ) {

													echo nectar_portfolio_video_popup_link( $post, $project_style, $video_embed, $video_m4v );

												}

												// image
												else {
													echo '<a href="' . esc_url( $featured_image[0] ) . '" class="default-link pretty_photo" >' . esc_html__( 'View Larger', 'salient' ) . '</a> ';
												}

												echo '<a class="default-link" href="' . esc_url( $the_project_link ) . '">' . esc_html__( 'More Details', 'salient' ) . '</a>';
												?>
										
									</div><!--/vert-center-->
								</div>
							</div><!--work-item-->
							
							<div class="work-meta">
								<h4 class="title"><?php the_title(); ?></h4>
								
												<?php
												if ( ! empty( $options['portfolio_date'] ) && $options['portfolio_date'] == 1 ) {
													echo get_the_date();
												}
												?>
			
							</div>
							<div class="nectar-love-wrap">
										<?php
										if ( function_exists( 'nectar_love' ) ) {
											nectar_love();}
										?>
							</div><!--/nectar-love-wrap-->	
						
										<?php
									} //project style 1


										// project style 2
									elseif ( $project_style == '2' ) {
										?>
							
							<div class="work-item style-2">
								
												<?php
												$thumb_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
												if ( $masonry_sizing_type == 'photography' && ! empty( $masonry_item_sizing ) ) {
													$thumb_size = $thumb_size . '_photography';

													// no tall size in photography
													if ( $thumb_size == 'tall_photography' ) {
														$thumb_size = 'wide_tall_photography';
													}
												}

												// custom thumbnail
												$custom_thumbnail = get_post_meta( $post->ID, '_nectar_portfolio_custom_thumbnail', true );

												if ( ! empty( $custom_thumbnail ) ) {
													echo '<img class="custom-thumbnail" src="' . esc_url( $custom_thumbnail ) . '" alt="' . get_the_title() . '" />';
												} else {

													if ( has_post_thumbnail() ) {
														 echo get_the_post_thumbnail( $post->ID, $thumb_size, array( 'title' => '' ) );
													}

													// no image added
													else {
														switch ( $thumb_size ) {
															case 'wide_photography':
																$no_image_size = 'no-portfolio-item-photography-wide.jpg';
																break;
															case 'regular_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide_tall_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide':
																$no_image_size = 'no-portfolio-item-wide.jpg';
																break;
															case 'tall':
																$no_image_size = 'no-portfolio-item-tall.jpg';
																break;
															case 'regular':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															case 'wide_tall':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															default:
																$no_image_size = 'no-portfolio-item-small.jpg';
																break;
														}
														 echo '<img src="' . get_template_directory_uri() . '/img/' . esc_attr(  $no_image_size ) . '" alt="no image added yet." />'; // WPCS: XSS ok.
													}
												}
												?>
				
								<div class="work-info-bg"></div>
								<div class="work-info">

									<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
		
									<div class="vert-center"><h3><?php echo get_the_title(); ?></h3> <p>
																			<?php
																			if ( ! empty( $options['portfolio_date'] ) && $options['portfolio_date'] == 1 ) {
																				echo get_the_date();}
																			?>
									</p></div><!--/vert-center-->
									
								</div>
							</div><!--work-item-->
							
										<?php
									} //project style 2





									elseif ( $project_style == '3' ) {
										?>
							
							<div class="work-item style-3">
								
												<?php
												$thumb_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
												if ( $masonry_sizing_type == 'photography' && ! empty( $masonry_item_sizing ) ) {
													$thumb_size = $thumb_size . '_photography';

													// no tall size in photography
													if ( $thumb_size == 'tall_photography' ) {
														$thumb_size = 'wide_tall_photography';
													}
												}

												// custom thumbnail
												$custom_thumbnail = get_post_meta( $post->ID, '_nectar_portfolio_custom_thumbnail', true );

												if ( ! empty( $custom_thumbnail ) ) {
													echo '<img class="custom-thumbnail" src="' . esc_url( $custom_thumbnail ) . '" alt="' . get_the_title() . '" />';
												} else {

													if ( has_post_thumbnail() ) {
														 echo get_the_post_thumbnail( $post->ID, $thumb_size, array( 'title' => '' ) );
													}

													// no image added
													else {
														switch ( $thumb_size ) {
															case 'wide_photography':
																$no_image_size = 'no-portfolio-item-photography-wide.jpg';
																break;
															case 'regular_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide_tall_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide':
																$no_image_size = 'no-portfolio-item-wide.jpg';
																break;
															case 'tall':
																$no_image_size = 'no-portfolio-item-tall.jpg';
																break;
															case 'regular':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															case 'wide_tall':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															default:
																$no_image_size = 'no-portfolio-item-small.jpg';
																break;
														}
														 echo '<img src="' . get_template_directory_uri() . '/img/' . esc_attr(  $no_image_size ) . '" alt="no image added yet." />'; // WPCS: XSS ok.
													}
												}
												?>
				
								<div class="work-info-bg"></div>
								<div class="work-info">

									<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
									
									<div class="vert-center">
										<h3><?php echo get_the_title(); ?> </h3> 
										<?php
										if ( ! empty( $project_excerpt ) ) {
											echo '<p>' . wp_kses_post( $project_excerpt ) . '</p>';
										} elseif ( ! empty( $options['portfolio_date'] ) && $options['portfolio_date'] == 1 ) {
											echo '<p>' . get_the_date() . '</p>';}
										?>
									</div><!--/vert-center-->
									
								</div>
							</div><!--work-item-->
							
										<?php
									} //project style 3


									elseif ( $project_style == '4' ) {
										?>
							
							<div class="work-item style-4">
								
												<?php
												$thumb_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
												if ( $masonry_sizing_type == 'photography' && ! empty( $masonry_item_sizing ) ) {
													$thumb_size = $thumb_size . '_photography';

													// no tall size in photography
													if ( $thumb_size == 'tall_photography' ) {
														$thumb_size = 'wide_tall_photography';
													}
												}

												// custom thumbnail
												$custom_thumbnail = get_post_meta( $post->ID, '_nectar_portfolio_custom_thumbnail', true );

												if ( ! empty( $custom_thumbnail ) ) {
													echo '<img class="custom-thumbnail" src="' . esc_url( $custom_thumbnail ) . '" alt="' . get_the_title() . '" />';
												} else {

													if ( has_post_thumbnail() ) {
														 echo get_the_post_thumbnail( $post->ID, $thumb_size, array( 'title' => '' ) );
													}

													// no image added
													else {
														switch ( $thumb_size ) {
															case 'wide_photography':
																$no_image_size = 'no-portfolio-item-photography-wide.jpg';
																break;
															case 'regular_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide_tall_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide':
																$no_image_size = 'no-portfolio-item-wide.jpg';
																break;
															case 'tall':
																$no_image_size = 'no-portfolio-item-tall.jpg';
																break;
															case 'regular':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															case 'wide_tall':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															default:
																$no_image_size = 'no-portfolio-item-small.jpg';
																break;
														}
														 echo '<img src="' . get_template_directory_uri() . '/img/' . esc_attr( $no_image_size ) . '" alt="no image added yet." />'; // WPCS: XSS ok.
													}
												}
												?>

								<div class="work-info">
									
									<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
									
									<div class="bottom-meta">
										<h3><?php echo get_the_title(); ?> </h3> 
										<?php
										if ( ! empty( $project_excerpt ) ) {
											echo '<p>' . wp_kses_post( $project_excerpt ) . '</p>';
										} elseif ( ! empty( $options['portfolio_date'] ) && $options['portfolio_date'] == 1 ) {
											echo '<p>' . get_the_date() . '</p>';}
										?>
									</div><!--/bottom-meta-->
									
								</div>
							</div><!--work-item-->
							
										<?php
									} //project style 4


									elseif ( $project_style == '5' ) {
										?>
							
							<div class="work-item style-3-alt">
								
												<?php
												$thumb_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
												if ( $masonry_sizing_type == 'photography' && ! empty( $masonry_item_sizing ) ) {
													$thumb_size = $thumb_size . '_photography';

													// no tall size in photography
													if ( $thumb_size == 'tall_photography' ) {
														$thumb_size = 'wide_tall_photography';
													}
												}

												// custom thumbnail
												$custom_thumbnail = get_post_meta( $post->ID, '_nectar_portfolio_custom_thumbnail', true );

												if ( ! empty( $custom_thumbnail ) ) {
													echo '<img class="custom-thumbnail" src="' . esc_url( $custom_thumbnail ) . '" alt="' . get_the_title() . '" />';
												} else {

													if ( has_post_thumbnail() ) {
														 echo get_the_post_thumbnail( $post->ID, $thumb_size, array( 'title' => '' ) );
													}

													// no image added
													else {
														switch ( $thumb_size ) {
															case 'wide_photography':
																$no_image_size = 'no-portfolio-item-photography-wide.jpg';
																break;
															case 'regular_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide_tall_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide':
																$no_image_size = 'no-portfolio-item-wide.jpg';
																break;
															case 'tall':
																$no_image_size = 'no-portfolio-item-tall.jpg';
																break;
															case 'regular':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															case 'wide_tall':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															default:
																$no_image_size = 'no-portfolio-item-small.jpg';
																break;
														}
														 echo '<img src="' . get_template_directory_uri() . '/img/' . esc_attr( $no_image_size ) . '" alt="no image added yet." />'; // WPCS: XSS ok.
													}
												}
												?>
				
								<div class="work-info-bg"></div>
								<div class="work-info">

									<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
									
									<div class="vert-center">
										<h3><?php echo get_the_title(); ?> </h3> 
										<?php
										if ( ! empty( $project_excerpt ) ) {
											echo '<p>' . wp_kses_post( $project_excerpt ) . '</p>';
										} elseif ( ! empty( $options['portfolio_date'] ) && $options['portfolio_date'] == 1 ) {
											echo '<p>' . get_the_date() . '</p>';}
										?>
									</div><!--/vert-center-->
									
								</div>
							</div><!--work-item-->
							
										<?php
									} //project style 5


									elseif ( $project_style == '6' ) {

										$using_custom_content = get_post_meta( $post->ID, '_nectar_portfolio_custom_grid_item', true );
										$custom_content       = get_post_meta( $post->ID, '_nectar_portfolio_custom_grid_item_content', true );
										?>
							
							<div class="work-item style-5" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>" >
								
												<?php
												$thumb_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
												if ( $masonry_sizing_type == 'photography' && ! empty( $masonry_item_sizing ) ) {
													$thumb_size = $thumb_size . '_photography';

													// no tall size in photography
													if ( $thumb_size == 'tall_photography' ) {
														$thumb_size = 'wide_tall_photography';
													}
												}

												$parallax_images = get_post_meta( $post->ID, '_nectar_3d_parallax_images', true );

												if ( ! empty( $parallax_images ) ) {

													echo '<div class="parallaxImg">';

													$images = explode( ',', $parallax_images );
													$i      = 0;
													foreach ( $images as $attach_id ) {
														$i++;

														$img = wp_get_attachment_image_src( $attach_id, $thumb_size );
														// add one sizer img
														if ( $i == 1 ) {
															echo '<img class="sizer" src="' . esc_url( $img[0] ) . '" alt="' . get_the_title() . '" />';
														}
														echo '<div class="parallaxImg-layer" data-img="' . esc_url( $img[0] ) . '" Layer-' . $i . '"></div>';

													}

													echo '</div>';

												}
												// no parallax images set
												else {
													if ( has_post_thumbnail() ) {

														$thumbnail_id  = get_post_thumbnail_id( $post->ID );
														$thumbnail_url = wp_get_attachment_image_src( $thumbnail_id, $thumb_size );

														echo '<img class="sizer" src="' . esc_url( $thumbnail_url[0] ) . '" alt="' . get_the_title() . '" />';

														echo '<div class="parallaxImg">';
														echo '<div class="parallaxImg-layer" data-img="' . esc_url( $thumbnail_url[0] ) . '"></div>';
														echo '<div class="parallaxImg-layer"><div class="bg-overlay"></div> <div class="work-meta"><div class="inner">';
														echo '	<h4 class="title"> ' . get_the_title() . '</h4>';

														if ( ! empty( $project_excerpt ) ) {
															echo '<p>' . wp_kses_post( $project_excerpt ) . '</p>';
														} elseif ( ! empty( $options['portfolio_date'] ) && $options['portfolio_date'] == 1 ) {
															echo '<p>' . get_the_date() . '</p>';
														}

														echo '</div></div></div></div>';
													}

													// no image added
													else {
														switch ( $thumb_size ) {
															case 'wide_photography':
																$no_image_size = 'no-portfolio-item-photography-wide.jpg';
																break;
															case 'regular_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide_tall_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide':
																$no_image_size = 'no-portfolio-item-wide.jpg';
																break;
															case 'tall':
																$no_image_size = 'no-portfolio-item-tall.jpg';
																break;
															case 'regular':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															case 'wide_tall':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															default:
																$no_image_size = 'no-portfolio-item-small.jpg';
																break;
														}


														echo '<img class="sizer" src="' . get_template_directory_uri() . '/img/' . esc_attr(  $no_image_size ) . '" alt="' . get_the_title() . '" />'; // WPCS: XSS ok.

														echo '<div class="parallaxImg">';
														echo '<div class="parallaxImg-layer" data-img="' . get_template_directory_uri() . '/img/' . esc_attr( $no_image_size ) . '" "></div>'; // WPCS: XSS ok.
														echo '<div class="parallaxImg-layer"><div class="bg-overlay"></div> <div class="work-meta"><div class="inner">';
														echo '	<h4 class="title"> ' . get_the_title() . '</h4>';

														if ( ! empty( $project_excerpt ) ) {
															echo '<p>' . wp_kses_post( $project_excerpt ) . '</p>';
														} elseif ( ! empty( $options['portfolio_date'] ) && $options['portfolio_date'] == 1 ) {
															echo '<p>' . get_the_date() . '</p>';
														}

														echo '</div></div></div></div>';

													}
												}

												if ( $lightbox_only != 'true' ) {
													?>
											
									<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
								
													<?php
												} else {

													$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
													$video_embed    = get_post_meta( $post->ID, '_nectar_video_embed', true );
													$video_m4v      = get_post_meta( $post->ID, '_nectar_video_m4v', true );

													// video
													if ( ! empty( $video_embed ) || ! empty( $video_m4v ) ) {

														echo nectar_portfolio_video_popup_link( $post, $project_style, $video_embed, $video_m4v );

													} else {
														?>
										
										<a href="<?php echo esc_url( $featured_image[0] ); ?>"  <?php	if ( ! empty( $project_image_caption ) ) { echo ' title="' . wp_kses_post( $project_image_caption ) . '" ';} ?> class="pretty_photo"></a>
										
														<?php
													}
												}

												?>

							
							</div><!--work-item-->

						
							
										<?php
									} //project style 6



										// project style 7
									elseif ( $project_style == '7' ) {

										$using_custom_content = get_post_meta( $post->ID, '_nectar_portfolio_custom_grid_item', true );
										$custom_content       = get_post_meta( $post->ID, '_nectar_portfolio_custom_grid_item_content', true );
										?>
							
							<div class="work-item style-2" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>">
								
												<?php
												$thumb_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
												if ( $masonry_sizing_type == 'photography' && ! empty( $masonry_item_sizing ) ) {
													$thumb_size = $thumb_size . '_photography';

													// no tall size in photography
													if ( $thumb_size == 'tall_photography' ) {
														$thumb_size = 'wide_tall_photography';
													}
												}

												// custom thumbnail
												$custom_thumbnail = get_post_meta( $post->ID, '_nectar_portfolio_custom_thumbnail', true );

												if ( ! empty( $custom_thumbnail ) ) {
													echo '<img class="custom-thumbnail" src="' . nectar_ssl_check( esc_url( $custom_thumbnail ) ) . '" alt="' . get_the_title() . '" />';
												} else {

													if ( has_post_thumbnail() ) {
														 echo get_the_post_thumbnail( $post->ID, $thumb_size, array( 'title' => '' ) );
													}

													// no image added
													else {
														switch ( $thumb_size ) {
															case 'wide_photography':
																$no_image_size = 'no-portfolio-item-photography-wide.jpg';
																break;
															case 'regular_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide_tall_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide':
																$no_image_size = 'no-portfolio-item-wide.jpg';
																break;
															case 'tall':
																$no_image_size = 'no-portfolio-item-tall.jpg';
																break;
															case 'regular':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															case 'wide_tall':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															default:
																$no_image_size = 'no-portfolio-item-small.jpg';
																break;
														}
														 echo '<img src="' . get_template_directory_uri() . '/img/' . esc_attr( $no_image_size ) . '" alt="no image added yet." />'; // WPCS: XSS ok.
													}
												}
												?>
				
								<div class="work-info-bg"></div>
								<div class="work-info">
									
										<?php
													// custom content
										if ( $using_custom_content == 'on' ) {
											if ( ! empty( $custom_project_link ) ) {
												echo '<a href="' . esc_url( $the_project_link ) . '"></a>';
											}
											// default
										} else {
											?>

										
											<?php if ( $lightbox_only != 'true' ) { ?>
											
											<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
										
												<?php
} else {

	$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
	$video_embed    = get_post_meta( $post->ID, '_nectar_video_embed', true );
	$video_m4v      = get_post_meta( $post->ID, '_nectar_video_m4v', true );

	// video
	if ( ! empty( $video_embed ) || ! empty( $video_m4v ) ) {

		echo nectar_portfolio_video_popup_link( $post, $project_style, $video_embed, $video_m4v );

	} else {
		?>
												
												<a href="<?php echo esc_url( $featured_image[0] ); ?>" <?php if ( ! empty( $project_image_caption ) ) { echo ' title="' . wp_kses_post( $project_image_caption ) . '" ';} ?> class="pretty_photo"></a>
												
											<?php
	}
}
										}
										?>
									
		
									<div class="vert-center">
										<?php
										if ( ! empty( $using_custom_content ) && $using_custom_content == 'on' ) {
											echo '<div class="custom-content">' . do_shortcode( $custom_content ) . '</div>';
										} else {
											?>
												
											<h3><?php echo get_the_title(); ?></h3> 
											<?php
											if ( ! empty( $project_excerpt ) ) {
												echo '<p>' . wp_kses_post( $project_excerpt ) . '</p>';
											} elseif ( ! empty( $options['portfolio_date'] ) && $options['portfolio_date'] == 1 ) {
												echo '<p>' . get_the_date() . '</p>';}
										}
										?>
									</div><!--/vert-center-->
									
								</div>
							</div><!--work-item-->
							
										<?php
									} //project style 7

										// project style 8
									elseif ( $project_style == '8' ) {

										$using_custom_content = get_post_meta( $post->ID, '_nectar_portfolio_custom_grid_item', true );
										$custom_content       = get_post_meta( $post->ID, '_nectar_portfolio_custom_grid_item_content', true );
										?>
							
							<div class="work-item style-2" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>">
								
												<?php
												$thumb_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
												if ( $masonry_sizing_type == 'photography' && ! empty( $masonry_item_sizing ) ) {
													$thumb_size = $thumb_size . '_photography';

													// no tall size in photography
													if ( $thumb_size == 'tall_photography' ) {
														$thumb_size = 'wide_tall_photography';
													}
												}

												// custom thumbnail
												$custom_thumbnail = get_post_meta( $post->ID, '_nectar_portfolio_custom_thumbnail', true );

												if ( ! empty( $custom_thumbnail ) ) {
													echo '<img class="custom-thumbnail" src="' . nectar_ssl_check( esc_url( $custom_thumbnail ) ) . '" alt="' . get_the_title() . '" />';
												} else {

													if ( has_post_thumbnail() ) {
														 echo get_the_post_thumbnail( $post->ID, $thumb_size, array( 'title' => '' ) );
													}

													// no image added
													else {
														switch ( $thumb_size ) {
															case 'wide_photography':
																$no_image_size = 'no-portfolio-item-photography-wide.jpg';
																break;
															case 'regular_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide_tall_photography':
																$no_image_size = 'no-portfolio-item-photography-regular.jpg';
																break;
															case 'wide':
																$no_image_size = 'no-portfolio-item-wide.jpg';
																break;
															case 'tall':
																$no_image_size = 'no-portfolio-item-tall.jpg';
																break;
															case 'regular':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															case 'wide_tall':
																$no_image_size = 'no-portfolio-item-tiny.jpg';
																break;
															default:
																$no_image_size = 'no-portfolio-item-small.jpg';
																break;
														}
														 echo '<img src="' . get_template_directory_uri() . '/img/' . esc_attr(  $no_image_size ) . '" alt="no image added yet." />'; // WPCS: XSS ok.
													}
												}
												?>
				
								<div class="work-info-bg"></div>
								<div class="work-info">
									
										<?php
													// custom content
										if ( $using_custom_content == 'on' ) {
											if ( ! empty( $custom_project_link ) ) {
												echo '<a href="' . esc_url( $the_project_link ) . '"></a>';
											}
											// default
										} else {
											?>

										
											<?php if ( $lightbox_only != 'true' ) { ?>
											
											<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
										
												<?php
} else {

	$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
	$video_embed    = get_post_meta( $post->ID, '_nectar_video_embed', true );
	$video_m4v      = get_post_meta( $post->ID, '_nectar_video_m4v', true );

	// video
	if ( ! empty( $video_embed ) || ! empty( $video_m4v ) ) {

		echo nectar_portfolio_video_popup_link( $post, $project_style, $video_embed, $video_m4v );

	} else {
		?>
												
												<a href="<?php echo esc_url( $featured_image[0] ); ?>" <?php if ( ! empty( $project_image_caption ) ) {	echo ' title="' . wp_kses_post( $project_image_caption ) . '" ';} ?> class="pretty_photo"></a>
												
											<?php
	}
}
										}
										?>
									
		
									<div class="vert-center">
										<?php
										if ( ! empty( $using_custom_content ) && $using_custom_content == 'on' ) {
											echo '<div class="custom-content">' . do_shortcode( $custom_content ) . '</div>';
										} else {
											?>
												
											
											<?php
											if ( ! empty( $project_excerpt ) ) {
												echo '<p>' . wp_kses_post( $project_excerpt ) . '</p>';
											} elseif ( ! empty( $options['portfolio_date'] ) && $options['portfolio_date'] == 1 ) {
												echo '<p>' . get_the_date() . '</p>';}
											?>
											<h3><?php echo get_the_title(); ?></h3> 
											<svg class="next-arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 39 12"><line class="top" x1="23" y1="-0.5" x2="29.5" y2="6.5" stroke="#ffffff;"/><line class="bottom" x1="23" y1="12.5" x2="29.5" y2="5.5" stroke="#ffffff;"/></svg><span class="line"></span></span>
										<?php } ?>
									</div><!--/vert-center-->
									
								</div>
							</div><!--work-item-->
							
										<?php
									} //project style 8

									elseif ( $project_style == '9' ) {

										$using_custom_content = get_post_meta( $post->ID, '_nectar_portfolio_custom_grid_item', true );
										$custom_content       = get_post_meta( $post->ID, '_nectar_portfolio_custom_grid_item_content', true );
										?>
							
						<div class="work-item style-1" data-custom-content="<?php echo esc_attr( $using_custom_content ); ?>">
							 
											<?php

											$thumb_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
											if ( $masonry_sizing_type == 'photography' && ! empty( $masonry_item_sizing ) ) {
												$thumb_size = $thumb_size . '_photography';

												// no tall size in photography
												if ( $thumb_size == 'tall_photography' ) {
													$thumb_size = 'wide_tall_photography';
												}
											}

											// custom thumbnail
											$custom_thumbnail = get_post_meta( $post->ID, '_nectar_portfolio_custom_thumbnail', true );

											if ( ! empty( $custom_thumbnail ) ) {

												$image_srcset        = '';
												$custom_thumbnail_id = fjarrett_get_attachment_id_from_url( $custom_thumbnail );

												if ( ! is_null( $custom_thumbnail_id ) && ! empty( $custom_thumbnail_id ) ) {

													if ( function_exists( 'wp_get_attachment_image_srcset' ) ) {

															$image_srcset_values = wp_get_attachment_image_srcset( $custom_thumbnail_id, 'full' );
														if ( $image_srcset_values ) {
															$image_srcset .= 'srcset="' . $image_srcset_values . '" ';
														}
													}
												}

												echo '<img class="custom-thumbnail" src="' . nectar_ssl_check( esc_url( $custom_thumbnail ) ) . '" alt="' . get_the_title() . '" />';

											} else {

												if ( has_post_thumbnail() ) {

													// create featured image with srcset
													$image_width  = null;
													$image_height = null;

													if ( ! empty( $image_meta['sizes'] ) && ! empty( $image_meta['sizes'][ $thumb_size ] ) ) {
														$image_width = $image_meta['sizes'][ $thumb_size ]['width'];
													}
													if ( ! empty( $image_meta['sizes'] ) && ! empty( $image_meta['sizes'][ $thumb_size ] ) ) {
														$image_height = $image_meta['sizes'][ $thumb_size ]['height'];
													}

													$wp_img_alt_tag = get_the_title();

													$image_src = null;
													$image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), $thumb_size );
													if ( ! empty( $image_src ) ) {
														$image_src = $image_src[0];
													}

															$project_featured_img = '<img class="size-' . esc_attr( $masonry_item_sizing ) . '" src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $wp_img_alt_tag ) . '" height="' . esc_attr( $image_height ) . '" width="' . esc_attr( $image_width ) . '" />';

													echo $project_featured_img; // WPCS: XSS ok
												}
												// no image added
												else {
													switch ( $thumb_size ) {
														case 'wide_photography':
															$no_image_size = 'no-portfolio-item-photography-wide.jpg';
															break;
														case 'regular_photography':
															$no_image_size = 'no-portfolio-item-photography-regular.jpg';
															break;
														case 'wide_tall_photography':
															$no_image_size = 'no-portfolio-item-photography-regular.jpg';
															break;
														case 'wide':
															$no_image_size = 'no-portfolio-item-wide.jpg';
															break;
														case 'tall':
															$no_image_size = 'no-portfolio-item-tall.jpg';
															break;
														case 'regular':
															$no_image_size = 'no-portfolio-item-tiny.jpg';
															break;
														case 'wide_tall':
															$no_image_size = 'no-portfolio-item-tiny.jpg';
															break;
														default:
															$no_image_size = 'no-portfolio-item-small.jpg';
															break;
													}
													 echo '<img src="' . get_template_directory_uri() . '/img/' . esc_attr(  $no_image_size ) . '" alt="no image added yet." />'; // WPCS: XSS ok.
												}
											}
											?>
							

							<div class="work-info"> 
								
							
										<?php if ( $lightbox_only != 'true' ) { ?>
									
									<a href="<?php echo esc_url( $the_project_link ); ?>"></a>
								
											<?php
} else {

	$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
	$video_embed    = get_post_meta( $post->ID, '_nectar_video_embed', true );
	$video_m4v      = get_post_meta( $post->ID, '_nectar_video_m4v', true );

	// video
	if ( ! empty( $video_embed ) || ! empty( $video_m4v ) ) {

		echo nectar_portfolio_video_popup_link( $post, $project_style, $video_embed, $video_m4v );


	} else {
		?>
												
												<a href="<?php echo esc_url( $featured_image[0] ); ?>" <?php if ( ! empty( $project_image_caption ) ) { echo ' title="' . wp_kses_post( $project_image_caption ) . '" ';} ?> class="pretty_photo"></a>
												
										<?php
	}
}
?>
											
			
							</div>
								
							</div><!--work-item-->
							
							<div class="work-meta">
								<h4 class="title"><?php the_title(); ?></h4>
								
										<?php
										if ( ! empty( $project_excerpt ) ) {
											echo '<p>' . wp_kses_post( $project_excerpt ) . '</p>';
										} elseif ( ! empty( $options['portfolio_date'] ) && $options['portfolio_date'] == 1 ) {
											echo '<p>' . get_the_date() . '</p>';}
										?>
								
							</div>

					
									<?php } //project style 9 ?>
						
						
						
				</div><!--/inner-->

				</div><!--/col-->
				
							<?php
							endwhile;
endif;
			?>
		</div><!--/portfolio-->
	
		
		<?php
		if ( ! empty( $options['portfolio_extra_pagination'] ) && $options['portfolio_extra_pagination'] == '1' ) {

				   global $wp_query, $wp_rewrite;

				   $fw_pagination   = ( $span_num == 'elastic-portfolio-item' ) ? 'fw-pagination' : null;
				   $masonry_padding = ( $project_style != '1' ) ? 'alt-style-padding' : null;

				   $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
				   $total_pages                                  = $wp_query->max_num_pages;

				   $permalink_structure = get_option( 'permalink_structure' );
				   $format              = empty( $permalink_structure ) ? '&paged=%#%' : 'page/%#%/';
			if ( $total_pages > 1 ) {

				echo '<div id="pagination" class="' . esc_attr( $fw_pagination ) . ' ' . esc_attr( $masonry_padding ) . esc_attr( $infinite_scroll_class ) . '" data-is-text="' . esc_attr__( 'All items loaded', 'salient' ) . '">';

				echo paginate_links(
					array(
						'base'    => get_pagenum_link( 1 ) . '%_%',
						'format'  => $format,
						'current' => $current,
						'total'   => $total_pages,
					)
				);

				echo '</div>';

			}
		}
			// regular pagination
		else {

			if ( get_next_posts_link() || get_previous_posts_link() ) {

				$fw_pagination   = ( $span_num == 'elastic-portfolio-item' ) ? 'fw-pagination' : null;
				$masonry_padding = ( $project_style != '1' ) ? 'alt-style-padding' : null;

				echo '<div id="pagination" class="' . esc_attr( $fw_pagination ) . ' ' . esc_attr( $masonry_padding ) . esc_attr( $infinite_scroll_class ) . '" data-is-text="' . esc_attr__( 'All items loaded', 'salient' ) . '">
					      <div class="prev">' . get_previous_posts_link( '&laquo; Previous Entries' ) . '</div>
					      <div class="next">' . get_next_posts_link( 'Next Entries &raquo;', '' ) . '</div>
				          </div>';

			}
		}
		?>
		
		</div><!--/container-->
		
	</div><!--/container-wrap-->

</div>

<?php get_footer(); ?>
