<?php
/**
 * Video Post Template
 *
 * @package Salient WordPress Theme
 * @subpackage Post Templates
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $options;
global $post;

$masonry_size_pm     = get_post_meta( $post->ID, '_post_item_masonry_sizing', true );
$masonry_item_sizing = ( ! empty( $masonry_size_pm ) ) ? $masonry_size_pm : 'regular';
$using_masonry       = null;

if ( isset( $GLOBALS['nectar_blog_std_style'] ) && $GLOBALS['nectar_blog_std_style'] != 'inherit' ) {
	$blog_standard_type = $GLOBALS['nectar_blog_std_style'];
} else {
	$blog_standard_type = ( ! empty( $options['blog_standard_type'] ) ) ? $options['blog_standard_type'] : 'classic';
}

$blog_type = $options['blog_type'];

if ( isset( $GLOBALS['nectar_blog_masonry_style'] ) && $GLOBALS['nectar_blog_masonry_style'] != 'inherit' ) {
	$masonry_type = $GLOBALS['nectar_blog_masonry_style'];
} else {
	$masonry_type = ( ! empty( $options['blog_masonry_type'] ) ) ? $options['blog_masonry_type'] : 'classic';
}

global $layout;

if ( $blog_type == 'masonry-blog-sidebar' && substr( $layout, 0, 3 ) != 'std' ||
$blog_type == 'masonry-blog-fullwidth' && substr( $layout, 0, 3 ) != 'std' ||
$blog_type == 'masonry-blog-full-screen-width' && substr( $layout, 0, 3 ) != 'std' ||
$layout == 'masonry-blog-sidebar' || $layout == 'masonry-blog-fullwidth' || $layout == 'masonry-blog-full-screen-width' ) {
	$using_masonry = true;
}

if ( $using_masonry == true && ! is_single() ) {
	$nectar_post_class_additions = $masonry_item_sizing . ' masonry-blog-item';
} else {
	$nectar_post_class_additions = $masonry_item_sizing;
}


$use_excerpt = ( ! empty( $options['blog_auto_excerpt'] ) && $options['blog_auto_excerpt'] == '1' ) ? 'true' : 'false';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $nectar_post_class_additions . ' video' ); ?>>
	
	<div class="inner-wrap animated">

		<div class="post-content">
			
			<?php
			if ( ! is_single() ) {

				$extra_class = '';
				if ( ! has_post_thumbnail() ) {
					$extra_class = 'no-img';
				}

				if ( ! ( $using_masonry != true && $blog_standard_type == 'minimal' || $using_masonry != true && $blog_standard_type == 'featured_img_left' ) &&
				! ( $using_masonry == true && $masonry_type == 'material' ) && ! ( $using_masonry == true && $masonry_type == 'auto_meta_overlaid_spaced' ) ) {
				?>
					
				<div class="post-meta <?php echo esc_attr( $extra_class ); ?>">
					
					<div class="date">
						<?php
						if ( $using_masonry == true ) {
							if ( $masonry_type != 'classic_enhanced' && $masonry_type != 'material' ) {
								echo get_the_date();
							}
						} else {
							?>

								<span class="month"><?php the_time( 'M' ); ?></span>
								<span class="day"><?php the_time( 'd' ); ?></span>
								<?php
								global $options;
								if ( ! empty( $options['display_full_date'] ) && $options['display_full_date'] == 1 ) {
									echo '<span class="year">' . get_the_time( 'Y' ) . '</span>';
								}
						}
						?>
						
					</div><!--/date-->
					
					<?php if ( ( $masonry_type == 'classic_enhanced' && $using_masonry == true ) ) { ?> 
						<span class="meta-author"> <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"> <i class="icon-default-style icon-salient-m-user"></i> <?php the_author(); ?></a> </span> 
						<?php if ( comments_open() ) { ?>
							<span class="meta-comment-count">  <a href="<?php comments_link(); ?>">
								<i class="icon-default-style steadysets-icon-chat-3"></i> <?php comments_number( '0', '1', '%' ); ?></a>
							</span>
						<?php } 

					} 

					if ( $using_masonry == true && $masonry_type == 'meta_overlaid' || $using_masonry == true && $masonry_type == 'auto_meta_overlaid_spaced' || $using_masonry == true && $masonry_type == 'material' ) {
					} else {

						if ( ! ( $using_masonry != true && $blog_standard_type == 'minimal' ) && ! ( $using_masonry != true && $blog_standard_type == 'featured_img_left' ) ) {
						?>
						 
						<div class="nectar-love-wrap">
							<?php
							if ( function_exists( 'nectar_love' ) ) {
								nectar_love();}
							?>
						</div><!--/nectar-love-wrap-->	
							<?php
						}
					}
					?>
					
					 </div><!--/post-meta-->
					 
				<?php } //conditional for entire post meta div 
				
			}

			$meta_overlaid_style = ( $using_masonry == true && $masonry_type == 'meta_overlaid' || $using_masonry == true && $masonry_type == 'auto_meta_overlaid_spaced' ) ? true : false;

			$img_size = ( $blog_type == 'masonry-blog-sidebar' && substr( $layout, 0, 3 ) != 'std' || $blog_type == 'masonry-blog-fullwidth' && substr( $layout, 0, 3 ) != 'std' || $blog_type == 'masonry-blog-full-screen-width' && substr( $layout, 0, 3 ) != 'std' || $layout == 'masonry-blog-sidebar' || $layout == 'masonry-blog-fullwidth' || $layout == 'masonry-blog-full-screen-width' ) ? 'large' : 'full';
			if ( $using_masonry == true && $masonry_type == 'meta_overlaid' ) {
				$img_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
			}
			if ( $using_masonry == true && $masonry_type == 'classic_enhanced' ) {
				$img_size = ( ! empty( $masonry_item_sizing ) && $masonry_item_sizing == 'regular' ) ? 'portfolio-thumb' : 'full';
			}

			if ( ! is_single() ) {
				if ( $using_masonry == true && $masonry_type == 'classic_enhanced' && has_post_thumbnail() && $masonry_item_sizing != 'wide_tall' || $using_masonry == true && $masonry_type == 'classic_enhanced' && $masonry_item_sizing == 'large_featured' ) {
					echo '<a class="img-link" href="' . esc_url( get_permalink() ) . '"><div class="video-play-button"></div><span class="post-featured-img">' . get_the_post_thumbnail( $post->ID, $img_size, array( 'title' => '' ) ) . '</span></a>';
				} elseif ( $using_masonry == true && $masonry_type == 'classic_enhanced' && ! has_post_thumbnail() || $using_masonry == true && $masonry_type == 'classic_enhanced' && has_post_thumbnail() && $masonry_item_sizing == 'wide_tall' ) {
					echo '<a class="img-link" href="' . esc_url( get_permalink() ) . '"><span class="post-featured-img">' . get_the_post_thumbnail( $post->ID, $img_size, array( 'title' => '' ) ) . '</span></a>';
				}

				if ( $using_masonry == true && $masonry_type == 'material' && has_post_thumbnail() ) {
						echo '<a class="img-link" href="' . esc_url( get_permalink() ) . '"><span class="post-featured-img"><span class="video-play-button"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="600px" height="800px" x="0px" y="0px" viewBox="0 0 600 800" enable-background="new 0 0 600 800" xml:space="preserve"><path fill="none" d="M0-1.79v800L600,395L0-1.79z"></path> </svg></span><span class="wrap">' . get_the_post_thumbnail( $post->ID, $img_size, array( 'title' => '' ) ) . '</span></span></a>';
				}
			}


		 /**
 		 * Featured Left Style
 		 */
			if ( $using_masonry != true && $blog_standard_type == 'featured_img_left' ) {

				if ( has_post_thumbnail() ) {

					 global $options;
					 $hide_featrued_image = ( ! empty( $options['blog_hide_featured_image'] ) ) ? $options['blog_hide_featured_image'] : '0';

				}

				if ( ! is_single() ) {
				?>
				  
					 <div class="article-content-wrap">
						
						<div class="post-featured-img-wrap"> 
							<span class="video-play-button"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="600px" height="800px" x="0px" y="0px" viewBox="0 0 600 800" enable-background="new 0 0 600 800" xml:space="preserve"><path fill="none" d="M0-1.79v800L600,395L0-1.79z"></path> </svg></span>
							<?php
							if ( has_post_thumbnail() ) {
									echo '<a href="' . esc_url( get_permalink() ) . '"><span class="post-featured-img" style="background-image: url(' . get_the_post_thumbnail_url( $post->ID, 'wide_photography', array( 'title' => '' ) ) . ');"></span></a>';
							}
							?>
							
						</div>
						
						<div class="post-content-wrap"> 
							
							<a class="entire-meta-link" href="<?php the_permalink(); ?>"></a>
							
							<?php
							echo '<span class="meta-category">';
							$categories = get_the_category();
							if ( ! empty( $categories ) ) {
								$output = null;
								foreach ( $categories as $category ) {
									$output .= '<a class="' . $category->slug . '" href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
								}
								echo trim( $output );
							}
							echo '</span>';
							?>
						
							<div class="post-header">

								<h3 class="title">
									<?php if ( ! is_single() && ! ( $using_masonry == true && $masonry_type == 'classic_enhanced' ) ) { ?> 
										<a href="<?php the_permalink(); ?>"><?php } ?>
											<?php the_title(); ?>
										<?php
										if ( ! is_single() && ! ( $using_masonry == true && $masonry_type == 'classic_enhanced' ) ) {
											?>
  								</a> 
									<?php } ?>
								</h3>

								
							</div><!--/post-header-->
							
							<?php
							// if no excerpt is set
							global $post;

							echo '<div class="excerpt">';
							$excerpt_length = ( ! empty( $options['blog_excerpt_length'] ) ) ? intval( $options['blog_excerpt_length'] ) : 15;
							echo nectar_excerpt( $excerpt_length );
							echo '</div>';

							if ( function_exists( 'get_avatar' ) ) {
									 echo '<div class="grav-wrap"><a href="' . get_author_posts_url( $post->post_author ) . '">' . get_avatar( get_the_author_meta( 'email' ), 70, null, get_the_author() ) . '</a><div class="text"><a href="' . get_author_posts_url( $post->post_author ) . '" rel="author">' . get_the_author() . '</a><span>' . get_the_date() . '</span></div></div>'; }


							?>

					</div><!--post-content-wrap-->

					</div><!--article-content-wrap-->

					<?php
				} //not single

				if ( is_single() ) {

					global $options;
					$hide_featrued_image = ( ! empty( $options['blog_hide_featured_image'] ) ) ? $options['blog_hide_featured_image'] : '0';

					$video_embed  = get_post_meta( $post->ID, '_nectar_video_embed', true );
					$video_m4v    = get_post_meta( $post->ID, '_nectar_video_m4v', true );
					$video_ogv    = get_post_meta( $post->ID, '_nectar_video_ogv', true );
					$video_poster = get_post_meta( $post->ID, '_nectar_video_poster', true );

					if ( ! empty( $video_embed ) && $hide_featrued_image != '1' || ! empty( $video_m4v ) && $hide_featrued_image != '1' ) {

							$wp_version = floatval( get_bloginfo( 'version' ) );

							// video embed
						if ( ! empty( $video_embed ) ) {

									 echo '<div class="video">' . do_shortcode( $video_embed ) . '</div>';

						}
							// self hosted video pre 3-6
						elseif ( ! empty( $video_m4v ) && $wp_version < '3.6' ) {

								 echo '<div class="video">';
										 // nectar_video($post->ID);
								 echo '</div>';

						}
							// self hosted video post 3-6
						elseif ( $wp_version >= '3.6' ) {

							if ( ! empty( $video_m4v ) || ! empty( $video_ogv ) ) {

								 $video_output = '[video ';

								if ( ! empty( $video_m4v ) ) {
									$video_output .= 'mp4="' . $video_m4v . '" '; }
								if ( ! empty( $video_ogv ) ) {
									$video_output .= 'ogv="' . $video_ogv . '"'; }

										$video_output .= ' poster="' . $video_poster . '"]';

										echo '<div class="video">' . do_shortcode( $video_output ) . '</div>';

							} // not empty m4v ogv

						} // greater than 3-6 wp

					} // not empty video embed

					the_content( '<span class="continue-reading">' . __( 'Read More', 'salient' ) . '</span>' );

				}//single


			} //featured left image style



		 /**
 		 * Minimal Standard Style
 		 */
			elseif ( $using_masonry != true && $blog_standard_type == 'minimal' ) {
		

				if ( ! is_single() ) { ?>
					 
					<div class="post-author">
						<?php
						if ( function_exists( 'get_avatar' ) ) {
							echo '<div class="grav-wrap"><a href="' . get_author_posts_url( $post->post_author ) . '">' . get_avatar( get_the_author_meta( 'email' ), 90, null, get_the_author() ) . '</a></div>'; }
						?>
						<span class="meta-author"> <?php the_author_posts_link(); ?></span>
						
					  <?php
						echo '<span class="meta-category">';

						$categories = get_the_category();
						if ( ! empty( $categories ) ) {

							echo '<span class="in">' . esc_html__( 'In', 'salient' ) . ' </span>';

							$output    = null;
							$cat_count = 0;
							foreach ( $categories as $category ) {
								$output .= '<a class="' . $category->slug . '" href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
								if ( count( $categories ) > 1 && ( $cat_count + 1 ) < count( $categories ) ) {
									$output .= ', ';
								}
								$cat_count++;
							}
							echo trim( $output );
						}
						echo '</span>';
						?>
					</div>
				<?php } ?>

				<div class="content-inner">
	 
						 <?php if ( ! is_single() ) { ?> 

							 <div class="article-content-wrap">

								<div class="post-header">
									<?php $h_num = '2'; ?>
		
									<h<?php echo esc_attr( $h_num ); ?> class="title">
										<?php if ( ! is_single() && ! ( $using_masonry == true && $masonry_type == 'classic_enhanced' ) ) { ?> 
											<a href="<?php the_permalink(); ?>"><?php } ?>
												<?php the_title(); ?>
											<?php
											if ( ! is_single() && ! ( $using_masonry == true && $masonry_type == 'classic_enhanced' ) ) {
												?>
  									</a> 
										<?php } ?>
									</h<?php echo esc_attr( $h_num ); ?>>

									
								</div><!--/post-header-->


								<?php

							} //not single



						  $video_embed  = get_post_meta( $post->ID, '_nectar_video_embed', true );
						  $video_m4v    = get_post_meta( $post->ID, '_nectar_video_m4v', true );
						  $video_ogv    = get_post_meta( $post->ID, '_nectar_video_ogv', true );
						  $video_poster = get_post_meta( $post->ID, '_nectar_video_poster', true );

if ( ! empty( $video_embed ) || ! empty( $video_m4v ) ) {

	$wp_version = floatval( get_bloginfo( 'version' ) );

	// video embed
	if ( ! empty( $video_embed ) ) {

					echo '<div class="video">' . do_shortcode( $video_embed ) . '</div>';

	}

	// self hosted video post 3-6
	elseif ( $wp_version >= '3.6' ) {

		if ( ! empty( $video_m4v ) || ! empty( $video_ogv ) ) {

			$video_output = '[video ';

			if ( ! empty( $video_m4v ) ) {
				$video_output .= 'mp4="' . $video_m4v . '" '; }
			if ( ! empty( $video_ogv ) ) {
				$video_output .= 'ogv="' . $video_ogv . '"'; }

			$video_output .= ' poster="' . $video_poster . '"]';

			echo '<div class="video">' . do_shortcode( $video_output ) . '</div>';
		}
	}
}



if ( ! is_single() ) {

	// if no excerpt is set
	global $post;

	if ( empty( $post->post_excerpt ) && $use_excerpt != 'true' ) {
		the_content( '<span class="continue-reading">' . __( 'Continue Reading', 'salient' ) . '</span>' );
	}

	// excerpt
	else {
		echo '<div class="excerpt">';
		$excerpt_length = ( ! empty( $options['blog_excerpt_length'] ) ) ? intval( $options['blog_excerpt_length'] ) : 15;

		the_excerpt();

		echo '</div>';
		echo '<a class="more-link" href="' . esc_url( get_permalink() ) . '"><span class="continue-reading">' . __( 'Continue Reading', 'salient' ) . '</span></a>';
	}

	?>

								 

							</div><!--article-content-wrap-->

<?php } //not single 


						if ( is_single() ) {
							// on the single post page display the content
							the_content( '<span class="continue-reading">' . __( 'Read More', 'salient' ) . '</span>' );
						}

						global $options;
						if ( $options['display_tags'] == true ) {

							if ( is_single() && has_tag() ) {

								echo '<div class="post-tags"><h4>' . esc_html__( 'Tags:', 'salient' ) . '</h4>';
								the_tags( '', '', '' );
								echo '<div class="clear"></div></div> ';

							}
						}
						?>

				</div><!--/content-inner-->


				<?php
			}

		 /**
 		 * All Other Styles
 		 */
			else {
			?>
			
			<div class="content-inner">
					
				<?php
				if ( ! is_single() && ( $using_masonry == true && $masonry_type == 'classic_enhanced' ) ||
						  ! is_single() && ( $using_masonry == true && $masonry_type == 'material' ) ) {

					?>
						   <a class="entire-meta-link" href="<?php the_permalink(); ?>"></a>
						  <?php
				}

				if ( $meta_overlaid_style == true ) {

					if ( $masonry_type == 'auto_meta_overlaid_spaced' && ! is_single() ) {
						echo '<span class="play"><span class="inner-wrap"><svg version="1.1"
								 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="600px" height="800px" x="0px" y="0px" viewBox="0 0 600 800" enable-background="new 0 0 600 800" xml:space="preserve"><path fill="none" d="M0-1.79v800L600,395L0-1.79z"></path> </svg></span></span>';
					}

					if ( has_post_thumbnail() ) {
						 $img_size = ( $blog_type == 'masonry-blog-sidebar' && substr( $layout, 0, 3 ) != 'std' || $blog_type == 'masonry-blog-fullwidth' && substr( $layout, 0, 3 ) != 'std' || $blog_type == 'masonry-blog-full-screen-width' && substr( $layout, 0, 3 ) != 'std' || $layout == 'masonry-blog-sidebar' || $layout == 'masonry-blog-fullwidth' || $layout == 'masonry-blog-full-screen-width' ) ? 'large' : 'full';
						 $img_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
						if ( ! is_single() ) {
							if ( $using_masonry == true && $masonry_type == 'auto_meta_overlaid_spaced' ) {
								echo '<a href="' . esc_url( get_permalink() ) . '"></a><span class="post-featured-img" style="background-image: url(' . get_the_post_thumbnail_url( $post->ID, 'medium_featured', array( 'title' => '' ) ) . ');"></span>';
							} else {
								echo '<a href="' . esc_url( get_permalink() ) . '"><span class="post-featured-img">' . get_the_post_thumbnail( $post->ID, $img_size, array( 'title' => '' ) ) . '</span></a>';
							}
						}
					} else {

						// no image added
						$img_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
						switch ( $img_size ) {
							case 'large_featured':
								$no_image_size = 'no-blog-item-large-featured.jpg';
								break;
							case 'wide_tall':
								$no_image_size = 'no-portfolio-item-tiny.jpg';
								break;
							default:
								$no_image_size = 'no-portfolio-item-tiny.jpg';
								break;
						}
						if ( ! is_single() ) {
							echo '<a href="' . esc_url( get_permalink() ) . '"><span class="post-featured-img"><img src="' . get_template_directory_uri() . '/img/' . $no_image_size . '" alt="no image added yet." /></span></a>';
						}
					}
				} elseif ( ! ( $using_masonry == true && $masonry_type == 'classic_enhanced' ) && ! ( $using_masonry == true && $masonry_type == 'material' ) && ! is_single() ) {

					  $video_embed  = get_post_meta( $post->ID, '_nectar_video_embed', true );
					  $video_m4v    = get_post_meta( $post->ID, '_nectar_video_m4v', true );
					  $video_ogv    = get_post_meta( $post->ID, '_nectar_video_ogv', true );
					  $video_poster = get_post_meta( $post->ID, '_nectar_video_poster', true );

					if ( ! empty( $video_embed ) || ! empty( $video_m4v ) ) {

						$wp_version = floatval( get_bloginfo( 'version' ) );

						// video embed
						if ( ! empty( $video_embed ) ) {

							echo '<div class="video">' . do_shortcode( $video_embed ) . '</div>';

						}
						// self hosted video post 3-6
						elseif ( $wp_version >= '3.6' ) {

							if ( ! empty( $video_m4v ) || ! empty( $video_ogv ) ) {

									 $video_output = '[video ';

								if ( ! empty( $video_m4v ) ) {
									$video_output .= 'mp4="' . $video_m4v . '" '; }
								if ( ! empty( $video_ogv ) ) {
									$video_output .= 'ogv="' . $video_ogv . '"'; }

									 $video_output .= ' poster="' . $video_poster . '"]';

								 echo '<div class="video">' . do_shortcode( $video_output ) . '</div>';
							}
						}
					}
				}

				
				if ( ! is_single() ) { 

					if ( $using_masonry == true && $masonry_type == 'classic_enhanced' || $using_masonry == true && $masonry_type == 'material' ) {
						echo '<span class="meta-category">';
						$categories = get_the_category();
						if ( ! empty( $categories ) ) {
							$output = null;
							foreach ( $categories as $category ) {
								$output .= '<a class="' . $category->slug . '" href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
							}
							echo trim( $output );
						}
						echo '</span>'; }
					?>

					<div class="article-content-wrap">
						
								<?php
								if ( $using_masonry == true && $masonry_type == 'auto_meta_overlaid_spaced' ) {
									echo '<span class="meta-category">';
									$categories = get_the_category();
									if ( ! empty( $categories ) ) {
										$output = null;
										foreach ( $categories as $category ) {
												$output .= '<a class="' . $category->slug . '" href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
										}
											echo trim( $output );
									}
									echo '</span>'; }
								
								
						if ( $using_masonry == true && $masonry_type == 'classic_enhanced' && ! has_post_thumbnail() && $masonry_item_sizing != 'large_featured' || $using_masonry == true && $masonry_type == 'classic_enhanced' && has_post_thumbnail() && $masonry_item_sizing == 'wide_tall' ) {
							echo '<div class="video-play-button"></div>';}
						
						?>

						<div class="post-header">
							<?php
							$h_num = '2';
							if ( $using_masonry == true && $masonry_type == 'classic_enhanced' ) {
								echo '<span>' . get_the_date() . '</span>';
								$h_num = '3';
							} elseif ( $using_masonry == true && $masonry_type == 'material' || $using_masonry == true && $masonry_type == 'auto_meta_overlaid_spaced' ) {
								$h_num = '3';
							}
							?>
								
							<h<?php echo esc_attr( $h_num ); ?> class="title">
								<?php if ( ! is_single() && ! ( $using_masonry == true && $masonry_type == 'classic_enhanced' ) ) { ?> 
									<a href="<?php the_permalink(); ?>"><?php } ?>
										<?php the_title(); ?>
									<?php
									if ( ! is_single() && ! ( $using_masonry == true && $masonry_type == 'classic_enhanced' ) ) {
										?>
  							</a> 
								<?php } ?>
							</h<?php echo esc_attr( $h_num ); ?>>

							<?php if ( ! ( $masonry_type == 'classic_enhanced' && $using_masonry == true ) && ! ( $using_masonry == true && $masonry_type == 'material' ) ) { ?> 
								<span class="meta-author"><span><?php echo esc_html__( 'By', 'salient' ); ?></span> <?php the_author_posts_link(); ?></span> <span class="meta-category">| <?php the_category( ', ' ); ?></span> <span class="meta-comment-count">| <a href="<?php comments_link(); ?>">
								<?php comments_number( esc_html__( 'No Comments', 'salient' ), esc_html__( 'One Comment ', 'salient' ), esc_html__( '% Comments', 'salient' ) ); ?></a></span>
							<?php } ?>
						</div><!--/post-header-->

					<?php
					if ( $meta_overlaid_style == false ) {
							// if no excerpt is set
							global $post;
						if ( empty( $post->post_excerpt ) && $use_excerpt != 'true' && ! ( $using_masonry == true && $masonry_type == 'classic_enhanced' ) && ! ( $using_masonry == true && $masonry_type == 'material' ) ) {
							the_content( '<span class="continue-reading">' . __( 'Read More', 'salient' ) . '</span>' );
						}

							// excerpt
						else {
							echo '<div class="excerpt">';
							$excerpt_length = ( ! empty( $options['blog_excerpt_length'] ) ) ? intval( $options['blog_excerpt_length'] ) : 15;

							if ( $using_masonry == true && $masonry_type == 'classic_enhanced' ) {

								if ( $masonry_item_sizing == 'wide_tall' && ! empty( $post->post_excerpt ) ) {

									echo the_excerpt();

								} elseif ( $masonry_item_sizing == 'large_featured' ) {

									echo nectar_excerpt( $excerpt_length * 2 );
								} else {
									echo nectar_excerpt( $excerpt_length );
								}
							} elseif ( $using_masonry == true && $masonry_type == 'material' ) {
								echo nectar_excerpt( $excerpt_length );
							} else {
								the_excerpt();
							}


							echo '</div>';

							if ( function_exists( 'get_avatar' ) && $using_masonry == true && $masonry_type == 'material' ) {
									 echo '<div class="grav-wrap"><a href="' . get_author_posts_url( $post->post_author ) . '">' . get_avatar( get_the_author_meta( 'email' ), 70, null, get_the_author() ) . '</a><div class="text"><a href="' . get_author_posts_url( $post->post_author ) . '" rel="author">' . get_the_author() . '</a><span>' . get_the_date() . '</span></div></div>'; }


							if ( ! ( $using_masonry == true && $masonry_type == 'material' ) ) {
								echo '<a class="more-link" href="' . esc_url( get_permalink() ) . '"><span class="continue-reading">' . __( 'Read More', 'salient' ) . '</span></a>';
							}
						}
					}
					?>

					</div><!--article-content-wrap-->
					
				<?php } 
				
			  
				if ( is_single() ) {


						global $options;
						$hide_featrued_image = ( ! empty( $options['blog_hide_featured_image'] ) ) ? $options['blog_hide_featured_image'] : '0';

						$video_embed  = get_post_meta( $post->ID, '_nectar_video_embed', true );
						$video_m4v    = get_post_meta( $post->ID, '_nectar_video_m4v', true );
						$video_ogv    = get_post_meta( $post->ID, '_nectar_video_ogv', true );
						$video_poster = get_post_meta( $post->ID, '_nectar_video_poster', true );

					if ( ! empty( $video_embed ) && $hide_featrued_image != '1' || ! empty( $video_m4v ) && $hide_featrued_image != '1' ) {

							$wp_version = floatval( get_bloginfo( 'version' ) );

							// video embed
						if ( ! empty( $video_embed ) ) {

									 echo '<div class="video">' . do_shortcode( $video_embed ) . '</div>';

						}
							// self hosted video post 3-6
						elseif ( $wp_version >= '3.6' ) {

							if ( ! empty( $video_m4v ) || ! empty( $video_ogv ) ) {

								 $video_output = '[video ';

								if ( ! empty( $video_m4v ) ) {
									$video_output .= 'mp4="' . $video_m4v . '" '; }
								if ( ! empty( $video_ogv ) ) {
									   $video_output .= 'ogv="' . $video_ogv . '"'; }

										$video_output .= ' poster="' . $video_poster . '"]';

										echo '<div class="video">' . do_shortcode( $video_output ) . '</div>';

							} // not empty m4v ogv

						} // greater than 3-6 wp

					} // not empty video embed




					// on the single post page display the content
					the_content( '<span class="continue-reading">' . __( 'Read More', 'salient' ) . '</span>' );
				}


				global $options;
				if ( $options['display_tags'] == true ) {

					if ( is_single() && has_tag() ) {

						echo '<div class="post-tags"><h4>' . esc_html__( 'Tags:', 'salient' ) . '</h4>';
						the_tags( '', '', '' );
						echo '<div class="clear"></div></div> ';

					}
				}
				?>
				
			</div><!--/content-inner-->

			<?php } // other styles ?>
			
		</div><!--/post-content-->

	</div><!--/inner-wrap-->
		
</article><!--/article-->
