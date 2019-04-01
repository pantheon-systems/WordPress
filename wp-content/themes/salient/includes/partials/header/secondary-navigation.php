<?php
/**
 * Secondary navigation bar
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

$using_page_header        = using_page_header( $post->ID );
$header_format            = ( ! empty( $options['header_format'] ) ) ? $options['header_format'] : 'default';
$header_link_hover_effect = ( ! empty( $options['header-hover-effect'] ) ) ? $options['header-hover-effect'] : 'default';
$mobile_fixed             = ( ! empty( $options['header-mobile-fixed'] ) ) ? $options['header-mobile-fixed'] : 'false';
$bg_header                = ( ! empty( $post->ID ) && $post->ID != 0 ) ? $using_page_header : 0;
$bg_header                = ( $bg_header == 1 ) ? 'true' : 'false';
$trans_header             = ( ! empty( $options['transparent-header'] ) && $options['transparent-header'] == '1' ) ? $options['transparent-header'] : 'false';
$perm_trans               = ( ! empty( $options['header-permanent-transparent'] ) && $trans_header != 'false' && $bg_header == 'true' && $header_format != 'centered-menu-bottom-bar' ) ? $options['header-permanent-transparent'] : 'false';

$using_secondary       = ( ! empty( $options['header_layout'] ) && $header_format != 'left-header' ) ? $options['header_layout'] : ' ';
$secondary_header_text = ( ! empty( $options['secondary-header-text'] ) ) ? 'true' : 'false';

if ( $using_secondary == 'header_with_secondary' ) { ?>

<div id="header-secondary-outer" data-lhe="<?php echo esc_attr( $header_link_hover_effect ); ?>" data-secondary-text="<?php echo esc_attr( $secondary_header_text ); ?>" data-full-width="<?php echo ( ! empty( $options['header-fullwidth'] ) && $options['header-fullwidth'] == '1' ) ? 'true' : 'false'; ?>" data-mobile-fixed="<?php echo esc_attr( $mobile_fixed ); ?>" data-permanent-transparent="<?php echo esc_attr( $perm_trans ); ?>" >
	<div class="container">
		<nav>
	<?php
	if ( ! empty( $options['enable_social_in_header'] ) && $options['enable_social_in_header'] == '1' && $header_format != 'centered-menu-bottom-bar' ) {
		nectar_header_social_icons( 'secondary-nav' );
	}
	?>
			 
	<?php
	if ( $secondary_header_text ) {
		$nectar_secondary_link = ( ! empty( $options['secondary-header-link'] ) ) ? $options['secondary-header-link'] : '';
		echo '<div class="nectar-center-text">';
		if ( ! empty( $nectar_secondary_link ) ) {
			echo '<a href="' . esc_url( $nectar_secondary_link ) . '">';
		}
		echo wp_kses_post( $options['secondary-header-text'] );
		if ( ! empty( $nectar_secondary_link ) ) {
			echo '</a>';
		}
		echo '</div>';
	}
	?>
			 
	<?php if ( has_nav_menu( 'secondary_nav' ) ) { ?>
				<ul class="sf-menu">
				<?php
				wp_nav_menu(
					array(
						'walker'         => new Nectar_Arrow_Walker_Nav_Menu(),
						'theme_location' => 'secondary_nav',
						'container'      => '',
						'items_wrap'     => '%3$s',
					)
				);
				nectar_hook_secondary_header_menu_items();

				?>
				</ul>
		<?php
}

?>
			 
		</nav>
	</div>
</div>

<?php }