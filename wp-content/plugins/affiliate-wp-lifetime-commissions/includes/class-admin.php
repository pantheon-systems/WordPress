<?php

class Affiliate_WP_Lifetime_Commissions_Admin {

	public function __construct() {
		// Save lifetime commissions option in the affiliate's user meta table
		add_action( 'affwp_update_affiliate', array( $this, 'update_affiliate' ), 0 );

		// Add checkbox to edit affiliate page
		add_action( 'affwp_edit_affiliate_bottom', array( $this, 'admin_field' ) );

		// load JS
		add_action( 'in_admin_footer', array( $this, 'scripts' ) );

		// add user profile field
		add_action( 'show_user_profile', array( $this, 'profile_field' ), 10 );
		add_action( 'edit_user_profile', array( $this, 'profile_field' ), 10 );

		// save user profile field
		add_action( 'personal_options_update', array( $this, 'save_profile_field' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_profile_field' ) );

		// add settings
		add_filter( 'affwp_settings_integrations', array( $this, 'settings_integrations' ) );

		// error checking
		add_filter( 'user_profile_update_errors', array( $this, 'check_fields' ), 10, 3 );

		// Delete meta data when user is deleted.
		add_action( 'wpmu_delete_user', array( $this, 'delete_user_meta' ) );
		add_action( 'deleted_user',     array( $this, 'delete_user_meta' ) );

	}

	/**
	 * Integration settings
	 *
	 * @since 1.0
	*/
	public function settings_integrations( $settings = array() ) {

		$settings['lifetime_commissions_header'] = array(
			'name' => __( 'Lifetime Commissions', 'affiliate-wp-lifetime-commissions' ),
			'type' => 'header'
		);

		$settings[ 'lifetime_commissions' ] = array(
			'name' => __( 'Enable For All Affiliates?', 'affiliate-wp-lifetime-commissions' ),
			'desc' => __( 'Check this box to enable lifetime commissions for all affiliates.', 'affiliate-wp-lifetime-commissions' ),
			'type' => 'checkbox'
		);

		$settings[ 'lifetime_commissions_enable_lifetime_referral_rates' ] = array(
			'name' => __( 'Enable Lifetime Referral Rates', 'affiliate-wp-lifetime-commissions' ),
			'desc' => __( 'Check this box to enable lifetime referral rates.', 'affiliate-wp-lifetime-commissions' ),
			'type' => 'checkbox'
		);

		$settings['lifetime_commissions_lifetime_referral_rate'] = array(
			'name' => __( 'Lifetime Referral Rate', 'affiliate-wp-lifetime-commissions' ),
			'desc' => __( 'Referral rate used for lifetime referrals, for all affiliates. The lifetime referral rate will take priority unless it is set to 0, or a per-affiliate lifetime referral rate is set.', 'affiliate-wp-lifetime-commissions' ),
			'type' => 'number',
			'size' => 'small',
			'step' => '0.01',
			'std' => affiliate_wp()->settings->get( 'referral_rate' )
		);

		$rate_type_options = array( __( 'Site Default', 'affiliate-wp-lifetime-commissions' ) );

		$settings['lifetime_commissions_lifetime_referral_rate_type'] = array(
			'name' => __( 'Lifetime Referral Rate Type', 'affiliate-wp-lifetime-commissions' ),
			'desc' => __( 'Should lifetime referrals be based on a percentage or flat rate amounts?', 'affiliate-wp-lifetime-commissions' ),
			'type' => 'select',
			'options' => array_merge( $rate_type_options, affwp_get_affiliate_rate_types() )
		);

		$settings[ 'lifetime_commissions_uninstall_on_delete' ] = array(
			'name' => __( 'Remove Data When Deleted?', 'affiliate-wp-lifetime-commissions' ),
			'desc' => __( 'Check this box if you would like Lifetime Commissions to completely remove all of its data when the plugin is deleted.', 'affiliate-wp-lifetime-commissions' ),
			'type' => 'checkbox'
		);

		return $settings;

	}

	/**
	 * Add Lifetime Commissions section to user's WP profile
	 * Allows an admin to link an affiliate to the user.
	 *
	 * @since  1.0
	*/
	public function profile_field( $user ) {

		// return if not admin
		if ( ! current_user_can( 'manage_affiliate_options' ) ) {
			return;
		}

	?>
		<h3><?php _e( 'Lifetime Commissions', 'affiliate-wp-lifetime-commissions' ); ?></h3>

		<table class="form-table">
			<tr>
				<th><label for="lifetime-affiliate-id"><?php _e( 'Linked Affiliate ID', 'affiliate-wp-lifetime-commissions' ); ?></label></th>
				<td>
					<input type="text" name="lifetime_affiliate_id" id="lifetime-affiliate-id" value="<?php echo esc_attr( get_the_author_meta( 'affwp_lc_affiliate_id', $user->ID ) ); ?>" class="regular-text" />
				</td>
			</tr>
		</table>
	<?php }


	/**
	 * Save the field when the values are changed on the user's WP profile page
	 *
	 * @since  1.0
	*/
	public function save_profile_field( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		/**
		 * Get the user's currently stored lifetime affiliate
		 */
		$current_lifetime_affiliate_id = get_user_meta( $user_id, 'affwp_lc_affiliate_id', true );

		/**
		 * Get the current affiliate ID
		 */
		$current_affiliate_id = get_user_meta( $user_id, 'affwp_lc_affiliate_id', true );

		/**
		 * Get the value of the lifetime affiliate field
		 */
		$posted_lifetime_affiliate_id = isset( $_POST['lifetime_affiliate_id'] ) ? $_POST['lifetime_affiliate_id'] : '';

		/**
		 * Get a list of user's additional emails and then delete these that are stored with the affiliate
		 */
		$lifetime_user_emails = get_user_meta( $user_id, 'affwp_lc_email' );

		/**
		 * Update the email associated with the affiliate if the user email is changed
		 */
		$current_user_email = get_the_author_meta( 'user_email', $user_id );
		$email              = isset( $_POST[ 'email' ] ) && $_POST[ 'email' ] ? $_POST[ 'email' ] : '';

		// user email address has changed
		if ( $email != $current_user_email ) {

			// when the user changes their email address, add it to the current affiliate in case the user purchases while logged out
			add_user_meta( affwp_get_affiliate_user_id( $current_affiliate_id ), 'affwp_lc_customer_email', $email );

			// if the user's email is changed, store it for later use.
			// we'll use these to loop through and remove the email addresses from the affiliate at a later point
			// once an affiliate is linked, the affiliate will receive all previous email addresses a customer used to have, and will be passed to the next affiliate if ever re-linked
			// email cannot already be in the array
			if ( ! in_array( $email, $lifetime_user_emails ) ) {
				add_user_meta( $user_id, 'affwp_lc_email', $email );
			}


		}

		/**
		 * Linked Affiliate ID field empty, delete meta keys for both customer and affiliate
		 */
		if ( empty( $posted_lifetime_affiliate_id ) ) {

			// loop through and delete all associated email addresses
			if ( $lifetime_user_emails ) {
				foreach ( $lifetime_user_emails as $email ) {
					delete_user_meta( affwp_get_affiliate_user_id( $current_affiliate_id ), 'affwp_lc_customer_email', $email );
				}
			}

			// finally, delete the affiliate ID from the customer
			delete_user_meta( $user_id, 'affwp_lc_affiliate_id', $current_affiliate_id );

			// delete the customer's user ID from the affiliate
			delete_user_meta( affwp_get_affiliate_user_id( $current_affiliate_id ), 'affwp_lc_customer_id', $user_id );

		}
		/**
		 * The ID was swapped for another, or the field went from blank to having a linked affiliate ID
		 */
		elseif (
			$posted_lifetime_affiliate_id != $current_lifetime_affiliate_id &&
			$this->is_valid_affiliate( $posted_lifetime_affiliate_id ) // affiliate ID must be valid
		) {


			// first, delete the customer's user ID from the affiliate
			delete_user_meta( affwp_get_affiliate_user_id( $current_affiliate_id ), 'affwp_lc_customer_id', $user_id );

			// transfer the user's email addresses from the old (current) affiliate to the newly posted affiliate
			if ( $lifetime_user_emails ) {
				foreach ( $lifetime_user_emails as $email ) {

					// loop through and delete all associated email addresses for the old affiliate
					delete_user_meta( affwp_get_affiliate_user_id( $current_affiliate_id ), 'affwp_lc_customer_email', $email );

					// loop through and add all associated email addresses to the new affiliate
					add_user_meta( affwp_get_affiliate_user_id( $posted_lifetime_affiliate_id ), 'affwp_lc_customer_email', $email );
				}
			}

			// as soon as a user is linked, it should store their WP user email
			if ( ! in_array( $current_user_email, $lifetime_user_emails) ) {
				add_user_meta( affwp_get_affiliate_user_id( $posted_lifetime_affiliate_id ), 'affwp_lc_customer_email', $current_user_email );
			}

			// add current user's email address to their own meta for recording. We'll use this later to delete the email address stored against the affiliate
			// only add if it doesn't already exists
			if ( ! get_user_meta( $user_id, 'affwp_lc_email', $current_user_email ) ) {
				add_user_meta( $user_id, 'affwp_lc_email', $current_user_email );
			}

			// update the affiliate's ID stored with the current user ID
			update_user_meta( $user_id, 'affwp_lc_affiliate_id', $posted_lifetime_affiliate_id );

			// update the user ID stored with the affiliate
			add_user_meta( affwp_get_affiliate_user_id( $posted_lifetime_affiliate_id ), 'affwp_lc_customer_id', $user_id );

		}

	}

	/**
	 * Show error if affiliate is invalid
	 *
	 * @since 1.0.2
	 */
	public function check_fields( $errors, $update, $user ) {
		$posted_lifetime_affiliate_id = isset( $_POST['lifetime_affiliate_id'] ) ? $_POST['lifetime_affiliate_id'] : '';

		if ( $posted_lifetime_affiliate_id && ! $this->is_valid_affiliate( $posted_lifetime_affiliate_id ) ) {
			$errors->add( 'invalid_affiliate_id', __( 'Invalid Linked Affiliate ID.', 'affiliate-wp-lifetime-commissions' ) );
		}
	}

	/**
	 * Deletes associated user meta when a user is deleted.
	 *
	 * @access public
	 * @since  1.2.3
	 *
	 * @param int $user_id User ID.
	 */
	public function delete_user_meta( $user_id ) {
		$meta_keys = array(
			'affwp_lc_enabled', 'affwp_lc_email', 'affwp_lc_affiliate_id',
			'affwp_lc_customer_email', 'affwp_lc_customer_id'
		);

		foreach ( $meta_keys as $meta_key ) {
			delete_user_meta( $user_id, $meta_key );
		}
	}

	/**
	 * Add checkbox to edit affiliate page
	 *
	 * @since  1.0
	 */
	public function admin_field( $affiliate ) {

		?>

		<table class="form-table">
			<tr><th scope="row"><label for="affwp_settings[lifetime_commissions_header]"><?php _e( 'Lifetime Commissions', 'affiliate-wp-lifetime-commissions' ); ?></label></th><td><hr></td></tr>
		</table>

		<?php
		// if all affiliate can earn lifetime commissions don't show this admin option
		$global_lifetime_commissions_enabled = affiliate_wp()->settings->get( 'lifetime_commissions' );

		if ( ! $global_lifetime_commissions_enabled ) :

		$checked = get_user_meta( $affiliate->user_id, 'affwp_lc_enabled', true );
		?>

		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="lifetime-commissions"><?php _e( 'Enable Lifetime Commissions', 'affiliate-wp-lifetime-commissions' ); ?></label>
				</th>

				<td>
					<input type="checkbox" name="lifetime_commissions" id="lifetime-commissions" value="1" <?php checked( $checked, 1 ); ?> />
					<p class="description"><?php _e( 'Allow this affiliate to receive lifetime commissions.', 'affiliate-wp-lifetime-commissions' ); ?></p>
				</td>

			</tr>

		</table>

		<?php
		endif;

		$global_rate   = affiliate_wp()->settings->get( 'lifetime_commissions_lifetime_referral_rate', 20 );
		$lifetime_rate = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'affwp_lc_lifetime_referral_rate', true );

		?>

		<?php
		// lifetime referral rates must be enabled
		if ( affiliate_wp()->settings->get( 'lifetime_commissions_enable_lifetime_referral_rates' ) ) : ?>
		<table class="form-table">

			<tr class="form-row">

				<th scope="row">
					<label for="lifetime-referral-rate"><?php _e( 'Lifetime Referral Rate', 'affiliate-wp-lifetime-commissions' ); ?></label>
				</th>

				<td>
					<input type="number" class="small-text" name="lifetime_referral_rate" id="lifetime-referral-rate" placeholder="<?php echo esc_attr( $global_rate ); ?>" value="<?php echo esc_attr( $lifetime_rate ); ?>" />
					<p class="description"><?php echo sprintf( __( 'The affiliate\'s lifetime referral rate. This rate takes priority over the <a href="%s">Lifetime Referral Rate</a> set in the Integrations tab.', 'affiliate-wp-lifetime-commissions' ), admin_url( 'admin.php?page=affiliate-wp-settings&tab=integrations' ) ); ?></p>

				</td>
			</tr>

			<?php
				$lifetime_referral_rate_type = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'affwp_lc_lifetime_referral_rate_type', true );
			?>
			<tr class="form-row">

				<th scope="row">
					<label for="lifetime-referral-rate-type"><?php _e( 'Lifetime Referral Rate Type', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<select name="lifetime_referral_rate_type" id="lifetime-referral-rate-type">
						<option value=""><?php _e( 'Lifetime Referral Rate Type Default', 'affiliate-wp' ); ?></option>
						<?php foreach( affwp_get_affiliate_rate_types() as $key => $type ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $lifetime_referral_rate_type, $key ); ?>><?php echo esc_html( $type ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php echo sprintf( __( 'The affiliate\'s lifetime referral rate type. This rate type takes priority over the <a href="%s">Lifetime Referral Rate Type</a> set in the Integrations tab.', 'affiliate-wp-lifetime-commissions' ), admin_url( 'admin.php?page=affiliate-wp-settings&tab=integrations' ) ); ?></p>
				</td>

			</tr>

		</table>
	<?php endif; ?>

		<?php

			$linked_users = affiliate_wp_lifetime_commissions()->integrations->get_affiliates_customer_ids( $affiliate->affiliate_id );

			if ( $linked_users ) :
		?>
		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="lifetime-commissions"><?php _e( 'Linked User Accounts', 'affiliate-wp-lifetime-commissions' ); ?></label>
				</th>

				<td>
				<p class="description"><?php _e( 'User accounts linked to this affiliate.', 'affiliate-wp-lifetime-commissions' ); ?></p>
				<?php
					$prefix = '';
					$list = '';

					foreach ( $linked_users as $id ) {

						$user_data = get_userdata( $id );

						if ( $user_data ) {
							$list .= $prefix . '<a href="' . get_edit_user_link( $id ) . '">' . $user_data->user_login . '</a>';
						    $prefix = ', ';
						}


					}

					echo '<p>' . $list . '</p>';
				?>

				</td>

			</tr>

		</table>
		<?php endif; ?>

		<?php
			$linked_emails = affiliate_wp_lifetime_commissions()->integrations->get_affiliates_customer_emails( $affiliate->affiliate_id );

			if ( $linked_emails ) :
		?>
		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="lifetime-commissions"><?php _e( 'Linked User Emails', 'affiliate-wp-lifetime-commissions' ); ?></label>
				</th>

				<td>
				<p class="description"><?php _e( 'User emails linked to this affiliate (including guest emails).', 'affiliate-wp-lifetime-commissions' ); ?></p>
				<?php
					foreach ( $linked_emails as $email ) {
						 echo '<p>' . $email . '</p>';
					}
				?>
				</td>

			</tr>

		</table>
		<?php endif; ?>

	<?php
	}

	/**
	 * Check if it is a valid affiliate
	 * Based on the is_valid_affiliate() function but without the $is_self check
	 * @since 1.0
	 */
	public function is_valid_affiliate( $affiliate_id = 0 ) {

		if( empty( $affiliate_id ) ) {
			$affiliate_id = $this->get_affiliate_id();
		}

		$active = 'active' == affwp_get_affiliate_status( $affiliate_id );

		$valid  = affiliate_wp()->affiliates->affiliate_exists( $affiliate_id );

		return ! empty( $valid ) && $active;
	}

	/**
	 * Get a user's stored email addresses from the affwp_lc_email metakey
	 *
	 * @since  1.0
	*/
	public function get_users_lifetime_emails( $user_id = 0 ) {
		$lifetime_user_emails = get_user_meta( $user_id, 'affwp_lc_email' );

		if ( $lifetime_user_emails ) {
			return $lifetime_user_emails;
		}

		return false;
	}

	/**
	 * Save lifetime commissions option in the affiliate's user meta table
	 *
	 * @since  1.0
	 */
	public function update_affiliate( $data ) {

		if ( empty( $data['affiliate_id'] ) ) {
			return false;
		}

		if ( ! current_user_can( 'manage_affiliates' ) ) {
			return;
		}

		$lifetime_commissions = isset( $data['lifetime_commissions'] ) ? $data['lifetime_commissions'] : '';

		if ( $lifetime_commissions ) {
			update_user_meta( affwp_get_affiliate_user_id( $data['affiliate_id'] ), 'affwp_lc_enabled', $lifetime_commissions );
		} else {
			delete_user_meta( affwp_get_affiliate_user_id( $data['affiliate_id'] ), 'affwp_lc_enabled' );
		}

		// lifetime referral rate
		$lifetime_rate = isset( $data['lifetime_referral_rate'] ) ? $data['lifetime_referral_rate'] : '';

		if ( $lifetime_rate ) {
			affwp_update_affiliate_meta( $data['affiliate_id'], 'affwp_lc_lifetime_referral_rate', absint( $lifetime_rate ) );
		} else {
			affwp_delete_affiliate_meta( $data['affiliate_id'], 'affwp_lc_lifetime_referral_rate' );
		}

		// lifetime referral rate type
		$lifetime_rate_type = isset( $data['lifetime_referral_rate_type'] ) ? $data['lifetime_referral_rate_type'] : '';

		if ( $lifetime_rate_type ) {
			affwp_update_affiliate_meta( $data['affiliate_id'], 'affwp_lc_lifetime_referral_rate_type', $lifetime_rate_type );
		} else {
			affwp_delete_affiliate_meta( $data['affiliate_id'], 'affwp_lc_lifetime_referral_rate_type' );
		}

	}

	/**
	 * Show or hide the table row based on the checkbox option
	 *
	 * @since 1.2
	 */
	public function scripts() {

		$screen = get_current_screen();

		if ( ! ( $screen->id === 'affiliates_page_affiliate-wp-settings' && is_admin() && isset( $_GET['tab'] ) && $_GET['tab'] === 'integrations' ) ) {
			return;
		}

		?>
		<script>
			jQuery(document).ready(function($) {

				// enable lifetime referral rates checkbox
				var optionLifetimeReferralRates = $('input[name="affwp_settings[lifetime_commissions_enable_lifetime_referral_rates]"]');

				// the table row that will be enabled/disabled
				var lifetimeReferralRatesRow = optionLifetimeReferralRates.closest('tr').next();

				// when the page first loads, determine if the row should be hidden or shown
				if ( optionLifetimeReferralRates.is(':checked') ) {
					lifetimeReferralRatesRow.show();
				} else {
					lifetimeReferralRatesRow.hide();
				}

				// show or hide the table row based on the checkbox option
				optionLifetimeReferralRates.click( function() {

					if ( this.checked ) {
						lifetimeReferralRatesRow.show();
					} else {
						lifetimeReferralRatesRow.hide();
					}

				});
			});
		</script>

		<?php
	}

}
new Affiliate_WP_Lifetime_Commissions_Admin;
