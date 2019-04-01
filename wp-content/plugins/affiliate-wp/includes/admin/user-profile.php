<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function affwp_user_profile_fields( $user ) {
  
	if( ! current_user_can( 'manage_affiliates' ) ) {
		return;
	}

	if( isset( $_GET['register_affiliate'] ) && 1 == absint( $_GET['register_affiliate'] ) ) {
		$affiliate_id = affwp_add_affiliate( array( 'user_id' => $user->ID ) );
	} else {
		$affiliate_id = affwp_get_affiliate_id( $user->ID );
	}

	$affiliate = 0 < absint( $affiliate_id ) ? affwp_get_affiliate( $affiliate_id ) : false;
	?>
	<h3><?php esc_attr_e( 'AffiliateWP', 'affiliate-wp' ); ?></h3>
	<table class="form-table">
		<tr>
			<th><label><?php esc_attr_e( 'Affiliate Registration Status', 'affiliate-wp' ); ?></label></th>
			<td>
				<?php if( $affiliate ):
					switch( $affiliate->status ) {
						case 'active':
							echo '<span style="color: green; font-weight: bold;">' . esc_attr( 'Active', 'affiliate-wp' ) . '</span>';
							break;
						case 'inactive':
							echo '<span>' . esc_attr( 'Inactive', 'affiliate-wp' ) . '</span>';
							break;
						case 'pending':
							echo '<span style="color: silver;">' . esc_attr( 'Pending', 'affiliate-wp' ) . '</span>';
							break;
						case 'rejected':
							echo '<span style="color: red; font-weight: bold;">' . esc_attr( 'Rejected', 'affiliate-wp' ) . '</span>';
							break;
					}
				else: ?>
				<span style="color: red;"><?php esc_attr_e( 'Not Registered', 'affiliate-wp' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>

		<tr>
			<th><label><?php esc_attr_e( 'Affiliate Actions', 'affiliate-wp' ); ?></label></th>
			<td>
				<?php if( $affiliate ):
					echo affwp_admin_link(
						'affiliates',
						__( 'Reports', 'affiliate-wp' ),
						array(
							'affwp_notice' => false,
							'affiliate_id' => $affiliate->affiliate_id,
							'action'       => 'view_affiliate'
						),
						array( 'class' => 'button' )
					);
					echo ' ';
					echo affwp_admin_link(
						'affiliates',
						__( 'Edit', 'affiliate-wp' ),
						array(
							'affwp_notice' => false,
							'action'       => 'edit_affiliate',
							'affiliate_id' => $affiliate->affiliate_id
						),
						array( 'class' => 'button' )
					);
				else: ?>
					<a href="<?php echo add_query_arg( array( 'user_id' => $user->ID, 'register_affiliate' => 1 ), admin_url( 'user-edit.php' ) ); ?>" class="button"><?php esc_attr_e( 'Register', 'affiliate-wp' ); ?></a>
				<?php endif; ?>
			</td>
		</tr>

		<?php 
		if ( ! affiliate_wp()->emails->is_email_disabled()
			&& ( $affiliate && ! in_array( $affiliate->status, array( 'active', 'inactive' ), true ) )
			&& affwp_email_notification_enabled( 'affiliate_application_accepted_email' )
		) : ?>
			<tr>
				<th scope="row"><label for="disable-affiliate-email"><?php _e( 'Disable Affiliate Email',  'affiliate-wp' ); ?></label></th>
				<td>
					<label for="disable-affiliate-email"><input type="checkbox" id="disable-affiliate-email" name="disable_affiliate_email" value="1" <?php checked( true, get_user_meta( $user->ID, 'affwp_disable_affiliate_email', true ) ); ?> /> <?php _e( 'Disable the application accepted email sent to the affiliate.', 'affiliate-wp' ); ?></label>
				</td>
			</tr>
		<?php endif; ?>
	</table>
	<?php
}
add_action( 'show_user_profile', 'affwp_user_profile_fields' );
add_action( 'edit_user_profile', 'affwp_user_profile_fields' );

/**
 * Save AffWP user settings.
 *
 * @since 1.9.5
 *
 * @param int $user_id Current user ID.
 */
function affwp_user_profile_update( $user_id ) {
	if ( current_user_can( 'edit_user', $user_id ) ) {
		if ( isset( $_REQUEST['disable_affiliate_email'] ) ) {
			update_user_meta( $user_id, 'affwp_disable_affiliate_email', 1 );
		} else {
			delete_user_meta( $user_id, 'affwp_disable_affiliate_email' );
		}
	}
}
add_action( 'personal_options_update',  'affwp_user_profile_update' );
add_action( 'edit_user_profile_update', 'affwp_user_profile_update' );

/**
 * Adds an Add Affiliate link to the user row actions.
 *
 * @since 2.2.2
 *
 * @param array   $actions An array of current action links.
 * @param WP_User $user    WP_User object for the currently-listed user.
 */
function affwp_user_row_add_affiliate_action( $actions, $user ) {
	if ( ! affwp_is_affiliate( $user->ID ) ) {
		$add_affiliate_link = esc_url( affwp_admin_url( 'affiliates' ,array( 'affwp_notice' => false, 'action' => 'add_affiliate', 'user_id' => $user->ID ) ) );
		$actions['add_affiliate'] = '<a href="' . $add_affiliate_link . '">' . __( 'Add Affiliate', 'affiliate-wp' ) . '</a>';
	}

	return $actions;
}
add_filter( 'user_row_actions', 'affwp_user_row_add_affiliate_action', 10, 2 );