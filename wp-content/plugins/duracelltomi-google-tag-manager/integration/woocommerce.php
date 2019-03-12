<?php
define( 'GTM4WP_WPFILTER_EEC_PRODUCT_ARRAY', 'gtm4wp_eec_product_array' );
define( 'GTM4WP_WPFILTER_EEC_CART_ITEM',     'gtm4wp_eec_cart_item' );
define( 'GTM4WP_WPFILTER_EEC_ORDER_ITEM',    'gtm4wp_eec_order_item' );

$gtm4wp_product_counter   = 0;
$gtm4wp_last_widget_title = "Sidebar Products";
if ( function_exists ( 'WC' ) ) {
	$GLOBALS["gtm4wp_is_woocommerce3"] = version_compare( WC()->version, "3.0", ">=" );
} else {
	$GLOBALS["gtm4wp_is_woocommerce3"] = false;
}

function gtm4wp_woocommerce_addjs( $js ) {
	$woo = WC();

	if ( version_compare( $woo->version, "2.1", ">=" ) ) {
		wc_enqueue_js( $js );
	} else {
		$woo->add_inline_js( $js );
	}
}

// from https://snippets.webaware.com.au/ramblings/php-really-doesnt-unicode/
function gtm4wp_untexturize($fancy) {
	$fixes = false;

	if ( $fixes === false ) {
		$fixes = array(
			json_decode('"\u201C"') => '"',     // left  double quotation mark
			json_decode('"\u201D"') => '"',     // right double quotation mark
			json_decode('"\u2018"') => "'",     // left  single quotation mark
			json_decode('"\u2019"') => "'",     // right single quotation mark
			json_decode('"\u2032"') => "'",     // prime (minutes, feet)
			json_decode('"\u2033"') => '"',     // double prime (seconds, inches)
			json_decode('"\u2013"') => '-',     // en dash
			json_decode('"\u2014"') => '--',    // em dash
		);
	}

	$normal = strtr($fancy, $fixes);

	return $normal;
}

function gtm4wp_woocommerce_html_entity_decode( $val ) {
	return gtm4wp_untexturize( html_entity_decode( $val, ENT_QUOTES, "utf-8" ) );
}

function gtm4wp_prefix_productid( $product_id ) {
	global $gtm4wp_options;

	if ( "" != $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMPRODIDPREFIX ] ) {
		return $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMPRODIDPREFIX ] . $product_id;
	} else {
		return $product_id;
	}
}

// from https://stackoverflow.com/questions/1252693/using-str-replace-so-that-it-only-acts-on-the-first-match
function gtm4wp_str_replace_first($from, $to, $subject) {
	$from = '/'.preg_quote($from, '/').'/';

	return preg_replace($from, $to, $subject, 1);
}

function gtm4wp_get_product_category_hierarchy( $category_id ) {
	$cat_hierarchy = "";

	$category_parent_list = get_term_parents_list( $category_id, 'product_cat', array(
		"format" => "name",
		"separator" => "/",
		"link" => false,
		"inclusive" => true
	));

	if ( is_string( $category_parent_list ) ) {
		$cat_hierarchy = trim( $category_parent_list, '/' );
	}

	return $cat_hierarchy;
}

function gtm4wp_get_product_category( $product_id, $fullpath = false) {
  $product_cat = "";

	$_product_cats = get_the_terms( $product_id, 'product_cat' );
	if ( ( is_array($_product_cats) ) && ( count( $_product_cats ) > 0 ) ) {
		$first_product_cat = array_pop( $_product_cats );
		if ( $fullpath ) {
			$product_cat = gtm4wp_get_product_category_hierarchy( $first_product_cat->term_id );
		} else {
			$product_cat = $first_product_cat->name;
		}
	}

	return $product_cat;
}

function gtm4wp_woocommerce_addglobalvars($return = '') {
	global $gtm4wp_options;

	$return .= "
	var gtm4wp_use_sku_instead        = " . (int)($gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSESKU ]) . ";
	var gtm4wp_id_prefix              = '" . esc_js( gtm4wp_prefix_productid("") ) . "';
	var gtm4wp_remarketing            = " . gtm4wp_escjs_boolean((boolean)($gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ])) . ";
	var gtm4wp_eec                    = " . gtm4wp_escjs_boolean((boolean)($gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ])) . ";
	var gtm4wp_classicec              = " . gtm4wp_escjs_boolean((boolean)($gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKCLASSICEC ])) . ";
	var gtm4wp_currency               = '" . esc_js( get_woocommerce_currency() ) . "';
	var gtm4wp_product_per_impression = " . (int)($gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCPRODPERIMPRESSION ]) . ";";

	return $return;
}

