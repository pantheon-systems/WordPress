(function( $, _ ) {

	// Local reference to the WordPress media namespace.
	var media = wp.media;

	/**
	 * A button for S3 actions
	 *
	 * @constructor
	 * @augments wp.media.view.Button
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.s3Button = media.view.Button.extend( {
		defaults: {
			text: '',
			style: 'primary',
			size: 'large',
			disabled: false
		},
		initialize: function( options ) {
			if ( options ) {
				this.options = options;
				this.defaults.text = as3cfpro_media.strings[ this.options.action ];
			}

			this.options = _.extend( {}, this.defaults, this.options );

			media.view.Button.prototype.initialize.apply( this, arguments );

			this.controller.on( 'selection:toggle', this.toggleDisabled, this );
		},

		toggleDisabled: function() {
			this.model.set( 'disabled', ! this.controller.state().get( 'selection' ).length );
		},

		click: function( e ) {
			e.preventDefault();
			if ( this.$el.hasClass( 'disabled' ) ) {
				return;
			}

			var selection = this.controller.state().get( 'selection' );

			if ( ! selection.length ) {
				return;
			}

			var askConfirm = false;
			var that = this;
			var models = [];

			selection.each( function( model ) {
				models.push( model );

				if ( ! askConfirm && that.options.confirm ) {
					if ( model.attributes[ that.options.confirm ] ) {
						askConfirm = true;
					}
				}
			} );

			if ( this.options.confirm && askConfirm ) {
				if ( ! confirm( as3cfpro_media.strings[ this.options.confirm ] ) ) {
					return;
				}
			}

			var payload = {
				_ajax_nonce: as3cfpro_media.nonces[ this.options.scope + '_' + this.options.action ],
				scope: this.options.scope,
				s3_action: this.options.action,
				ids: _.pluck( models, 'id' )
			};

			this.startS3Action();
			this.fireS3Action( payload )
				.done( function() {
					_.each( models, function( model ) {

						// Refresh the attributes for each model from the server.
						model.fetch();
					} );
				} );
		},

		startS3Action: function() {
			$( '.media-toolbar .spinner' ).css( 'visibility', 'visible' ).show();
			$( '.media-toolbar-secondary .button' ).addClass( 'disabled' );
		},

		/**
		 * Send the S3 action request via ajax.
		 *
		 * @param {object} payload
		 *
		 * @return {$.promise}      A jQuery promise that represents the request,
		 *                          decorated with an abort() method.
		 */
		fireS3Action: function( payload ) {
			return wp.ajax.send( 'as3cfpro_process_media_action', { data: payload } )
				.done( _.bind( this.returnS3Action, this ) );
		},

		returnS3Action: function( response ) {
			if ( response && '' !== response ) {
				$( '.as3cf-notice' ).remove();
				$( '#wp-media-grid h1' ).after( response );
			}

			this.controller.trigger( 'selection:action:done' );
			$( '.media-toolbar .spinner' ).hide();
			$( '.media-toolbar-secondary .button' ).removeClass( 'disabled' );
		},

		render: function() {
			media.view.Button.prototype.render.apply( this, arguments );
			if ( this.controller.isModeActive( 'select' ) ) {
				this.$el.addClass( 's3-actions-selected-button' );
			} else {
				this.$el.addClass( 's3-actions-selected-button hidden' );
			}
			this.toggleDisabled();
			return this;
		}
	} );

	/**
	 * Show and hide the S3 buttons for the grid view only
	 */
	// Local instance of the SelectModeToggleButton to extend
	var wpSelectModeToggleButton = media.view.SelectModeToggleButton;

	/**
	 * Extend the SelectModeToggleButton functionality to show and hide
	 * the S3 buttons when the Bulk Select button is clicked
	 */
	media.view.SelectModeToggleButton = wpSelectModeToggleButton.extend( {
		toggleBulkEditHandler: function() {
			wpSelectModeToggleButton.prototype.toggleBulkEditHandler.call( this, arguments );
			var toolbar = this.controller.content.get().toolbar;

			if ( this.controller.isModeActive( 'select' ) ) {
				toolbar.$( '.s3-actions-selected-button' ).removeClass( 'hidden' );
			} else {
				toolbar.$( '.s3-actions-selected-button' ).addClass( 'hidden' );
			}
		}
	} );

	// Local instance of the AttachmentsBrowser
	var wpAttachmentsBrowser = media.view.AttachmentsBrowser;

	/**
	 * Extend the Attachments browser toolbar to add the S3 buttons
	 */
	media.view.AttachmentsBrowser = wpAttachmentsBrowser.extend( {
		createToolbar: function() {
			wpAttachmentsBrowser.prototype.createToolbar.call( this );

			_( as3cfpro_media.actions.bulk ).each( function( action ) {
				this.toolbar.set( action + 'S3SelectedButton', new media.view.s3Button( {
					action: action,
					scope: 'bulk',
					controller: this.controller,
					priority: -60
				} ).render() );
			}.bind( this ) );
		}
	} );

	$( document ).ready( function() {
		// Ask for confirmation when trying to remove attachment from S3 when the local file is missing
		$( 'body' ).on( 'click', '.as3cfpro_remove a.local-warning', function( event ) {
			if ( confirm( as3cfpro_media.strings.local_warning ) ) {
				return true;
			}
			event.preventDefault();
			event.stopImmediatePropagation();
			return false;
		} );

		// Ask for confirmation on bulk action removal from S3
		$( 'body' ).on( 'click', '.bulkactions #doaction', function( e ) {

			var action = $( '#bulk-action-selector-top' ).val();
			if ( 'bulk_as3cfpro_remove' !== action ) {
				// No need to do anything when not removing from S3
				return true;
			}

			var continueRemoval = false;
			var mediaChecked = 0;

			// Show warning if we have selected attachments to remove that have missing local files
			$( 'input:checkbox[name="media[]"]:checked' ).each( function() {
				var $titleTh = $( this ).parent().siblings( '.column-title' );

				if ( $titleTh.find( '.row-actions span.as3cfpro_remove a' ).hasClass( 'local-warning' ) ) {
					mediaChecked++;

					if ( confirm( as3cfpro_media.strings.bulk_local_warning ) ) {
						continueRemoval = true;
					}

					// Break out of loop early
					return false;
				}
			} );

			if ( mediaChecked > 0 ) {
				// If media selected that have local files missing, return the outcome of the confirmation
				return continueRemoval;
			}

			// No media selected continue form submit
			return true;
		} );

	} );

})( jQuery, _ );
