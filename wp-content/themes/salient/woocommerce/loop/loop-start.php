<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

$options = get_nectar_theme_options(); 
$product_style = (!empty($options['product_style'])) ? $options['product_style'] : 'classic';
$nectar_woo_desktop_cols = (!empty($options['product_desktop_cols'])) ? $options['product_desktop_cols'] : 'default';
$nectar_woo_desktop_small_cols = (!empty($options['product_desktop_small_cols'])) ? $options['product_desktop_small_cols'] : 'default';
$nectar_woo_tablet_cols = (!empty($options['product_tablet_cols'])) ? $options['product_tablet_cols'] : 'default';
$nectar_woo_phone_cols = (!empty($options['product_phone_cols'])) ? $options['product_phone_cols'] : 'default';

?>

<?php if(function_exists('wc_get_loop_prop')) { ?>
  <ul class="products columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) );?>" data-n-desktop-columns="<?php echo esc_attr( $nectar_woo_desktop_cols ); ?>" data-n-desktop-small-columns="<?php echo esc_attr( $nectar_woo_desktop_small_cols ); ?>" data-n-tablet-columns="<?php echo esc_attr( $nectar_woo_tablet_cols ); ?>" data-n-phone-columns="<?php echo esc_attr( $nectar_woo_phone_cols ); ?>" data-product-style="<?php echo esc_attr( $product_style ); ?>">
<?php } else { ?>
  <ul class="products" data-product-style="<?php echo esc_attr( $product_style ); ?>">
<?php } ?>


