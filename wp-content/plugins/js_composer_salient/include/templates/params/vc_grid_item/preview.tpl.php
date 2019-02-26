<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>"/>
	<meta name="viewport" content="width=device-width"/>
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php wp_head(); ?>
	<style type="text/css">
		body {
			background-color: #FFF;
			color: #000;
			font-size: 12px;
		}

		<?php echo visual_composer()->parseShortcodesCustomCss( $shortcodes_string ) ?>
		.vc_gitem-preview {
			margin: 60px auto;
		}

		.vc_gitem-preview .vc_grid-item {
			display: block;
			margin: 0 auto;
		}

		.vc_grid-item-width-dropdown {
			margin-top: 10px;
			text-align: center;
		}

		.vc_container {
			margin: 0 15px;
		}

		img {
			width: 100%;
		}
	</style>
</head>
<div id="vc_grid-item-primary" class="vc_grid-item-site-content">
	<div id="vc_grid-item-content" role="vc_grid-item-main">
		<div class="vc_gitem-preview" data-vc-grid-settings="{}">
			<div class="vc_container">
				<div class="vc_row">
					<?php echo $grid_item->renderItem( $post ); ?>
				</div>
			</div>

		</div>
	</div>
	<!-- #content -->
</div>
<!-- #primary -->
<?php wp_footer(); ?>
<script type="text/javascript">
	var currentWidth = '<?php echo $default_width_value ?>',
		vcSetItemWidth = function ( value ) {
			jQuery( '.vc_grid-item' ).removeClass( 'vc_col-sm-' + currentWidth )
				.addClass( 'vc_col-sm-' + value );
			currentWidth = value;
		}, changeAnimation;
	changeAnimation = function ( animation ) {
		var $animatedBlock, prevAnimation;
		$animatedBlock = jQuery( '.vc_gitem-animated-block' );
		prevAnimation = $animatedBlock.data( 'vcAnimation' );
		$animatedBlock.hide()
			.addClass( 'vc_gitem-animate vc_gitem-animate-' + animation )
			.removeClass( 'vc_gitem-animate-' + prevAnimation )
			.data( 'vcAnimation', animation );
		setTimeout( function () {
			$animatedBlock.show();
		}, 100 );
	};
	jQuery( document ).ready( function ( $ ) {
		window.parent.vc && window.parent.vc.app.showPreview( currentWidth );
	} );
</script>
</body>
</html>