function gtm4wp_woocommerce_datalayer_filter_items( $dataLayer ) {
	global $gtm4wp_options, $wp_query, $gtm4wp_datalayer_name, $gtm4wp_product_counter, $gtm4wp_is_woocommerce3;

	$woo = WC();

	if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCCUSTOMERDATA ] && $gtm4wp_is_woocommerce3 ) {
		// we need to use this instead of $woo->customer as this will load proper total order number and value from the database instead of the session
		$woo_customer = new WC_Customer( $woo->customer->get_id() );

		$dataLayer["customerTotalOrders"]      = $woo_customer->get_order_count();
		$dataLayer["customerTotalOrderValue"]  = $woo_customer->get_total_spent();

		$dataLayer["customerFirstName"]        = $woo_customer->get_first_name();
		$dataLayer["customerLastName"]         = $woo_customer->get_last_name();

		$dataLayer["customerBillingFirstName"] = $woo_customer->get_billing_first_name();
		$dataLayer["customerBillingLastName"]  = $woo_customer->get_billing_last_name();
		$dataLayer["customerBillingCompany"]   = $woo_customer->get_billing_company();
		$dataLayer["customerBillingAddress1"]  = $woo_customer->get_billing_address_1();
		$dataLayer["customerBillingAddress2"]  = $woo_customer->get_billing_address_2();
		$dataLayer["customerBillingCity"]      = $woo_customer->get_billing_city();
		$dataLayer["customerBillingPostcode"]  = $woo_customer->get_billing_postcode();
		$dataLayer["customerBillingCountry"]   = $woo_customer->get_billing_country();
		$dataLayer["customerBillingEmail"]     = $woo_customer->get_billing_email();
		$dataLayer["customerBillingPhone"]     = $woo_customer->get_billing_phone();

		$dataLayer["customerShippingFirstName"] = $woo_customer->get_shipping_first_name();
		$dataLayer["customerShippingLastName"]  = $woo_customer->get_shipping_last_name();
		$dataLayer["customerShippingCompany"]   = $woo_customer->get_shipping_company();
		$dataLayer["customerShippingAddress1"]  = $woo_customer->get_shipping_address_1();
		$dataLayer["customerShippingAddress2"]  = $woo_customer->get_shipping_address_2();
		$dataLayer["customerShippingCity"]      = $woo_customer->get_shipping_city();
		$dataLayer["customerShippingPostcode"]  = $woo_customer->get_shipping_postcode();
		$dataLayer["customerShippingCountry"]   = $woo_customer->get_shipping_country();
	}

	if ( is_product_category() || is_product_tag() || is_front_page() || is_shop() ) {
		$ecomm_pagetype = "category";
		if ( is_front_page() ) {
			$ecomm_pagetype = "home";
		} else if ( is_search() ) {
			$ecomm_pagetype = "searchresults";
		}

		if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ] ) {
			$dataLayer["ecomm_prodid"] = array();
			$dataLayer["ecomm_pagetype"] = $ecomm_pagetype;
			$dataLayer["ecomm_totalvalue"] = 0;
		}
	} else if ( is_product() ) {
		if ( ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ] ) || ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) ) {
			$postid     = get_the_ID();
			$product    = wc_get_product( $postid );
			$product_id = $product->get_id();

			$product_cat = gtm4wp_get_product_category( $product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );

			if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSESKU ] ) {
				$product_sku = $product->get_sku();
				if ( "" != $product_sku ) {
					$product_id = $product_sku;
				}
			}
			$remarketing_id = (string)$product_id;
			$product_price = $product->get_price();

			if ( "variable" != $product->get_type() ) {
				$dataLayer["productIsVariable"] = 0;

				$_temp_productdata = array(
					"name"       => gtm4wp_woocommerce_html_entity_decode( get_the_title() ),
					"id"         => $product_id,
					"price"      => $product_price,
					"category"   => $product_cat,
					"stocklevel" => $product->get_stock_quantity()
				);
				$eec_product_array = apply_filters( GTM4WP_WPFILTER_EEC_PRODUCT_ARRAY, $_temp_productdata, "productdetail" );

				if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ] ) {
					$dataLayer["ecomm_prodid"] = gtm4wp_prefix_productid( $remarketing_id );
					$dataLayer["ecomm_pagetype"] = "product";
					$dataLayer["ecomm_totalvalue"] = (float)$eec_product_array[ "price" ];
				}

				if ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) {
					$dataLayer["ecommerce"] = array(
						"detail" => array(
							"products" => array($eec_product_array)
						)
					);
				}
			} else {
				$dataLayer["productIsVariable"] = 1;

				$dataLayer["ecomm_prodid"] = gtm4wp_prefix_productid( $remarketing_id );
				$dataLayer["ecomm_pagetype"] = "product";
				$dataLayer["ecomm_totalvalue"] = $product_price;
			}
		}
	} else if ( is_cart() ) {
    // moved to gtm4wp-woocommerce-enhanced.js
    /*
		if ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) {
			gtm4wp_woocommerce_addjs("
				$( document ).on( 'click', '[name=update_cart]', function() {
					gtm4wp_handle_cart_qty_change();
				});

				$( document ).on( 'keypress', '.woocommerce-cart-form input[type=number]', function() {
					gtm4wp_handle_cart_qty_change();
				});

				function gtm4wp_handle_cart_qty_change() {
					$( '.product-quantity input.qty' ).each(function() {
						var _original_value = $( this ).prop( 'defaultValue' );

						var _current_value  = parseInt( $( this ).val() );
						if ( isNaN( _current_value ) ) {
							_current_value = _original_value;
						}

						if ( _original_value != _current_value ) {
							var productdata = $( this ).closest( '.cart_item' ).find( '.remove' );

							if ( _original_value < _current_value ) {
								". $gtm4wp_datalayer_name .".push({
									'event': 'gtm4wp.addProductToCartEEC',
									'ecommerce': {
										'currencyCode': '".get_woocommerce_currency()."',
										'add': {
											'products': [{
												'name':     productdata.data( 'gtm4wp_product_name' ),
												'id':       productdata.data( 'gtm4wp_product_id' ),
												'price':    productdata.data( 'gtm4wp_product_price' ),
												'category': productdata.data( 'gtm4wp_product_cat' ),
												'variant':  productdata.data( 'gtm4wp_product_variant' ),
												'quantity': _current_value - _original_value
											}]
										}
									}
								});
							} else {
								". $gtm4wp_datalayer_name .".push({
									'event': 'gtm4wp.removeFromCartEEC',
									'ecommerce': {
										'currencyCode': '".get_woocommerce_currency()."',
										'remove': {
											'products': [{
												'name':     productdata.data( 'gtm4wp_product_name' ),
												'id':       productdata.data( 'gtm4wp_product_id' ),
												'price':    productdata.data( 'gtm4wp_product_price' ),
												'category': productdata.data( 'gtm4wp_product_cat' ),
												'variant':  productdata.data( 'gtm4wp_product_variant' ),
												'quantity': _original_value - _current_value
											}]
										}
									}
								});
							}
						} // end if qty changed
					}); // end each qty field
				}; // end gtm4wp_handle_cart_qty_change()
			");
		}
	*/

		if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ] || $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCEECCARTASFIRSTSTEP ] ) {
			$gtm4wp_cart_products             = array();
			$gtm4wp_cart_products_remarketing = array();

			foreach( $woo->cart->get_cart() as $cart_item_id => $cart_item_data) {
				$product = apply_filters( 'woocommerce_cart_item_product', $cart_item_data["data"], $cart_item_data, $cart_item_id );

				if ( ! apply_filters( GTM4WP_WPFILTER_EEC_CART_ITEM, true, $cart_item_data ) ) {
					continue;
				}

				$product_id   = $product->get_id();
				$product_type = $product->get_type();

				if ( "variation" == $product_type ) {
					$parent_product_id = ( $gtm4wp_is_woocommerce3 ? $product->get_parent_id() : $product->id);
					$product_cat = gtm4wp_get_product_category( $parent_product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );
				} else {
					$product_cat = gtm4wp_get_product_category( $product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );
				}

				$remarketing_id = $product_id;
				$product_sku    = $product->get_sku();
				if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSESKU ] && ( "" != $product_sku ) ) {
					$remarketing_id = $product_sku;
				}

				if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCEECCARTASFIRSTSTEP ] ) {
					$_temp_productdata = array(
						"id"         => $remarketing_id,
						"name"       => $product->get_title(),
						"price"      => $product->get_price(),
						"category"   => $product_cat,
						"stocklevel" => $product->get_stock_quantity(),
						"quantity"   => $cart_item_data["quantity"]
					);

					if ( "variation" == $product_type ) {
						$_temp_productdata[ "variant" ] = implode(",", $product->get_variation_attributes());
					}

					$eec_product_array = apply_filters( GTM4WP_WPFILTER_EEC_PRODUCT_ARRAY, $_temp_productdata, "cart" );
					$gtm4wp_cart_products[] = $eec_product_array;
				}

				$gtm4wp_cart_products_remarketing[] = gtm4wp_prefix_productid( $remarketing_id );
			}

			if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ] ) {
				$dataLayer["ecomm_prodid"] = $gtm4wp_cart_products_remarketing;
				$dataLayer["ecomm_pagetype"] = "cart";
				if ( ! $woo->cart->prices_include_tax ) {
					$cart_total = $woo->cart->cart_contents_total;
				} else {
					$cart_total = $woo->cart->cart_contents_total + $woo->cart->tax_total;
				}
				$dataLayer["ecomm_totalvalue"] = (float)$cart_total;
			}

			if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCEECCARTASFIRSTSTEP ] ) {
				$dataLayer["ecommerce"] = array(
					"checkout" => array(
						"actionField" => array(
							"step" => 1
						),
						"products" => $gtm4wp_cart_products
					)
				);
			}
		}
	} else if ( is_order_received_page() ) {
		$do_not_flag_tracked_order = (boolean)($gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCNOORDERTRACKEDFLAG ]);
		$order_id                  = empty( $_GET[ "order" ] ) ? ( $GLOBALS[ "wp" ]->query_vars[ "order-received" ] ? $GLOBALS[ "wp" ]->query_vars[ "order-received" ] : 0 ) : absint( $_GET[ "order" ] );
		$order_id_filtered         = apply_filters( 'woocommerce_thankyou_order_id', $order_id );
		if ( "" != $order_id_filtered ) {
			$order_id = $order_id_filtered;
		}

		$order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET[ "key" ] ) ? "" : wc_clean( $_GET[ "key" ] ) );

		if ( $order_id > 0 ) {
			$order = wc_get_order( $order_id );

			if ( $order instanceof WC_Order ) {
				if ( $gtm4wp_is_woocommerce3 ) {
					$this_order_key = $order->get_order_key();
				} else {
					$this_order_key = $order->order_key;
				}

				if ( $this_order_key != $order_key ) {
					unset( $order );
				}
			} else {
				unset( $order );
			}
		}

		if ( ( 1 == get_post_meta( $order_id, '_ga_tracked', true ) ) && !$do_not_flag_tracked_order ) {
			unset( $order );
		}

		if ( isset( $order ) ) {
		  if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCEXCLUDETAX ] ) {
		  	$order_revenue = $order->get_total() - $order->get_total_tax();
			} else {
		  	$order_revenue = $order->get_total();
			}

			if ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKCLASSICEC ] ) {
				$dataLayer["transactionId"]             = $order->get_order_number();
				$dataLayer["transactionDate"]           = date("c");
				$dataLayer["transactionType"]           = "sale";
				$dataLayer["transactionAffiliation"]    = "";
				$dataLayer["transactionTotal"]          = $order_revenue;
				if ( $gtm4wp_is_woocommerce3 ) {
					$dataLayer["transactionShipping"]       = $order->get_shipping_total();
				} else {
					$dataLayer["transactionShipping"]       = $order->get_total_shipping();
				}
				$dataLayer["transactionTax"]            = $order->get_total_tax();
				$dataLayer["transactionPaymentType"]    = ( $gtm4wp_is_woocommerce3 ? $order->get_payment_method_title() : $order->payment_method_title);
				$dataLayer["transactionCurrency"]       = get_woocommerce_currency();
				$dataLayer["transactionShippingMethod"] = $order->get_shipping_method();
				$dataLayer["transactionPromoCode"]      = implode( ", ", $order->get_used_coupons() );
			}

			if ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) {
				$dataLayer["ecommerce"] = array(
					"currencyCode" => get_woocommerce_currency(),
					"purchase" => array(
						"actionField" => array(
							"id"          => $order->get_order_number(),
							"affiliation" => "",
							"revenue"     => $order_revenue,
							"tax"         => $order->get_total_tax(),
							"shipping"    => ( $gtm4wp_is_woocommerce3 ? $order->get_shipping_total() : $order->get_total_shipping()),
							"coupon"      => implode( ", ", $order->get_used_coupons() )
						)
					)
				);
			}

			$_products = array();
			$_sumprice = 0;
			$_product_ids = array();

			if ( $order->get_items() ) {
				foreach ( $order->get_items() as $item ) {

					if ( ! apply_filters( GTM4WP_WPFILTER_EEC_ORDER_ITEM, true, $item ) ) {
						continue;
					}

					$product = ( $gtm4wp_is_woocommerce3 ? $item->get_product() : $order->get_product_from_item( $item ) ) ;

					$product_id   = $product->get_id();
					$product_type = $product->get_type();

					if ( "variation" == $product_type ) {
						$parent_product_id = ( $gtm4wp_is_woocommerce3 ? $product->get_parent_id() : $product->id);
						$product_cat = gtm4wp_get_product_category( $parent_product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );
					} else {
						$product_cat = gtm4wp_get_product_category( $product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );
					}

					$remarketing_id = $product_id;
					$product_sku    = $product->get_sku();
					if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSESKU ] && ( "" != $product_sku ) ) {
						$remarketing_id = $product_sku;
					}

					$product_price = $order->get_item_total( $item );
					$_temp_productdata = array(
						"id"         => $remarketing_id,
						"name"       => $product->get_title(),
						"sku"        => $product_sku ? $product_sku : $product_id,
						"category"   => $product_cat,
						"price"      => $product_price,
						"currency"   => get_woocommerce_currency(),
						"stocklevel" => $product->get_stock_quantity(),
						"quantity"   => $item['qty']
					);

					if ( "variation" == $product_type ) {
						$_temp_productdata[ "variant" ] = implode(",", $product->get_variation_attributes());
					}

					$eec_product_array = apply_filters( GTM4WP_WPFILTER_EEC_PRODUCT_ARRAY, $_temp_productdata, "purchase" );
					$_products[] = $eec_product_array;

					$_sumprice += $product_price * $eec_product_array[ "quantity" ];
					$_product_ids[] = gtm4wp_prefix_productid( $remarketing_id );
				}
			}

			if ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKCLASSICEC ] ) {
				$dataLayer["transactionProducts"] = $_products;
				$dataLayer["event"] = "gtm4wp.orderCompleted";
			}

			if ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) {
				$dataLayer["ecommerce"]["purchase"]["products"] = $_products;
				$dataLayer["event"] = "gtm4wp.orderCompletedEEC";
			}

			if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ] ) {
				$dataLayer["ecomm_prodid"] = $_product_ids;
				$dataLayer["ecomm_pagetype"] = "purchase";
				$dataLayer["ecomm_totalvalue"] = (float)$_sumprice;
			}

			if ( !$do_not_flag_tracked_order ) {
				update_post_meta( $order_id, '_ga_tracked', 1 );
			}
		}
	} else if ( is_checkout() ) {
		if ( ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) || ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ] ) ) {
			$gtm4wp_checkout_products             = array();
			$gtm4wp_checkout_products_remarketing = array();
			$gtm4wp_totalvalue                    = 0;

			foreach( $woo->cart->get_cart() as $cart_item_id => $cart_item_data) {
				$product = apply_filters( 'woocommerce_cart_item_product', $cart_item_data["data"], $cart_item_data, $cart_item_id );

				if ( ! apply_filters( GTM4WP_WPFILTER_EEC_CART_ITEM, true, $cart_item_data ) ) {
					continue;
				}

				$product_id = $product->get_id();
				$product_type = $product->get_type();

				if ( "variation" == $product_type ) {
					$parent_product_id = ( $gtm4wp_is_woocommerce3 ? $product->get_parent_id() : $product->id);
					$product_cat = gtm4wp_get_product_category( $parent_product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );
				} else {
					$product_cat = gtm4wp_get_product_category( $product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );
				}

				$remarketing_id = $product_id;
				$product_sku    = $product->get_sku();
				if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSESKU ] && ( "" != $product_sku ) ) {
					$remarketing_id = $product_sku;
				}

				$_temp_productdata = array(
					"id"         => $remarketing_id,
					"name"       => $product->get_title(),
					"price"      => $product->get_price(),
					"category"   => $product_cat,
					"stocklevel" => $product->get_stock_quantity(),
					"quantity"   => $cart_item_data["quantity"]
				);

				if ( "variation" == $product_type ) {
					$_temp_productdata[ "variant" ] = implode(",", $product->get_variation_attributes());
				}

				$eec_product_array = apply_filters( GTM4WP_WPFILTER_EEC_PRODUCT_ARRAY, $_temp_productdata, "checkout" );
				$gtm4wp_checkout_products[] = $eec_product_array;

				$gtm4wp_checkout_products_remarketing[] = gtm4wp_prefix_productid( $remarketing_id );
				$gtm4wp_totalvalue += $eec_product_array[ "quantity" ] * $eec_product_array[ "price" ];
			} // end foreach cart item

			if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ] ) {
				$dataLayer["ecomm_prodid"] = $gtm4wp_checkout_products_remarketing;
				$dataLayer["ecomm_pagetype"] = "cart";
				$dataLayer["ecomm_totalvalue"] = (float)$gtm4wp_totalvalue;
			}

			if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) {
				$dataLayer["ecommerce"] = array(
					"checkout" => array(
						"actionField" => array(
							"step" => 1 + (int)$gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCEECCARTASFIRSTSTEP ]
						),
						"products" => $gtm4wp_checkout_products
					)
				);

				gtm4wp_woocommerce_addjs("
					var gtm4wp_checkout_products    = " . json_encode($gtm4wp_checkout_products) . ";
					var gtm4wp_checkout_step_offset = " . (int)$gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCEECCARTASFIRSTSTEP ] . ";");

// moved to gtm4wp-woocommerce-enhanced.js
/*
				gtm4wp_woocommerce_addjs("
					var gtm4wp_checkout_products    = " . json_encode($gtm4wp_checkout_products) . ";
					var gtm4wp_checkout_step_offset = " . (int)$gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCEECCARTASFIRSTSTEP ] . ";
					var gtm4wp_checkout_step_fired  = []; // step 1 will be the billing section which is reported during pageload, no need to handle here

					$( document ).on( 'blur', 'input[name^=shipping_]:not(input[name=shipping_method])', function() {
					  // do not report checkout step if already reported
						if ( gtm4wp_checkout_step_fired.indexOf( 'shipping' ) > -1 ) {
							return;
						}

					  // do not report checkout step if user is traversing through the section without filling in any data
						if ( $( this ).val().trim() == '' ) {
							return;
						}

						". $gtm4wp_datalayer_name .".push({
							'event': 'gtm4wp.checkoutStepEEC',
							'ecommerce': {
								'checkout': {
									'actionField': {
										'step': 2 + gtm4wp_checkout_step_offset
									},
									'products': gtm4wp_checkout_products
								}
							}
						});

						gtm4wp_checkout_step_fired.push( 'shipping' );
					});

					$( document ).on( 'click', 'input[name=shipping_method]', function() {
					  // do not report checkout step if already reported
						if ( gtm4wp_checkout_step_fired.indexOf( 'shipping_method' ) > -1 ) {
							return;
						}

						// do not fire event during page load
						if ( 'complete' != document.readyState ) {
							return;
						}

						". $gtm4wp_datalayer_name .".push({
							'event': 'gtm4wp.checkoutStepEEC',
							'ecommerce': {
								'checkout': {
									'actionField': {
										'step': 3 + gtm4wp_checkout_step_offset
									},
									'products': gtm4wp_checkout_products
								}
							}
						});

						gtm4wp_checkout_step_fired.push( 'shipping_method' );
					});

					$( document ).on( 'click', 'input[name=payment_method]', function() {
					  // do not report checkout step if already reported
						if ( gtm4wp_checkout_step_fired.indexOf( 'payment_method' ) > -1 ) {
							return;
						}

						// do not fire event during page load
						if ( 'complete' != document.readyState ) {
							return;
						}

						". $gtm4wp_datalayer_name .".push({
							'event': 'gtm4wp.checkoutStepEEC',
							'ecommerce': {
								'checkout': {
									'actionField': {
										'step': 4 + gtm4wp_checkout_step_offset
									},
									'products': gtm4wp_checkout_products
								}
							}
						});

						gtm4wp_checkout_step_fired.push( 'payment_method' );
					});

					$( 'form[name=checkout]' ).on( 'submit', function() {
					  console.log('form submit');

						if ( gtm4wp_checkout_step_fired.indexOf( 'shipping_method' ) == -1 ) {
							// shipping methods are not visible if only one is available
							// and if the user has already a pre-selected method, no click event will fire to report the checkout step
							$( 'input[name=shipping_method]:checked' ).trigger( 'click' );

							gtm4wp_checkout_step_fired.push( 'shipping_method' );
						}

						if ( gtm4wp_checkout_step_fired.indexOf( 'payment_method' ) == -1 ) {
							// if the user has already a pre-selected method, no click event will fire to report the checkout step
							$( 'input[name=payment_method]:checked' ).trigger( 'click' );

							gtm4wp_checkout_step_fired.push( 'payment_method' );
						}

						var _shipping_el = $( '#shipping_method input:checked' );
						if ( _shipping_el.length > 0 ) {
							". $gtm4wp_datalayer_name .".push({
								'event': 'gtm4wp.checkoutOptionEEC',
								'ecommerce': {
									'checkout_option': {
										'actionField': {
											'step': 3 + gtm4wp_checkout_step_offset,
											'option': 'Shipping: ' + _shipping_el.val()
										}
									}
								}
							});
						}

						var _payment_el = $( '.payment_methods input:checked' );
						if ( _payment_el.length > 0 ) {
							". $gtm4wp_datalayer_name .".push({
								'event': 'gtm4wp.checkoutOptionEEC',
								'ecommerce': {
									'checkout_option': {
										'actionField': {
											'step': 4 + gtm4wp_checkout_step_offset,
											'option': 'Payment: ' + _payment_el.val()
										}
									}
								}
							});
						}
					});");
*/
			}
		}
	} else {
		if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ] ) {
			$dataLayer["ecomm_pagetype"] = "other";
		}
	}

  // moved to gtm4wp-woocommerce-enhanced.js
  /*
	if ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) {
		gtm4wp_woocommerce_addjs("
	$( document ).on( 'click', '.mini_cart_item a.remove,.product-remove a.remove', function() {
		var productdata = $( this );

		var qty = 0;
		var qty_element = $( this ).closest( '.cart_item' ).find( '.product-quantity input.qty' );
		if ( 0 == qty_element.length ) {
			qty_element = $( this ).closest( '.mini_cart_item' ).find( '.quantity' );
			if ( qty_element.length > 0 ) {
				qty = parseInt( qty_element.text() );

				if ( isNaN( qty ) ) {
					qty = 0;
				}
			}
		} else {
			qty = qty_element.val();
		}

		if ( 0 == qty ) {
			return true;
		}

		". $gtm4wp_datalayer_name .".push({
			'event': 'gtm4wp.removeFromCartEEC',
			'ecommerce': {
				'remove': {
					'products': [{
						'name':     productdata.data( 'gtm4wp_product_name' ),
						'id':       productdata.data( 'gtm4wp_product_id' ),
						'price':    productdata.data( 'gtm4wp_product_price' ),
						'category': productdata.data( 'gtm4wp_product_cat' ),
						'variant':  productdata.data( 'gtm4wp_product_variant' ),
						'quantity': qty
					}]
				}
			}
		});
	});
		");
	}
	*/

	if ( isset ( $_COOKIE[ "gtm4wp_product_readded_to_cart" ] ) ) {
		$cart_item  = $woo->cart->get_cart_item( $_COOKIE[ "gtm4wp_product_readded_to_cart" ] );
		if ( !empty( $cart_item ) ) {
		  $product    = $cart_item["data"];

			$product_id   = $product->get_id();
			$product_type = $product->get_type();

			if ( "variation" == $product_type ) {
				$parent_product_id = ( $gtm4wp_is_woocommerce3 ? $product->get_parent_id() : $product->id);
				$product_cat = gtm4wp_get_product_category( $parent_product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );
			} else {
				$product_cat = gtm4wp_get_product_category( $product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );
			}

			$remarketing_id = $product_id;
			$product_sku    = $product->get_sku();
			if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSESKU ] && ( "" != $product_sku ) ) {
				$remarketing_id = $product_sku;
			}

			$dataLayer["ecommerce"]["add"]["products"][] = array(
				"name"       => $product->get_title(),
				"id"         => $remarketing_id,
				"price"      => $product->get_price(),
				"category"   => $product_cat,
				"stocklevel" => $product->get_stock_quantity(),
				"quantity"   => $cart_item["quantity"]
			);

			if ( "variation" == $product_type ) {
				$dataLayer["ecommerce"]["add"][0][ "variant" ] = implode(",", $product->get_variation_attributes());
			}
		}

		gtm4wp_woocommerce_addjs( "document.cookie = 'gtm4wp_product_readded_to_cart=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';" );
		unset( $_COOKIE[ "gtm4wp_product_readded_to_cart" ] );
	}

	return $dataLayer;
}

