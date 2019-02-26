<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<div class="<?php echo isset( $css_class ) && ! empty( $css_class ) ? esc_attr( $css_class ) : 'vc_navbar' ?>"
	role="navigation"
	id="vc_navbar">
	<div class="vc_navbar-header">
		<?php echo $nav_bar->getLogo() ?>
	</div>
	<ul class="vc_navbar-nav">
		<?php foreach ( $controls as $control ) : echo $control[1]; endforeach; ?>
	</ul>
	<!--/.nav-collapse -->
</div>
