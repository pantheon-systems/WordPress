<?php
$affiliate        = affwp_get_affiliate( absint( $_GET['affiliate_id'] ) );
$affiliate_id     = $affiliate->affiliate_id;
$name             = affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );
$user_info        = get_userdata( $affiliate->user_id );
$user_url         = $user_info->user_url;
$promotion_method = get_user_meta( $affiliate->user_id, 'affwp_promotion_method', true );
?>
<div class="wrap">

	<h2><?php _e( 'Review Affiliate', 'affiliate-wp' ); ?> <?php affwp_admin_link( 'affiliates', __( 'Go Back', 'affiliate-wp' ), array(), array( 'class' => 'button-secondary' ) ); ?></h2>

	<form method="post" id="affwp_review_affiliate">

		<?php
		/**
		 * Fires at the top of the review-affiliate admin screen, just inside of the form element.
		 *
		 * @param \AffWP\Affiliate $affiliate Affiliate object.
		 */
		do_action( 'affwp_review_affiliate_top', $affiliate );
		?>

		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<?php _e( 'Name', 'affiliate-wp' ); ?>
				</th>

				<td>
					<?php echo esc_html( $name ); ?>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<?php _e( 'Username', 'affiliate-wp' ); ?>
				</th>

				<td>
					<?php echo esc_html( $user_info->user_login ); ?>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<?php _e( 'Email Address', 'affiliate-wp' ); ?>
				</th>

				<td>
					<?php echo esc_html( $user_info->user_email ); ?>
				</td>

			</tr>

			<?php if ( $user_url ) : ?>
			<tr class="form-row form-required">

				<th scope="row">
					<?php _e( 'Website URL', 'affiliate-wp' ); ?>
				</th>

				<td>
					<a href="<?php echo esc_url( $user_url ); ?>" title="<?php _e( 'Affiliate&#8217;s Website URL', 'affiliate-wp' ); ?>" target="blank"><?php echo esc_url( $user_url ); ?></a>
				</td>

			</tr>
			<?php endif; ?>

			<?php if ( $promotion_method ) : ?>
				<tr class="form-row form-required">

					<th scope="row">
						<?php _e( 'Promotion Method', 'affiliate-wp' ); ?>
					</th>

					<td>
						<?php echo esc_html( $promotion_method ); ?>
					</td>

				</tr>
			<?php endif; ?>

			<tr class="form-row" id="affwp-rejection-reason">

				<th scope="row">
					<?php _e( 'Rejection Reason', 'affiliate-wp' ); ?>
				</th>

				<td>
					<textarea class="large-text" name="affwp_rejection_reason" rows="10"></textarea>
					<p class="description"><?php _e( 'Leave blank if approving this affiliate.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<?php
			/**
			 * Fires at the end of the review-affiliate admin screen, prior to the closing table element tag.
			 *
			 * @param \AffWP\Affiliate $affiliate Affiliate object.
			 */
			do_action( 'affwp_review_affiliate_end', $affiliate );
			?>

		</table>

		<?php
		/**
		 * Fires at the bottom of the review-affiliate admin screen, just prior to the submit button.
		 *
		 * @param \AffWP\Affiliate $affiliate Affiliate object.
		 */
		do_action( 'affwp_review_affiliate_bottom', $affiliate );
		?>

		<?php wp_nonce_field( 'affwp_moderate_affiliates_nonce', 'affwp_moderate_affiliates_nonce' ); ?>
		<input type="hidden" name="affiliate_id" value="<?php echo esc_attr( absint( $affiliate_id ) ); ?>"/>
		<input type="hidden" name="affwp_action" value="moderate_affiliate"/>
		<input type="submit" name="affwp_accept" value="<?php esc_attr_e( __( 'Accept Affiliate', 'affiliate-wp' ) ); ?>" class="button button-primary"/>
		<input type="submit" name="affwp_reject" value="<?php esc_attr_e( __( 'Reject Affiliate', 'affiliate-wp' ) ); ?>" class="button button-secondary"/>

	</form>

</div>