function gtm4wp_woocommerce_single_add_to_cart_tracking() {
	global $product, $gtm4wp_datalayer_name, $gtm4wp_options;

	// exit early if there is nothing to do
	if ( ( false === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKCLASSICEC ] ) && ( false === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) ) {
		return;
	}

	$product_id = $product->get_id();

	$product_cat = gtm4wp_get_product_category( $product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );

	$remarketing_id = $product_id;
	$product_sku    = $product->get_sku();
	if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSESKU ] && ( "" != $product_sku ) ) {
		$remarketing_id = $product_sku;
	}

	$_temp_productdata = array(
		"id"         => $remarketing_id,
		"name"       => $product->get_title(),
		"sku"        => $product_sku ? $product_sku : $product_id,
		"category"   => $product_cat,
		"price"      => $product->get_price(),
		"currency"   => get_woocommerce_currency(),
		"stocklevel" => $product->get_stock_quantity()
	);
	$eec_product_array = apply_filters( GTM4WP_WPFILTER_EEC_PRODUCT_ARRAY, $_temp_productdata, "addtocartsingle" );

	foreach( $eec_product_array as $eec_product_array_key => $eec_product_array_value ) {
		echo '<input type="hidden" name="gtm4wp_' . esc_attr( $eec_product_array_key ). '" value="' . esc_attr( $eec_product_array_value ). '" />'."\n";
	}
}

