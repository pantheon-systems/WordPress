<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * WooCommercePoorGuysSwissKnife Main Class
 *
 * @class 		WCPGSK_Main
 * @version		1.4
 * @package		WooCommerce-Poor-Guys-Swiss-Knife/Classes
 * @category	Class
 * @author 		Uli Hake
 */
 
if ( ! class_exists ( 'WCPGSK_Main' ) ) {

    class WCPGSK_Main {
		private $dir;
		private $assets_dir;
		private $assets_url;
		public $version;
		private $file;
		
		/**
		 * Constructor function.
		 *
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */
		public function __construct( $file ) {
			global $wcpgsk_about;
			$this->dir = dirname( $file );
			$this->file = $file;
			$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
			$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
			$this->load_plugin_textdomain();		

			add_action( 'init', array( $this, 'load_localisation' ), 0 );
			// Run this on activation.
			register_activation_hook( $this->file, array( $this, 'activation' ) );
			if ( is_admin() ) : // admin actions
				// Hook into admin_init first
				add_action( 'admin_init', array($this, 'wcpgsk_register_setting') );
				add_filter( 'plugin_action_links_'. plugin_basename($this->file), array($this, 'wcpgsk_admin_plugin_actions'), -10 );
				add_action( 'admin_menu', array($this, 'wcpgsk_admin_menu') );				
				$wcpgsk_about = new WCPGSK_About( $this->file );
				//add_action( 'add_meta_boxes', array($this, 'wcpgsk_add_meta_box_minmaxstep'), 10 );
				add_action('woocommerce_product_options_general_product_data', array( $this, 'wcpgsk_set_price_html' ), 99 );
				
				add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'wcpgsk_product_write_panel_tab' ), 99 );
				add_action( 'woocommerce_product_write_panels',     array( $this, 'wcpgsk_product_write_panels' ), 99 );
				add_action( 'woocommerce_process_product_meta', array( $this, 'wcpgsk_process_product_meta' ), 99);
				add_action( 'woocommerce_process_product_meta_variable', array( $this, 'wcpgsk_process_product_meta' ), 99);
				add_action('wp_ajax_get_locale_field_form', array($this, 'get_locale_field_form_callback'));	
				
				add_action('wp_ajax_wcpgsk_save_checkoutjs', array($this, 'wcpgsk_save_checkoutjs_callback'));	
				add_action( 'wcpgsk_settings_page_email', array( $this, 'wcpgsk_settings_page_email_wcpgsk' ), 10, 1 );
				add_action( 'wcpgsk_settings_page_labels', array( $this, 'wcpgsk_page_labels' ), 10, 1 );
				
			endif;
			
			//billing and shipping filters
			add_filter( 'woocommerce_billing_fields' , array($this, 'add_billing_custom_fields'), 10, 1 );
			add_filter( 'woocommerce_shipping_fields' , array($this, 'add_shipping_custom_fields'), 10, 1 );


			add_filter('woocommerce_admin_billing_fields', array($this, 'wcpgsk_admin_billing_fields'), 10, 1);
			add_filter('woocommerce_admin_shipping_fields', array($this, 'wcpgsk_admin_shipping_fields'), 10, 1);

			add_filter( 'woocommerce_checkout_fields' , array($this, 'wcpgsk_checkout_fields_billing'), 10, 1 );
			add_filter( 'woocommerce_checkout_fields' , array($this, 'wcpgsk_checkout_fields_shipping'), 10, 1 );
			if ( function_exists('WC') ) :
				add_filter( 'wc_address_i18n_params', array($this, 'wcpgsk_address_i18n_params'), 10, 1 );//$this->locale['default'] );
				//bind late to allow other plugins to add their fields
				add_filter( 'woocommerce_country_locale_field_selectors', array($this, 'wcpgsk_country_locale_field_selectors'), 99, 1);
			else :
				add_filter( 'woocommerce_params', array($this, 'wcpgsk_address_i18n_params'), 10, 1 );// $woocommerce_params )			
			endif;
			
			
			add_action( 'woocommerce_checkout_process', array($this, 'wcpgsk_checkout_process'), 9 );

			add_filter( 'woocommerce_load_order_data', array($this, 'wcpgsk_load_order_data'), 5,  1);
			add_action( 'woocommerce_checkout_init', array($this, 'wcpgsk_checkout_init'), 10, 1 );
						
			add_action( 'woocommerce_email_after_order_table', array($this, 'wcpgsk_email_after_order_table') );
			add_action( 'woocommerce_order_details_after_order_table', array($this, 'wcpgsk_order_details_after_order_table'), 10, 1 );
			add_filter( 'woocommerce_address_to_edit', array($this, 'wcpgsk_address_to_edit'), 10, 1 );
			add_action( 'woocommerce_after_template_part', array($this, 'wcpgsk_after_template_part'), 10, 4 );
			
			add_filter( 'woocommerce_process_myaccount_field_billing_postcode', array($this, 'wcpgsk_process_myaccount_field_billing_postcode'), 99, 1 );
			add_filter( 'woocommerce_process_myaccount_field_shipping_postcode', array($this, 'wcpgsk_process_myaccount_field_shipping_postcode'), 99, 1 );

			add_filter( 'woocommerce_order_formatted_billing_address', array($this, 'wcpgsk_order_formatted_billing_address'), 99 );
			add_filter( 'woocommerce_order_formatted_shipping_address', array($this, 'wcpgsk_order_formatted_shipping_address'), 99 );

			add_filter( 'woocommerce_user_column_billing_address', array($this, 'wcpgsk_order_formatted_billing_address'), 99 );
			add_filter( 'woocommerce_user_column_shipping_address', array($this, 'wcpgsk_order_formatted_shipping_address'), 99 );

			add_filter( 'woocommerce_customer_meta_fields', array($this, 'wcpgsk_customer_meta_fields'), 99 );
			//add_filter( 'woocommerce_customer_meta_fields', array($this, 'wcpgsk_customer_meta_fields'), 99 );

			add_filter( 'woocommerce_email_headers', array( $this, 'wcpgsk_email_headers' ), PHP_INT_MAX, 3 );
			
			add_action( 'wp_print_scripts', array($this, 'wcpgsk_handle_scripts'), 100 );
			add_action( 'wp_enqueue_scripts', array($this, 'wcpgsk_degenerate'), 100 );		
			
			add_filter( 'woocommerce_sale_flash', array( $this, 'wcpgsk_sale_flash' ), 10, 3 );
			add_action( 'woocommerce_proceed_to_checkout', array( $this, 'wcpgsk_after_cart' ), 10 );
			
			add_action( 'woocommerce_init', array( $this, 'wcpgsk_empty_cart' ), PHP_INT_MAX );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'wcpgsk_checkout_update_order_meta' ), 10, 2 );
			
			add_filter('woocommerce_get_price_html', array( $this, 'wcpgsk_get_price_html' ), 10, 2 );
			add_filter('woocommerce_empty_price_html', array( $this, 'wcpgsk_empty_price_html' ), PHP_INT_MAX, 2 );
			add_filter('woocommerce_free_sale_price_html', array( $this, 'wcpgsk_free_sale_price_html' ), PHP_INT_MAX, 2 );
			add_filter('woocommerce_free_price_html', array( $this, 'wcpgsk_free_price_html' ), PHP_INT_MAX, 2 );
			add_filter('woocommerce_after_my_account', array( $this, 'wcpgsk_after_my_account' ), 10 );
			
		}

		/**
		 * Process empty cart request
		 *
		 * @since 1.9.7
		 *
		 * @access public
		 */
		public function wcpgsk_empty_cart() {
			if ( isset( $_POST['wcpgsk_empty_cart'] ) ) {				
				global $woocommerce;
				$woocommerce->cart->empty_cart();
			}
		}
		
		/**
		 * Add empty cart form
		 *
		 * @since 1.9.7
		 *
		 * @access public
		 */
		public function wcpgsk_after_cart( ) {
			$options = get_option( 'wcpgsk_settings' );
			if ( isset( $options['cart']['addemptycart'] ) && $options['cart']['addemptycart'] ) :
				if ( isset( $options['process']['confirmemptycart'] ) && !empty( $options['process']['confirmemptycart'] ) ) :
					$eclabel = isset( $options['process']['emptycartlabel'] ) && !empty( $options['process']['emptycartlabel'] ) ? $options['process']['emptycartlabel'] : "Empty cart"; 
					$cclabel = isset( $options['process']['confirmemptycart'] ) && !empty( $options['process']['confirmemptycart'] ) ? $options['process']['confirmemptycart'] : "Yes, empty cart"; 
					echo '<div class="wcpgsk_empty_cart"><input type="button" class="button" style="width:100%" id="wcpgsk_confirm_empty_cart" value="' . __( $eclabel, WCPGSK_DOMAIN) . '"/></div>';
					echo '<div class="wcpgsk_empty_cart"><input type="submit" class="button" style="display:none;width:100%" id="wcpgsk_empty_cart" name="wcpgsk_empty_cart" value="' . __( $cclabel, WCPGSK_DOMAIN) . '"></div>';
				else :
					$eclabel = isset( $options['process']['emptycartlabel'] ) && !empty( $options['process']['emptycartlabel'] ) ? $options['process']['emptycartlabel'] : "Empty cart"; 
					echo '<div class="wcpgsk_empty_cart"><input type="submit" class="button" name="wcpgsk_empty_cart" value="' . __( $eclabel, WCPGSK_DOMAIN) . '"></div>';
				endif;
			endif;
		}
		
		
		
		/**
		 * Sale flash text
		 *
		 * @since 1.9.7
		 *
		 * @access public
		 * @param string $flash
		 * @param mixed $post
		 * @param mixed $product
		 * @return string
		 */
		public function wcpgsk_sale_flash( $flash, $post, $product ) {
			$options = get_option( 'wcpgsk_settings' );					
			//$lp = get_product( $product->post->ID );
			if ( $product->get_price() === '' || $product->get_price() == 0 ) :
				return '';
			endif;			
			$product_id = $product->post->ID;
			$onsale_label = get_post_meta($product_id, '_wcpgsk_onsale_html', true);
			if ( isset( $onsale_label ) && !empty( $onsale_label ) ) :
				return '<span class="onsale">' . __( $onsale_label, WCPGSK_DOMAIN ) . '</span>';
			endif;
			
			if ( isset( $options['process']['onsalelabel'] ) && !empty( $options['process']['onsalelabel'] ) ) :
				$flash = '<span class="onsale">' . __( $options['process']['onsalelabel'], WCPGSK_DOMAIN ) . '</span>';
			endif;
			return $flash;
		}
		
		/**
		 * Add additional email headers
		 *
		 * @since 1.9.7
		 *
		 * @access public
		 * @param string $headers (existing)
		 * @param string $context
		 * @param mixed $email
		 * @return string
		 */
		public function wcpgsk_email_headers( $headers = '', $context = '', $email = array() ) {
			$options = get_option( 'wcpgsk_settings' );
			if ( isset( $headers ) && !empty( $headers ) ) :
				$headers = explode( "\r\n", $headers );
			else :
				$headers = array();
			endif;
			if ( isset( $options['email']['wc_cc_email'] ) && !empty( $options['email']['wc_cc_email'] ) && is_email( $options['email']['wc_cc_email'] ) ) :
				$headers[] = "Cc: " . $options['email']['wc_cc_email'];
			endif;
			if ( isset($options['email']['wc_bcc_email']) && !empty( $options['email']['wc_bcc_email'] ) && is_email( $options['email']['wc_bcc_email'] ) ) :			
				$headers[] = "Bcc: " . $options['email']['wc_bcc_email'];			
			endif;
			
			return implode( "\r\n", $headers);
		}
		
		/**
		 * Email configuration settings
		 *
		 * @since 1.9.7
		 *
		 * @access public
		 * @param array $options 
		 */
		public function wcpgsk_settings_page_email_wcpgsk( $options ) {
			if ( isset( $options['email']['wc_cc_email'] ) && !empty( $options['email']['wc_cc_email'] ) ) :			
				$emails = explode( ',', str_replace( ';', ',', $options['email']['wc_cc_email'] ) );
				$options['email']['wc_cc_email'] = isset( $emails[0] ) ? $emails[0] : '';
			else :
				$options['email']['wc_cc_email'] = '';				
			endif;
			if ( isset($options['email']['wc_bcc_email']) && !empty( $options['email']['wc_bcc_email'] ) ) :			
				$emails = explode( ',', str_replace( ';', ',', $options['email']['wc_bcc_email'] ) );
				$options['email']['wc_bcc_email'] = isset( $emails[0] ) ? $emails[0] : '';
			else :
				$options['email']['wc_bcc_email'] = '';
			endif;
		?>
			<h3 class="wcpgsk_acc_header"><?php echo __('Email Settings',WCPGSK_DOMAIN); ?></h3>
			<div>
				<table class="widefat" border="1" >
				<thead>
					<tr>
						<th><?php _e('Settings Name', WCPGSK_DOMAIN);  ?></th>
						<th><?php _e('Data', WCPGSK_DOMAIN);  ?></th>		
						<th><?php _e('Comments', WCPGSK_DOMAIN);  ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="25%"><?php _e( 'Carbon Copy Email (CC) Recipient', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[email][wc_cc_email]" id="wcpgsk_wc_cc_email" type="email" value="<?php echo $options['email']['wc_cc_email'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Specify a valid email address that will receive copies of all WooCommerce emails.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Blind Carbon Copy (BCC) Email Recipient', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[email][wc_bcc_email]" id="wcpgsk_wc_bcc_email" type="email" value="<?php echo $options['email']['wc_bcc_email'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Specify a valid email address that will receive hidden copies of all WooCommerce emails.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr><td colspan="3"><?php _e('WooCommerce Rich Guys Swiss Knife includes more email options, e.g. context dependent cc and bcc email recipients, etc.', WCPGSK_DOMAIN); ?></td></tr>	
				</tbody>
				</table>
				<?php submit_button( __( 'Save Changes', WCPGSK_DOMAIN ) ); ?>

			</div>
		<?php
		}

		
		/**
		 * Add extended amount information
		 *
		 * @since 2.1.0
		 *
		 * @access public
		 * @output fields
		 */
		public function wcpgsk_set_price_html() {
			woocommerce_wp_text_input(array('id' => '_wcpgsk_button_label', 'label' => __('Specify button label', WCPGSK_DOMAIN) ));
			woocommerce_wp_text_input(array('id' => '_wcpgsk_extend_price', 'label' => __('Specify extended price data', WCPGSK_DOMAIN) ));
			$placements = array( 
				'after' => __( 'After price', WCPGSK_DOMAIN ),
				'before' => __( 'Before price', WCPGSK_DOMAIN ),
				'newline' => __( 'On newline', WCPGSK_DOMAIN ),
			);
			woocommerce_wp_select(array('id' => '_wcpgsk_extend_price_placement', 'label' => __('Select placement of extended price data', WCPGSK_DOMAIN), 'options' => $placements ));
			woocommerce_wp_text_input(array('id' => '_wcpgsk_onsale_html', 'label' => __('Specify on sale label', WCPGSK_DOMAIN) ));
			woocommerce_wp_text_input(array('id' => '_wcpgsk_empty_price_html', 'label' => __('Specify empty price label', WCPGSK_DOMAIN) ));
			woocommerce_wp_text_input(array('id' => '_wcpgsk_free_price_html', 'label' => __('Specify free price label', WCPGSK_DOMAIN) ));
			woocommerce_wp_text_input(array('id' => '_wcpgsk_free_sale_html', 'label' => __('Specify free sales label', WCPGSK_DOMAIN) ));
		}
		
		/**
		 * Display extended price information
		 *
		 * @since 2.1.0
		 *
		 * @access public
		 * @param string $price
		 * @param mixed $product
		 * @return string $price extended
		 */
		public function wcpgsk_get_price_html($price, $product) {
			$product_id = $product->post->ID;
			$extend_price_data = get_post_meta($product_id, '_wcpgsk_extend_price', true);
			if ( isset( $extend_price_data ) && !empty( $extend_price_data ) ) :
				$extend_price_placement = get_post_meta($product_id, '_wcpgsk_extend_price_placement', true);
				switch ( $extend_price_placement ) :
					case "before" :
						$price = '<span class="wcpgsk-extend-price-data">' . __( $extend_price_data, WCPGSK_DOMAIN ) . '</span> ' . $price;
					break;
					case "newline" :
						$price = $price . '<div class="wcpgsk-extend-price-data">' . __( $extend_price_data, WCPGSK_DOMAIN ) . '</div>';
					break;
					default :
						$price = $price . ' <span class="wcpgsk-extend-price-data">' . __( $extend_price_data, WCPGSK_DOMAIN ) . '</span>';						
					break;
				endswitch;
			endif;
			if ( $product->product_type != 'variable' && $product->product_type != 'external' && $product->managing_stock() ) :
				$hdl_backorder = $product->get_availability();
				$options = get_option( 'wcpgsk_settings' );					
				if ( isset( $hdl_backorder ) && isset( $hdl_backorder['class'] ) && $hdl_backorder['class'] == 'available-on-backorder' && isset( $options['process']['backorderlabel'] ) && !empty( $options['process']['backorderlabel'] ) ) :
					$price = $price . '<div class="wcpgsk-extend-price-data">' . __( $options['process']['backorderlabel'], WCPGSK_DOMAIN ) . '</div>';
				endif;
			endif;
			
			return $price;
		}

		/**
		 * Display empty price label if defined as label or for product
		 *
		 * @since 2.1.0
		 *
		 * @access public
		 * @param string $empty
		 * @param mixed $product
		 * @return string $price label for empty
		 */
		public function wcpgsk_empty_price_html( $empty, $product ) {
			$options = get_option( 'wcpgsk_settings' );		
			$product_id = $product->post->ID;
			$g_empty_price = isset( $options['process']['empty_price_html'] ) && !empty( $options['process']['empty_price_html'] ) ? $options['process']['empty_price_html'] : '';
			$p_empty_price = get_post_meta($product_id, '_wcpgsk_empty_price_html', true);
			if ( isset( $p_empty_price ) && !empty( $p_empty_price ) ) :
				return $p_empty_price;
			endif;
			return $g_empty_price;
		}

		/**
		 * Display free sales price label if defined for a product
		 *
		 * @since 2.1.0
		 *
		 * @access public
		 * @param string $empty
		 * @param mixed $product
		 * @return string $price label for empty
		 */
		public function wcpgsk_free_sale_price_html( $price, $product ) {
			$product_id = $product->post->ID;
			$free_sales_label = get_post_meta($product_id, '_wcpgsk_free_sale_html', true);
			if ( isset( $free_sales_label ) && !empty( $free_sales_label ) ) :
				$price = str_replace( __( 'Free!', 'woocommerce' ), __( $free_sales_label, WCPGSK_DOMAIN ), $price );
			endif;
			return $price;
		}

		/**
		 * Display free price label if defined for a product
		 *
		 * @since 2.1.0
		 *
		 * @access public
		 * @param string $empty
		 * @param mixed $product
		 * @return string $price label for empty
		 */
		public function wcpgsk_free_price_html( $price, $product ) {
			$product_id = $product->post->ID;
			$free_price_label = get_post_meta($product_id, '_wcpgsk_free_price_html', true);
			if ( isset( $free_price_label ) && !empty( $free_price_label ) ) :
				$price = __( $free_price_label, WCPGSK_DOMAIN );
			endif;
			return $price;
		}
		
		/**
		 * Label configuration settings
		 *
		 * @since 1.9.7
		 *
		 * @access public
		 * @param array $options 
		 */
		public function wcpgsk_page_labels( $options ) {
		?>
			<h3 class="wcpgsk_acc_header"><?php echo __('Label Settings',WCPGSK_DOMAIN); ?></h3>
			<div>
				<table class="widefat" border="1" >
				<thead>
					<tr>
						<th><?php _e('Settings Name', WCPGSK_DOMAIN);  ?></th>
						<th><?php _e('Data', WCPGSK_DOMAIN);  ?></th>		
						<th><?php _e('Comments', WCPGSK_DOMAIN);  ?></th>
					</tr>
				</thead>
				<tbody>
					<?php do_action( 'wcpgsk_settings_labels_before', $options ); ?>
					<tr>
						<td width="25%"><?php _e( 'On Sale Label', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[process][onsalelabel]" type="text" id="wcpgsk_onsalelabel" value="<?php echo $options['process']['onsalelabel'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the on sale label. (You may need to adapt css!)', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Backorder Label', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[process][backorderlabel]" type="text" id="wcpgsk_backorderlabel" value="<?php echo $options['process']['backorderlabel'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the backorder label. (You may need to adapt css!)', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Add to Cart Button Label', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[process][fastcheckoutbtn]" type="text" id="wcpgsk_fastcheckout_btn" value="<?php echo $options['process']['fastcheckoutbtn'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the label for the Add to Cart button.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Read more Button Label', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[process][readmorebtn]" type="text" id="wcpgsk_readmore_btn" value="<?php echo $options['process']['readmorebtn'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the label for the Read more button.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'View products Button Label', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[process][viewproductsbtn]" type="text" id="wcpgsk_viewproducts_btn" value="<?php echo $options['process']['viewproductsbtn'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the label for the View products button.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Select options Button Label', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[process][selectoptionsbtn]" type="text" id="wcpgsk_selectoptions_btn" value="<?php echo $options['process']['selectoptionsbtn'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the label for the Select options button.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Buy product Button Label', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[process][buyproductbtn]" type="text" id="wcpgsk_buyproduct_btn" value="<?php echo $options['process']['buyproductbtn'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the label for the Buy product button.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Out of stock Button Label', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[process][outofstockbtn]" type="text" id="wcpgsk_outofstock_btn" value="<?php echo $options['process']['outofstockbtn'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the label for the Out of stock button.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Tax Label', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_countries_tax_or_vat]" type="text" id="wcpgsk_woocommerce_countries_tax_or_vat" value="<?php echo $options['filters']['woocommerce_countries_tax_or_vat'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the tax label.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Includes tax message', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_countries_inc_tax_or_vat]" type="text" id="wcpgsk_woocommerce_countries_inc_tax_or_vat" value="<?php echo $options['filters']['woocommerce_countries_inc_tax_or_vat'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the tax included message.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Tax excluded message', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_countries_ex_tax_or_vat]" type="text" id="wcpgsk_woocommerce_countries_ex_tax_or_vat" value="<?php echo $options['filters']['woocommerce_countries_ex_tax_or_vat'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the tax excluded message.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Product Description Tab Label', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_product_description_tab_title]" type="text" id="wcpgsk_woocommerce_product_description_tab_title" value="<?php echo $options['filters']['woocommerce_product_description_tab_title'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the label for the product description tab.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Product Description Header', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_product_description_heading]" type="text" id="wcpgsk_woocommerce_product_description_heading" value="<?php echo $options['filters']['woocommerce_product_description_heading'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the product description header.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Product Additional Information Tab Label', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_product_additional_information_tab_title]" type="text" id="wcpgsk_woocommerce_product_additional_information_tab_title" value="<?php echo $options['filters']['woocommerce_product_additional_information_tab_title'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the label for the product additional information tab.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td width="25%"><?php _e( 'Product Additional Information Header', WCPGSK_DOMAIN ); ?></td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_product_additional_information_heading]" type="text" id="wcpgsk_woocommerce_product_additional_information_heading" value="<?php echo $options['filters']['woocommerce_product_additional_information_heading'] ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the product additional information header.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td><?php _e('Set Order Button Text', WCPGSK_DOMAIN); ?>:</td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_order_button_text]" type="text" class="wcpgsk_textfield" value="<?php if (!empty($options['filters']['woocommerce_order_button_text'])) echo esc_attr( $options['filters']['woocommerce_order_button_text'] ); ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Set the order button text.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td><?php _e('Pay Order Button Text', WCPGSK_DOMAIN); ?>:</td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_pay_order_button_text]" type="text" class="wcpgsk_textfield" value="<?php if (!empty($options['filters']['woocommerce_pay_order_button_text'])) echo esc_attr( $options['filters']['woocommerce_pay_order_button_text'] ); ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Set the pay order button text.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					
					<tr>
						<td><?php _e('Set Login required message', WCPGSK_DOMAIN); ?>:</td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_checkout_must_be_logged_in_message]" type="text" class="wcpgsk_textfield" value="<?php if (!empty($options['filters']['woocommerce_checkout_must_be_logged_in_message'])) echo esc_attr( $options['filters']['woocommerce_checkout_must_be_logged_in_message'] ); ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Change the login required message for the checkout form.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td><?php _e('Set Login possible message', WCPGSK_DOMAIN); ?>:</td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_checkout_login_message]" type="text" class="wcpgsk_textfield" value="<?php if (!empty($options['filters']['woocommerce_checkout_login_message'])) echo esc_attr( $options['filters']['woocommerce_checkout_login_message'] ); ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Change the returning customer login message for the checkout form.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td><?php _e('Set Coupon message', WCPGSK_DOMAIN); ?>:</td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_checkout_coupon_message]" type="text" class="wcpgsk_textfield" value="<?php if (!empty($options['filters']['woocommerce_checkout_coupon_message'])) echo esc_attr( $options['filters']['woocommerce_checkout_coupon_message'] ); ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Change the coupon message for the checkout form.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td><?php _e('Set Coupon link message', WCPGSK_DOMAIN); ?>:</td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_checkout_coupon_link_message]" type="text" class="wcpgsk_textfield" value="<?php if (!empty($options['filters']['woocommerce_checkout_coupon_link_message'])) echo esc_attr( $options['filters']['woocommerce_checkout_coupon_link_message'] ); ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Change the coupon link message for the checkout form. (WC 2.2+ only)', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td><?php _e('Define order received message', WCPGSK_DOMAIN); ?>:</td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_thankyou_order_received_text]" type="text" class="wcpgsk_textfield" value="<?php if (!empty($options['filters']['woocommerce_thankyou_order_received_text'])) echo esc_attr( $options['filters']['woocommerce_thankyou_order_received_text'] ); ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define the order received thank you message.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td><?php _e('Define alternative placeholder image url', WCPGSK_DOMAIN); ?>:</td>
						<td>
							<input name="wcpgsk_settings[filters][woocommerce_placeholder_img_src]" type="text" class="wcpgsk_textfield" value="<?php if (!empty($options['filters']['woocommerce_placeholder_img_src'])) echo esc_attr( $options['filters']['woocommerce_placeholder_img_src'] ); ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define alternative placeholder image url.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td><?php _e('Define empty price label', WCPGSK_DOMAIN); ?>:</td>
						<td>
							<input name="wcpgsk_settings[process][empty_price_html]" type="text" class="wcpgsk_textfield" value="<?php if (!empty($options['process']['empty_price_html'])) echo esc_attr( $options['process']['empty_price_html'] ); ?>" class="regular-text" />
						</td>
						<td>
							<span class="description"><?php _e('Define a empty price label.', WCPGSK_DOMAIN); ?></span>
						</td>
					</tr>
					<?php do_action( 'wcpgsk_settings_labels_after', $options ); ?>					
				</tbody>
				</table>
				<?php submit_button( __( 'Save Changes', WCPGSK_DOMAIN ) ); ?>

			</div>
		<?php		
		}
		
		/**
		 * Hook available and WCPGSK configured WooCommerce filters
		 *
		 * @since 1.9.2
		 */
		public function wcpgsk_hook_woocommerce_filters() {
			$options = get_option( 'wcpgsk_settings' );
			if ( !empty( $options['filters'] ) ) :
				foreach ( $options['filters'] as $filter => $value ) :
					//do it late
					add_filter( $filter, array( $this, 'wcpgsk_apply_woocommerce_filter' ), 1 );
				endforeach;
			endif;
		}
		
		
		
		/**
		 * Run available WooCommerce filters
		 *
		 * @since 1.9.2
		 * @changed 2.1.0
		 * @return configured string value
		 */
		public function wcpgsk_apply_woocommerce_filter( $filterval, $param2 = null ) {
			$options = get_option( 'wcpgsk_settings' );

			$the_filter = current_filter();
			if ( isset( $options['filters'][ $the_filter ] ) ) {
				$configval = $options['filters'][ $the_filter ];
				switch ( $the_filter ) :
					case "woocommerce_create_account_default_checked" :
						if ( $configval ) :
							$filterval = true;
						else :
							$filterval = false;
						endif;
					break;
					case "loop_shop_per_page" :
						if ( !empty( $configval ) && is_numeric( $configval ) && intval( $configval ) > 0 ) :
							$filterval = intval( $configval );
						endif;
					break;
					case "loop_shop_columns" :
						if ( !empty( $configval ) && is_numeric( $configval ) && intval( $configval ) > 0 ) :
							$filterval = intval( $configval );
						endif;
					break;
					case "woocommerce_product_thumbnails_columns" :
						if ( !empty( $configval ) && is_numeric( $configval ) && intval( $configval ) > 0 ) :
							$filterval = intval( $configval );
						endif;
					break;
					
					case "woocommerce_checkout_coupon_message" :
						$current_wc_version = get_option( 'woocommerce_db_version' );
						
						if ( version_compare( $current_wc_version, '2.2.0', '>=' ) ) :
							//make translatable
							if ( !empty( $configval ) ) :
								if ( isset( $options['filters']['woocommerce_checkout_coupon_link_message'] ) && !empty( $options['filters']['woocommerce_checkout_coupon_link_message'] ) ) :
									$filterval = __($configval, WCPGSK_DOMAIN) . ' <a href="#" class="showcoupon">' . __( $options['filters']['woocommerce_checkout_coupon_link_message'], WCPGSK_DOMAIN ) . '</a>';
								else :
									$filterval = __($configval, WCPGSK_DOMAIN) . ' <a href="#" class="showcoupon">' . __( 'Click here to enter your code', 'woocommerce' ) . '</a>';								
								endif;
							endif;
						else :
							//make translatable
							if ( !empty( $configval ) ) :
								$filterval = __($configval, WCPGSK_DOMAIN);
							endif;
						endif;
						
					break;
					case "woocommerce_product_description_heading" :
						$filterval = __($configval, WCPGSK_DOMAIN);
					break;
					case "woocommerce_product_additional_information_heading" :
						$filterval = __($configval, WCPGSK_DOMAIN);
					break;
					default :
						//make translatable
						if ( !empty( $configval ) ) :
							$filterval = __($configval, WCPGSK_DOMAIN);
						endif;
					break;
				endswitch;
			}			
			return $filterval;
		}
		
		
		/**
		 * Register Product Tab for Min/Max/Step Configuration for Simple and Variable products
		 *
		 * @access public
		 * @since 1.6.0
		 * @echo html
		 */
		public function wcpgsk_product_write_panel_tab() {
			global $post;
			$options = get_option( 'wcpgsk_settings' );
			if ( isset($options['cart']['minmaxstepproduct']) && $options['cart']['minmaxstepproduct'] == 1 ) :			
				if ( $terms = wp_get_object_terms( $post->ID, 'product_type' ) )
					$product_type = sanitize_title( current( $terms )->name );
				else
					$product_type = 'simple';			
				if ( $product_type === 'simple' || $product_type === 'variable' ) :
					echo "<li class=\"wcpgsk_product_tab\"><a class=\"icon16 icon-media\" href=\"#wcpgsk_data_tab\">" . __( 'Cart Quantities', WCPGSK_DOMAIN ) . "</a></li>";
				endif;
			endif;
		}
		
		/**
		 * Save checkout js
		 *
		 * 
		 * @access public
		 * @since 1.8.1
		 * @return nothing 
		 */
		public function wcpgsk_save_checkoutjs_callback() {
			global $wpdb;
			$data = $_POST['checkoutjs'];//json_decode(str_replace('\\"','"', $_POST['jsondata']), true);
			
			$checkoutjs = $_POST['checkoutjs'];
			if ( isset($checkoutjs) ) :
				update_option('wcpgsk_checkoutjs', stripslashes($checkoutjs));
				echo __('js saved', WCPGSK_DOMAIN);
			endif;
			$this->phpdie();

		}
		
		/**
		 * Helper function: mimics 2.1.x for 2.0.x installations and calls +2.1 function directly if available
		 *
		 * 
		 * @access public
		 * @since 1.7.1
		 * @return array of localizable fields 
		 */
		public function wcpgsk_get_country_locale_field_selectors() {
			if ( function_exists('WC') ) :
				return WC()->countries->get_country_locale_field_selectors();
			else :
				//we need this for internal usage... on the frontend fields are hardcode in WC < 2.1 into js
				$locale_fields = array (
					'address_1'	=> '#billing_address_1_field, #shipping_address_1_field',
					'address_2'	=> '#billing_address_2_field, #shipping_address_2_field',
					'state'		=> '#billing_state_field, #shipping_state_field',
					'postcode'	=> '#billing_postcode_field, #shipping_postcode_field',
					'city'		=> '#billing_city_field, #shipping_city_field'
				);
				//@TODO: Do we really want to filter in WC < 2.1?
				return apply_filters( 'woocommerce_country_locale_field_selectors', $locale_fields );
			endif;
		}

		/**
		 * Helper function: not really necessary...
		 *
		 * @access public
		 * @since 1.7.1
		 * @return array localization config
		 */
		public function wcpgsk_get_country_locale() {
			if ( function_exists('WC') ) :
				return WC()->countries->get_country_locale();
			else :
				global $woocommerce;
				return $woocommerce->countries->get_country_locale();
			endif;
		}
		
		/**
		 * Helper function: not really necessary...
		 *
		 * @access public
		 * @since 1.7.1
		 * @return array of allowed countries
		 */
		public function wcpgsk_get_allowed_countries() {
			if ( function_exists('WC') ) :
				return WC()->countries->get_allowed_countries();
			else :
				global $woocommerce;
				return $woocommerce->countries->get_allowed_countries();
			endif;
		}
		
		/**
		 * Locale field form
		 *
		 * @access public
		 * @since 1.7.1
		 * @echo html
		 */		 
		public function get_locale_field_form_callback() {
			$jsfields = $this->wcpgsk_get_country_locale_field_selectors(); //WC()->countries->get_country_locale_field_selectors();
			$localedata = $this->wcpgsk_get_country_locale(); //WC()->countries->get_country_locale();
			$locale = get_option('wcpgsk_locale');
			$localeCode = $_POST['localeCode'];
			
			if ( $localeCode ) :
				?>
				<tr>
				<td colspan="3">
				<input name="wcpgsk_settings[locale][countrycode]" type="hidden" value="<?php echo $localeCode; ?>" />
				</td>
				</tr>
				<?php
				$localepostcode = isset($localedata[$localeCode]) && isset($localedata[$localeCode]['postcode_before_city']) ? ( $localedata[$localeCode]['postcode_before_city'] ? '1' : '0' ) : ( isset($localedata['default']['postcode_before_city']) && $localedata['default']['postcode_before_city'] ? '1' : '0' );
				$locale[$localeCode]['postcode_before_city'] = isset($locale[$localeCode]['postcode_before_city']) ? $locale[$localeCode]['postcode_before_city'] : $localepostcode;
				foreach ($jsfields as $field => $fieldids) :
					$localelabel = isset($localedata[$localeCode]) && isset($localedata[$localeCode][$field]) && isset($localedata[$localeCode][$field]['label']) ? $localedata[$localeCode][$field]['label'] : ( isset($localedata['default'][$field]['label']) ? $localedata['default'][$field]['label'] : '' );
					$locale[$localeCode]['label_' . $field] = isset($locale[$localeCode]['label_' . $field]) ? $locale[$localeCode]['label_' . $field] : $localelabel;

					$localeplaceholder = isset($localedata[$localeCode]) && isset($localedata[$localeCode][$field]) && isset($localedata[$localeCode][$field]['placeholder']) ? $localedata[$localeCode][$field]['placeholder'] : ( isset($localedata['default'][$field]['placeholder']) ? $localedata['default'][$field]['placeholder'] : '' );
					$locale[$localeCode]['placeholder_' . $field] = isset($locale[$localeCode]['placeholder_' . $field]) ? $locale[$localeCode]['placeholder_' . $field] : $localeplaceholder;

					$localerequired = isset($localedata[$localeCode]) && isset($localedata[$localeCode][$field]) && isset($localedata[$localeCode][$field]['required']) ? ( $localedata[$localeCode][$field]['required'] ? '1' : '0' ) : ( isset($localedata['default'][$field]['required']) && $localedata['default'][$field]['required'] ? '1' : '0' );
					$locale[$localeCode]['required_' . $field] = isset($locale[$localeCode]['required_' . $field]) ? $locale[$localeCode]['required_' . $field] : $localerequired;
					if ( $field !== 'state' ) :
						$localehidden = isset($localedata[$localeCode]) && isset($localedata[$localeCode][$field]) && isset($localedata[$localeCode][$field]['hidden']) ? ( $localedata[$localeCode][$field]['hidden'] ? '1' : '0' ) : ( isset($localedata['default'][$field]['hidden']) && $localedata['default'][$field]['hidden'] ? '1' : '0' );
						$locale[$localeCode]['hidden_' . $field] = isset($locale[$localeCode]['hidden_' . $field]) ? $locale[$localeCode]['hidden_' . $field] : $localehidden;
					endif;

					
				?>
				<tr>
					<td><?php echo $field; ?></td>
					<td>
						<input name="wcpgsk_settings[locale][required_<?php echo $field; ?>]" type="hidden" value="0" />
						<input name="wcpgsk_settings[locale][required_<?php echo $field; ?>]" type="checkbox" value="1" <?php if ( isset($locale[$localeCode]['required_' . $field]) && 1 == ($locale[$localeCode]['required_' . $field])) echo "checked='checked'"; ?> />
					</td>
					<td>
						<span class="description"><?php echo __('Required status for ', WCPGSK_DOMAIN) . ' ' . $field; ?></span>
					</td>
				</tr>
				<?php if ( $field != 'state' ) : ?>
				<tr>
					<td><?php echo $field; ?></td>
					<td>
						<input name="wcpgsk_settings[locale][hidden_<?php echo $field; ?>]" type="hidden" value="0" />
						<input name="wcpgsk_settings[locale][hidden_<?php echo $field; ?>]" type="checkbox" value="1" <?php if ( isset($locale[$localeCode]['hidden_' . $field]) && 1 == ($locale[$localeCode]['hidden_' . $field])) echo "checked='checked'"; ?> />
					</td>
					<td>
						<span class="description"><?php echo __('Hidden status for ', WCPGSK_DOMAIN) . ' ' . $field; ?></span>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<td><?php echo $field; ?></td>
					<td>
						<input name="wcpgsk_settings[locale][label_<?php echo $field; ?>]" value="<?php if (isset($locale[$localeCode]['label_' . $field]) ) echo $locale[$localeCode]['label_' . $field]; ?>" class="regular-text" />
					</td>
					<td>
						<span class="description"><?php echo __('Label for ', WCPGSK_DOMAIN) . ' ' . $field; ?></span>
					</td>
				</tr>
				<tr>
					<td><?php echo $field; ?></td>
					<td>
						<input name="wcpgsk_settings[locale][placeholder_<?php echo $field; ?>]" value="<?php if (isset($locale[$localeCode]['placeholder_' . $field]) ) echo $locale[$localeCode]['placeholder_' . $field]; ?>" class="regular-text" />
					</td>
					<td>
						<span class="description"><?php echo __('Placeholder for ', WCPGSK_DOMAIN) . ' ' . $field; ?></span>
					</td>
				</tr>
				<tr>
					<td colspan="3"><hr /></td>
				</tr>
				<?php
				endforeach;
				?>
				<tr>
					<td><?php echo __('Post/Zip Code Placement', WCPGSK_DOMAIN); ?></td>
					<td>
						<input name="wcpgsk_settings[locale][postcode_before_city]" type="hidden" value="0" />
						<input name="wcpgsk_settings[locale][postcode_before_city]" type="checkbox" value="1" <?php if ( isset($locale[$localeCode]['postcode_before_city']) && 1 == ($locale[$localeCode]['postcode_before_city'])) echo "checked='checked'"; ?> />
					</td>
					<td>
						<span class="description"><?php echo __('Post/Zip code before city', WCPGSK_DOMAIN); ?></span>
					</td>
				</tr>
				
				<?php
			endif;
		}
		
		/**
		 * Product Tab Content for Min/Max/Step Configuration for Simple and Variable products
		 *
		 * @access public
		 * @since 1.6.0
		 * @echo html
		 */
		public function wcpgsk_product_write_panels() {
			global $post;
			$options = get_option( 'wcpgsk_settings' );
			if ( isset($options['cart']['minmaxstepproduct']) && $options['cart']['minmaxstepproduct'] == 1 ) :
			
				// the product
				if ( $terms = wp_get_object_terms( $post->ID, 'product_type' ) )
					$product_type = sanitize_title( current( $terms )->name );
				else
					$product_type = 'simple';
				if ( $product_type === 'simple' || $product_type === 'variable' ) :	
					/*
					$minqty   = get_post_meta( $post->ID, '_wcpgsk_minqty',  true );
					$maxqty   = get_post_meta( $post->ID, '_wcpgsk_maxqty',  true );
					$stepqty  = get_post_meta( $post->ID, '_wcpgsk_stepqty',     true );
					$minqty = isset($minqty) && !empty($minqty) ? $minqty : 0;
					$maxqty = isset($maxqty) && !empty($maxqty) ? $maxqty : 0;
					$stepqty = isset($stepqty) && !empty($stepqty) ? $stepqty : 0;
					*/
					$style = 'min-width:140px;padding:10px 10px 10px 10px;display:block;font-size:inherit !important;font-family:inherit !important;font-face:inherit !important;line-height: 18px !important;';
					$style_before = 'height:auto !important;vertical-align:sub;padding: 0 3px 0 0 !important;font:400 20px/1 dashicons !important;line-height: 18px !important;content:"\f163";';
					$active_style = '';					
					?>
					<style type="text/css">
						#woocommerce-product-data ul.product_data_tabs li.wcpgsk_product_tab a { <?php echo $style; ?> }
						#woocommerce-product-data ul.product_data_tabs li.wcpgsk_product_tab a:hover { <?php echo $style; ?> }
						#woocommerce-product-data ul.product_data_tabs li.wcpgsk_product_tab a:before { <?php echo $style_before; ?> }
						<?php echo $active_style; ?>
					</style>
					<div id="wcpgsk_data_tab" class="panel wc-metaboxes-wrapper woocommerce_options_panel">
					<?php
						do_action( 'wcpgsk_qty_panel_settings' );
						woocommerce_wp_checkbox( array( 'id' => '_wcpgsk_selectqty', 'label' => __( 'Quantity Selector', WCPGSK_DOMAIN ), 'description' => __( 'Convert quantity input to selector based on quantity configuration.', WCPGSK_DOMAIN ) ) );
					
						woocommerce_wp_text_input( array( 'id' => '_wcpgsk_minqty', 'type' => 'numeric', 'label' => __( 'Minimum Quantity', WCPGSK_DOMAIN ), 'desc_tip' => true, 'description' => __( 'Please specify an integer value. 0 deactivates the option.', WCPGSK_DOMAIN ), 'custom_attributes' => array('style' => 'width:40%' ) ) );
						woocommerce_wp_text_input( array( 'id' => '_wcpgsk_maxqty', 'type' => 'numeric', 'label' => __( 'Maximum Quantity', WCPGSK_DOMAIN ), 'desc_tip' => true, 'description' => __( 'Please specify an integer value. Should be equal or higher than minimum value or 0. 0 deactivates the option.', WCPGSK_DOMAIN ), 'custom_attributes' => array('style' => 'width:40%' ) ) );
						woocommerce_wp_text_input( array( 'id' => '_wcpgsk_stepqty', 'type' => 'numeric', 'label' => __( 'Increment Quantity', WCPGSK_DOMAIN ), 'desc_tip' => true, 'description' => __( 'Please specify an integer value for increment steps. Please assure consistency with minimum and maximum value. 0 deactivates the option.', WCPGSK_DOMAIN ), 'custom_attributes' => array('style' => 'width:40%' ) ) );
					?>
					</div>
					<?php
				endif;
			endif;
		}

		/**
		 * Store WCPGSK Min/Max/Step configuration data for the product
		 *
		 * @access public
		 * @since 1.6.0
		 * @return void
		 */
		public function wcpgsk_process_product_meta( $post_id ) {
			global $woocommerce, $wpdb;
			$options = get_option( 'wcpgsk_settings' );
			if ( isset($options['cart']['minmaxstepproduct']) && $options['cart']['minmaxstepproduct'] == 1 ) :
			
				if (WP_DEBUG) {
					//trigger_error(sprintf(__("The 'label' and 'id' values of the 'args' parameter of '%s::%s()' are required", WCPGSK_DOMAIN), $_POST['_wcpgsk_minqty'], $post_id));
					//die();
				}
				$minqty = 0;
				$maxqty = 0;
				$stepqty = 0;
				if ( isset( $_POST['_wcpgsk_minqty'] ) && is_numeric($_POST['_wcpgsk_minqty']) ) :
					$minqty = intval($_POST['_wcpgsk_minqty']) ;
				endif;
				if ( isset( $_POST['_wcpgsk_maxqty'] ) && is_numeric($_POST['_wcpgsk_maxqty']) ) :
					$maxqty = intval($_POST['_wcpgsk_maxqty']) ;
				endif;
				if ( isset( $_POST['_wcpgsk_stepqty'] ) && is_numeric($_POST['_wcpgsk_stepqty']) ) :
					$stepqty = intval($_POST['_wcpgsk_stepqty']) ;
				endif;
				
				if ($minqty > $maxqty && $maxqty > 0) $minqty = 1;
				if ($maxqty < $minqty) $maxqty = 0;
				do_action( 'wcpgsk_process_additional_product_meta', $post_id );
				if ( isset( $_POST['_wcpgsk_selectqty'] ) && $_POST['_wcpgsk_selectqty'] == 'yes' ) :
					update_post_meta( $post_id, '_wcpgsk_selectqty', 'yes' );
				else :
					update_post_meta( $post_id, '_wcpgsk_selectqty', 0 );
				endif;
				update_post_meta( $post_id, '_wcpgsk_minqty', $minqty );
				update_post_meta( $post_id, '_wcpgsk_maxqty', $maxqty );
				update_post_meta( $post_id, '_wcpgsk_stepqty', $stepqty );
			endif;
			if ( isset( $_POST['_wcpgsk_button_label'] ) && $_POST['_wcpgsk_button_label'] ) :
				update_post_meta( $post_id, '_wcpgsk_button_label', wp_kses_post( $_POST['_wcpgsk_button_label'] ) );
			else :
				update_post_meta( $post_id, '_wcpgsk_button_label', '' );
			endif;
			if ( isset( $_POST['_wcpgsk_extend_price'] ) && $_POST['_wcpgsk_extend_price'] ) :
				update_post_meta( $post_id, '_wcpgsk_extend_price', wp_kses_post( $_POST['_wcpgsk_extend_price'] ) );
				update_post_meta( $post_id, '_wcpgsk_extend_price_placement', $_POST['_wcpgsk_extend_price_placement'] );			
			else :
				update_post_meta( $post_id, '_wcpgsk_extend_price', '' );
				update_post_meta( $post_id, '_wcpgsk_extend_price_placement', 'after' );				
			endif;
			if ( isset( $_POST['_wcpgsk_empty_price_html'] ) && $_POST['_wcpgsk_empty_price_html'] ) :
				update_post_meta( $post_id, '_wcpgsk_empty_price_html', wp_kses_post( $_POST['_wcpgsk_empty_price_html'] ) );			
			else :
				update_post_meta( $post_id, '_wcpgsk_empty_price_html', '' );			
			endif;
			if ( isset( $_POST['_wcpgsk_free_sale_html'] ) && $_POST['_wcpgsk_free_sale_html'] ) :
				update_post_meta( $post_id, '_wcpgsk_free_sale_html', wp_kses_post( $_POST['_wcpgsk_free_sale_html'] ) );			
			else :
				update_post_meta( $post_id, '_wcpgsk_free_sale_html', '' );			
			endif;
			if ( isset( $_POST['_wcpgsk_free_price_html'] ) && $_POST['_wcpgsk_free_price_html'] ) :
				update_post_meta( $post_id, '_wcpgsk_free_price_html', wp_kses_post( $_POST['_wcpgsk_free_price_html'] ) );			
			else :
				update_post_meta( $post_id, '_wcpgsk_free_price_html', '' );			
			endif;
			if ( isset( $_POST['_wcpgsk_onsale_html'] ) && $_POST['_wcpgsk_onsale_html'] ) :
				update_post_meta( $post_id, '_wcpgsk_onsale_html', wp_kses_post( $_POST['_wcpgsk_onsale_html'] ) );			
			else :
				update_post_meta( $post_id, '_wcpgsk_onsale_html', '' );			
			endif;
			
		}

		/**
		 * Check for minimum and maximum items in cart and if they fulfill the settings.
		 * and raise error and message if rules are not fulfilled, otherwise clear messages
		 *
		 * @access public
		 * @param mixed $checkout
		 * @since 1.1.0
		 * @return void
		 */		
		public function wcpgsk_checkout_init($checkout) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			
			$cartItems = sizeof( $woocommerce->cart->cart_contents );
			$allowed = $options['cart']['maxitemscart'];
			
			//check cart items count and diminish if more than one variation for a product exists
			if ($allowed > 0 && isset($options['cart']['variationscountasproduct']) && $options['cart']['variationscountasproduct'] == 0) {	
				$varproducts = array();
				foreach($woocommerce->cart->cart_contents as $i => $values) {
					$key = $values['product_id'];
					if (isset($values[$key]) && isset($values[$variation_id]) && $values[$key] != $values['variation_id']) {
						if (isset($varproducts[$key])) $varproducts[$key] = 1;
						else $varproducts[$key] = 0;
					}
				}
				if (!empty($varproducts)) $cartItems = $cartItems - array_sum($varproducts);
			}
			
			if ($allowed > 0 && $cartItems > $allowed ) :
				wcpgsk_clear_messages();
				// Sets error message.
				wcpgsk_add_error( sprintf( __( 'You have reached the maximum amount of %s items allowed for your cart!', WCPGSK_DOMAIN ), $allowed ) );
				wcpgsk_set_messages();
				$cart_url = $woocommerce->cart->get_cart_url();
				wcpgsk_add_message( __('Remove products from the cart', WCPGSK_DOMAIN) . ': <a href="' . $cart_url . '">' . __('Cart', WCPGSK_DOMAIN) . '</a>');
				wcpgsk_set_messages();
				//wp_redirect( get_permalink( woocommerce_get_page_id( 'cart' ) ) );
				//exit;				
			else :
				$allowed = $options['cart']['minitemscart'];

				//check cart items count and diminish if more than one variation for a product exists
				if ($allowed > 1 && isset($options['cart']['variationscountasproduct']) && $options['cart']['variationscountasproduct'] == 0) {	
					$varproducts = array();
					foreach($woocommerce->cart->cart_contents as $i => $values) {
						$key = $values['product_id'];
						if (isset($values[$key]) && isset($values['variation_id']) && $values[$key] != $values['variation_id']) {
							if (isset($varproducts[$key])) $varproducts[$key] = 1;
							else $varproducts[$key] = 0;
						}
					}
					if (!empty($varproducts)) $cartItems = $cartItems - array_sum($varproducts);
				}
				
				if ($allowed > 1 && $allowed > $cartItems ) :
					// Sets error message.
					wcpgsk_clear_messages();

					wcpgsk_add_error( sprintf( __( 'You still have not reached the minimum amount of %s items required for your cart!', WCPGSK_DOMAIN ), $allowed )  );
					wcpgsk_set_messages();
					$valid = false;
					
					$shop_page_id = woocommerce_get_page_id( 'shop' );
					//$shop_page_url = get_permalink(icl_object_id($shop_page_id, 'page', false));
					$shop_page_url = get_permalink($shop_page_id);
					wcpgsk_add_message( __('Select more products from the shop', WCPGSK_DOMAIN) . ': <a href="' . $shop_page_url . '">' . __('Shop', WCPGSK_DOMAIN) . '</a>');
					wcpgsk_set_messages();
					//wp_redirect( get_permalink( woocommerce_get_page_id( 'shop' ) ) );
					//exit;								
				else :
					wcpgsk_clear_messages();			
				endif;
			endif;
		}
		
		/**
		 * Register settings.
		 * Establish settings if no default settings are available for some resason
		 *
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */		
		public function wcpgsk_register_setting() {	
			register_setting( 'wcpgsk_options', 'wcpgsk_settings', array($this, 'wcpgsk_options_validate') );
			//check if we have initial settings, if not store default settings
			global $wcpgsk_options;
			$this->register_plugin_version();
			$wcpgsk_options = get_option( 'wcpgsk_settings' );
			if ( empty($wcpgsk_options) ) :
				$this->wcpgsk_initial_settings();
			endif;
		}

		public function wcpgsk_load_order_data($meta) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			$metas = array();
			$checkout_fields = array_merge($woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'billing_' ), $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'shipping_' ));
			//$meta = array_merge($meta, $checkout_fields);
			foreach ($checkout_fields as $key => $field) : 
				if ( isset($options['woofields']['billing'][$key]['custom_' . $key]) && $options['woofields']['billing'][$key]['custom_' . $key] && !isset($meta[$key]) ) :
					$meta[$key] = '';
				elseif ( isset($options['woofields']['shipping'][$key]['custom_' . $key]) && $options['woofields']['shipping'][$key]['custom_' . $key] && !isset($meta[$key]) ) : 
					$meta[$key] = '';
				endif;				
			endforeach;

			return apply_filters( 'wcpgsk_load_order_data', $meta );	
		}
		
		/**
		 * Display billing and shipping form data captured.
		 *
		 * @access public
		 * @since 1.3.0
		 * @output html
		 */		
		public function wcpgsk_email_after_order_table($order) {
			if ( !isset( $order->hidecustomproductemail ) || $order->hidecustomproductemail != 'yes' ) :
				global $woocommerce;
				$options = get_option( 'wcpgsk_settings' );
				$billing_fields = $this->wcpgsk_additional_data($order, 'billing');
				$shipping_fields = $this->wcpgsk_additional_data($order, 'shipping');
				?>
				<table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0" class="wcpgsk_customer_data">

					<tr>
						<?php 
						if ( isset($billing_fields) && !empty($billing_fields) ) : 
						?>
						<td valign="top" width="50%" class="billing_data">

							<?php if ( !empty($options['checkoutform']['morebillingtitle']) ) : ?>
							<h3><?php _e( $options['checkoutform']['morebillingtitle'], 'woocommerce' ); ?></h3>
							<?php endif; ?>

							<dl>
							<?php 
								foreach ($billing_fields as $key => $field) :
									$key_type = "billing_" . $key;
									$label = !empty($field['label']) ? $field['label'] . ": " : "";
									if ( is_array( $field['captured'] ) ) :
										echo '<dt>' . $label . '</dt><dd>' . wp_kses_post( make_clickable( implode( '<br />', $field['captured'] ) ) ) . '</dd>';
									else :
										echo '<dt>' . $label . '</dt><dd>' . wp_kses_post( make_clickable( $field['captured'] ) ) . '</dd>';
									endif;
								endforeach;
							?>
							</dl>

						</td>

						<?php 
						endif;
						if ( get_option( 'woocommerce_ship_to_billing_address_only' ) == 'no' && isset($shipping_fields) && !empty($shipping_fields) ) : 
						?>

						<td valign="top" width="50%" class="shipping_data">
							<?php if ( !empty($options['checkoutform']['moreshippingtitle']) ) : ?>
							<h3><?php _e( $options['checkoutform']['moreshippingtitle'], 'woocommerce' ); ?></h3>
							<?php endif; ?>

							<dl>
							<?php 
								foreach ($shipping_fields as $key => $field) :
									$key_type = "shipping_" . $key;
									$label = !empty($field['label']) ? $field['label'] . ": " : "";
									if ( is_array( $field['captured'] ) ) :
										echo '<dt>' . $label . '</dt><dd>' . wp_kses_post( make_clickable( implode( '<br />', $field['captured'] ) ) ) . '</dd>';
									else :
										echo '<dt>' . $label . '</dt><dd>' . wp_kses_post( make_clickable( $field['captured'] ) ) . '</dd>';
									endif;
								endforeach;
							?>
							</dl>

						</td>

						<?php endif; ?>

					</tr>

				</table>
				<?php
			endif;
		}
		
		/**
		 * Display billing and shipping form data captured.
		 *
		 * @access public
		 * @since 1.3.0
		 * @output html
		 */		
		public function wcpgsk_order_details_after_order_table($order) {
			$options = get_option( 'wcpgsk_settings' );
			$billing_fields = $this->wcpgsk_additional_data($order, 'billing');
			$shipping_fields = $this->wcpgsk_additional_data($order, 'shipping');
			?>
			<table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">
				<tr>
					<?php 
					if ( isset($billing_fields) && !empty($billing_fields) ) : 
					?>
					<td valign="top" width="50%" class="billing_data">

						<?php if ( !empty($options['checkoutform']['morebillingtitle']) ) : ?>
						<h3><?php _e( $options['checkoutform']['morebillingtitle'], 'woocommerce' ); ?></h3>
						<?php endif; ?>

						<dl>
						<?php 
							//$billing_fields = $this->wcpgsk_additional_data($order, 'billing');
							//if ( isset($billing_fields) && !empty($billing_fields) ) :
							foreach ($billing_fields as $key => $field) :
								$key_type = "billing_" . $key;
								$label = !empty($field['label']) ? $field['label'] . ": " : "";
								if ( is_array( $field['captured'] ) ) :
									echo '<dt>' . $label . '</dt><dd>' . wp_kses_post( make_clickable( implode( '<br />', $field['captured'] ) ) ) . '</dd>';
								else :
									echo '<dt>' . $label . '</dt><dd>' . wp_kses_post( make_clickable( $field['captured'] ) ) . '</dd>';
								endif;
							endforeach;
							//endif;
						?>
						</dl>

					</td>

					<?php 
					endif;
					if ( get_option( 'woocommerce_ship_to_billing_address_only' ) == 'no' && isset($shipping_fields) && !empty($shipping_fields) ) : 
					?>

					<td valign="top" width="50%" class="shipping_data">
						<?php if ( !empty($options['checkoutform']['moreshippingtitle']) ) : ?>
						<h3><?php _e( $options['checkoutform']['moreshippingtitle'], 'woocommerce' ); ?></h3>
						<?php endif; ?>
						<dl>
						<?php 
							foreach ($shipping_fields as $key => $field) :
								$key_type = "shipping_" . $key;
								$label = !empty($field['label']) ? $field['label'] . ": " : "";
								if ( is_array( $field['captured'] ) ) :
									echo '<dt>' . $label . '</dt><dd>' . wp_kses_post( make_clickable( implode( '<br />', $field['captured'] ) ) ) . '</dd>';
								else :
									echo '<dt>' . $label . '</dt><dd>' . wp_kses_post( make_clickable( $field['captured'] ) ) . '</dd>';
								endif;
							endforeach;
						?>
						</dl>

					</td>

					<?php endif; ?>

				</tr>

			</table>
			<?php
		}
		
		/**
		 * Display billing and shipping custom fields for user meta on my-account page.
		 *
		 * @access public
		 * @since 2.1.2
		 * @output html
		 */		
		public function wcpgsk_after_my_account() {
			$options = get_option( 'wcpgsk_settings' );
			$billing_fields = $this->wcpgsk_additional_data_user('billing');
			$shipping_fields = $this->wcpgsk_additional_data_user('shipping');
			$col = 1;
			
			if ( get_option( 'woocommerce_ship_to_billing_address_only' ) == 'no' && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) echo '<div class="col2-set addresses">';			
			?>
				<div class="col-<?php echo ( ( $col = $col * -1 ) < 0 ) ? 1 : 2; ?> address billing_data">
		
				<?php 
				if ( isset($billing_fields) && !empty($billing_fields) ) : 
				?>

					<?php if ( !empty($options['checkoutform']['morebillingtitle']) ) : ?>
					<h3><?php _e( $options['checkoutform']['morebillingtitle'], 'woocommerce' ); ?></h3>
					<?php endif; ?>

					<dl>
					<?php 
						//$billing_fields = $this->wcpgsk_additional_data($order, 'billing');
						//if ( isset($billing_fields) && !empty($billing_fields) ) :
						foreach ($billing_fields as $key => $field) :
							$key_type = "billing_" . $key;
							$label = !empty($field['label']) ? $field['label'] . ": " : "";
							if ( is_array( $field['captured'] ) ) :
								echo '<dt>' . $label . '</dt><dd>' . wp_kses_post( make_clickable( implode( '<br />', $field['captured'] ) ) ) . '</dd>';
							else :
								echo '<dt>' . $label . '</dt><dd>' . wp_kses_post( make_clickable( $field['captured'] ) ) . '</dd>';
							endif;
						endforeach;
						//endif;
					?>
					</dl>
				</div>
				<?php 
				endif;
				if ( get_option( 'woocommerce_ship_to_billing_address_only' ) == 'no' && isset($shipping_fields) && !empty($shipping_fields) ) : 
				
				?>
					<div class="col-<?php echo ( ( $col = $col * -1 ) < 0 ) ? 1 : 2; ?> address shipping_data">
					<?php if ( !empty($options['checkoutform']['moreshippingtitle']) ) : ?>
					<h3><?php _e( $options['checkoutform']['moreshippingtitle'], 'woocommerce' ); ?></h3>
					<?php endif; ?>
					<dl>
					<?php 
						foreach ($shipping_fields as $key => $field) :
							$key_type = "shipping_" . $key;
							$label = !empty($field['label']) ? $field['label'] . ": " : "";
							if ( is_array( $field['captured'] ) ) :
								echo '<dt>' . $label . '</dt><dd>' . wp_kses_post( make_clickable( implode( '<br />', $field['captured'] ) ) ) . '</dd>';
							else :
								echo '<dt>' . $label . '</dt><dd>' . wp_kses_post( make_clickable( $field['captured'] ) ) . '</dd>';
							endif;
						endforeach;
					?>
					</dl>

					</div>

				<?php endif; ?>

			<?php
			if ( get_option( 'woocommerce_ship_to_billing_address_only' ) == 'no' && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) echo '</div>';
			
		}
		
		/**
		 * Update our order billing address.
		 *
		 * @access public
		 * @since 1.3.0
		 * @output Settings page
		 */		
		public function wcpgsk_additional_data($order, $fortype) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			$captured_fields = array();
			$field_order = 1;	
			$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), $fortype . '_' );
			foreach ($checkout_fields as $key => $field) :
				$checkout_fields[$key]['order'] = ((!empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);
				$checkout_fields[$key]['required'] = ((isset($options['woofields']['required_' . $key])) ? $options['woofields']['required_' . $key] : $checkout_fields[$key]['required']);
				$checkout_fields[$key]['hideorder'] = ((isset($options['woofields']['hideorder_' . $key])) ? $options['woofields']['hideorder_' . $key] : 0);
				if (!isset($configField['label'])) $configField['label'] = __($checkout_fields[$key]['label'], WCPGSK_DOMAIN);				
				$field_order++;
			endforeach;

			uasort($checkout_fields, array($this, "compareFieldOrder"));						

			foreach ($checkout_fields as $key => $field) : 
				//$fieldLabel = $field['displaylabel'];
				$fieldkey = str_replace('billing_', '', $key);
				$fieldkey = str_replace('shipping_', '', $key);
				
				if ($key != 'billing_email_validator' && $field['hideorder'] == 0 && $key != 'billing_phone' && $key != 'billing_email' && !$field['hideorder'] ) :
					if ( isset($options['woofields'][$fortype][$key]['custom_' . $key]) && $options['woofields'][$fortype][$key]['custom_' . $key]) :
						$configField['label'] = isset($checkout_fields[$key]['label']) && !empty($checkout_fields[$key]['label']) ? __($checkout_fields[$key]['label'], WCPGSK_DOMAIN) : "";
						$captured_value = $order->$key;
						if ( isset($options['woofields']['settings_' . $key]) ) :
							$params = $this->explodeParameters($options['woofields']['settings_' . $key]);
							if ( isset($params) && isset($params['validate']) && !empty($params['validate']) && $params['validate'] == 'password' ) :
								$captured_value = '*******';
							endif;
						endif;
						$configField['captured'] = $captured_value;
						$captured_fields[$fieldkey] = $configField;
					endif;
				endif;
			endforeach;
			return $captured_fields;	
		}

		/**
		 * Get user meta data for billing or shipping.
		 *
		 * @access public
		 * @since 2.1.2
		 * @output Settings page
		 */		
		public function wcpgsk_additional_data_user($fortype) {
			global $woocommerce;
			$customer_id = get_current_user_id();
			$options = get_option( 'wcpgsk_settings' );
			$captured_fields = array();
			$field_order = 1;	
			$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), $fortype . '_' );
			foreach ($checkout_fields as $key => $field) :
				$checkout_fields[$key]['order'] = ((!empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);
				$checkout_fields[$key]['required'] = ((isset($options['woofields']['required_' . $key])) ? $options['woofields']['required_' . $key] : $checkout_fields[$key]['required']);
				$checkout_fields[$key]['hideorder'] = ((isset($options['woofields']['hideorder_' . $key])) ? $options['woofields']['hideorder_' . $key] : 0);
				if (!isset($configField['label'])) $configField['label'] = __($checkout_fields[$key]['label'], WCPGSK_DOMAIN);				
				$field_order++;
			endforeach;

			uasort($checkout_fields, array($this, "compareFieldOrder"));						

			foreach ($checkout_fields as $key => $field) : 
				//$fieldLabel = $field['displaylabel'];
				$fieldkey = str_replace('billing_', '', $key);
				$fieldkey = str_replace('shipping_', '', $key);
				if ($key != 'billing_email_validator' && $key != 'billing_phone' && $key != 'billing_email' ) :
					if ( isset($options['woofields'][$fortype][$key]['custom_' . $key]) && $options['woofields'][$fortype][$key]['custom_' . $key]) :
						$configField['label'] = isset($checkout_fields[$key]['label']) && !empty($checkout_fields[$key]['label']) ? __($checkout_fields[$key]['label'], WCPGSK_DOMAIN) : "";
						$captured_value = get_user_meta( $customer_id, $key, true );//$order->$key;
						if ( isset($options['woofields']['settings_' . $key]) ) :
							$params = $this->explodeParameters($options['woofields']['settings_' . $key]);
							if ( isset($params) && isset($params['validate']) && !empty($params['validate']) && $params['validate'] == 'password' ) :
								$captured_value = '*******';
							endif;
						endif;
						$configField['captured'] = $captured_value;
						$captured_fields[$fieldkey] = $configField;
					endif;
				endif;
			endforeach;
			return $captured_fields;	
		}
				
		/**
		 * Our Admin Settings Page.
		 *
		 * @access public
		 * @since 1.1.0
		 * @output Settings page
		 */		
		public function wcpgsk__options_page() {
			global $woocommerce, $wcpgsk_options, $wcpgsk_name;
			//must check that the user has the required capability 
			if (!current_user_can('manage_options'))
			{
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
			$hidden_field_name = 'wcpgsk_submit_hidden';
			// read options values
			$options = get_option( 'wcpgsk_settings' );
			if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
				// Save the posted values in the database
				//var_dump($_POST);
				/*
				if ( isset($_POST['wcpgsk_settings']['checkoutform']['checkoutjs']) ) :
					$wcpgsk_checkoutjs = $options['checkoutform']['checkoutjs'];
					$options['checkoutform']['checkoutjs'] = '';
					update_option('wcpgsk_checkoutjs', $wcpgsk_checkoutjs);
				endif;
				*/
				update_option( 'wcpgsk_settings', $options );
				//echo "<h1>mumpits</h1>";
			}
			do_action( 'wcpgsk_settings_update', $options );
			// Now display the settings editing screen
			//get some reused labels
			 
			$placeWide = __('Wide',WCPGSK_DOMAIN);
			$placeFirst = __('First',WCPGSK_DOMAIN);
			$placeLast = __('Last',WCPGSK_DOMAIN);
			$defchecked = __('Default: checked', WCPGSK_DOMAIN);
			$defunchecked = __('Default: unchecked', WCPGSK_DOMAIN);
			
			
			echo '<div class="wrap">';
			// icon for settings
			echo '<div id="icon-themes" class="icon32"><br></div>';
			// header
			$wcpgsk_name = apply_filters('wcpgsk_plus_name', $wcpgsk_name);
			echo "<h2>" . __( $wcpgsk_name, WCPGSK_DOMAIN ) . "</h2>";
			// settings form 
				
			if ( isset($options['locale']['countrycode']) && $options['locale']['countrycode'] ) :
				$locale = get_option( 'wcpgsk_locale', array() );

				$localeCode = $options['locale']['countrycode'];
				$jsfields = $this->wcpgsk_get_country_locale_field_selectors();
				foreach ($jsfields as $field => $fieldids) :
					$locale[$localeCode]['label_' . $field] = $options['locale']['label_' . $field];
					$locale[$localeCode]['placeholder_' . $field] = $options['locale']['placeholder_' . $field];
					$locale[$localeCode]['required_' . $field] = $options['locale']['required_' . $field];
					if ( $field !== 'state' ) :
						$locale[$localeCode]['hidden_' . $field] = $options['locale']['hidden_' . $field];
					endif;
				endforeach;
				$locale[$localeCode]['postcode_before_city'] = $options['locale']['postcode_before_city'];	
				// Save the posted values in the database
				update_option( 'wcpgsk_locale', $locale );
			endif;
								
			?>
			<form name="form" method="post" action="options.php" id="frm1">
				<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
					<?php
						settings_fields( 'wcpgsk_options' );
						$options = get_option( 'wcpgsk_settings' );
						$options['process']['fastcheckoutbtn'] = isset($options['process']['fastcheckoutbtn']) ? $options['process']['fastcheckoutbtn'] : '';
						$options['process']['readmorebtn'] = isset($options['process']['readmorebtn']) ? $options['process']['readmorebtn'] : '';
						$options['process']['buyproductbtn'] = isset($options['process']['buyproductbtn']) ? $options['process']['buyproductbtn'] : '';
						$options['process']['viewproductsbtn'] = isset($options['process']['viewproductsbtn']) ? $options['process']['viewproductsbtn'] : '';
						$options['process']['selectoptionsbtn'] = isset($options['process']['selectoptionsbtn']) ? $options['process']['selectoptionsbtn'] : '';
						$options['process']['outofstockbtn'] = isset($options['process']['outofstockbtn']) ? $options['process']['outofstockbtn'] : '';	
						if ( !isset( $options['process']['onsalelabel'] ) ) :
							$options['process']['onsalelabel'] = __( 'Sale!', WCPGSK_DOMAIN );
						endif;
						if ( !isset( $options['process']['backorderlabel'] ) ) :
							$options['process']['backorderlabel'] = '';
						endif;
						if ( !isset( $options['process']['emptycartlabel'] ) ) :
							$options['process']['emptycartlabel'] = __( 'Empty cart?', WCPGSK_DOMAIN );
						endif;
						if ( !isset( $options['process']['confirmemptycart'] ) ) :
							$options['process']['confirmemptycart'] = __( 'Yes, empty cart', WCPGSK_DOMAIN );
						endif;
						if ( !isset( $options['checkoutform']['cssclass'] ) ) :
							$options['cssclass']['cssclass'] = '';
						endif;
						
						
						//add options if necessary
						//unset( $options['filters'] );
						if ( !isset( $options['filters'] ) || empty( $options['filters'] ) ) :
							$options['filters'] = array(
								'loop_shop_per_page' => get_option( 'posts_per_page' ),
								'loop_shop_columns' => '4',
								'woocommerce_product_thumbnails_columns' => '3',
								'woocommerce_create_account_default_checked' => 0,
								'woocommerce_product_description_tab_title' => __('Description', 'woocommerce'),
								'woocommerce_product_description_heading' => __( 'Product Description', 'woocommerce' ),
								'woocommerce_product_additional_information_tab_title' => __('Additional Information', 'woocommerce'),
								'woocommerce_product_additional_information_heading' => __( 'Additional Information', 'woocommerce' ),
								'woocommerce_checkout_must_be_logged_in_message' => __( 'You must be logged in to checkout.', 'woocommerce' ),
								'woocommerce_checkout_login_message' => __( 'Returning customer?', 'woocommerce' ),
								
								'woocommerce_checkout_coupon_message' => __( 'Have a coupon?', 'woocommerce' ),
								'woocommerce_checkout_coupon_link_message' => __( 'Click here to enter your code', 'woocommerce' ),
								
								'woocommerce_order_button_text' => __( 'Place order', 'woocommerce' ),
								'woocommerce_pay_order_button_text' => __( 'Pay for order', 'woocommerce' ),
								'woocommerce_thankyou_order_received_text' => __( 'Thank you. Your order has been received.', 'woocommerce' ),						
								
								'woocommerce_countries_tax_or_vat' => $this->WC()->countries->tax_or_vat(),
								'woocommerce_countries_inc_tax_or_vat' => $this->WC()->countries->inc_tax_or_vat(),
								'woocommerce_countries_ex_tax_or_vat' => $this->WC()->countries->ex_tax_or_vat(),
								'woocommerce_placeholder_img_src' => '',
							);
						endif;
						
					?>
				<div id="wcpgsk_accordion">
					<?php do_action( 'wcpgsk_settings_page_zero', $options ); ?>

					<h3 class="wcpgsk_acc_header"><?php echo __('Shop Settings',WCPGSK_DOMAIN); ?></h3>
					<div>
						<table class="widefat" border="1" >
						<thead>
							<tr>
								<th><?php _e('Settings Name', WCPGSK_DOMAIN);  ?></th>
								<th><?php _e('Data', WCPGSK_DOMAIN);  ?></th>		
								<th><?php _e('Comments', WCPGSK_DOMAIN);  ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php _e('Product items per page', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[filters][loop_shop_per_page]" type="number" value="<?php if (isset($options['filters']['loop_shop_per_page'])) echo esc_attr( $options['filters']['loop_shop_per_page'] ); ?>" size="3" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php esc_attr(_e('Specify the number of items you want to show on a shop page.', WCPGSK_DOMAIN)); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Product columns per page', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[filters][loop_shop_columns]" type="number" value="<?php if (isset($options['filters']['loop_shop_columns'])) echo esc_attr( $options['filters']['loop_shop_columns'] ); ?>" size="2" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php esc_attr(_e('Specify the number of product columns you want to show on a shop page.', WCPGSK_DOMAIN)); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Product thumbnail columns per page', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[filters][woocommerce_product_thumbnails_columns]" type="number" value="<?php if (isset($options['filters']['woocommerce_product_thumbnails_columns'])) echo esc_attr( $options['filters']['woocommerce_product_thumbnails_columns'] ); ?>" size="2" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php esc_attr(_e('Specify the number of product thumbnail columns you want to show on a shop page.', WCPGSK_DOMAIN)); ?></span>
								</td>
							</tr>
							
							<tr>
								<td width="25%"><?php _e( 'Default state of create account', WCPGSK_DOMAIN ); ?></td>
								<td>
									<input class="checkbox" name="wcpgsk_settings[filters][woocommerce_create_account_default_checked]" id="wcpgsk_create_account" value="0" type="hidden">
									<input class="checkbox" name="wcpgsk_settings[process][woocommerce_create_account_default_checked]" id="wcpgsk_create_account" value="1" type="checkbox" <?php if ( isset( $options['filters']['woocommerce_create_account_default_checked'] ) && 1 == ($options['filters']['woocommerce_create_account_default_checked'])) echo "checked='checked'"; ?> type="checkbox">
								</td>
								<td>
									<span class="description"><?php _e('Control the default state of the create account checkbox in checkout', WCPGSK_DOMAIN); ?></span>
								</td>
								
							</tr>
							<tr>
								<td width="25%"><?php _e( 'Enable Fast Cart', WCPGSK_DOMAIN ); ?></td>
								<td>
									<input class="checkbox" name="wcpgsk_settings[process][fastcart]" id="wcpgsk_fastcart" value="0" type="hidden">
									<input class="checkbox" name="wcpgsk_settings[process][fastcart]" id="wcpgsk_fastcart" value="1" type="checkbox" <?php if (  1 == ($options['process']['fastcart'])) echo "checked='checked'"; ?> type="checkbox">
								</td>
								<td>
									<span class="description"><?php _e('This option takes customers to cart after adding an item. Do not activate both, Fast Cart and Fast Checkout...', WCPGSK_DOMAIN); ?></span>
								</td>
								
							</tr>
							<tr>
								<td width="25%"><?php _e( 'Enable Fast Checkout', WCPGSK_DOMAIN ); ?></td>
								<td>
									<input class="checkbox" name="wcpgsk_settings[process][fastcheckout]" id="wcpgsk_fastcheckout" value="0" type="hidden">
									<input class="checkbox" name="wcpgsk_settings[process][fastcheckout]" id="wcpgsk_fastcheckout" value="1" type="checkbox" <?php if (  1 == ($options['process']['fastcheckout'])) echo "checked='checked'"; ?> type="checkbox">
								</td>
								<td>
									<span class="description"><?php _e('This option takes customers to checkout after adding an item. Do not activate both, Fast Cart and Fast Checkout...', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td width="25%"><?php _e( 'Enable Payment Gateways Configuration', WCPGSK_DOMAIN ); ?></td>
								<td>
									<input class="checkbox" name="wcpgsk_settings[process][paymentgateways]" id="wcpgsk_paymentgateways" value="0" type="hidden">
									<input class="checkbox" name="wcpgsk_settings[process][paymentgateways]" id="wcpgsk_paymentgateways" value="1" type="checkbox" <?php if (  1 == ($options['process']['paymentgateways'])) echo "checked='checked'"; ?> type="checkbox">
								</td>
								<td>
									<span class="description"><?php _e('This option allows you to configure the available payment gateways for each product.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<?php do_action( 'wcpgsk_settings_shop_after', $options ); ?>
						</tbody>
						</table>
						<?php submit_button( __( 'Save Changes', WCPGSK_DOMAIN ) ); ?>
					
					</div>
					<?php do_action( 'wcpgsk_settings_page_labels', $options ); ?>
					<?php do_action( 'wcpgsk_settings_page_email', $options ); ?>
					
					<?php do_action( 'wcpgsk_settings_page_two', $options ); ?>

					<h3 class="wcpgsk_acc_header"><?php echo __('Cart Settings',WCPGSK_DOMAIN); ?></h3>
					<div>
						<table class="widefat" border="1" >
						<thead>
							<tr>
								<th><?php _e('Settings Name', WCPGSK_DOMAIN);  ?></th>
								<th width="50%"><?php _e('Data', WCPGSK_DOMAIN);  ?></th>		
								<th><?php _e('Comments', WCPGSK_DOMAIN);  ?></th>
							</tr>
						</thead>
						<tbody>
							<?php do_action( 'wcpgsk_settings_cart_before', $options ); ?>							
							<tr>
								<td><?php _e('Add empty cart button to cart', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[cart][addemptycart]" type="hidden" value="0" />
									<input name="wcpgsk_settings[cart][addemptycart]" type="checkbox" value="1" <?php if (  1 == ($options['cart']['addemptycart'])) echo "checked='checked'"; ?> />
								</td>
								<td>
									<span class="description"><?php _e('Allow your customers to empty the cart.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td width="25%"><?php _e( 'Empty Cart Label', WCPGSK_DOMAIN ); ?></td>
								<td>
									<input name="wcpgsk_settings[process][emptycartlabel]" type="text" id="wcpgsk_emptycartlabel" value="<?php echo isset( $options['process']['emptycartlabel'] ) ? $options['process']['emptycartlabel'] : ''; ?>" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('Label for the empty cart button.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td width="25%"><?php _e( 'Confirm Empty Cart Label', WCPGSK_DOMAIN ); ?></td>
								<td>
									<input name="wcpgsk_settings[process][confirmemptycart]" type="text" id="wcpgsk_confirmemptycart" value="<?php echo isset( $options['process']['confirmemptycart'] ) ? $options['process']['confirmemptycart'] : ''; ?>" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('Leave this blank to allow empty cart action without confirmation.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							
							<tr>
								<td><?php _e('Minimum cart items', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[cart][minitemscart]" type="text" value="<?php if (isset($options['cart']['minitemscart'])) echo esc_attr( $options['cart']['minitemscart'] ); ?>" size="2" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php esc_attr(_e('You can specify the minimum of items allowed in woocommerce customer carts for wholesale purposes. If you leave this blank 0 (=option deactivated) will be established. Please be aware that you have to set the maximum to the same or a higher limit. This value will be automatically adjusted to assure store operations.', WCPGSK_DOMAIN)); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Maximum cart items', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[cart][maxitemscart]" type="text" value="<?php if (isset($options['cart']['maxitemscart'])) echo esc_attr( $options['cart']['maxitemscart'] ); ?>" size="2" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('You can specify the maximum of items allowed in woocommerce customer carts. If you leave this blank 0 (=option deactivated) will be established.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Treat variation items like individual (different) product items when counting items in cart', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[cart][variationscountasproduct]" type="hidden" value="0" />
									<input name="wcpgsk_settings[cart][variationscountasproduct]" type="checkbox" value="1" <?php if (  1 == ($options['cart']['variationscountasproduct'])) echo "checked='checked'"; ?> />
								</td>
								<td>
									<span class="description"><?php _e('If you want to allow users to buy the maximum of variations allowed, even if the the product maximum is reached, do not check this option. Minimum handling takes this into account, too.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Maximum global cart quantity', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[cart][maxqtycart]" type="text" value="<?php if (isset($options['cart']['maxqtycart'])) echo esc_attr( $options['cart']['maxqtycart'] ); ?>" size="3" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('You can specify the allowed overall maximum (sum of all item quantities). If you leave this blank, 0 (=option deactivated) will be established.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Minimum global cart quantity', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[cart][minqtycart]" type="text" value="<?php if (isset($options['cart']['minqtycart'])) echo esc_attr( $options['cart']['minqtycart'] ); ?>" size="3" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('You can specify the required overall minimum (sum of all item quantities). If you leave this blank, 0 (=option deactivated) will be established.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Allow min/max/step configuration on per product basis', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[cart][minmaxstepproduct]" type="hidden" value="0" />
									<input name="wcpgsk_settings[cart][minmaxstepproduct]" type="checkbox" value="1" <?php if (  1 == ($options['cart']['minmaxstepproduct'])) echo "checked='checked'"; ?> />
								</td>
								<td>
									<span class="description"><?php _e('If you want to configure minimum/maximum and steps for item quantities, enable this checkbox. Individual product settings take precedence over the following global settings.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							
							<tr>
								<td><?php _e('Switch off quantity input', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<?php
									$noquantity = __('No quantity input for', WCPGSK_DOMAIN);
									$quantity = __('Quantity input for', WCPGSK_DOMAIN);
									?>
									<select name="wcpgsk_settings[cart][variationproductnoqty]" class="wcpgsk_qty_select">
										<option value="1" <?php if ($options['cart']['variationproductnoqty'] == 1) echo "selected"; ?> ><?php echo $noquantity . ' ' . __('Product Variation', WCPGSK_DOMAIN);?></option>	
										<option value="0" <?php if ($options['cart']['variationproductnoqty'] == 0) echo "selected"; ?> ><?php echo $quantity . ' ' . __('Product Variation', WCPGSK_DOMAIN);?></option>
									</select><br />
									<select name="wcpgsk_settings[cart][variableproductnoqty]" class="wcpgsk_qty_select">
										<option value="1" <?php if ($options['cart']['variableproductnoqty'] == 1) echo "selected"; ?> ><?php echo $noquantity . ' ' . __('Variable Product', WCPGSK_DOMAIN);?></option>	
										<option value="0" <?php if ($options['cart']['variableproductnoqty'] == 0) echo "selected"; ?> ><?php echo $quantity . ' ' . __('Variable Product', WCPGSK_DOMAIN);?></option>
									</select><br />
									<!-- Does not apply
									<select name="wcpgsk_settings[cart][groupedproductnoqty]" class="wcpgsk_qty_select">
										<option value="1" <?php //if ($options['cart']['groupedproductnoqty'] == 1) echo "selected"; ?> ><?php //echo $noquantity  . ' ' . __('Grouped Product', WCPGSK_DOMAIN);?></option>	
										<option value="0" <?php //if ($options['cart']['groupedproductnoqty'] == 0) echo "selected"; ?> ><?php //echo $quantity . ' ' . __('Grouped Product', WCPGSK_DOMAIN);?></option>
									</select><br />
									-->
									<select name="wcpgsk_settings[cart][externalproductnoqty]" class="wcpgsk_qty_select">
										<option value="1" <?php if ($options['cart']['externalproductnoqty'] == 1) echo "selected"; ?> ><?php echo $noquantity . ' ' . __('External Product', WCPGSK_DOMAIN);?></option>
										<option value="0" <?php if ($options['cart']['externalproductnoqty'] == 0) echo "selected"; ?> ><?php echo $quantity . ' ' . __('External Product', WCPGSK_DOMAIN);?></option>
									</select><br />
									
									<select name="wcpgsk_settings[cart][simpleproductnoqty]" class="wcpgsk_qty_select">
										<option value="1" <?php if ($options['cart']['simpleproductnoqty'] == 1) echo "selected"; ?> ><?php echo $noquantity . ' ' . __('Simple Product', WCPGSK_DOMAIN);?></option>	
										<option value="0" <?php if ($options['cart']['simpleproductnoqty'] == 0) echo "selected"; ?> ><?php echo $quantity . ' ' . __('Simple Product', WCPGSK_DOMAIN);?></option>
									</select>
								</td>
								<td>
									<span class="description"><?php _e('Switch off the quantity input field and set quantity automatically to one if a customer adds a product or product variation to his/her cart.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
						<?php
							$product_types = array('variation' => __('Set the minimum and maximum quantities allowed for each item that a customer can add to his/her cart', WCPGSK_DOMAIN),
								'variable' => __('Please, you should not confuse product quantity and the allowed maximum and minimum of individual items in a cart.', WCPGSK_DOMAIN),
								//does not apply 'grouped' => __('The min and max quantity values only have effect if you enable quantity input for a given product type. To disable this functionality you can set this value to 0.', WCPGSK_DOMAIN),
								'external' => __('To squizze out more of WooCommerce you may want to upgrade to WooCommerce Rich Guys Swiss Knife :-).', WCPGSK_DOMAIN),
								'simple' => __('Individual items can be personalized by Woocommerce Rich Guys Swiss Knife during checkout.', WCPGSK_DOMAIN));
							foreach($product_types as $type => $descr) :
								$minqty = !empty($options['cart']['minqty_' . $type]) ? $options['cart']['minqty_' . $type] : 0;
								$maxqty = !empty($options['cart']['maxqty_' . $type]) ? $options['cart']['maxqty_' . $type] : 0;
								$stepqty = !empty($options['cart']['stepqty_' . $type]) ? $options['cart']['stepqty_' . $type] : 0;
								
						?>
							<tr>
								<td><?php _e('Min/Max/Step quantity <strong>' . $type . ' products', WCPGSK_DOMAIN); ?></strong>:</td>
								<td>
									<input name="wcpgsk_settings[cart][minqty_<?php echo $type ; ?>]" type="text" value="<?php echo $minqty; ?>" size="2" class="wcpgsk_textfield_short" /> |
									<input name="wcpgsk_settings[cart][maxqty_<?php echo $type ; ?>]" type="text" value="<?php echo $maxqty ; ?>" size="2" class="wcpgsk_textfield_short" /> |
									<input name="wcpgsk_settings[cart][stepqty_<?php echo $type ; ?>]" type="text" value="<?php echo $stepqty ; ?>" size="2" class="wcpgsk_textfield_short" />
								</td>
								<td>
									<span class="description"><?php echo $descr ; ?></span>
								</td>
							</tr>
						<?php
							endforeach;
						?>
							
						<?php do_action( 'wcpgsk_settings_cart_after', $options ); ?>

						</tbody>
						</table>
						<?php submit_button( __( 'Save Changes', WCPGSK_DOMAIN ) ); ?>
					</div>
					<?php do_action( 'wcpgsk_settings_page_four', $options ); ?>
						<h3 class="wcpgsk_acc_header"><?php echo __('Checkout Settings',WCPGSK_DOMAIN); ?></h3>
						<div>
						<table class="widefat" border="1" >
						<thead>
							<tr>
								<th><?php _e('Settings Name', WCPGSK_DOMAIN);  ?></th>
								<th><?php _e('Data', WCPGSK_DOMAIN);  ?></th>		
								<th><?php _e('Comments', WCPGSK_DOMAIN);  ?></th>
							</tr>
						</thead>
						<tbody>
							<?php do_action( 'wcpgsk_settings_checkout_before', $options ); ?>
							<tr>
								<td><?php _e('Min date offset for date fields', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[checkoutform][mindate]" type="text" value="<?php if (!empty($options['checkoutform']['mindate'])) echo esc_attr( $options['checkoutform']['mindate'] ); ?>" size="2" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('For date fields you can specify a minimum offset in number of days.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Max date offset for date fields', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[checkoutform][maxdate]" type="text" value="<?php if (!empty($options['checkoutform']['maxdate'])) echo esc_attr( $options['checkoutform']['maxdate'] ); ?>" size="3" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('For date fields you can specify a maximum offset in number of days.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Use calendar style time picker', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[checkoutform][caltimepicker]" type="hidden" value="0" />
									<input name="wcpgsk_settings[checkoutform][caltimepicker]" type="checkbox" value="1" <?php if ( isset( $options['checkoutform']['caltimepicker'] ) && 1 == ($options['checkoutform']['caltimepicker'])) echo "checked='checked'"; ?> />
								</td>
								<td>
									<span class="description"><?php _e('Use alternative presentation for time picker fields.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							
							<tr>
								<td><?php _e('AM/PM for calendar style time picker', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[checkoutform][caltimeampm]" type="hidden" value="0" />
									<input name="wcpgsk_settings[checkoutform][caltimeampm]" type="checkbox" value="1" <?php if ( isset( $options['checkoutform']['caltimeampm'] ) && 1 == ($options['checkoutform']['caltimeampm'])) echo "checked='checked'"; ?> />
								</td>
								<td>
									<span class="description"><?php _e('Activate AM/PM support for calendar style time picker.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Css class', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[checkoutform][cssclass]" type="text" class="wcpgsk_textfield" value="<?php if (!empty($options['checkoutform']['cssclass'])) echo esc_attr( $options['checkoutform']['cssclass'] ); ?>" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('You can add one or more css classes to custom fields. Use space as separator.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Additional billing fields title', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[checkoutform][morebillingtitle]" type="text" class="wcpgsk_textfield" value="<?php if (!empty($options['checkoutform']['morebillingtitle'])) echo esc_attr( $options['checkoutform']['morebillingtitle'] ); ?>" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('You can set the additional billing information section title used in emails and order receipts. If you leave this field empty the section title will be omitted.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Additional shipping fields title', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[checkoutform][moreshippingtitle]" type="text" class="wcpgsk_textfield" value="<?php if (!empty($options['checkoutform']['moreshippingtitle'])) echo esc_attr( $options['checkoutform']['moreshippingtitle'] ); ?>" class="regular-text" />
								</td>
								<td>
									<span class="description"><?php _e('You can set the additional shipping information section title used in emails and order receipts. If you leave this field empty the section title will be omitted.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
						
							<tr>
								<td><?php _e('Add billing email validator', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<input name="wcpgsk_settings[checkoutform][billingemailvalidator]" type="hidden" value="0" />
									<input name="wcpgsk_settings[checkoutform][billingemailvalidator]" type="checkbox" value="1" <?php if (  1 == ($options['checkoutform']['billingemailvalidator'])) echo "checked='checked'"; ?> />
								</td>
								<td>
									<span class="description"><?php _e('If you want to oblige the user to input his/her email a second time and to assure that the email is valid, activate. This field will be added automatically.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<tr>
								<td><?php _e('Checkout Script', WCPGSK_DOMAIN); ?>:</td>
								<td>
									<textarea name="wcpgsk_checkoutjs" id="wcpgsk_checkoutjs"><?php echo get_option('wcpgsk_checkoutjs') ;?></textarea>
									<button onclick="save_checkoutjs();return false;"><?php _e('Save js', WCPGSK_DOMAIN); ?></button>
									<span id="result_save_checkoutjs"></span>
								</td>
								<td>
									<span class="description"><?php _e('You can save your own js code for the checkout page here. Only code, no tags!', WCPGSK_DOMAIN); ?></span>
									<br />
									<span class="description"><?php _e('Please use with care as this may break things if not handled with care.', WCPGSK_DOMAIN); ?></span>
									<br />
									<span class="description"><?php _e('You can include your script by editing wcpgsk_user.js too but you will have to restore your changes after every update of this plugin.', WCPGSK_DOMAIN); ?></span>
								</td>
							</tr>
							<?php do_action('wcpgsk_settings_checkout_after', $options); ?>

						</tbody>
						</table>
						<?php submit_button( __( 'Save Changes', WCPGSK_DOMAIN ) ); ?>
					</div>
					<?php
					do_action( 'wcpgsk_settings_page_six', $options );
					
					//if ( function_exists('WC') ) :
					?>
						<h3 class="wcpgsk_acc_header"><?php echo __('Woocommerce Checkout Localization',WCPGSK_DOMAIN); ?></h3>
						<div>
							<table class="widefat" border="1" >
							<thead>
								<tr>
									<th><?php _e('Field Name', WCPGSK_DOMAIN);  ?></th>
									<th><?php _e('Default Behaviour', WCPGSK_DOMAIN);  ?></th>		
									<th><?php _e('Comments', WCPGSK_DOMAIN);  ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$jsfields = $this->wcpgsk_get_country_locale_field_selectors();
								foreach ($jsfields as $field => $fieldids) :
								?>
								<tr>
									<td><?php echo $field; ?></td>
									<td>
										<input name="wcpgsk_settings[checkoutform][default_<?php echo $field; ?>]" type="hidden" value="0" />
										<input name="wcpgsk_settings[checkoutform][default_<?php echo $field; ?>]" type="checkbox" value="1" <?php if ( isset($options['checkoutform']['default_' . $field]) && 1 == ($options['checkoutform']['default_' . $field])) echo "checked='checked'"; ?> />
									</td>
									<td>
										<span class="description"><?php echo __('Let WCPGSK handle localization for fields of type', WCPGSK_DOMAIN) . ' ' . $field; ?></span>
									</td>
								</tr>

								<?php
								endforeach;
								?>
							</tbody>
							</table>
							<table class="widefat" border="1" >
							<thead>
								<tr>
									<th><?php _e('Field Name', WCPGSK_DOMAIN);  ?></th>
									<th><?php _e('Data', WCPGSK_DOMAIN);  ?></th>		
									<th><?php _e('Comments', WCPGSK_DOMAIN);  ?></th>
								</tr>
							</thead>
							<tbody>
							
								<tr>
									<td><?php _e('Field Localization', WCPGSK_DOMAIN);  ?></td>
									<td>
									<select name="wcpgsk_configcountry" id="wcpgsk_configcountry" onChange="get_locale_fields_form()"> 
										<option value=""><?php echo __('Select Locale', WCPGSK_DOMAIN); ?></option>
										<option value="default"><?php _e('Default Locale', WCPGSK_DOMAIN); ?></option>
									<?php
										$countries = $this->wcpgsk_get_allowed_countries();//WC()->countries->countries;

										foreach ($countries as $cc => $cn) :							
										echo '<option value="' . $cc . '">' . $cn . '</option>';
										endforeach;
									?>
									</select>
									</td>
									<td>
									<?php _e('Select country to configure', WCPGSK_DOMAIN);  ?>
									</td>
									
								</tr>
							</tbody>
							<tbody id="locale_field_form">
							</tbody>
							
							</table>
							<?php submit_button( __( 'Save Changes', WCPGSK_DOMAIN ) ); ?>
						
						</div>
					<?php
					
					$checkoutforms = array('billing' => 'Billing', 'shipping' => 'Shipping');
					$checkoutforms = apply_filters( 'wcpgsk_checkoutforms', $checkoutforms );
					foreach($checkoutforms as $section => $title) :
					?>
						<h3 class="wcpgsk_acc_header"><?php echo __('Woocommerce Checkout ' . $title . ' Section',WCPGSK_DOMAIN); ?></h3>
						<div>
							<table class="wcpgsk_fieldtable widefat" id="wcpgsk_<?php echo $section ;?>_table" border="1" >
							<thead>
								<tr>
									<th class="wcpgsk_replace"><?php _e('Order', WCPGSK_DOMAIN);  ?></th>
									<th><?php _e('Field Name', WCPGSK_DOMAIN);  ?></th>
									<th><?php _e('Remove Field', WCPGSK_DOMAIN);  ?></th>		
									<th><?php _e('Required Field', WCPGSK_DOMAIN);  ?></th>
									<th><?php _e('Hide in Receipts', WCPGSK_DOMAIN);  ?></th>
									<th><span title="<?php esc_attr_e('Store only in customer context, do not include in order', WCPGSK_DOMAIN); ?>"><?php _e('Customer data', WCPGSK_DOMAIN);  ?></span></th>
									<th class="wcpgsk_replace"><?php _e('Label', WCPGSK_DOMAIN);  ?></th>
									<th class="wcpgsk_replace"><?php _e('Placeholder', WCPGSK_DOMAIN);  ?></th>
									<th class="wcpgsk_replace"><?php _e('Display', WCPGSK_DOMAIN);  ?></th>
									<th class="wcpgsk_replace"><?php _e('Type', WCPGSK_DOMAIN);  ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td></td>
									<td><input type="checkbox" class="select_removes" for="removes_<?php echo $section ;?>" id="select_remove_<?php echo $section ;?>" value="1" /> <?php _e('Select All', WCPGSK_DOMAIN);  ?></td>
									<td><input type="checkbox" class="select_required" for="required_<?php echo $section ;?>" id="select_required_<?php echo $section ;?>" value="1" /> <?php _e('Select All', WCPGSK_DOMAIN);  ?></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<?php 
								if ($section == 'order') :
									$checkout_fields = array();
									$checkout_fields = apply_filters( 'wcpgsk_order_checkout_fields', $checkout_fields, $options );
								elseif ($section == 'shared') :
									$checkout_fields = array();
									$checkout_fields = apply_filters( 'wcpgsk_shared_checkout_fields', $checkout_fields, $options );
								else:
									$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), $section . '_' );
									$field_order = 1;
									foreach ($checkout_fields as $key => $field) :
										
										$checkout_fields[$key]['placeholder'] = isset($checkout_fields[$key]['placeholder']) ? $checkout_fields[$key]['placeholder'] : '';
										$checkout_fields[$key]['label'] = isset($checkout_fields[$key]['label']) ? $checkout_fields[$key]['label'] : '';
										$checkout_fields[$key]['required'] = isset($checkout_fields[$key]['required']) ? $checkout_fields[$key]['required'] : 0;
										
										$checkout_fields[$key]['fieldkey'] = $key;
										$checkout_fields[$key]['displaylabel'] = isset($options['woofields']['label_' . $key]) && !empty($field['label']) ? __($field['label'], WCPGSK_DOMAIN) : $key;
										$checkout_fields[$key]['order'] = ((isset($options['woofields']['order_' . $key]) && !empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);
										//$checkout_fields[$key]['placeholder'] = ((isset($options['woofields']['placeholder_' . $key]) && !empty($options['woofields']['placeholder_' . $key])) ? $options['woofields']['placeholder_' . $key] : $checkout_fields[$key]['placeholder']);
										$checkout_fields[$key]['placeholder'] = ((isset($options['woofields']['placeholder_' . $key])) ? $options['woofields']['placeholder_' . $key] : $checkout_fields[$key]['placeholder']);
										$checkout_fields[$key]['label'] = ((isset($options['woofields']['label_' . $key])) ? $options['woofields']['label_' . $key] : $checkout_fields[$key]['label']);
										//$checkout_fields[$key]['label'] = ((isset($options['woofields']['label_' . $key]) && !empty($options['woofields']['label_' . $key])) ? $options['woofields']['label_' . $key] : $checkout_fields[$key]['label']);
										//before required defreq
										$checkout_fields[$key]['defreq'] = ((isset($checkout_fields[$key]['required']) && $checkout_fields[$key]['required'] == 1) ? $defchecked : $defunchecked);
										$checkout_fields[$key]['required'] = ((isset($options['woofields']['required_' . $key])) ? $options['woofields']['required_' . $key] : $checkout_fields[$key]['required']);
										$checkout_fields[$key]['customeronly'] = ((isset($options['woofields']['customeronly_' . $key])) ? $options['woofields']['customeronly_' . $key] : 0);
										$checkout_fields[$key]['hideorder'] = ((isset($options['woofields']['hideorder_' . $key])) ? $options['woofields']['hideorder_' . $key] : 0);
										$checkout_fields[$key]['type'] = ((isset($options['woofields']['type_' . $key]) && !empty($options['woofields']['type_' . $key])) ? $options['woofields']['type_' . $key] : ((!empty($checkout_fields[$key]['type'])) ? $checkout_fields[$key]['type'] : 'text') );
										
										$checkout_fields[$key]['classsel'] = ((isset($options['woofields']['class_' . $key]) && !empty($options['woofields']['class_' . $key])) ? $options['woofields']['class_' . $key] : ((isset($checkout_fields[$key]['class'])) ? $checkout_fields[$key]['class'][0] : 'form-row-wide') );
										$checkout_fields[$key]['settings'] = ((isset($options['woofields']['settings_' . $key]) && !empty($options['woofields']['settings_' . $key])) ? $options['woofields']['settings_' . $key] : '' );
										$field_order++;
									endforeach;
								endif;
								$checkout_fields = apply_filters( 'wcpgsk_checkout_fields', $checkout_fields, $options );
								uasort( $checkout_fields, array($this, "compareFieldOrder") );						
								$field_order = 1;
								
								foreach ($checkout_fields as $key => $field) : 
									$fieldLabel = $field['displaylabel'];
									$options['woofields'][$section][$key]['custom_' . $key] = isset($options['woofields'][$section][$key]['custom_' . $key]) ? $options['woofields'][$section][$key]['custom_' . $key] : '';
								?>
									<tr class="wcpgsk_order_row">
										<td class="wcpgsk_order_col"><span class="ui-icon ui-icon-arrow-4"></span><span class="wcpgsk_order_span"><?php echo $field['order']; ?></span><input type="hidden"  class="wcpgsk_order_input" name="wcpgsk_settings[woofields][order_<?php echo $field['fieldkey']; ?>]" value="<?php echo $field['order']; ?>" /></td>
										<td>
											<?php
												if ($options['woofields'][$section][$key]['custom_' . $key] == $key && $key != 'order_comments') :
											?>
												<input name="wcpgsk_settings[woofields][<?php echo $section ;?>][<?php echo $key; ?>][custom_<?php echo $key; ?>]" type="hidden" value="<?php echo $key; ?>" />
												<button class="wcpgsk_remove_field" for="wcpgsk_<?php echo $section ;?>_table" name="wcpgsk_settings[woofields][<?php echo $section ;?>][<?php echo $key; ?>][fieldname_<?php echo $key; ?>]"><?php _e('X',WCPGSK_DOMAIN) ; ?></button> <?php echo $key; ?>
											<?php
												else :
													if ($section == 'order' && $key == 'order_comments') :
													?>
														<input name="wcpgsk_settings[woofields][<?php echo $section ;?>][<?php echo $key; ?>][custom_<?php echo $key; ?>]" type="hidden" value="<?php echo $key; ?>" />
													<?php
													endif;
													echo $fieldLabel; 
												endif;
											?>
										</td>
										<td><input name="wcpgsk_settings[woofields][remove_<?php echo $field['fieldkey']; ?>]" type="hidden" value="0" />
											<input name="wcpgsk_settings[woofields][remove_<?php echo $field['fieldkey']; ?>]" type="checkbox" class="removes_<?php echo $section ;?>" value="1" 
											<?php if ( isset($options['woofields']['remove_' . $field['fieldkey']]) && 1 == ($options['woofields']['remove_' . $field['fieldkey']]) ) echo 'checked="checked"'; ?>   /></td>
										<td><input name="wcpgsk_settings[woofields][required_<?php echo $field['fieldkey']; ?>]" type="hidden" value="0" />
											<input name="wcpgsk_settings[woofields][required_<?php echo $field['fieldkey']; ?>]" type="checkbox" class="required_<?php echo $section ;?>" value="1" <?php if (  1 == $field['required'] ) echo "checked='checked'"; ?> />
											<small> <?php echo $field['defreq']; ?></small>
										</td>
										<td><input name="wcpgsk_settings[woofields][hideorder_<?php echo $field['fieldkey']; ?>]" type="hidden" value="0" />
											<input name="wcpgsk_settings[woofields][hideorder_<?php echo $field['fieldkey']; ?>]" type="checkbox" class="hideorder_<?php echo $section ;?>" value="1" <?php if (  1 == $field['hideorder'] ) echo "checked='checked'"; ?> />
										</td>
										<td><input name="wcpgsk_settings[woofields][customeronly_<?php echo $field['fieldkey']; ?>]" type="hidden" value="0" />
											<input name="wcpgsk_settings[woofields][customeronly_<?php echo $field['fieldkey']; ?>]" type="checkbox" class="customeronly_<?php echo $section ;?>" value="1" <?php if ( isset( $field['customeronly'] ) && 1 == $field['customeronly'] ) echo "checked='checked'"; ?> />
										</td>
										<td><input type="text" name="wcpgsk_settings[woofields][label_<?php echo $field['fieldkey']; ?>]" class="wcpgsk_textfield" 
											value="<?php echo esc_attr( $field['label'] ); ?>" /></td>
										<td><input type="text" name="wcpgsk_settings[woofields][placeholder_<?php echo $field['fieldkey']; ?>]" class="wcpgsk_textfield" 
											value="<?php echo esc_attr( $field['placeholder'] ); ?>" /></td>
										<td>
											<select name="wcpgsk_settings[woofields][class_<?php echo $field['fieldkey']; ?>]">
												<option value="form-row-wide" 
												<?php if (  'form-row-wide' == ($field['classsel']) ) echo 'selected="selected"'; ?>><?php echo $placeWide; ?></option>
												<option value="form-row-first" 
												<?php if (  'form-row-first' == ($field['classsel']) ) echo 'selected="selected"'; ?>><?php echo $placeFirst; ?></option>
												<option value="form-row-last" 
												<?php if (  'form-row-last' == ($field['classsel']) ) echo 'selected="selected"'; ?>><?php echo $placeLast; ?></option>
											</select>
										</td>
										<td class="wcpgsk_functions_col">
											<?php
												if ($options['woofields'][$section][$key]['custom_' . $key] == $key && $key != 'order_comments') :
											?>
												<button class="wcpgsk_configure_field" table="wcpgsk_<?php echo $section ;?>_table" for="<?php echo $key ; ?>" type="<?php echo $field['type'] ; ?>" name="wcpgsk_settings[woofields][button_<?php echo $key ; ?>]"><?php echo $field['type'] ; ?></button>
												<input name="wcpgsk_settings[woofields][type_<?php echo $key; ?>]" type="hidden" value="<?php echo $field['type'] ; ?>" />
												<input name="wcpgsk_settings[woofields][settings_<?php echo $key; ?>]" type="hidden" value="<?php echo $field['settings'] ; ?>" />
											<?php
												else :
													echo $field['type']; 
												endif;
											?>
										</td>
									</tr>
								<?php 
									$field_order++;
								endforeach; 
								$custom = 'nn2id';
								$newField = __('New Field', WCPGSK_DOMAIN);
								?>
								
								<tr valign="top" class="wcpgsk_add_field_row" id="wcpgsk_add_<?php echo $section ;?>_field_row">
										<td class="wcpgsk_order_col"><span class="ui-icon ui-icon-arrow-4"></span><span class="wcpgsk_order_span"><?php echo $custom; ?></span><input type="hidden"  class="wcpgsk_order_input" convert="wcpgsk_settings[woofields][order_<?php echo $custom ; ?>]" value="<?php echo $custom; ?>" /></td>
										<td class="wcpgsk_fieldname_col">
											<input convert="wcpgsk_settings[woofields][<?php echo $section ;?>][<?php echo $custom; ?>][custom_<?php echo $custom; ?>]" type="hidden" value="<?php echo $custom; ?>" />
											
											<button class="wcpgsk_remove_field" for="wcpgsk_<?php echo $section ;?>_table" convert="wcpgsk_settings[woofields][fieldname_<?php echo $custom; ?>]"><?php _e('X',WCPGSK_DOMAIN) ; ?></button> <span convert="wcpgsk_settings[woofields][ident_<?php echo $custom; ?>]"><?php echo $custom; ?></span>
										</td>
										<td><input convert="wcpgsk_settings[woofields][remove_<?php echo $custom; ?>]" type="hidden" value="0" />
											<input convert="wcpgsk_settings[woofields][remove_<?php echo $custom; ?>]" type="checkbox" class="removes_<?php echo $section ;?>" value="1" /></td>
										<td><input convert="wcpgsk_settings[woofields][required_<?php echo $custom; ?>]" type="hidden" value="0" />
											<input convert="wcpgsk_settings[woofields][required_<?php echo $custom; ?>]" type="checkbox" class="required_<?php echo $section ;?>" value="1" />
										</td>
										<td><input convert="wcpgsk_settings[woofields][hideorder_<?php echo $custom; ?>]" type="hidden" value="0" />
											<input convert="wcpgsk_settings[woofields][hideorder_<?php echo $custom; ?>]" type="checkbox" class="hideorder_<?php echo $section ;?>" value="1" />
										</td>
										<td><input convert="wcpgsk_settings[woofields][customeronly_<?php echo $custom; ?>]" type="hidden" value="0" />
											<input convert="wcpgsk_settings[woofields][customeronly_<?php echo $custom; ?>]" type="checkbox" class="customeronly_<?php echo $section ;?>" value="1" />
										</td>
										<td><input type="text" convert="wcpgsk_settings[woofields][label_<?php echo $custom; ?>]" class="wcpgsk_textfield" value="<?php echo $newField; ?>" /></td>
										<td><input type="text" convert="wcpgsk_settings[woofields][placeholder_<?php echo $custom; ?>]" class="wcpgsk_textfield" value="<?php echo $newField; ?>" /></td>
										<td>
											<select convert="wcpgsk_settings[woofields][class_<?php echo $custom; ?>]">
												<option value="form-row-wide"><?php echo $placeWide ?></option>
												<option value="form-row-first"><?php echo $placeFirst ?></option>
												<option value="form-row-last" ><?php echo $placeLast ?></option>
											</select>
										</td>
										<td class="wcpgsk_functions_col">
											<button convert="wcpgsk_settings[woofields][button_<?php echo $custom; ?>]"></button>
											<input convert="wcpgsk_settings[woofields][type_<?php echo $custom; ?>]" type="hidden" value="" />
											<input convert="wcpgsk_settings[woofields][settings_<?php echo $custom; ?>]" type="hidden" value="" />
										</td>
								   </tr>
							</tbody>
							</table>
							Select type: <select id="wcpgsk_<?php echo $section ;?>_table_type">
								<option value="text">text</option>
								<option value="textarea">textarea</option>
								<option value="date">date</option>
								<option value="time" >time</option>
								<option value="number" >number</option>
								<option value="select" >select</option>
								<?php
									do_action( 'wcpgsk_settings_form_field_types', $options );								
								?>
							</select>

							Identifier New Field: <input type="text" id="wcpgsk_<?php echo $section ;?>_table_fieldid" value="" maxlength="25" size="12" /> <a href="javascript:;" class="add_custom_field button-primary" id="add_custom_<?php echo $section ;?>_btn" for="wcpgsk_<?php echo $section ;?>_table" placeholder="<?php echo $section ;?>"><?php _e( 'New ' . $title . ' Field' , WCPGSK_DOMAIN ); ?></a>
							<?php submit_button( __( 'Save Changes', WCPGSK_DOMAIN ) ); ?>
						</div>
					
					<?php
					endforeach;
					do_action( 'wcpgsk_settings_page_eight', $options );
					do_action( 'wcpgsk_settings_page_about', $options );
					?>

				</div>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" /></p>
			</form>
			
			<div id="wcpgsk_error_dialog" title="WC Poor Guys Swiss Knife Error">
				<p id="wcpgsk_error_message"></p>
			</div>
			
			<?php
				do_action( 'wcpgsk_settings_page_dialogs_one', $options );
				$validateTip = __('Required form fields are marked with *.', WCPGSK_DOMAIN);
				
			?>
			<div id="wcpgsk_dialog_form_container" title="<?php _e('Configure your custom field', WCPGSK_DOMAIN) ; ?>">

			</div>

			<div id="wcpgsk_dialog_form_select" title="Configure Select Field" class="wcpgsk_dialog_forms">
			<p class="validateTips"><?php echo $validateTip ; ?></p>
			<form for="wcpgsk_dlg_form_select" accept-charset="utf-8">
			<table class="wcpgsfieldkconfig">
			<tr class="field_option field_option_choices">
				<td class="label">
					<label><?php _e('Options', WCPGSK_DOMAIN) ; ?>*</label>
					<p><?php _e('Enter each option on a new line.', WCPGSK_DOMAIN) ; ?>
					<br /><?php _e('You can specify value and label for each option like this:', WCPGSK_DOMAIN) ; ?>
					</p>
					<p><strong>jazz : Charles Mingus<br>blues : John Lee Hooker</strong></p>
				</td>
				<td>
					<textarea rows="6" for="wcpgsk_add_options" class="textarea field_option-choices" autocomplete="off" defaultValue="" placeholder=""></textarea>	
				</td>
			</tr>
			<tr class="field_option field_option_select">
				<td class="label">
					<label><?php _e('Default Value', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Specify default values, one per line.', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<textarea rows="3" for="wcpgsk_add_selected" class="textarea" defaultValue="" placeholder=""></textarea>	
				</td>
			</tr>
			<tr class="field_option field_option_select">
				<td class="label">
					<label><?php _e('Allow Null?', WCPGSK_DOMAIN) ; ?></label>
				</td>
				<td>
					<ul class="wcpgsk-radio-list radio horizontal"><li><label><input id="wcpgsk_add_allow_null_1" for="wcpgsk_add_allow_null" value="1" type="radio"><?php _e('Yes', WCPGSK_DOMAIN) ; ?></label></li><li><label><input id="wcpgsk_add_allow_null_0" for="wcpgsk_add_allow_null" value="0" checked="&quot;checked&quot;" data-checked="&quot;checked&quot;" type="radio"><?php _e('No', WCPGSK_DOMAIN) ; ?></label></li></ul>	
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Null value option text', WCRGSK_DOMAIN) ; ?></label>
					<p><?php _e('Specify text like "Please select an option..."', WCRGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_nulllabel" value="" />
				</td>
			</tr>			
			<tr class="field_option field_option_select">
				<td class="label">
					<label><?php _e('Select multiple values?', WCPGSK_DOMAIN) ; ?></label>
				</td>
				<td>
					<ul class="wcpgsk-radio-list radio horizontal"><li><label><input id="wcpgsk_add_multiple_1" for="wcpgsk_add_multiple" value="1" type="radio"><?php _e('Yes', WCPGSK_DOMAIN) ; ?></label></li><li><label><input id="wcpgsk_add_multiple_0" for="wcpgsk_add_multiple" value="0" checked="&quot;checked&quot;" data-checked="&quot;checked&quot;" type="radio"><?php _e('No', WCPGSK_DOMAIN) ; ?></label></li></ul>	
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Presentation', WCPGSK_DOMAIN) ; ?></label>
					
				</td>
				<td>
					<select for="wcpgsk_add_presentation">
						<option value="select"><?php _e('As select list', WCPGSK_DOMAIN) ; ?></option>
						<option value="radio"><?php _e('As radio buttons', WCPGSK_DOMAIN) ; ?></option>
						<option value="checkbox"><?php _e('As checkboxes', WCPGSK_DOMAIN) ; ?></option>
					</select>
				</td>
			</tr>


			</table>
			</form>

			</div>

			<div id="wcpgsk_dialog_form_text" title="Configure Text Field" class="wcpgsk_dialog_forms">
			<form for="wcpgsk_dlg_form_text">
			<table class="wcpgsfieldkconfig">
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Maximum characters', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Maxlength attribute for input tag. Value has to be a number.', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_maxlength" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Size', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Size attribute for input tag. Value has to be a number.', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_size" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Pattern attribute', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a regular expression. A regular expression can be used to establish or substitute validation. Example: .{3,5} will require at least 3 characters and allow for a maximum of five. Examples for html5 pattern attributes can be found <a href="http://html5pattern.com/" target="_blank">here</a>. In some circumstances, the symbols ^ in front and $ at the end provide some magical improvements for your regular expressions. Please test on all browsers!', WCPGSK_DOMAIN) ; ?></p>					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_pattern" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Validation', WCPGSK_DOMAIN) ; ?></label>
					
				</td>
				<td>
					<select for="wcpgsk_add_validate">
						<option value="none"><?php _e('No validation', WCPGSK_DOMAIN) ; ?></option>
						<option value="email"><?php _e('Email validation', WCPGSK_DOMAIN) ; ?></option>				
						<option value="date"><?php _e('Date', WCPGSK_DOMAIN) ; ?></option>
						<option value="time"><?php _e('Time', WCPGSK_DOMAIN) ; ?></option>
						<option value="password"><?php _e('Password', WCPGSK_DOMAIN) ; ?></option>
						<option value="number"><?php _e('Number', WCPGSK_DOMAIN) ; ?></option>
						<option value="integer"><?php _e('Integer', WCPGSK_DOMAIN) ; ?></option>
						<option value="float"><?php _e('Float', WCPGSK_DOMAIN) ; ?></option>
						<option value="custom1"><?php _e('Custom1', WCPGSK_DOMAIN) ; ?></option>
						<option value="custom2"><?php _e('Custom2', WCPGSK_DOMAIN) ; ?></option>
						<option value="custom3"><?php _e('Custom3', WCPGSK_DOMAIN) ; ?></option>
					</select>
				</td>
			</tr>
			<tr class="field_option field_option_select">
				<td class="label">
					<label><?php _e('Add repeat input for validation, e.g. email or password fields?', WCPGSK_DOMAIN) ; ?></label>
				</td>
				<td>
					<ul class="wcpgsk-radio-list radio horizontal"><li><label><input id="wcpgsk_add_repeat_field_0" for="wcpgsk_add_repeat_field" value="0" type="radio" checked="&quot;checked&quot;" data-checked="&quot;checked&quot;" ><?php _e('No', WCPGSK_DOMAIN) ; ?></label></li><li><label><input id="wcpgsk_add_repeat_field_1" for="wcpgsk_add_repeat_field" value="1" type="radio"><?php _e('Yes', WCPGSK_DOMAIN) ; ?></label></li></ul>	
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Minimum characters', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Minlength attribute of the input tag. Value has to be a number. (In the Html5 specification but not supported by most browsers)', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_minlength" value="" />
				</td>
			</tr>

			</table>
			</form>

			</div>

			<div id="wcpgsk_dialog_form_textarea" title="Configure Textarea Field" class="wcpgsk_dialog_forms">
			<p class="validateTips"><?php echo $validateTip ; ?></p>
			<form for="wcpgsk_dlg_form_textarea">
			<table class="wcpgsfieldkconfig">
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Textarea rows', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_rows" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Textarea cols', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_cols" value="" />
				</td>
			</tr>
			</table>
			</form>

			</div>

			<div id="wcpgsk_dialog_form_date" title="Configure Date Field" class="wcpgsk_dialog_forms">
			<p class="validateTips"><?php echo $validateTip ; ?></p>
			<form for="wcpgsk_dlg_form_date">
			<table class="wcpgsfieldkconfig">
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Minimum offset in days', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number for dynamic calculation or a fix date in the format specified in Date format', WCPGSK_DOMAIN) ; ?></p>					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_mindays" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Maximum offset in days', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number for dynamic calculation or a fix date in the format specified in Date format', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_maxdays" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Date format', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Please select a date format.', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<select for="wcpgsk_add_dateformat">
						<option value="yy/mm/dd">yyyy/mm/dd</option>
						<option value="mm/dd/yy">mm/dd/yyyy</option>
						<option value="dd/mm/yy">dd/mm/yyyy</option>
					</select>
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Exclude weekends', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('If you want to exclude weekends, please check.', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<ul class="wcpgsk-radio-list radio horizontal">
					<li><label><input id="wcpgsk_add_exweekend_0" for="wcpgsk_add_exweekend" value="0" type="radio" checked="checked" data-checked="checked" ><?php _e('No', WCPGSK_DOMAIN) ; ?></label></li>
					<li><label><input id="wcpgsk_add_exweekend_1" for="wcpgsk_add_exweekend" value="1" type="radio"><?php _e('Yes', WCPGSK_DOMAIN) ; ?></label></li>
					</ul>	
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Exclude week days', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Please specify weekdays to exclude using integers separated by coma.', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_daysexcluded" style="width:100%" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Exclude dates', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Please specify dates like holidays you want to exclude from selection separated by coma.', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_datesexcluded" style="width:100%" />
				</td>
			</tr>
			</table>
			</form>
			</div>

			<div id="wcpgsk_dialog_form_time" title="Configure Time Field" class="wcpgsk_dialog_forms">
			<p class="validateTips"><?php echo $validateTip ; ?></p>
			<form for="wcpgsk_dlg_form_time">
			<table class="wcpgsfieldkconfig">
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Minimum hour', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_minhour" value="" />
				</td>
			</tr>

			<tr class="field_option">
				<td class="label">
					<label><?php _e('Maximum hour', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_maxhour" value="" />
				</td>
			</tr>

			<tr class="field_option">
				<td class="label">
					<label><?php _e('Hour steps', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_hoursteps" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Minute steps', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_minutesteps" value="" />
				</td>
			</tr>
			</table>
			</form>

			</div>

			<div id="wcpgsk_dialog_form_number" title="Configure Number Field" class="wcpgsk_dialog_forms">
			<p class="validateTips"><?php echo $validateTip ; ?></p>
			<form for="wcpgsk_dlg_form_number">
			<table class="wcpgsfieldkconfig">
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Default value', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_value" value="" />
				</td>
			</tr>

			<tr class="field_option">
				<td class="label">
					<label><?php _e('Default upper range value', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
				</td>
				<td>
					<input type="text" for="wcpgsk_add_rangemax" value="" />
				</td>
			</tr>


			<tr class="field_option">
				<td class="label">
					<label><?php _e('Minimum value', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_minvalue" value="" />
				</td>
			</tr>

			<tr class="field_option">
				<td class="label">
					<label><?php _e('Maximum value', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_maxvalue" />
				</td>
			</tr>

			<tr class="field_option">
				<td class="label">
					<label><?php _e('Number step', WCPGSK_DOMAIN) ; ?></label>
					<p><?php _e('Value has to be a number', WCPGSK_DOMAIN) ; ?></p>
					
				</td>
				<td>
					<input type="text" for="wcpgsk_add_numstep" value="" />
				</td>
			</tr>
			<tr class="field_option">
				<td class="label">
					<label><?php _e('Presentation', WCPGSK_DOMAIN) ; ?></label>
					
				</td>
				<td>
					<select for="wcpgsk_add_numpres">
						<option value="false"><?php _e('Default', WCPGSK_DOMAIN) ; ?></option>
						<option value="true"><?php _e('Range with minimum and maximum', WCPGSK_DOMAIN) ; ?></option>
						<option value="min"><?php _e('Range with minimum', WCPGSK_DOMAIN) ; ?></option>
						<option value="max"><?php _e('Range with maximum', WCPGSK_DOMAIN) ; ?></option>
					</select>
				</td>
			</tr>
			</table>
			</form>

			</div>
			
			
			<?php
			do_action( 'wcpgsk_settings_form_field_dialogs', $options );								
			
			echo '</div>';
			echo '<!--unit test options page end-->';
			
		}
		
		/**
		 * Helper function to order array.
		 *
		 * @access public
		 * @param array $a
		 * @param array $b
		 * @since 1.1.0
		 * @return $input (validated)
		 */		
		public function compareFieldOrder($a, $b) {
			if ($a['order'] == $b['order']) {
				return 0;
			}
			return ($a['order'] < $b['order']) ? -1 : 1;
		}		
		
		/**
		 * Our Validation for submitted Settings Page.
		 *
		 * @access public
		 * @since 1.1.0
		 * @return $input (validated)
		 */		
		public function wcpgsk_options_validate( $input ) {
			global $woocommerce;
			//$wcpgsk_options = get_option( 'wcpgsk_settings' );
			
			if ( empty($input['cart']['minitemscart']) ) $input['cart']['minitemscart'] = 0;
			if ( !ctype_digit($input['cart']['minitemscart']) ) $input['cart']['minitemscart'] = 0;
			if ( empty($input['cart']['maxitemscart'] ) ) $input['cart']['maxitemscart'] = 0;
			if ( !ctype_digit($input['cart']['maxitemscart']) ) $input['cart']['maxitemscart'] = 0;
			$mincart = $input['cart']['minitemscart'];
			$maxcart = $input['cart']['maxitemscart'];
			if ($mincart > $maxcart && $maxcart > 0) $input['cart']['minitemscart'] = 1;

			if ( empty($input['cart']['minqtycart']) ) $input['cart']['minqtycart'] = 0;
			if ( !ctype_digit($input['cart']['minqtycart']) ) $input['cart']['minqtycart'] = 0;
			if ( empty($input['cart']['maxqtycart'] ) ) $input['cart']['maxqtycart'] = 0;
			if ( !ctype_digit($input['cart']['maxqtycart']) ) $input['cart']['maxqtycart'] = 0;
			$minqtycart = $input['cart']['minqtycart'];
			$maxqtycart = $input['cart']['maxqtycart'];
			if ($minqtycart > $maxqtycart && $maxqtycart > 0) $input['cart']['minqtycart'] = 1;


			
			//@todo:could be a string, but not vital
			if (empty($input['checkoutform']['mindate']) || !ctype_digit($input['checkoutform']['mindate'])) $input['checkoutform']['mindate'] = 0;
			if (empty($input['checkoutform']['maxdate']) || !ctype_digit($input['checkoutform']['maxdate'])) $input['checkoutform']['maxdate'] = 365;
			
			$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'billing_' );
			$field_order = 1;	
			foreach ($checkout_fields as $key => $field) : 
				if (empty($input['woofields']['order_' . $key]) || !ctype_digit($input['woofields']['order_' . $key])) $input['woofields']['order_' . $key] = $field_order;
				$field_order++;
			endforeach;

			$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'shipping_' );
			$field_order = 1;	
			foreach ($checkout_fields as $key => $field) : 
				if (empty($input['woofields']['order_' . $key]) || !ctype_digit($input['woofields']['order_' . $key])) $input['woofields']['order_' . $key] = $field_order;
				$field_order++;
			endforeach;

			if ( isset( $input['process']['fastcart'] ) && $input['process']['fastcart'] == 1 && $input['process']['fastcheckout'] == 1) $input['process']['fastcheckout'] = 0;
			
			//$product_types = array('variation', 'variable', 'grouped', 'external', 'simple');
			$product_types = array('variation', 'variable', 'external', 'simple');
			foreach($product_types as $type) :
				if ( empty($input['cart']['maxqty_' . $type]) ) $input['cart']['maxqty_' . $type] = 0;
				if ( !ctype_digit($input['cart']['maxqty_' . $type]) ) $input['cart']['maxqty_' . $type] = 0;

				if ( empty($input['cart']['stepqty_' . $type]) ) $input['cart']['stepqty_' . $type] = 0;
				if ( !ctype_digit($input['cart']['stepqty_' . $type]) ) $input['cart']['stepqty_' . $type] = 0;
				
				if ( empty($input['cart']['minqty_' . $type]) ) $input['cart']['minqty_' . $type] = 0;
				if ( !ctype_digit($input['cart']['minqty_' . $type]) ) $input['cart']['minqty_' . $type] = 0;
				//very basic consistency check for quantity settings
				if ( $input['cart']['minqty_' . $type] > $input['cart']['maxqty_' . $type] && $input['cart']['maxqty_' . $type] > 0 ) $input['cart']['minqty_' . $type] = 1;
				if ($input['cart']['maxqty_' . $type] == 1) $input['cart'][$type . 'productnoqty'] = 1;
			endforeach;
			$input = apply_filters('wcpgsk_validate_settings', $input);
			return $input;
		}
		
		/**
		 * Our filter for customer editing billing and shipping address.
		 *
		 * @access public
		 * @param array $address
		 * @since 1.5.1
		 * @return array $address (processed)
		 * @TODO elaborate on this and billing and shipping custom fields, WooCommerce needs the country field present but nothing else... We have ways to improve this.
		 */						
		public function wcpgsk_address_to_edit($address) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			$field_order = 1;	
			$locale = get_option( 'wcpgsk_locale', array() );
			$wc_locale = $this->wcpgsk_get_country_locale();

			foreach ($address as $key => $field) :
				$address[$key]['order'] = ((!empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);			
				
				if ( isset($options['woofields']['remove_' . $key]) && $options['woofields']['remove_' . $key] == 1) :
					$address[$key]['custom_attributes'] = array('style' => 'display:none');
					$address[$key]['label'] = '';
					$address[$key]['required'] = false;

				//elseif ( $key == 'billing_email_validator' ) :
					//$address[$key]['custom_attributes'] = array('style' => 'display:none');
					//$address[$key]['label'] = '';
					//$address[$key]['required'] = false;
				else :
					
					$is_billing = strpos($key, 'billing_');
					$cc = false;
					$wc_field_handle = false;
					if ( $is_billing !== false ) :
						$wc_field_handle = str_replace('billing_', '', $key);
						if ( isset($address['billing_country']['value']) ) :
							$cc = $address['billing_country']['value'];
						else :
							$cc = $this->wcpgsk_load_address_value($key);
						endif;
					else : 
						$wc_field_handle = str_replace('shipping_', '', $key);					
						if ( isset($address['shipping_country']['value']) ) :
							$cc = $address['shipping_country']['value'];
						else :
							$cc = $this->wcpgsk_load_address_value($key);
						endif;
						
					endif;
					if ( $cc && $wc_field_handle && isset($options['checkoutform']['default_' . $wc_field_handle]) && $options['checkoutform']['default_' . $wc_field_handle] ) :
						$address[$key] = $this->wcpgsk_config_locale_field($address[$key], $wc_field_handle, $cc, $wc_locale, $locale);
					else :
						if ( isset($options['woofields']['label_' . $key]) ) :	
							$address[$key]['label'] = __( $options['woofields']['label_' . $key], WCPGSK_DOMAIN );
						endif;
						if ( isset($options['woofields']['placeholder_' . $key]) ) :	
							$address[$key]['placeholder'] = __( $options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN );
						endif;
						if ( isset($options['woofields']['required_' . $key]) ) :
							$address[$key]['required'] = ((isset($options['woofields']['required_' . $key])) ? $options['woofields']['required_' . $key] : ( isset($address[$key]['required']) ? $address[$key]['required'] : false ) );
						endif;
						//if (!empty($options['woofields']['class_' . $key])) {
						if (!empty($address[$key]['class']) && is_array($address[$key]['class'])) :
							$address[$key]['class'][0] = 'form-row-wide';
						else :
							$address[$key]['class'] = array ('form-row-wide');
						endif;
					endif;
				endif;
				$field_order++;				
			endforeach;
			
			uasort($address, array($this, "compareFieldOrder"));						
			return $address;
		}
		
		public function wcpgsk_load_address_value($key) {
			global $woocommerce, $current_user;
			get_currentuserinfo();
			
			$value = get_user_meta( get_current_user_id(), $key, true );

			if ( ! $value ) {
				switch( $key ) {
					case 'billing_email' :
					case 'shipping_email' :
						$value = $current_user->user_email;
					break;
					case 'billing_country' :
					case 'shipping_country' :
						$value = $woocommerce->countries->get_base_country();
					break;
					case 'billing_state' :
					case 'shipping_state' :
						$value = $woocommerce->countries->get_base_state();
					break;
				}
			}
			return $value;		
		}
		
		public function wcpgsk_config_locale_field($address_field, $wc_field_handle, $countrycode, $wc_locale, $locale) {

			$default_required = isset($locale['default']['required_' . $wc_field_handle]) ? $locale['default']['required_' . $wc_field_handle] : '0';
			$wc_localerequired = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode][$wc_field_handle]) && isset($wc_locale[$countrycode][$wc_field_handle]['required']) ? ( $wc_locale[$countrycode][$wc_field_handle]['required'] ? '1' : '0' ) : ( isset($wc_locale['default'][$wc_field_handle]['required']) && $wc_locale['default'][$wc_field_handle]['required'] ? '1' : $default_required );
			$locale[$countrycode]['required_' . $wc_field_handle] = isset($locale[$countrycode]['required_' . $wc_field_handle]) ? $locale[$countrycode]['required_' . $wc_field_handle] : $wc_localerequired;
			$address_field['required'] = $locale[$countrycode]['required_' . $wc_field_handle] ? true : false;
			
			$default_label = isset($locale['default']['label_' . $wc_field_handle]) ? $locale['default']['label_' . $wc_field_handle] : '';
			$wc_localelabel = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode][$wc_field_handle]) && isset($wc_locale[$countrycode][$wc_field_handle]['label']) ? $wc_locale[$countrycode][$wc_field_handle]['label'] : ( isset($wc_locale['default'][$wc_field_handle]['label']) ? $wc_locale['default'][$wc_field_handle]['label'] : $default_label );						
			$locale[$countrycode]['label_' . $wc_field_handle] = isset($locale[$countrycode]['label_' . $wc_field_handle]) ? $locale[$countrycode]['label_' . $wc_field_handle] : $wc_localelabel;
			$address_field['label'] = __($locale[$countrycode]['label_' . $wc_field_handle], WCPGSK_DOMAIN);
			
			$default_placeholder = isset($locale['default']['placeholder_' . $wc_field_handle]) ? $locale['default']['placeholder_' . $wc_field_handle] : '';
			$wc_localeplaceholder = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode][$wc_field_handle]) && isset($wc_locale[$countrycode][$wc_field_handle]['placeholder']) ? $wc_locale[$countrycode][$wc_field_handle]['placeholder'] : ( isset($wc_locale['default'][$wc_field_handle]['placeholder']) ? $wc_locale['default'][$wc_field_handle]['placeholder'] : $default_placeholder );
			$locale[$countrycode]['placeholder_' . $wc_field_handle] = isset($locale[$countrycode]['placeholder_' . $wc_field_handle]) ? $locale[$countrycode]['placeholder_' . $wc_field_handle] : $wc_localeplaceholder;
			$address_field['placeholder'] = __($locale[$countrycode]['placeholder_' . $wc_field_handle], WCPGSK_DOMAIN);

			$default_hidden = isset($locale['default']['hidden_' . $wc_field_handle]) ? $locale['default']['hidden_' . $wc_field_handle] : '0';
			$wc_localehidden = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode][$wc_field_handle]) && isset($wc_locale[$countrycode][$wc_field_handle]['hidden']) ? ( $wc_locale[$countrycode][$wc_field_handle]['hidden'] ? '1' : '0' ) : ( isset($wc_locale['default'][$wc_field_handle]['hidden']) && $wc_locale['default'][$wc_field_handle]['hidden'] ? '1' : $default_hidden );
			$locale[$countrycode]['hidden_' . $wc_field_handle] = isset($locale[$countrycode]['hidden_' . $wc_field_handle]) ? $locale[$countrycode]['hidden_' . $wc_field_handle] : $wc_localehidden;
			$address_field['hidden'] = $locale[$countrycode]['hidden_' . $wc_field_handle] == '1' ? true : false;			
			return $address_field;
		}
		
		/**
		 * Filter fields for admin user page.
		 *
		 * @access public
		 * @param array $show_fields
		 * @since 1.5.3
		 * @changed 1.6.2
		 * @return array $show_fields (processed)
		 */						
		public function wcpgsk_customer_meta_fields($show_fields) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			$field_order = 1;	
			$address = $show_fields['billing']['fields'];
			foreach ($address as $key => $field) :
				$address[$key]['order'] = ((!empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);			
				if ( isset($options['woofields']['remove_' . $key]) && $options['woofields']['remove_' . $key] == 1) :
					unset($address[$key]);
				else :
					if ( isset($options['woofields']['label_' . $key]) && !empty($options['woofields']['label_' . $key]) ) :
						$address[$key]['label'] = __($options['woofields']['label_' . $key], WCPGSK_DOMAIN);
					elseif ( empty($options['woofields']['label_' . $key]) ) :
						$address[$key]['label'] = '';
					endif;
					if ( isset($options['woofields']['placeholder_' . $key]) && !empty($options['woofields']['placeholder_' . $key]) ) :
						$address[$key]['description'] = __($options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN);
					endif;
				endif;
				
				$field_order++;				
			endforeach;
			
			uasort($address, array($this, "compareFieldOrder"));						
			$show_fields['billing']['fields'] = $address;

			$field_order = 1;	
			$address = $show_fields['shipping']['fields'];
			foreach ($address as $key => $field) :
				$address[$key]['order'] = ((!empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);			
				if ( isset($options['woofields']['remove_' . $key]) && $options['woofields']['remove_' . $key] == 1) :
					unset($address[$key]);
				else :
					if ( isset($options['woofields']['label_' . $key]) && !empty($options['woofields']['label_' . $key]) ) :
						$address[$key]['label'] = __($options['woofields']['label_' . $key], WCPGSK_DOMAIN);
					elseif ( empty($options['woofields']['label_' . $key]) ) : 
						$address[$key]['label'] = '';
					endif;
					if ( isset($options['woofields']['placeholder_' . $key]) && !empty($options['woofields']['placeholder_' . $key]) ) :
						$address[$key]['description'] = __($options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN);
					endif;				
				endif;
				$field_order++;				
			endforeach;
			
			uasort($address, array($this, "compareFieldOrder"));						

			$show_fields['shipping']['fields'] = $address;
			return $show_fields;
		}
		
		/**
		 * Handle zip/postcode if postcode field has been removed.
		 *
		 * @access public
		 * @param string $postval
		 * @since 1.5.3
		 * @return string $postval (processed)
		 */						
		public function wcpgsk_process_myaccount_field_billing_postcode($postval) {
			$options = get_option( 'wcpgsk_settings' );
			if ( isset($options['woofields']['remove_billing_postcode']) && $options['woofields']['remove_billing_postcode'] == 1 && isset($_POST['billing_country']) && !empty($_POST['billing_country']) ) :
				$country = $_POST['billing_country'];
				switch ( $country ) :
					case "GB" :
						$postval = 'bfpo0000';
					break;
					case "US" :
						$postval = '00000-0000';
					break;
					case "CH" :
						$postval = '0000';
					break;
					default:
						$postval = '0000';
					break;
				endswitch;
			endif;
			return $postval;
		}
		
		/**
		 * Handle zip/postcode if postcode field has been removed.
		 *
		 * @access public
		 * @param string $postval
		 * @since 1.5.3
		 * @return string $postval (processed)
		 */						
		public function wcpgsk_process_myaccount_field_shipping_postcode($postval) {
			$options = get_option( 'wcpgsk_settings' );
			if ( isset($options['woofields']['remove_shipping_postcode']) && $options['woofields']['remove_shipping_postcode'] == 1 && isset($_POST['shipping_country']) && !empty($_POST['shipping_country']) ) :
				$country = $_POST['shipping_country'];
				switch ( $country ) :
					case "GB" :
						$postval = 'bfpo0000';
					break;
					case "US" :
						$postval = '00000-0000';
					break;
					case "CH" :
						$postval = '0000';
					break;
					default:
						$postval = '0000';
					break;
				endswitch;
			endif;
			return $postval;
		}
		
		/**
		 * Assure that templates provide our WCPGSK Funtionality correctly.
		 *
		 * @access public
		 * @param string $template_name
		 * @param string $template_path
		 * @param string $located
		 * @param mixed $args
		 * @since 1.5.2
		 * @return array $fields (processed)
		 */						
		function wcpgsk_after_template_part( $template_name, $template_path, $located, $args ) {
			if ( $template_name == 'myaccount/form-edit-address.php' ) :
				wcpgsk_after_checkout_form($template_name);
			endif;
		}
		
		/**
		 * Update our order shipping address.
		 *
		 * @access public
		 * @since 1.5.3
		 * @output Settings page
		 */		
		public function wcpgsk_order_formatted_shipping_address($address) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			$field_order = 1;
			$new_address = array();
			if ( !isset($address['first_name']) ) $address['first_name'] = '';
			if ( !isset($address['last_name']) ) $address['last_name'] = '';
			if ( !isset($address['company']) ) $address['company'] = '';
			if ( !isset($address['address_1']) ) $address['address_1'] = '';
			if ( !isset($address['address_2']) ) $address['address_2'] = '';
			if ( !isset($address['city']) ) $address['city'] = '';
			if ( !isset($address['state']) ) $address['state'] = '';
			if ( !isset($address['postcode']) ) $address['postcode'] = '';
			if ( !isset($address['country']) ) $address['country'] = '';
			
			foreach ($address as $key => $field) :
				$keycheck = 'shipping_' . $key;
				
				if ( isset($options['woofields']['remove_' . $keycheck]) && $options['woofields']['remove_' . $keycheck] == 1) :
					$new_address[$key]['order'] = ((!empty($options['woofields']['order_' . $keycheck]) && ctype_digit($options['woofields']['order_' . $keycheck])) ? $options['woofields']['order_' . $keycheck] : $field_order);			
					$new_address[$key]['value'] = '';
				else :
					$new_address[$key]['order'] = ((!empty($options['woofields']['order_' . $keycheck]) && ctype_digit($options['woofields']['order_' . $keycheck])) ? $options['woofields']['order_' . $keycheck] : $field_order);			
					$new_address[$key]['value'] = $address[$key];
				endif;
				$field_order++;				
			endforeach;			
			uasort($new_address, array($this, "compareFieldOrder"));
			$address = array();
			foreach ($new_address as $key => $field) :
				$address[$key] = $new_address[$key]['value'];
			endforeach;
			return $address;
		}
		
		/**
		 * Update our order billing address.
		 *
		 * @access public
		 * @since 1.5.3
		 * @output Settings page
		 */		
		public function wcpgsk_order_formatted_billing_address($address) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			$field_order = 1;
			$new_address = array();
			if ( !isset($address['first_name']) ) $address['first_name'] = '';
			if ( !isset($address['last_name']) ) $address['last_name'] = '';
			if ( !isset($address['company']) ) $address['company'] = '';
			if ( !isset($address['address_1']) ) $address['address_1'] = '';
			if ( !isset($address['address_2']) ) $address['address_2'] = '';
			if ( !isset($address['city']) ) $address['city'] = '';
			if ( !isset($address['state']) ) $address['state'] = '';
			if ( !isset($address['postcode']) ) $address['postcode'] = '';
			if ( !isset($address['country']) ) $address['country'] = '';
			
			foreach ($address as $key => $field) :
				$keycheck = 'billing_' . $key;
				
				if ( isset($options['woofields']['remove_' . $keycheck]) && $options['woofields']['remove_' . $keycheck] == 1) :
					$new_address[$key]['order'] = ((!empty($options['woofields']['order_' . $keycheck]) && ctype_digit($options['woofields']['order_' . $keycheck])) ? $options['woofields']['order_' . $keycheck] : $field_order);			
					$new_address[$key]['value'] = '';
				else :
					$new_address[$key]['order'] = ((!empty($options['woofields']['order_' . $keycheck]) && ctype_digit($options['woofields']['order_' . $keycheck])) ? $options['woofields']['order_' . $keycheck] : $field_order);			
					$new_address[$key]['value'] = $address[$key];
				endif;
				$field_order++;				
			endforeach;			
			uasort($new_address, array($this, "compareFieldOrder"));
			$address = array();
			foreach ($new_address as $key => $field) :
				$address[$key] = $new_address[$key]['value'];
			endforeach;
			return $address;
		}
		
		/**
		 * Our filter to add billing fields.
		 *
		 * @access public
		 * @param array $fields
		 * @since 1.1.0
		 * @return array $fields (processed)
		 */						
		function add_billing_custom_fields( $fields ) {
			$options = get_option( 'wcpgsk_settings' );
			$options['woofields']['label_billing_email_validator'] = !empty($options['woofields']['label_billing_email_validator']) ? $options['woofields']['label_billing_email_validator'] : '';
			$options['woofields']['placehoder_billing_email_validator'] = !empty($options['woofields']['placehoder_billing_email_validator']) ? $options['woofields']['placehoder_billing_email_validator'] : '';
			$options['woofields']['required_billing_email_validator'] = isset($options['woofields']['required_billing_email_validator']) ? $options['woofields']['required_billing_email_validator'] : 1;
			if ( !is_user_logged_in() ) :			
				if (isset($options['checkoutform']['billingemailvalidator']) && $options['checkoutform']['billingemailvalidator'] == 1) {
					$fields['billing_email_validator'] = array(
						'type'				=> 'text',
						'label' 			=> __( $options['woofields']['label_billing_email_validator'], WCPGSK_DOMAIN ),
						'placeholder' 		=> __( $options['woofields']['placehoder_billing_email_validator'], WCPGSK_DOMAIN ),
						'required' 			=> (($options['woofields']['required_billing_email_validator'] == 1) ? true : false),
						//not necessary... 'validate'			=> 'email'
					);
				}
			endif;
			if (isset($options['woofields']['billing']) && is_array($options['woofields']['billing'])) {
				foreach($options['woofields']['billing'] as $customkey => $customconfig) {
					//$fieldrepeater = null;
					$fields[$customkey] = $this->createCustomStandardField($customkey, 'billing', $options['woofields']['type_' . $customkey]);
				}
			}
			$field_order = 1;
			foreach ($fields as $key => $field) :
				$fields[$key]['order'] = ((!empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);			
			
				if ( isset($options['woofields']['remove_' . $key]) && $options['woofields']['remove_' . $key] == 1) :
					$fields[$key]['required'] = false;
				endif;
				if ( isset($options['woofields']['required_' . $key]) && $options['woofields']['required_' . $key] != 1) :
					$fields[$key]['required'] = false;
				endif;
				$field_order++;
			endforeach;
			uasort($fields, array($this, "compareFieldOrder"));						
			return $fields;
		}

		/**
		 * Our filter to add shipping fields.
		 *
		 * @access public
		 * @param array $fields
		 * @since 1.1.0
		 * @return array $fields (processed)
		 */						
		function add_shipping_custom_fields( $fields ) {
			$options = get_option( 'wcpgsk_settings' );
			
			if (isset($options['woofields']['shipping']) && is_array($options['woofields']['shipping'])) {
				foreach($options['woofields']['shipping'] as $customkey => $customconfig) {
					$fields[$customkey] = $this->createCustomStandardField($customkey, 'shipping', $options['woofields']['type_' . $customkey]);
				}
			}
			$field_order = 1;
			foreach ($fields as $key => $field) :
				$fields[$key]['order'] = ((!empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);			
				if ( isset($options['woofields']['remove_' . $key]) && $options['woofields']['remove_' . $key] == 1) :
					$fields[$key]['required'] = false;
				endif;
				$field_order++;
			endforeach;
			uasort($fields, array($this, "compareFieldOrder"));						
			
			
			return $fields;
		}
		
		/**
		 * Our filter for billing fields.
		 *
		 * @access public
		 * @param array $fields
		 * @since 1.1.0
		 * @return array $fields (processed)
		 */						
		public function wcpgsk_admin_billing_fields($fields) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			$billing_fields = array();
			$field_order = 1;	
			$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'billing_' );
			$defchecked = __('Default: checked', WCPGSK_DOMAIN);
			$defunchecked = __('Default: unchecked', WCPGSK_DOMAIN);

			foreach ($checkout_fields as $key => $field) :
				$checkout_fields[$key]['required'] = isset( $checkout_fields[$key]['required'] ) ?  $checkout_fields[$key]['required'] : 0;
				$checkout_fields[$key]['label'] = !empty($checkout_fields[$key]['label']) ? $checkout_fields[$key]['label'] : '';
				$checkout_fields[$key]['placeholder'] = !empty($checkout_fields[$key]['placeholder']) ? $checkout_fields[$key]['placeholder'] : '';
				$checkout_fields[$key]['fieldkey'] = $key;
				$checkout_fields[$key]['displaylabel'] = !empty($field['label']) ? __($field['label'], WCPGSK_DOMAIN) : $key;
				$checkout_fields[$key]['order'] = ((!empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);
				$checkout_fields[$key]['placeholder'] = ((!empty($options['woofields']['placeholder_' . $key])) ? $options['woofields']['placeholder_' . $key] : $checkout_fields[$key]['placeholder']);
				$checkout_fields[$key]['label'] = ((!empty($options['woofields']['label_' . $key])) ? $options['woofields']['label_' . $key] : $checkout_fields[$key]['label']);
				//$checkout_fields[$key]['label'] = ((isset($options['woofields']['label_' . $key])) ? $options['woofields']['label_' . $key] : $checkout_fields[$key]['label']);
				//before required defreq
				$checkout_fields[$key]['defreq'] = ((isset($checkout_fields[$key]['required']) && $checkout_fields[$key]['required'] == 1) ? $defchecked : $defunchecked);
				$checkout_fields[$key]['required'] = ((isset($options['woofields']['required_' . $key])) ? $options['woofields']['required_' . $key] : $checkout_fields[$key]['required']);
				$checkout_fields[$key]['hideorder'] = ((isset($options['woofields']['hideorder_' . $key])) ? $options['woofields']['hideorder_' . $key] : 0);
				$checkout_fields[$key]['type'] = ((!empty($options['woofields']['type_' . $key])) ? $options['woofields']['type_' . $key] : ((!empty($checkout_fields[$key]['type'])) ? $checkout_fields[$key]['type'] : 'text') );
				$checkout_fields[$key]['classsel'] = ((!empty($options['woofields']['class_' . $key])) ? $options['woofields']['class_' . $key] : ((isset($checkout_fields[$key]['class'])) ? $checkout_fields[$key]['class'][0] : 'form-row-wide') );
				$checkout_fields[$key]['settings'] = ((!empty($options['woofields']['settings_' . $key])) ? $options['woofields']['settings_' . $key] : '' );
				$field_order++;
			endforeach;

			uasort($checkout_fields, array($this, "compareFieldOrder"));						

			foreach ($checkout_fields as $key => $field) : 
				//$fieldLabel = $field['displaylabel'];
				$fieldkey = str_replace('billing_', '', $key);
				if (isset($fields[$fieldkey])): 
					//@TODO: this will never return nothing as $fields does not hold any keys according to $fieldkey
					$billing_fields[$fieldkey] = $fields[$fieldkey];
				else:
					//if ($key != 'billing_email_validator' && $field['hideorder'] == 0) :
					if ($key != 'billing_email_validator') :
						if ( isset( $options['woofields']['billing'][$key]['custom_' . $key] ) && $options['woofields']['billing'][$key]['custom_' . $key] ) :
							$configField = $this->createCustomStandardField($key, 'billing', $options['woofields']['type_' . $key]);
							if (isset($configField['class'])) unset($configField['class']);
							if (isset($configField['clear'])) unset($configField['clear']);
							if (isset($configField['placeholder'])) unset($configField['placeholder']);
							if (isset($configField['required'])) unset($configField['required']);
							if (isset($configField['validate'])) unset($configField['validate']);
							if (isset($configField['custom_attributes'])) unset($configField['custom_attributes']);

							if (!isset($configField['label'])) $configField['label'] = __($checkout_fields[$key]['label'], WCPGSK_DOMAIN);
							
							//show select values as text in this case
							if ( !empty($configField['type']) && $configField['type'] == 'select' ) $configField['type'] = 'text';
							//textarea is not recognized by woocommerce in order billing address context
							if ( !empty($configField['type']) && $configField['type'] == 'textarea' ) $configField['type'] = 'text';
							//if ( !empty($configField['type']) && $configField['type'] == 'fileupload' ) $configField['type'] = 'text';
							//if ($field['hideorder'] == 0)
								$configField['show'] = true;
							//else
							//	$configField['show'] = false;
								
							$billing_fields[$fieldkey] = $configField;
						endif;
					endif;
				endif;
				
			endforeach;
			return apply_filters( 'wcpgsk_admin_billing_fields', $billing_fields );	
		}

		/**
		 * Our filter for shipping fields.
		 *
		 * @access public
		 * @param array $fields
		 * @since 1.1.0
		 * @return array $fields (processed)
		 */						
		public function wcpgsk_admin_shipping_fields($fields) {
			global $woocommerce;
			$defchecked = __('Default: checked', WCPGSK_DOMAIN);
			$defunchecked = __('Default: unchecked', WCPGSK_DOMAIN);
			$options = get_option( 'wcpgsk_settings' );
			$shipping_fields = array();
			$field_order = 1;	
			$checkout_fields = $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'shipping_' );
			$field_order = 1;

			foreach ($checkout_fields as $key => $field) :
				$checkout_fields[$key]['required'] = isset( $checkout_fields[$key]['required'] ) ?  $checkout_fields[$key]['required'] : 0;
				$checkout_fields[$key]['label'] = !empty($checkout_fields[$key]['label']) ? $checkout_fields[$key]['label'] : '';
				$checkout_fields[$key]['placeholder'] = !empty($checkout_fields[$key]['placeholder']) ? $checkout_fields[$key]['placeholder'] : '';

				$checkout_fields[$key]['fieldkey'] = $key;
				$checkout_fields[$key]['displaylabel'] = !empty($field['label']) ? __($field['label'], WCPGSK_DOMAIN) : $key;
				$checkout_fields[$key]['order'] = ((!empty($options['woofields']['order_' . $key]) && ctype_digit($options['woofields']['order_' . $key])) ? $options['woofields']['order_' . $key] : $field_order);
				$checkout_fields[$key]['placeholder'] = ((!empty($options['woofields']['placeholder_' . $key])) ? $options['woofields']['placeholder_' . $key] : $checkout_fields[$key]['placeholder']);
				$checkout_fields[$key]['label'] = ((!empty($options['woofields']['label_' . $key])) ? $options['woofields']['label_' . $key] : $checkout_fields[$key]['label']);
				//before required defreq
				$checkout_fields[$key]['defreq'] = ((isset($checkout_fields[$key]['required']) && $checkout_fields[$key]['required'] == 1) ? $defchecked : $defunchecked);
				$checkout_fields[$key]['required'] = ((isset($options['woofields']['required_' . $key])) ? $options['woofields']['required_' . $key] : $checkout_fields[$key]['required']);
				$checkout_fields[$key]['hideorder'] = ((isset($options['woofields']['hideorder_' . $key])) ? $options['woofields']['hideorder_' . $key] : 0);
				$checkout_fields[$key]['type'] = ((!empty($options['woofields']['type_' . $key])) ? $options['woofields']['type_' . $key] : ((!empty($checkout_fields[$key]['type'])) ? $checkout_fields[$key]['type'] : 'text') );
				
				$checkout_fields[$key]['classsel'] = ((!empty($options['woofields']['class_' . $key])) ? $options['woofields']['class_' . $key] : ((isset($checkout_fields[$key]['class'])) ? $checkout_fields[$key]['class'][0] : 'form-row-wide') );
				$checkout_fields[$key]['settings'] = ((!empty($options['woofields']['settings_' . $key])) ? $options['woofields']['settings_' . $key] : '' );
				$field_order++;
			endforeach;

			uasort($checkout_fields, array($this, "compareFieldOrder"));						

			foreach ($checkout_fields as $key => $field) : 
				//$fieldLabel = $field['displaylabel'];
				$fieldkey = str_replace('shipping_', '', $key);
				if (isset($fields[$fieldkey])): 
					$shipping_fields[$fieldkey] = $fields[$fieldkey];
				else:
					if ($key != 'shipping_email_validator') :
						if ( isset( $options['woofields']['shipping'][$key]['custom_' . $key] ) && $options['woofields']['shipping'][$key]['custom_' . $key] ) :
							$configField = $this->createCustomStandardField($key, 'shipping', $options['woofields']['type_' . $key]);
							//unset(configField['placeholder']);
							if (isset($configField['class'])) unset($configField['class']);
							if (isset($configField['clear'])) unset($configField['clear']);
							if (isset($configField['placeholder'])) unset($configField['placeholder']);
							if (isset($configField['required'])) unset($configField['required']);
							if (isset($configField['validate'])) unset($configField['validate']);
							if (isset($configField['custom_attributes'])) unset($configField['custom_attributes']);

							if (!isset($configField['label'])) $configField['label'] = __($checkout_fields[$key]['label'], WCPGSK_DOMAIN);
							//show select values as text in this case
							//show select values as text in this case
							if ( !empty($configField['type']) && $configField['type'] == 'select' ) $configField['type'] = 'text';
							//textarea is not recognized by woocommerce in order billing address context
							if ( !empty($configField['type']) && $configField['type'] == 'textarea' ) $configField['type'] = 'text';
							//if ($field['hideorder'] == 0)
								$configField['show'] = true;
							//else
							//	$configField['show'] = false;
								
							
							$shipping_fields[$fieldkey] = $configField;
						endif;
					endif;
				endif;
				
			endforeach;
			return apply_filters( 'wcpgsk_admin_shipping_fields', $shipping_fields );	
		}
		
		/**
		 * Handle WooCommerce js overwrites of field labels, placeholders, hidden and required status.
		 *
		 * @access public
		 * @param array $params
		 * @since 1.7.1
		 * @return array $params
		 */						
		public function wcpgsk_address_i18n_params($params) {
			$options = get_option( 'wcpgsk_settings' );
			$locale = get_option( 'wcpgsk_locale', array() );

			$wc_locale = $this->wcpgsk_get_country_locale();
			$jsfields = $this->wcpgsk_get_country_locale_field_selectors();
			$countrycodes = $this->wcpgsk_get_allowed_countries();
			foreach($countrycodes as $countrycode => $cn) :

				$default_postcode = isset($locale['default']['postcode_before_city']) ? $locale['default']['postcode_before_city'] : '0';
				$wc_localepostcode = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode]['postcode_before_city']) && $wc_locale[$countrycode]['postcode_before_city'] ? 1 : ( isset($wc_locale['default']['postcode_before_city']) && $wc_locale['default']['postcode_before_city'] ? 1 : $default_postcode );
				$locale[$countrycode]['postcode_before_city'] = isset($locale[$countrycode]['postcode_before_city']) ? $locale[$countrycode]['postcode_before_city'] : $wc_localepostcode;
				$wc_locale[$countrycode]['postcode_before_city'] = $locale[$countrycode]['postcode_before_city'] ? true : false;
			
				foreach ($jsfields as $fieldkey => $config) :
					if ( isset($jsfields[$fieldkey]) && !empty($jsfields[$fieldkey]) ) :
						//use wcpgsk billing fields configuration
						if ( isset($options['checkoutform']['default_' . $fieldkey]) && $options['checkoutform']['default_' . $fieldkey] ) :

							$default_label = isset($locale['default']['label_' . $fieldkey]) ? $locale['default']['label_' . $fieldkey] : '';
							$wc_localelabel = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode][$fieldkey]) && isset($wc_locale[$countrycode][$fieldkey]['label']) ? $wc_locale[$countrycode][$fieldkey]['label'] : ( isset($wc_locale['default'][$fieldkey]['label']) ? $wc_locale['default'][$fieldkey]['label'] : $default_label );
							$locale[$countrycode]['label_' . $fieldkey] = isset($locale[$countrycode]['label_' . $fieldkey]) ? $locale[$countrycode]['label_' . $fieldkey] : $wc_localelabel;
							$wc_locale[$countrycode][$fieldkey]['label'] = __($locale[$countrycode]['label_' . $fieldkey], WCPGSK_DOMAIN);
							
							$default_placeholder = isset($locale['default']['placeholder_' . $fieldkey]) ? $locale['default']['placeholder_' . $fieldkey] : '';
							$wc_localeplaceholder = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode][$fieldkey]) && isset($wc_locale[$countrycode][$fieldkey]['placeholder']) ? $wc_locale[$countrycode][$fieldkey]['placeholder'] : ( isset($wc_locale['default'][$fieldkey]['placeholder']) ? $wc_locale['default'][$fieldkey]['placeholder'] : $default_placeholder );
							$locale[$countrycode]['placeholder_' . $fieldkey] = isset($locale[$countrycode]['placeholder_' . $fieldkey]) ? $locale[$countrycode]['placeholder_' . $fieldkey] : $wc_localeplaceholder;
							$wc_locale[$countrycode][$fieldkey]['placeholder'] = __($locale[$countrycode]['placeholder_' . $fieldkey], WCPGSK_DOMAIN);

							$default_required = isset($locale['default']['required_' . $fieldkey]) ? $locale['default']['required_' . $fieldkey] : '0';
							$wc_localerequired = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode][$fieldkey]) && isset($wc_locale[$countrycode][$fieldkey]['required']) ? ( $wc_locale[$countrycode][$fieldkey]['required'] ? '1' : '0' ) : ( isset($wc_locale['default'][$fieldkey]['required']) && $wc_locale['default'][$fieldkey]['required'] ? '1' : $default_required );
							$locale[$countrycode]['required_' . $fieldkey] = isset($locale[$countrycode]['required_' . $fieldkey]) ? $locale[$countrycode]['required_' . $fieldkey] : $wc_localerequired;
							$wc_locale[$countrycode][$fieldkey]['required'] = $locale[$countrycode]['required_' . $fieldkey] == '1' ? true : false;

							$default_hidden = isset($locale['default']['hidden_' . $fieldkey]) ? $locale['default']['hidden_' . $fieldkey] : '0';
							$wc_localehidden = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode][$fieldkey]) && isset($wc_locale[$countrycode][$fieldkey]['hidden']) ? ( $wc_locale[$countrycode][$fieldkey]['hidden'] ? '1' : '0' ) : ( isset($wc_locale['default'][$fieldkey]['hidden']) && $wc_locale['default'][$fieldkey]['hidden'] ? '1' : $default_hidden );
							$locale[$countrycode]['hidden_' . $fieldkey] = isset($locale[$countrycode]['hidden_' . $fieldkey]) ? $locale[$countrycode]['hidden_' . $fieldkey] : $wc_localehidden;
							$wc_locale[$countrycode][$fieldkey]['hidden'] = $locale[$countrycode]['hidden_' . $fieldkey] == '1' ? true : false;
						
						else :
							$wc_locale[$countrycode][$fieldkey]['required'] = false;					
							if (isset($options['woofields']['required_billing_' . $fieldkey]) && $options['woofields']['required_billing_' . $fieldkey] == 1) :
								$wc_locale[$countrycode][$fieldkey]['required'] = true;
							else :
								$wc_locale[$countrycode][$fieldkey]['required'] = false;							
							endif;
							if (isset($options['woofields']['remove_billing_' . $fieldkey]) && $options['woofields']['remove_billing_' . $fieldkey] == 1) :
								$wc_locale[$countrycode][$fieldkey]['required'] = false;
								$wc_locale[$countrycode][$fieldkey]['hidden'] = true;
							endif;
							if ( isset($options['woofields']['label_billing_' . $fieldkey]) ) :
								$wc_locale[$countrycode][$fieldkey]['label'] = __($options['woofields']['label_billing_' . $fieldkey], WCPGSK_DOMAIN);
							endif;
							if ( isset($options['woofields']['placeholder_billing_' . $fieldkey]) ) :
								$wc_locale[$countrycode][$fieldkey]['placeholder'] = __($options['woofields']['placeholder_billing_' . $fieldkey], WCPGSK_DOMAIN);
							endif;
						
						endif;
						
					endif;
				endforeach;
			endforeach;
			
			if ( function_exists('WC') ) :
				return array(
					'locale'                    => json_encode( $wc_locale ),
					'locale_fields'             => json_encode( WC()->countries->get_country_locale_field_selectors() ),
					'i18n_required_text'        => esc_attr__( 'required', 'woocommerce' ),
				);		
			else :
				//locale fields exist hardcoded in wc js file for WC versions below 2.1
				$params['locale'] = json_encode( $wc_locale );
				return $params;			
			endif;
		}

		/**
		 * Handle WooCommerce js overwrites of field labels, placeholders, hidden and required status.
		 * 
		 *
		 * @access public
		 * @param array $locale_fields
		 * @since 1.7.1
		 * @return array $locale_fields
		 */								
		public function wcpgsk_country_locale_field_selectors($locale_fields) {
			$options = get_option('wcpgsk_settings');
			foreach($locale_fields as $field => $selectors) :
				$selarray = array();
				if ( isset($options['checkoutform']['default_' . $field]) && $options['checkoutform']['default_' . $field] ) :
					$selarray[] = '#billing_' . $field . '_field';
					$selarray[] = '#shipping_' . $field . '_field';					
				else :
					$selarray[] = '#no_wc_billing_' . $field . '_handle';
					$selarray[] = '#no_wc_shipping_' . $field . '_handle';
				endif;
				$locale_fields[$field] = implode(', ', $selarray);
			endforeach;
			return $locale_fields;
		}
		
		/**
		 * Our filter for billing fields.
		 *
		 * @access public
		 * @param array $fields
		 * @since 1.1.0
		 * @modified 1.7.1
		 * @return array $fields (processed)
		 */						
		public function wcpgsk_checkout_fields_billing($fields) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			
			$field_order = 1;	
			
			$orderfields = array();
			$lastClass = array();
			foreach ($fields['billing'] as $key => $field) {
				if (isset($options['woofields']['remove_' . $key]) && $options['woofields']['remove_' . $key] == 1) {
					if ( $key == 'billing_country' ) :
						$countries = $woocommerce->countries->get_allowed_countries();
						if ( isset( $countries ) && is_array( $countries ) && count( $countries ) == 1 ) :
							$fields['billing'][$key]['class'] = array('hidecountry');
						elseif ( isset( $countries ) && is_array( $countries ) && count( $countries ) > 2 ) :
							//incorrect admin setting, do nothing
						endif;						
					else :
						unset($fields['billing'][$key]);					
					endif;
				}
				else {
				
					$orderfields[$key] = $fields['billing'][$key];
					
					if ( isset($options['woofields']['settings_' . $key]) && $options['woofields']['settings_' . $key]) :
						$orderfields[$key] = $this->createCustomStandardField($key, 'billing', $options['woofields']['type_' . $key]);
					endif;
					
					//cosmetic stuff
					if (!empty($options['woofields']['class_' . $key])) {
						if (!empty($orderfields[$key]['class']) && is_array($orderfields[$key]['class']))
							$orderfields[$key]['class'][0] = $options['woofields']['class_' . $key];
						else
							$orderfields[$key]['class'] = array ($options['woofields']['class_' . $key]);
					}
					
					//Respect WC handling if desired
					$wc_field_handle = str_replace('billing_', '', $key);					
					if ( isset($options['checkoutform']['default_' . $wc_field_handle]) && $options['checkoutform']['default_' . $wc_field_handle] ) :
						//do nothing with required
						$orderfields[$key]['label'] = !empty($options['woofields']['label_' . $key]) ? __($options['woofields']['label_' . $key], WCPGSK_DOMAIN) : '';
						$orderfields[$key]['placeholder'] = !empty($options['woofields']['placeholder_' . $key]) ? __($options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN) : '';
					else :	
						$orderfields[$key]['label'] = !empty($options['woofields']['label_' . $key]) ? __($options['woofields']['label_' . $key], WCPGSK_DOMAIN) : '';
					
						if (isset($options['woofields']['required_' . $key]) && $options['woofields']['required_' . $key] != 1) :
							$orderfields[$key]['required'] = false;
						elseif (isset($options['woofields']['required_' . $key]) && $options['woofields']['required_' . $key] == 1) :
							$orderfields[$key]['required'] = true;
						else :
							$orderfields[$key]['required'] = false;					
						endif;
						$orderfields[$key]['placeholder'] = !empty($options['woofields']['placeholder_' . $key]) ? __($options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN) : '';
					endif;
					//set the order data

					
					if (empty($options['woofields']['order_' . $key]) || !ctype_digit($options['woofields']['order_' . $key])) {
						$orderfields[$key]['order'] = $field_order;
					}
					else {
						$orderfields[$key]['order'] = $options['woofields']['order_' . $key];
					}
					if ( isset($options['woofields']['settings_' . $key]) && $options['woofields']['settings_' . $key]) :
						$params = $this->explodeParameters($options['woofields']['settings_' . $key]);
						//if ( strpos($key, '_wcpgsk_repeater') !== false ) :
						//$testkey = str_replace('_wcpgsk_repeater', '', $key);
						//if ( !empty($options['woofields']['settings_' . $testkey]) ) :
						if ( is_array($params) && !empty($params) && isset($params['repeat_field']) && $params['repeat_field'] == '1' ) :
							$repkey = $key . '_wcpgsk_repeater';
							
							$orderfields[$repkey] = $this->createCustomStandardFieldClone($key, 'billing', $options['woofields']['type_' . $key]);

							//set all our other data
							//unset required for repeater fields as this collides with WooCommerce
							if ( isset($orderfields[$repkey]['required']) ) unset($orderfields[$repkey]['required']);
							//check if repeater field
						
							$orderfields[$repkey]['placeholder'] = !empty($options['woofields']['placeholder_' . $key]) ? __($options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN) : '';
							//set the order data

							
							if (empty($options['woofields']['order_' . $key]) || !ctype_digit($options['woofields']['order_' . $key])) {
								$orderfields[$repkey]['order'] = $field_order + 0.5;
							}
							else {
								$orderfields[$repkey]['order'] = intval($options['woofields']['order_' . $key]) + 0.5;
							}
						endif;
					endif;
					unset($fields['billing'][$key]);
				}
				$field_order++;
			}
			//order the fields
			uasort($orderfields, array($this, "compareFieldOrder"));						
			
			//add the fields again
			foreach ($orderfields as $key => $field) {
				if ($key == 'order')
					unset($field['order']);
				else
					$fields['billing'][$key] = $field;
			}
			return $fields;
		}
		
		/**
		 * Our filter for shipping fields.
		 *
		 * @access public
		 * @param array $fields
		 * @since 1.1.0
		 * @return array $fields (processed)
		 */						
		public function wcpgsk_checkout_fields_shipping($fields) {
			global $woocommerce;
			$options = get_option( 'wcpgsk_settings' );
			
			$field_order = 1;	
			
			$orderfields = array();
			
			foreach ($fields['shipping'] as $key => $field) {
				if (isset($options['woofields']['remove_' . $key]) && $options['woofields']['remove_' . $key] == 1) {
					if ( $key == 'shipping_country' ) :
						$countries = $woocommerce->countries->get_shipping_countries();
						if ( isset( $countries ) && is_array( $countries ) && count( $countries ) == 1 ) :
							$fields['shipping'][$key]['class'] = array('hidecountry');
						elseif ( isset( $countries ) && is_array( $countries ) && count( $countries ) > 2 ) :
							//incorrect admin setting, do nothing
						endif;						
					else :
						unset($fields['shipping'][$key]);
					endif;
				}
				else {
				
					$orderfields[$key] = $fields['shipping'][$key];

					//cosmetic stuff
					if (!empty($options['woofields']['class_' . $key])) {
						if (!empty($orderfields[$key]['class']) && is_array($orderfields[$key]['class']))
							$orderfields[$key]['class'][0] = $options['woofields']['class_' . $key];
						else
							$orderfields[$key]['class'] = array ($options['woofields']['class_' . $key]);
					}
					
					//Respect WC handling if desired
					$wc_field_handle = str_replace('shipping_', '', $key);					
					if ( isset($options['checkoutform']['default_' . $wc_field_handle]) && $options['checkoutform']['default_' . $wc_field_handle] ) :
						//do nothing with required
						$orderfields[$key]['label'] = !empty($options['woofields']['label_' . $key]) ? __($options['woofields']['label_' . $key], WCPGSK_DOMAIN) : '';
						$orderfields[$key]['placeholder'] = !empty($options['woofields']['placeholder_' . $key]) ? __($options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN) : '';
					else :	
						$orderfields[$key]['label'] = !empty($options['woofields']['label_' . $key]) ? __($options['woofields']['label_' . $key], WCPGSK_DOMAIN) : '';
						if (isset($options['woofields']['required_' . $key]) && $options['woofields']['required_' . $key] != 1) :
							$orderfields[$key]['required'] = false;
						elseif (isset($options['woofields']['required_' . $key]) && $options['woofields']['required_' . $key] == 1) :
							$orderfields[$key]['required'] = true;
						else :
							$orderfields[$key]['required'] = false;					
						endif;
						$orderfields[$key]['placeholder'] = !empty($options['woofields']['placeholder_' . $key]) ? __($options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN) : '';
					endif;

					//set the order data

					
					if (empty($options['woofields']['order_' . $key]) || !ctype_digit($options['woofields']['order_' . $key])) {
						$orderfields[$key]['order'] = $field_order;
					}
					else {
						$orderfields[$key]['order'] = $options['woofields']['order_' . $key];
					}

					if ( isset($options['woofields']['settings_' . $key]) && $options['woofields']['settings_' . $key]) :
						$params = $this->explodeParameters($options['woofields']['settings_' . $key]);
						//if ( strpos($key, '_wcpgsk_repeater') !== false ) :
						//$testkey = str_replace('_wcpgsk_repeater', '', $key);
						//if ( !empty($options['woofields']['settings_' . $testkey]) ) :
						if ( is_array($params) && !empty($params) && isset($params['repeat_field']) && $params['repeat_field'] == '1' ) :
							$repkey = $key . '_wcpgsk_repeater';
							
							$orderfields[$repkey] = $this->createCustomStandardFieldClone($key, 'shipping', $options['woofields']['type_' . $key]);

							//set all our other data
							//woocommerce changed?
							//unset required for repeater fields as this collides with WooCommerce
							if ( isset($orderfields[$repkey]['required']) ) unset($orderfields[$repkey]['required']);
							//check if repeater field
						
							$orderfields[$repkey]['placeholder'] = !empty($options['woofields']['placeholder_' . $key]) ? __($options['woofields']['placeholder_' . $key], WCPGSK_DOMAIN) : '';
							//set the order data

							
							if (empty($options['woofields']['order_' . $key]) || !ctype_digit($options['woofields']['order_' . $key])) {
								$orderfields[$repkey]['order'] = $field_order + 0.5;
							}
							else {
								$orderfields[$repkey]['order'] = intval($options['woofields']['order_' . $key]) + 0.5;
							}
						endif;
					endif;
					unset($fields['shipping'][$key]);
				}
				$field_order++;
			}
			//order the fields
			uasort($orderfields, array($this, "compareFieldOrder"));						
			
			//add the fields again
			foreach ($orderfields as $key => $field) {
				if ($key == 'order')
					unset($field['order']);
				else
					$fields['shipping'][$key] = $field;
			}
			return $fields;
		}
				
		
		/**
		 * Process form data supplied via the checkout form.
		 *
		 * @access public
		 * @since 1.1.0
		 * @modified 1.7.1
		 * @return Raise errors and adjust values if necessary
		 */						
		public function wcpgsk_checkout_process() {
			global $woocommerce;
			global $wcpgsk_session;
			if ( function_exists('WC') ) :
				WC()->session->set('post', $_POST);
			else :
				$wcpgsk_session->post = $_POST;
			endif;
			
			//$wcpgsk_session->post = $_POST;
			
			$options = get_option( 'wcpgsk_settings' );
			
			//do_action('wcpgsk_checkout_process_action');
			if ( !is_user_logged_in() ) :
				if (isset($options['checkoutform']['billingemailvalidator']) && $options['checkoutform']['billingemailvalidator'] == 1) {
					if ($_POST[ 'billing_email' ] && $_POST[ 'billing_email_validator' ] && strtolower($_POST[ 'billing_email' ]) != strtolower($_POST[ 'billing_email_validator' ]))
						wcpgsk_add_error(  '<strong>' . __('Email addresses do not match', WCPGSK_DOMAIN) . ': ' . $_POST[ 'billing_email' ] . ' : ' . (empty($_POST[ 'billing_email_validator' ]) ? __('Missing validation email', WCPGSK_DOMAIN) : $_POST[ 'billing_email_validator' ]) . '</strong>');
					elseif ($_POST[ 'billing_email' ] && !$_POST[ 'billing_email_validator' ])
						wcpgsk_add_error(  '<strong>' . __('You have to supply a validation email for: ', WCPGSK_DOMAIN) . $_POST[ 'billing_email' ] . '</strong>');
				}
			endif;
			//Just communicate the required state to WC plus the label in billing context based on user choice and locale configuration
			//WC will take care of the error handling
			if ( isset($_POST['billing_country']) && $_POST['billing_country'] ) :
				//Respect WC handling if desired
				$jsfields = $this->wcpgsk_get_country_locale_field_selectors();
				$locale = get_option( 'wcpgsk_locale', array() );
				$wc_locale = $this->wcpgsk_get_country_locale();
				$countrycode = $_POST['billing_country'];
				foreach ($jsfields as $wc_field_handle => $wc_field_config) :
					if ( isset($options['checkoutform']['default_' . $wc_field_handle]) && $options['checkoutform']['default_' . $wc_field_handle] ) :
						$default_required = isset($locale['default']['required_' . $wc_field_handle]) ? $locale['default']['required_' . $wc_field_handle] : '0';
						$wc_localerequired = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode][$wc_field_handle]) && isset($wc_locale[$countrycode][$wc_field_handle]['required']) ? ( $wc_locale[$countrycode][$wc_field_handle]['required'] ? '1' : '0' ) : ( isset($wc_locale['default'][$wc_field_handle]['required']) && $wc_locale['default'][$wc_field_handle]['required'] ? '1' : $default_required );
						$locale[$countrycode]['required_' . $wc_field_handle] = isset($locale[$countrycode]['required_' . $wc_field_handle]) ? $locale[$countrycode]['required_' . $wc_field_handle] : $wc_localerequired;
						$required_field = $locale[$countrycode]['required_' . $wc_field_handle] ? true : false;
						
						$default_label = isset($locale['default']['label_' . $wc_field_handle]) ? $locale['default']['label_' . $wc_field_handle] : '';
						$wc_localelabel = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode][$wc_field_handle]) && isset($wc_locale[$countrycode][$wc_field_handle]['label']) ? $wc_locale[$countrycode][$wc_field_handle]['label'] : ( isset($wc_locale['default'][$wc_field_handle]['label']) ? $wc_locale['default'][$wc_field_handle]['label'] : $default_label );						
						$locale[$countrycode]['label_' . $wc_field_handle] = isset($locale[$countrycode]['label_' . $wc_field_handle]) ? $locale[$countrycode]['label_' . $wc_field_handle] : $wc_localelabel;
						$required_label = $locale[$countrycode]['label_' . $wc_field_handle];

						$woocommerce->checkout->checkout_fields['billing']['billing_' . $wc_field_handle]['required'] = $required_field;
						$woocommerce->checkout->checkout_fields['billing']['billing_' . $wc_field_handle]['label'] = __($required_label, WCPGSK_DOMAIN);

					endif;
				endforeach;
			endif;

			//Just communicate the required state to WC plus the label in shipping context based on user choice and locale configuration
			//WC will take care of the error handling
			if ( isset($_POST['shipping_country']) && $_POST['shipping_country'] ) :
				//Respect WC handling if desired
				$jsfields = $this->wcpgsk_get_country_locale_field_selectors();
				$locale = get_option( 'wcpgsk_locale', array() );
				$wc_locale = $this->wcpgsk_get_country_locale();
				$countrycode = $_POST['shipping_country'];
				foreach ($jsfields as $wc_field_handle => $wc_field_config) :
					if ( isset($options['checkoutform']['default_' . $wc_field_handle]) && $options['checkoutform']['default_' . $wc_field_handle] ) :
						$default_required = isset($locale['default']['required_' . $wc_field_handle]) ? $locale['default']['required_' . $wc_field_handle] : '0';
						$wc_localerequired = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode][$wc_field_handle]) && isset($wc_locale[$countrycode][$wc_field_handle]['required']) ? ( $wc_locale[$countrycode][$wc_field_handle]['required'] ? '1' : '0' ) : ( isset($wc_locale['default'][$wc_field_handle]['required']) && $wc_locale['default'][$wc_field_handle]['required'] ? '1' : $default_required );
						$locale[$countrycode]['required_' . $wc_field_handle] = isset($locale[$countrycode]['required_' . $wc_field_handle]) ? $locale[$countrycode]['required_' . $wc_field_handle] : $wc_localerequired;
						$required_field = $locale[$countrycode]['required_' . $wc_field_handle] == '1' ? true : false;
						
						$default_label = isset($locale['default']['label_' . $wc_field_handle]) ? $locale['default']['label_' . $wc_field_handle] : '';
						$wc_localelabel = isset($wc_locale[$countrycode]) && isset($wc_locale[$countrycode][$wc_field_handle]) && isset($wc_locale[$countrycode][$wc_field_handle]['label']) ? $wc_locale[$countrycode][$wc_field_handle]['label'] : ( isset($wc_locale['default'][$wc_field_handle]['label']) ? $wc_locale['default'][$wc_field_handle]['label'] : $default_label );						
						$locale[$countrycode]['label_' . $wc_field_handle] = isset($locale[$countrycode]['label_' . $wc_field_handle]) ? $locale[$countrycode]['label_' . $wc_field_handle] : $wc_localelabel;
						$required_label = $locale[$countrycode]['label_' . $wc_field_handle];
						
						$woocommerce->checkout->checkout_fields['shipping']['shipping_' . $wc_field_handle]['required'] = $required_field;
						$woocommerce->checkout->checkout_fields['shipping']['shipping_' . $wc_field_handle]['label'] = __($required_label, WCPGSK_DOMAIN);
					endif;
				endforeach;
			endif;
			
			$combine = array();
			foreach($_POST as $key => $val) {
				if ( strpos($key, '_wcpgsk_repeater') !== false ) :
					$testkey = str_replace('_wcpgsk_repeater', '', $key);
					if ( $_POST[$key] != $_POST[$testkey] ) :
						$captured_value = $_POST[ $testkey ];
						if ( isset($options['woofields']['settings_' . $testkey]) ) :
							$params = $this->explodeParameters($options['woofields']['settings_' . $testkey]);
							if ( isset($params) && isset($params['validate']) && !empty($params['validate']) && $params['validate'] == 'password' ) :
								$captured_value = '*******';
							endif;
						endif;
						$forLabel = '';
						if ( isset($options['woofields']['label_' . $testkey]) && !empty($options['woofields']['label_' . $testkey]) ) :
							$forLabel = __($options['woofields']['label_' . $testkey], WCPGSK_DOMAIN);
						endif;
						wcpgsk_add_error(  '<strong>' . sprintf(__('You have to validate the value <em style="color:red">%s</em> for %s correctly! Please check your input.', WCPGSK_DOMAIN), $captured_value, $forLabel ) . '</strong>');
					
					endif;
					unset($_POST[$key]);
				
				elseif ( ( isset($options['woofields']['billing'][$key]['custom_' . $key]) && $options['woofields']['billing'][$key]['custom_' . $key] ) || ( isset( $options['woofields']['shipping'][$key]['custom_' . $key] ) && $options['woofields']['shipping'][$key]['custom_' . $key] ) ) :
					if ( $options['woofields']['type_' . $key] == 'date' && !empty($_POST[$key]) ) :
						//transform back based on field setting
						$params = $this->explodeParameters($options['woofields']['settings_' . $key]);
						$_POST[$key] = str_replace('-', '/', $_POST[$key]);
						if ( isset($params['dateformat']) && !empty($params['dateformat']) ) :
							if ( $params['dateformat'] == 'dd/mm/yy' ) :
								$arrdate = explode('/', $_POST[$key]);
								$_POST[$key] = $arrdate[2].'/'.$arrdate[1].'/'.$arrdate[0];
							elseif ( $params['dateformat'] == 'mm/dd/yy' ) :
								$arrdate = explode('/', $_POST[$key]);
								$_POST[$key] = $arrdate[2].'/'.$arrdate[0].'/'.$arrdate[1];								
							endif;
						endif;
						if ( $this->ValidateDate($_POST[$key]) ) :
							if ( isset($params) && isset($params['mindays']) && !empty($params['mindays']) && ( ctype_digit( strval( $params['mindays'] ) ) || is_numeric( strval( $params['mindays'] ) ) ) ) :	
								$forLabel = '';
								$daydiff = $this->datediffdays($_POST[$key]);
								if ( $params['mindays'] < 0 ) :
									if ( $daydiff < $params['mindays'] ) :
										if ( isset($options['woofields']['label_' . $key]) && !empty($options['woofields']['label_' . $key]) ) :
											$forLabel = __($options['woofields']['label_' . $key], WCPGSK_DOMAIN);
										endif;
										$mindate = date('Y/m/d', strtotime(date("Y-m-d") . ' - ' . ( absint( $params['mindays'] ) - 1 ) . ' days'));
										wcpgsk_add_error(  '<strong>' . sprintf(__('Date value for <em style="color:red">%s</em> has to be set at least to <em>%s</em>!', WCPGSK_DOMAIN), $forLabel, $mindate ) . '</strong>');						
									endif;								
								else :	
									if ( $daydiff < $params['mindays'] ) :
										if ( isset($options['woofields']['label_' . $key]) && !empty($options['woofields']['label_' . $key]) ) :
											$forLabel = __($options['woofields']['label_' . $key], WCPGSK_DOMAIN);
										endif;
										$mindate = date('Y/m/d', strtotime(date("Y-m-d") . ' + ' . $params['mindays'] . ' days'));
										wcpgsk_add_error(  '<strong>' . sprintf(__('Date value for <em style="color:red">%s</em> has to be set at least to <em>%s</em>!', WCPGSK_DOMAIN), $forLabel, $mindate ) . '</strong>');						
									endif;
								endif;
							elseif ( isset($params) && isset($params['mindays']) && !empty($params['mindays']) ) :
								$forLabel = '';
								$mindays = $params['mindays'];
								if ( isset($params['dateformat']) && !empty($params['dateformat']) ) :
									if ( $params['dateformat'] == 'dd/mm/yy' ) :
										$arrdate = explode('/', $params['mindays']);
										$mindays = $arrdate[2].'/'.$arrdate[1].'/'.$arrdate[0];
									elseif ( $params['dateformat'] == 'mm/dd/yy' ) :
										$arrdate = explode('/', $params['mindays']);
										$mindays = $arrdate[2].'/'.$arrdate[0].'/'.$arrdate[1];								
									endif;
								endif;

								if ( $_POST[$key] < $mindays ) :
									if ( isset($options['woofields']['label_' . $key]) && !empty($options['woofields']['label_' . $key]) ) :
										$forLabel = __($options['woofields']['label_' . $key], WCPGSK_DOMAIN);
									endif;
									wcpgsk_add_error(  '<strong>' . sprintf(__('Date value for <em style="color:red">%s</em> has to be set at least to <em>%s</em>!', WCPGSK_DOMAIN), $forLabel, $params['mindays'] ) . '</strong>');						
								endif;								
	
							endif;
							if ( isset($params) && isset($params['maxdays']) && !empty($params['maxdays']) && ( ctype_digit( strval( $params['maxdays'] ) ) || is_numeric( strval( $params['maxdays'] ) ) ) ) :
								$forLabel = '';
								$daydiff = $this->datediffdays($_POST[$key]);
								if ( $params['maxdays'] < 0 ) :
									if ( $daydiff > $params['maxdays'] ) :
										if ( isset($options['woofields']['label_' . $key]) && !empty($options['woofields']['label_' . $key]) ) :
											$forLabel = __($options['woofields']['label_' . $key], WCPGSK_DOMAIN);
										endif;
										$maxdate = date('Y/m/d', strtotime(date("Y-m-d") . ' - ' . ( absint( $params['maxdays'] - 1 ) ) . ' days'));
										wcpgsk_add_error(  '<strong>' . sprintf(__('Date value for <em style="color:red">%s</em> has to be priorrrr to <em>%s</em>!', WCPGSK_DOMAIN), $forLabel, $maxdate ) . '</strong>');
									endif;									
								else :
									if ( $daydiff > $params['maxdays'] ) :
										if ( isset($options['woofields']['label_' . $key]) && !empty($options['woofields']['label_' . $key]) ) :
											$forLabel = __($options['woofields']['label_' . $key], WCPGSK_DOMAIN);
										endif;
										$maxdate = date('Y/m/d', strtotime(date("Y-m-d") . ' + ' . ($params['maxdays'] + 1) . ' days'));
										wcpgsk_add_error(  '<strong>' . sprintf(__('Date value for <em style="color:red">%s</em> has to be prior to <em>%s</em>!', WCPGSK_DOMAIN), $forLabel, $maxdate ) . '</strong>');						
									endif;
								endif;
							elseif ( isset($params) && isset($params['maxdays']) && !empty($params['maxdays']) ) :
								$forLabel = '';
								$maxdays = $params['maxdays'];
								if ( isset($params['dateformat']) && !empty($params['dateformat']) ) :
									if ( $params['dateformat'] == 'dd/mm/yy' ) :
										$arrdate = explode('/', $params['maxdays']);
										$maxdays = $arrdate[2].'/'.$arrdate[1].'/'.$arrdate[0];
									elseif ( $params['dateformat'] == 'mm/dd/yy' ) :
										$arrdate = explode('/', $params['maxdays']);
										$maxdays = $arrdate[2].'/'.$arrdate[0].'/'.$arrdate[1];								
									endif;
								endif;

								if ( $_POST[$key] > $maxdays ) :
									if ( isset($options['woofields']['label_' . $key]) && !empty($options['woofields']['label_' . $key]) ) :
										$forLabel = __($options['woofields']['label_' . $key], WCPGSK_DOMAIN);
									endif;
									wcpgsk_add_error(  '<strong>' . sprintf(__('Date value for <em style="color:red">%s</em> has to be prior to <em>%s</em>!', WCPGSK_DOMAIN), $forLabel, $params['maxdays'] ) . '</strong>');						
								endif;								
							endif;
						else :
							$forLabel = '';
							if ( isset($options['woofields']['label_' . $key]) && !empty($options['woofields']['label_' . $key]) ) :
								$forLabel = __($options['woofields']['label_' . $key], WCPGSK_DOMAIN);
							endif;
							wcpgsk_add_error(  '<strong>' . sprintf(__('You have to supply a valid date for <em style="color:red">%s</em> using the format year/month/day, e.g. 2014/12/24', WCPGSK_DOMAIN), $forLabel ) . '</strong>');												
						endif;
					elseif ( $options['woofields']['type_' . $key] == 'select' ) :
						//if ( is_array($key) ) :
						//endif;

					endif;
					
					
					$combine[$key] = array();
					if (is_array($_POST[$key])) {
						foreach($_POST[$key] as $value){
							$combine[$key][] = esc_attr($value);
						}									
					}
					else $combine[$key][] = esc_attr($val);
				endif;
			}
			foreach($combine as $key => $val) {
				$_POST[$key] = implode('|', $val);
			}
			
		}
		
		function wcpgsk_checkout_update_order_meta( $order_id, $posted ) {
			global $woocommerce;
			global $wcpgsk_session;
			if ( function_exists('WC') ) :
				WC()->session->set('post', $_POST);
			else :
				$wcpgsk_session->post = $_POST;
			endif;			
			$options = get_option( 'wcpgsk_settings' );
			foreach($posted as $key => $val) :
				if ( ( isset($options['woofields']['billing'][$key]['custom_' . $key]) && $options['woofields']['billing'][$key]['custom_' . $key] ) || ( isset( $options['woofields']['shipping'][$key]['custom_' . $key] ) && $options['woofields']['shipping'][$key]['custom_' . $key] ) ) :
					if ( !isset( $options['woofields']['customeronly_' . $key] ) || ( isset( $options['woofields']['customeronly_' . $key] ) && $options['woofields']['customeronly_' . $key] == 0 ) ) :
						update_post_meta( $order_id, "_" . $key, $val );
					endif;
				endif;
			endforeach;
		}
		
		public function createCustomStandardField($customkey, $context, $type) {
			$options = get_option( 'wcpgsk_settings' );
			$clear = false;
			$field = array();
			if (isset($options['woofields'][$context]) && is_array($options['woofields'][$context])) {
				$params = $this->explodeParameters($options['woofields']['settings_' . $customkey]);
				$custom_attributes = array();
				$seloptions = array();
				$selected = null;
				$clear = $options['woofields']['class_' . $customkey] == 'form-row-last' ? true : false;
				$validate = array();
				$display = '';
				$default = '';
				$class = array( $options['woofields']['class_' . $customkey] );
				if ( isset( $options['checkoutform']['cssclass'] ) && !empty( $options['checkoutform']['cssclass'] ) ) :
					$class = array_merge( $class, array_map( "sanitize_html_class", explode( ' ', $options['checkoutform']['cssclass'] ) ) );
				endif;
				if (is_array($params) && !empty($params)) {
					foreach($params as $key => $value) {
						switch($key) {
							//does not make much sense as validation class is not really available in woocommerce
							//we put this as a parameter
							case 'validate':
								if ( $value && $value == 'password' ) :
									$type = 'password';
									$validate = array();
								elseif ( !empty($value) ) :
									$custom_attributes[$key] = $value;
									$validate = array($value);
								endif;
								break;
							case 'options':
								$nulllabel = isset( $params['nulllabel'] ) ? $params['nulllabel'] : __( 'Select an option', WCRGSK_DOMAIN );
								$custom_attributes['data-placeholder'] = esc_attr( $nulllabel );
								$chosen_select = "";
								if ( get_option( 'woocommerce_enable_chosen' ) == 'yes' ) :
									$chosen_select = "wcrgsk-chosen-select";
									$class[] = $chosen_select;
								endif;
								if ( isset( $params['allow_null'] ) && $params['allow_null'] ) :
									//data-nulloption								
									$seloptions[''] = empty( $chosen_select ) ? $nulllabel : '';									
								endif;
								
								foreach($value as $keyval => $option) {
									$seloptions[$keyval] = __($option, WCPGSK_DOMAIN);
								}
								break;
							case 'selected':
								if ( !empty($value) ) :
									foreach($value as $keyval => $option) {
										//if (!empty($option) || $value == 0) $selected = $option;
										if (!empty($keyval)) $selected = $keyval;
									}
								endif;
								break;

							case 'multiple':
								if ($value == 1) $custom_attributes[$key] = 'multiple';
								break;

							case 'value':
								if (!empty($value) || $value === 0 || $value === '0') $default = $value;
								break;
							
							case 'repeat_field':
								//not necessary here?
								break;
							default:
								if (!empty($value) || $value === 0 || $value === '0')
									$custom_attributes[$key] = $value;
						}
					}
				}
				$options['woofields']['label_' . $customkey] = !empty($options['woofields']['label_' . $customkey]) ? $options['woofields']['label_' . $customkey] : '';
				$options['woofields']['placeholder_' . $customkey] = !empty($options['woofields']['placeholder_' . $customkey]) ? $options['woofields']['placeholder_' . $customkey] : '';
				$options['woofields']['required_' . $customkey] = isset($options['woofields']['required_' . $customkey]) && $options['woofields']['required_' . $customkey] == 1 ? 1 : 0; 
				switch($type) {
					case 'password':
						$custom_attributes['display'] = 'text';
						$field = array(
							'type'				=> 'password',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> (($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $class,
							'validate'			=> $validate,
							'clear'				=> $clear
						);
					break;

					case 'text':
						$custom_attributes['display'] = 'text';
						$field = array(
							'type'				=> 'text',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> (($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $class,
							'validate'			=> $validate,
							'clear'				=> $clear
						);
					break;
					
					case 'number':
						$custom_attributes['display'] = 'number';
						$field = array(
							'type'				=> 'text',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'default'			=> $default,
							'class' 			=> $class,
							'validate'			=> $validate,
							'clear'				=> $clear
						);
						break;
						
					case 'date':
						$custom_attributes['display'] = 'date';
						$field = array(
							'type'				=> 'text',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $class,
							'validate'			=> array('date'),
							'clear'				=> $clear
						);
						break;

						case 'time':
						$custom_attributes['display'] = 'time';

						$field = array(
							'type'				=> 'text',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $class,
							'validate'			=> array('time'),
							'clear'				=> $clear
						);
						break;

						case 'textarea':
						$custom_attributes['display'] = 'textarea';

						$field = array(
							'type'				=> 'textarea',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $class,
							'validate'			=> $validate,
							'clear'				=> $clear
						);
						break;

						case 'select':
						$custom_attributes['display'] = $display;
						$custom_attributes['hasselected'] = !empty($selected) ? 'true' : 'false';
						$field = array(
							'type'				=> 'select',
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'options' 			=> $seloptions,
							'default'			=> $selected,
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $class,
							'validate'			=> $validate,
							'clear'				=> $clear					
						);
						break;
						default:
						$custom_attributes['display'] = $display;
						$field = array(
							'type'				=> $type,
							'label' 			=> __( $options['woofields']['label_' . $customkey], WCPGSK_DOMAIN ),
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $class,
							'validate'			=> $validate,
							'clear'				=> $clear
						);						
						break;
				}
				//apply_filters('wcpgsk_create_custom_standard_field', $field, 
			}
			return apply_filters('wcpgsk_create_custom_standard_field', $field);
		}

		public function createCustomStandardFieldClone($customkey, $context, $type) {
			$options = get_option( 'wcpgsk_settings' );
			$clear = false;
			$field = array();
			if (isset($options['woofields'][$context]) && is_array($options['woofields'][$context])) {
				$params = $this->explodeParameters($options['woofields']['settings_' . $customkey]);
				$custom_attributes = array();
				$seloptions = array();
				$selected = null;
				$clear = $options['woofields']['class_' . $customkey] == 'form-row-first' ? true : false;
				$validate = array();
				$display = '';
				$default = '';
				
				if (is_array($params) && !empty($params)) {
					foreach($params as $key => $value) {
						switch($key) {
							//does not make much sense as validation class is not really available in woocommerce
							//we put this as a parameter
							case 'validate':
								if ( $value && $value == 'password' ) :
									$type = 'password';
									$validate = array();
								elseif ( !empty($value) ) :
									$custom_attributes[$key] = $value;
									$validate = array($value);
								endif;
								break;
							case 'options':
								$nulllabel = isset( $params['nulllabel'] ) ? $params['nulllabel'] : __( 'Select an option', WCRGSK_DOMAIN );
								$custom_attributes['data-placeholder'] = esc_attr( $nulllabel );
								$chosen_select = "";
								if ( get_option( 'woocommerce_enable_chosen' ) == 'yes' ) :
									$chosen_select = "wcrgsk-chosen-select";
									$class[] = $chosen_select;
								endif;
								if ( isset( $params['allow_null'] ) && $params['allow_null'] ) :
									//data-nulloption								
									$seloptions[''] = empty( $chosen_select ) ? $nulllabel : '';									
								endif;
								
								foreach($value as $keyval => $option) {
									$seloptions[$keyval] = __($option, WCPGSK_DOMAIN);
								}
								break;
							case 'selected':
							
								if ( !empty($value) ) :
									foreach($value as $keyval => $option) {
										//if (!empty($option) || $value == 0) $selected = $option;
										if (!empty($keyval)) $selected = $keyval;
									}
								endif;
								break;

							case 'multiple':
								if ($value == 1) $custom_attributes[$key] = 'multiple';
								break;

							case 'value':
								if (!empty($value) || $value === 0 || $value === '0') $default = $value;
								break;
							
							default:
								if (!empty($value) || $value === 0 || $value === '0')
									$custom_attributes[$key] = $value;
						}
					}
				}
				//$options['woofields']['label_' . $customkey] = !empty($options['woofields']['label_' . $customkey]) ? $options['woofields']['label_' . $customkey] : '';
				$options['woofields']['placeholder_' . $customkey] = !empty($options['woofields']['placeholder_' . $customkey]) ? $options['woofields']['placeholder_' . $customkey] : '';
				$options['woofields']['required_' . $customkey] = isset($options['woofields']['required_' . $customkey]) && $options['woofields']['required_' . $customkey] == 1 ? 1 : 0; 
				$clone_class = array( 'form-row-wide' );
				if ($options['woofields']['class_' . $customkey] == 'form-row-first') $clone_class = array( 'form-row-last' );
				
				if ( isset( $options['checkoutform']['cssclass'] ) && !empty( $options['checkoutform']['cssclass'] ) ) :
					$clone_class = array_merge( $clone_class, array_map( "sanitize_html_class", explode( ' ', $options['checkoutform']['cssclass'] ) ) );
				endif;
				
				$clone_label = __('Repeat value', WCPGSK_DOMAIN);
				
				switch($type) {
					case 'password':
						$custom_attributes['display'] = 'text';
						$field = array(
							'type'				=> 'password',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> (($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $clone_class,
							'validate'			=> $validate,
							'clear'				=> $clear
						);
					break;
					case 'text':
						$custom_attributes['display'] = 'text';
						$field = array(
							'type'				=> 'text',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> (($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $clone_class,
							'validate'			=> $validate,
							'clear'				=> $clear
						);
					break;
					
					case 'number':
						$custom_attributes['display'] = 'number';
						$field = array(
							'type'				=> 'text',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'default'			=> $default,
							'class' 			=> $clone_class,
							'validate'			=> $validate,
							'clear'				=> $clear
						);
						break;
						
					case 'date':
						$custom_attributes['display'] = 'date';
						$field = array(
							'type'				=> 'text',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $clone_class,
							'validate'			=> array('date'),
							'clear'				=> $clear
						);
						break;

						case 'time':
						$custom_attributes['display'] = 'time';

						$field = array(
							'type'				=> 'text',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $clone_class,
							'validate'			=> array('time'),
							'clear'				=> $clear
						);
						break;

						case 'textarea':
						$custom_attributes['display'] = 'textarea';

						$field = array(
							'type'				=> 'textarea',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $clone_class,
							'validate'			=> $validate,
							'clear'				=> $clear
						);
						break;

						case 'select':
						$custom_attributes['display'] = $display;
						$field = array(
							'type'				=> 'select',
							'label' 			=> $clone_label,
							'placeholder' 		=> __( $options['woofields']['placeholder_' . $customkey], WCPGSK_DOMAIN ),
							'required' 			=> ( ($options['woofields']['required_' . $customkey] == 1) ? true : false),
							'options' 			=> $seloptions,
							'default'			=> $selected,
							'custom_attributes'	=> $custom_attributes,
							'class' 			=> $clone_class,
							'validate'			=> $validate,
							'clear'				=> $clear					
						);
						break;
				}
			}
			return $field;
		}
		
		private function explodeParameters($settings) {
			$params = array();
			foreach (explode('&', $settings) as $chunk) {
				$param = explode("=", $chunk);

				if ($param) {
					$key =  str_replace('wcpgsk_add_',  '', urldecode($param[0]));
					if (!empty($key))
						$params[$key] = urldecode($param[1]);
					$new_choices = array();
					
					
					// explode choices from each line
					if( isset($params[$key]) && $params[$key] && (strpos($key, 'options') !== false || strpos($key, 'selected') !== false) )
					{
						// stripslashes ("")
						$params[$key] = stripslashes_deep($params[$key]);
					
						if(strpos($params[$key], "\n") !== false)
						{
							// found multiple lines, explode it
							$params[$key] = explode("\n", $params[$key]);
						}
						else
						{
							// no multiple lines! 
							$params[$key] = array($params[$key]);
						}
										
						// key => value
						foreach($params[$key] as $line)
						{
							if(strpos($line, ' : ') !== false)
							{
								$option = explode(' : ', $line);
								$new_choices[ trim($option[0]) ] = trim($option[1]);
							}
							else
							{
								$new_choices[ trim($line) ] = trim($line);
							}
						}
						// update options
						$params[$key] = $new_choices;
					}
				}
			}
			return $params;
		}

		public function explodeAttribute($param) {
			$params = array();

			if ($param) {
				
				if(strpos($param, "\n") !== false)
				{
					// found multiple lines, explode it
					$params[0] = explode("\n", $param);
				}
				else
				{
					// no multiple lines! 
					$params[0] = array($param);
				}
								
				// key => value
				foreach($params[0] as $line)
				{
					if(strpos($line, ' : ') !== false)
					{
						$option = explode(' : ', $line);
						$new_choices[ trim($option[0]) ] = trim($option[1]);
					}
					else
					{
						$new_choices[ trim($line) ] = trim($line);
					}
				}
				// update options
				$params[0] = $new_choices;
			}
			return $params;
		}
		
		/**
		 * Helper function to calculate difference in days (php < 5.3 compatible) 
		 * @access public
		 * @param string $formdate 
		 * @since 1.7.1
		 * @return int $days
		 */		
		public function datediffdays($formdate) {
			$dateSplits = explode("/", $formdate);
			$date1 =  strtotime(date('Y-m-d'));
			$date2 =  mktime(0, 0, 0, (int)$dateSplits[1],(int)$dateSplits[2],(int)$dateSplits[0]);
			$days = ($date2 - $date1)/(3600*24);
			// returns numberofdays
			return $days; 		
		} 	
		
		/**
		 * Helper function to validate a date (php < 5.3 compatible) 
		 * @access public
		 * @param string $formdate 
		 * @since 1.7.1
		 * @return int $days
		 */		
		function ValidateDate($formdate, $format = 'Y-m-d') {
			$version = explode('.', phpversion());
			$dateSplits = explode("/", $formdate);
			if ( count($dateSplits) == 3 ) :
				return checkdate($dateSplits[1], $dateSplits[2], $dateSplits[0]);
			else :
				return false;
			endif;
		}		
		/**
		 * Our admin menu and admin scripts
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */
		public function wcpgsk_admin_menu() {
			// Add a new submenu under Woocommerce:
			global $wcpgsk_name;
			$wcpgsk_name = apply_filters('wcpgsk_plus_name', $wcpgsk_name);
			add_submenu_page( 'woocommerce' , __( $wcpgsk_name, WCPGSK_DOMAIN ), __( $wcpgsk_name, WCPGSK_DOMAIN ), 'manage_options', WCPGSK_DOMAIN, array($this, 'wcpgsk__options_page') );
			add_action( 'admin_enqueue_scripts', array($this, 'wcpgsk_admin_scripts'), 20 );
		}
		
		/**
		 * Our admin scripts
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */
		public function wcpgsk_admin_scripts( $hook_suffix ) {
			if ( $hook_suffix == 'woocommerce_page_wcpgsk' ) {

				if( !wp_script_is('jquery-ui-accordion', 'queue') ) {
					wp_enqueue_script('jquery-ui-accordion');
				}

				wp_enqueue_script( 'wcpgsk_admin', plugins_url( '/assets/js/wcpgsk_admin.js', $this->file ), '', '' );
				
				if(!wp_script_is('jquery-ui-sortable', 'queue')){
						wp_enqueue_script('jquery-ui-sortable');
				}
				if(!wp_script_is('jquery-ui-dialog', 'queue')){
						wp_enqueue_script('jquery-ui-dialog');
				}
				if(!wp_script_is('jquery-ui-sortable', 'queue')){
						wp_enqueue_script('jquery-ui-sortable');
				}
				
				
				wp_register_script('accordion-js', plugins_url( '/assets/js/accordion.js', $this->file ), '', '', false);
				wp_register_style('accordion-styles', plugins_url( '/assets/css/accordion_styles.css', $this->file ), '', '');
				wp_register_style('wcpgsk-styles', plugins_url( '/assets/css/wcpgsk_styles.css', $this->file ), '', '');
		 
				wp_enqueue_script( 'accordion-js' );
				wp_enqueue_style( 'accordion-styles' );
				wp_enqueue_style( 'wcpgsk-styles' );
				// Include in admin_enqueue_scripts action hook
				wp_enqueue_media();
				wp_enqueue_script( 'custom-header' );		
				
			}
		}

		
		
		/**
		 * Run on activation.
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */
		public function activation() {
			$this->register_plugin_version();
			global $wcpgsk_options;
			$this->wcpgsk_initial_settings();			
		} // End activation()

		/**
		 * Register the plugin's version.
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */
		private function register_plugin_version () {
			if ( $this->version != '' ) {
				update_option( WCPGSK_DOMAIN . '-version', $this->version );
			}
		} // End register_plugin_version()
		
		// Plugin links
		public function wcpgsk_admin_plugin_actions($links) {
			$wcpgsk_links = array(
				'<a href="admin.php?page=' . WCPGSK_DOMAIN . '">'.__('Settings').'</a>',
			);
			return array_merge( $wcpgsk_links, $links );
		}
		
		/**
		 * Initial settings for our plugin
		 * @access public
		 * @since 1.1.0
		 * @changed 1.6.2 add wcpgsk default settings for billing and shipping fields
		 * @return void
		 */		
		private function wcpgsk_initial_settings() {
			global $woocommerce;
			$options = get_option('wcpgsk_settings');
			
			if ( !$options ) :
				
				$defaults = array( 
					'filters' => array(
						'loop_shop_per_page' => get_option( 'posts_per_page' ),
						'loop_shop_columns' => '4',
						'woocommerce_product_thumbnails_columns' => '3',
						'woocommerce_product_description_tab_title' => __('Description', 'woocommerce'),
						'woocommerce_product_description_heading' => __( 'Product Description', 'woocommerce' ),
						'woocommerce_product_additional_information_tab_title' => __('Additional Information', 'woocommerce'),
						'woocommerce_product_additional_information_heading' => __( 'Additional Information', 'woocommerce' ),
						
						'woocommerce_checkout_must_be_logged_in_message' => __( 'You must be logged in to checkout.', 'woocommerce' ),
						
						'woocommerce_checkout_login_message' => __( 'Returning customer?', 'woocommerce' ),
						
						'woocommerce_checkout_coupon_message' => __( 'Have a coupon?', 'woocommerce' ),
						'woocommerce_checkout_coupon_link_message' => __( 'Click here to enter your code', 'woocommerce' ),
						
						'woocommerce_order_button_text' => __( 'Place order', 'woocommerce' ),
						'woocommerce_pay_order_button_text' => __( 'Pay for order', 'woocommerce' ),
						'woocommerce_thankyou_order_received_text' => __( 'Thank you. Your order has been received.', 'woocommerce' ),						
						'woocommerce_countries_tax_or_vat' => $this->WC()->countries->tax_or_vat(),
						'woocommerce_countries_inc_tax_or_vat' => $this->WC()->countries->inc_tax_or_vat(),
						'woocommerce_countries_ex_tax_or_vat' => $this->WC()->countries->ex_tax_or_vat(),						
					),
					'wcpgsk_forms' => array( array(
						'label'  => __( 'Label', WCPGSK_DOMAIN ),
						'placeholder' => __( 'Placeholder', WCPGSK_DOMAIN ))),
					'cart' => array(
						'addemptycart' => 0,
						'minmaxstepproduct' => 0,
						'minitemscart' => 1,
						'maxitemscart' => 3,
						'minqtycart' => 0,
						'maxqtycart' => 0,
						'minvariationperproduct' => 1,
						'maxvariationperproduct' => 1,
						'maxqty_variation' => 0,
						'minqty_variation' => 0,
						'maxqty_variable' => 0,
						'minqty_variable' => 0,
						'maxqty_grouped' => 0,
						'minqty_grouped' => 0,
						'maxqty_external' => 0,
						'minqty_external' => 0,
						'maxqty_simple' => 0,
						'minqty_simple' => 0,
						'variationscountasproduct' => 0,
						'variationproductnoqty' => 0,
						'variableproductnoqty' => 0,
						'groupedproductnoqty' => 0,
						'externalproductnoqty' => 0,
						'simpleproductnoqty' => 0),
					'checkoutform' => array(
						'cartitemforms' => 1,
						'servicetitle' => __('Service data', WCPGSK_DOMAIN),
						'serviceformmerge' => 'woocommerce_before_order_notes',
						'sharedtitle' => __('Additional Information', WCPGSK_DOMAIN),
						'sharedformmerge' => 'woocommerce_after_checkout_billing_form',
						'tooltippersonalization' => '',
						'billingemailvalidator' => 0,
						'mindate' => 2,
						'maxdate' => 450,
						'caltimepicker' => 0,
						'caltimeampm' => 0,
						'enabletooltips' => 1,
						'enabletimesliders' => 1,
						'cssclass' => ''),
					'variations' => array(
						'extendattributes' => 1,
						'sortextendattributes' => 1),
					'process' => array(
						'fastcheckoutbtn' => '',
						'fastcart' => 0,
						'fastcheckout' => 0,
						'paymentgateways' => 0,
						'onsalelabel' => 'Sale!',
						'backorderlabel' => '',
						'emptycartlabel' => 'Empty cart?',
						'confirmemptycart' => 'Yes, empty cart',
						'empty_price_html' => ''),
					'email' => array(
						'wc_cc_email' => '',
						'wc_bcc_email' => ''),
					);
				//add default woocommerce billing and shipping field settings to wcpgsk settings to fix the problem of fields not showing up after activation of our plugin
				$checkout_fields = array_merge($woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'billing_' ), $woocommerce->countries->get_address_fields( $woocommerce->countries->get_base_country(), 'shipping_' ));
				foreach ($checkout_fields as $key => $field) : 
					$defaults['woofields']['placeholder_' . $key] = isset($checkout_fields[$key]['placeholder']) ? $checkout_fields[$key]['placeholder'] : '';
					$defaults['woofields']['label_' . $key] = isset($checkout_fields[$key]['label']) ? $checkout_fields[$key]['label'] : '';
					$defaults['woofields']['required_' . $key] = isset($checkout_fields[$key]['required']) ? $checkout_fields[$key]['required'] : 0;
					$defaults['woofields']['remove_' . $key] = 0;
					$defaults['woofields']['class_' . $key] = isset($checkout_fields[$key]['class']) ? $checkout_fields[$key]['class'][0] : 'form-row-wide';				
					$defaults['woofields']['settings_' . $key] = '';
				endforeach;
					
				add_option( 'wcpgsk_settings', apply_filters( 'wcpgsk_defaults', $defaults ) );
			endif;
		}
		
		/**
		 * Load the plugin's localisation file.
		 * @access public
		 * @since 1.1.0
		 * @return void
		 */
		public function load_localisation () {
			load_plugin_textdomain( WCPGSK_DOMAIN, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
		} // End load_localisation()

		/**
		 * Load the plugin textdomain from the main WordPress "languages" folder.
		 * @since 1.1.0
		 * @return  void
		 */
		public function load_plugin_textdomain () {
			$domain = WCPGSK_DOMAIN;
			// The "plugin_locale" filter is also used in load_plugin_textdomain()
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
		} // End load_plugin_textdomain()
		
		public function phpdie() {
			if (defined('PHPUNIT_TESTING')) {
				//do nothing
				//throw new TestingPhpDieException();
			} else {
				die();
			}
		}	
		
		/**
		 * Optimize load by dequeue if not necessary.
		 * @since 1.9.0
		 * @changed 1.9.8
		 * @return  void
		 */
		public function wcpgsk_handle_scripts() {

		}				

		/**
		 * Returns the WooCommerce instance
		 *
		 * @since 1.9.2
		 * @return WooCommerce woocommerce instance
		 */
		private function WC() {

			if ( function_exists( 'WC' ) && !is_null( WC() ) ) :
				return WC();
			else :
				global $woocommerce;
				return $woocommerce;
			endif;
		}
		
		/**
		 * Try to get rid of generator if not a WooCommerce page
		 * @since 1.9.0
		 * @return  void
		 */		
		public function wcpgsk_degenerate() { 
			global $wp_scripts;		
			//first check that woo exists to prevent fatal errors
			if ( function_exists( 'is_woocommerce' ) ) :
				$options = get_option( 'wcpgsk_settings' );
			
				//dequeue scripts and styles
				if ( is_cart() ) :
					wp_enqueue_script( 'wcpgsk-cart', plugins_url('/assets/js/wcpgsk-cart.js', $this->file) , array('jquery'), '', false);
					wp_enqueue_style( 'wcpgsk-cart-css', plugins_url('/assets/css/wcpgsk-cart.css', $this->file) , array(), '');
				endif;
				if ( is_checkout() || is_account_page() ) :
					wp_enqueue_script( 'jquery-ui-dialog' );
					wp_enqueue_script( 'jquery-ui-datepicker' );
					wp_enqueue_script( 'jquery-ui-slider' );
					wp_enqueue_script( 'jquery-ui-button' );
	
					wp_enqueue_script( 'jquery-ui-sliderAccess', plugins_url('/assets/js/jquery-ui-sliderAccess.js', $this->file) , '', '', false);
					if ( isset( $options['checkoutform']['caltimepicker'] ) && 1 == $options['checkoutform']['caltimepicker'] ) :
						wp_enqueue_style( 'jquery-ui-timepicker-css', plugins_url('/assets/css/jquery.ui.timepicker.css', $this->file) , array(), '');
						wp_enqueue_script( 'jquery-ui-timepicker-addon', plugins_url('/assets/js/jquery.ui.timepicker.js', $this->file) , array('jquery'), '', true);											
					else :
						wp_enqueue_script( 'jquery-ui-timepicker-addon', plugins_url('/assets/js/jquery-ui-timepicker-addon.js', $this->file) , array('jquery'), '', true);
					endif;

					wp_enqueue_script( 'wcpgsk-validate', plugins_url('/assets/js/wcpgsk-validate.js', $this->file) , '', '', false);
					wp_enqueue_script( 'wcpgsk-userjs', plugins_url('wcpgsk-user.js', $this->file) , '', '', false);

					$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
					wp_enqueue_style( 'jquery-ui-style', apply_filters('wcpgsk_jquery_ui', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', plugins_url('/assets/css/jquery-ui.css', $this->file) ), array(), '');
					
					wp_enqueue_style( 'jquery-ui-timepicker-addon', plugins_url('/assets/css/jquery-ui-timepicker-addon.css', $this->file) , array(), '');
					wp_enqueue_style( 'wcpgsk-country', plugins_url('/assets/css/wcpgsk-checkout.css', $this->file) , array(), '');
				endif;
			endif;
		}		
	}	
}