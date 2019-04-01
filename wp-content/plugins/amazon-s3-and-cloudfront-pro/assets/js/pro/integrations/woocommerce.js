( function( $, _ ) {

	var fileFrame;
	var attachment;
	var fileNameInput;
	var fileUrlInput;
	var fileNamePlaceholder;
	var fileUrlPlaceholder;

	/**
	 * Render custom media uploader
	 *
	 * @param object row
	 *
	 * @return void
	 */
	function renderMediaUploader( row ) {

		// Find the correct file name and url inputs
		fileNameInput = row.find( 'td.file_name input.input_text' );
		fileUrlInput = row.find( 'td.file_url input.input_text' );

		// Use existing file frame instance or create one
		if ( undefined !== fileFrame ) {
			fileFrame.open();
			return;
		}

		fileFrame = wp.media.frames.file_frame = wp.media( {
			title: as3cf_woo.strings.media_modal_title,
			button: {
				text: as3cf_woo.strings.media_modal_button
			},
			states: [
				new wp.media.controller.Library( {
					title: as3cf_woo.strings.media_modal_title,
					filterable: 'all',
					multiple: false
				} )
			]
		} );

		// Handle file selection
		fileFrame.on( 'select', function() {
			attachment = fileFrame.state().get( 'selection' ).first().toJSON();

			fileNamePlaceholder = fileNameInput.attr( 'placeholder' );
			fileUrlPlaceholder = fileUrlInput.attr( 'placeholder' );
			fileNameInput.val( '' ).attr( 'placeholder', as3cf_woo.strings.input_placeholder );
			fileUrlInput.val( '' ).attr( 'placeholder', as3cf_woo.strings.input_placeholder );

			isAmazonS3Attachment();
		} );

		// Ensure files are uploaded to the woocommerce_uploads directory
		fileFrame.on( 'ready', function() {
			fileFrame.uploader.options.uploader.params = {
				type: 'downloadable_product'
			};
		} );

		fileFrame.open();
	}

	/**
	 * Get S3 attachment info via ajax call
	 *
	 * @return void
	 */
	function isAmazonS3Attachment() {
		wp.ajax.post( 'as3cf_woo_is_amazon_provider_attachment', {
			_nonce: as3cf_woo.nonces.is_amazon_provider_attachment,
			attachment_id: attachment.id
		} ).done( _.bind( updateFileInputs, this ) );
	}

	/**
	 * Update file inputs
	 *
	 * @param bool response
	 *
	 * @return void
	 */
	function updateFileInputs( response ) {
		var output = attachment.url;

		if ( response ) {
			var shortcode = _.template( '[amazon_s3 id="<%= id %>"]' );

			output = shortcode( {
				id: attachment.id
			} );
		}

		fileNameInput.val( attachment.title ).attr( 'placeholder', fileNamePlaceholder ).trigger( 'change' );
		fileUrlInput.val( output ).attr( 'placeholder', fileUrlPlaceholder ).trigger( 'change' );
	}

	// Replace WooCommerce upload file click handler
	$( document ).ready( function() {
		$( document ).off( 'click', '.upload_file_button' );
		$( document.body ).off( 'click', '.upload_file_button' ); // WooCommerce 2.4+

		$( document ).on( 'click', '.upload_file_button', function( e ) {
			var row = $( this ).closest( 'tr' );

			e.preventDefault();
			renderMediaUploader( row );
		} );
	} );

} )( jQuery, _ );