// moved to gtm4wp-woocommerce-enhanced.js
/*
function gtm4wp_woocommerce_wp_footer() {
	global $gtm4wp_options, $gtm4wp_datalayer_name;

	if ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKCLASSICEC ] ) {
		gtm4wp_woocommerce_addjs("
		$( '.add_to_cart_button:not(.product_type_variable, .product_type_grouped, .single_add_to_cart_button)' ).click(function() {
			var productdata = $( this ).closest( '.product' ).find( '.gtm4wp_productdata' );

			". $gtm4wp_datalayer_name .".push({
				'event': 'gtm4wp.addProductToCart',
				'productName': productdata.data( 'gtm4wp_product_name' ),
				'productSKU': $( this ).data( 'product_sku' ),
				'productID': $( this ).data( 'product_id' ),
			});
		});
		");
	}

	if ( ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ] ) && ( ! is_cart() ) && ( ! is_product() ) ) {
		echo "
<script data-cfasync='false' type='text/javascript'>
	(function($) {
		if ( $( '.gtm4wp_productdata' ).length > 0 ) {
			for( var i=0; i<". $gtm4wp_datalayer_name .".length; i++ ) {
				if ( ". $gtm4wp_datalayer_name ."[ i ][ 'ecomm_prodid' ] ) {
					break;
				}
			}

			if ( i == ". $gtm4wp_datalayer_name .".length ) {
				// no existing dyn remarketing data found in the datalayer
				i = 0;
				". $gtm4wp_datalayer_name ."[ i ][ 'ecomm_prodid' ] = [];
			}

			if ( typeof ". $gtm4wp_datalayer_name ."[ i ][ 'ecomm_prodid' ].push == 'undefined' ) {
				return false;
			}

			var productdata;
			$( '.gtm4wp_productdata' ).each( function() {
				productdata = jQuery( this );

				". $gtm4wp_datalayer_name ."[ i ][ 'ecomm_prodid' ].push( '".gtm4wp_prefix_productid("")."' + productdata.data( 'gtm4wp_product_id' ) );
			});
		}
	})(jQuery);
</script>";
	}

	if ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) {
		echo "
<script data-cfasync='false' type='text/javascript'>
	(function($) {
		if ( $( '.gtm4wp_productdata,.widget-product-item' ).length > 0 ) {
			for( var i=0; i<". $gtm4wp_datalayer_name .".length; i++ ) {
				if ( ". $gtm4wp_datalayer_name ."[ i ][ 'ecommerce' ] ) {

					if ( ! ". $gtm4wp_datalayer_name ."[ i ][ 'ecommerce' ][ 'impressions' ] ) {
						". $gtm4wp_datalayer_name ."[ i ][ 'ecommerce' ][ 'impressions' ] = [];
					}

					break;
				}
			}

			if ( i == ". $gtm4wp_datalayer_name .".length ) {
				// no existing ecommerce data found in the datalayer
				i = 0;
				". $gtm4wp_datalayer_name ."[ i ][ 'ecommerce' ] = {};
				". $gtm4wp_datalayer_name ."[ i ][ 'ecommerce' ][ 'impressions' ] = [];
			}

			". $gtm4wp_datalayer_name ."[ i ][ 'ecommerce' ][ 'currencyCode' ] = '".get_woocommerce_currency()."';

			var productdata;
			$( '.gtm4wp_productdata,.widget-product-item' ).each( function() {
				productdata = jQuery( this );

				". $gtm4wp_datalayer_name ."[ i ][ 'ecommerce' ][ 'impressions' ].push({
					'name':     productdata.data( 'gtm4wp_product_name' ),
					'id':       productdata.data( 'gtm4wp_product_id' ),
					'price':    productdata.data( 'gtm4wp_product_price' ),
					'category': productdata.data( 'gtm4wp_product_cat' ),
					'position': productdata.data( 'gtm4wp_product_listposition' ),
					'list':     productdata.data( 'gtm4wp_productlist_name' )
				});
			});
		}
	})(jQuery);
</script>";

		gtm4wp_woocommerce_addjs("
		$( '.add_to_cart_button:not(.product_type_variable, .product_type_grouped, .single_add_to_cart_button)' ).click(function() {
			var productdata = $( this ).closest( '.product' ).find( '.gtm4wp_productdata' );

			". $gtm4wp_datalayer_name .".push({
				'event': 'gtm4wp.addProductToCartEEC',
				'ecommerce': {
					'currencyCode': '".get_woocommerce_currency()."',
					'add': {
						'products': [{
							'name':     productdata.data( 'gtm4wp_product_name' ),
							'id':       productdata.data( 'gtm4wp_product_id' ),
							'price':    productdata.data( 'gtm4wp_product_price' ),
							'category': productdata.data( 'gtm4wp_product_cat' ),
							'quantity': 1
						}]
					}
				}
			});
		});
		");
	}
}
*/

