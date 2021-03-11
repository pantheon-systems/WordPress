var $j 		= jQuery.noConflict(),
	$window = $j( window );

$window.on( 'load', function() {
	"use strict";
	// Variable image product
	oceanwpWooVariableImage();
} );

/* ==============================================
WOOCOMMERCE VARIABLE IMAGE PRODUCT
============================================== */
function oceanwpWooVariableImage() {
	"use strict"

	/**
	 * Stores a default attribute for an element so it can be reset later
	 */
	$j.fn.wc_set_variation_attr = function( attr, value ) {
		if ( undefined === this.attr( 'data-o_' + attr ) ) {
			this.attr( 'data-o_' + attr, ( ! this.attr( attr ) ) ? '' : this.attr( attr ) );
		}
		this.attr( attr, value );
	};

	/**
	 * Reset a default attribute for an element so it can be reset later
	 */
	$j.fn.wc_reset_variation_attr = function( attr ) {
		if ( undefined !== this.attr( 'data-o_' + attr ) ) {
			this.attr( attr, this.attr( 'data-o_' + attr ) );
		}
	};

	/**
	 * Sets product images for the chosen variation
	 */
	$j.fn.wc_variations_image_update = function( variation ) {
		var $form 				= this,
			$product 			= $form.closest( '.product' ),
			$product_gallery  	= $product.find( '.images' ),
			$product_img 		= $product.find( 'div.images .main-images img:eq(0), div.images .main-images img:eq(1), div.images .product-thumbnails .first-thumbnail:not(.slick-cloned) img' ),
			$product_link 		= $product.find( 'div.images a.woocommerce-main-image:eq(0), div.images a.woocommerce-main-image:eq(1), div.images a.oceanwp-lightbox-trigger:eq(0), div.images a.oceanwp-lightbox-trigger:eq(1), div.images .first-thumbnail:not(.slick-cloned) a.woo-thumbnail:eq(3)' );

		if ( variation && variation.image && variation.image.src && variation.image.src.length > 1 ) {
			// Image attrs
			$product_img.wc_set_variation_attr( 'src', variation.image.src );
			$product_img.wc_set_variation_attr( 'height', variation.image.src_h );
			$product_img.wc_set_variation_attr( 'width', variation.image.src_w );
			$product_img.wc_set_variation_attr( 'srcset', variation.image.srcset );
			$product_img.wc_set_variation_attr( 'sizes', variation.image.sizes );
			$product_img.wc_set_variation_attr( 'title', variation.image.title );
			$product_img.wc_set_variation_attr( 'alt', variation.image.alt );
			$product_img.wc_set_variation_attr( 'data-src', variation.image.full_src );
			$product_img.wc_set_variation_attr( 'data-large_image', variation.image.full_src );
			$product_img.wc_set_variation_attr( 'data-large_image_width', variation.image.full_src_w );
			$product_img.wc_set_variation_attr( 'data-large_image_height', variation.image.full_src_h );
			$product_link.wc_set_variation_attr( 'href', variation.image.full_src );
			$product_link.wc_set_variation_attr( 'title', variation.image.title );
		} else {
			// Reset image attrs
			$product_img.wc_reset_variation_attr( 'src' );
			$product_img.wc_reset_variation_attr( 'width' );
			$product_img.wc_reset_variation_attr( 'height' );
			$product_img.wc_reset_variation_attr( 'srcset' );
			$product_img.wc_reset_variation_attr( 'sizes' );
			$product_img.wc_reset_variation_attr( 'title' );
			$product_img.wc_reset_variation_attr( 'alt' );
			$product_img.wc_reset_variation_attr( 'data-src' );
			$product_img.wc_reset_variation_attr( 'data-large_image' );
			$product_img.wc_reset_variation_attr( 'data-large_image_width' );
			$product_img.wc_reset_variation_attr( 'data-large_image_height' );
			$product_link.wc_reset_variation_attr( 'href' );
			$product_link.wc_reset_variation_attr( 'title' );
		}

		window.setTimeout( function() {
			$product_gallery.trigger( 'woocommerce_gallery_init_zoom' );
			
			// Refresh slide
			if ( $j( 'body' ).hasClass( 'single-product' ) ) {
				$j( '.product .main-images, .product .product-thumbnails' ).slick( 'refresh' );

				// Refresh lightbox
				if ( ! $j( 'body' ).hasClass( 'no-lightbox' ) ) {
					$j( '.product-images-slider' ).removeData( 'chocolat' ).Chocolat( {
						loop           	: true,
						imageSelector   : '.product-image:not(.slick-cloned) .woo-lightbox'
		            } );
		        }
	        }
	        
			$j( window ).trigger( 'resize' );
		}, 10 );
	};
	
}