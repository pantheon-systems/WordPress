<?php
/**
 * Post single bottom meta bar - used only with the Ascend theme skin when the fullscreen header layout is in use.
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

$fullscreen_header                 = ( ! empty( $options['blog_header_type'] ) && $options['blog_header_type'] == 'fullscreen' && is_singular( 'post' ) ) ? true : false;
$fullscreen_class                  = ( $fullscreen_header == true ) ? 'fullscreen-header full-width-content' : null;
$remove_single_post_comment_number = ( ! empty( $options['blog_remove_single_comment_number'] ) ) ? $options['blog_remove_single_comment_number'] : '0';
$blog_social_style                 = ( ! empty( $options['blog_social_style'] ) ) ? $options['blog_social_style'] : 'default';

?>

<div id="single-below-header" class="<?php echo esc_attr( $fullscreen_class ); // WPCS: XSS ok. ?> custom-skip" data-remove-post-comment-number="<?php echo esc_attr( $remove_single_post_comment_number ); ?>">
	<?php if ( $blog_social_style != 'fixed_bottom_right' ) { ?>
	<span class="meta-share-count"><i class="icon-default-style steadysets-icon-share"></i> 
		<?php
		echo '<a href=""><span class="share-count-total">0</span> <span class="plural">' . esc_html__( 'Shares', 'salient' ) . '</span> <span class="singular">' . esc_html__( 'Share', 'salient' ) . '</span> </a>';
		nectar_blog_social_sharing();
		?>
	 </span>
	<?php } else { ?>
	<span class="meta-love"><span class="n-shortcode"> <?php echo nectar_love( 'return' ); ?>  </span></span>
	<?php } ?>
  <span class="meta-category"><i class="icon-default-style steadysets-icon-book2"></i> <?php the_category( ', ' ); ?></span>
  <span class="meta-comment-count"><i class="icon-default-style steadysets-icon-chat-3"></i> <a class="comments-link" href="<?php comments_link(); ?>"><?php comments_number( esc_html__( 'No Comments', 'salient' ), esc_html__( 'One Comment ', 'salient' ), esc_html__( '% Comments', 'salient' ) ); ?></a></span>
</div><!--/single-below-header-->