// moved to gtm4wp-woocommerce-enhanced.js
/*
function gtm4wp_woocommerce_enhanced_ecom_product_click() {
	global $gtm4wp_datalayer_name;

	gtm4wp_woocommerce_addjs("
		$( '.products li:not(.product-category) a:not(.add_to_cart_button),.widget-product-item' ).click(function( event ) {
			if ( 'undefined' == typeof google_tag_manager ) {
				return true;
			}

			var _productdata = $( this ).closest( '.product' );

			if ( _productdata.length > 0 ) {
				var productdata = _productdata.find( '.gtm4wp_productdata' );

			} else {
				var _productdata = $( this ).closest( 'ul.products li' );

				if ( _productdata.length > 0 ) {
					var productdata = _productdata.find( '.gtm4wp_productdata' );

				} else {
					var productdata = jQuery( this );

				}
			}

			if ( ( 'undefined' == typeof productdata.data( 'gtm4wp_product_id' ) ) || ( '' == productdata.data( 'gtm4wp_product_id' ) ) ) {
				return true;
			}

			// only act on links pointing to the product detail page
			if ( productdata.data( 'gtm4wp_product_url' ) != $( this ).attr( 'href' ) ) {
				return true;
			}

			var ctrl_key_pressed = event.ctrlKey;

			event.preventDefault();
			if ( ctrl_key_pressed ) {
				// we need to open the new tab/page here so that popup blocker of the browser doesn't block our code
				var _productpage = window.open( 'about:blank', '_blank' );
			}

			". $gtm4wp_datalayer_name .".push({
				'event': 'gtm4wp.productClickEEC',
				'ecommerce': {
					'currencyCode': '".get_woocommerce_currency()."',
					'click': {
					  'actionField': {'list': productdata.data( 'gtm4wp_productlist_name' )},
						'products': [{
							'id':       productdata.data( 'gtm4wp_product_id' ),
							'name':     productdata.data( 'gtm4wp_product_name' ),
							'price':    productdata.data( 'gtm4wp_product_price' ),
							'category': productdata.data( 'gtm4wp_product_cat' ),
							'position': productdata.data( 'gtm4wp_product_listposition' )
						}]
					}
				},
				'eventCallback': function() {
					if ( ctrl_key_pressed && _productpage ) {
						_productpage.location.href= productdata.data( 'gtm4wp_product_url' );
					} else {
						document.location.href = productdata.data( 'gtm4wp_product_url' )
					}
				},
				'eventTimeout': 2000
			});
		});
	");
}
*/

