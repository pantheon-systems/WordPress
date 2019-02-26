<?php 
/*template name: Contact*/
get_header(); 

nectar_page_header($post->ID);  

$options = get_nectar_theme_options(); 

wp_enqueue_script('nectarMap', get_template_directory_uri() . '/js/map.js', array('jquery'), '1.0', TRUE);

?>

<div class="container-wrap">

	<div id="contact-map" class="nectar-google-map" data-dark-color-scheme="<?php if(!empty($options['map-dark-color-scheme'])) echo esc_attr( $options['map-dark-color-scheme'] ); ?>" data-ultra-flat="<?php if(!empty($options['map-ultra-flat'])) echo esc_attr( $options['map-ultra-flat'] ); ?>" data-greyscale="<?php if(!empty($options['map-greyscale'])) echo esc_attr( $options['map-greyscale'] ); ?>" data-extra-color="<?php if(!empty($options['map-color'])) echo esc_attr( $options['map-color'] ); ?>" data-enable-animation="<?php if(!empty($options['enable-map-animation'])) echo esc_attr( $options['enable-map-animation'] ); ?>" data-enable-zoom="<?php if(!empty($options['enable-map-zoom'])) echo esc_attr( $options['enable-map-zoom'] ); ?>" data-zoom-level="<?php if(!empty($options['zoom-level'])) echo esc_attr( $options['zoom-level'] ); ?>" data-center-lat="<?php if(!empty($options['center-lat'])) echo esc_attr( $options['center-lat'] ); ?>" data-center-lng="<?php if(!empty($options['center-lng'])) echo esc_attr( $options['center-lng'] ); ?>" data-marker-img="<?php if(!empty($options['marker-img'])) echo nectar_options_img( $options['marker-img'] ); ?>"></div>
	
	<div class="map-marker-list contact-map">
		<?php
			$count = 0;

			for($i = 1; $i <= 10; $i++){
				if(!empty($options['map-point-'.$i]) && $options['map-point-'.$i] != 0 ) {
					$count++;

					echo '<div class="map-marker" data-lat="'. esc_attr( $options['latitude'.$i] ) .'" data-lng="'. esc_attr( $options['longitude'.$i] ) .'" data-mapinfo="'. esc_attr( $options['map-info'.$i] ) .'"></div>';
				}	
			}
		?>
		
	</div>
	
	<div class="container main-content">
		
		<div class="row">
	
			<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
				
				<?php the_content(); ?>
	
			<?php endwhile; endif; ?>
				
	
		</div><!--/row-->
		
	</div><!--/container-->

</div>
<?php get_footer(); ?>