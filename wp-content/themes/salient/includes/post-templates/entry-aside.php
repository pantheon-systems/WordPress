<?php
/**
 * Aside Post Template
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
$masonry_type        = ( ! empty( $options['blog_masonry_type'] ) ) ? $options['blog_masonry_type'] : 'classic';
$using_masonry       = null; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $masonry_item_sizing ); ?>>

	<div class="inner-wrap animated">

		<div class="post-content">
			
			<?php if ( ! is_single() ) { ?>
				
				<?php

				global $layout;

				$extra_class = '';
				if ( ! has_post_thumbnail() ) {
					$extra_class = 'no-img';
				}
				?>
				
				<div class="post-meta <?php echo esc_attr( $extra_class ); ?>">
					
					<?php
					$blog_type   = $options['blog_type'];
					$use_excerpt = ( ! empty( $options['blog_auto_excerpt'] ) && $options['blog_auto_excerpt'] == '1' ) ? 'true' : 'false';
					?>
					
					<div class="date">
						<?php
						if (
						$blog_type == 'masonry-blog-sidebar' && substr( $layout, 0, 3 ) != 'std' ||
						$blog_type == 'masonry-blog-fullwidth' && substr( $layout, 0, 3 ) != 'std' ||
						$blog_type == 'masonry-blog-full-screen-width' && substr( $layout, 0, 3 ) != 'std' ||
						$layout == 'masonry-blog-sidebar' || $layout == 'masonry-blog-fullwidth' || $layout == 'masonry-blog-full-screen-width' ) {
							$using_masonry = true;
							echo get_the_date();
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
					
					<?php
					if ( $using_masonry == true && $masonry_type == 'meta_overlaid' ) {
					} else {
						?>
					 
						<div class="nectar-love-wrap">
							<?php
							if ( function_exists( 'nectar_love' ) ) {
								nectar_love();}
							?>
						</div><!--/nectar-love-wrap-->	
					<?php } ?>
								
				</div><!--/post-meta-->
				
			<?php } ?>
			
			<div class="content-inner">
				
				<?php
				if ( has_post_thumbnail() ) {

					 $img_size = ( $blog_type == 'masonry-blog-sidebar' && substr( $layout, 0, 3 ) != 'std' || $blog_type == 'masonry-blog-fullwidth' && substr( $layout, 0, 3 ) != 'std' || $blog_type == 'masonry-blog-full-screen-width' && substr( $layout, 0, 3 ) != 'std' || $layout == 'masonry-blog-sidebar' || $layout == 'masonry-blog-fullwidth' || $layout == 'masonry-blog-full-screen-width' ) ? 'large' : 'full';
					if ( $using_masonry == true && $masonry_type == 'meta_overlaid' ) {
						$img_size = ( ! empty( $masonry_item_sizing ) ) ? $masonry_item_sizing : 'portfolio-thumb';
					}
					if ( ! is_single() ) {
						echo '<a href="' . esc_url( get_permalink() ) . '"><span class="post-featured-img">' . get_the_post_thumbnail( $post->ID, $img_size, array( 'title' => '' ) ) . '</span></a>'; }

					 global $options;
					 $hide_featrued_image = ( ! empty( $options['blog_hide_featured_image'] ) ) ? $options['blog_hide_featured_image'] : '0';
					if ( is_single() && $hide_featrued_image != '1' ) {
						echo '<span class="post-featured-img">' . get_the_post_thumbnail( $post->ID, 'full', array( 'title' => '' ) ) . '</span>';
					}
				}
				?>
				
				<div class="aside-inner">
					<h2 class="title"><?php the_content( '<span class="continue-reading">' . __( 'Read More', 'salient' ) . '</span>' ); ?></h2>
					<span title="Aside" class="icon"></span>
					
					<?php if ( ! is_single() ) { ?>
					<div class="post-header">
						<span class="meta-author"><span><?php echo esc_html__( 'By', 'salient' ); ?></span> <?php the_author_posts_link(); ?></span> <span class="meta-category">| <?php the_category( ', ' ); ?></span> <span class="meta-comment-count">| <a href="<?php comments_link(); ?>">
						<?php comments_number( esc_html__( 'No Comments', 'salient' ), esc_html__( 'One Comment ', 'salient' ), esc_html__( '% Comments', 'salient' ) ); ?></a></span>
					</div><!--/post-header-->
					
				<?php } ?>
				</div><!--/link-inner-->


				<?php
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
			
		</div><!--/post-content-->

	</div><!--/inner-wrap-->
		
</article><!--/article-->
