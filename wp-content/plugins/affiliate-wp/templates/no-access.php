<?php if( current_user_can( 'manage_affiliates' ) ) : ?>
	<p class="no-access"><?php _e( 'To see the Affiliate Area, log in as an existing affiliate, or add your account as an affiliate.', 'affiliate-wp' ); ?></p>
<?php else : ?>
	<p class="no-access"><?php _e( 'The affiliate area is available only for registered affiliates.', 'affiliate-wp' ); ?></p>
<?php endif; ?>