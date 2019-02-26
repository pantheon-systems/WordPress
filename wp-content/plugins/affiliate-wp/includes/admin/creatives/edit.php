<?php
$creative = affwp_get_creative( absint( $_GET['creative_id'] ) );
?>
<div class="wrap">

	<h2><?php _e( 'Edit Creative', 'affiliate-wp' ); ?></h2>

	<form method="post" id="affwp_edit_creative">

		<?php
		/**
		 * Fires at the top of the edit-creative admin screen.
		 *
		 * @param \AffWP\Creative $creative The creative object.
		 */
		do_action( 'affwp_edit_creative_top', $creative ); ?>

		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="name"><?php _e( 'Name', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="name" id="name" value="<?php echo esc_attr( stripslashes( $creative->name ) ); ?>" class="regular-text" />
					<p class="description"><?php _e( 'The name of this creative. For your identification only.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="name"><?php _e( 'Description', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<textarea name="description" id="description" class="large-text" rows="8"><?php echo esc_textarea( stripslashes( $creative->description ) ); ?></textarea>
					<p class="description"><?php _e( 'An optional description for this creative. This is visible to affiliates and is displayed below the creative.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="url"><?php _e( 'URL', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="url" id="url" value="<?php echo esc_url( $creative->url ); ?>" class="regular-text" />
					<p class="description"><?php _e( 'The URL this creative should link to. Based on your Referral Settings, the affiliate&#8217;s ID or username will be automatically appended.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="text"><?php _e( 'Text', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="text" id="text" value="<?php echo esc_attr( stripslashes( $creative->text ) ); ?>" class="regular-text" maxlength="255" />
					<p class="description"><?php _e( 'Text for this creative.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="image"><?php _e( 'Image', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input id="image" name="image" type="text" class="upload_field regular-text" value="<?php echo esc_attr( $creative->image ); ?>" />
					<input class="upload_image_button button-secondary" type="button" value="Choose Image" />
					<p class="description"><?php _e( 'Select your image. You can also enter an image URL if your image is hosted elsewhere.', 'affiliate-wp' ); ?></p>

					<?php if ( ! empty( $creative->image ) ) { ?>
						<div id="preview_image">
							<img src="<?php echo esc_attr( $creative->image ); ?>" />
						</div>
					<?php } else { ?>
						<div id="preview_image" style="display: none;">

						</div>
					<?php } ?>

				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="date"><?php _e( 'Created', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input class="medium-text" type="text" name="date" id="date" value="<?php echo esc_attr( $creative->date_i18n( 'datetime' ) ); ?>" disabled="1" />
					<p class="description"><?php _e( 'The date the creative was created. This cannot be changed.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="status"><?php _e( 'Status', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<select name="status" id="status">
						<option value="active"<?php selected( 'active', $creative->status ); ?>><?php _e( 'Active', 'affiliate-wp' ); ?></option>
						<option value="inactive"<?php selected( 'inactive',  $creative->status ); ?>><?php _e( 'Inactive', 'affiliate-wp' ); ?></option>
					</select>
					<p class="description"><?php _e( 'Select the status of the creative.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

		</table>

		<?php
		/**
		 * Fires at the bottom of the edit-creative admin screen.
		 *
		 * @param \AffWP\Creative $creative The creative object.
		 */
		do_action( 'affwp_edit_creative_bottom', $creative );
		?>

		<input type="hidden" name="affwp_action" value="update_creative" />

		<?php submit_button( __( 'Update Creative', 'affiliate-wp' ) ); ?>

	</form>

</div>
