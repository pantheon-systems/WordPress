<?php
/**
 * Footer bottom content
 *
 * @package Salient WordPress Theme
 * @subpackage Partials
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
global $post;
$options = get_nectar_theme_options();

$exclude_pages = ( ! empty( $options['exclude_cta_pages'] ) ) ? $options['exclude_cta_pages'] : array();
$cta_link      = ( ! empty( $options['cta-btn-link'] ) ) ? $options['cta-btn-link'] : '#';
$cta_btn_color = ( ! empty( $options['cta-btn-color'] ) ) ? $options['cta-btn-color'] : 'accent-color';

if ( ! empty( $options['cta-text'] ) && current_page_url() != $cta_link && ! in_array( $post->ID, $exclude_pages ) ) {

	?>

  <div id="call-to-action">
	<div class="container">
	  <div class="triangle"></div>
	  <span> <?php echo wp_kses_post( $options['cta-text'] ); ?> </span>
	  <a class="nectar-button 
	  <?php
		if ( $cta_btn_color != 'see-through' ) {
			echo 'regular-button ';}
		?>
	  <?php echo esc_html( $cta_btn_color ); ?>" data-color-override="false" href="<?php echo esc_url( $cta_link ); ?>">
	  <?php
		if ( ! empty( $options['cta-btn'] ) ) {
			echo wp_kses_post( $options['cta-btn'] );}
		?>
</a>
	</div>
  </div>

	<?php
}
