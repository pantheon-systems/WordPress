<?php
/**
 * Header search template
 *
 * @package    Salient WordPress Theme
 * @subpackage Includes
 * @version    9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$options = get_nectar_theme_options();

if ( ! empty( $options['header-disable-ajax-search'] ) && $options['header-disable-ajax-search'] == '1' ) {
	$ajax_search = 'no';
} else {
	$ajax_search = 'yes';
} ?>

<div id="search-outer" class="nectar">
	<div id="search">
		<div class="container">
			 <div id="search-box">
				 <div class="inner-wrap">
					 <div class="col span_12">
						  <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="GET">
							<?php
							$theme_skin    = ( ! empty( $options['theme-skin'] ) ) ? $options['theme-skin'] : 'original';
							$header_format = ( ! empty( $options['header_format'] ) ) ? $options['header_format'] : 'default';
							if ( $header_format == 'centered-menu-bottom-bar' ) {
								$theme_skin = 'material';
							}

							if ( $theme_skin == 'material' ) {
							?>
							 <input type="text" name="s" <?php if ( $ajax_search == 'yes' ) { echo 'id="s"'; } ?> value="" placeholder="<?php echo esc_attr__( 'Search', 'salient' ); ?>" /> 
							 <?php
							} else {
								?>
								<input type="text" name="s" <?php if ( $ajax_search == 'yes' ) { echo 'id="s"'; } ?> value="<?php echo esc_attr__( 'Start Typing...', 'salient' ); ?>" data-placeholder="<?php echo esc_attr__( 'Start Typing...', 'salient' ); ?>" />
							<?php } ?></form>
								
						<?php
						if ( $theme_skin == 'ascend' && $ajax_search == 'no' ) {
							echo '<span><i>' . __( 'Press enter to begin your search', 'salient' ) . '</i></span>'; }
						if ( $theme_skin == 'material' ) {
							echo '<span>' . esc_html__( 'Hit enter to search or ESC to close', 'salient' ) . '</span>'; }
						?>
					</div><!--/span_12-->
				</div><!--/inner-wrap-->
			 </div><!--/search-box-->
			 <div id="close"><a href="#">
				<?php
				if ( $theme_skin == 'material' ) {
					echo '<span class="close-wrap"> <span class="close-line close-line1"></span> <span class="close-line close-line2"></span> </span>';
				} else {
					echo '<span class="icon-salient-x" aria-hidden="true"></span>';
				}
				?>
				 </a></div>
		 </div><!--/container-->
	</div><!--/search-->
</div><!--/search-outer-->
