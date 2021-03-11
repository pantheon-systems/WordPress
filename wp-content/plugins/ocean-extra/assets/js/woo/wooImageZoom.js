var $j = jQuery.noConflict();

$j( document ).ready( function() {
	"use strict";
	// Woo image zoom
	oceanwpWooImageZoom();
} );

/* ==============================================
WOOCOMMERCE IMAGE ZOOM
============================================== */
function oceanwpWooImageZoom() {
	"use strict"

	// wc_single_product_params is required to continue.
	if ( typeof wc_single_product_params === 'undefined' ) {
		return false;
	}

	/**
	 * Product gallery class.
	 */
	var ProductGallery = function( $target, args ) {
		this.$target = $target;
		this.$images = $j( '.woocommerce-product-gallery__image', $target );

		// No images? Abort.
		if ( 0 === this.$images.length ) {
			this.$target.css( 'opacity', 1 );
			return;
		}

		// Make this object available.
		$target.data( 'product_gallery', this );

		// Pick functionality to initialize...
		this.zoom_enabled = $j.isFunction( $j.fn.zoom ) && wc_single_product_params.zoom_enabled;

		// ...also taking args into account.
		if ( args ) {
			this.zoom_enabled = false === args.zoom_enabled ? false : this.zoom_enabled;
		}

		// Bind functions to this.
		this.initZoom = this.initZoom.bind( this );

		if ( this.zoom_enabled ) {
			this.initZoom();
			$target.on( 'woocommerce_gallery_init_zoom', this.initZoom );
		}
	};

	ProductGallery.prototype.initZoom = function() {
		var zoomTarget   = this.$images,
			galleryWidth = this.$target.width(),
			zoomEnabled  = false;

		$j( zoomTarget ).each( function( index, target ) {
			var image = $j( target ).find( 'img' );

			if ( image.data( 'large_image_width' ) > galleryWidth ) {
				zoomEnabled = true;
				return false;
			}
		} );

		// But only zoom if the img is larger than its container.
		if ( zoomEnabled ) {
			var zoom_options = {
				touch: false
			};

			if ( 'ontouchstart' in window ) {
				zoom_options.on = 'click';
			}

			zoomTarget.trigger( 'zoom.destroy' );
			zoomTarget.zoom( zoom_options );
		}
	};

	/**
	 * Function to call wc_product_gallery on jquery selector.
	 */
	$j.fn.wc_product_gallery = function( args ) {
		new ProductGallery( this, args );
		return this;
	};

	/*
	 * Initialize all galleries on page.
	 */
	$j( '.woocommerce-product-gallery' ).each( function() {
		$j( this ).wc_product_gallery();
	} );
	
}