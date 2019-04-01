<?php

class Affiliate_WP_Formidable_Pro extends Affiliate_WP_Base {

	/**
	 * @see Affiliate_WP_Base::init
	 * @access  public
	 * @since   1.6
	 */
	public function init() {

		$this->context = 'formidablepro';

		add_filter( 'frm_add_form_settings_section', array( $this, 'frm_add_form_settings_section' ), 10, 2 );
		add_filter( 'frm_form_options_before_update', array( $this, 'frm_form_options_before_update' ), 15, 2 );

		add_filter( 'frm_after_create_entry', array( $this, 'add_pending_referral' ), 9, 2 );

		add_action( 'frm_payment_status_complete', array( $this, 'mark_referral_complete' ), 10 );
		add_action( 'frm_payment_paypal_ipn', array( $this, 'mark_referral_complete_paypal' ), 10 );

		add_action( 'frm_payment_status_failed', array( $this, 'revoke_referral_on_refund' ), 10, 2 );
		add_action( 'frm_payment_status_refunded', array( $this, 'revoke_referral_on_refund' ), 10, 2 );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

	}

	/**
	 * Add section to Formidable Pro form sections
	 *
	 * @since 1.6
	 *
	 * @author Naomi C. Bush <hello@naomicbush.com>
	 *
	 * @param array $sections
	 * @param array $values
	 *
	 * @return array
	 */
	public function frm_add_form_settings_section( $sections, $values ) {

		$sections[ 'affiliateWP' ] = array(
			'class'    => 'Affiliate_WP_Formidable_Pro',
		    'function' => 'do_settings_section'
		);

		return $sections;
	}

