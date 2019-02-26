<?php
$affiliate_id          = affwp_get_affiliate_id();
$frontend              = affiliatewp_direct_link_tracking()->frontend;
$direct_link_count     = affwp_dlt_count_direct_links( array( 'affiliate_id' => $affiliate_id ) );
$direct_links          = affwp_dlt_get_direct_links( array( 'affiliate_id' => $affiliate_id ) );
$rejected_direct_links = affwp_dlt_get_direct_links( array( 'affiliate_id' => $affiliate_id, 'status' => 'rejected' ) );
$invalid_domains       = isset( $_GET['invalid'] ) && 'true' === $_GET['invalid'] ? true : false;
$non_ssl_note          = ! is_ssl() ? sprintf( __( 'Note, your domain%s must be HTTP (not HTTPS) based.', 'affiliatewp-direct-link-tracking' ), 1 !== affwp_dlt_get_domain_limit( $affiliate_id ) ? __( 's', 'affiliatewp-direct-link-tracking' ) : '' ) : '';
?>
<div id="affwp-affiliate-dashboard-direct-links" class="affwp-tab-content">
	<h4><?php _e( 'Direct Links', 'affiliatewp-direct-link-tracking' ); ?></h4>

	<?php
	/**
	 * Fires at the top of the Direct Links dashboard tab.
	 *
	 * @param int $affiliate_id Affiliate ID of the currently logged-in affiliate.
	 */
	do_action( 'affwp_direct_link_tracking_direct_links_top', $affiliate_id );
	?>

	<form id="affwp-affiliate-dashboard-profile-form" class="affwp-form" method="post">

		<p class="affwp-direct-link-tracking-note">
			<?php printf( __( 'Direct links allow you to link directly to this site, from your own website, without an affiliate link. Submit your domain or individual domain path%s below for approval.', 'affiliatewp-direct-link-tracking' ), 1 !== affwp_dlt_get_domain_limit( $affiliate_id ) ? __( 's', 'affiliatewp-direct-link-tracking' ) : '' ); ?>
			<?php echo $non_ssl_note; ?>
		</p>

		<?php

		if ( $invalid_domains || $rejected_direct_links ) :

			$notices = array();

			if ( $invalid_domains ) {
				$notices[] = __( 'An invalid domain was submitted.', 'affiliatewp-direct-link-tracking' );
			}

			if ( $rejected_direct_links ) {
				$notices[] = __( 'You have domains that were not approved:', 'affiliatewp-direct-link-tracking' );
				foreach ( $rejected_direct_links as $direct_link ) {
					$notices[] = sprintf( __( '%s', 'affiliatewp-direct-link-tracking' ), $direct_link->url );
				}
			}

			?>
			<p class="affwp-notice error"><?php echo implode( '<br />', $notices ); ?></p>
		<?php endif;

		if ( $direct_links ) {
			$direct_links = array_combine( range( 1, count( $direct_links ) ), array_values( (array) $direct_links ) );
		}

		if ( ! $direct_link_count ) :
			$count = (int) affwp_dlt_get_domain_limit( $affiliate_id ) > 1 ? __( '#1', 'affiliatewp-direct-link-tracking' ) : '';

			// affiliate does not have any domains
		?>

		<div class="affwp-wrap">
			<label for="direct-link-tracking-url[1]"><?php printf( __( 'Direct Link Domain %s', 'affiliatewp-direct-link-tracking' ), $count ); ?> <?php echo $frontend->remove_url(); ?></label>
			<input class="wide" id="direct-link-tracking-url[1]" type="text" name="direct_link_tracking_urls_new[]" value="" />
		</div>

		<?php else : ?>

			<?php
			/**
			 * Affiliate has at least 1 domain assigned to them
			 */
			foreach ( $direct_links as $key => $direct_link ) :

				$url_id      = isset( $direct_link->url_id ) ? $direct_link->url_id : '';
				$url         = isset( $direct_link->url ) ? $direct_link->url : '';
				$old_url     = isset( $direct_link->url_old ) ? $direct_link->url_old : '';
				$is_pending  = isset( $direct_link->status ) && 'pending' === $direct_link->status ? true : false;
				$is_inactive = isset( $direct_link->status ) && 'inactive' === $direct_link->status ? true : false;
				$is_rejected = isset( $direct_link->status ) && 'rejected' === $direct_link->status ? true : false;

				$count = (int) affwp_dlt_get_domain_limit( $affiliate_id ) > 1 ? sprintf( __( ' #%s', 'affiliatewp-direct-link-tracking' ), $key ) : '';
			?>

			<div class="affwp-wrap">
				<label for="direct-link-tracking-url[<?php echo $key;?>]"><?php printf( __( 'Direct Link Domain%s', 'affiliatewp-direct-link-tracking' ), $count ); ?> <?php echo $frontend->remove_url( $url_id ); ?></label>
				<?php
				// sets the value of the input field
				$value = $is_rejected && $old_url ? $old_url : $url;

				// disable input field if status is "pending"
				$disabled = $is_pending || $is_inactive ? ' disabled="disabled"' : '';
				?>

				<input class="wide" id="direct-link-tracking-url[<?php echo $key; ?>]" type="text" name="<?php echo $frontend->field_name; ?>[<?php echo $url_id; ?>]" value="<?php echo $value; ?>"<?php echo $disabled; ?> />

				<?php do_action( 'affwp_direct_link_tracking_show_notices', $direct_link ); ?>
			</div>

			<?php endforeach; ?>

		<?php endif; ?>

		<?php echo $frontend->to_clone(); ?>

		<p><a href="#" class="affwp-dlt-add-url" style="display: none;"><?php _e( 'Add new domain', 'affiliatewp-direct-link-tracking' ); ?></a></p>

		<input type="hidden" name="affwp_action" value="update_direct_links" />
		<input type="hidden" name="affiliate_id" value="<?php echo absint( $affiliate_id ); ?>" />
		<input type="submit" class="button" value="<?php esc_attr_e( 'Save Direct Links', 'affiliatewp-direct-link-tracking' ); ?>" />

	</form>

	<?php
	/**
	 * Fires at the bottom of the Direct Links dashboard tab.
	 *
	 * @param int $affiliate_id Affiliate ID of the currently logged-in affiliate.
	 */
	do_action( 'affwp_direct_link_tracking_direct_links_bottom', $affiliate_id );
	?>

</div>
