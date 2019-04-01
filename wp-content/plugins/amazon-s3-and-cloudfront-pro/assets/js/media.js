var test = {};

( function( $, _ ) {

	// Local reference to the WordPress media namespace.
	var media = wp.media;

	// Local instance of the Attachment Details TwoColumn used in the edit attachment modal view
	var wpAttachmentDetailsTwoColumn = media.view.Attachment.Details.TwoColumn;

	/**
	 * Add S3 details to attachment.
	 */
	media.view.Attachment.Details.TwoColumn = wpAttachmentDetailsTwoColumn.extend( {
		events: function() {
			return _.extend( {}, wpAttachmentDetailsTwoColumn.prototype.events, {
				'click .local-warning': 'confirmS3Removal',
				'click #as3cfpro-toggle-acl': 'toggleACL'
			} );
		},

		render: function() {
			// Retrieve the S3 details for the attachment
			// before we render the view
			this.fetchS3Details( this.model.get( 'id' ) );
		},

		fetchS3Details: function( id ) {
			wp.ajax.send( 'as3cf_get_attachment_provider_details', {
				data: {
					_nonce: as3cf_media.nonces.get_attachment_provider_details,
					id: id
				}
			} ).done( _.bind( this.renderView, this ) );
		},

		renderView: function( response ) {
			// Render parent media.view.Attachment.Details
			wpAttachmentDetailsTwoColumn.prototype.render.apply( this );

			this.renderActionLinks( response );
			this.renderS3Details( response );
		},

		renderActionLinks: function( response ) {
			var links = ( response && response.links ) || [];
			var $actionsHtml = this.$el.find( '.actions' );
			var $s3Actions = $( '<div />', {
				'class': 's3-actions'
			} );

			var s3Links = [];
			_( links ).each( function( link ) {
				s3Links.push( link );
			} );

			$s3Actions.append( s3Links.join( ' | ' ) );
			$actionsHtml.append( $s3Actions );
		},

		renderS3Details: function( response ) {
			if ( ! response || ! response.provider_object ) {
				return;
			}
			var $detailsHtml = this.$el.find( '.attachment-info .details' );
			var html = this.generateDetails( response, [ 'provider_name', 'region', 'bucket', 'key', 'acl' ] );
			$detailsHtml.append( html );
		},

		generateDetails: function( response, keys ) {
			var html = '';
			var template = _.template( '<div class="<%= key %>"><strong><%= label %>:</strong> <%= value %></div>' );

			_( keys ).each( function( key ) {
				if ( response.provider_object[ key ] ) {
					var value = response.provider_object[ key ];

					if ( 'acl' === key ) {
						value = response.provider_object[ key ][ 'name' ];

						if ( response.acl_toggle ) {
							var acl_template = _.template( '<a href="#" id="as3cfpro-toggle-acl" title="<%= title %>" data-currentACL="<%= acl %>"><%= value %></a>' );

							value = acl_template( {
								title: response.provider_object[ key ][ 'title' ],
								acl: response.provider_object[ key ][ 'acl' ],
								value: value
							} );
						}
					}

					html += template( {
						key: key,
						label: as3cf_media.strings[ key ],
						value: value
					} );
				}
			} );

			return html;
		},

		confirmS3Removal: function( event ) {
			if ( ! confirm( as3cfpro_media.strings.local_warning ) ) {
				event.preventDefault();
				event.stopImmediatePropagation();
				return false;
			}
		},

		toggleACL: function( event ) {
			event.preventDefault();

			var toggle = $( '#as3cfpro-toggle-acl' );
			var currentACL = toggle.data( 'currentacl' );
			var newACL = as3cfpro_media.settings.private_acl;

			toggle.hide();
			toggle.after( '<span id="as3cfpro-updating">' + as3cfpro_media.strings.updating_acl + '</span>' );

			if ( currentACL === as3cfpro_media.settings.private_acl ) {
				newACL = as3cfpro_media.settings.default_acl;
			}

			wp.ajax.send( 'as3cfpro_update_acl', {
				data: {
					_ajax_nonce: as3cfpro_media.nonces.singular_update_acl,
					id: this.model.get( 'id' ),
					acl: newACL
				}
			} )
				.done( _.bind( this.updateACL, this ) )
				.fail( _.bind( this.renderACLError, this ) );
		},

		renderACLError: function() {
			$( '#as3cfpro-updating' ).remove();
			$( '#as3cfpro-toggle-acl' ).show();
			alert( as3cfpro_media.strings.change_acl_error );
		},

		updateACL: function( response ) {
			if ( null == response.acl_display || null == response.title || null == response.acl || null == response.url ) {
				this.renderACLError();

				return;
			}

			this.model.set( 'url', response.url );
			this.render();

			var toggle = $( '#as3cfpro-toggle-acl' );

			$( '#as3cfpro-updating' ).remove();

			toggle.text( response.acl_display );
			toggle.attr( 'title', response.title );
			toggle.data( 'currentacl', response.acl );
			toggle.show();
		}
	} );

} )( jQuery, _ );
