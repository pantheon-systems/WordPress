var gtm4wp_last_selected_product_variation;
var gtm4wp_changedetail_fired_during_pageload=false;

function gtm4wp_handle_cart_qty_change() {
	jQuery( '.product-quantity input.qty' ).each(function() {
		var _original_value = jQuery( this ).prop( 'defaultValue' );

		var _current_value  = parseInt( jQuery( this ).val() );
		if ( Number.isNaN( _current_value ) ) {
			_current_value = _original_value;
		}

		if ( _original_value != _current_value ) {
			var productdata = jQuery( this ).closest( '.cart_item' ).find( '.remove' );

			if ( _original_value < _current_value ) {
				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.addProductToCartEEC',
					'ecommerce': {
						'currencyCode': gtm4wp_currency,
						'add': {
							'products': [{
								'name':       productdata.data( 'gtm4wp_product_name' ),
								'id':         productdata.data( 'gtm4wp_product_id' ),
								'price':      productdata.data( 'gtm4wp_product_price' ),
								'category':   productdata.data( 'gtm4wp_product_cat' ),
								'variant':    productdata.data( 'gtm4wp_product_variant' ),
								'stocklevel': productdata.data( 'gtm4wp_product_stocklevel' ),
								'quantity':   _current_value - _original_value
							}]
						}
					}
				});
			} else {
				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.removeFromCartEEC',
					'ecommerce': {
						'currencyCode': gtm4wp_currency,
						'remove': {
							'products': [{
								'name':       productdata.data( 'gtm4wp_product_name' ),
								'id':         productdata.data( 'gtm4wp_product_id' ),
								'price':      productdata.data( 'gtm4wp_product_price' ),
								'category':   productdata.data( 'gtm4wp_product_cat' ),
								'variant':    productdata.data( 'gtm4wp_product_variant' ),
								'stocklevel': productdata.data( 'gtm4wp_product_stocklevel' ),
								'quantity':   _original_value - _current_value
							}]
						}
					}
				});
			}
		} // end if qty changed
	}); // end each qty field
} // end gtm4wp_handle_cart_qty_change()

