<?php

class AffiliateWP_Rates_Admin {

	public function __construct() {
		add_filter( 'affwp_settings_tabs',           array( $this, 'setting_tab'       ) );
		add_action( 'admin_init',                    array( $this, 'register_settings' ) );
		add_filter( 'affwp_settings_rates_sanitize', array( $this, 'sanitize_rates'    ) );
	}

	public function setting_tab( $tabs ) {
		$tabs['rates'] = __( 'Tiered Rates', 'affiliate-wp-tiered' );
		return $tabs;
	}

	public function register_settings() {

		add_settings_section(
			'affwp_settings_rates',
			__return_null(),
			'__return_false',
			'affwp_settings_rates'
		);

		add_settings_field(
			'affwp_settings[rates]',
			__( 'Tiered Affiliate Rates', 'affiliate-wp-tiered' ),
			array( $this, 'rates_table' ),
			'affwp_settings_rates',
			'affwp_settings_rates'
		);

		add_settings_field(
			'affwp_settings[rate-expiration]',
			__( 'Tiered Rate Expiration', 'affiliate-wp-tiered' ),
			array( $this, 'rate_expiration_setting' ),
			'affwp_settings_rates',
			'affwp_settings_rates'
		);

	}

	public function sanitize_rates( $input ) {

		// TODO need to sort these from low to high

		if( ! empty( $input['rates'] ) ) {

			if( ! is_array( $input['rates'] ) ) {
				$input['rates'] = array();
			}

			// Sort rates low to high.
			usort( $input['rates'], function( $rate1, $rate2 ) {
				if ( $rate1['rate'] == $rate2['rate'] ) {
					return 0;
				}
				return ( $rate1['rate'] < $rate2['rate'] ) ? -1 : 1;
			} );

			foreach( $input['rates'] as $key => $rate ) {

				// Allow for 0 values.
				$rate_value = affwp_abs_number_round( $rate['rate'] );

				if ( empty( $rate['threshold'] ) || null === $rate_value ) {

					unset( $input['rates'][ $key ] );

				} else {

					$input['rates'][ $key ]['threshold'] = absint( $rate['threshold'] );
					$input['rates'][ $key ]['rate']      = $rate_value;

					// If unchecked, the rate is "enabled".
					if ( ! isset( $rate['disabled'] ) ) {
						$input['rates'][ $key ]['disabled'] = null;
					}

				}

			}

		}

		// Tiered Rate Expiration.
		if ( ! isset( $input['rate-expiration'] ) ) {
			// Core handles saving checkboxes as 'on' when enabled. Here, handle the "unchecked" case.
			$input['rate-expiration'] = null;
		}

		return $input;
	}

	public function rates_table() {

		$rates = affiliate_wp_tiers()->get_rates();
		$count = count( $rates );
?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {

			$('#affwp_new_rate').on('click', function(e) {

				e.preventDefault();

				var row = $('#affiliatewp-rates tbody tr:last');

				clone = row.clone(true);

				var count = $('#affiliatewp-rates tbody tr').length;

				clone.find( 'td input, td select' ).val( '' );
				clone.find( 'input, select' ).each(function() {
					var name = $( this ).attr( 'name' );

					name = name.replace( /\[(\d+)\]/, '[' + parseInt( count ) + ']');

					$( this ).attr( 'name', name ).attr( 'id', name );
				});

				clone.insertAfter( row );

			});

			// remove row
			$('.affwp_remove_rate').on('click', function(e) {
				e.preventDefault();
				$(this).parent().parent().remove();
			});

		});
		</script>
		<style type="text/css">
		#affiliatewp-rates th { padding-left: 10px; }
		.affwp_remove_rate { margin: 8px 0 0 0; cursor: pointer; width: 10px; height: 10px; display: inline-block; text-indent: -9999px; overflow: hidden; }
		.affwp_remove_rate:active, .affwp_remove_rate:hover { background-position: -10px 0!important }
		</style>
		<form id="affiliatewp-rates-form">
			<table id="affiliatewp-rates" class="form-table wp-list-table widefat fixed posts">
				<thead>
					<tr>
						<th><?php _e( 'Type', 'affiliate-wp-tiered' ); ?></th>
						<th><?php _e( 'Threshold', 'affiliate-wp-tiered' ); ?></th>
						<th><?php _e( 'Rate', 'affiliate-wp-tiered' ); ?></th>
						<th><?php _e( 'Disabled', 'affiliate-wp-tiered' ); ?></th>
						<th style="width:5%;"></th>
					</tr>
				</thead>
				<tbody>
					<?php if( $rates ) : ?>
						<?php foreach( $rates as $key => $rate ) :
							$type = ! empty( $rate['type'] ) ? $rate['type'] : 'referrals';
							$disabled = isset( $rate['disabled'] );

