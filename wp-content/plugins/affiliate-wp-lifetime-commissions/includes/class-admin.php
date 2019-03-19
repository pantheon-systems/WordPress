<?php

class Affiliate_WP_Lifetime_Commissions_Admin {

	public function __construct() {

		// Save lifetime commissions option in the affiliate's user meta table.
		add_action( 'affwp_update_affiliate', array( $this, 'update_affiliate' ), 0 );

		// Add checkbox to edit affiliate page.
		add_action( 'affwp_edit_affiliate_bottom', array( $this, 'affiliate_admin_edit' ) );

		// Add settings.
		add_filter( 'affwp_settings_integrations', array( $this, 'settings_integrations' ) );

		// Delink a linked customer from an affiliate.
		add_action( 'affwp_lc_delink_customer', array( $this, 'delink_customer' ) );

		// Load JS.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 100 );

		// Ajax add customer email.
		add_action( 'wp_ajax_affwp_lc_add_email', array( $this, 'add_customer_email' ), 100 );

		// Add link icon to the referral id column to indicate a lifetime referral.
		add_filter( 'affwp_referral_table_referral_id', array( $this, 'affwp_custom_referral_id_column' ), 10, 2 );
	}

	/**
	 * Add link icon to the referral id column to indicate a lifetime referral.
	 *
	 * @since 1.3
	 */
	function affwp_custom_referral_id_column( $value, $referral ) {

		$custom = maybe_unserialize( $referral->custom );

		if ( $custom && is_array( $custom ) && in_array( 'lifetime_referral', $custom ) || $custom == 'lifetime_referral' ) {
			$value = $value . ' <span class="dashicons dashicons-admin-links" aria-label="' . __( 'Lifetime Referral', 'affiliate-wp-lifetime-commissions' ) . '"></span>';
		}

		return $value;
	}

	/**
	 * Integration settings.
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

		$settings[ 'lifetime_commissions_customers_access' ] = array(
			'name' => __( 'Lifetime Customers Access', 'affiliate-wp-lifetime-commissions' ),
			'desc' => __( 'Check this box to allow all affiliates to see their lifetime customers.', 'affiliate-wp-lifetime-commissions' ),
			'type' => 'checkbox',
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

		$settings['lifetime_commissions_lifetime_length'] = array(
			'name' => __( 'Lifetime Length', 'affiliate-wp-lifetime-commissions' ),
			'desc' => __( 'The number of days lifetime commissions will be generated from a referral for all affiliates. The lifetime length will always be used unless a per-affiliate lifetime length is set. Set to 0 for infinite.', 'affiliate-wp-lifetime-commissions' ),
			'type' => 'number',
			'size' => 'small',
			'step' => '1',
			'std'  => 0
		);

		$settings[ 'lifetime_commissions_link_customers_on_registration' ] = array(
			'name' => __( 'Link Customers To Affiliates On User Registration', 'affiliate-wp-lifetime-commissions' ),
			'desc' => __( 'Check this box if you would like to automatically link customers to affiliates on user registration.', 'affiliate-wp-lifetime-commissions' ),
			'type' => 'checkbox',
		);

		$settings[ 'lifetime_commissions_uninstall_on_delete' ] = array(
			'name' => __( 'Remove Data When Deleted?', 'affiliate-wp-lifetime-commissions' ),
			'desc' => __( 'Check this box if you would like Lifetime Commissions to completely remove all of its data when the plugin is deleted.', 'affiliate-wp-lifetime-commissions' ),
			'type' => 'checkbox'
		);

		return $settings;

	}

	/**
	 * Add checkbox to edit affiliate page.
	 *
	 * @since  1.0
	 */
	public function affiliate_admin_edit( $affiliate ) {

		?>

		<table class="form-table">
			<tr><th scope="row"><label for="affwp_settings[lifetime_commissions_header]"><?php _e( 'Lifetime Commissions', 'affiliate-wp-lifetime-commissions' ); ?></label></th><td><hr></td></tr>
		</table>

		<?php
		// if all affiliate can earn lifetime commissions don't show this admin option
		$global_lifetime_commissions_enabled = affiliate_wp()->settings->get( 'lifetime_commissions' );

		if ( ! $global_lifetime_commissions_enabled ) :

			$checked = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'affwp_lc_enabled', true );
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

		// If all affiliate can access their lifetime customers.
		$global_lifetime_customers_access = affiliate_wp()->settings->get( 'lifetime_commissions_customers_access', false );

		if ( ! $global_lifetime_customers_access ) :

