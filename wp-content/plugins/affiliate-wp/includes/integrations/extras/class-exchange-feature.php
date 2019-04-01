<?php

/**
 * Class Affiliate_WP_Exchange_Per_Product_Feature
 *
 * This class manages the product feature for configuring per-product rates.
 *
 * @since  1.5
 *
 * @access internal
 */
class Affiliate_WP_Exchange_Per_Product_Feature extends IT_Exchange_Product_Feature_Abstract {

	/**
	 * This echos the feature metabox.
	 *
	 * @since 1.5
	 *
	 * @param WP_Post $post
	 */
	public function print_metabox( $post ) {
		$data = it_exchange_get_product_feature( $post->ID, $this->slug );
		$rate = $data['rate'];
		$disabled = (bool) $data['disabled'];
		?>

		<p><?php _e( 'These settings will be used to calculate affiliate earnings per-sale. Leave blank to use default affiliate rates.', 'affiliate-wp' ); ?></p>

		<p>
			<label for="affwp_product_rate">
				<?php _e( 'Referral Rate', 'affiliate-wp' ); ?><br>
				<input type="text" name="_affwp_it-exchange_product_rate" id="affwp_product_rate" class="small-text" value="<?php echo esc_attr( $rate ); ?>" />
			</label>
		</p>

		<p>
			<label for="affwp_disable_referrals">
				<input type="checkbox" name="_affwp_it-exchange_referrals_disabled" id="affwp_disable_referrals" value="1"<?php checked( $disabled, true ); ?> />
				<?php _e( 'Disable referrals on this product', 'affiliate-wp' ); ?>
			</label>
		</p>


	<?php
	}

	/**
	 * This saves the values.
	 *
	 * @since 1.5
	 *
	 * @return void
	 */
	public function save_feature_on_product_save() {

		// Abort if we don't have a product ID
		$product_id = empty( $_POST['ID'] ) ? false : $_POST['ID'];
		if ( ! $product_id ) {
			return;
		}

		$data = array(
			'rate'      => sanitize_text_field( $_POST['_affwp_it-exchange_product_rate'] ),
			'disabled'  => (bool) isset( $_POST['_affwp_it-exchange_referrals_disabled'] ) ? 1 : false
		);

		it_exchange_update_product_feature( $product_id, $this->slug, $data );
	}

	/**
	 * This updates the feature for a product.
	 *
	 * We save the values to the spots affiliate WP expects, not as a unified array.
	 *
	 * @since 1.5
	 *
	 * @param integer $product_id the product id
	 * @param array   $new_value  the new values
	 * @param array   $options
	 *
	 * @return boolean
	 */
	function save_feature( $product_id, $new_value, $options = array() ) {

		$defaults = array(
			'rate'      => '',
			'disabled'  => false
		);

		$new_value = ITUtility::merge_defaults( $new_value, $defaults );

		$new_value['rate'] = trim( $new_value['rate'] );

		if ( ! empty( $new_value['rate'] ) ) {
			$new_value['rate'] = absint( $new_value['rate'] );
		}

		$res1 = update_post_meta( $product_id, '_affwp_it-exchange_product_rate', $new_value['rate'] );
		$res2 = update_post_meta( $product_id, '_affwp_it-exchange_referrals_disabled', (bool) $new_value['disabled'] );

		return $res1 && $res2;
	}

	/**
	 * Return the product's features.
	 *
	 * @since 1.5
	 *
	 * @param mixed     $existing the values passed in by the WP Filter API. Ignored here.
	 * @param integer   $product_id the WordPress post ID
	 * @param array     $options
	 *
	 * @return string product feature
	 */
	function get_feature( $existing, $product_id, $options = array() ) {
		$defaults = array(
			'rate'      => '',
			'disabled'  => false
		);

		$values = array(
			'rate'      => get_post_meta( $product_id, '_affwp_it-exchange_product_rate', true ),
			'disabled'  => (bool) get_post_meta( $product_id, '_affwp_it-exchange_referrals_disabled', true )
		);

		$values = ITUtility::merge_defaults( $values, $defaults );

		if ( ! isset( $options['field'] ) ) { // if we aren't looking for a particular field
			return $values;
		}

		$field = $options['field'];

		if ( isset( $values[ $field ] ) ) { // if the field exists with that name just return it
			return $values[ $field ];
		} else if ( strpos( $field, "." ) !== false ) { // if the field name was passed using array dot notation
			$pieces  = explode( '.', $field );
			$context = $values;
			foreach ( $pieces as $piece ) {
				if ( ! is_array( $context ) || ! array_key_exists( $piece, $context ) ) {
					// error occurred
					return null;
				}
				$context = &$context[ $piece ];
			}
			return $context;
		} else {
			return null; // we didn't find the data specified
		}
	}

	/**
	 * Does the product have the feature?
	 *
	 * @since 1.5
	 *
	 * @param mixed   $result Not used by core
	 * @param integer $product_id
	 * @param array   $options
	 *
	 * @return boolean
	 */
	function product_has_feature( $result, $product_id, $options = array() ) {

		$supports  = it_exchange_product_supports_feature( $product_id, $this->slug );
		$not_empty = it_exchange_get_product_feature( $product_id, $this->slug, array( 'field' => 'rate' ) ) != '';

		return $supports && $not_empty;
	}

	/**
	 * Does the product support this feature?
	 *
	 * This is different than if it has the feature, a product can
	 * support a feature but might not have the feature set.
	 *
	 * @since 1.5
	 *
	 * @param mixed   $result Not used by core
	 * @param integer $product_id
	 * @param array   $options
	 *
	 * @return boolean
	 */
	function product_supports_feature( $result, $product_id, $options = array() ) {

		$product_type = it_exchange_get_product_type( $product_id );

		return it_exchange_product_type_supports_feature( $product_type, $this->slug );
	}
}
new Affiliate_WP_Exchange_Per_Product_Feature( array(
	'slug'          => 'affwp-per-product-rate',
	'description'   => __( 'Manage per-product affiliate rates.', 'affiliate-wp' ),
	'metabox_title' => __( 'AffiliateWP', 'affiliate-wp' )
) );