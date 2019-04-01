<?php
/**
 * WooCommerce Product Settings
 *
 * @package WooCommerce/Admin
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Settings_Products', false ) ) {
	return new WC_Settings_Products();
}

/**
 * WC_Settings_Products.
 */
class WC_Settings_Products extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'products';
		$this->label = __( 'Products', 'woocommerce' );

		parent::__construct();
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			''             => __( 'General', 'woocommerce' ),
			'inventory'    => __( 'Inventory', 'woocommerce' ),
			'downloadable' => __( 'Downloadable products', 'woocommerce' ),
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		$settings = $this->get_settings( $current_section );

		$this->product_display_settings_moved_notice();

		WC_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Show a notice showing where some options have moved.
	 *
	 * @since 3.3.0
	 * @todo remove in next major release.
	 */
	private function product_display_settings_moved_notice() {
		if ( get_user_meta( get_current_user_id(), 'dismissed_product_display_settings_moved_notice', true ) ) {
			return;
		}
		?>
		<div id="message" class="updated woocommerce-message inline">
			<a class="woocommerce-message-close notice-dismiss" style="top:0;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'product_display_settings_moved' ), 'woocommerce_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woocommerce' ); ?></a>

			<p>
				<?php
				echo wp_kses(
					sprintf(
						/* translators: %s: URL to customizer. */
						__( 'Looking for the product display options? They can now be found in the Customizer. <a href="%s">Go see them in action here.</a>', 'woocommerce' ), esc_url(
							add_query_arg(
								array(
									'autofocus' => array(
										'panel' => 'woocommerce',
									),
									'url'       => wc_get_page_permalink( 'shop' ),
								), admin_url( 'customize.php' )
							)
						)
					), array(
						'a' => array(
							'href'  => array(),
							'title' => array(),
						),
					)
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::save_fields( $settings );

		if ( $current_section ) {
			do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
		}
	}

	/**
	 * Get settings array.
	 *
	 * @param string $current_section Current section name.
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {
		if ( 'inventory' === $current_section ) {
			$settings = apply_filters(
				'woocommerce_inventory_settings', array(

					array(
						'title' => __( 'Inventory', 'woocommerce' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'product_inventory_options',
					),

					array(
						'title'   => __( 'Manage stock', 'woocommerce' ),
						'desc'    => __( 'Enable stock management', 'woocommerce' ),
						'id'      => 'woocommerce_manage_stock',
						'default' => 'yes',
						'type'    => 'checkbox',
					),

					array(
						'title'             => __( 'Hold stock (minutes)', 'woocommerce' ),
						'desc'              => __( 'Hold stock (for unpaid orders) for x minutes. When this limit is reached, the pending order will be cancelled. Leave blank to disable.', 'woocommerce' ),
						'id'                => 'woocommerce_hold_stock_minutes',
						'type'              => 'number',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
						),
						'css'               => 'width: 80px;',
						'default'           => '60',
						'autoload'          => false,
						'class'             => 'manage_stock_field',
					),

					array(
						'title'         => __( 'Notifications', 'woocommerce' ),
						'desc'          => __( 'Enable low stock notifications', 'woocommerce' ),
						'id'            => 'woocommerce_notify_low_stock',
						'default'       => 'yes',
						'type'          => 'checkbox',
						'checkboxgroup' => 'start',
						'autoload'      => false,
						'class'         => 'manage_stock_field',
					),

					array(
						'desc'          => __( 'Enable out of stock notifications', 'woocommerce' ),
						'id'            => 'woocommerce_notify_no_stock',
						'default'       => 'yes',
						'type'          => 'checkbox',
						'checkboxgroup' => 'end',
						'autoload'      => false,
						'class'         => 'manage_stock_field',
					),

					array(
						'title'    => __( 'Notification recipient(s)', 'woocommerce' ),
						'desc'     => __( 'Enter recipients (comma separated) that will receive this notification.', 'woocommerce' ),
						'id'       => 'woocommerce_stock_email_recipient',
						'type'     => 'text',
						'default'  => get_option( 'admin_email' ),
						'css'      => 'width: 250px;',
						'autoload' => false,
						'desc_tip' => true,
						'class'    => 'manage_stock_field',
					),

					array(
						'title'             => __( 'Low stock threshold', 'woocommerce' ),
						'desc'              => __( 'When product stock reaches this amount you will be notified via email.', 'woocommerce' ),
						'id'                => 'woocommerce_notify_low_stock_amount',
						'css'               => 'width:50px;',
						'type'              => 'number',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
						),
						'default'           => '2',
						'autoload'          => false,
						'desc_tip'          => true,
						'class'             => 'manage_stock_field',
					),

					array(
						'title'             => __( 'Out of stock threshold', 'woocommerce' ),
						'desc'              => __( 'When product stock reaches this amount the stock status will change to "out of stock" and you will be notified via email. This setting does not affect existing "in stock" products.', 'woocommerce' ),
						'id'                => 'woocommerce_notify_no_stock_amount',
						'css'               => 'width:50px;',
						'type'              => 'number',
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
						),
						'default'           => '0',
						'desc_tip'          => true,
						'class'             => 'manage_stock_field',
					),

					array(
						'title'   => __( 'Out of stock visibility', 'woocommerce' ),
						'desc'    => __( 'Hide out of stock items from the catalog', 'woocommerce' ),
						'id'      => 'woocommerce_hide_out_of_stock_items',
						'default' => 'no',
						'type'    => 'checkbox',
					),

					array(
						'title'    => __( 'Stock display format', 'woocommerce' ),
						'desc'     => __( 'This controls how stock quantities are displayed on the frontend.', 'woocommerce' ),
						'id'       => 'woocommerce_stock_format',
						'css'      => 'min-width:150px;',
						'class'    => 'wc-enhanced-select',
						'default'  => '',
						'type'     => 'select',
						'options'  => array(
							''           => __( 'Always show quantity remaining in stock e.g. "12 in stock"', 'woocommerce' ),
							'low_amount' => __( 'Only show quantity remaining in stock when low e.g. "Only 2 left in stock"', 'woocommerce' ),
							'no_amount'  => __( 'Never show quantity remaining in stock', 'woocommerce' ),
						),
						'desc_tip' => true,
					),

					array(
						'type' => 'sectionend',
						'id'   => 'product_inventory_options',
					),

				)
			);

		} elseif ( 'downloadable' === $current_section ) {
			$settings = apply_filters(
				'woocommerce_downloadable_products_settings', array(
					array(
						'title' => __( 'Downloadable products', 'woocommerce' ),
						'type'  => 'title',
						'id'    => 'digital_download_options',
					),

					array(
						'title'    => __( 'File download method', 'woocommerce' ),
						'desc'     => sprintf(
							/* translators: 1: X-Accel-Redirect 2: X-Sendfile 3: mod_xsendfile */
							__( 'Forcing downloads will keep URLs hidden, but some servers may serve large files unreliably. If supported, %1$s / %2$s can be used to serve downloads instead (server requires %3$s).', 'woocommerce' ),
							'<code>X-Accel-Redirect</code>',
							'<code>X-Sendfile</code>',
							'<code>mod_xsendfile</code>'
						),
						'id'       => 'woocommerce_file_download_method',
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'css'      => 'min-width:300px;',
						'default'  => 'force',
						'desc_tip' => true,
						'options'  => array(
							'force'     => __( 'Force downloads', 'woocommerce' ),
							'xsendfile' => __( 'X-Accel-Redirect/X-Sendfile', 'woocommerce' ),
							'redirect'  => __( 'Redirect only', 'woocommerce' ),
						),
						'autoload' => false,
					),

					array(
						'title'         => __( 'Access restriction', 'woocommerce' ),
						'desc'          => __( 'Downloads require login', 'woocommerce' ),
						'id'            => 'woocommerce_downloads_require_login',
						'type'          => 'checkbox',
						'default'       => 'no',
						'desc_tip'      => __( 'This setting does not apply to guest purchases.', 'woocommerce' ),
						'checkboxgroup' => 'start',
						'autoload'      => false,
					),

					array(
						'desc'          => __( 'Grant access to downloadable products after payment', 'woocommerce' ),
						'id'            => 'woocommerce_downloads_grant_access_after_payment',
						'type'          => 'checkbox',
						'default'       => 'yes',
						'desc_tip'      => __( 'Enable this option to grant access to downloads when orders are "processing", rather than "completed".', 'woocommerce' ),
						'checkboxgroup' => 'end',
						'autoload'      => false,
					),

					array(
						'type' => 'sectionend',
						'id'   => 'digital_download_options',
					),

				)
			);

		} else {
			$settings = apply_filters(
				'woocommerce_product_settings', apply_filters(
					'woocommerce_products_general_settings', array(
						array(
							'title' => __( 'Shop pages', 'woocommerce' ),
							'type'  => 'title',
							'desc'  => '',
							'id'    => 'catalog_options',
						),
						array(
							'title'    => __( 'Shop page', 'woocommerce' ),
							/* translators: %s: URL to settings. */
							'desc'     => '<br/>' . sprintf( __( 'The base page can also be used in your <a href="%s">product permalinks</a>.', 'woocommerce' ), admin_url( 'options-permalink.php' ) ),
							'id'       => 'woocommerce_shop_page_id',
							'type'     => 'single_select_page',
							'default'  => '',
							'class'    => 'wc-enhanced-select-nostd',
							'css'      => 'min-width:300px;',
							'desc_tip' => __( 'This sets the base page of your shop - this is where your product archive will be.', 'woocommerce' ),
						),
						array(
							'title'         => __( 'Add to cart behaviour', 'woocommerce' ),
							'desc'          => __( 'Redirect to the cart page after successful addition', 'woocommerce' ),
							'id'            => 'woocommerce_cart_redirect_after_add',
							'default'       => 'no',
							'type'          => 'checkbox',
							'checkboxgroup' => 'start',
						),
						array(
							'desc'          => __( 'Enable AJAX add to cart buttons on archives', 'woocommerce' ),
							'id'            => 'woocommerce_enable_ajax_add_to_cart',
							'default'       => 'yes',
							'type'          => 'checkbox',
							'checkboxgroup' => 'end',
						),
						array(
							'title'       => __( 'Placeholder image', 'woocommerce' ),
							'id'          => 'woocommerce_placeholder_image',
							'type'        => 'text',
							'default'     => '',
							'class'       => '',
							'css'         => '',
							'placeholder' => __( 'Enter attachment ID or URL to an image', 'woocommerce' ),
							'desc_tip'    => __( 'This is the attachment ID, or image URL, used for placeholder images in the product catalog. Products with no image will use this.', 'woocommerce' ),
						),
						array(
							'type' => 'sectionend',
							'id'   => 'catalog_options',
						),

						array(
							'title' => __( 'Measurements', 'woocommerce' ),
							'type'  => 'title',
							'id'    => 'product_measurement_options',
						),

						array(
							'title'    => __( 'Weight unit', 'woocommerce' ),
							'desc'     => __( 'This controls what unit you will define weights in.', 'woocommerce' ),
							'id'       => 'woocommerce_weight_unit',
							'class'    => 'wc-enhanced-select',
							'css'      => 'min-width:300px;',
							'default'  => 'kg',
							'type'     => 'select',
							'options'  => array(
								'kg'  => __( 'kg', 'woocommerce' ),
								'g'   => __( 'g', 'woocommerce' ),
								'lbs' => __( 'lbs', 'woocommerce' ),
								'oz'  => __( 'oz', 'woocommerce' ),
							),
							'desc_tip' => true,
						),

						array(
							'title'    => __( 'Dimensions unit', 'woocommerce' ),
							'desc'     => __( 'This controls what unit you will define lengths in.', 'woocommerce' ),
							'id'       => 'woocommerce_dimension_unit',
							'class'    => 'wc-enhanced-select',
							'css'      => 'min-width:300px;',
							'default'  => 'cm',
							'type'     => 'select',
							'options'  => array(
								'm'  => __( 'm', 'woocommerce' ),
								'cm' => __( 'cm', 'woocommerce' ),
								'mm' => __( 'mm', 'woocommerce' ),
								'in' => __( 'in', 'woocommerce' ),
								'yd' => __( 'yd', 'woocommerce' ),
							),
							'desc_tip' => true,
						),

						array(
							'type' => 'sectionend',
							'id'   => 'product_measurement_options',
						),

						array(
							'title' => __( 'Reviews', 'woocommerce' ),
							'type'  => 'title',
							'desc'  => '',
							'id'    => 'product_rating_options',
						),

						array(
							'title'           => __( 'Enable reviews', 'woocommerce' ),
							'desc'            => __( 'Enable product reviews', 'woocommerce' ),
							'id'              => 'woocommerce_enable_reviews',
							'default'         => 'yes',
							'type'            => 'checkbox',
							'checkboxgroup'   => 'start',
							'show_if_checked' => 'option',
						),

						array(
							'desc'            => __( 'Show "verified owner" label on customer reviews', 'woocommerce' ),
							'id'              => 'woocommerce_review_rating_verification_label',
							'default'         => 'yes',
							'type'            => 'checkbox',
							'checkboxgroup'   => '',
							'show_if_checked' => 'yes',
							'autoload'        => false,
						),

						array(
							'desc'            => __( 'Reviews can only be left by "verified owners"', 'woocommerce' ),
							'id'              => 'woocommerce_review_rating_verification_required',
							'default'         => 'no',
							'type'            => 'checkbox',
							'checkboxgroup'   => 'end',
							'show_if_checked' => 'yes',
							'autoload'        => false,
						),

						array(
							'title'           => __( 'Product ratings', 'woocommerce' ),
							'desc'            => __( 'Enable star rating on reviews', 'woocommerce' ),
							'id'              => 'woocommerce_enable_review_rating',
							'default'         => 'yes',
							'type'            => 'checkbox',
							'checkboxgroup'   => 'start',
							'show_if_checked' => 'option',
						),

						array(
							'desc'            => __( 'Star ratings should be required, not optional', 'woocommerce' ),
							'id'              => 'woocommerce_review_rating_required',
							'default'         => 'yes',
							'type'            => 'checkbox',
							'checkboxgroup'   => 'end',
							'show_if_checked' => 'yes',
							'autoload'        => false,
						),

						array(
							'type' => 'sectionend',
							'id'   => 'product_rating_options',
						),

					)
				)
			);
		}

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
	}
}

return new WC_Settings_Products();
