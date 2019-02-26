<?php
/**
 * Portfolio single content area
 *
 * @package Salient WordPress Theme
 * @subpackage Partials
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fwp = get_post_meta( $post->ID, '_nectar_portfolio_item_layout', true );
if ( empty( $fwp ) ) {
	$fwp = 'false';
}

global $post;

$options                   = get_nectar_theme_options();
$enable_gallery_slider     = get_post_meta( get_the_ID(), '_nectar_gallery_slider', true );
$hidden_featured_media     = get_post_meta( $post->ID, '_nectar_hide_featured', true );
$hidden_project_title      = get_post_meta( $post->ID, '_nectar_hide_title', true );
$portfolio_remove_comments = ( ! empty( $options['portfolio_remove_comments'] ) ) ? $options['portfolio_remove_comments'] : '0';
$theme_skin                = ( ! empty( $options['theme-skin'] ) && $options['theme-skin'] == 'ascend' ) ? 'ascend' : 'default';

?>

<div class="post-area col <?php if ( $fwp != 'enabled' ) { echo 'span_9'; } else { echo 'span_12'; } ?>">
  
	<?php

	if ( ! post_password_required() ) {

		$video_embed  = get_post_meta( $post->ID, '_nectar_video_embed', true );
		$video_m4v    = get_post_meta( $post->ID, '_nectar_video_m4v', true );
		$video_ogv    = get_post_meta( $post->ID, '_nectar_video_ogv', true );
		$video_poster = get_post_meta( $post->ID, '_nectar_video_poster', true );

		// Gallery
		if ( class_exists( 'MultiPostThumbnails' ) && MultiPostThumbnails::has_post_thumbnail( get_post_type(), 'second-slide' ) || ! empty( $enable_gallery_slider ) && $enable_gallery_slider == 'on' ) {

			if ( floatval( get_bloginfo( 'version' ) ) < '3.6' ) {
        
			} else {

				if ( ! empty( $enable_gallery_slider ) && $enable_gallery_slider == 'on' ) {
					$gallery_ids = grab_ids_from_gallery();
					?>
		
    		  <div class="flex-gallery"> 
    			 <ul class="slides">
    					  <?php
    						foreach ( $gallery_ids as $image_id ) {
    							echo '<li>' . wp_get_attachment_image( $image_id, '', false ) . '</li>';
    						}
    						?>
    			  </ul>
    			</div><!--/gallery-->
			   
					<?php
				}
			}
		}

		// Video
		elseif ( ! empty( $video_embed ) && $hidden_featured_media != 'on' || ! empty( $video_m4v ) && $hidden_featured_media != 'on' ) {


			// video embed
			if ( ! empty( $video_embed ) ) {

				 echo '<div class="video">' . do_shortcode( $video_embed ) . '</div>';

			}
			// self hosted video pre 3-6
			elseif ( ! empty( $video_m4v ) && floatval( get_bloginfo( 'version' ) ) < '3.6' ) {

				echo '<div class="video">';
				   nectar_video( $post->ID );
				echo '</div>';

			}
			// self hosted video post 3-6
			elseif ( floatval( get_bloginfo( 'version' ) ) >= '3.6' ) {

				if ( ! empty( $video_m4v ) || ! empty( $video_ogv ) ) {

					$video_output = '[video ';

					if ( ! empty( $video_m4v ) ) {
						$video_output .= 'mp4="' . esc_url( $video_m4v ) . '" '; }
					if ( ! empty( $video_ogv ) ) {
						$video_output .= 'ogv="' . esc_url( $video_ogv ) . '"'; }

					$video_output .= ' poster="' . esc_url( $video_poster ) . '" height="720" width="1280"]';

					echo '<div class="video">' . do_shortcode( $video_output ) . '</div>'; // WPCS: XSS ok.
				}
			}
		}

		// Regular Featured Img
		elseif ( $hidden_featured_media != 'on' ) {

			if ( has_post_thumbnail() ) {
				echo get_the_post_thumbnail( $post->ID, 'full', array( 'title' => '' ) );
			}
		}
	}
	?>
  
	<?php
	// extra content
	if ( ! post_password_required() ) {

		$portfolio_extra_content = get_post_meta( $post->ID, '_nectar_portfolio_extra_content', true );

		if ( ! empty( $portfolio_extra_content ) ) {
			echo '<div id="portfolio-extra">';

			$extra_content = shortcode_empty_paragraph_fix( apply_filters( 'the_content', $portfolio_extra_content ) );
			echo do_shortcode( $extra_content );

			echo '</div>';
		}
	} elseif ( $fwp == 'enabled' ) {
		the_content();
	}


	if ( comments_open() && $theme_skin != 'ascend' && $portfolio_remove_comments != '1' ) {
		?>
  
	<div class="comments-section">
		   <?php comments_template(); ?>
	</div>
  
	<?php } ?>  
  
</div><!--/post-area-->