							if ( $disabled ) :
								$aria_label = __( 'This rate tier is disabled', 'affiliate-wp-tiered' );
							else :
								$aria_label = __( 'This rate tier is enabled', 'affiliate-wp-tiered' );
							endif;
							?>
							<tr>
								<td>
									<select name="affwp_settings[rates][<?php echo $key; ?>][type]">
										<option value="referrals"<?php selected( 'referrals', $type ); ?>><?php _e( 'Number of Referrals', 'affiliate-wp-tiered' ); ?></option>
										<option value="earnings"<?php selected( 'earnings', $type ); ?>><?php _e( 'Total Earnings', 'affiliate-wp-tiered' ); ?></option>
									</select>
								</td>
								<td>
									<input name="affwp_settings[rates][<?php echo $key; ?>][threshold]" type="text" value="<?php echo esc_attr( $rate['threshold'] ); ?>"/>
								</td>
								<td>
									<input name="affwp_settings[rates][<?php echo $key; ?>][rate]" type="text" value="<?php echo esc_attr( $rate['rate'] ); ?>"/>
								</td>
								<td>
									<input name="affwp_settings[rates][<?php echo $key; ?>][disabled]" id="affwp_settings[disabled]" type="checkbox" <?php checked( $disabled, true ); ?> aria-label="<?php echo esc_attr( $aria_label ); ?>"/>
								</td>
								<td>
									<a href="#" class="affwp_remove_rate" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">&times;</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="3"><?php _e( 'No tiered rates created yet', 'affiliate-wp-tiered' ); ?></td>
						</tr>
					<?php endif; ?>
                    			<?php if( empty( $rates ) ) : ?>
						<tr>
							<td>
								<select name="affwp_settings[rates][<?php echo $count; ?>][type]">
									<option value="referrals"><?php _e( 'Number of Referrals', 'affiliate-wp-tiered' ); ?></option>
									<option value="earnings"><?php _e( 'Total Earnings', 'affiliate-wp-tiered' ); ?></option>
								</select>
							</td>
							<td>
								<input name="affwp_settings[rates][<?php echo $count; ?>][threshold]" type="text" value=""/>
							</td>
							<td>
								<input name="affwp_settings[rates][<?php echo $count; ?>][rate]" type="text" value=""/>
							</td>
							<td>
								<a href="#" class="affwp_remove_rate" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">&times;</a>
							</td>
						</tr>
                    			<?php endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="1">
							<button id="affwp_new_rate" name="affwp_new_rate" class="button"><?php _e( 'Add New Rate', 'affiliate-wp-tiered' ); ?></button>
						</th>
						<th colspan="3">
							<?php _e( 'Add rates from low to high', 'affiliate-wp-tiered' ); ?>
						</th>
					</tr>
				</tfoot>
			</table>
		</form>
<?php
	}

	/**
	 * Displays the Tiered Rate Expiration setting.
	 *
	 * @since 1.1
	 * @access public
	 */
	public function rate_expiration_setting() {
		$expiration = affiliate_wp()->settings->get( 'rate-expiration', null );

		echo '<label for="affwp_settings[rate-expiration]">';
		printf( '<input type="checkbox" name="affwp_settings[rate-expiration]" id="affwp_settings[rate-expiration]" %1$s>',
			checked( isset( $expiration ), true, false )
		);
		echo __( 'Reset tiered affiliate rates on the 1st of every month.', 'affiliate-wp-tiered' ) . '</label>';
	}

}
new AffiliateWP_Rates_Admin;
