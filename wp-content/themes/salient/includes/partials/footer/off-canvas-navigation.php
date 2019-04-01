<?php
/**
 * Off canvas navigation
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

$header_format = ( ! empty( $options['header_format'] ) ) ? $options['header_format'] : 'default';
$theme_skin    = ( ! empty( $options['theme-skin'] ) ) ? $options['theme-skin'] : 'original';
if ( 'centered-menu-bottom-bar' == $header_format ) {
	$theme_skin = 'material';
}


$mobile_fixed  = ( ! empty( $options['header-mobile-fixed'] ) ) ? $options['header-mobile-fixed'] : 'false';
$has_main_menu = ( has_nav_menu( 'top_nav' ) ) ? 'true' : 'false';

$side_widget_area          = ( ! empty( $options['header-slide-out-widget-area'] ) ) ? $options['header-slide-out-widget-area'] : 'off';
$user_set_side_widget_area = $side_widget_area;
if ( $has_main_menu == 'true' && $mobile_fixed == '1' || $has_main_menu == 'true' && $theme_skin == 'material' ) {
	$side_widget_area = '1';
}


$full_width_header = ( ! empty( $options['header-fullwidth'] ) && $options['header-fullwidth'] == '1' ) ? true : false;
$side_widget_class = ( ! empty( $options['header-slide-out-widget-area-style'] ) ) ? $options['header-slide-out-widget-area-style'] : 'slide-out-from-right';

if ( $header_format == 'centered-menu-under-logo' ) {
	if ( $side_widget_class == 'slide-out-from-right-hover' && $user_set_side_widget_area == '1' ) {
		$side_widget_class = 'slide-out-from-right';
	}
}

$side_widget_overlay_opacity = ( ! empty( $options['header-slide-out-widget-area-overlay-opacity'] ) ) ? $options['header-slide-out-widget-area-overlay-opacity'] : 'dark';
$prepend_top_nav_mobile      = ( ! empty( $options['header-slide-out-widget-area-top-nav-in-mobile'] ) ) ? $options['header-slide-out-widget-area-top-nav-in-mobile'] : 'false';
if ( $theme_skin == 'material' ) {
	$prepend_top_nav_mobile = '1';
}

$dropdown_func = ( ! empty( $options['header-slide-out-widget-area-dropdown-behavior'] ) ) ? $options['header-slide-out-widget-area-dropdown-behavior'] : 'default';
if ( $side_widget_class == 'fullscreen' || $side_widget_class == 'fullscreen-alt' ) {
	$dropdown_func = 'default';
}

if ( $side_widget_area == '1' ) {

	if ( $side_widget_class == 'fullscreen' ) {
		echo '</div><!--blurred-wrap-->';
	}
	?>

	<div id="slide-out-widget-area-bg" class="<?php echo esc_attr( $side_widget_class ) . ' ' . esc_attr( $side_widget_overlay_opacity ); ?>">
				 <?php
					if ( $side_widget_class == 'fullscreen-alt' ) {
						echo '<div class="bg-inner"></div>';}
					?>
	</div>
  
	<div id="slide-out-widget-area" class="<?php echo esc_attr( $side_widget_class ); ?>" data-dropdown-func="<?php echo esc_attr( $dropdown_func ); ?>" data-back-txt="<?php echo esc_attr__( 'Back', 'salient' ); ?>">

		<?php
		if ( $side_widget_class == 'fullscreen' || $side_widget_class == 'fullscreen-alt' || ( $theme_skin == 'material' && $side_widget_class == 'slide-out-from-right' ) || ( $theme_skin == 'material' && $side_widget_class == 'slide-out-from-right-hover' ) ) {
			echo '<div class="inner-wrap">';}
		?>

		<?php $prepend_mobile_menu = ( $prepend_top_nav_mobile == '1' && $has_main_menu == 'true' && $user_set_side_widget_area != 'off' ) ? 'true' : 'false'; ?>
		<div class="inner" data-prepend-menu-mobile="<?php echo esc_attr( $prepend_mobile_menu ); ?>">

		  <a class="slide_out_area_close" href="#">
			<?php
			if ( $theme_skin != 'material' ) {
				echo '<span class="icon-salient-x icon-default-style"></span>';
			} else {
				echo '<span class="close-wrap"> <span class="close-line close-line1"></span> <span class="close-line close-line2"></span> </span>';
			}
			?>
		  </a>


		   <?php

			if ( $user_set_side_widget_area == 'off' || $prepend_top_nav_mobile == '1' && $has_main_menu == 'true' ) {
				?>
			   <div class="off-canvas-menu-container mobile-only">
					 
					  <?php
						$using_secondary = ( ! empty( $options['header_layout'] ) && $header_format != 'left-header' ) ? $options['header_layout'] : ' ';

						if ( ! empty( $options['secondary-header-text'] ) && $using_secondary == 'header_with_secondary' ) {
							$nectar_secondary_link = ( ! empty( $options['secondary-header-link'] ) ) ? $options['secondary-header-link'] : '';
							echo '<div class="secondary-header-text">';
							if ( ! empty( $nectar_secondary_link ) ) {
								echo '<a href="' . esc_url( $nectar_secondary_link ) . '">'; }
							  echo wp_kses_post( $options['secondary-header-text'] );
							if ( ! empty( $nectar_secondary_link ) ) {
								echo '</a>'; }
							echo '</div>';
						}
						?>
						
					  <ul class="menu">
						<?php

							// use default top nav menu if ocm is not activated
						  // but is needed for mobile when the mobile fixed nav is on
							wp_nav_menu(
								array(
									'theme_location' => 'top_nav',
									'container'      => '',
									'items_wrap'     => '%3$s',
								)
							);

						if ( $header_format == 'centered-menu' || $header_format == 'menu-left-aligned' ) {
							if ( has_nav_menu( 'top_nav_pull_right' ) ) {
									wp_nav_menu(
										array(
											'walker'     => new Nectar_Arrow_Walker_Nav_Menu(),
											'theme_location' => 'top_nav_pull_right',
											'container'  => '',
											'items_wrap' => '%3$s',
										)
									);
							}
						}

						if ( $header_format == 'centered-menu-bottom-bar' ) {
							if ( has_nav_menu( 'top_nav_pull_left' ) ) {
								wp_nav_menu(
									array(
										'walker'         => new Nectar_Arrow_Walker_Nav_Menu(),
										'theme_location' => 'top_nav_pull_left',
										'container'      => '',
										'items_wrap'     => '%3$s',
									)
								);
							}
						}

						?>
		
					</ul>

					<ul class="menu secondary-header-items">
					<?php
						// material secondary nav in menu
						$using_secondary = ( ! empty( $options['header_layout'] ) && $header_format != 'left-header' ) ? $options['header_layout'] : ' ';
					if ( ( $theme_skin == 'material' && $using_secondary == 'header_with_secondary' && has_nav_menu( 'secondary_nav' ) ) ||
							 ( $theme_skin != 'material' && $mobile_fixed == '1' && $using_secondary == 'header_with_secondary' && has_nav_menu( 'secondary_nav' ) ) ) {
																wp_nav_menu(
																	array(
																		'walker'         => new Nectar_Arrow_Walker_Nav_Menu(),
																		'theme_location' => 'secondary_nav',
																		'container'      => '',
																		'items_wrap'     => '%3$s',
																	)
																);
					}
					?>
					</ul>
				</div>
				<?php
			}

			if ( has_nav_menu( 'off_canvas_nav' ) && $user_set_side_widget_area != 'off' ) {
				?>
			  <div class="off-canvas-menu-container">
				  <ul class="menu">
						  <?php
							wp_nav_menu(
								array(
									'theme_location' => 'off_canvas_nav',
									'container'      => '',
									'items_wrap'     => '%3$s',
								)
							);

							?>
							  
				</ul>
			</div>
			
				<?php
			}

			// widget area
			if ( $side_widget_class != 'slide-out-from-right-hover' ) {
				if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( 'Off Canvas Menu' ) ) :
elseif ( ! has_nav_menu( 'off_canvas_nav' ) && $user_set_side_widget_area != 'off' ) :
	?>
		
				  <div class="widget">			
					  
					   </div>
			  <?php
			 endif;

			}
			?>

		</div>

		<?php

			$using_social_or_bottomtext = ( ! empty( $options['header-slide-out-widget-area-social'] ) && $options['header-slide-out-widget-area-social'] == '1' || ! empty( $options['header-slide-out-widget-area-bottom-text'] ) ) ? true : false;

			echo '<div class="bottom-meta-wrap">';

			nectar_hook_ocm_bottom_meta();

		if ( $side_widget_class == 'slide-out-from-right-hover' ) {
			if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( 'Off Canvas Menu' ) ) :
elseif ( ! has_nav_menu( 'off_canvas_nav' ) && $user_set_side_widget_area != 'off' ) :
	?>
		
				  <div class="widget">			
					  
				   </div>
			  <?php
			 endif;

		}

			global $using_secondary;
			/*social icons*/
		if ( ! empty( $options['header-slide-out-widget-area-social'] ) && $options['header-slide-out-widget-area-social'] == '1' ) {
			$social_link_arr = array( 'twitter-url', 'facebook-url', 'vimeo-url', 'pinterest-url', 'linkedin-url', 'youtube-url', 'tumblr-url', 'dribbble-url', 'rss-url', 'github-url', 'behance-url', 'google-plus-url', 'instagram-url', 'stackexchange-url', 'soundcloud-url', 'flickr-url', 'spotify-url', 'vk-url', 'vine-url', 'houzz-url', 'yelp-url', 'bandcamp-url', 'tripadvisor-url', 'mixcloud-url', 'snapchat-url', 'telegram-url', 'slack-url', 'medium-url', 'phone-url', 'email-url' );
			$social_icon_arr = array( 'fa fa-twitter', 'fa fa-facebook', 'fa fa-vimeo', 'fa fa-pinterest', 'fa fa-linkedin', 'fa fa-youtube-play', 'fa fa-tumblr', 'fa fa-dribbble', 'fa fa-rss', 'fa fa-github-alt', 'fa fa-behance', 'fa fa-google-plus', 'fa fa-instagram', 'fa fa-stackexchange', 'fa fa-soundcloud', 'fa fa-flickr', 'icon-salient-spotify', 'fa fa-vk', 'fa-vine', 'fa-houzz', 'fa-yelp', 'fa-bandcamp', 'fa-tripadvisor', 'fa-mixcloud', 'fa fa-snapchat', 'fa fa-telegram', 'fa fa-slack', 'fa fa-medium', 'fa fa-phone', 'fa fa-envelope' );

			echo '<ul class="off-canvas-social-links">';

			for ( $i = 0; $i < count( $social_link_arr ); $i++ ) {

				if ( ! empty( $options[ $social_link_arr[ $i ] ] ) && strlen( $options[ $social_link_arr[ $i ] ] ) > 1 ) {
					echo '<li><a target="_blank" href="' . esc_url( $options[ $social_link_arr[ $i ] ] ) . '"><i class="' . esc_attr( $social_icon_arr[ $i ] ) . '"></i></a></li>';
				}
			}

					echo '</ul>';
		} elseif ( ! empty( $options['enable_social_in_header'] ) && $options['enable_social_in_header'] == '1' && $using_secondary != 'header_with_secondary' ) {
			echo '<ul class="off-canvas-social-links mobile-only">';
			nectar_header_social_icons( 'off-canvas' );
			echo '</ul>';
		}

			 /*bottom text*/
		if ( ! empty( $options['header-slide-out-widget-area-bottom-text'] ) ) {
			$desktop_social = ( ! empty( $options['enable_social_in_header'] ) && $options['enable_social_in_header'] == '1' ) ? 'false' : 'true';
			echo '<p class="bottom-text" data-has-desktop-social="' . esc_attr( $desktop_social ) . '">' . wp_kses_post( $options['header-slide-out-widget-area-bottom-text'] ) . '</p>';
		}

			echo '</div><!--/bottom-meta-wrap-->';

		if ( $side_widget_class == 'fullscreen' || $side_widget_class == 'fullscreen-alt' || ( $theme_skin == 'material' && $side_widget_class == 'slide-out-from-right' ) || ( $theme_skin == 'material' && $side_widget_class == 'slide-out-from-right-hover' ) ) {
			echo '</div> <!--/inner-wrap-->';
		}
		?>

	</div>
<?php }