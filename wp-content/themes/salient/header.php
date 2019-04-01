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
	<link rel="shortcut icon" href="<?php echo esc_url( nectar_options_img( $options['favicon'] ) ); ?>" />
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

nectar_hook_after_body_open();

if ( $nectar_header_options['theme_skin'] == 'material' ) {
	echo '<div class="ocm-effect-wrap"><div class="ocm-effect-wrap-inner">';
}

if ( $nectar_header_options['n_boxed_style'] ) {
	echo '<div id="boxed">';
}

nectar_page_trans_markup();

get_template_part( 'includes/partials/header/secondary-navigation' );

get_template_part( 'includes/partials/header/header-space' );

?>

<div id="header-outer" <?php nectar_header_nav_attributes(); ?>>
	
	<?php

	if ( empty( $options['theme-skin'] ) || ( ! empty( $options['theme-skin'] ) && $nectar_header_options['theme_skin'] != 'ascend' && $nectar_header_options['header_format'] != 'left-header' ) ) {
		get_template_part( 'includes/header-search' );
	}

	get_template_part( 'includes/partials/header/header-menu' );


	if ( ! empty( $options['enable-cart'] ) && $options['enable-cart'] == '1' && $nectar_header_options['theme_skin'] != 'material' ) {

		if ( $woocommerce ) {
			echo nectar_header_cart_output();
		}
	}

	?>
			
	 
</div><!--/header-outer-->

<?php

if ( ! empty( $options['enable-cart'] ) && $options['enable-cart'] == '1' ) {
	  get_template_part( 'includes/partials/header/woo-slide-in-cart' );
}

if ( $nectar_header_options['theme_skin'] == 'ascend' || $nectar_header_options['header_format'] == 'left-header' ) {
	if ( $nectar_header_options['header_search'] != 'false' ) {
		get_template_part( 'includes/header-search' ); }
}

if ( $nectar_header_options['mobile_fixed'] != '1' ) {
	get_template_part( 'includes/partials/header/classic-mobile-nav' );
}

?>

<div id="ajax-content-wrap">

<?php
if ( $nectar_header_options['side_widget_area'] == '1' && $nectar_header_options['side_widget_class'] == 'fullscreen' ) {
	echo '<div class="blurred-wrap">';
}