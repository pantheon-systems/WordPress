/* global affwp_batch_vars */
jQuery(document).ready(function($) {

	var AffWP_Batch, AffWP_Batch_Import;

	/**
	 * Batch Processor.
	 *
	 * @since 2.0
	 */
	AffWP_Batch = {

		init : function() {
			this.submit();
		},

		/**
		 * Handles form submission preceding batch processing.
		 *
		 * @since 2.0
		 */
		submit : function() {

			var	self = this,
				form = $( '.affwp-batch-form' );

			form.on( 'submit', function( event ) {
				event.preventDefault();

				var submitButton = $(this).find( 'input[type="submit"]' );

				if ( ! submitButton.hasClass( 'button-disabled' ) ) {

					// Handle the Are You Sure (AYS) if present on the form element.
					var ays = $( this ).data( 'ays' );

					if ( ays !== undefined ) {
						if ( ! confirm( ays ) ) {
							return;
						}
					}

					var data = {
						batch_id: $( this ).data( 'batch_id' ),
						nonce: $( this ).data( 'nonce' ),
						form: $( this ).serializeAssoc(),
					};

					// Disable the button.
					submitButton.addClass( 'button-disabled' );

					$( this ).find('.notice-wrap').remove();

					// Add the progress bar.
					$( this ).append( '<div class="notice-wrap"><div class="affwp-batch-progress"><div></div></div></div>' );

					// Add the spinner.
					submitButton.parent().append( '<span class="spinner is-active"></span>' );

					// Start the process.
					self.process_step( 1, data, self );

				}

			} );
		},

		/**
		 * Processes a single batch of data.
		 *
		 * @since 2.0
		 *
		 * @param {integer}  step Step in the process.
		 * @param {string[]} data Form data.
		 * @param {object}   self Instance.
		 */
		process_step : function( step, data, self ) {

			var self = this;

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					batch_id: data.batch_id,
					action: 'process_batch_request',
					nonce: data.nonce,
					form: data.form,
					step: step,
					data: data
				},
				dataType: "json",
				success: function( response ) {

					if( response.data.done || response.data.error ) {

						var batchSelector = response.data.mapping ? '.affwp-batch-import-form' : '.affwp-batch-form';

						// We need to get the actual in progress form, not all forms on the page
						var	batchForm   = $( batchSelector ),
							spinner     = batchForm.find( '.spinner' ),
							notice_wrap = batchForm.find('.notice-wrap');

						batchForm.find('.button-disabled').removeClass('button-disabled');

						if ( response.data.error ) {

							spinner.remove();
							notice_wrap.html('<div class="updated error"><p>' + response.data.error + '</p></div>');

						} else if ( response.data.done ) {

							spinner.remove();
							notice_wrap.html('<div id="affwp-batch-success" class="updated notice"><p class="affwp-batch-success">' + response.data.message + '</p></div>');

							if ( response.data.url ) {
								window.location = response.data.url;
							}

						} else {

							notice_wrap.remove();

						}
					} else {
						$('.affwp-batch-progress div').animate({
							width: response.data.percentage + '%',
						}, 50, function() {
							// Animation complete.
						});

						self.process_step( parseInt( response.data.step ), data, self );
					}

				}
			}).fail(function (response) {
				if ( window.console && window.console.log ) {
					console.log( response );
				}
			});

		},

	};

	AffWP_Batch.init();

	AffWP_Batch_Import = $.extend( {}, AffWP_Batch, {

		submit: function() {
			var	self = this;

			// Handle multiple importers on the same screen.
			$( '.affwp-batch-import-form' ).each( function( index, obj ) {
				$( this ).ajaxForm( {
					beforeSubmit: self.before_submit,
					complete:     self.complete,
					dataType:     'json',
					error:        self.error,
					data:         {
						action:   'process_batch_import',
						batch_id: $( this ).data( 'batch_id' ),
						nonce:    $( this ).data( 'nonce' )
					},
					url:          ajaxurl,
					resetForm:    true
				} );
			} );

		},

		before_submit: function( arr, $form, options ) {
			var self = this;

			$form.find('.notice-wrap').remove();
			$form.append( '<div class="notice-wrap"><span class="spinner is-active"></span><div class="affwp-batch-progress"><div></div></div></div>' );

			// Check whether client browser fully supports all File API.
			if ( window.File && window.FileReader && window.FileList && window.Blob ) {

				// HTML5 File API is supported by browser

			} else {

				var import_form = $( '.affwp-batch-import-form' ).find( '.affwp-batch-progress' ).parent().parent();
				var notice_wrap = import_form.find( '.notice-wrap' );

				import_form.find( '.button-disabled' ).removeClass( 'button-disabled' );

				// Error for older unsupported browsers that doesn't support HTML5 File API.
				notice_wrap.html('<div class="update error"><p>' + affwp_batch_vars.unsupported_browser + '</p></div>');
				return false;

			}

		},

		success: function( responseText, statusText, xhr, $form ) {
			console.log( $form );
		},

		complete: function( xhr ) {

			var	response = jQuery.parseJSON( xhr.responseText ),
				self     = this;

			if( response.success ) {

				// Select only the current form.
				var $form = $('.affwp-batch-import-form .notice-wrap').parent();

				$form.find( '.affwp-import-file-wrap, .notice-wrap' ).remove();
				$form.find( '.affwp-import-options' ).slideDown();

				// Show column mapping
				var select  = $form.find( 'select.affwp-import-csv-column' );
				var row     = select.parent().parent();
				var options = '';

				var selectName, selectRegex;

				var columns = response.data.columns.sort(function(a,b) {
					if( a < b ) return -1;
					if( a > b ) return 1;
					return 0;
				});

				$.each( select, function( selectKey, selectValue ) {

					currentSelect = $( this );
					selectName    = $( selectValue ).attr( 'name' );

					$.each( columns, function( columnKey, columnValue ) {

						processedColumnValue = columnValue.toLowerCase().replace( / /g, '_' );

						columnRegex = new RegExp( "\\[" + processedColumnValue + "\\]" );

						if ( selectName.length && selectName.match( columnRegex ) ) {
							// If the column matches a select, auto-map it. Boom.
							options += '<option value="' + columnValue + '" selected="selected">' + columnValue + '</option>';

							// Update the preview if there's a first-row value.
							if ( false != response.data.first_row[ columnValue ] ) {
								currentSelect.parent().next().html( response.data.first_row[ columnValue ] );
							} else {
								currentSelect.parent().next().html( '' );
							}

						} else {
							options += '<option value="' + columnValue + '">' + columnValue + '</option>';
						}

					} );

					// Add the options markup to the select.
					$( this ).append( options );

					// Reset options.
					options = '';

				} );

				select.on( 'change', function() {
					var $key = $(this).val();

					if( ! $key ) {

						$(this).parent().next().html( '' );

					} else {

						if( false != response.data.first_row[$key] ) {
							$(this).parent().next().html( response.data.first_row[$key] );
						} else {
							$(this).parent().next().html( '' );
						}

					}

				});

				$('body').on( 'click', '.affwp-import-proceed', function( event ) {

					event.preventDefault();

					// Validate for required fields.
					if ( $form.data( 'required' ) ) {
						var	required = $form.data( 'required' ),
							requiredFields = [];

						if ( required.indexOf( ',' ) ) {
							requiredFields = required.split( ',' );
						} else {
							requiredFields = [ required ];
						}

						var triggerValidation = false;

						$.each( requiredFields, function( key, value ) {
							field    = $( "select[name='affwp-import-field[" + value + "]']" );
							tableRow = field.parent().parent();

							// Remove the validation class if this is a repeat click.
							tableRow.removeClass( 'affwp-required-import-field' );

							// If nothing is mapped, trigger validation.
							if ( field.val() == '' ) {
								triggerValidation = true;

								tableRow.addClass( 'affwp-required-import-field' );
								field.parent().next().html( affwp_batch_vars.import_field_required );
							}
						} );

						// If validation has been triggered, bail from submitting the form.
						if ( triggerValidation ) {
							return;
						}

					}

					$form.find( '.notice-wrap' ).remove();

					// Add the spinner.
					$( this ).parent().append( '<span class="spinner is-active"></span>' );

					$form.append( '<div class="notice-wrap"><div class="affwp-batch-progress"><div></div></div></div>' );

					response.data.mapping = $form.serialize();
					response.data.form = $form.serializeAssoc();

					AffWP_Batch.process_step( 1, response.data, self );
				});

			} else {

				self.error( xhr );

			}

		},

		error: function( xhr ) {

			// Something went wrong. This will display error on form
			var	response    = jQuery.parseJSON( xhr.responseText ),
				import_form = $( '.affwp-batch-import-form' ).find( '.affwp-batch-progress' ).parent().parent(),
				notice_wrap = import_form.find( '.notice-wrap' ),
				self        = this;

			import_form.find( '.button-disabled' ).removeClass( 'button-disabled' );

			if ( response.data.error ) {

				notice_wrap.html('<div class="update error"><p>' + response.data.error + '</p></div>');

			} else {

				notice_wrap.remove();

			}
		}


	} );

	AffWP_Batch_Import.init();

	$.extend({
		isArray: function (arr){
			if (arr && typeof arr == 'object'){
				if (arr.constructor == Array){
					return true;
				}
			}
			return false;
		},
		arrayMerge: function (){
			var a = {};
			var n = 0;
			var argv = $.arrayMerge.arguments;
			for (var i = 0; i < argv.length; i++){
				if ($.isArray(argv[i])){
					for (var j = 0; j < argv[i].length; j++){
						a[n++] = argv[i][j];
					}
					a = $.makeArray(a);
				} else {
					for (var k in argv[i]){
						if (isNaN(k)){
							var v = argv[i][k];
							if (typeof v == 'object' && a[k]){
								v = $.arrayMerge(a[k], v);
							}
							a[k] = v;
						} else {
							a[n++] = argv[i][k];
						}
					}
				}
			}
			return a;
		},
		count: function (arr){
			if ($.isArray(arr)){
				return arr.length;
			} else {
				var n = 0;
				for (var k in arr){
					if (!isNaN(k)){
						n++;
					}
				}
				return n;
			}
		},
	});

	$.fn.extend({
		serializeAssoc: function (){
			var o = {
				aa: {},
				add: function (name, value){
					var tmp = name.match(/^(.*)\[([^\]]*)\]$/);
					if (tmp){
						var v = {};
						if (tmp[2])
							v[tmp[2]] = value;
						else
							v[$.count(v)] = value;
						this.add(tmp[1], v);
					}
					else if (typeof value == 'object'){
						if (typeof this.aa[name] != 'object'){
							this.aa[name] = {};
						}
						this.aa[name] = $.arrayMerge(this.aa[name], value);
					}
					else {
						this.aa[name] = value;
					}
				}
			};
			var a = $(this).serializeArray();
			for (var i = 0; i < a.length; i++){
				o.add(a[i].name, a[i].value);
			}
			return o.aa;
		}
	});

} );
