<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$editAccess = vc_user_access_check_shortcode_edit( $shortcode );
$allAccess = vc_user_access_check_shortcode_all( $shortcode );

?>
<div class="vc_controls<?php echo ! empty( $extended_css ) ? ' ' . $extended_css : '' ?>">
	<div class="vc_controls-<?php echo $position ?>">
		<a class="vc_element-name">
			<span class="vc_btn-content"><?php echo $name ?></span>
		</a>
			<?php foreach ( $controls as $control ) : ?>
				<?php if ( 'add' === $control && vc_user_access()
						->part( 'shortcodes' )
						->checkStateAny( true, 'custom', null )
						->get() ) : ?>
					<a class="vc_control-btn vc_control-btn-prepend vc_edit" href="#"
					   title="<?php printf( __( 'Prepend to %s', 'js_composer' ), $name ) ?>"><span
							class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-add"></i></span></a>
				<?php elseif ( $editAccess && 'edit' === $control ) :  ?>
					<a class="vc_control-btn vc_control-btn-edit" href="#"
					   title="<?php printf( __( 'Edit %s', 'js_composer' ), $name ) ?>"><span
							class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-mode_edit"></i></span></a>
				<?php elseif ( $allAccess && 'clone' === $control ) :  ?>
					<a class="vc_control-btn vc_control-btn-clone" href="#"
					   title="<?php printf( __( 'Clone %s', 'js_composer' ), $name ) ?>"><span
							class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-content_copy"></i></span></a>
				<?php elseif ( $allAccess && 'delete' === $control ) :  ?>
					<a class="vc_control-btn vc_control-btn-delete" href="#"
					   title="<?php printf( __( 'Delete %s', 'js_composer' ), $name ) ?>"><span
							class="vc_btn-content"><i class="vc-composer-icon vc-c-icon-delete_empty"></i></span></a>
				<?php endif ?>
			<?php endforeach ?>
	</div>
</div>