$GLOBALS["gtm4wp_cart_item_proddata"] = '';
function gtm4wp_woocommerce_cart_item_product_filter( $product, $cart_item="", $cart_id="" ) {
	global $gtm4wp_options, $gtm4wp_is_woocommerce3;

	$product_id   = $product->get_id();
	$product_type = $product->get_type();

	if ( "variation" == $product_type ) {
		$parent_product_id = ( $gtm4wp_is_woocommerce3 ? $product->get_parent_id() : $product->id);
		$product_cat = gtm4wp_get_product_category( $parent_product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );
	} else {
		$product_cat = gtm4wp_get_product_category( $product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );
	}

	$remarketing_id = $product_id;
	$product_sku    = $product->get_sku();
	if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSESKU ] && ( "" != $product_sku ) ) {
		$remarketing_id = $product_sku;
	}

	$_temp_productdata = array(
		"id"          => $remarketing_id,
		"name"        => $product->get_title(),
		"price"       => $product->get_price(),
		"category"    => $product_cat,
		"productlink" => apply_filters( 'the_permalink', get_permalink(), 0),
		"variant"     => "",
		"stocklevel"  => $product->get_stock_quantity()
	);

	if ( "variation" == $product_type ) {
		$_temp_productdata[ "variant" ] = implode(",", $product->get_variation_attributes());
	}

	$eec_product_array = apply_filters( GTM4WP_WPFILTER_EEC_PRODUCT_ARRAY, $_temp_productdata, "cart" );
	$GLOBALS["gtm4wp_cart_item_proddata"] = $eec_product_array;

	return $product;
}

function gtm4wp_woocommerce_cart_item_remove_link_filter( $remove_from_cart_link ) {
	if ( ! isset( $GLOBALS["gtm4wp_cart_item_proddata"] ) ) {
		return $remove_from_cart_link;
	}

	if ( ! is_array( $GLOBALS["gtm4wp_cart_item_proddata"] ) ) {
		return $remove_from_cart_link;
	}

	$cartlink_with_data = sprintf('data-gtm4wp_product_id="%s" data-gtm4wp_product_name="%s" data-gtm4wp_product_price="%s" data-gtm4wp_product_cat="%s" data-gtm4wp_product_url="%s" data-gtm4wp_product_variant="%s" data-gtm4wp_product_stocklevel="%s" href="',
		esc_attr( $GLOBALS["gtm4wp_cart_item_proddata"]["id"] ),
		esc_attr( $GLOBALS["gtm4wp_cart_item_proddata"]["name"] ),
		esc_attr( $GLOBALS["gtm4wp_cart_item_proddata"]["price"] ),
		esc_attr( $GLOBALS["gtm4wp_cart_item_proddata"]["category"] ),
		esc_url(  $GLOBALS["gtm4wp_cart_item_proddata"]["productlink"] ),
		esc_attr( $GLOBALS["gtm4wp_cart_item_proddata"]["variant"] ),
		esc_attr( $GLOBALS["gtm4wp_cart_item_proddata"]["stocklevel"] )
	);
	$GLOBALS["gtm4wp_cart_item_proddata"] = '';

	return gtm4wp_str_replace_first( 'href="', $cartlink_with_data, $remove_from_cart_link );
}

