<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$block = $block_data[0];
$settings = $block_data[1];
$link_setting = empty( $settings[0] ) ? '' : $settings[0];
?>
<?php if ( 'title' === $block ) :  ?>
	<h2 class="post-title">
		<?php echo empty( $link_setting ) || 'no_link' !== $link_setting ? $this->getLinked( $post, $post->title, $link_setting, 'link_title' ) : $post->title ?>
	</h2>
<?php elseif ( 'image' === $block && ! empty( $post->thumbnail ) ) :  ?>
	<div class="post-thumb">
		<?php echo empty( $link_setting ) || 'no_link' !== $link_setting ? $this->getLinked( $post, $post->thumbnail, $link_setting, 'link_image' ) : $post->thumbnail ?>
	</div>
<?php elseif ( 'text' === $block ) :  ?>
	<div class="entry-content">
		<?php echo empty( $link_setting ) || 'text' === $link_setting ? $post->content : $post->excerpt; ?>
	</div>
<?php elseif ( 'link' === $block ) :  ?>
	<a href="<?php echo $post->link ?>" class="vc_read_more" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'js_composer' ), $post->title_attribute ) ); ?>"<?php echo $this->link_target ?>><?php _e( 'Read more', 'js_composer' ) ?></a>
<?php endif ?>
