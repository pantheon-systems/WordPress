<?php
/**
 * Post single author bio - used only with the Ascend theme skin.
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

$theme_skin    = ( ! empty( $options['theme-skin'] ) ) ? $options['theme-skin'] : 'original';
$header_format = ( ! empty( $options['header_format'] ) ) ? $options['header_format'] : 'default';
if ( $header_format == 'centered-menu-bottom-bar' ) {
	$theme_skin = 'material';
}
$fullscreen_header = ( ! empty( $options['blog_header_type'] ) && $options['blog_header_type'] == 'fullscreen' && is_singular( 'post' ) ) ? true : false;

$grav_size        = 80;
$fw_class         = 'full-width-section ';
$next_post        = get_previous_post();
$next_post_button = ( ! empty( $options['blog_next_post_link'] ) && $options['blog_next_post_link'] == '1' ) ? 'on' : 'off';
?>

<div id="author-bio" data-midnight="dark" class="<?php echo esc_attr( $fw_class ); // WPCS: XSS ok. ?> <?php if ( empty( $next_post ) || $next_post_button == 'off' || $fullscreen_header == false && $next_post_button == 'off' ) { echo 'no-pagination';} ?> ">
  <div class="span_12">
	<?php
	if ( function_exists( 'get_avatar' ) ) {
		echo get_avatar( get_the_author_meta( 'email' ), $grav_size, null, get_the_author() ); }
	?>
	<div id="author-info">
	  <h3><span><?php if ( $theme_skin == 'ascend' ) { echo '<i>' . esc_html__( 'Author', 'salient' ) . '</i>'; } else { _e( 'About', 'salient' ); } ?></span> <?php the_author(); ?></h3>
	  <p><?php the_author_meta( 'description' ); ?></p>
	</div>
	<?php
	if ( $theme_skin == 'ascend' ) {
		echo '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" data-hover-text-color-override="#fff" data-hover-color-override="false" data-color-override="#000000" class="nectar-button see-through-2 large">' . esc_html__( 'More posts by', 'salient' ) . ' ' . get_the_author() . ' </a>'; }
	?>
	<div class="clear"></div>
  </div>
</div>