	/**
	 * Render Formidable Pro settings section
	 *
	 * @since 1.6
	 *
	 * @author Naomi C. Bush <hello@naomicbush.com>
	 *
	 * @param array $values
	 */
	public static function do_settings_section( $values ) {

		if ( isset( $values[ 'id' ] ) ) {
			$frm_field   = new FrmField();
			$form_fields = $frm_field->getAll( "fi.form_id='{$values['id']}' and fi.type not in ('divider', 'html', 'break', 'captcha', 'rte', 'form')", ' ORDER BY field_order' );
			unset( $frm_field );
		}

		?>
		<style type="text/css">
			.icon-affiliatewp:before {
				font-family: 'affwp-dashicons' !important;
				content: "\e000";
			}
		</style>
		<h2><span class="icon-affiliatewp"></span> <?php _e( 'AffiliateWP', 'affiliate-wp' ); ?></h2>

		<table class="form-table">
			<tr>
				<th scope="row" nowrap="nowrap">
					<label for="affiliatewp_referral_description_field"><?php _e( 'Referral description', 'affiliate-wp' ); ?></label>
				</th>
				<td>
					<select name="options[affiliatewp][referral_description_field]">
						<option value="">&mdash; <?php _e( 'Select Field', 'affiliate-wp' ) ?> &mdash;</option>
						<?php
						if ( isset( $form_fields ) and is_array( $form_fields ) ) {
							foreach ( $form_fields as $field ) {
								if ( 'checkbox' == $field->type ) {
									continue;
								}
								?>
								<option value="<?php echo $field->id ?>" <?php selected( $field->id, self::get_array_values( $values, 'affiliatewp/referral_description_field' ) ) ?>>
									<?php echo substr( esc_attr( stripslashes( $field->name ) ), 0, 50 );
									unset( $field );
									?>
								</option>
							<?php
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" nowrap="nowrap">
					<label for="affiliatewp_purchase_amount_field"><?php _e( 'Purchase Amount', 'affiliate-wp' ); ?></label>
				</th>
				<td>
					<select name="options[affiliatewp][purchase_amount_field]">
						<option value="">&mdash; <?php _e( 'Select Field', 'affiliate-wp' ) ?> &mdash;</option>
						<?php
						if ( isset( $form_fields ) and is_array( $form_fields ) ) {
							foreach ( $form_fields as $field ) {
								if ( $field->type == 'checkbox' ) {
									continue;
								}
								?>
								<option value="<?php echo $field->id ?>" <?php selected( $field->id, self::get_array_values( $values, 'affiliatewp/purchase_amount_field' ) ) ?>>
									<?php echo substr( esc_attr( stripslashes( $field->name ) ), 0, 50 );
									unset( $field );
									?>
								</option>
							<?php
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" nowrap="nowrap">
					<label for="affwp_referral_type"><?php _e( 'Referral Type', 'affiliate-wp' ); ?></label>
				</th>
				<td>
					<select name="options[affiliatewp][referral_type]" id="affwp_referral_type">
						<?php foreach( affiliate_wp()->referrals->types_registry->get_types() as $type_id => $type ) : ?>
							<option value="<?php echo esc_attr( $type_id ); ?>"<?php selected( $type_id, self::get_array_values( $values, 'affiliatewp/referral_type' ) ); ?>><?php echo esc_html( $type['label'] ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php esc_html_e( 'Select the referral type for this form.', 'affiliate-wp' ); ?>
				</td>
			</tr>
		</table>
	<?php
	}

	/**
	 * Save AffiliateWP section settings when form settings are updated
	 *
	 * @since 1.6
	 *
	 * @author Naomi C. Bush <hello@naomicbush.com>
	 *
	 * @param array $options
	 * @param array $values
	 *
	 * @return array
	 */
	public function frm_form_options_before_update( $options, $values ) {

		$options[ 'affiliatewp' ][ 'referral_description_field' ] = ( isset( $values[ 'options' ][ 'affiliatewp' ][ 'referral_description_field' ] ) ) ? $values[ 'options' ][ 'affiliatewp' ][ 'referral_description_field' ] : '';
		$options[ 'affiliatewp' ][ 'purchase_amount_field' ]      = ( isset( $values[ 'options' ][ 'affiliatewp' ][ 'purchase_amount_field' ] ) ) ? $values[ 'options' ][ 'affiliatewp' ][ 'purchase_amount_field' ] : '';

		return $options;

	}

	/**
	 * Add referral when form is submitted
	 *
	 * @since 1.6
	 *
	 * @author Naomi C. Bush <hello@naomicbush.com>
	 *
	 * @param int $entry_id
	 * @param int $form_id
	 */
	public function add_pending_referral( $entry_id, $form_id ) {

		if ( $this->was_referred() ) {

			$form = FrmForm::getOne( $form_id );

			$this->referral_type = isset( $form->options['affiliatewp']['referral_type'] ) ? $form->options['affiliatewp']['referral_type'] : 'sale';

			$field_referral_description = $form->options['affiliatewp']['referral_description_field'];
			$field_purchase_amount      = $form->options['affiliatewp']['purchase_amount_field'];

			// Return if the "Referral description" and "Purchase Amount" options were not configured in the form settings.
			if ( empty( $field_referral_description ) || empty( $field_purchase_amount ) ) {
				return;
			}

			$description     = FrmEntryMeta::get_entry_meta_by_field( $entry_id, $field_referral_description );
			$description     = ! empty( $description ) ? $description : '';
			$purchase_amount = floatval( FrmEntryMeta::get_entry_meta_by_field( $entry_id, $field_purchase_amount ) );

			$referral_total  = $this->calculate_referral_amount( $purchase_amount, $entry_id );

			$this->insert_pending_referral( $referral_total, $entry_id, $description );

			if ( empty( $referral_total ) ) {
				$this->mark_referral_complete( array( 'entry_id' => $entry_id ) );
			}
		}

	}

	/**
	 * Update referral status and add note to Formidable Pro entry
	 *
	 * @since 1.6
	 *
	 * @author Naomi C. Bush <hello@naomicbush.com>
	 *
	 * @param array $atts
	 */
	public function mark_referral_complete( $atts ) {

		$this->complete_referral( $atts['entry_id'] );

		$referral = affiliate_wp()->referrals->get_by( 'reference', $atts['entry_id'], $this->context );
		$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
		$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
		$note     = sprintf( __( 'AffiliateWP: Referral #%1$d for %2$s recorded for %3$s (ID: %4$d).', 'affiliate-wp' ),
			$referral->referral_id,
			$amount,
			$name,
			$referral->affiliate_id
		);

		FrmEntryMeta::add_entry_meta( $atts['entry_id'], 0, '', array( 'comment' => $note, 'user_id' => 0 ) );

	}

	/**
	 * Update referral status and add note to Formidable Pro entry for Formidable PayPal Standard add-on
	 *
	 * @since 2.2.2
	 *
	 * @param array $atts
	 */
	public function mark_referral_complete_paypal( $atts ) {

		if ( isset( $atts['pay_vars']['completed'] ) && $atts['pay_vars']['completed'] ) {

			if ( isset( $atts['entry']->id ) ) {

				$entry_id = $atts['entry']->id;

				$this->complete_referral( $entry_id );

				$referral = affiliate_wp()->referrals->get_by( 'reference', $entry_id, $this->context );
				$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
				$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
				$note     = sprintf( __( 'AffiliateWP: Referral #%d for %s recorded for %s', 'affiliate-wp' ), $referral->referral_id, $amount, $name );

				FrmEntryMeta::add_entry_meta( $entry_id, 0, '', array( 'comment' => $note, 'user_id' => 0 ) );

			}

		}

	}

	/**
	 * Update referral status and add note to Formidable Pro entry
	 *
	 * @since 1.6
	 *
	 * @author Naomi C. Bush <hello@naomicbush.com>
	 *
	 * @param array $atts
	 */
	public function revoke_referral_on_refund( $atts ) {

		$this->reject_referral( $atts['entry_id'] );

		$referral = affiliate_wp()->referrals->get_by( 'reference', $atts['entry_id'], $this->context );
		$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
		$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
		$note     = sprintf( __( 'AffiliateWP: Referral #%d for %s for %s rejected', 'affiliate-wp' ), $referral->referral_id, $amount, $name );

		FrmEntryMeta::add_entry_meta( $atts['entry_id'], 0, '', array( 'comment' => $note, 'user_id' => 0 ) );

	}

	/**
	 * Link to Formidable Pro entry in the referral reference column
	 *
	 * @since 1.6
	 *
	 * @author   Naomi C. Bush <hello@naomicbush.com>
	 *
	 * @param int    $reference
	 * @param object $referral
	 *
	 * @return string
	 *
	 */
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'formidablepro' != $referral->context ) {

			return $reference;

		}

		$url = admin_url( 'admin.php?page=formidable-entries&frm_action=show&id=' . $reference );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';

	}

	/**
	 * Helper function to retrieve a value from an array
	 *
	 * @since 1.6
	 *
	 * @author Naomi C. Bush <hello@naomicbush.com>
	 *
	 * @param array  $array
	 * @param string $key
	 *
	 * @return string
	 */
	public static function get_array_value( $array, $key ) {

		return isset( $array[ $key ] ) ? $array[ $key ] : '';

	}

	/**
	 * Helper function to retrieve a value from a multidimensional array
	 *
	 * @since 1.6
	 *
	 * @author Naomi C. Bush <hello@naomicbush.com>
	 *
	 * @param array  $array
	 * @param string $key
	 *
	 * @return string
	 */
	public static function get_array_values( $array, $keys ) {

		$keys  = explode( '/', $keys );
		$value = $array;

		foreach ( $keys as $current_key ) {
			$value = self::get_array_value( $value, $current_key );
		}

		return $value;

	}

}

if ( class_exists( 'FrmHooksController' ) ) {
	new Affiliate_WP_Formidable_Pro;
}
