<?php
/**
 * Footer copyright bar
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

$disable_footer_copyright = ( ! empty( $options['disable-copyright-footer-area'] ) && $options['disable-copyright-footer-area'] == 1 ) ? 'true' : 'false';
$copyright_footer_layout  = ( ! empty( $options['footer-copyright-layout'] ) ) ? $options['footer-copyright-layout'] : 'default';
$footer_columns           = ( ! empty( $options['footer_columns'] ) ) ? $options['footer_columns'] : '4';



if ( $disable_footer_copyright == 'false' ) {
	?>


  <div class="row" id="copyright" data-layout="<?php echo esc_attr( $copyright_footer_layout ); ?>">
	
	<div class="container">
	   
		<?php if ( $footer_columns != '1' ) { ?>
		<div class="col span_5">
		   
			<?php
			if ( $copyright_footer_layout == 'centered' ) {
				if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( 'Footer Copyright' ) ) :
else :
	?>
	
				<div class="widget">			
		   
				</div>		   
			<?php
			endif;
			}
			?>
		   
			<?php if ( ! empty( $options['disable-auto-copyright'] ) && $options['disable-auto-copyright'] == 1 ) { ?>
			<p>
				<?php
				if ( ! empty( $options['footer-copyright-text'] ) ) {
					echo wp_kses_post( $options['footer-copyright-text'] );}
				?>
			 </p>	
			<?php } else { ?>
			<p>&copy; <?php echo date( 'Y' ) . ' ' . esc_html( get_bloginfo( 'name' ) ); ?>. 
					   <?php
						if ( ! empty( $options['footer-copyright-text'] ) ) {
							echo wp_kses_post( $options['footer-copyright-text'] );}
						?>
			 </p>
			<?php } ?>
		   
		</div><!--/span_5-->
		<?php } ?>
	   
	  <div class="col span_7 col_last">
		<ul class="social">
			<?php
			if ( ! empty( $options['use-twitter-icon'] ) && $options['use-twitter-icon'] == 1 ) {
				?>
			   <li><a target="_blank" href="<?php echo esc_url( $options['twitter-url'] ); ?>"><i class="fa fa-twitter"></i> </a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-facebook-icon'] ) && $options['use-facebook-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['facebook-url'] ); ?>"><i class="fa fa-facebook"></i> </a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-vimeo-icon'] ) && $options['use-vimeo-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['vimeo-url'] ); ?>"> <i class="fa fa-vimeo"></i> </a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-pinterest-icon'] ) && $options['use-pinterest-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['pinterest-url'] ); ?>"><i class="fa fa-pinterest"></i> </a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-linkedin-icon'] ) && $options['use-linkedin-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['linkedin-url'] ); ?>"><i class="fa fa-linkedin"></i> </a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-youtube-icon'] ) && $options['use-youtube-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['youtube-url'] ); ?>"><i class="fa fa-youtube-play"></i> </a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-tumblr-icon'] ) && $options['use-tumblr-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['tumblr-url'] ); ?>"><i class="fa fa-tumblr"></i> </a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-dribbble-icon'] ) && $options['use-dribbble-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['dribbble-url'] ); ?>"><i class="fa fa-dribbble"></i> </a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-rss-icon'] ) && $options['use-rss-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo ( ! empty( $options['rss-url'] ) ) ? esc_url( $options['rss-url'] ) : esc_html( get_bloginfo( 'rss_url' ) ); ?>"><i class="fa fa-rss"></i> </a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-github-icon'] ) && $options['use-github-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['github-url'] ); ?>"><i class="fa fa-github-alt"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-behance-icon'] ) && $options['use-behance-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['behance-url'] ); ?>"> <i class="fa fa-behance"></i> </a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-google-plus-icon'] ) && $options['use-google-plus-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['google-plus-url'] ); ?>"><i class="fa fa-google-plus"></i> </a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-instagram-icon'] ) && $options['use-instagram-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['instagram-url'] ); ?>"><i class="fa fa-instagram"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-stackexchange-icon'] ) && $options['use-stackexchange-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['stackexchange-url'] ); ?>"><i class="fa fa-stackexchange"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-soundcloud-icon'] ) && $options['use-soundcloud-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['soundcloud-url'] ); ?>"><i class="fa fa-soundcloud"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-flickr-icon'] ) && $options['use-flickr-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['flickr-url'] ); ?>"><i class="fa fa-flickr"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-spotify-icon'] ) && $options['use-spotify-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['spotify-url'] ); ?>"><i class="icon-salient-spotify"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-vk-icon'] ) && $options['use-vk-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['vk-url'] ); ?>"><i class="fa fa-vk"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-vine-icon'] ) && $options['use-vine-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['vine-url'] ); ?>"><i class="fa-vine"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-houzz-icon'] ) && $options['use-houzz-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['houzz-url'] ); ?>"><i class="fa-houzz"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-yelp-icon'] ) && $options['use-yelp-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['yelp-url'] ); ?>"><i class="fa-yelp"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-snapchat-icon'] ) && $options['use-snapchat-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['snapchat-url'] ); ?>"><i class="fa-snapchat"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-mixcloud-icon'] ) && $options['use-mixcloud-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['mixcloud-url'] ); ?>"><i class="fa-mixcloud"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-bandcamp-icon'] ) && $options['use-bandcamp-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['bandcamp-url'] ); ?>"><i class="fa-bandcamp"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-tripadvisor-icon'] ) && $options['use-tripadvisor-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['tripadvisor-url'] ); ?>"><i class="fa-tripadvisor"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-telegram-icon'] ) && $options['use-telegram-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['telegram-url'] ); ?>"><i class="fa-telegram"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-slack-icon'] ) && $options['use-slack-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['slack-url'] ); ?>"><i class="fa-slack"></i></a></li> <?php } ?>
		  <?php
			if ( ! empty( $options['use-medium-icon'] ) && $options['use-medium-icon'] == 1 ) {
				?>
			 <li><a target="_blank" href="<?php echo esc_url( $options['medium-url'] ); ?>"><i class="fa-medium"></i></a></li> <?php } ?>
		</ul>
	  </div><!--/span_7-->

	  <?php if ( $footer_columns == '1' ) { ?>
		<div class="col span_5">
		   
			<?php
			if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( 'Footer Copyright' ) ) :
else :
	?>
	
			<div class="widget">			
	   
			</div>		   
		<?php endif; ?>
	  
			<?php if ( ! empty( $options['disable-auto-copyright'] ) && $options['disable-auto-copyright'] == 1 ) { ?>
			<p>
				<?php
				if ( ! empty( $options['footer-copyright-text'] ) ) {
					echo wp_kses_post( $options['footer-copyright-text'] );
				}
				?>
			 </p>	
			<?php } else { ?>
			<p>&copy; <?php echo date( 'Y' ) . ' ' . esc_html( get_bloginfo( 'name' ) ); ?>. 
					   <?php
						if ( ! empty( $options['footer-copyright-text'] ) ) {
							echo wp_kses_post( $options['footer-copyright-text'] );}
						?>
			 </p>
			<?php } ?>
		   
		</div><!--/span_5-->
		<?php } ?>
	
	</div><!--/container-->
	
  </div><!--/row-->
  
	<?php }