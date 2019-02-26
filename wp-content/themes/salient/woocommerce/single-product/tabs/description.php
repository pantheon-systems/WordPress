<?php
/**
 * Description tab
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $nectar_options;

$tab_pos = (!empty($nectar_options['product_tab_position'])) ? $nectar_options['product_tab_position'] : 'default';

$heading = esc_html( apply_filters( 'woocommerce_product_description_heading', __( 'Description', 'woocommerce' ) ) );

?>

<?php if ( $heading && $tab_pos != 'fullwidth' && $tab_pos != 'fullwidth_centered'): ?>
  <h2><?php echo $heading; ?></h2>
<?php endif; ?>

<?php the_content(); ?>
