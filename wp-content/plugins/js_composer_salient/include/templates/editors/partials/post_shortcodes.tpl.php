<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<script type="text/javascript">
	window.vc_post_shortcodes = JSON.parse( decodeURIComponent( ("<?php echo rawurlencode( json_encode( $editor->post_shortcodes ) ); ?>" + '') ) );
</script>

<?php /*
 <?php echo json_encode($editor->post_shortcodes); ?>
 JSON.parse(decodeURIComponent(("<?php echo urlencode(json_encode($editor->post_shortcodes)); ?>"+'').replace(/\+/g,'%20')));
 */
?>
