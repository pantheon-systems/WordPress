<?php
$sidebar_style = '';
// Don't show the sidebar when away from core settings.
if ( ! empty( $_GET['action'] ) ) {
	$sidebar_style = ' style="display: none;"';
}
?>
<div class="as3cf-sidebar pro"<?php echo $sidebar_style; ?>>
	<?php do_action( 'as3cfpro_sidebar' ); ?>
</div>
