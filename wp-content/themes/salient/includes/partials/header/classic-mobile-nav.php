<?php
/**
 * Classic mobile navigation
 *
 * @package    Salient WordPress Theme
 * @subpackage Partials
 * @version    9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$options = get_nectar_theme_options();

$header_format = ( ! empty( $options['header_format'] ) ) ? $options['header_format'] : 'default';
$mobile_fixed  = ( ! empty( $options['header-mobile-fixed'] ) ) ? $options['header-mobile-fixed'] : 'false';
$has_main_menu = ( has_nav_menu( 'top_nav' ) ) ? 'true' : 'false';

// using pr
$using_pr_menu = 'false';
if ( $header_format == 'menu-left-aligned' || $header_format == 'centered-menu' ) {
	if ( has_nav_menu( 'top_nav_pull_right' ) ) {
		$using_pr_menu = 'true';
	}
}


?>

<div id="mobile-menu" data-mobile-fixed="<?php echo esc_attr( $mobile_fixed ); ?>">    
  <div class="container">
	<ul>
		<?php
		if ( $has_main_menu == 'true' && $mobile_fixed == 'false' ) {

			wp_nav_menu(
				array(
					'theme_location' => 'top_nav',
					'menu'           => 'Top Navigation Menu',
					'container'      => '',
					'items_wrap'     => '%3$s',
				)
			);

			if ( $header_format == 'centered-menu' && $using_pr_menu == 'true' || $header_format == 'menu-left-aligned' && $using_pr_menu == 'true' ) {
				wp_nav_menu(
					array(
						'walker'         => new Nectar_Arrow_Walker_Nav_Menu(),
						'theme_location' => 'top_nav_pull_right',
						'container'      => '',
						'items_wrap'     => '%3$s',
					)
				);
				nectar_hook_pull_right_menu_items();
			}

			echo '<li id="mobile-search">  
          <form action="' . esc_url( home_url() ) . '" method="GET">
                <input type="text" name="s" value="" placeholder="' . esc_attr__( 'Search..', 'salient' ) . '" />
          </form> 
          </li>';
		} else {
			echo '<li><a href="">No menu assigned!</a></li>';
		}
		?>
				 
	</ul>
  </div>
  
</div>
