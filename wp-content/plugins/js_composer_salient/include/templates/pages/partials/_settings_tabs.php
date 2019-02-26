<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<h2 class="nav-tab-wrapper">
	<?php foreach ( $tabs as $slug => $title ) :  ?>
		<?php $url = 'admin.php?page=' . rawurlencode( $slug ); ?>
		<a href="<?php echo esc_attr( is_network_admin() ? network_admin_url( $url ) : admin_url( $url ) ) ?>"
		   class="nav-tab<?php echo $active_tab === $slug ? esc_attr( ' nav-tab-active' ) : '' ?>">
			<?php echo $title ?>
		</a>
	<?php endforeach ?>
</h2>
