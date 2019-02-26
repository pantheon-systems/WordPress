<?php
$default_rate = affiliate_wp()->settings->get( 'referral_rate', 20 );
$default_rate = affwp_abs_number_round( $default_rate );
?>
<div class="wrap">

	<h2><?php _e( 'New Affiliate', 'affiliate-wp' ); ?></h2>

	<form method="post" id="affwp_add_affiliate">

		<?php
		/**
		 * Fires at the top of the new-affiliate admin screen, just inside of the form element.
		 */
		do_action( 'affwp_new_affiliate_top' );
		?>

		<p><?php printf( __( 'Use this form to create a new affiliate account. Each affiliate is tied directly to a user account, so if the user account for the affiliate does not yet exist, <a href="%s" target="_blank">create one</a>.', 'affiliate-wp' ), admin_url( 'user-new.php' ) ); ?></p>

		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="user_name"><?php _e( 'User login name', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<span class="affwp-ajax-search-wrap">
						<input type="text" name="user_name" id="user_name" class="affwp-user-search affwp-enable-on-complete" data-affwp-status="bypass" autocomplete="off" />
					</span>
					<p class="search-description description"><?php _e( 'Begin typing the name of the affiliate to perform a search for their associated user account.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row hidden affwp-user-email-wrap">

				<th scope="row">
					<label for="user_email"><?php _e( 'User email', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="user_email" id="user_email" class="affwp-user-email" />
					<p class="description"><?php _e( 'Enter an email address for the new user.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row hidden affwp-user-pass-wrap">

				<th scope="row">
					<label for="user_email"><?php _e( 'User Password', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<p class="description"><?php _e( 'The password will be auto-generated and can be reset by the user or an administrator after the account is created.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row">

				<th scope="row">
					<label for="status"><?php _e( 'Affiliate Status', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<select name="status" id="status" disabled="disabled">
						<option value="active"><?php _e( 'Active', 'affiliate-wp' ); ?></option>
						<option value="inactive"><?php _e( 'Inactive', 'affiliate-wp' ); ?></option>
						<option value="pending"><?php _e( 'Pending', 'affiliate-wp' ); ?></option>
					</select>
					<p class="description"><?php _e( 'The status assigned to the affiliate&#8217;s account.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row">

				<th scope="row">
					<label for="rate_type"><?php _e( 'Referral Rate Type', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<select name="rate_type" id="rate_type" disabled="disabled">
						<option value=""><?php _e( 'Site Default', 'affiliate-wp' ); ?></option>
						<?php foreach( affwp_get_affiliate_rate_types() as $key => $type ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $type ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php _e( 'The affiliate&#8217;s referral rate type.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row">

				<th scope="row">
					<label for="rate"><?php _e( 'Referral Rate', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input class="small-text" type="number" name="rate" id="rate" step="0.01" min="0" max="999999" placeholder="<?php echo esc_attr( $default_rate ); ?>" disabled="disabled" />
					<p class="description"><?php _e( 'The affiliate&#8217;s referral rate, such as 20 for 20%. If left blank, the site default will be used.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row">

				<th scope="row">
					<label for="payment_email"><?php _e( 'Payment Email', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input class="regular-text" type="text" name="payment_email" id="payment_email" disabled="disabled" />
					<p class="description"><?php _e( 'Affiliate&#8217;s payment email for systems such as PayPal, Moneybookers, or others. Leave blank to use the affiliate&#8217;s user email.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row">

				<th scope="row">
					<label for="notes"><?php _e( 'Affiliate Notes', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<textarea name="notes" rows="5" cols="50" id="notes" class="large-text" disabled="disabled"></textarea>
					<p class="description"><?php _e( 'Enter any notes for this affiliate. Notes are only visible to the admin.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row" id="affwp-welcome-email-row">

				<th scope="row">
					<label for="welcome_email"><?php _e( 'Welcome Email', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<label class="description">
						<input type="checkbox" name="welcome_email" id="welcome_email" value="1" disabled="disabled"/>
						<?php _e( 'Send welcome email after registering affiliate?', 'affiliate-wp' ); ?>
					</label>
				</td>

			</tr>

			<?php
			/**
			 * Fires at the end of the new-affiliate admin screen form area, below form fields.
			 */
			do_action( 'affwp_new_affiliate_end' );
			?>

		</table>

		<?php
		/**
		 * Fires at the bottom of the new-affiliate admin screen, prior to the submit button.
		 */
		do_action( 'affwp_new_affiliate_bottom' );
		?>

		<input type="hidden" name="affwp_action" value="add_affiliate" />

		<?php submit_button( __( 'Add Affiliate', 'affiliate-wp' ) ); ?>

	</form>

</div>
