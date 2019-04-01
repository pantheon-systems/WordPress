<?php
/**
 * Post single author bio - used on all theme skins except Ascend.
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
$grav_size = 80;
$fw_class  = null;
$has_tags  = 'false';
if ( ! empty( $options['display_tags'] ) && $options['display_tags'] == true && has_tag() ) {
	$has_tags = 'true'; }

?>

<div id="author-bio" class="<?php echo esc_attr( $fw_class ); // WPCS: XSS ok. ?>" data-has-tags="<?php echo esc_attr( $has_tags );  // WPCS: XSS ok. ?>">
  <div class="span_12">
	<?php
	if ( function_exists( 'get_avatar' ) ) {
		echo get_avatar( get_the_author_meta( 'email' ), $grav_size, null, get_the_author() ); }
	?>
	<div id="author-info">
	  <h3><span><?php if ( $theme_skin == 'ascend' ) { _e( 'Author', 'salient' ); } elseif ( $theme_skin != 'material' ) { _e( 'About', 'salient' ); } ?></span> 
		
		<?php
		if ( $theme_skin == 'material' ) {
			echo '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '">'; }
		echo get_the_author();
		if ( $theme_skin == 'material' ) {
			echo '</a>'; }
		?>
		</h3>
	   
	  <p><?php the_author_meta( 'description' ); ?></p>
	</div>
	<?php
	if ( $theme_skin == 'ascend' ) {
		echo '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" data-hover-text-color-override="#fff" data-hover-color-override="false" data-color-override="#000000" class="nectar-button see-through-2 large"> ' . esc_html__( 'More posts by', 'salient' ) . ' ' . get_the_author() . ' </a>'; }
	?>
	<div class="clear"></div>
  </div>
</div>
