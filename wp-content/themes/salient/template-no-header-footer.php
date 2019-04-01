<?php 
/*template name: No Header & Footer */
?>

<!doctype html>

<html <?php language_attributes(); ?> class="no-js">
<head>

<!-- Meta Tags -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<?php
$options = get_nectar_theme_options();

if ( ! empty( $options['responsive'] ) && $options['responsive'] == 1 ) { ?>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />

<?php } else { ?>
	<meta name="viewport" content="width=1200" />
<?php } ?>	

<!--Shortcut icon-->
<?php if ( ! empty( $options['favicon'] ) && ! empty( $options['favicon']['url'] ) ) { ?>
	<link rel="shortcut icon" href="<?php echo nectar_options_img( $options['favicon'] ); ?>" />
<?php } 

wp_head();

if ( ! empty( $options['google-analytics'] ) ) {
	echo $options['google-analytics'];}
?>
 
</head>

<?php

 global $post;
 global $woocommerce;
 $nectar_header_options = nectar_get_header_variables();

?>

<body <?php body_class(); nectar_body_attributes(); ?>>

<?php 

if(!empty($options['boxed_layout']) && $options['boxed_layout'] == '1') { echo '<div id="boxed">'; }

?>

<?php nectar_page_trans_markup(); ?>

<div id="header-outer" <?php nectar_header_nav_attributes(); ?>> <header id="top"> <div class="span_3"></div><div class="span_9"></div> </header> </div><!--/header-outer-->

<div id="ajax-content-wrap">

<?php

if ( $nectar_header_options['side_widget_area'] == '1' && $nectar_header_options['side_widget_class'] == 'fullscreen' ) {
	echo '<div class="blurred-wrap">';
}

nectar_page_header($post->ID);

$nectar_fp_options = nectar_get_full_page_options();
$options           = get_nectar_theme_options();
$header_format     = ( ! empty( $options['header_format'] ) ) ? $options['header_format'] : 'default';
$theme_skin        = ( ! empty( $options['theme-skin'] ) ) ? $options['theme-skin'] : 'original';
if ( 'centered-menu-bottom-bar' == $header_format ) {
	$theme_skin = 'material';
}

?>

<div class="container-wrap">
	<div class="<?php if ( $nectar_fp_options['page_full_screen_rows'] != 'on' ) { echo 'container';} ?> main-content">
		<div class="row">
			
			<?php

			// breadcrumbs
			if ( function_exists( 'yoast_breadcrumb' ) && ! is_home() && ! is_front_page() ) {
				yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' ); }

			 // buddypress
			 global $bp;
			if ( $bp && ! bp_is_blog_page() ) {
				echo '<h1>' . get_the_title() . '</h1>';
			}

			 // fullscreen rows
			if ( $nectar_fp_options['page_full_screen_rows'] == 'on' ) {
				echo '<div id="nectar_fullscreen_rows" data-animation="' . esc_attr( $nectar_fp_options['page_full_screen_rows_animation'] ) . '" data-row-bg-animation="' . esc_attr( $nectar_fp_options['page_full_screen_rows_bg_img_animation'] ) . '" data-animation-speed="' . esc_attr( $nectar_fp_options['page_full_screen_rows_animation_speed'] ) . '" data-content-overflow="' . esc_attr( $nectar_fp_options['page_full_screen_rows_content_overflow'] ) . '" data-mobile-disable="' . esc_attr( $nectar_fp_options['page_full_screen_rows_mobile_disable'] ) . '" data-dot-navigation="' . esc_attr( $nectar_fp_options['page_full_screen_rows_dot_navigation'] ) . '" data-footer="' . esc_attr( $nectar_fp_options['page_full_screen_rows_footer'] ) . '" data-anchors="' . esc_attr( $nectar_fp_options['page_full_screen_rows_anchors'] ) . '">';
			}

			if ( have_posts() ) :
				while ( have_posts() ) :

					the_post();

					the_content();

				 endwhile;
			 endif;

			if ( $nectar_fp_options['page_full_screen_rows'] == 'on' ) {
				echo '</div>';
			}
			?>

		</div><!--/row-->
	</div><!--/container-->
</div><!--/container-wrap-->


<?php

get_template_part( 'includes/partials/footer/off-canvas-navigation' );

?>

</div> <!--/ajax-content-wrap-->


<?php
if ( ! empty( $options['boxed_layout'] ) && $options['boxed_layout'] == '1' && $header_format != 'left-header' ) {
	echo '</div><!--/boxed closing div-->'; }


get_template_part( 'includes/partials/footer/back-to-top' );

get_template_part( 'includes/partials/footer/body-border' );

wp_footer();

if ( 'material' == $theme_skin ) {
	echo '</div></div><!--/ocm-effect-wrap-->';
}

nectar_hook_before_body_close();

?>

</body>
</html>
