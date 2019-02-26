<?php
	$direct_link = affwp_dlt_get_direct_link( absint( $_GET['url_id'] ) );
	$affiliate   = affwp_get_affiliate( absint( $direct_link->affiliate_id ) );
	$user_info   = get_userdata( $affiliate->user_id );
?>
<div class="wrap">

	<h2><?php _e( 'Edit Direct Link', 'affiliatewp-direct-link-tracking' ); ?></h2>

	<form method="post" id="affwp_edit_direct_link">

		<?php do_action( 'affwp_direct_link_tracking_edit_direct_link_top' ); ?>

		<table class="form-table">

			<tr class="form-row">
				<th scope="row">
					<label for="user_login"><?php _e( 'Affiliate', 'affiliatewp-direct-link-tracking' ); ?></label>
				</th>

				<td>
					<input class="regular-text" type="text" name="user_login" id="user_login" value="<?php echo esc_attr( $user_info->user_login ); ?>" disabled />
					<p class="description"><?php _e( 'The affiliate this direct link belongs to. This cannot be changed.', 'affiliatewp-direct-link-tracking' ); ?></p>
				</td>
			</tr>

			<tr class="form-row">
				<th scope="row">
					<label for="affiliate_id"><?php _e( 'Affiliate ID', 'affiliatewp-direct-link-tracking' ); ?></label>
				</th>

				<td>
					<input class="medium-text" type="text" name="affiliate_id" id="affiliate_id" value="<?php echo esc_attr( $affiliate->affiliate_id ); ?>" disabled />
					<p class="description"><?php _e( 'The affiliate ID the direct link belongs to. This cannot be changed.', 'affiliatewp-direct-link-tracking' ); ?></p>
				</td>
			</tr>

			<tr class="form-row form-required">
				<th scope="row">
					<label for="url"><?php _e( 'Domain', 'affiliatewp-direct-link-tracking' ); ?></label>
				</th>

				<td>
					<input type="text" name="url" id="url" class="regular-text" value="<?php echo esc_attr( $direct_link->url ); ?>" />
					<p class="description"><?php _e( 'The domain of the direct link.', 'affiliatewp-direct-link-tracking' ); ?></p>
				</td>
			</tr>

			<tr class="form-row">
				<th scope="row">
					<label for="date_added"><?php _e( 'Date Added', 'affiliatewp-direct-link-tracking' ); ?></label>
				</th>

				<td>
					<input class="medium-text" type="text" name="date_added" id="date_added" value="<?php echo esc_attr( date_i18n( get_option( 'date_format' ), strtotime( $direct_link->date ) ) ); ?>" disabled />
					<p class="description"><?php _e( 'The date the direct link was added. This cannot be changed.', 'affiliatewp-direct-link-tracking' ); ?></p>
				</td>
			</tr>

			<tr class="form-row">
				<th scope="row">
					<label for="status"><?php _e( 'Status', 'affiliatewp-direct-link-tracking' ); ?></label>
				</th>

				<td>
					<select name="status" id="status">
						<option value="active" <?php selected( $direct_link->status, 'active' ); ?>><?php _e( 'Active', 'affiliatewp-direct-link-tracking' ); ?></option>
						<option value="inactive" <?php selected( $direct_link->status, 'inactive' ); ?>><?php _e( 'Inactive', 'affiliatewp-direct-link-tracking' ); ?></option>
						<option value="pending" <?php selected( $direct_link->status, 'pending' ); ?>><?php _e( 'Pending', 'affiliatewp-direct-link-tracking' ); ?></option>
						<option value="rejected" <?php selected( $direct_link->status, 'rejected' ); ?>><?php _e( 'Rejected', 'affiliatewp-direct-link-tracking' ); ?></option>
					</select>
					<p class="description"><?php _e( 'The status of the direct link.', 'affiliatewp-direct-link-tracking' ); ?></p>
				</td>
			</tr>

		</table>

		<?php do_action( 'affwp_direct_link_tracking_edit_direct_link_bottom' ); ?>

		<?php echo wp_nonce_field( 'affwp_edit_direct_link_nonce', 'affwp_edit_direct_link_nonce' ); ?>
		<input type="hidden" name="affwp_action" value="update_direct_link" />
		<input type="hidden" name="affiliate_id" value="<?php echo absint( $direct_link->affiliate_id ); ?>" />

		<?php submit_button( __( 'Update Direct Link', 'affiliatewp-direct-link-tracking' ) ); ?>

	</form>

</div>
