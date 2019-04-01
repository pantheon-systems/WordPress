<?php
/**
 * Salient blog related functions
 *
 * @package Salient WordPress Theme
 * @subpackage helpers
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



// excerpt length
if ( ! function_exists( 'excerpt_length' ) ) {
	function excerpt_length( $length ) {

		global $nectar_options;
		$excerpt_length = ( ! empty( $nectar_options['blog_excerpt_length'] ) ) ? intval( $nectar_options['blog_excerpt_length'] ) : 30;

		return $excerpt_length;
	}
}

add_filter( 'excerpt_length', 'excerpt_length', 999 );





// custom excerpt ending
if ( ! function_exists( 'nectar_excerpt_more' ) ) {
	function nectar_excerpt_more( $more ) {
		return '...';
	}
}
add_filter( 'excerpt_more', 'nectar_excerpt_more' );






if ( ! function_exists( 'nectar_set_post_views' ) ) {

	function nectar_set_post_views() {

		global $post;

		if ( get_post_type() == 'post' && is_single() ) {

			$post_id = $post->ID;

			if ( ! empty( $post_id ) ) {

				$the_view_count = get_post_meta( $post_id, 'nectar_blog_post_view_count', true );

				if ( $the_view_count != '' ) {

					$the_view_count = intval( $the_view_count );
					$the_view_count++;
					update_post_meta( $post_id, 'nectar_blog_post_view_count', $the_view_count );

				} else {

					$the_view_count = 0;
					delete_post_meta( $post_id, 'nectar_blog_post_view_count' );
					add_post_meta( $post_id, 'nectar_blog_post_view_count', '0' );

				}
			}
		}

	}
}

add_action( 'wp_head', 'nectar_set_post_views' );







if ( ! function_exists( 'nectar_excerpt' ) ) {

	function nectar_excerpt( $limit ) {

		if ( has_excerpt() ) {
			$the_excerpt = get_the_excerpt();
			$the_excerpt = preg_replace( '/\[[^\]]+\]/', '', $the_excerpt );  // strip shortcodes, keep shortcode content
			return wp_trim_words( $the_excerpt, $limit );
		} else {
			$the_content = get_the_content();
			$the_content = preg_replace( '/\[[^\]]+\]/', '', $the_content );  // strip shortcodes, keep shortcode content
			return wp_trim_words( $the_content, $limit );
		}
	}
}





// remove the page jump when clicking read more button
function nectar_remove_more_jump_link( $link ) {
	$offset = strpos( $link, '#more-' );
	if ( $offset ) {
		$end = strpos( $link, '"', $offset );
	}
	if ( $end ) {
		$link = substr_replace( $link, '', $offset, $end - $offset );
	}
	return $link;
}
add_filter( 'the_content_more_link', 'nectar_remove_more_jump_link' );










function nectar_remove_category_list_rel( $output ) {
	// Remove rel attribute from the category list
	return str_replace( ' rel="category tag"', '', $output );
}

add_filter( 'wp_list_categories', 'nectar_remove_category_list_rel' );
add_filter( 'the_category', 'nectar_remove_category_list_rel' );









function nectar_blog_social_sharing() {

	 global $nectar_options;
	 global $post;

	 ob_start();

	 $fullscreen_header              = ( ! empty( $nectar_options['blog_header_type'] ) && $nectar_options['blog_header_type'] == 'fullscreen' && is_singular( 'post' ) ) ? true : false;
	 $fullscreen_class               = ( $fullscreen_header == true ) ? 'hide-share-count' : null;
	$blog_header_type                = ( ! empty( $nectar_options['blog_header_type'] ) ) ? $nectar_options['blog_header_type'] : 'default';
	 $remove_single_post_nectar_love = ( ! empty( $nectar_options['blog_remove_single_nectar_love'] ) ) ? $nectar_options['blog_remove_single_nectar_love'] : '0';

	if ( ! empty( $nectar_options['blog_social'] ) && $nectar_options['blog_social'] == 1 || $fullscreen_header == true ) {

		  echo '<div class="nectar-social ' . $fullscreen_class . '">';

		if ( $fullscreen_header == false ) {

			if ( $remove_single_post_nectar_love != '1' ) {
				echo '<span class="n-shortcode">' . nectar_love( 'return' ) . '</span>';
			}

			// facebook
			if ( ! empty( $nectar_options['blog-facebook-sharing'] ) && $nectar_options['blog-facebook-sharing'] == 1 ) {
				echo "<a class='facebook-share nectar-sharing' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'>  <i class='fa fa-facebook'></i> <span class='social-text'>" . esc_html__( 'Share', 'salient' ) . "</span> <span class='count'></span></a>";
			}
			// twitter
			if ( ! empty( $nectar_options['blog-twitter-sharing'] ) && $nectar_options['blog-twitter-sharing'] == 1 ) {
				echo "<a class='twitter-share nectar-sharing' href='#' title='" . esc_attr__( 'Tweet this', 'salient' ) . "'> <i class='fa fa-twitter'></i> <span class='social-text'>" . esc_html__( 'Tweet', 'salient' ) . "</span> <span class='count'></span></a>";
			}

			// google plus
			if ( ! empty( $nectar_options['blog-google-plus-sharing'] ) && $nectar_options['blog-google-plus-sharing'] == 1 ) {
				echo "<a class='google-plus-share nectar-sharing-alt' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-google-plus'></i> <span class='social-text'>" . esc_html__( 'Share', 'salient' ) . "</span> <span class='count'>0</span></a>";
			}

			// linkedIn
			if ( ! empty( $nectar_options['blog-linkedin-sharing'] ) && $nectar_options['blog-linkedin-sharing'] == 1 ) {
				echo "<a class='linkedin-share nectar-sharing' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-linkedin'></i> <span class='social-text'>" . esc_html__( 'Share', 'salient' ) . "</span> <span class='count'> </span></a>";
			}

			// pinterest
			if ( ! empty( $nectar_options['blog-pinterest-sharing'] ) && $nectar_options['blog-pinterest-sharing'] == 1 ) {
				echo "<a class='pinterest-share nectar-sharing' href='#' title='" . esc_attr__( 'Pin this', 'salient' ) . "'> <i class='fa fa-pinterest'></i> <span class='social-text'>" . esc_html__( 'Pin', 'salient' ) . "</span> <span class='count'></span></a>";
			}
		} else {
			// facebook
			echo "<a class='facebook-share nectar-sharing' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-facebook'></i> <span class='count'></span></a>";
			echo "<a class='twitter-share nectar-sharing' href='#' title='" . esc_attr__( 'Tweet this', 'salient' ) . "'> <i class='fa fa-twitter'></i> <span class='count'></span></a>";
			echo "<a class='google-plus-share nectar-sharing-alt' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-google-plus'></i> <span class='count'>0</span></a>";
			echo "<a class='linkedin-share nectar-sharing' href='#' title='" . esc_attr__( 'Share this', 'salient' ) . "'> <i class='fa fa-linkedin'></i> <span class='count'> </span></a>";
			echo "<a class='pinterest-share nectar-sharing' href='#' title='" . esc_attr__( 'Pin this', 'salient' ) . "'> <i class='fa fa-pinterest'></i> <span class='count'></span></a>";

		}

			echo '</div>';

	}

		$sharing_output = ob_get_contents();
		ob_end_clean();

		echo $sharing_output; // WPCS: XSS ok.
}






function nectar_next_post_display() {

	 global $post;
	 global $nectar_options;

	 $post_header_style            = ( ! empty( $nectar_options['blog_header_type'] ) ) ? $nectar_options['blog_header_type'] : 'default';
	 $post_pagination_style        = ( ! empty( $nectar_options['blog_next_post_link_style'] ) ) ? $nectar_options['blog_next_post_link_style'] : 'fullwidth_next_only';
	 $post_pagination_style_output = ( $post_pagination_style == 'contained_next_prev' ) ? 'fullwidth_next_prev' : $post_pagination_style;
	 $full_width_content_class     = ( $post_pagination_style == 'contained_next_prev' ) ? '' : 'full-width-content';

	 ob_start();
	 $next_post = get_previous_post();
	if ( ! empty( $next_post ) && ! empty( $nectar_options['blog_next_post_link'] ) && $nectar_options['blog_next_post_link'] == '1' ||
	   $post_pagination_style == 'contained_next_prev' && ! empty( $nectar_options['blog_next_post_link'] ) && $nectar_options['blog_next_post_link'] == '1' ||
		   $post_pagination_style == 'fullwidth_next_prev' && ! empty( $nectar_options['blog_next_post_link'] ) && $nectar_options['blog_next_post_link'] == '1' ) { ?>

			<div data-post-header-style="<?php echo esc_attr( $post_header_style ); ?>" class="blog_next_prev_buttons wpb_row vc_row-fluid <?php echo esc_attr( $full_width_content_class ); ?> standard_section" data-style="<?php echo esc_attr( $post_pagination_style_output ); ?>" data-midnight="light">

				<?php

				if ( ! empty( $next_post ) ) {
					  $bg       = get_post_meta( $next_post->ID, '_nectar_header_bg', true );
					  $bg_color = get_post_meta( $next_post->ID, '_nectar_header_bg_color', true );
				} else {
					$bg       = '';
					$bg_color = '';
				}

				if ( $post_pagination_style == 'fullwidth_next_prev' || $post_pagination_style == 'contained_next_prev' ) {

					// next & prev
						$previous_post = get_next_post();
						$next_post     = get_previous_post();

						$hidden_class = ( empty( $previous_post ) ) ? 'hidden' : null;
						$only_class   = ( empty( $next_post ) ) ? ' only' : null;
						echo '<ul class="controls"><li class="previous-post ' . $hidden_class . $only_class . '">';

					if ( ! empty( $previous_post ) ) {
						$previous_post_id = $previous_post->ID;
						$bg               = get_post_meta( $previous_post_id, '_nectar_header_bg', true );

						if ( ! empty( $bg ) ) {
							// page header
							echo '<div class="post-bg-img" style="background-image: url(' . $bg . ');"></div>';
						} elseif ( has_post_thumbnail( $previous_post_id ) ) {
							// featured image
							$post_thumbnail_id  = get_post_thumbnail_id( $previous_post_id );
							$post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
							echo '<div class="post-bg-img" style="background-image: url(' . esc_url( $post_thumbnail_url ) . ');"></div>';
						}

							echo '<a href="' . esc_url( get_permalink( $previous_post_id ) ) . '"></a><h3><span>' . esc_html__( 'Previous Post', 'salient' ) . '</span><span class="text">' . wp_kses_post( $previous_post->post_title ) . '
					<svg class="next-arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 39 12"><line class="top" x1="23" y1="-0.5" x2="29.5" y2="6.5" stroke="#ffffff;"></line><line class="bottom" x1="23" y1="12.5" x2="29.5" y2="5.5" stroke="#ffffff;"></line></svg><span class="line"></span></span></h3>';
					}
						echo '</li>';

						$hidden_class = ( empty( $next_post ) ) ? 'hidden' : null;
						$only_class   = ( empty( $previous_post ) ) ? ' only' : null;

						echo '<li class="next-post ' . $hidden_class . $only_class . '">';

					if ( ! empty( $next_post ) ) {
						$next_post_id = $next_post->ID;
						$bg           = get_post_meta( $next_post_id, '_nectar_header_bg', true );

						if ( ! empty( $bg ) ) {
							// page header
							echo '<div class="post-bg-img" style="background-image: url(' . $bg . ');"></div>';
						} elseif ( has_post_thumbnail( $next_post_id ) ) {
							// featured image
							$post_thumbnail_id  = get_post_thumbnail_id( $next_post_id );
							$post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
							echo '<div class="post-bg-img" style="background-image: url(' . esc_url( $post_thumbnail_url ) . ');"></div>';
						}
					}

							echo '<a href="' . esc_url( get_permalink( $next_post_id ) ) . '"></a><h3><span>' . esc_html__( 'Next Post', 'salient' ) . '</span><span class="text">' . wp_kses_post( $next_post->post_title ) . '
					<svg class="next-arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 39 12"><line class="top" x1="23" y1="-0.5" x2="29.5" y2="6.5" stroke="#ffffff;"></line><line class="bottom" x1="23" y1="12.5" x2="29.5" y2="5.5" stroke="#ffffff;"></line></svg><span class="line"></span></span></h3>';

						echo '</li></ul>';

				} else {

					// next only
					if ( ! empty( $bg ) || ! empty( $bg_color ) ) {
						// page header
						if ( ! empty( $bg ) ) {
							echo '<img src="' . esc_url( $bg ) . '" alt="' . get_the_title( $next_post->ID ) . '" />';
						} else {
							echo '<span class="bg-color-only-indicator"></span>';
						}
					} elseif ( has_post_thumbnail( $next_post->ID ) ) {
						// featured image
						$post_thumbnail_id  = get_post_thumbnail_id( $next_post->ID );
						$post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
						echo '<img src="' . esc_url( $post_thumbnail_url ) . '" alt="' . get_the_title( $next_post->ID ) . '" />';
					}
					?>

						<div class="col span_12 dark left">
						<div class="inner">
							<span><?php echo '<i>' . esc_html__( 'Next Post', 'salient' ) . '</i>'; ?></span>
							<?php previous_post_link( '%link', '<h3>%title</h3>' ); ?>
						</div>		
						</div>
						<span class="bg-overlay"></span>
						<span class="full-link"><?php previous_post_link( '%link' ); ?></span>

					<?php } ?>
				
					
			 
		 </div>

		<?php
	}

	$next_post_link_output = ob_get_contents();
	ob_end_clean();

	echo $next_post_link_output; // WPCS: XSS ok.

}










function nectar_related_post_display() {

	global $post;
	global $nectar_options;

	$using_related_posts = ( ! empty( $nectar_options['blog_related_posts'] ) && ! empty( $nectar_options['blog_related_posts'] ) == '1' ) ? true : false;

	if ( $using_related_posts == false ) {
		return;
	}

	ob_start();

	$current_categories = get_the_category( $post->ID );
	if ( $current_categories ) {

		$category_ids = array();
		foreach ( $current_categories as $individual_category ) {
			$category_ids[] = $individual_category->term_id;
		}

		$relatedBlogPosts = array(
			'category__in'        => $category_ids,
			'post__not_in'        => array( $post->ID ),
			'showposts'           => 3,
			'ignore_sticky_posts' => 1,
		);

		$related_posts_query = new WP_Query( $relatedBlogPosts );
		$related_post_count  = $related_posts_query->post_count;

		if ( $related_post_count < 2 ) {
			return;
		}

		$span_num = ( $related_post_count == 2 ) ? 'span_6' : 'span_4';

		$related_title_text        = esc_html__( 'Related Posts', 'salient' );
		$related_post_title_option = ( ! empty( $nectar_options['blog_related_posts_title_text'] ) ) ? wp_kses_post( $nectar_options['blog_related_posts_title_text'] ) : 'Related Posts';

		switch ( $related_post_title_option ) {
			case 'related_posts':
				$related_title_text = esc_html__( 'Related Posts', 'salient' );
				break;

			case 'similar_posts':
				$related_title_text = esc_html__( 'Similar Posts', 'salient' );
				break;

			case 'you_may_also_like':
				$related_title_text = esc_html__( 'You May Also Like', 'salient' );
				break;
			case 'recommended_for_you':
				$related_title_text = esc_html__( 'Recommended For You', 'salient' );
				break;
		}

		$hidden_title_class = null;
		if ( $related_post_title_option == 'hidden' ) {
			$hidden_title_class = 'hidden';
		}

		$using_post_pag     = ( ! empty( $nectar_options['blog_next_post_link'] ) && $nectar_options['blog_next_post_link'] == '1' ) ? 'true' : 'false';
		$related_post_style = ( ! empty( $nectar_options['blog_related_posts_style'] ) ) ? esc_html( $nectar_options['blog_related_posts_style'] ) : 'material';

		echo '<div class="row vc_row-fluid full-width-section related-post-wrap" data-using-post-pagination="' . esc_attr( $using_post_pag ) . '" data-midnight="dark"> <div class="row-bg-wrap"><div class="row-bg"></div></div> <h3 class="related-title ' . $hidden_title_class . '">' . wp_kses_post( $related_title_text ) . '</h3><div class="row span_12 blog-recent related-posts columns-' . esc_attr( $related_post_count ) . '" data-style="' . esc_attr( $related_post_style ) . '" data-color-scheme="light">';
		if ( $related_posts_query->have_posts() ) :
			while ( $related_posts_query->have_posts() ) :
				$related_posts_query->the_post();
				?>
	
	<div class="col <?php echo esc_attr( $span_num ); ?>">
	<div <?php post_class( 'inner-wrap' ); ?>>

					<?php
					if ( has_post_thumbnail() ) {
						$related_image_size = ( $related_post_count == 2 ) ? 'wide_photography' : 'portfolio-thumb';
						echo '<a href="' . esc_url( get_permalink() ) . '" class="img-link"><span class="post-featured-img">' . get_the_post_thumbnail( $post->ID, $related_image_size, array( 'title' => '' ) ) . '</span></a>';
					}
					?>

					<?php
					echo '<span class="meta-category">';
					$categories = get_the_category();
					if ( ! empty( $categories ) ) {
						$output = null;
						foreach ( $categories as $category ) {
							$output .= '<a class="' . esc_attr( $category->slug ) . '" href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
						}
						echo trim( $output );
					}
					echo '</span>';
					?>
		
					<a class="entire-meta-link" href="<?php the_permalink(); ?>"></a>

					<div class="article-content-wrap">
						<div class="post-header">
							<span class="meta"> 
								<?php
								if ( $related_post_style != 'material' ) {
									echo get_the_date();}
								?>
							 </span> 
							<h3 class="title"><?php the_title(); ?></h3>	
						</div><!--/post-header-->
		
						<?php
						if ( function_exists( 'get_avatar' ) && $related_post_style == 'material' ) {
							 echo '<div class="grav-wrap">' . get_avatar( get_the_author_meta( 'email' ), 70, null, get_the_author() ) . '<div class="text"> <a href="' . get_author_posts_url( $post->post_author ) . '">' . get_the_author() . '</a><span>' . get_the_date() . '</span></div></div>'; }

						?>
					</div>
	
					<?php if ( $related_post_style != 'material' ) { ?>
		
	<div class="post-meta">
		<span class="meta-author"> <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"> <i class="icon-default-style icon-salient-m-user"></i> <?php the_author(); ?></a> </span> 
		
						<?php if ( comments_open() ) { ?>
			<span class="meta-comment-count">  <a href="<?php comments_link(); ?>">
				<i class="icon-default-style steadysets-icon-chat-3"></i> <?php comments_number( '0', '1', '%' ); ?></a>
			</span>
		<?php } ?>
		
	</div>
						<?php

}
?>
	 
</div>
</div>
				 <?php

			endwhile;
endif;

		echo '</div></div>';

		wp_reset_postdata();

	}// if has categories
		$related_posts_content = ob_get_contents();

		ob_end_clean();

		echo $related_posts_content; // WPCS: XSS ok.

}





/** Grab IDs from new WP 3.5 gallery */
if ( ! function_exists( 'grab_ids_from_gallery' ) ) {

	function grab_ids_from_gallery() {
		global $post;

		if ( $post != null ) {

			// if WP 5.0+ block editor
			if ( function_exists( 'parse_blocks' ) ) {

				if ( false !== strpos( $post->post_content, '<!-- wp:' ) ) {
					 $post_blocks = parse_blocks( $post->post_content );

					 // loop through and look for gallery
					foreach ( $post_blocks as $key => $block ) {

						// gallery block found
						if ( isset( $block['blockName'] ) && isset( $block['innerHTML'] ) && $block['blockName'] == 'core/gallery' ) {

							   preg_match_all( '/data-id="([^"]*)"/', $block['innerHTML'], $id_matches );

							if ( $id_matches && isset( $id_matches[1] ) ) {
								return $id_matches[1];
							}
						} //gallery block found end

					} //foreach post block loop end

				} //if the post appears to be using gutenberg

			}

			$attachment_ids          = array();
			$pattern                 = '\[(\[?)(gallery)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
			$ids                     = array();
			$portfolio_extra_content = get_post_meta( $post->ID, '_nectar_portfolio_extra_content', true );

			if ( preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches ) ) {

				$count = count( $matches[3] );      // in case there is more than one gallery in the post.
				for ( $i = 0; $i < $count; $i++ ) {
					$atts = shortcode_parse_atts( $matches[3][ $i ] );
					if ( isset( $atts['ids'] ) ) {
						$attachment_ids = explode( ',', $atts['ids'] );
						$ids            = array_merge( $ids, $attachment_ids );
					}
				}
			}

			if ( preg_match_all( '/' . $pattern . '/s', $portfolio_extra_content, $matches ) ) {
				$count = count( $matches[3] );
				for ( $i = 0; $i < $count; $i++ ) {
					$atts = shortcode_parse_atts( $matches[3][ $i ] );
					if ( isset( $atts['ids'] ) ) {
						$attachment_ids = explode( ',', $atts['ids'] );
						$ids            = array_merge( $ids, $attachment_ids );
					}
				}
			}
			return $ids;
		} else {
			$ids = array();
			return $ids;
		}

	}
}



// fixing filtering for shortcodes
if ( ! function_exists( 'shortcode_empty_paragraph_fix' ) ) {
	function shortcode_empty_paragraph_fix( $content ) {
		$array = array(
			'<p>['    => '[',
			']</p>'   => ']',
			']<br />' => ']',
		);

		$content = strtr( $content, $array );
		return $content;
	}
}

add_filter( 'the_content', 'shortcode_empty_paragraph_fix' );




// Remove default entry class position
if ( ! function_exists( 'nectar_remove_hentry_cssclass' ) ) {
	function nectar_remove_hentry_cssclass( $classes ) {
		$classes = array_diff( $classes, array( 'hentry' ) );
		return $classes;
	}
}
add_filter( 'post_class', 'nectar_remove_hentry_cssclass' );
