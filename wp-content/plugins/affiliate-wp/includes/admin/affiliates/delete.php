<?php
if( ! empty( $_GET['affiliate_id'] ) && is_array( $_GET['affiliate_id'] ) ) {

	$to_delete = array_map( 'absint', $_GET['affiliate_id'] );

} else {

	$to_delete = ! empty( $_GET['affiliate_id'] ) ? array( absint( $_GET['affiliate_id'] ) ) : array();

}

// Number of affiliate accounts marked for deletion.
$to_delete_count = count( $to_delete );

// Get user IDs of affiliates marked for deletion.
$affiliates_to_delete = affiliate_wp()->affiliates->get_affiliates( array(
	'number'       => $to_delete_count,
	'affiliate_id' => $to_delete
) );

// Is the current user's affiliate account flagged for deletion?
$deleting_self = in_array( get_current_user_id(), wp_list_pluck( $affiliates_to_delete, 'user_id' ) );

// Is the current user's affiliate account the only one flagged for deletion?
$deleting_only_self = ( 1 == $to_delete_count && $deleting_self ) ? true : false;

?>
<div class="wrap">

	<h2><?php _e( 'Delete Affiliates', 'affiliate-wp' ); ?></h2>

	<form method="post" id="affwp_delete_affiliate">

		<?php
		/**
		 * Fires at the top of the delete affiliate admin screen.
		 *
		 * @param int $to_delete Affiliate ID to delete.
		 */
		do_action( 'affwp_delete_affiliate_top', $to_delete );
		?>

		<p><?php echo _n(
			'You have specified the following affiliate for deletion:',
			'You have specified the following affiliates for deletion:',
			$to_delete_count,
			'affiliate-wp'
		); ?></p>

		<ul>
		<?php foreach( $to_delete as $affiliate_id ) : ?>

			<li>
				<?php printf( _x( 'ID #%d: %s', 'Affiliate ID, affiliate name', 'affiliate-wp' ), $affiliate_id, affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id ) ); ?>
				<input type="hidden" name="affwp_affiliate_ids[]" value="<?php echo esc_attr( $affiliate_id ); ?>"/>
			</li>

		<?php endforeach; ?>
		</ul>

		<p>
			<?php echo _n(
				'Deleting this affiliate will also delete their referral and visit data.',
				'Deleting these affiliates will also delete their referral and visit data.',
				$to_delete_count,
				'affiliate-wp'
			); ?>
		</p>

		<?php if ( current_user_can( 'delete_users' ) && false === $deleting_only_self ) : ?>
			<?php
			// If the current user's affiliate account is flagged for deletion with the group, show a reassuring message.
			if ( $deleting_self ) :
				$self_delete_notice = ' <strong>' . __( '(The current user will not be deleted)', 'affiliate-wp' ) . '</strong>';
			else :
				$self_delete_notice = '';
			endif;
			?>
			<p>
				<label for="affwp_delete_users_too">
					<input type="checkbox" name="affwp_delete_users_too" id="affwp_delete_users_too" value="1" />
					<?php
					echo _n(
						'Delete this affiliate&#8217;s user account as well?',
						sprintf( 'Delete these affiliates&#8217; user accounts as well?%s', $self_delete_notice ),
						$to_delete_count,
						'affiliate-wp'
					); ?>
				</label>
			</p>
		<?php endif; ?>

		<?php
		/**
		 * Fires at the bottom of the delete affiliate admin screen.
		 *
		 * @param int $to_delete Affiliate ID to delete.
		 */
		do_action( 'affwp_delete_affiliate_bottom', $to_delete );
		?>

		<input type="hidden" name="affwp_action" value="delete_affiliates" />
		<?php echo wp_nonce_field( 'affwp_delete_affiliates_nonce', 'affwp_delete_affiliates_nonce' ); ?>

		<?php submit_button( __( 'Confirm Deletion', 'affiliate-wp' ) ); ?>

	</form>

</div>
