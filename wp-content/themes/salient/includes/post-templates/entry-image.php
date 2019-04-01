<?php
/**
 * Image Post Template
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

$use_excerpt = ( ! empty( $options['blog_auto_excerpt'] ) && $options['blog_auto_excerpt'] == '1' ) ? 'true' : 'false';

if ( $using_masonry == true && ! is_single() ) {
	$nectar_post_class_additions = $masonry_item_sizing . ' masonry-blog-item';
} else {
	$nectar_post_class_additions = $masonry_item_sizing;
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $nectar_post_class_additions ); ?>>

	<div class="inner-wrap animated">

		<div class="post-content">
			
			<?php if ( ! is_single() ) { 

				$extra_class = '';
				global $layout;
				if ( ! has_post_thumbnail() ) {
					$extra_class = 'no-img';
				}
				?>
				
				<div class="post-meta <?php echo esc_attr( $extra_class ); ?>">
					
					<?php
					$blog_type = $options['blog_type'];
					?>
					
					<div class="date">
						<?php
						if (
						$blog_type == 'masonry-blog-sidebar' && substr( $layout, 0, 3 ) != 'std' ||
						$blog_type == 'masonry-blog-fullwidth' && substr( $layout, 0, 3 ) != 'std' ||
						$blog_type == 'masonry-blog-full-screen-width' && substr( $layout, 0, 3 ) != 'std' ||
						$layout == 'masonry-blog-sidebar' || $layout == 'masonry-blog-fullwidth' || $layout == 'masonry-blog-full-screen-width' ) {
							echo get_the_date();
							$using_masonry = true;
						} else {

							if ( $blog_standard_type != 'minimal' ) {
								?>
						
								<span class="month"><?php the_time( 'M' ); ?></span>
								<span class="day"><?php the_time( 'd' ); ?></span>
								<?php
								global $options;
								if ( ! empty( $options['display_full_date'] ) && $options['display_full_date'] == 1 ) {
									echo '<span class="year">' . get_the_time( 'Y' ) . '</span>';
								}
							} else {
								echo '<a href="' . esc_url( get_permalink() ) . '">' . get_the_date() . '</a>';
							}
						}
						?>
					</div><!--/date-->
					
					<?php
					if ( $using_masonry == true && $masonry_type == 'meta_overlaid' ) {
					} else {

						if ( ! ( $using_masonry != true && $blog_standard_type == 'minimal' ) ) {
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
				
				<?php
			}

			$meta_overlaid_style = ( $using_masonry == true && $masonry_type == 'meta_overlaid' ) ? true : false;
			?>


			<?php
			/**
  		 * Minimal Standard Style
  		 */
			if ( $using_masonry != true && $blog_standard_type == 'minimal' ) {
				

				 if ( ! is_single() ) { ?>
					 
					<div class="post-author">
						<?php
						if ( function_exists( 'get_avatar' ) ) {
							echo '<div class="grav-wrap"><a href="' . get_author_posts_url( $post->post_author ) . '">' . get_avatar( get_the_author_meta( 'email' ), 90 ) . '</a></div>'; }
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

					<?php
					if ( has_post_thumbnail() ) {

						 global $options;
						 $hide_featrued_image           = ( ! empty( $options['blog_hide_featured_image'] ) ) ? $options['blog_hide_featured_image'] : '0';
						 $single_post_header_inherit_fi = ( ! empty( $options['blog_post_header_inherit_featured_image'] ) ) ? $options['blog_post_header_inherit_featured_image'] : '0';
						if ( is_single() && $hide_featrued_image != '1' && $single_post_header_inherit_fi != '1' ) {
							echo '<span class="post-featured-img">' . get_the_post_thumbnail( $post->ID, 'full', array( 'title' => '' ) ) . '</span>';
						}
					}
					
						 
						 if ( ! is_single() ) { ?> 

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
								if ( has_post_thumbnail() ) {

									if ( ! is_single() ) {

										echo '<a href="' . esc_url( get_permalink() ) . '"><span class="post-featured-img">' . get_the_post_thumbnail( $post->ID, $img_size, array( 'title' => '' ) ) . '</span></a>';
									}
								}

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
  		 * All other styles
  		 */
			else {
			
			?>


			<div class="content-inner">
				
				<?php
				if ( has_post_thumbnail() ) {

					if ( ! is_single() ) {
						$img_size = ( $blog_type == 'masonry-blog-sidebar' && substr( $layout, 0, 3 ) != 'std' || $blog_type == 'masonry-blog-fullwidth' && substr( $layout, 0, 3 ) != 'std' || $blog_type == 'masonry-blog-full-screen-width' && substr( $layout, 0, 3 ) != 'std' || $layout == 'masonry-blog-sidebar' || $layout == 'masonry-blog-fullwidth' || $layout == 'masonry-blog-full-screen-width' ) ? 'large' : 'full';
						echo '<a href="' . esc_url( get_permalink() ) . '"><span class="post-featured-img">' . get_the_post_thumbnail( $post->ID, $img_size, array( 'title' => '' ) ) . '</span></a>';
					}

					 global $options;
					 $hide_featrued_image           = ( ! empty( $options['blog_hide_featured_image'] ) ) ? $options['blog_hide_featured_image'] : '0';
					 $single_post_header_inherit_fi = ( ! empty( $options['blog_post_header_inherit_featured_image'] ) ) ? $options['blog_post_header_inherit_featured_image'] : '0';
					if ( is_single() && $hide_featrued_image != '1' && $single_post_header_inherit_fi != '1' ) {
						echo '<span class="post-featured-img">' . get_the_post_thumbnail( $post->ID, 'full', array( 'title' => '' ) ) . '</span>';
					}
				} elseif ( $meta_overlaid_style == true ) {

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
					echo '<a href="' . esc_url( get_permalink() ) . '"><span class="post-featured-img"><img src="' . get_template_directory_uri() . '/img/' . $no_image_size . '" alt="no image added yet." /></span></a>';

				}

				
				if ( ! is_single() ) { ?>
					<div class="article-content-wrap">
						<div class="post-header">
							<h2 class="title">
							<?php
							if ( ! is_single() ) {
								?>
								 <a href="<?php the_permalink(); ?>"><?php } ?><?php the_title(); ?>
								 <?php
									if ( ! is_single() ) {
										?>
								  </a> <?php } ?></h2>
							<span class="meta-author"><span><?php echo esc_html__( 'By', 'salient' ); ?></span> <?php the_author_posts_link(); ?></span> <span class="meta-category">| <?php the_category( ', ' ); ?></span> <span class="meta-comment-count">| <a href="<?php comments_link(); ?>">
							<?php comments_number( esc_html__( 'No Comments', 'salient' ), esc_html__( 'One Comment ', 'salient' ), esc_html__( '% Comments', 'salient' ) ); ?></a></span>
						</div><!--/post-header-->
					<?php

					if ( $meta_overlaid_style == false ) {

						// if no excerpt is set
						global $post;
						if ( empty( $post->post_excerpt ) && $use_excerpt != 'true' ) {
							the_content( '<span class="continue-reading">' . __( 'Read More', 'salient' ) . '</span>' );
						}

						// excerpt
						else {
							echo '<div class="excerpt">';
								the_excerpt();
							echo '</div>';
							echo '<a class="more-link" href="' . esc_url( get_permalink() ) . '"><span class="continue-reading">' . __( 'Read More', 'salient' ) . '</span></a>';
						}
					}

					?>

					</div><!--article-content-wrap-->
				
				<?php } 
				
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

			<?php } // other styles ?>
			
		</div><!--/post-content-->

	</div><!--/inner-wrap-->
		
</article><!--/article-->
