<?php
/**
 * WC_Settings_Restrictions class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Settings_Restrictions' ) ) :

/**
 * WooCommerce Global Restriction Settings.
 *
 * @version     1.1.10
 */
class WC_Settings_Restrictions extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'restrictions';
		$this->label = __( 'Restrictions', 'woocommerce-conditional-shipping-and-payments' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
		add_action( 'woocommerce_admin_field_wccsp_restrictions_overview', array( $this, 'restrictions_overview' ) );
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {

		$restrictions = WC_CSP()->restrictions->get_admin_global_field_restrictions();

		$sections = array(
			'' => __( 'Restrictions', 'woocommerce' )
		);

		foreach ( $restrictions as $restriction_id => $restriction ) {
			$sections[ $restriction_id ] = esc_html( $restriction->get_title() );
		}

		return apply_filters( 'woocommerce_csp_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {

		return apply_filters( 'woocommerce_csp_settings', array(

			array(
				'title' => __( 'Restrictions', 'woocommerce-conditional-shipping-and-payments' ),
				'type'  => 'title',
				'desc'  => __( 'Use Restrictions to conditionally exclude Payment Gateways, Shipping Methods and Shipping Countries or States.', 'woocommerce-conditional-shipping-and-payments' ),
				'id'    => 'restriction_options'
			),

			array(
				'type'  => 'wccsp_restrictions_overview'
			),

			array( 'type' => 'sectionend', 'id' => 'global_restriction_options' ),

			array(
				'title' => __( 'Debug Options', 'woocommerce-conditional-shipping-and-payments' ),
				'type'  => 'title',
				'desc'  => __( 'Use these options to troubleshoot your payment and shipping settings.', 'woocommerce-conditional-shipping-and-payments' ),
				'id'    => 'wccsp_restrictions_debug'
			),

			array(
				'title'         => __( 'Disable Global Restrictions', 'woocommerce-conditional-shipping-and-payments' ),
				'desc'          => __( 'Disable all global restrictions', 'woocommerce-conditional-shipping-and-payments' ),
				'id'            => 'wccsp_restrictions_disable_global',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'desc_tip'      => __( 'Disable all restrictions created in the <strong>Payment Gateways</strong>, <strong>Shipping Methods</strong> and <strong>Shipping Countries &amp; States</strong> tab sections above.', 'woocommerce-conditional-shipping-and-payments' ),
			),

			array(
				'title'         => __( 'Disable Product Restrictions', 'woocommerce-conditional-shipping-and-payments' ),
				'desc'          => __( 'Disable all product-level restrictions', 'woocommerce-conditional-shipping-and-payments' ),
				'id'            => 'wccsp_restrictions_disable_product',
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'desc_tip'      => __( 'Disable all restrictions created from the <strong>Product Data > Restrictions</strong> tab of your products.', 'woocommerce-conditional-shipping-and-payments' ),
			),

			array( 'type' => 'sectionend', 'id' => 'global_restriction_debug_options' ),

		) );
	}

	/**
	 * Output the settings.
	 * @return void
	 */
	public function output() {

		global $current_section;

		// Define restrictions that can be customised here.

		if ( $current_section ) {

			$restriction = WC_CSP()->restrictions->get_restriction( $current_section );

			if ( $restriction ) {
				$restriction->admin_options();
			}

		} else {

			$settings = $this->get_settings();

			WC_Admin_Settings::output_fields( $settings );
		}
	}

	/**
	 * Save settings.
	 * @return void
	 */
	public function save() {

		global $current_section;

		if ( ! $current_section ) {

			$settings = $this->get_settings();
			WC_Admin_Settings::save_fields( $settings );

		} else {

			do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
		}

		// Clear cached shipping rates.
		WC_CSP_Core_Compatibility::clear_cached_shipping_rates();
	}

	/**
	 * Output restrictions overview table.
	 *
	 * @return void
	 */
	public function restrictions_overview() {

		$restrictions = WC_CSP()->restrictions->get_admin_global_field_restrictions();

		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Restrictions Overview', 'woocommerce-conditional-shipping-and-payments' ) ?></th>
			<td class="forminp">
				<table class="wc_shipping wc_restrictions_overview widefat wp-list-table" cellspacing="0">
					<thead>
						<tr>
							<th class="name"><?php _e( 'Restriction Type', 'woocommerce-conditional-shipping-and-payments' ); ?></th>
							<th class="status"><?php _e( 'Active Rules', 'woocommerce-conditional-shipping-and-payments' ); ?></th>
							<th class="summary"><?php _e( 'Summary', 'woocommerce-conditional-shipping-and-payments' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $restrictions as $restriction_id => $restriction ) {

							$rules = $restriction->get_global_restriction_data( false );

							?><tr>
								<td class="name">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=restrictions&section=' . $restriction_id ) ); ?>">
										<?php echo esc_html( $restriction->get_title() ); ?>
									</a>
								</td>
								<td class="status">
									<?php echo count( $rules ); ?>
								</td>
								<td class="summary">
									<ul><?php

									if ( ! empty( $rules ) ) {
										foreach ( $rules as $rule_key => $rule ) {
											?><li>
												<?php echo $restriction->get_options_description( $rule ); ?>
												<a class="edit_rule" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=restrictions&section=' . $restriction_id . '&view_rule=' . $rule_key ) ); ?>"></a>
											</li><?php
										}

									} else {
										echo '<li>&ndash;</li>';
									}

									?></ul>
								</td>
							</tr><?php
						}
					?></tbody>
				</table>
			</td>
		</tr>
		<?php
	}
}

endif;

return new WC_Settings_Restrictions();
