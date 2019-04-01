<?php
/** @var string $dashboard */
/** @var string $check_url */
/** @var string $title */
/** @var string $type */
/** @var string $message */
/** @var string $extra */
/** @var bool $dismissible */
/** @var string $dismiss_url */
/** @var array $links */
?>
<div id="<?php echo "as3cfpro_license_notice_{$type}" ?>" class="as3cf-pro-license-notice notice notice-info important <?php echo $dismissible ? 'is-dismissible' : '' ?>">
	<p>
		<strong><?php echo $title; ?></strong> &mdash; <?php echo $message; ?>
	</p>

	<?php if ( $extra ) : ?>
		<p>
			<?php echo $extra; ?>
		</p>
	<?php endif; ?>

	<?php if ( $links ) : ?>
		<p class="notice-links">
			<?php echo join( ' | ', $links ) ?>
		</p>
	<?php endif; ?>

	<?php if ( $args['dismissible'] ) : // use inline script to omit need to enqueue a script dashboard-wide ?>
<script>
( function( $ ) {
	$( '#<?php echo "as3cfpro_license_notice_{$type}.is-dismissible" ?>' ).on( 'click', '.notice-dismiss', function() {
		$.get( '<?php echo $args['dismiss_url'] ?>' );
	} );
} )( jQuery );
</script>
	<?php endif ?>
</div>