function gtp4wp_woocommerce_reset_loop() {
	global $woocommerce_loop;

	$woocommerce_loop[ "listtype" ] = "";
}

function gtm4wp_woocommerce_add_related_to_loop( $arg ) {
	global $woocommerce_loop;

	$woocommerce_loop[ "listtype" ] = __( "Related Products", "duracelltomi-google-tag-manager" );

	return $arg;
}

function gtm4wp_woocommerce_add_cross_sell_to_loop( $arg ) {
	global $woocommerce_loop;

	$woocommerce_loop[ "listtype" ] = __( "Cross-Sell Products", "duracelltomi-google-tag-manager" );

	return $arg;
}

function gtm4wp_woocommerce_add_upsells_to_loop( $arg ) {
	global $woocommerce_loop;

	$woocommerce_loop[ "listtype" ] = __( "Upsell Products", "duracelltomi-google-tag-manager" );

	return $arg;
}

function gtm4wp_woocommerce_before_template_part( $template_name ) {
	ob_start();
}

function gtm4wp_woocommerce_after_template_part( $template_name ) {
	global $product, $gtm4wp_product_counter, $gtm4wp_last_widget_title, $gtm4wp_options;

	$productitem = ob_get_contents();
	ob_end_clean();

	if ( "content-widget-product.php" == $template_name ) {
	  $product_id    = $product->get_id();
		$product_cat = gtm4wp_get_product_category( $product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );

		$remarketing_id = $product_id;
		$product_sku    = $product->get_sku();
		if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSESKU ] && ( "" != $product_sku ) ) {
			$remarketing_id = $product_sku;
		}

		$_temp_productdata = array(
			"id"           => $remarketing_id,
			"name"         => $product->get_title(),
			"price"        => $product->get_price(),
			"category"     => $product_cat,
			"productlink"  => apply_filters( 'the_permalink', get_permalink(), 0),
			"listname"     => $gtm4wp_last_widget_title,
			"listposition" => $gtm4wp_product_counter,
			"stocklevel"   => $product->get_stock_quantity()
		);
		$eec_product_array = apply_filters( GTM4WP_WPFILTER_EEC_PRODUCT_ARRAY, $_temp_productdata, "widgetproduct" );

		$productlink_with_data = sprintf('data-gtm4wp_product_id="%s" data-gtm4wp_product_name="%s" data-gtm4wp_product_price="%s" data-gtm4wp_product_cat="%s" data-gtm4wp_product_url="%s" data-gtm4wp_productlist_name="%s" data-gtm4wp_product_listposition="%s" data-gtm4wp_product_stocklevel="%s" href="',
			esc_attr( $eec_product_array[ "id" ] ),
			esc_attr( $eec_product_array[ "name" ] ),
			esc_attr( $eec_product_array[ "price" ] ),
			esc_attr( $eec_product_array[ "category" ] ),
			esc_url(  $eec_product_array[ "productlink" ] ),
			esc_attr( $eec_product_array[ "listname" ] ),
			esc_attr( $eec_product_array[ "listposition" ] ),
			esc_attr( $eec_product_array[ "stocklevel" ] )
		);

		$gtm4wp_product_counter++;

		$productitem = str_replace( 'href="', $productlink_with_data, $productitem );
	}

	echo $productitem;
}

function gtm4wp_widget_title_filter( $widget_title ) {
	global $gtm4wp_product_counter, $gtm4wp_last_widget_title;

	$gtm4wp_product_counter = 1;
	$gtm4wp_last_widget_title = $widget_title . __( " (widget)", "duracelltomi-google-tag-manager" );

	return $widget_title;
}

function gtm4wp_before_recent_products_loop() {
	global $woocommerce_loop;

	$woocommerce_loop[ "listtype" ] = __( "Recent Products", "duracelltomi-google-tag-manager" );
}

function gtm4wp_before_sale_products_loop() {
	global $woocommerce_loop;

	$woocommerce_loop[ "listtype" ] = __( "Sale Products", "duracelltomi-google-tag-manager" );
}

function gtm4wp_before_best_selling_products_loop() {
	global $woocommerce_loop;

	$woocommerce_loop[ "listtype" ] = __( "Best Selling Products", "duracelltomi-google-tag-manager" );
}

function gtm4wp_before_top_rated_products_loop() {
	global $woocommerce_loop;

	$woocommerce_loop[ "listtype" ] = __( "Top Rated Products", "duracelltomi-google-tag-manager" );
}

function gtm4wp_before_featured_products_loop() {
	global $woocommerce_loop;

	$woocommerce_loop[ "listtype" ] = __( "Featured Products", "duracelltomi-google-tag-manager" );
}

function gtm4wp_before_related_products_loop() {
	global $woocommerce_loop;

	$woocommerce_loop[ "listtype" ] = __( "Related Products", "duracelltomi-google-tag-manager" );
}

function gtm4wp_woocommerce_before_shop_loop_item() {
	global $product, $woocommerce_loop, $wp_query, $gtm4wp_options;

	if ( !isset( $product ) ) {
		return;
	}

	$product_id  = $product->get_id();

	$product_cat = "";
	if ( is_product_category() ) {
		global $wp_query;
		$cat_obj = $wp_query->get_queried_object();
		if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] ) {
			$product_cat = gtm4wp_get_product_category_hierarchy( $cat_obj->term_id );
		} else {
			$product_cat = $cat_obj->name;
		}
	} else {
		$product_cat = gtm4wp_get_product_category( $product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );
	}

	if ( is_search() ) {
		$list_name = __( "Search Results", "duracelltomi-google-tag-manager" );
	} else if ( isset( $woocommerce_loop[ "listtype" ] ) && ( $woocommerce_loop[ "listtype" ] != '' ) ) {
		$list_name = $woocommerce_loop[ "listtype" ];
	} else {
		$list_name = __( "General Product List", "duracelltomi-google-tag-manager" );
	}

	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$posts_per_page = get_query_var('posts_per_page');
	if ( $posts_per_page < 1 ) {
		$posts_per_page = 1;
	}

	$remarketing_id = $product_id;
	$product_sku    = $product->get_sku();
	if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSESKU ] && ( "" != $product_sku ) ) {
		$remarketing_id = $product_sku;
	}

	$_temp_productdata = array(
		"id"           => $remarketing_id,
		"name"         => $product->get_title(),
		"price"        => $product->get_price(),
		"category"     => $product_cat,
		"productlink"  => apply_filters( 'the_permalink', get_permalink(), 0),
		"listname"     => $list_name,
		"listposition" => $woocommerce_loop[ "loop" ] + ( $posts_per_page * ($paged-1) ),
		"stocklevel"   => $product->get_stock_quantity()
	);
	$eec_product_array = apply_filters( GTM4WP_WPFILTER_EEC_PRODUCT_ARRAY, $_temp_productdata, "productlist" );

	printf('<span class="gtm4wp_productdata" style="display:none; visibility:hidden;" data-gtm4wp_product_id="%s" data-gtm4wp_product_name="%s" data-gtm4wp_product_price="%s" data-gtm4wp_product_cat="%s" data-gtm4wp_product_url="%s" data-gtm4wp_product_listposition="%s" data-gtm4wp_productlist_name="%s" data-gtm4wp_product_stocklevel="%s"></span>',
		esc_attr( $eec_product_array[ "id" ] ),
		esc_attr( $eec_product_array[ "name" ] ),
		esc_attr( $eec_product_array[ "price" ] ),
		esc_attr( $eec_product_array[ "category" ] ),
		esc_url(  $eec_product_array[ "productlink" ] ),
		esc_attr( $eec_product_array[ "listposition" ] ),
		esc_attr( $eec_product_array[ "listname" ] ),
		esc_attr( $eec_product_array[ "stocklevel" ] )
	);
}

