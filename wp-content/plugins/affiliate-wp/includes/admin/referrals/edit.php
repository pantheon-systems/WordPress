<?php
$referral  = affwp_get_referral( absint( $_GET['referral_id'] ) );
$payout    = affwp_get_payout( $referral->payout_id );
$visit     = affwp_get_visit( $referral->visit_id );
$affiliate = affwp_get_affiliate( $referral->affiliate_id );

$disabled = disabled( (bool) $payout, true, false );

?>
<div class="wrap">

	<h2><?php _e( 'Edit Referral', 'affiliate-wp' ); ?></h2>

	<form method="post" id="affwp_edit_referral">

		<?php
		/**
		 * Fires at the top of the edit-referral admin screen.
		 *
		 * @param \AffWP\Referral $referral The referral object.
		 */
		do_action( 'affwp_edit_referral_top', $referral );
		?>

		<table class="form-table">


			<tr class="form-row form-required">

				<th scope="row">
					<label for="referral_id"><?php _e( 'Referral ID', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input class="small-text" type="text" name="referral_id" id="referral_id" value="<?php echo esc_attr( $referral->ID ); ?>" disabled="disabled"/>
					<p class="description"><?php _e( 'The referral ID. This cannot be changed.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="affiliate"><?php _e( 'Affiliate', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<p>
						<?php
						$affiliate_name = affiliate_wp()->affiliates->get_affiliate_name( $affiliate->ID );

						if ( $affiliate && $affiliate_name ) {
							/* translators: 1: Affiliate link, 2: Affiliate ID */
							printf( __( '%1$s (ID: #%2$s)', 'affiliate-wp' ),
								affwp_admin_link( 'affiliates', $affiliate_name, array(
									'action'       => 'view_affiliate',
									'affiliate_id' => $affiliate->ID
								) ),
								esc_html( $affiliate->ID )
							);
						} else {
							esc_html_e( '(user deleted)', 'affiliate-wp' );
						}

						?>
					</p>
					<p class="description"><?php _e( 'The name and ID of the affiliate who generated this referral. This association cannot be changed.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="payout"><?php _e( 'Payout', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<?php if ( $payout ) : ?>

						<p>
							<?php
							/* translators: 1: Payout total amount with view link */
							$payout_total_link = sprintf( __( 'Total: %1$s', 'affiliate-wp' ),
								affwp_admin_link( 'payouts', affwp_currency_filter( affwp_format_amount( $payout->amount ) ), array(
									'action'    => 'view_payout',
									'payout_id' => $payout->ID
								) )
							);

							/* translators: 1: Payout link with total, 2: Payout ID */
							printf( __( '%1$s (ID: #%2$s)', 'affiliate-wp' ),
								$payout_total_link,
								esc_html( $payout->ID )
							);
							?>
						</p>

					<?php else : ?>

						<p>
							<?php
							if ( in_array( $referral->status, array( 'pending', 'unpaid' ), true ) ) {

								/* translators: 1: Pay Out action link */
								printf( __( 'None | %1$s', 'affiliate-wp' ),
									affwp_admin_link( 'referrals', __( 'Pay Out', 'affiliate-wp' ), array(
										'referral_id'  => $referral->ID,
										'action'       => 'mark_as_paid',
										'_wpnonce'     => wp_create_nonce( 'referral-nonce' ),
										'affwp_notice' => 'payout_created',
									) )
								);

							} else {
								esc_html_e( 'None', 'affiliate-wp' );
							}
							?>
						</p>

					<?php endif; ?>

				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="amount"><?php _e( 'Amount', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="amount" id="amount" value="<?php echo esc_attr( $referral->amount ); ?>" <?php echo $disabled; ?>/>
					<?php if ( $payout ) : ?>
						<p class="description"><?php esc_html_e( 'The referral amount cannot be changed once it has been included in a payout.', 'affiliate-wp' ); ?></p>
					<?php else : ?>
						<p class="description"><?php _e( 'The amount of the referral, such as 15.', 'affiliate-wp' ); ?></p>
					<?php endif; ?>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="date"><?php _e( 'Date', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="date" id="date" value="<?php echo esc_attr( $referral->date_i18n( 'datetime' ) ); ?>" disabled="disabled" />
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="visit"><?php _e( 'Visit', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<?php if ( $visit ) : ?>

						<p>
							<?php
							if ( empty( $visit->url ) ) {
								$visit_link = __( 'None', 'affiliate-wp' );
							} else {
								$visit_link = make_clickable( esc_url( $visit->url ) );
							}

							/* translators: 1: Visit link, 2: Visit ID */
							printf( __( 'URL: %1$s (ID: #%2$s)', 'affiliate-wp' ),
								$visit_link,
								esc_html( $visit->ID )
							);
							?>
						</p>

						<p>
							<?php
							/* translators: 1: Visit date */
							printf( _x( 'Date: %1$s (%2$s)', 'visit', 'affiliate-wp' ),
								$visit->date_i18n( 'date' ),
								$visit->date_i18n( 'time' )
							);
							?>
						</p>

					<?php else : ?>

						<?php _ex( 'None', 'visit', 'affiliate-wp' ); ?>

					<?php endif; ?>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="description"><?php _e( 'Description', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<textarea name="description" id="description" rows="5" cols="60"><?php echo esc_html( $referral->description ); ?></textarea>
					<p class="description"><?php _e( 'Enter a description for this referral.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="reference"><?php _e( 'Reference', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="reference" id="reference" value="<?php echo esc_attr( $referral->reference ); ?>" />
					<p class="description"><?php _e( 'Enter a reference for this referral (optional). Usually this would be the transaction ID of the associated purchase.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">
				<?php $readonly = __checked_selected_helper( true, ! empty( $referral->context ), false, 'readonly' ); ?>
				<th scope="row">
					<label for="context"><?php _e( 'Context', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="context" id="context" value="<?php echo esc_attr( $referral->context ); ?>" <?php echo $readonly; ?> />
					<p class="description"><?php _e( 'Context for this referral (optional). Usually this is used to identify the payment system or integration that was used for the transaction.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">
				<?php $readonly = __checked_selected_helper( true, ! empty( $referral->custom ), false, 'readonly' ); ?>
				<th scope="row">
					<label for="context"><?php _e( 'Custom', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="custom" id="custom" value="<?php echo esc_attr( $referral->custom ); ?>" <?php echo $readonly; ?> />
					<p class="description"><?php _e( 'Custom data stored for this referral (optional).', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="status"><?php _e( 'Status', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<select name="status" id="status" <?php echo $disabled; ?>>
						<option value="unpaid"<?php selected( 'unpaid', $referral->status ); ?>><?php _e( 'Unpaid', 'affiliate-wp' ); ?></option>
						<option value="paid"<?php selected( 'paid', $referral->status ); ?>><?php _e( 'Paid', 'affiliate-wp' ); ?></option>
						<option value="pending"<?php selected( 'pending', $referral->status ); ?>><?php _e( 'Pending', 'affiliate-wp' ); ?></option>
						<option value="rejected"<?php selected( 'rejected', $referral->status ); ?>><?php _e( 'Rejected', 'affiliate-wp' ); ?></option>
					</select>
					<?php if ( $payout ) : ?>
						<p class="description"><?php esc_html_e( 'The referral status cannot be changed once it has been included in a payout.', 'affiliate-wp' ); ?></p>
					<?php else : ?>
						<p class="description"><?php _e( 'Select the status of the referral.', 'affiliate-wp' ); ?></p>
					<?php endif; ?>
				</td>

			</tr>

		</table>

		<?php
		/**
		 * Fires at the bottom of the edit-referral admin screen (inside the form element).
		 *
		 * @param \AffWP\Referral $referral The referral object.
		 */
		do_action( 'affwp_edit_referral_bottom', $referral );
		?>

		<?php echo wp_nonce_field( 'affwp_edit_referral_nonce', 'affwp_edit_referral_nonce' ); ?>
		<input type="hidden" name="referral_id" value="<?php echo absint( $referral->referral_id ); ?>" />
		<input type="hidden" name="affwp_action" value="process_update_referral" />

		<?php submit_button( __( 'Update Referral', 'affiliate-wp' ) ); ?>

	</form>

</div>
