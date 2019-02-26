<?php
	$order_id = get_query_var( 'order-pay' );

if ( $order_id ) {
	$order    = wc_get_order( $order_id );
	$document = $order ? get_user_meta( $order->get_user_id(), '_ebanx_document', true ) : false;
	$address  = $order->get_address();

	$fields        = array(
		'ebanx_billing_brazil_document' => array(
			'label' => 'CPF',
			'value' => $document,
		),
		'billing_phone'                 => array(
			'label' => 'Telephone',
			'value' => $address['phone'],
		),
		'billing_postcode'              => array(
			'label' => 'Postcode / ZIP',
			'value' => $address['postcode'],
		),
		'billing_address_1'             => array(
			'label' => __( 'Street address', 'woocommerce-gateway-ebanx' ),
			'value' => $address['address_1'],
		),
		'billing_city'                  => array(
			'label' => __( 'Town / City', 'woocommerce-gateway-ebanx' ),
			'value' => $address['city'],
		),
		'billing_country'               => array(
			'value' => $address['country'],
			'type'  => 'hidden',
		),
	);
	$countries_obj = new WC_Countries();
	$states        = $countries_obj->get_states( 'BR' );
}
?>

<?php if ( $order_id ) : ?>
	<div class="ebanx-compliance-fields ebanx-compliance-fiels-br">
		<?php foreach ( $fields as $name => $field ) : ?>
			<?php if ( isset( $field['type'] ) && 'hidden' === $field['type'] ) : ?>
				<input
					type="hidden"
					name="<?php echo esc_attr( "{$id}[{$name}]" ); ?>"
					value="<?php echo esc_attr( isset( $field['value'] ) ? $field['value'] : null ); ?>"
					class="input-text"
				/>
			<?php else : ?>
				<div class="ebanx-form-row ebanx-form-row-wide">
					<label for="<?php echo esc_attr( "{$id}[{$name}]" ); ?>"><?php echo esc_attr( $field['label'] ); ?></label>
					<input
						type="<?php echo esc_attr( isset( $field['type'] ) ? $field['type'] : 'text' ); ?>"
						name="<?php echo esc_attr( "{$id}[{$name}]" ); ?>"
						id="<?php echo esc_attr( "{$id}[{$name}]" ); ?>"
						value="<?php echo esc_attr( isset( $field['value'] ) ? $field['value'] : null ); ?>"
						class="input-text"
					/>
				</div>
			<?php endif ?>
		<?php endforeach ?>
		<div class="ebanx-form-row ebanx-form-row-wide">
			<label for="<?php echo esc_attr( "{$id}[billing_state]" ); ?>"><?php esc_html_e( 'State / County', 'woocommerce-gateway-ebanx' ); ?></label>
			<select name="<?php echo esc_attr( "{$id}[billing_state]" ); ?>" id="<?php echo esc_attr( "{$id}[billing_state]" ); ?>" class="ebanx-select-field">
				<option value="" selected>Select...</option>
				<?php foreach ( $states as $abbr => $name ) : ?>
					<option value="<?php echo esc_attr( $abbr ); ?>"><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
<?php endif ?>
