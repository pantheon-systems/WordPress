<?php
/**
 * The template for displaying single posts.
 *
 * @package Salient WordPress Theme
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();


$options           = get_nectar_theme_options();
$fullscreen_header = ( ! empty( $options['blog_header_type'] ) && $options['blog_header_type'] == 'fullscreen' && is_singular( 'post' ) ) ? true : false;
$blog_header_type  = ( ! empty( $options['blog_header_type'] ) ) ? $options['blog_header_type'] : 'default';
$theme_skin        = ( ! empty( $options['theme-skin'] ) ) ? $options['theme-skin'] : 'original';
$header_format     = ( ! empty( $options['header_format'] ) ) ? $options['header_format'] : 'default';
if ( $header_format == 'centered-menu-bottom-bar' ) {
	$theme_skin = 'material';
}
$hide_sidebar                      = ( ! empty( $options['blog_hide_sidebar'] ) ) ? $options['blog_hide_sidebar'] : '0';
$blog_type                         = $options['blog_type'];
$blog_social_style                 = ( ! empty( $options['blog_social_style'] ) ) ? $options['blog_social_style'] : 'default';
$enable_ss                         = ( ! empty( $options['blog_enable_ss'] ) ) ? $options['blog_enable_ss'] : 'false';
$remove_single_post_date           = ( ! empty( $options['blog_remove_single_date'] ) ) ? $options['blog_remove_single_date'] : '0';
$remove_single_post_author         = ( ! empty( $options['blog_remove_single_author'] ) ) ? $options['blog_remove_single_author'] : '0';
$remove_single_post_comment_number = ( ! empty( $options['blog_remove_single_comment_number'] ) ) ? $options['blog_remove_single_comment_number'] : '0';
$remove_single_post_nectar_love    = ( ! empty( $options['blog_remove_single_nectar_love'] ) ) ? $options['blog_remove_single_nectar_love'] : '0';

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		// main post header
		nectar_page_header( $post->ID );

endwhile;
endif;


if ( $fullscreen_header == true ) {

	// post header fullscreen style when no image is supplied
	get_template_part( 'includes/partials/single-post/post-header-no-img-fullscreen' );

} ?>


<div class="container-wrap <?php echo ( $fullscreen_header == true ) ? 'fullscreen-blog-header' : null; ?> <?php if ( $blog_type == 'std-blog-fullwidth' || $hide_sidebar == '1' ) { echo 'no-sidebar';} ?>" data-midnight="dark" data-remove-post-date="<?php echo esc_attr( $remove_single_post_date ); ?>" data-remove-post-author="<?php echo esc_attr( $remove_single_post_author ); ?>" data-remove-post-comment-number="<?php echo esc_attr( $remove_single_post_comment_number ); ?>">
	<div class="container main-content">
		
		<?php
		// post header regular style when no image is supplied
		get_template_part( 'includes/partials/single-post/post-header-no-img-regular' );
		?>
			
		<div class="row">
			
			<?php

			if ( function_exists( 'yoast_breadcrumb' ) ) {
				yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); }


			$blog_standard_type = ( ! empty( $options['blog_standard_type'] ) ) ? $options['blog_standard_type'] : 'classic';
			$blog_type          = $options['blog_type'];
			if ( $blog_type == null ) {
				$blog_type = 'std-blog-sidebar';
			}

			if ( $blog_standard_type == 'minimal' && $blog_type == 'std-blog-sidebar' || $blog_type == 'std-blog-fullwidth' ) {
				$std_minimal_class = 'standard-minimal';
			} else {
				$std_minimal_class = '';
			}

			if ( $blog_type == 'std-blog-fullwidth' || $hide_sidebar == '1' ) {
				echo '<div class="post-area col ' . $std_minimal_class . ' span_12 col_last">'; // WPCS: XSS ok.
			} else {
				echo '<div class="post-area col ' . $std_minimal_class . ' span_9">'; // WPCS: XSS ok.
			}

			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();

					get_template_part( 'includes/post-templates/entry', get_post_format() );

				 endwhile;
			 endif;

			wp_link_pages();


			// bottom social location for default minimal post header style
			if ( $blog_header_type == 'default_minimal' && $blog_social_style != 'fixed_bottom_right' && 'post' == get_post_type() ) {

				get_template_part( 'includes/partials/single-post/default-minimal-bottom-social' );

			}


			if ( $theme_skin != 'ascend' ) {

				if ( ! empty( $options['author_bio'] ) && $options['author_bio'] == true && 'post' == get_post_type() ) {

					 get_template_part( 'includes/partials/single-post/author-bio' );

				}

				if ( $theme_skin != 'material' ) {
					?>

						<div class="comments-section">
								<?php comments_template(); ?>
						 </div>   


					<?php
				}
			}
			?>

			</div><!--/span_9-->
			
			
			<?php if ( $blog_type != 'std-blog-fullwidth' && $hide_sidebar != '1' ) { ?>
				
				<div id="sidebar" data-nectar-ss="<?php echo esc_attr( $enable_ss ); ?>" class="col span_3 col_last">
					<?php get_sidebar(); ?>
				</div><!--/sidebar-->
				
			<?php } ?>
				
		</div><!--/row-->

		

		<div class="row">

			<?php if ( $theme_skin == 'ascend' && $fullscreen_header == true && 'post' == get_post_type() ) {
				//Ascend theme skin only bottom meta bar when using fullscreen post header
				get_template_part( 'includes/partials/single-post/post-meta-bar-ascend-skin' );
			}
			
			//Ascend and Material theme skin post pagination positioning
			if ( $theme_skin == 'ascend' || $theme_skin == 'material' ) {
				
				nectar_next_post_display();
				nectar_related_post_display();

			}
			?>

			<?php
			if ( ! empty( $options['author_bio'] ) && $options['author_bio'] == true && $theme_skin == 'ascend' && 'post' == get_post_type() ) {

				get_template_part( 'includes/partials/single-post/author-bio-ascend-skin' );

			}
			?>


			<?php if ( $theme_skin == 'ascend' || $theme_skin == 'material' ) { ?>

			  <div class="comments-section" data-author-bio="<?php if ( ! empty( $options['author_bio'] ) && $options['author_bio'] == true ) { echo 'true'; } else { echo 'false'; } ?>">
				   <?php comments_template(); ?>
			 </div>   

			<?php } ?>

		</div>


		<?php
		//original theme skin post pagination positioning
		if ( $theme_skin != 'ascend' && $theme_skin != 'material' ) {
			 nectar_next_post_display();
			 nectar_related_post_display();
		}
		?>
		
	</div><!--/container-->

</div><!--/container-wrap-->


<?php if ( $blog_social_style == 'fixed_bottom_right' ) {

		get_template_part( 'includes/partials/single-post/fixed-social-sharing-buttons' );
}

get_footer(); ?>