function gtm4wp_woocommerce_cart_item_restored( $cart_item_key ) {
	setcookie( "gtm4wp_product_readded_to_cart", $cart_item_key );
}

function gtm4wp_woocommerce_enqueue_scripts() {
  global $gtm4wp_options, $gtp4wp_plugin_url;

	if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKCLASSICEC ] ) {
    $in_footer = apply_filters( 'gtm4wp_' . GTM4WP_OPTION_INTEGRATE_WCTRACKCLASSICEC, false);
		wp_enqueue_script( "gtm4wp-woocommerce-classic", $gtp4wp_plugin_url . "js/gtm4wp-woocommerce-classic.js", array( "jquery" ), GTM4WP_VERSION, $in_footer );
	}

	if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) {
    $in_footer = apply_filters( 'gtm4wp_' . GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC, false);
		wp_enqueue_script( "gtm4wp-woocommerce-enhanced", $gtp4wp_plugin_url . "js/gtm4wp-woocommerce-enhanced.js", array( "jquery" ), GTM4WP_VERSION, $in_footer );
	}
}

function gtm4wp_wc_quick_view_before_single_product() {
	global $gtm4wp_options, $gtm4wp_datalayer_name;

	$dataLayer = array(
		"event" => "gtm4wp.changeDetailViewEEC"
	);

	if ( ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ] ) || ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) ) {
		$postid     = get_the_ID();
		$product    = wc_get_product( $postid );
		$product_id = $product->get_id();

		$product_cat = gtm4wp_get_product_category( $product_id, $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSEFULLCATEGORYPATH ] );

		if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCUSESKU ] ) {
			$product_sku = $product->get_sku();
			if ( "" != $product_sku ) {
				$product_id = $product_sku;
			}
		}
		$remarketing_id = (string)$product_id;
		$product_price = $product->get_price();

		if ( "variable" != $product->get_type() ) {
			$_temp_productdata = array(
				"name"       => gtm4wp_woocommerce_html_entity_decode( get_the_title() ),
				"id"         => $product_id,
				"price"      => $product_price,
				"category"   => $product_cat,
				"stocklevel" => $product->get_stock_quantity()
			);
			$eec_product_array = apply_filters( GTM4WP_WPFILTER_EEC_PRODUCT_ARRAY, $_temp_productdata, "productdetail" );

			if ( $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCREMARKETING ] ) {
				$dataLayer["ecomm_prodid"] = gtm4wp_prefix_productid( $remarketing_id );
				$dataLayer["ecomm_pagetype"] = "product";
				$dataLayer["ecomm_totalvalue"] = (float)$eec_product_array[ "price" ];
			}

			if ( true === $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) {
				$dataLayer["ecommerce"] = array(
					"detail" => array(
						"products" => array($eec_product_array)
					)
				);
			}
		} else {
			$dataLayer["ecomm_prodid"] = gtm4wp_prefix_productid( $remarketing_id );
			$dataLayer["ecomm_pagetype"] = "product";
			$dataLayer["ecomm_totalvalue"] = $product_price;
		}
	}

	echo '
	<script>
		'. $gtm4wp_datalayer_name .'.push(' . json_encode( $dataLayer ). ');
	</script>';
}

// do not add filter if someone enabled WooCommerce integration without an activated WooCommerce plugin
if ( function_exists ( "WC" ) ) {
	add_filter( GTM4WP_WPFILTER_COMPILE_DATALAYER, "gtm4wp_woocommerce_datalayer_filter_items" );

	add_filter( "loop_end", "gtp4wp_woocommerce_reset_loop" );
	add_action( "woocommerce_before_shop_loop_item", "gtm4wp_woocommerce_before_shop_loop_item" );
	add_action( "woocommerce_after_add_to_cart_button", "gtm4wp_woocommerce_single_add_to_cart_tracking" );

//	add_action( "wp_footer", "gtm4wp_woocommerce_wp_footer" );
	add_action( "wp_enqueue_scripts", "gtm4wp_woocommerce_enqueue_scripts" );
	add_filter( GTM4WP_WPACTION_ADDGLOBALVARS, "gtm4wp_woocommerce_addglobalvars" );

	if ( true === $GLOBALS[ "gtm4wp_options" ][ GTM4WP_OPTION_INTEGRATE_WCTRACKENHANCEDEC ] ) {
//		add_action( "wp_footer", "gtm4wp_woocommerce_enhanced_ecom_product_click" );
		add_action( "woocommerce_before_template_part", "gtm4wp_woocommerce_before_template_part" );
		add_action( "woocommerce_after_template_part", "gtm4wp_woocommerce_after_template_part" );
		add_filter( "widget_title", "gtm4wp_widget_title_filter" );
		add_action( "wc_quick_view_before_single_product", "gtm4wp_wc_quick_view_before_single_product" );

		add_filter( "woocommerce_cart_item_product",     "gtm4wp_woocommerce_cart_item_product_filter" );
		add_filter( "woocommerce_cart_item_remove_link", "gtm4wp_woocommerce_cart_item_remove_link_filter" );
		add_action( "woocommerce_cart_item_restored",    "gtm4wp_woocommerce_cart_item_restored" );

		add_filter( "woocommerce_related_products_args",    "gtm4wp_woocommerce_add_related_to_loop" );
		add_filter( "woocommerce_related_products_columns", "gtm4wp_woocommerce_add_related_to_loop" );
		add_filter( "woocommerce_cross_sells_columns",      "gtm4wp_woocommerce_add_cross_sell_to_loop" );
		add_filter( "woocommerce_upsells_columns",          "gtm4wp_woocommerce_add_upsells_to_loop" );

		add_action( "woocommerce_shortcode_before_recent_products_loop",       "gtm4wp_before_recent_products_loop" );
		add_action( "woocommerce_shortcode_before_sale_products_loop",         "gtm4wp_before_sale_products_loop" );
		add_action( "woocommerce_shortcode_before_best_selling_products_loop", "gtm4wp_before_best_selling_products_loop" );
		add_action( "woocommerce_shortcode_before_top_rated_products_loop",    "gtm4wp_before_top_rated_products_loop" );
		add_action( "woocommerce_shortcode_before_featured_products_loop",     "gtm4wp_before_featured_products_loop" );
		add_action( "woocommerce_shortcode_before_related_products_loop",      "gtm4wp_before_related_products_loop" );
	}
}