jQuery(function() {
	var is_cart     = jQuery( 'body' ).hasClass( 'woocommerce-cart' );
	var is_checkout = jQuery( 'body' ).hasClass( 'woocommerce-checkout' );

	// track impressions of products in product lists
	if ( jQuery( '.gtm4wp_productdata,.widget-product-item' ).length > 0 ) {
		var products = [];
		var productdata;
		jQuery( '.gtm4wp_productdata,.widget-product-item' ).each( function() {

			productdata = jQuery( this );
			products.push({
				'name':       productdata.data( 'gtm4wp_product_name' ),
				'id':         productdata.data( 'gtm4wp_product_id' ),
				'price':      productdata.data( 'gtm4wp_product_price' ),
				'category':   productdata.data( 'gtm4wp_product_cat' ),
				'position':   productdata.data( 'gtm4wp_product_listposition' ),
				'list':       productdata.data( 'gtm4wp_productlist_name' ),
				'stocklevel': productdata.data( 'gtm4wp_product_stocklevel' )
			});

		});

		if ( gtm4wp_product_per_impression > 0 ) {
			// Need to split the product submissions up into chunks in order to avoid the GA 8kb submission limit
			var chunk;
			while ( products.length ) {
				chunk = products.splice( 0, gtm4wp_product_per_impression );

				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.productImpressionEEC',
					'ecommerce': {
						'currencyCode': gtm4wp_currency,
						'impressions': chunk
					}
				});
			}
		} else {
			for( var i=0; i<window[ gtm4wp_datalayer_name ].length; i++ ) {
				if ( window[ gtm4wp_datalayer_name ][ i ][ 'ecommerce' ] ) {

					if ( ! window[ gtm4wp_datalayer_name ][ i ][ 'ecommerce' ][ 'impressions' ] ) {
						window[ gtm4wp_datalayer_name ][ i ][ 'ecommerce' ][ 'impressions' ] = [];
					}

					break;
				}
			}

			if ( i == window[ gtm4wp_datalayer_name ].length ) {
				// no existing ecommerce data found in the datalayer
				i = 0;
				window[ gtm4wp_datalayer_name ][ i ][ 'ecommerce' ] = {};
				window[ gtm4wp_datalayer_name ][ i ][ 'ecommerce' ][ 'impressions' ] = products;
			}

			window[ gtm4wp_datalayer_name ][ i ][ 'ecommerce' ][ 'currencyCode' ] = gtm4wp_currency;

		}
	}

	// track add to cart events for simple products in product lists
	jQuery( document ).on( 'click', '.add_to_cart_button:not(.product_type_variable, .product_type_grouped, .single_add_to_cart_button)', function() {
		var productdata = jQuery( this ).closest( '.product' ).find( '.gtm4wp_productdata' );

		window[ gtm4wp_datalayer_name ].push({
			'event': 'gtm4wp.addProductToCartEEC',
			'ecommerce': {
				'currencyCode': gtm4wp_currency,
				'add': {
					'products': [{
						'name':       productdata.data( 'gtm4wp_product_name' ),
						'id':         productdata.data( 'gtm4wp_product_id' ),
						'price':      productdata.data( 'gtm4wp_product_price' ),
						'category':   productdata.data( 'gtm4wp_product_cat' ),
						'stocklevel': productdata.data( 'gtm4wp_product_stocklevel' ),
						'quantity':   1
					}]
				}
			}
		});
	});

	// track add to cart events for products on product detail pages
	jQuery( document ).on( 'click', '.single_add_to_cart_button', function() {
		var _product_form       = jQuery( this ).closest( 'form.cart' );
		var _product_var_id     = jQuery( '[name=variation_id]', _product_form );
		var _product_id         = jQuery( '[name=gtm4wp_id]', _product_form ).val();
		var _product_name       = jQuery( '[name=gtm4wp_name]', _product_form ).val();
		var _product_sku        = jQuery( '[name=gtm4wp_sku]', _product_form ).val();
		var _product_category   = jQuery( '[name=gtm4wp_category]', _product_form ).val();
		var _product_price      = jQuery( '[name=gtm4wp_price]', _product_form ).val();
		var _product_currency   = jQuery( '[name=gtm4wp_currency]', _product_form ).val();
		var _product_stocklevel = jQuery( '[name=gtm4wp_stocklevel]', _product_form ).val();

		if ( _product_var_id.length > 0 ) {
			if ( gtm4wp_last_selected_product_variation ) {
				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.addProductToCartEEC',
					'ecommerce': {
						'currencyCode': _product_currency,
						'add': {
							'products': [gtm4wp_last_selected_product_variation]
						}
					}
				});
			}
/*
			_product_var_id_val = _product_var_id.val();
			_product_form_variations = _product_form.data( 'product_variations' );

			_product_form_variations.forEach( function( product_var ) {
				if ( product_var.variation_id == _product_var_id_val ) {
					_product_var_sku = product_var.sku;
					if ( ! _product_var_sku ) {
						_product_var_sku = _product_var_id_val;
					}

					var _tmp = [];
					for( var attrib_key in product_var.attributes ) {
						_tmp.push( product_var.attributes[ attrib_key ] );
					}

					window[ gtm4wp_datalayer_name ].push({
						'event': 'gtm4wp.addProductToCartEEC',
						'ecommerce': {
							'currencyCode': _product_currency,
							'add': {
								'products': [{
									'id': gtm4wp_use_sku_instead ? _product_var_sku : _product_var_id_val,
									'name': _product_name,
									'price': product_var.display_price,
									'category': _product_category,
									'variant': _tmp.join(','),
									'quantity': jQuery( 'form.cart:first input[name=quantity]' ).val(),
									'stocklevel': _product_stocklevel
								}]
							}
						}
					});

				}
			});
*/
		} else {
			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.addProductToCartEEC',
				'ecommerce': {
					'currencyCode': _product_currency,
					'add': {
						'products': [{
							'id': gtm4wp_use_sku_instead ? _product_sku : _product_id,
							'name': _product_name,
							'price': _product_price,
							'category': _product_category,
							'quantity': jQuery( 'form.cart:first input[name=quantity]' ).val(),
							'stocklevel': _product_stocklevel
						}]
					}
				}
			});
		}
	});

	// track remove links in mini cart widget and on cart page
	jQuery( document ).on( 'click', '.mini_cart_item a.remove,.product-remove a.remove', function() {
		var productdata = jQuery( this );

		var qty = 0;
		var qty_element = jQuery( this ).closest( '.cart_item' ).find( '.product-quantity input.qty' );
		if ( qty_element.length === 0 ) {
			qty_element = jQuery( this ).closest( '.mini_cart_item' ).find( '.quantity' );
			if ( qty_element.length > 0 ) {
				qty = parseInt( qty_element.text() );

				if ( Number.isNaN( qty ) ) {
					qty = 0;
				}
			}
		} else {
			qty = qty_element.val();
		}

		if ( qty === 0 ) {
			return true;
		}

		window[ gtm4wp_datalayer_name ].push({
			'event': 'gtm4wp.removeFromCartEEC',
			'ecommerce': {
				'remove': {
					'products': [{
						'name':       productdata.data( 'gtm4wp_product_name' ),
						'id':         productdata.data( 'gtm4wp_product_id' ),
						'price':      productdata.data( 'gtm4wp_product_price' ),
						'category':   productdata.data( 'gtm4wp_product_cat' ),
						'variant':    productdata.data( 'gtm4wp_product_variant' ),
						'stocklevel': productdata.data( 'gtm4wp_product_stocklevel' ),
						'quantity':   qty
					}]
				}
			}
		});
	});

	// track clicks in product lists
	jQuery( document ).on( 'click', '.products li:not(.product-category) a:not(.add_to_cart_button):not(.quick-view-button),.products>div:not(.product-category) a:not(.add_to_cart_button):not(.quick-view-button),.widget-product-item', function( event ) {
		// do nothing if GTM is blocked for some reason
		if ( 'undefined' == typeof google_tag_manager ) {
			return true;
		}

		var _productdata = jQuery( this ).closest( '.product' );
		var productdata = '';

		if ( _productdata.length > 0 ) {
			productdata = _productdata.find( '.gtm4wp_productdata' );

		} else {
			_productdata = jQuery( this ).closest( '.products li' );

			if ( _productdata.length > 0 ) {
				productdata = _productdata.find( '.gtm4wp_productdata' );

			} else {
				_productdata = jQuery( this ).closest( '.products>div' );

				if ( _productdata.length > 0 ) {
					productdata = _productdata.find( '.gtm4wp_productdata' );
				} else {
					productdata = jQuery( this );
				}
			}
		}

		if ( ( 'undefined' == typeof productdata.data( 'gtm4wp_product_id' ) ) || ( '' == productdata.data( 'gtm4wp_product_id' ) ) ) {
			return true;
		}

		// only act on links pointing to the product detail page
		if ( productdata.data( 'gtm4wp_product_url' ) != jQuery( this ).attr( 'href' ) ) {
			return true;
		}

		var ctrl_key_pressed = event.ctrlKey || event.metaKey;

		event.preventDefault();
		if ( ctrl_key_pressed ) {
			// we need to open the new tab/page here so that popup blocker of the browser doesn't block our code
			var _productpage = window.open( 'about:blank', '_blank' );
		}

		window[ gtm4wp_datalayer_name ].push({
			'event': 'gtm4wp.productClickEEC',
			'ecommerce': {
				'currencyCode': gtm4wp_currency,
				'click': {
					'actionField': {'list': productdata.data( 'gtm4wp_productlist_name' )},
					'products': [{
						'id':         productdata.data( 'gtm4wp_product_id' ),
						'name':       productdata.data( 'gtm4wp_product_name' ),
						'price':      productdata.data( 'gtm4wp_product_price' ),
						'category':   productdata.data( 'gtm4wp_product_cat' ),
						'stocklevel': productdata.data( 'gtm4wp_product_stocklevel' ),
						'position':   productdata.data( 'gtm4wp_product_listposition' )
					}]
				}
			},
			'eventCallback': function() {
				if ( ctrl_key_pressed && _productpage ) {
					_productpage.location.href= productdata.data( 'gtm4wp_product_url' );
				} else {
					document.location.href = productdata.data( 'gtm4wp_product_url' );
				}
			},
			'eventTimeout': 2000
		});
	});

	// track variable products on their detail pages
	jQuery( document ).on( 'found_variation', function( event, product_variation ) {
		if ( "undefined" == typeof product_variation ) {
			// some ither plugins trigger this event without variation data
			return;
		}

		if ( (document.readyState === "interactive") && gtm4wp_changedetail_fired_during_pageload ) {
			// some custom attribute rendering plugins fire this event multiple times during page load
			return;
		}

		var _product_form       = event.target;
		var _product_var_id     = jQuery( '[name=variation_id]', _product_form );
		var _product_id         = jQuery( '[name=gtm4wp_id]', _product_form ).val();
		var _product_name       = jQuery( '[name=gtm4wp_name]', _product_form ).val();
		var _product_sku        = jQuery( '[name=gtm4wp_sku]', _product_form ).val();
		var _product_category   = jQuery( '[name=gtm4wp_category]', _product_form ).val();
		var _product_price      = jQuery( '[name=gtm4wp_price]', _product_form ).val();
		var _product_currency   = jQuery( '[name=gtm4wp_currency]', _product_form ).val();
		var _product_stocklevel = jQuery( '[name=gtm4wp_stocklevel]', _product_form ).val();

		var current_product_detail_data   = {
			name: _product_name,
			id: 0,
			price: 0,
			category: _product_category,
			stocklevel: _product_stocklevel,
			variant: ''
		};

		current_product_detail_data.id = product_variation.variation_id;
		if ( gtm4wp_use_sku_instead && product_variation.sku && ('' !== product_variation.sku) ) {
			current_product_detail_data.id = product_variation.sku;
		}
		current_product_detail_data.price = product_variation.display_price;

		var _tmp = [];
		for( var attrib_key in product_variation.attributes ) {
			_tmp.push( product_variation.attributes[ attrib_key ] );
		}
		current_product_detail_data.variant = _tmp.join(',');
		gtm4wp_last_selected_product_variation = current_product_detail_data;

		window[ gtm4wp_datalayer_name ].push({
			'event': 'gtm4wp.changeDetailViewEEC',
			'ecommerce': {
				'currencyCode': _product_currency,
				'detail': {
					'products': [current_product_detail_data]
				},
			},
			'ecomm_prodid': gtm4wp_id_prefix + current_product_detail_data.id,
			'ecomm_pagetype': 'product',
			'ecomm_totalvalue': current_product_detail_data.price,
		});

		if ( document.readyState === "interactive" ) {
			gtm4wp_changedetail_fired_during_pageload = true;
		}
	});
	jQuery( '.variations select' ).trigger( 'change' );

	// initiate codes in WooCommere Quick View
	jQuery( document ).ajaxSuccess( function( event, xhr, settings ) {
		if ( settings.url.indexOf( 'wc-api=WC_Quick_View' ) > -1 ) {
		  setTimeout( function() {
				jQuery( ".woocommerce.quick-view" ).parent().find( "script" ).each( function(i) {
					eval( jQuery( this ).text() );
				});
			}, 500);
		}
	});

	// codes for enhanced ecommerce events on cart page
	if ( is_cart ) {
		jQuery( document ).on( 'click', '[name=update_cart]', function() {
			gtm4wp_handle_cart_qty_change();
		});

		jQuery( document ).on( 'keypress', '.woocommerce-cart-form input[type=number]', function() {
			gtm4wp_handle_cart_qty_change();
		});
	}

	// codes for enhanced ecommerce events on checkout page
	if ( is_checkout ) {
		window.gtm4wp_checkout_step_offset = window.gtm4wp_checkout_step_offset || 0;
		window.gtm4wp_checkout_products    = window.gtm4wp_checkout_products || [];
		var gtm4wp_checkout_step_fired          = []; // step 1 will be the billing section which is reported during pageload, no need to handle here

		jQuery( document ).on( 'blur', 'input[name^=shipping_]:not(input[name=shipping_method])', function() {
			// do not report checkout step if already reported
			if ( gtm4wp_checkout_step_fired.indexOf( 'shipping' ) > -1 ) {
				return;
			}

			// do not report checkout step if user is traversing through the section without filling in any data
			if ( jQuery( this ).val().trim() == '' ) {
				return;
			}

			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.checkoutStepEEC',
				'ecommerce': {
					'checkout': {
						'actionField': {
							'step': 2 + window.gtm4wp_checkout_step_offset
						},
						'products': window.gtm4wp_checkout_products
					}
				}
			});

			gtm4wp_checkout_step_fired.push( 'shipping' );
		});

		jQuery( document ).on( 'change', 'input[name=shipping_method]', function() {
			// do not report checkout step if already reported
			if ( gtm4wp_checkout_step_fired.indexOf( 'shipping_method' ) > -1 ) {
				return;
			}

			// do not fire event during page load
			if ( 'complete' != document.readyState ) {
				return;
			}

			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.checkoutStepEEC',
				'ecommerce': {
					'checkout': {
						'actionField': {
							'step': 3 + window.gtm4wp_checkout_step_offset
						},
						'products': window.gtm4wp_checkout_products
					}
				}
			});

			gtm4wp_checkout_step_fired.push( 'shipping_method' );
		});

		jQuery( document ).on( 'change', 'input[name=payment_method]', function() {
			// do not report checkout step if already reported
			if ( gtm4wp_checkout_step_fired.indexOf( 'payment_method' ) > -1 ) {
				return;
			}

			// do not fire event during page load
			if ( 'complete' != document.readyState ) {
				return;
			}

			window[ gtm4wp_datalayer_name ].push({
				'event': 'gtm4wp.checkoutStepEEC',
				'ecommerce': {
					'checkout': {
						'actionField': {
							'step': 4 + window.gtm4wp_checkout_step_offset
						},
						'products': window.gtm4wp_checkout_products
					}
				}
			});

			gtm4wp_checkout_step_fired.push( 'payment_method' );
		});

		jQuery( 'form[name=checkout]' ).on( 'submit', function() {
			if ( gtm4wp_checkout_step_fired.indexOf( 'shipping_method' ) == -1 ) {
				// shipping methods are not visible if only one is available
				// and if the user has already a pre-selected method, no click event will fire to report the checkout step
				jQuery( 'input[name=shipping_method]:checked' ).trigger( 'click' );

				gtm4wp_checkout_step_fired.push( 'shipping_method' );
			}

			if ( gtm4wp_checkout_step_fired.indexOf( 'payment_method' ) == -1 ) {
				// if the user has already a pre-selected method, no click event will fire to report the checkout step
				jQuery( 'input[name=payment_method]:checked' ).trigger( 'click' );

				gtm4wp_checkout_step_fired.push( 'payment_method' );
			}

			var _shipping_el = jQuery( '#shipping_method input:checked' );
			if ( _shipping_el.length > 0 ) {
				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.checkoutOptionEEC',
					'ecommerce': {
						'checkout_option': {
							'actionField': {
								'step': 3 + window.gtm4wp_checkout_step_offset,
								'option': 'Shipping: ' + _shipping_el.val()
							}
						}
					}
				});
			}

			var _payment_el = jQuery( '.payment_methods input:checked' );
			if ( _payment_el.length > 0 ) {
				window[ gtm4wp_datalayer_name ].push({
					'event': 'gtm4wp.checkoutOptionEEC',
					'ecommerce': {
						'checkout_option': {
							'actionField': {
								'step': 4 + window.gtm4wp_checkout_step_offset,
								'option': 'Payment: ' + _payment_el.val()
							}
						}
					}
				});
			}
		});
	}

	// codes for AdWords dynamic remarketing
	if ( window.gtm4wp_remarketing&& !is_cart && !is_checkout ) {
		if ( jQuery( '.gtm4wp_productdata' ).length > 0 ) {
			for( var i=0; i<window[ gtm4wp_datalayer_name ].length; i++ ) {
				if ( window[ gtm4wp_datalayer_name ][ i ][ 'ecomm_prodid' ] ) {
					break;
				}
			}

			if ( i == window[ gtm4wp_datalayer_name ].length ) {
				// no existing dyn remarketing data found in the datalayer
				i = 0;
				window[ gtm4wp_datalayer_name ][ i ][ 'ecomm_prodid' ] = [];
			}

			if ( 'undefined' == typeof window[ gtm4wp_datalayer_name ][ i ][ 'ecomm_prodid' ].push ) {
				return false;
			}

			var productdata;
			jQuery( '.gtm4wp_productdata' ).each( function() {
				productdata = jQuery( this );

				window[ gtm4wp_datalayer_name ][ i ][ 'ecomm_prodid' ].push( gtm4wp_id_prefix + productdata.data( 'gtm4wp_product_id' ) );
			});
		}
	}
});