			$checked = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'affwp_lc_customers_access', true );
			?>
			<table class="form-table">

				<tr class="form-row form-required">

					<th scope="row">
						<label for="lifetime-customers-access"><?php _e( 'Lifetime Customers Access', 'affiliate-wp-lifetime-commissions' ); ?></label>
					</th>

					<td>
						<input type="checkbox" name="lifetime_customers_access" id="lifetime-customers-access" value="1" <?php checked( $checked, 1 ); ?> />
						<p class="description"><?php _e( 'Allow affiliate to see their lifetime customers.', 'affiliate-wp-lifetime-commissions' ); ?></p>
					</td>

				</tr>

			</table>

		<?php
		endif;

		$global_rate   = affiliate_wp()->settings->get( 'lifetime_commissions_lifetime_referral_rate', 20 );
		$lifetime_rate = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'affwp_lc_lifetime_referral_rate', true );

		// Lifetime referral rates must be enabled.
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

		<?php
        endif;

        $global_length   = affiliate_wp()->settings->get( 'lifetime_commissions_lifetime_length', 0 );
        $lifetime_length = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'affwp_lc_lifetime_length', true );

        ?>

        <table class="form-table">

            <tr class="form-row">

                <th scope="row">
                    <label for="lifetime-referral-length"><?php _e( 'Lifetime Length', 'affiliate-wp-lifetime-commissions' ); ?></label>
                </th>

                <td>
                    <input type="number" class="small-text" name="lifetime_referral_length" id="lifetime-referral-length" placeholder="<?php echo esc_attr( $global_length ); ?>" value="<?php echo esc_attr( $lifetime_length ); ?>" />
                    <p class="description"><?php echo sprintf( __( 'The number of days this affiliate can earn lifetime commissions from a referral. This length takes priority over the <a href="%s">Lifetime Length</a> set in the Integrations tab.', 'affiliate-wp-lifetime-commissions' ), admin_url( 'admin.php?page=affiliate-wp-settings&tab=integrations' ) ); ?></p>
                </td>
            </tr>

        </table>

		<?php
		$linked_customers = affiliate_wp_lifetime_commissions()->integrations->get_customers_for_affiliate( $affiliate->affiliate_id );
		if ( $linked_customers ) : ?>

			<table class="form-table">

				<tr class="form-row form-required">

					<th scope="row">
						<label for="lifetime-commissions"><?php _e( 'Linked Customers', 'affiliate-wp-lifetime-commissions' ); ?></label>
					</th>

					<td>
						<p class="description"><?php _e( 'Customers linked to this affiliate.', 'affiliate-wp-lifetime-commissions' ); ?></p>
						<ul class="affwp-lc-linked-customers">
						<?php

							foreach ( $linked_customers as $customer ) {

								if( ! $customer ) {
									continue;
								}

								echo '<li>';

								if ( $customer->user_id ) {
									echo '<a href="' . get_edit_user_link( $customer->user_id ) . '">';
								}

								echo $customer->get_name() . ' (' . $customer->email . ')';

								if( $customer->user_id ) {
									echo '</a>';
								}

								$delink_url = wp_nonce_url( add_query_arg( array( 'affwp_action' => 'lc_delink_customer', 'affiliate_id' => $affiliate->affiliate_id, 'customer' => $customer->customer_id ) ), 'affwp-lc-delink-customer-nonce' );
								echo ' | <a href="' . $delink_url . '">' . __( 'Unlink customer', 'affiliate-wp-lifetime-commissions' ) . '</a>';

								echo'</li>';

							}
						?>
					</ul>
					</td>

				</tr>

			</table>

		<?php endif; ?>

		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="lifetime-commissions"><?php _e( 'Add New Customer Email', 'affiliate-wp-lifetime-commissions' ); ?></label>
				</th>

				<td>
					<input class="regular-text" type="email" name="affwp_lc_email" id="affwp_lc_email" value="" placeholder="<?php esc_attr_e( 'Enter an email address', 'affiliate-wp-lifetime-commissions' ); ?>"/>
					<input type="hidden" name="affwp_lc_affiliate_id" id="affwp_lc_affiliate_id" value="<?php echo esc_attr( $affiliate->affiliate_id ); ?>">
					<input type="submit" name="affwp_lc_add_email" id="affwp_lc_add_email" class="button" value="<?php _e( 'Add Email', 'affiliate-wp-lifetime-commissions' ); ?>">
					<p class="description"><?php _e( 'Enter an email to link a new customer to this affiliate.', 'affiliate-wp-lifetime-commissions' ); ?></p>
				</td>

			</tr>

		</table>

		<?php
	}

	/**
	 * Save lifetime commissions option in the affiliate's user meta table.
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
		    affwp_update_affiliate_meta( $data['affiliate_id'], 'affwp_lc_enabled', $lifetime_commissions );
		} else {
		    affwp_delete_affiliate_meta( $data['affiliate_id'], 'affwp_lc_enabled' );
		}

		// Lifetime customers access.
		$lifetime_customers_access = isset( $data['lifetime_customers_access'] ) ? $data['lifetime_customers_access'] : '';

		if ( $lifetime_customers_access ) {
			affwp_update_affiliate_meta( $data['affiliate_id'], 'affwp_lc_customers_access', $lifetime_customers_access );
		} else {
			affwp_delete_affiliate_meta( $data['affiliate_id'], 'affwp_lc_customers_access' );
		}

		// Lifetime referral rate.
		$lifetime_rate = isset( $data['lifetime_referral_rate'] ) ? $data['lifetime_referral_rate'] : '';

		if ( $lifetime_rate ) {
			affwp_update_affiliate_meta( $data['affiliate_id'], 'affwp_lc_lifetime_referral_rate', absint( $lifetime_rate ) );
		} else {
			affwp_delete_affiliate_meta( $data['affiliate_id'], 'affwp_lc_lifetime_referral_rate' );
		}

		// Lifetime length.
		$lifetime_length = isset( $data['lifetime_referral_length'] ) && '' !== $data['lifetime_referral_length'] ? $data['lifetime_referral_length'] : null;

		if ( $lifetime_length !== null ) {
			affwp_update_affiliate_meta( $data['affiliate_id'], 'affwp_lc_lifetime_length', absint( $data['lifetime_referral_length'] ) );
		} else {
			affwp_delete_affiliate_meta( $data['affiliate_id'], 'affwp_lc_lifetime_length' );
		}

		// Lifetime referral rate type.
		$lifetime_rate_type = isset( $data['lifetime_referral_rate_type'] ) ? $data['lifetime_referral_rate_type'] : '';

		if ( $lifetime_rate_type ) {
			affwp_update_affiliate_meta( $data['affiliate_id'], 'affwp_lc_lifetime_referral_rate_type', $lifetime_rate_type );
		} else {
			affwp_delete_affiliate_meta( $data['affiliate_id'], 'affwp_lc_lifetime_referral_rate_type' );
		}

		if ( ! empty( $data['affwp_lc_email'] ) && is_email( $data['affwp_lc_email'] ) ) {

			$customer = affiliate_wp()->customers->get_by( 'email', $data['affwp_lc_email'] );

			if ( $customer ) {

				if ( ! $customer->user_id ) {

					$user = get_user_by( 'email', $customer->email );

					if ( $user ) {

						$args = array(
							'user_id'     => $user->ID,
							'customer_id' => $customer->customer_id,
						);

						affwp_update_customer( $args );
					}

				}

				$linked_affiliates = affwp_get_customer_meta( $customer->customer_id, 'affiliate_id' );

				if ( ! $linked_affiliates ) {

					affwp_add_customer_meta( $customer->customer_id, 'affiliate_id', $data['affiliate_id'], true );

				}

			} else {

				$args = array(
					'email'        => $data['affwp_lc_email'],
					'affiliate_id' => $data['affiliate_id'],
				);

				$user = get_user_by( 'email', $data['affwp_lc_email'] );

				if ( $user ) {
					$args['user_id']    = $user->ID;
					$args['first_name'] = $user->first_name;
					$args['last_name']  = $user->last_name;
				}

				affwp_add_customer( $args );
			}

		}

	}

	/**
	 * Delink a linked customer from an affiliate.
	 *
	 * @since 1.3
	 */
	public function delink_customer( $data ) {

		if ( empty( $data[ '_wpnonce' ] ) ) {
			return;
		}

		if( ! wp_verify_nonce( $data['_wpnonce'], 'affwp-lc-delink-customer-nonce' ) ) {
			wp_die( __( 'Nonce verification failed', 'affiliate-wp' ), __( 'Error', 'affiliate-wp-lifetime-commissions' ), array( 'response' => 403 ) );
		}

		$customer_id  = absint( $data[ 'customer' ] );
		$affiliate_id = absint( $data[ 'affiliate_id' ] );

		affwp_delete_customer_meta( $customer_id, 'affiliate_id', $affiliate_id );

		$message = urlencode( __( 'Customer unlinked successfully', 'affiliate-wp-lifetime-commissions' ) );

		wp_redirect( affwp_admin_url( 'affiliates', array( 'action' => 'edit_affiliate', 'affiliate_id' => $affiliate_id, 'affwp_notice' => 'affiliate_delinked', 'affwp_message' => $message ) ) ); exit;

	}

	/**
	 * Admin scripts.
	 *
	 * @since 1.3
	 */
	public function admin_scripts() {

		if ( ! affwp_is_admin_page() ) {
			return;
		}

		// Use minified libraries if SCRIPT_DEBUG is set to false.
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Register scripts.
		wp_register_script( 'affwplc-admin-scripts', AFFWP_LC_PLUGIN_URL . 'assets/js/admin-scripts' . $suffix . '.js', array(), AFFWP_LC_VERSION, false );

		// Enqueue scripts.
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'edit_affiliate' ) {
			wp_enqueue_script( 'affwplc-admin-scripts' );
		}

	}

	/**
	 * Handles Ajax for adding a new customer email.
	 *
	 * @since 1.3
	 */
	public function add_customer_email() {

		if ( empty( $_REQUEST['customer_email'] ) || empty( $_REQUEST['affiliate_id'] ) ) {
			wp_die( -1 );
		}

		if ( ! current_user_can( 'manage_affiliates' ) ) {
			wp_die( -1 );
		}

		$customer_email = sanitize_text_field( $_REQUEST['customer_email'] );
		$affiliate_id   = sanitize_text_field( $_REQUEST['affiliate_id'] );

		if ( ! is_email( $customer_email ) ) {
			wp_die( -1 );
		}

		$customer = affiliate_wp()->customers->get_by( 'email', $customer_email );

		if ( $customer ) {

			if ( ! $customer->user_id ) {

				$user = get_user_by( 'email', $customer->email );

				if ( $user ) {

					$args = array(
						'user_id'     => $user->ID,
						'customer_id' => $customer->customer_id,
					);

					affwp_update_customer( $args );
				}

			}

			$linked_affiliate = affwp_get_customer_meta( $customer->customer_id, 'affiliate_id' );

			if ( ! $linked_affiliate ) {

				$customer_meta = affwp_add_customer_meta( $customer->customer_id, 'affiliate_id', $affiliate_id, true );

				if ( $customer_meta ) {

					$customer_html = $this->get_customer_html( $customer->customer_id, $affiliate_id );

					$response = array(
						'message'       => __( 'Customer has been linked to this affiliate.', 'affiliate-wp-lifetime-commissions' ),
						'customer_html' => $customer_html,
					);

					wp_send_json_success( $response );

				} else {

					$response = array(
						'message' => __( 'Unable to link customer to this affiliate.', 'affiliate-wp-lifetime-commissions' ),
					);

					wp_send_json_error( $response );

				}

			} else {

				$response = array(
					'message' => __( 'Customer is already linked to an affiliate.', 'affiliate-wp-lifetime-commissions' ),
				);

				wp_send_json_error( $response );

			}

		} else {

			$args = array(
				'email'        => $customer_email,
				'affiliate_id' => $affiliate_id,
			);

			$user = get_user_by( 'email', $customer_email );

			if ( $user ) {
				$args['user_id']    = $user->ID;
				$args['first_name'] = $user->first_name;
				$args['last_name']  = $user->last_name;
			}

			$customer_id = affwp_add_customer( $args );

			$customer_html = $this->get_customer_html( $customer_id, $affiliate_id );

			if ( $customer_id ) {

				$response = array(
					'message'       => __( 'Customer has been linked to this affiliate.', 'affiliate-wp-lifetime-commissions' ),
					'customer_html' => $customer_html,
				);

				wp_send_json_success( $response );

			} else {

				$response = array(
					'message' => __( 'Unable to link customer to this affiliate.', 'affiliate-wp-lifetime-commissions' ),
				);

				wp_send_json_error( $response );

			}

		}

	}

	/**
	 * Get customer HTML for Ajax response.
	 *
	 * @since 1.3
	 */
	function get_customer_html( $customer_id, $affiliate_id ) {

		ob_start();

		$customer = affwp_get_customer( $customer_id );

		if ( ! $customer ) {
			return false;
		}

		echo '<li>';

		if ( $customer->user_id ) {
			echo '<a href="' . get_edit_user_link( $customer->user_id ) . '">';
		}

		echo $customer->get_name() . ' (' . $customer->email . ')';

		if ( $customer->user_id ) {
			echo '</a>';
		}

		$delink_url = wp_nonce_url( add_query_arg( array(
			'affwp_action' => 'lc_delink_customer',
			'affiliate_id' => $affiliate_id,
			'customer'     => $customer->customer_id,
		) ), 'affwp-lc-delink-customer-nonce' );
		echo ' | <a href="' . $delink_url . '">' . __( 'Unlink customer', 'affiliate-wp-lifetime-commissions' ) . '</a>';

		echo '</li>';

		return ob_get_clean();
	}

}
new Affiliate_WP_Lifetime_Commissions_Admin;
