<?php
/**
 * Footer widget area
 *
 * @package Salient WordPress Theme
 * @subpackage Partials
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
$options = get_nectar_theme_options();

$using_footer_widget_area = ( ! empty( $options['enable-main-footer-area'] ) && $options['enable-main-footer-area'] == 1 ) ? 'true' : 'false';
$footer_columns           = ( ! empty( $options['footer_columns'] ) ) ? $options['footer_columns'] : '4';

if ( $using_footer_widget_area == 'true' ) { ?>
  
<div id="footer-widgets" data-cols="<?php echo esc_attr( $footer_columns ); ?>">
  
  <div class="container">
	
	<?php nectar_hook_before_footer_widget_area(); ?>
	
	<div class="row">
	   
	  <?php

		if ( $footer_columns == '1' ) {
			$footer_column_class = 'span_12';
		} elseif ( $footer_columns == '2' ) {
			$footer_column_class = 'span_6';
		} elseif ( $footer_columns == '3' ) {
			$footer_column_class = 'span_4';
		} else {
			$footer_column_class = 'span_3';
		}
		?>
	   
	  <div class="col <?php echo esc_attr( $footer_column_class ); // WPCS: XSS ok. ?>">
		  <!-- Footer widget area 1 -->
		  <?php
			if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( 'Footer Area 1' ) ) :
else :
	?>
  
			<div class="widget">		
			   
				   </div>
			<?php endif; ?>
	  </div><!--/span_3-->
	   
	  <?php if ( $footer_columns == '2' || $footer_columns == '3' || $footer_columns == '4' || $footer_columns == '5' ) { ?>

		<div class="col <?php echo esc_attr( $footer_column_class ); // WPCS: XSS ok. ?>">
		   <!-- Footer widget area 2 -->
			<?php
			if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( 'Footer Area 2' ) ) :
else :
	?>
  
			  <div class="widget">			
				
					 </div>
			<?php endif; ?>
			
		</div><!--/span_3-->

		<?php } ?>

	   
	  <?php if ( $footer_columns == '3' || $footer_columns == '4' || $footer_columns == '5' ) { ?>
		<div class="col <?php echo esc_attr( $footer_column_class ); // WPCS: XSS ok. ?>">
		   <!-- Footer widget area 3 -->
			<?php
			if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( 'Footer Area 3' ) ) :
else :
	?>
	
			   <div class="widget">			
			   
				  </div>		   
			<?php endif; ?>
			
		</div><!--/span_3-->
		<?php } ?>
	   
	  <?php if ( $footer_columns == '4' || $footer_columns == '5' ) { ?>
		<div class="col <?php echo esc_attr( $footer_column_class ); // WPCS: XSS ok. ?>">
		   <!-- Footer widget area 4 -->
			<?php
			if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( 'Footer Area 4' ) ) :
else :
	?>
  
			  <div class="widget">		
				
					   </div><!--/widget-->	
			<?php endif; ?>
			
		</div><!--/span_3-->
		<?php } ?>
	   
	</div><!--/row-->
	
	<?php nectar_hook_after_footer_widget_area(); ?>
	
  </div><!--/container-->

</div><!--/footer-widgets-->

	<?php
} //endif for enable main footer area