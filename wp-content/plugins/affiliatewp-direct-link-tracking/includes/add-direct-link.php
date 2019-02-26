<div class="wrap">

	<h2><?php _e( 'New Direct Link', 'affiliatewp-direct-link-tracking' ); ?></h2>

	<form method="post" id="affwp_add_direct_link">

		<?php do_action( 'affwp_direct_link_tracking_new_direct_link_top' ); ?>

		<p><?php _e( 'Use this screen to manually create a new direct link for an affiliate.', 'affiliatewp-direct-link-tracking' ); ?></p>

		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="user_name"><?php _e( 'Affiliate', 'affiliatewp-direct-link-tracking' ); ?></label>
				</th>

				<td>
					<span class="affwp-ajax-search-wrap">
						<input type="text" name="user_name" id="user_name" class="affwp-user-search" data-affwp-status="active" autocomplete="off" />
					</span>
					<p class="description"><?php _e( 'Enter the name of the affiliate or enter a partial name or email to perform a search.', 'affiliatewp-direct-link-tracking' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="url"><?php _e( 'Domain', 'affiliatewp-direct-link-tracking' ); ?></label>
				</th>

				<td>
					<input type="text" class="regular-text" name="url" id="url" />
					<p class="description"><?php _e( 'Enter a domain for this direct link.', 'affiliatewp-direct-link-tracking' ); ?></p>
				</td>

			</tr>

		</table>

		<?php do_action( 'affwp_direct_link_tracking_new_direct_link_bottom' ); ?>

		<?php echo wp_nonce_field( 'affwp_add_direct_link_nonce', 'affwp_add_direct_link_nonce' ); ?>
		<input type="hidden" name="affwp_action" value="add_direct_link" />

		<?php submit_button( __( 'Add Direct Link', 'affiliatewp-direct-link-tracking' ) ); ?>

	</form>

</div>
