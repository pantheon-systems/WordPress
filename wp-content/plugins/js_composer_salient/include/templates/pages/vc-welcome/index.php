<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
preg_match( '/^(\d+)(\.\d+)?/', WPB_VC_VERSION, $matches );
?>
<div class="wrap vc-page-welcome about-wrap">
	<h1><?php echo sprintf( __( 'Welcome to WPBakery Page Builder %s', 'js_composer' ), isset( $matches[0] ) ? $matches[0] : WPB_VC_VERSION ) ?></h1>

	<div class="about-text">
		<?php _e( 'Congratulations! You are about to use most powerful time saver for WordPress ever - page builder plugin with Frontend and Backend editors by WPBakery.', 'js_composer' ) ?>
	</div>
	<div class="wp-badge vc-page-logo">
		<?php echo sprintf( __( 'Version %s', 'js_composer' ), WPB_VC_VERSION ) ?>
	</div>
	<p class="vc-page-actions">
		<?php if ( vc_user_access()
				->wpAny( 'manage_options' )
				->part( 'settings' )
				->can( 'vc-general-tab' )
				->get() && ( ! is_multisite() || ! is_main_site() )
		) : ?>
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=vc-general' ) ) ?>"
			class="button button-primary"><?php _e( 'Settings', 'js_composer' ) ?></a><?php endif; ?>
		<a href="https://twitter.com/share" class="twitter-share-button"
			data-via="wpbakery"
			data-text="Take full control over your #WordPress site with WPBakery Page Builder page builder"
			data-url="http://wpbakery.com" data-size="large">Tweet</a>
		<script>! function ( d, s, id ) {
				var js, fjs = d.getElementsByTagName( s )[ 0 ], p = /^http:/.test( d.location ) ? 'http' : 'https';
				if ( ! d.getElementById( id ) ) {
					js = d.createElement( s );
					js.id = id;
					js.src = p + '://platform.twitter.com/widgets.js';
					fjs.parentNode.insertBefore( js, fjs );
				}
			}( document, 'script', 'twitter-wjs' );</script>
	</p>
	<?php vc_include_template( '/pages/partials/_tabs.php', array(
			'slug' => $page->getSlug(),
			'active_tab' => $active_page->getSlug(),
			'tabs' => $pages,
		) );
	?>
	<?php echo $active_page->render(); ?>
</div>
