<?php
/**
 * The template for displaying single portfolio post type projects.
 *
 * @package Salient WordPress Theme
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$fwp = get_post_meta( $post->ID, '_nectar_portfolio_item_layout', true );
if ( empty( $fwp ) ) {
	$fwp = 'false';
}

global $post;

$bg       = get_post_meta( $post->ID, '_nectar_header_bg', true );
$bg_color = get_post_meta( $post->ID, '_nectar_header_bg_color', true );
$bg_type  = get_post_meta( $post->ID, '_nectar_slider_bg_type', true );
if ( empty( $bg_type ) ) {
	$bg_type = 'image_bg';
}

$options                   = get_nectar_theme_options();
$featured_src              = ( has_post_thumbnail( $post->ID ) ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ) : array( 'empty' );
$full_width_portfolio      = ( ! empty( $fwp ) && $fwp == 'enabled' ) ? 'id="full_width_portfolio" data-featured-img="' . esc_url( $featured_src[0] ) . '"' : 'data-featured-img="' . esc_url( $featured_src[0] ) . '"';
$single_nav_pos            = ( ! empty( $options['portfolio_single_nav'] ) ) ? $options['portfolio_single_nav'] : 'in_header';
$subtitle                  = get_post_meta( $post->ID, '_nectar_header_subtitle', true );
$project_social_style      = ( ! empty( $options['portfolio_social_style'] ) ) ? $options['portfolio_social_style'] : 'default';
$portfolio_remove_comments = ( ! empty( $options['portfolio_remove_comments'] ) ) ? $options['portfolio_remove_comments'] : '0';
$theme_skin                = ( ! empty( $options['theme-skin'] ) && $options['theme-skin'] == 'ascend' ) ? 'ascend' : 'default';

?>

<div <?php echo $full_width_portfolio; if ( ! empty( $bg ) && $fwp != 'enabled' || ! empty( $bg_color ) && $fwp != 'enabled' ) { echo ' data-project-header-bg="true"';} // WPCS: XSS ok. ?>>
			
		<?php
		nectar_page_header( $post->ID );

		if ( empty( $bg ) && empty( $bg_color ) && $bg_type != 'video_bg' ) {
			?>
			
				<div class="row project-title">
					<div class="container">
						<div class="title-wrap">
						<div class="col span_12 section-title <?php if ( empty( $options['portfolio_social'] ) || $options['portfolio_social'] == 0 || empty( $options['portfolio_date'] ) || $options['portfolio_date'] == 0 ) { echo 'no-date';} ?> ">
							
							<h1><?php the_title(); ?></h1>
							<?php
							if ( ! empty( $subtitle ) ) {
								?>
								 <span class="subheader"><?php echo wp_kses_post( $subtitle ); ?></span> <?php } ?>

							<?php
							if ( $single_nav_pos == 'in_header' ) {
								project_single_controls();}
							?>
					 
						</div> 
					</div>
				</div> 
			</div><!--/row-->
			
		<?php } //project header ?>
		
	<div class="container-wrap" data-nav-pos="<?php echo esc_attr( $single_nav_pos ); ?>">
		
		<div class="container main-content"> 
			
			<?php
			$enable_gallery_slider = get_post_meta( get_the_ID(), '_nectar_gallery_slider', true );
			?>
			
			<div class="row <?php if ( ! empty( $enable_gallery_slider ) && $enable_gallery_slider == 'on' ) { echo 'gallery-slider';} ?> ">
				
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();

						if ( function_exists( 'yoast_breadcrumb' ) ) {
							yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); 
						}

						get_template_part( 'includes/partials/single-portfolio/content-area' );

						if ( $fwp != 'enabled' ) {
							get_template_part( 'includes/partials/single-portfolio/sidebar' );
						}

				endwhile;
			 endif;
			?>
				
			</div>


			<?php if ( comments_open() && $theme_skin == 'ascend' && $portfolio_remove_comments != '1' ) { ?>
						
				<div class="comments-section row">
				   <?php comments_template(); ?>
				</div>
			
			<?php } ?>  

		</div><!--/container-->

		<?php
		if ( $single_nav_pos == 'after_project' || $single_nav_pos == 'after_project_2' ) {
				get_template_part( 'includes/partials/single-portfolio/bottom-project-navigation' );
		}
		?>

	
	</div><!--/container-wrap-->

</div><!--/if portfolio fullwidth-->


<?php

if ( $project_social_style == 'fixed_bottom_right' ) {
	get_template_part( 'includes/partials/single-portfolio/fixed-social-sharing-buttons' );
}


get_footer(); ?>