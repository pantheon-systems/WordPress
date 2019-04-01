<div class="wpses-sidebar">

	<a class="wpses-banner" href="https://deliciousbrains.com/wp-offload-ses/upgrade/"></a>

	<div class="wpses-upgrade-details">
		<h1><?php _e( 'Upgrade', 'wp-ses' ); ?></h1>
		<h3><?php _e( 'Get email open and click tracking for all your Amazon SES emails', 'wp-ses' ); ?></h3>
		<ul>
			<li><?php _e( 'Track email opens and link clicks', 'wp-ses' ); ?></li>
			<li><?php _e( 'Log sent emails and failures', 'wp-ses' ); ?></li>
			<li><?php _e( 'Queue email to handle rate limits and retry failures', 'wp-ses' ); ?></li>
			<li><?php _e( 'Step-by-step setup wizard encouraging best practices', 'wp-ses' ); ?></li>
			<li><?php _e( 'Email support', 'wp-ses' ); ?></li>
		</ul>

		<p style="margin-bottom: 0;">
			<a href="https://deliciousbrains.com/wp-offload-ses/upgrade/"><?php _e( 'Visit deliciousbrains.com →', 'wp-ses' ); ?></a>
		</p>
	</div>

	<form method="post" action="https://deliciousbrains.com/email-subscribe/" target="_blank" class="wpses-subscribe wpses-block">
		<h2><?php _e( 'Get 40% Off!', 'wp-ses' ); ?></h2>

		<?php $user = wp_get_current_user(); ?>

		<p class="wpses-interesting">
			<?php echo wptexturize( __( "We're celebrating the launch of WP Offload SES with 40% off! Submit your name and email and we'll send you a discount for 40% off the upgrade (limited time only)", 'wp-ses' ) ); ?>
		</p>

		<div class="wpses-field">
			<input type="email" name="email" value="<?php echo esc_attr( $user->user_email ); ?>" placeholder="<?php _e( 'Your Email', 'wp-ses' ); ?>"/>
		</div>

		<div class="wpses-field">
			<input type="text" name="first_name" value="<?php echo esc_attr( trim( $user->first_name ) ); ?>" placeholder="<?php _e( 'First Name', 'wp-ses' ); ?>"/>
		</div>

		<div class="wpses-field">
			<input type="text" name="last_name" value="<?php echo esc_attr( trim( $user->last_name ) ); ?>" placeholder="<?php _e( 'Last Name', 'wp-ses' ); ?>"/>
		</div>

		<input type="hidden" name="campaigns[]" value="22" />
		<input type="hidden" name="source" value="14" />

		<div class="wpses-field wpses-subscribe-button">
			<button type="submit" class="button"><?php _e( 'Send me the coupon', 'wp-ses' ); ?></button>
		</div>

		<p class="promise"><?php _e( 'We promise we will not use your email for anything else and you can unsubscribe with 1-click anytime.', 'wp-ses' ); ?></p>
	</form>

	<div class="wpses-block credits">
		<h4>Created &amp; maintained by</h4>
		<ul>
			<li>
				<a href="https://deliciousbrains.com/?utm_campaign=WP%2BOffload%2BSES%2BUpgrade&utm_source=OSES%2BFree&utm_medium=insideplugin">
					<img src="//www.gravatar.com/avatar/e62fc2e9c8d9fc6edd4fea5339036a91?size=64" alt="" width="32" height="32">
					<span>Delicious Brains Inc.</span>
				</a>
			</li>
		</ul>
	</div>

</div>
