<?php

class WCML_Cart {
	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var Sitepress */
	private $sitepress;
	/** @var WooCommerce */
	private $woocommerce;

	/**
	 * WCML_Cart constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress $sitepress
	 * @param WooCommerce $woocommerce
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress, WooCommerce $woocommerce ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
		$this->woocommerce      = $woocommerce;
	}

	public function add_hooks() {

		if ( $this->is_clean_cart_enabled() ) {

			$this->enqueue_dialog_ui();

			add_action( 'wcml_removed_cart_items', array( $this, 'wcml_removed_cart_items_widget' ) );
 			add_action( 'wp_ajax_wcml_cart_clear_removed_items', array( $this, 'wcml_cart_clear_removed_items' ) );
			add_action( 'wp_ajax_nopriv_wcml_cart_clear_removed_items', array(
				$this,
				'wcml_cart_clear_removed_items'
			) );

			if( $this->is_clean_cart_enabled_for_currency_switch() ){
				add_filter( 'wcml_switch_currency_exception', array( $this, 'cart_switching_currency' ), 10, 4 );
				add_action( 'wcml_before_switch_currency', array(
					$this,
					'switching_currency_empty_cart_if_needed'
				), 10, 2 );
            }
		} else {
			//cart widget
			add_action( 'wp_ajax_woocommerce_get_refreshed_fragments', array( $this, 'wcml_refresh_fragments' ), 0 );
			add_action( 'wp_ajax_woocommerce_add_to_cart', array( $this, 'wcml_refresh_fragments' ), 0 );
			add_action( 'wp_ajax_nopriv_woocommerce_get_refreshed_fragments', array(
				$this,
				'wcml_refresh_fragments'
			), 0 );
			add_action( 'wp_ajax_nopriv_woocommerce_add_to_cart', array( $this, 'wcml_refresh_fragments' ), 0 );

			//cart
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'woocommerce_calculate_totals' ), 100 );
			add_action( 'woocommerce_get_cart_item_from_session', array( $this, 'translate_cart_contents' ) );
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'translate_cart_subtotal' ) );
			add_action( 'woocommerce_before_checkout_process', array( $this, 'wcml_refresh_cart_total' ) );
			add_filter( 'woocommerce_cart_item_data_to_validate', array( $this, 'validate_cart_item_data' ), 10, 2 );

			add_filter( 'woocommerce_cart_item_permalink', array( $this, 'cart_item_permalink' ), 10, 2 );
			add_filter( 'woocommerce_paypal_args', array( $this, 'filter_paypal_args' ) );
			add_filter( 'woocommerce_add_to_cart_sold_individually_quantity', array(
				$this,
				'add_to_cart_sold_individually_exception'
			), 10, 5 );

			$this->localize_flat_rates_shipping_classes();
		}

		add_filter( 'woocommerce_cart_needs_payment', array(
			$this,
			'use_cart_contents_total_for_needs_payment'
		), 10, 2 );

	}

	public function is_clean_cart_enabled() {

		$cart_sync_settings   = $this->woocommerce_wpml->settings['cart_sync'];
		$wpml_cookies_enabled = $this->sitepress->get_setting( $this->sitepress->get_wp_api()->constant( 'WPML_Cookie_Setting::COOKIE_SETTING_FIELD' ) );

		if (
			$wpml_cookies_enabled &&
			( $cart_sync_settings['currency_switch'] === $this->sitepress->get_wp_api()->constant( 'WCML_CART_CLEAR' ) ||
			  $cart_sync_settings['lang_switch'] === $this->sitepress->get_wp_api()->constant( 'WCML_CART_CLEAR' ) )
		) {
			return true;
		}

		return false;
	}

	private function is_clean_cart_enabled_for_currency_switch() {

		$cart_sync_settings   = $this->woocommerce_wpml->settings['cart_sync'];
		$wpml_cookies_enabled = $this->sitepress->get_setting( $this->sitepress->get_wp_api()->constant( 'WPML_Cookie_Setting::COOKIE_SETTING_FIELD' ) );

		if (
			$wpml_cookies_enabled &&
			$cart_sync_settings['currency_switch'] === $this->sitepress->get_wp_api()->constant( 'WCML_CART_CLEAR' )
		) {
			return true;
		}

		return false;
	}

	public function enqueue_dialog_ui() {

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

	}

	public function wcml_removed_cart_items_widget( $args = array() ) {

		if ( ! empty( $this->woocommerce->session ) ) {
			$removed_cart_items = new WCML_Removed_Cart_Items_UI( $args, $this->woocommerce_wpml, $this->sitepress, $this->woocommerce );
			$preview            = $removed_cart_items->get_view();

			if ( ! isset( $args['echo'] ) || $args['echo'] ) {
				echo $preview;
			} else {
				return $preview;
			}
		}

	}

	public function switching_currency_empty_cart_if_needed( $currency, $force_switch ) {
		if ( $force_switch && $this->woocommerce_wpml->settings['cart_sync']['currency_switch'] == $this->sitepress->get_wp_api()->constant( 'WCML_CART_CLEAR' ) ) {
			$this->empty_cart_if_needed( 'currency_switch' );
			$this->woocommerce->session->set( 'wcml_switched_type', 'currency' );
		}
	}

	public function empty_cart_if_needed( $switching_type ) {

		if ( $this->woocommerce_wpml->settings['cart_sync'][ $switching_type ] == $this->sitepress->get_wp_api()->constant( 'WCML_CART_CLEAR' ) ) {
			$removed_products = $this->woocommerce->session->get( 'wcml_removed_items' ) ? maybe_unserialize( $this->woocommerce->session->get( 'wcml_removed_items' ) ) : array();

			foreach ( WC()->cart->get_cart_for_session() as $item_key => $cart ) {
				if ( ! in_array( $cart['product_id'], $removed_products ) ) {
					$removed_products[] = $cart['product_id'];
				}
				WC()->cart->remove_cart_item( $item_key );
			}

			if ( ! empty( $this->woocommerce->session ) ) {
				$this->woocommerce->session->set( 'wcml_removed_items', serialize( $removed_products ) );
			}
		}
	}

	public function wcml_cart_clear_removed_items() {

		$nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wcml_clear_removed_items' ) ) {
			die( 'Invalid nonce' );
		}

		$this->woocommerce->session->__unset( 'wcml_removed_items' );
		$this->woocommerce->session->__unset( 'wcml_switched_type' );
	}

	public function cart_switching_currency( $exc, $current_currency, $new_currency, $return = false ) {

		$cart_for_session = !is_null( WC()->cart ) ? array_filter( WC()->cart->get_cart_contents() ) : false;

		if ( $this->woocommerce_wpml->settings['cart_sync']['currency_switch'] == WCML_CART_SYNC || empty( $cart_for_session ) ) {
			return $exc;
		}

		$dialog_title         = __( 'Switching currency?', 'woocommerce-multilingual' );
		$confirmation_message = __( 'Your cart is not empty! After you switched the currency, all items from the cart will be removed and you have to add them again.', 'woocommerce-multilingual' );
		$stay_in              = sprintf( __( 'Keep using %s', 'woocommerce-multilingual' ), $current_currency );
		$switch_to            = __( 'Proceed', 'woocommerce-multilingual' );

		ob_start();
		$this->cart_alert( $dialog_title, $confirmation_message, $switch_to, $stay_in, $new_currency, $current_currency );
		$html = ob_get_contents();
		ob_end_clean();

		if ( $return ) {
			return array( 'prevent_switching' => $html );
		} else {
			echo json_encode( array( 'prevent_switching' => $html ) );
		}

		return true;
	}

    public function cart_alert( $dialog_title, $confirmation_message, $switch_to, $stay_in, $switch_to_value, $stay_in_value = false, $language_switch = false ){
        if( apply_filters( 'wcml_hide_cart_alert_dialog', false ) ){
            $switching_type = $language_switch ? 'lang_switch' : 'currency_switch';
            $this->empty_cart_if_needed( $switching_type );
            return false;
        }?>
        <div id="wcml-cart-dialog-confirm" title="<?php echo esc_attr( $dialog_title ) ?>">
            <p><?php echo esc_html( $confirmation_message ); ?></p>
        </div>

	    <script type="text/javascript">
		  jQuery(document).ready(function () {
			  var dialogBox = jQuery("#wcml-cart-dialog-confirm");
			  dialogBox.dialog({
				  resizable: false,
				  draggable: false,
				  height: "auto",
				  width: 500,
				  modal: true,
				  closeOnEscape: false,
				  dialogClass: "otgs-ui-dialog wcml-cart-dialog",
				  create: function () {
					  jQuery('#jquery-ui-style-css').attr('disabled', 'disabled');
				  },
				  open: function (event, ui) {
					  jQuery(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
					  repositionDialog();
				  },
				  close: function (event, ui) {
					  jQuery('#jquery-ui-style-css').removeAttr('disabled');
				  },
				  buttons: {
					  "<?php echo $switch_to; ?>": function () {
						  jQuery(this).dialog("close");
					      <?php if( $language_switch ): ?>
						  window.location = '<?php echo esc_url( $switch_to_value, null, 'redirect' ); ?>';
					      <?php else: ?>
						  jQuery('.wcml_currency_switcher').parent().find('img').remove();
						  wcml_load_currency("<?php echo esc_js( $switch_to_value ); ?>", true);
					      <?php endif; ?>

					  },
					  "<?php echo $stay_in; ?>": function () {
						  jQuery(this).dialog("close");
					      <?php if( $language_switch ): ?>
						  window.location = '<?php echo esc_url( $stay_in_value, null, 'redirect' ); ?>';
					      <?php else: ?>
                          jQuery('.wcml_currency_switcher').each( function(){
                              jQuery(this).parent().find('img').remove();
                              jQuery(this).removeAttr('disabled');
                              jQuery(this).val('<?php echo esc_js( $stay_in_value ); ?>');
                          });
                          jQuery(document).on('click', '.wcml_currency_switcher a', wcml_switch_currency_handler );
					      <?php endif; ?>
					  }
				  }
			  });

			  jQuery(window).resize(repositionDialog);

			  function repositionDialog() {
				  var winH = jQuery(window).height() - 180;
				  jQuery('.wcml-cart-dialog').css({
					  "max-height": winH,
					  "max-width": "95%"
				  });

				  dialogBox.dialog("option", "position", {
					  my: "center",
					  at: "center",
					  of: window
				  });
			  };


		  });
	    </script>
		<?php
	}

	public function wcml_refresh_fragments() {
		WC()->cart->calculate_totals();
		$this->woocommerce_wpml->locale->wcml_refresh_text_domain();
	}

	/*
	 *  Update cart and cart session when switch language
	 */
	public function woocommerce_calculate_totals( $cart, $currency = false ) {

		$current_language = $this->sitepress->get_current_language();
		$new_cart_data    = array();

		foreach ( $cart->cart_contents as $key => $cart_item ) {
			$tr_product_id = apply_filters( 'translate_object_id', $cart_item['product_id'], 'product', false, $current_language );
			//translate custom attr labels in cart object

			if ( version_compare( WC_VERSION, '3.0.0', '<' ) && isset( $cart_item['data']->product_attributes ) ) {
				foreach ( $cart_item['data']->product_attributes as $attr_key => $product_attribute ) {
					if ( isset( $product_attribute['is_taxonomy'] ) && ! $product_attribute['is_taxonomy'] ) {
						$cart->cart_contents[ $key ]['data']->product_attributes[ $attr_key ]['name'] = $this->woocommerce_wpml->strings->translated_attribute_label(
							$product_attribute['name'],
							$product_attribute['name'],
							$tr_product_id );
					}
				}
			}

			//translate custom attr value in cart object
			if ( isset( $cart_item['variation'] ) && is_array( $cart_item['variation'] ) ) {
				foreach ( $cart_item['variation'] as $attr_key => $attribute ) {
					$cart->cart_contents[ $key ]['variation'][ $attr_key ] = $this->get_cart_attribute_translation(
						$attr_key,
						$attribute,
						$cart_item['variation_id'],
						$current_language,
						$cart_item['product_id'],
						$tr_product_id
					);
				}
			}

			if ( $currency !== false ) {
				$cart->cart_contents[ $key ]['data']->price = get_post_meta( $cart_item['product_id'], '_price', 1 );
			}

			$display_as_translated = apply_filters(  'wpml_is_display_as_translated_post_type', false, 'product' );
            if ( $cart_item['product_id'] == $tr_product_id || $display_as_translated ) {
				$new_cart_data[ $key ] = apply_filters( 'wcml_cart_contents_not_changed', $cart->cart_contents[ $key ], $key, $current_language );
	            $new_cart_data[ $key ][ 'data_hash' ]  = $this->get_data_cart_hash( $cart_item );
				continue;
			}

			if ( isset( $cart->cart_contents[ $key ]['variation_id'] ) && $cart->cart_contents[ $key ]['variation_id'] ) {
				$tr_variation_id = apply_filters( 'translate_object_id', $cart_item['variation_id'], 'product_variation', false, $current_language );
				if ( ! is_null( $tr_variation_id ) ) {
					$cart->cart_contents[ $key ]['product_id']   = intval( $tr_product_id );
					$cart->cart_contents[ $key ]['variation_id'] = intval( $tr_variation_id );
					if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '<' ) ) {
						$cart->cart_contents[ $key ]['data']->id = intval( $tr_product_id );
					} else {
						$cart->cart_contents[ $key ]['data']->set_id( intval( $tr_product_id ) );
					}
					$cart->cart_contents[ $key ]['data']->post = get_post( $tr_product_id );
				}
			} else {
				if ( ! is_null( $tr_product_id ) ) {
					$cart->cart_contents[ $key ]['product_id'] = intval( $tr_product_id );
					if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '<' ) ) {
						$cart->cart_contents[ $key ]['data']->id = intval( $tr_product_id );
					} else {
						$cart->cart_contents[ $key ]['data']->set_id( intval( $tr_product_id ) );
					}
					$cart->cart_contents[ $key ]['data']->post = get_post( $tr_product_id );
				}
			}

			if ( ! is_null( $tr_product_id ) ) {

				$new_key                   = $this->wcml_generate_cart_key( $cart->cart_contents, $key );
				$cart->cart_contents       = apply_filters( 'wcml_update_cart_contents_lang_switch', $cart->cart_contents, $key, $new_key, $current_language );
				$new_cart_data[ $new_key ] = $cart->cart_contents[ $key ];
				$new_cart_data[ $new_key ][ 'data_hash' ]  = $this->get_data_cart_hash( $new_cart_data[ $new_key ] );

				$new_cart_data = apply_filters( 'wcml_cart_contents', $new_cart_data, $cart->cart_contents, $key, $new_key );
			}
		}

		$cart->cart_contents              = $this->wcml_check_on_duplicate_products_in_cart( $new_cart_data );
		$this->woocommerce->session->cart = $cart->cart_contents;

		return $cart->cart_contents;
	}

	/**
	 * @param $cart_item array
	 *
	 * @return string
	 */
	public function get_data_cart_hash( $cart_item ) {

		$data_hash = '';

		if ( function_exists( 'wc_get_cart_item_data_hash' ) ) {
			$hash_product_object = wc_get_product( $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'] );
			if( $hash_product_object ){
				$data_hash = wc_get_cart_item_data_hash( $hash_product_object );
			}
		}

		return $data_hash;
	}

	/**
	 * @param array $item_data
	 * @param WC_Product $product Product object
	 *
	 * @return array
	 */
	public function validate_cart_item_data( array $item_data, WC_Product $product ) {

		if( $item_data['attributes'] ){

			$product_id = $product->get_parent_id();
			$product_language = $this->sitepress->get_language_for_element( $product_id, 'post_'.$item_data['type']);
			$tr_product_id = apply_filters( 'translate_object_id', $product_id, 'product', false, $product_language );

		    foreach ( $item_data['attributes'] as $key => $name ){
		        $item_data['attributes'][$key] = $this->get_cart_attribute_translation( $key, $name, $product->get_id(), $product_language, $product_id, $tr_product_id );
            }

        }

		return $item_data;
	}

	public function wcml_check_on_duplicate_products_in_cart( $cart_contents ) {

		$exists_products = array();
		remove_action( 'woocommerce_before_calculate_totals', array( $this, 'woocommerce_calculate_totals' ), 100 );

		foreach ( $cart_contents as $key => $cart_content ) {
			$cart_contents = apply_filters( 'wcml_check_on_duplicated_products_in_cart', $cart_contents, $key, $cart_content );
			if ( apply_filters( 'wcml_exception_duplicate_products_in_cart', false, $cart_content ) ) {
				continue;
			}

			$quantity = $cart_content['quantity'];

			$search_key = $this->wcml_generate_cart_key( $cart_contents, $key );
			if ( array_key_exists( $search_key, $exists_products ) ) {
				unset( $cart_contents[ $key ] );
				$cart_contents[ $exists_products[ $search_key ] ]['quantity'] = $cart_contents[ $exists_products[ $search_key ] ]['quantity'] + $quantity;
				$this->woocommerce->cart->calculate_totals();
			} else {
				$exists_products[ $search_key ] = $key;
			}
		}

		add_action( 'woocommerce_before_calculate_totals', array( $this, 'woocommerce_calculate_totals' ), 100 );

		return $cart_contents;
	}

	public function get_cart_attribute_translation( $attr_key, $attribute, $variation_id, $current_language, $product_id, $tr_product_id ) {

		$attr_translation = $attribute;

		if ( ! empty( $attribute ) ) {
			//delete 'attribute_' at the beginning
			$taxonomy = substr( $attr_key, 10, strlen( $attr_key ) - 1 );

			if ( taxonomy_exists( $taxonomy ) ) {
				if ( $this->woocommerce_wpml->attributes->is_translatable_attribute( $taxonomy ) ) {
					$term_id          = $this->woocommerce_wpml->terms->wcml_get_term_id_by_slug( $taxonomy, $attribute );
					$trnsl_term_id    = apply_filters( 'translate_object_id', $term_id, $taxonomy, true, $current_language );
					$term             = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $trnsl_term_id, $taxonomy );
					$attr_translation = $term->slug;
				}
			} elseif( $variation_id ) {

				$trnsl_attr = get_post_meta( $variation_id, $attr_key, true );

				if ( $trnsl_attr ) {
					$attr_translation = $trnsl_attr;
				} else {
					$attr_translation = $this->woocommerce_wpml->attributes->get_custom_attr_translation( $product_id, $tr_product_id, $taxonomy, $attribute );
				}
			}
		}

		return $attr_translation;
	}

	public function wcml_generate_cart_key( $cart_contents, $key ) {
		$cart_item_data = $this->get_cart_item_data_from_cart( $cart_contents[ $key ] );

		return $this->woocommerce->cart->generate_cart_id(
			$cart_contents[ $key ]['product_id'],
			$cart_contents[ $key ]['variation_id'],
			$cart_contents[ $key ]['variation'],
			$cart_item_data
		);
	}

	//get cart_item_data from existing cart array ( from session )
	public function get_cart_item_data_from_cart( $cart_contents ) {
		unset( $cart_contents['product_id'] );
		unset( $cart_contents['variation_id'] );
		unset( $cart_contents['variation'] );
		unset( $cart_contents['quantity'] );
		unset( $cart_contents['line_total'] );
		unset( $cart_contents['line_subtotal'] );
		unset( $cart_contents['line_tax'] );
		unset( $cart_contents['line_subtotal_tax'] );
		unset( $cart_contents['line_tax_data'] );
		unset( $cart_contents['data'] );
		unset( $cart_contents['key'] );

		return apply_filters( 'wcml_filter_cart_item_data', $cart_contents );
	}

	public function translate_cart_contents( $item ) {

		// translate the product id and product data
		$item['product_id'] = apply_filters( 'translate_object_id', $item['product_id'], 'product', true );
		if ( $item['variation_id'] ) {
			$item['variation_id'] = apply_filters( 'translate_object_id', $item['variation_id'], 'product_variation', true );
		}

		$item_product_title = $item['variation_id'] ? get_the_title( $item['variation_id'] ) : get_the_title( $item['product_id'] );

		if ( $this->sitepress->get_wp_api()->version_compare( $this->sitepress->get_wp_api()->constant( 'WC_VERSION' ), '3.0.0', '>=' ) ) {
			$item['data']->set_name( $item_product_title );
		} else {
			$item['data']->post->post_title = $item_product_title;
		}

		return $item;
	}

	public function translate_cart_subtotal( $cart ) {

		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			//special case: check if attachment loading
			$attachments = array( 'png', 'jpg', 'jpeg', 'gif', 'js', 'css' );

			foreach ( $attachments as $attachment ) {
				$match = preg_match( '/\.' . $attachment . '$/', $_SERVER['REQUEST_URI'] );
				if ( ! empty( $match ) ) {
					return false;
				}
			}
		}

		if ( apply_filters( 'wcml_calculate_totals_exception', true, $cart ) ) {
			$cart->calculate_totals();
		}

	}

	// refresh cart total to return correct price from WC object
	public function wcml_refresh_cart_total() {
		WC()->cart->calculate_totals();
	}


	public function localize_flat_rates_shipping_classes() {

		if ( is_ajax() && isset( $_POST['action'] ) && $_POST['action'] == 'woocommerce_update_order_review' ) {
			$this->woocommerce->shipping->load_shipping_methods();
			$shipping_methods = $this->woocommerce->shipping->get_shipping_methods();
			foreach ( $shipping_methods as $method ) {
				if ( isset( $method->flat_rate_option ) ) {
					add_filter( 'option_' . $method->flat_rate_option, array( $this, 'translate_shipping_class' ) );
				}
			}

		}
	}

	public function translate_shipping_class( $rates ) {

		if ( is_array( $rates ) ) {
			foreach ( $rates as $shipping_class => $value ) {
				$term_id = $this->woocommerce_wpml->terms->wcml_get_term_id_by_slug( 'product_shipping_class', $shipping_class );

				if ( $term_id && ! is_wp_error( $term_id ) ) {
					$translated_term_id = apply_filters( 'translate_object_id', $term_id, 'product_shipping_class', true );
					if ( $translated_term_id != $term_id ) {
						$term = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $translated_term_id, 'product_shipping_class' );
						unset( $rates[ $shipping_class ] );
						$rates[ $term->slug ] = $value;

					}
				}
			}
		}

		return $rates;
	}

	public function filter_paypal_args( $args ) {
		$args['lc'] = $this->sitepress->get_current_language();

		//filter URL when default permalinks uses
		$wpml_settings = $this->sitepress->get_settings();
		if ( $wpml_settings['language_negotiation_type'] == 3 ) {
			$args['notify_url'] = str_replace( '%2F&', '&', $args['notify_url'] );
		}

		return $args;
	}

	public function add_to_cart_sold_individually_exception( $qt, $quantity, $product_id, $variation_id, $cart_item_data ) {

		$post_id = $product_id;
		if ( $variation_id ) {
			$post_id = $variation_id;
		}

		//check if product already added to cart in another language
		foreach ( WC()->cart->cart_contents as $cart_item ) {

			if ( $this->sold_individually_product( $cart_item, $cart_item_data, $post_id, $quantity ) ) {

				$this->sold_individually_exception( $post_id );

			}
		}

		return $qt;
	}

	public function sold_individually_product( $cart_item, $cart_item_data, $post_id, $quantity ) {

		$current_product_trid = $this->sitepress->get_element_trid( $post_id, 'post_' . get_post_type( $post_id ) );

		if ( $cart_item['variation_id'] ) {
			$cart_element_trid = $this->sitepress->get_element_trid( $cart_item['variation_id'], 'post_product_variation' );
		} else {
			$cart_element_trid = $this->sitepress->get_element_trid( $cart_item['product_id'], 'post_product' );
		}

		if ( apply_filters( 'wcml_add_to_cart_sold_individually', true, $cart_item_data, $post_id, $quantity ) &&
		     $current_product_trid == $cart_element_trid &&
		     $cart_item['quantity'] > 0
		) {
			return true;
		} else {
			return false;
		}
	}

	public function sold_individually_exception( $post_id ) {

		$wc_cart_url   = esc_url( wc_get_cart_url() );
		$message_title = sprintf( esc_html__( 'You cannot add another &quot;%s&quot; to your cart.', 'woocommerce' ), get_the_title( $post_id ) );

		$message = '<a href="' . $wc_cart_url . '" class="button wc-forward">' . esc_html__( 'View Cart', 'woocommerce' ) . '</a>';
		$message .= ' ' . $message_title;

		throw new Exception( $message );

	}

	/**
	 * @param bool $needs
	 * @param WC_Cart $cart
	 *
	 * @return bool
	 */
	public function use_cart_contents_total_for_needs_payment( $needs, $cart ) {

		if ( version_compare( WC()->version, '3.2', '<' ) ) {
			$needs = ( isset( $cart->cart_contents_total ) && $cart->cart_contents_total > 0 )
			         || ( isset( $cart->total ) && $cart->total > 0 )
			         || isset( $cart->recurring_carts );
		}

		return $needs;
	}

	/**
	 * @param string $permalink
	 * @param array $cart_item
	 *
	 * @return string
	 */
	public function cart_item_permalink( $permalink, $cart_item ) {

		if ( ! $this->sitepress->get_setting( 'auto_adjust_ids' ) ) {
			$permalink = get_permalink( $cart_item['product_id'] );
		}

		return $permalink;
	}

}