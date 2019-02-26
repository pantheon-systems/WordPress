<?php
/**
 *
 * @var int
 */
$order_id = get_query_var( 'order-pay' );

if ( $order_id ) {
	$order    = wc_get_order( $order_id );
	$document = $order ? get_user_meta( $order->get_user_id(), '_ebanx_document', true ) : false;
	$address  = $order->get_address();

	$fields = array(
		'ebanx_billing_argentina_document' => array(
			'label' => __( 'Document', 'woocommerce-gateway-ebanx' ),
			'value' => $document,
		),
		'billing_postcode'                 => array(
			'label' => 'Postcode / ZIP',
			'value' => $address['postcode'],
		),
		'billing_address_1'                => array(
			'label' => __( 'Street address', 'woocommerce-gateway-ebanx' ),
			'value' => $address['address_1'],
		),
		'billing_city'                     => array(
			'label' => __( 'Town / City', 'woocommerce-gateway-ebanx' ),
			'value' => $address['city'],
		),
		'billing_state'                    => array(
			'label' => __( 'State / County', 'woocommerce-gateway-ebanx' ),
			'value' => $address['state'],
		),
		'billing_country'                  => array(
			'label' => 'Country',
			'value' => $address['country'],
			'type'  => 'hidden',
		),
	);
}
?>

<?php if ( $order_id ) : ?>
	<div class="ebanx-compliance-fields ebanx-compliance-fiels-ar">
		<?php foreach ( $fields as $name => $field ) : ?>
			<?php if ( isset( $field['type'] ) && 'hidden' === $field['type'] ) : ?>
				<input
					type="hidden"
					name="<?php echo esc_html( "{$id}[{$name}]" ); ?>"
					value="<?php echo isset( $field['value'] ) ? esc_html( $field['value'] ) : null; ?>"
					class="input-text"
				/>
			<?php else : ?>
				<div class="ebanx-form-row ebanx-form-row-wide">
					<label for="<?php echo esc_html( "{$id}[{$name}]" ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
					<input
						type="<?php echo isset( $field['type'] ) ? esc_html( $field['type'] ) : 'text'; ?>"
						name="<?php echo esc_html( "{$id}[{$name}]" ); ?>"
						id="<?php echo esc_html( "{$id}[{$name}]" ); ?>"
						value="<?php echo isset( $field['value'] ) ? esc_html( $field['value'] ) : null; ?>"
						class="input-text"
					/>
				</div>
			<?php endif ?>
		<?php endforeach ?>
		<div class="ebanx-form-row ebanx-form-row-wide">
			<label for="<?php echo esc_html( "{$id}[billing_state]" ); ?>"><?php esc_html_e( 'State / County', 'woocommerce-gateway-ebanx' ); ?></label>
			<select name="<?php echo esc_html( "{$id}[billing_state]" ); ?>" id="<?php echo esc_html( "{$id}[billing_state]" ); ?>" class="ebanx-select-field">
				<option value="" selected>Select...</option>
				<?php foreach ( $states as $abbr => $name ) : ?>
					<option value="<?php echo esc_html( $abbr ); ?>"><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
<?php endif ?>
