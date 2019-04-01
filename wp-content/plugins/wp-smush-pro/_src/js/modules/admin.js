/**
 * Processes bulk smushing
 *
 * @author Umesh Kumar <umeshsingla05@gmail.com>
 *
 * TODO: Use Element tag for all the class selectors
 */
let WP_Smush = WP_Smush || {};
window.WP_Smush = WP_Smush;

/**
 * Smush translation strings.
 *
 * @var {array} wp_smush_msgs
 */
if ( wp_smush_msgs ) {
	const wp_smush_msgs = wp_smush_msgs;
}

/**
 * Show/hide the progress bar for Smushing/Restore/SuperSmush
 *
 * @param cur_ele
 * @param txt Message to be displayed
 * @param {string} state show/hide
 */
let progress_bar = function ( cur_ele, txt, state ) {
	//Update Progress bar text and show it
	let progress_button = cur_ele.parents().eq( 1 ).find( '.wp-smush-progress' );

	if ( 'show' === state ) {
		progress_button.html( txt );
	} else {
		/** @var {string} wp_smush_msgs.all_done */
		progress_button.html( wp_smush_msgs.all_done );
	}

	progress_button.toggleClass( 'visible' );
};

/**
 * Check membership validity
 *
 * @param {int} data.show_warning
 */
let membership_validity = function ( data ) {
	const member_validity_notice = jQuery( '#wp-smush-invalid-member' );

	//Check for Membership warning

	if ( 'undefined' !== typeof ( data ) && 'undefined' !== typeof ( data.show_warning ) && member_validity_notice.length > 0 ) {
		if ( data.show_warning ) {
			member_validity_notice.show();
		} else {
			member_validity_notice.hide();
		}
	}
};

let remove_element = function ( el, timeout ) {
	if ( typeof timeout === 'undefined' ) {
		timeout = 100;
	}
	el.fadeTo( timeout, 0, function () {
		el.slideUp( timeout, function () {
			el.remove();
		} );
	} );
};

jQuery( function ( $ ) {
	'use strict';

	/**
	 * Remove the quick setup dialog
	 */
	function remove_dialog() {
		$( 'dialog#smush-quick-setup' ).remove();
	}

	/**
	 * Update image size in attachment info panel.
	 *
	 * @since 2.8
	 *
	 * @param new_size
	 */
	function update_image_stats( new_size ) {
		if ( 0 === new_size ) {
			return;
		}

		let attachmentSize = $( '.attachment-info .file-size' );
		const currentSize = attachmentSize.contents().filter( function () {
			return this.nodeType === 3;
		} ).text();

		// There is a space before the size.
		if ( currentSize !== ( ' ' + new_size ) ) {
			const sizeStrongEl = attachmentSize.contents().filter( function () {
				return this.nodeType === 1;
			} ).text();
			attachmentSize.html( '<strong>' + sizeStrongEl + '</strong> ' + new_size );
		}
	}

	// Show the Quick Setup dialog.
	if ( $( '#smush-quick-setup' ).size() > 0 ) {
		/** @var {string} wp_smush_msgs.quick_setup_title */
		WDP.showOverlay( "#smush-quick-setup", {
			title: wp_smush_msgs.quick_setup_title,
			class: 'no-close wp-smush-overlay wp-smush-quick-setup'
		} );
		remove_dialog();
	}

	let smushAddParams = function ( url, data ) {
		if ( !$.isEmptyObject( data ) ) {
			url += ( url.indexOf( '?' ) >= 0 ? '&' : '?' ) + $.param( data );
		}

		return url;
	};

	// url for smushing
	WP_Smush.errors = [];

	/** @var {array} wp_smushit_data */
	WP_Smush.timeout = wp_smushit_data.timeout;

	/**
	 * Checks for the specified param in URL
	 * @param arg
	 * @returns {*}
	 */
	WP_Smush.geturlparam = function ( arg ) {
		var $sPageURL = window.location.search.substring( 1 );
		var $sURLVariables = $sPageURL.split( '&' );

		for ( var i = 0; i < $sURLVariables.length; i++ ) {
			var $sParameterName = $sURLVariables[i].split( '=' );
			if ( $sParameterName[0] == arg ) {
				return $sParameterName[1];
			}
		}
	};

	WP_Smush.Smush = function ( $button, bulk, smush_type ) {
		var self = this;
		var skip_resmush = $button.data( 'smush' );
		//If smush attribute is not defined, Need not skip resmush ids
		skip_resmush = ( ( typeof skip_resmush == typeof undefined ) || skip_resmush == false ) ? false : true;

		this.init = function () {
			this.$button = $( $button[0] );
			this.is_bulk = typeof bulk ? bulk : false;
			this.url = ajaxurl;
			this.$log = $( ".smush-final-log" );
			this.deferred = jQuery.Deferred();
			this.deferred.errors = [];

			var ids = wp_smushit_data.resmush.length > 0 && !skip_resmush ? ( wp_smushit_data.unsmushed.length > 0 ? wp_smushit_data.resmush.concat( wp_smushit_data.unsmushed ) : wp_smushit_data.resmush ) : wp_smushit_data.unsmushed;
			if ( 'object' == typeof ids ) {
				//If button has resmush class, and we do have ids that needs to resmushed, put them in the list
				this.ids = ids.filter( function ( itm, i, a ) {
					return i == a.indexOf( itm );
				} );
			} else {
				this.ids = ids;
			}

			this.is_bulk_resmush = wp_smushit_data.resmush.length > 0 && !skip_resmush ? true : false;

			this.$status = this.$button.parent().find( '.smush-status' );

			//Added for NextGen support
			this.smush_type = 'undefined' != typeof smush_type ? smush_type : 'media';
			this.single_ajax_suffix = 'nextgen' == this.smush_type ? 'smush_manual_nextgen' : 'wp_smushit_manual';
			this.bulk_ajax_suffix = 'nextgen' == this.smush_type ? 'wp_smushit_nextgen_bulk' : 'wp_smushit_bulk';
			this.url = this.is_bulk ? smushAddParams( this.url, {action: this.bulk_ajax_suffix} ) : smushAddParams( this.url, {action: this.single_ajax_suffix} );
		};

		/** Send Ajax request for smushing the image **/
		WP_Smush.ajax = function ( is_bulk_resmush, $id, $send_url, $getnxt, nonce ) {
			"use strict";
			var param = {
				is_bulk_resmush: is_bulk_resmush,
				attachment_id: $id,
				get_next: $getnxt,
				_nonce: nonce
			};
			param = jQuery.param( param );
			return $.ajax( {
				type: "GET",
				data: param,
				url: $send_url,
				timeout: WP_Smush.timeout,
				dataType: 'json'
			} );
		};

		//Show loader in button for single and bulk smush
		this.start = function () {
			this.$button.attr( 'disabled', 'disabled' );
			this.$button.addClass( 'wp-smush-started' );

			this.bulk_start();
			this.single_start();
		};

		this.bulk_start = function () {
			if ( !this.is_bulk ) return;

			//Hide the Bulk Div
			$( '.wp-smush-bulk-wrapper' ).hide();

			//Show the Progress Bar
			$( '.bulk-smush-wrapper .wp-smush-bulk-progress-bar-wrapper' ).show();

			//Remove any Global Notices if there
			$( '.sui-notice-top' ).remove();

			//Hide the Bulk Limit message
			$( 'p.smush-error-message.limit_exceeded' ).remove();
			//Hide parent wrapper, if there are no other messages
			if ( $( 'div.smush-final-log p' ).length <= 0 ) {
				$( 'div.smush-final-log' ).hide();
			}
		};

		this.single_start = function () {
			if ( this.is_bulk ) return;
			this.show_loader();
			this.$status.removeClass( "error" );
		};

		this.enable_button = function () {
			this.$button.prop( "disabled", false );
			//For Bulk process, Enable other buttons
			$( 'button.wp-smush-all' ).removeAttr( 'disabled' );
			$( 'button.wp-smush-scan, a.wp-smush-lossy-enable, button.wp-smush-resize-enable, input#wp-smush-save-settings' ).removeAttr( 'disabled' );
		};

		this.show_loader = function () {
			progress_bar( this.$button, wp_smush_msgs.smushing, 'show' );
		};

		this.hide_loader = function () {
			progress_bar( this.$button, wp_smush_msgs.smushing, 'hide' );
		};

		this.single_done = function () {
			if ( this.is_bulk ) return;

			this.hide_loader();

			this.request.done( function ( response ) {
				if ( typeof response.data != 'undefined' ) {

					// Check if stats div exists.
					var parent = self.$status.parent();
					var stats_div = parent.find( '.smush-stats-wrapper' );

					// If we've updated status, replace the content.
					if ( response.data.status ) {
						//remove Links
						parent.find( '.smush-status-links' ).remove();
						self.$status.replaceWith( response.data.status );
					}

					// Check whether to show membership validity notice or not.
					membership_validity( response.data );

					if ( response.success && response.data !== "Not processed" ) {
						self.$status.removeClass( 'sui-hidden' );
						self.$button.parent().removeClass( 'unsmushed' ).addClass( 'smushed' );
						self.$button.remove();
					} else {
						self.$status.addClass( "error" );
						self.$status.html( response.data.error_msg );
						self.$status.show();
					}
					if ( 'undefined' != stats_div && stats_div.length ) {
						stats_div.replaceWith( response.data.stats );
					} else {
						parent.append( response.data.stats );
					}

					// Update image size in attachment info panel.
					update_image_stats( response.data.new_size );
				}
				self.enable_button();
			} ).error( function ( response ) {
				self.$status.html( response.data );
				self.$status.addClass( "error" );
				self.enable_button();
			} );

		};

		this.sync_stats = function () {
			var message_holder = $( 'div.wp-smush-bulk-progress-bar-wrapper div.wp-smush-count.tc' );
			//Store the existing content in a variable
			var progress_message = message_holder.html();
			message_holder.html( wp_smush_msgs.sync_stats );

			//Send ajax
			$.ajax( {
				type: "GET",
				url: ajaxurl,
				data: {
					'action': 'get_stats'
				},
				success: function ( response ) {
					if ( response && 'undefined' != typeof response ) {
						response = response.data;
						$.extend( wp_smushit_data, {
							count_images: response.count_images,
							count_smushed: response.count_smushed,
							count_total: response.count_total,
							count_resize: response.count_resize,
							count_supersmushed: response.count_supersmushed,
							savings_bytes: response.savings_bytes,
							savings_conversion: response.savings_conversion,
							savings_resize: response.savings_resize,
							size_before: response.size_before,
							size_after: response.size_after
						} );
						//Got the stats, Update it
						update_stats( this.smush_type );
					}
				}
			} ).always( function () {
					message_holder.html( progress_message );
				}
			);
		};

		/** After the Bulk Smushing has been Finished **/
		this.bulk_done = function () {
			if ( !this.is_bulk ) return;

			//Enable the button
			this.enable_button();

			//Show Notice
			if ( self.ids.length == 0 ) {
				$( '.bulk-smush-wrapper .wp-smush-all-done, .wp-smush-pagespeed-recommendation' ).show();
				$( '.wp-smush-bulk-wrapper' ).hide();
				// Hide the progress bar if scan is finished.
				$( '.wp-smush-bulk-progress-bar-wrapper' ).hide();
			} else {
				if ( $( '.bulk-smush-wrapper .wp-smush-resmush-notice' ).length > 0 ) {
					$( '.bulk-smush-wrapper .wp-smush-resmush-notice' ).show();
				} else {
					$( '.bulk-smush-wrapper .wp-smush-remaining' ).show();
				}
			}

			//Enable Resmush and scan button
			$( '.wp-resmush.wp-smush-action, .wp-smush-scan' ).removeAttr( 'disabled' );

			// Show loader.
			if ( self.ids.length == 0 ) {
				$( '.sui-summary-smush .smush-stats-icon' ).addClass( 'sui-hidden' );
			} else {
				$( '.sui-summary-smush .smush-stats-icon' ).removeClass( 'sui-icon-loader sui-loading sui-hidden' ).addClass( 'sui-icon-info sui-warning' );
			}
		};

		this.is_resolved = function () {
			"use strict";
			return this.deferred.state() === "resolved";
		};

		this.free_exceeded = function () {
			if ( self.ids.length > 0 ) {
				let progress = jQuery( '.wp-smush-bulk-progress-bar-wrapper' );
				progress.addClass( 'wp-smush-exceed-limit' )
					.find( '.sui-progress-close' )
					.attr( 'data-tooltip', wp_smush_msgs.bulk_resume )
					.removeClass( 'wp-smush-cancel-bulk' )
					.addClass( 'wp-smush-all' );

				progress.find( '.sui-box-body.sui-hidden' ).removeClass( 'sui-hidden' );
			} else {
				$( '.wp-smush-notice.wp-smush-all-done, .wp-smush-pagespeed-recommendation' ).show();
			}
		};

		this.update_remaining_count = function () {
			if ( this.is_bulk_resmush ) {
				//ReSmush Notice
				if ( $( '.wp-smush-resmush-notice .wp-smush-remaining-count' ).length && 'undefined' != typeof self.ids ) {
					$( '.wp-smush-resmush-notice .wp-smush-remaining-count' ).html( self.ids.length );
				}
			} else {
				//Smush Notice
				if ( $( '.bulk-smush-wrapper .wp-smush-remaining-count' ).length && 'undefined' != typeof self.ids ) {
					$( '.bulk-smush-wrapper .wp-smush-remaining-count' ).html( self.ids.length );
				}
			}

			// Update sidebar count.
			if ( $( '.smush-sidenav .wp-smush-remaining-count' ).length && 'undefined' != typeof self.ids ) {
				if ( self.ids.length > 0 ) {
					$( '.smush-sidenav .wp-smush-remaining-count' ).html( self.ids.length );
				} else {
					$( '.sui-summary-smush .smush-stats-icon' ).addClass( 'sui-hidden' );
					$( '.smush-sidenav .wp-smush-remaining-count' ).removeClass( 'sui-tag sui-tag-warning' ).html( '' );
				}
			}
		};

		this.update_progress = function ( _res ) {
			//If not bulk
			if ( !this.is_bulk_resmush && !this.is_bulk ) {
				return;
			}

			var progress = '';

			//Update localized stats
			if ( _res && ( 'undefined' != typeof _res.data || 'undefined' != typeof _res.data.stats ) ) {
				update_localized_stats( _res.data.stats, this.smush_type );
			}

			if ( !this.is_bulk_resmush ) {
				//handle progress for normal bulk smush
				progress = ( wp_smushit_data.count_smushed / wp_smushit_data.count_total ) * 100;
			} else {
				//If the Request was successful, Update the progress bar
				if ( _res.success ) {
					//Handle progress for Super smush progress bar
					if ( wp_smushit_data.resmush.length > 0 ) {
						//Update the Count
						$( '.wp-smush-images-remaining' ).html( wp_smushit_data.resmush.length );
					} else if ( wp_smushit_data.resmush.length == 0 && this.ids.length == 0 ) {
						//If all images are resmushed, show the All Smushed message

						//Show All Smushed
						$( '.bulk-resmush-wrapper .wp-smush-all-done, .wp-smush-pagespeed-recommendation' ).removeClass( 'sui-hidden' );

						//Hide Everything else
						$( '.wp-smush-resmush-wrap, .wp-smush-bulk-progress-bar-wrapper' ).hide();
					}
				}

				//handle progress for normal bulk smush
				//Set Progress Bar width
				if ( 'undefined' !== typeof self.ids && 'undefined' !== typeof wp_smushit_data.count_total && wp_smushit_data.count_total > 0 ) {
					progress = ( wp_smushit_data.count_smushed / wp_smushit_data.count_total ) * 100;
				}
			}

			//Show Bulk Wrapper and Smush Notice
			if ( self.ids.length == 0 ) {
				//Sync stats for Bulk smush media Library ( Skip for Nextgen )
				if ( 'nextgen' != this.smush_type ) {
					this.sync_stats();
				}
				$( '.bulk-smush-wrapper .wp-smush-all-done, .wp-smush-pagespeed-recommendation' ).show();
				$( '.wp-smush-bulk-wrapper' ).hide();
			}

			//Update remaining count
			self.update_remaining_count();

			//if we have received the progress data, update the stats else skip
			if ( 'undefined' !== typeof _res.data.stats ) {
				// increase the progress bar
				this._update_progress( wp_smushit_data.count_smushed, progress );
				// update the counter
				this._update_progress_status( wp_smushit_data.count_smushed, wp_smushit_data.count_total);
			}
			// Update stats and counts.
			update_stats( this.smush_type );
		};

		this._update_progress = function ( count, width ) {
			"use strict";
			if ( !this.is_bulk && !this.is_bulk_resmush ) {
				return;
			}
			//Update the Progress Bar Width
			// get the progress bar
			var $progress_bar = jQuery( '.bulk-smush-wrapper .wp-smush-progress-inner' );
			if ( $progress_bar.length < 1 ) {
				return;
			}
			// increase progress
			$progress_bar.css( 'width', width + '%' );

		};

		this._update_progress_status = function ( smushed, total ) {
			let progress_status = jQuery( '.bulk-smush-wrapper .sui-progress-state-text' );

			if ( 1 > progress_status.length ) {
				return;
			}

			progress_status.find( 'span' ).html( smushed + '/' + total );
		};

		//Whether to send the ajax requests further or not
		this.continue = function () {
			var continue_smush = self.$button.attr( 'continue_smush' );

			if ( typeof continue_smush == typeof undefined ) {
				continue_smush = true;
			}

			if ( 'false' == continue_smush || !continue_smush ) {
				continue_smush = false;
			}

			return continue_smush && this.ids.length > 0 && this.is_bulk;
		};

		this.increment_errors = function ( id ) {
			WP_Smush.errors.push( id );
		};

		// Send ajax request for smushing single and bulk, call update_progress on ajax response.
		this.call_ajax = function () {
			let nonce_value = '';
			this.current_id = this.is_bulk ? this.ids.shift() : this.$button.data( "id" ); //remove from array while processing so we can continue where left off

			//Remove the id from respective variable as well
			this.update_smush_ids( this.current_id );

			let nonce_field = this.$button.parent().find( '#_wp_smush_nonce' );
			if ( nonce_field ) {
				nonce_value = nonce_field.val();
			}

			this.request = WP_Smush.ajax( this.is_bulk_resmush, this.current_id, this.url, 0, nonce_value )
				.error( function () {
					self.increment_errors( self.current_id );
				} ).done( function ( res ) {
					//Increase the error count except if bulk request limit excceded
					if ( typeof res.success === "undefined" || ( typeof res.success !== "undefined" && typeof res.success.data !== "undefined" && res.success === false && res.data.error !== 'bulk_request_image_limit_exceeded' ) ) {
						self.increment_errors( self.current_id );
					}
					//If no response or success is false, do not process further
					if ( !res || !res.success ) {
						//@todo: Handle Bulk Smush limit error message
						if ( 'undefined' !== typeof res && 'undefined' !== typeof res.data && typeof res.data.error !== 'undefined' ) {
							var error_class = 'undefined' != typeof res.data.error_class ? 'smush-error-message ' + res.data.error_class : 'smush-error-message';
							var error_msg = '<p class="' + error_class + '">' + res.data.error_message + '</p>';
							if ( 'undefined' != typeof res.data.error && 'bulk_request_image_limit_exceeded' == res.data.error ) {
								var ajax_error_message = $( '.wp-smush-ajax-error' );
								//If we have ajax error message div, append after it
								if ( ajax_error_message.length > 0 ) {
									ajax_error_message.after( error_msg );
								} else {
									//Otherwise prepend
									self.$log.prepend( error_msg );
								}
							} else if ( 'undefined' != typeof res.data.error_class && "" != res.data.error_class && $( 'div.smush-final-log .' + res.data.error_class ).length > 0 ) {
								var error_count = $( 'p.smush-error-message.' + res.data.error_class + ' .image-error-count' );
								//Get the error count, increase and append
								var image_count = error_count.html();
								image_count = parseInt( image_count ) + 1;
								//Append the updated image count
								error_count.html( image_count );
							} else {
								//Print the error on screen
								self.$log.append( error_msg );
							}
							self.$log.show();
						}
					}

					//Check whether to show the warning notice or not
					membership_validity( res.data );

					//Bulk Smush Limit Exceeded: Stop ajax requests, remove progress bar, append the last image id back to smush variable, and reset variables to allow the user to continue bulk smush
					if ( typeof res.data !== "undefined" && res.data.error == 'bulk_request_image_limit_exceeded' && !self.is_resolved() ) {
						//Add a data attribute to the smush button, to stop sending ajax
						self.$button.attr( 'continue_smush', false );

						self.free_exceeded();

						//Reinsert the current id
						wp_smushit_data.unsmushed.unshift( self.current_id );

						//Update the remaining count to length of remaining ids + 1 (Current id)
						self.update_remaining_count();
					} else {

						if ( self.is_bulk && res.success ) {
							self.update_progress( res );
						} else if ( self.ids.length == 0 ) {
							//Sync stats anyway
							self.sync_stats();
						}
					}
					self.single_done();
				} ).complete( function () {
					if ( !self.continue() || !self.is_bulk ) {
						//Calls deferred.done()
						self.deferred.resolve();
					} else {
						self.call_ajax();
					}
				} );

			self.deferred.errors = WP_Smush.errors;
			return self.deferred;
		};

		this.init( arguments );

		//Send ajax request for single and bulk smushing
		this.run = function () {

			// if we have a definite number of ids
			if ( this.is_bulk && this.ids.length > 0 ) {
				this.call_ajax();
			}

			if ( !this.is_bulk )
				this.call_ajax();

		};

		//Show bulk smush errors, and disable bulk smush button on completion
		this.bind_deferred_events = function () {

			this.deferred.done( function () {

				self.$button.removeAttr( 'continue_smush' );

				if ( WP_Smush.errors.length ) {
					var error_message = '<div class="wp-smush-ajax-error">' + wp_smush_msgs.error_in_bulk.replace( "{{errors}}", WP_Smush.errors.length ) + '</div>';
					//Remove any existing notice
					$( '.wp-smush-ajax-error' ).remove();
					self.$log.prepend( error_message );
				}

				self.bulk_done();

				//Re enable the buttons
				$( '.wp-smush-button:not(.wp-smush-finished), .wp-smush-scan' ).removeAttr( 'disabled' );
			} );

		};
		/**
		 * Handles the Cancel button Click
		 * Update the UI, and enables the bulk smush button
		 **/
		this.cancel_ajax = function () {
			$( '.wp-smush-cancel-bulk' ).on( 'click', function () {
				//Add a data attribute to the smush button, to stop sending ajax
				self.$button.attr( 'continue_smush', false );
				//Sync and update stats
				self.sync_stats();
				update_stats( this.smush_type );

				self.request.abort();
				self.enable_button();
				self.$button.removeClass( 'wp-smush-started' );
				wp_smushit_data.unsmushed.unshift( self.current_id );
				$( '.wp-smush-bulk-wrapper' ).show();

				//Hide the Progress Bar
				$( '.wp-smush-bulk-progress-bar-wrapper' ).hide();
			} );
		};
		/**
		 * Remove the current id from unsmushed/resmush variable
		 * @param current_id
		 */
		this.update_smush_ids = function ( current_id ) {
			if ( 'undefined' !== typeof wp_smushit_data.unsmushed && wp_smushit_data.unsmushed.length > 0 ) {
				var u_index = wp_smushit_data.unsmushed.indexOf( current_id );
				if ( u_index > -1 ) {
					wp_smushit_data.unsmushed.splice( u_index, 1 );
				}
			}
			//remove from the resmush list
			if ( 'undefined' !== typeof wp_smushit_data.resmush && wp_smushit_data.resmush.length > 0 ) {
				var index = wp_smushit_data.resmush.indexOf( current_id );
				if ( index > -1 ) {
					wp_smushit_data.resmush.splice( index, 1 );
				}
			}
		};

		this.start();
		this.run();
		this.bind_deferred_events();

		//Handle Cancel Ajax
		this.cancel_ajax();

		return this.deferred;
	};

	/**
	 * Handle the Bulk Smush/ Bulk Resmush button click
	 */
	$( 'body' ).on( 'click', 'button.wp-smush-all', function ( e ) {
		// prevent the default action
		e.preventDefault();

		$( '.sui-notice-top.sui-notice-success' ).remove();

		// Remove limit exceeded styles.
		let progress = $( '.wp-smush-bulk-progress-bar-wrapper' );
		progress.removeClass( 'wp-smush-exceed-limit' )
			.find( '.sui-progress-close' ).attr( 'data-tooltip', wp_smush_msgs.bulk_stop );
		// Hide Resume button
		progress.find( '.sui-box-body' ).addClass( 'sui-hidden' );

		//Disable Resmush and scan button
		$( '.wp-resmush.wp-smush-action, .wp-smush-scan, .wp-smush-button, a.wp-smush-lossy-enable, button.wp-smush-resize-enable, input#wp-smush-save-settings' ).attr( 'disabled', 'disabled' );

		//Check for ids, if there is none (Unsmushed or lossless), don't call smush function
		if ( typeof wp_smushit_data == 'undefined' ||
			( wp_smushit_data.unsmushed.length == 0 && wp_smushit_data.resmush.length == 0 )
		) {
			return false;
		}

		$( ".wp-smush-remaining" ).hide();

		// Show loader.
		$( '.sui-summary-smush .smush-stats-icon' ).removeClass( 'sui-icon-info sui-warning' ).addClass( 'sui-icon-loader sui-loading' );

		new WP_Smush.Smush( $( this ), true );
	} );

	/** Disable the action links **/
	var disable_links = function ( c_element ) {

		var parent = c_element.parent();
		//reduce parent opacity
		parent.css( {'opacity': '0.5'} );
		//Disable Links
		parent.find( 'a' ).attr( 'disabled', 'disabled' );
	};

	/** Enable the Action Links **/
	var enable_links = function ( c_element ) {

		var parent = c_element.parent();

		//reduce parent opacity
		parent.css( {'opacity': '1'} );
		//Disable Links
		parent.find( 'a' ).removeAttr( 'disabled' );
	};
	/**
	 * Restore image request with a specified action for Media Library / NextGen Gallery
	 * @param e
	 * @param current_button
	 * @param smush_action
	 * @returns {boolean}
	 */
	var process_smush_action = function ( e, current_button, smush_action, action ) {

		//If disabled
		if ( 'disabled' == current_button.attr( 'disabled' ) ) {
			return false;
		}

		e.preventDefault();

		//Remove Error
		$( '.wp-smush-error' ).remove();

		//Hide stats
		$( '.smush-stats-wrapper' ).hide();

		var mode = 'grid';
		if ( 'smush_restore_image' == smush_action ) {
			if ( $( document ).find( 'div.media-modal.wp-core-ui' ).length > 0 ) {
				mode = 'grid';
			} else {
				mode = window.location.search.indexOf( 'item' ) > -1 ? 'grid' : 'list';
			}
		}

		//Get the image ID and nonce
		var params = {
			action: smush_action,
			attachment_id: current_button.data( 'id' ),
			mode: mode,
			_nonce: current_button.data( 'nonce' )
		};

		//Reduce the opacity of stats and disable the click
		disable_links( current_button );

		progress_bar( current_button, wp_smush_msgs[action], 'show' );

		//Restore the image
		$.post( ajaxurl, params, function ( r ) {

			progress_bar( current_button, wp_smush_msgs[action], 'hide' );

			//reset all functionality
			enable_links( current_button );

			if ( r.success && 'undefined' != typeof(r.data.button) ) {
				//Replace in immediate parent for nextgen
				if ( 'undefined' != typeof (this.data) && this.data.indexOf( 'nextgen' ) > -1 ) {
					//Show the smush button, and remove stats and restore option
					current_button.parent().html( r.data.button );
				} else {
					//Show the smush button, and remove stats and restore option
					current_button.parents().eq( 1 ).html( r.data.button );
				}

				if ( 'undefined' != typeof (r.data) && 'restore' === action ) {
					update_image_stats( r.data.new_size );
				}
			} else {
				if ( r.data.message ) {
					//show error
					current_button.parent().append( r.data.message );
				}
			}
		} )
	};

	/**
	 * Validates the Resize Width and Height against the Largest Thumbnail Width and Height
	 *
	 * @param wrapper_div jQuery object for the whole setting row wrapper div
	 * @param width_only Whether to validate only width
	 * @param height_only Validate only Height
	 * @returns {boolean} All Good or not
	 *
	 */
	var validate_resize_settings = function ( wrapper_div, width_only, height_only ) {
		var resize_checkbox = wrapper_div.find( '#wp-smush-resize, #wp-smush-resize-quick-setup' );

		if ( !height_only ) {
			var width_input = wrapper_div.find( '#wp-smush-resize_width, #quick-setup-resize_width' );
			var width_error_note = wrapper_div.find( '.sui-notice-info.wp-smush-update-width' );
		}
		if ( !width_only ) {
			var height_input = wrapper_div.find( '#wp-smush-resize_height, #quick-setup-resize_height' );
			var height_error_note = wrapper_div.find( '.sui-notice-info.wp-smush-update-height' );
		}

		var width_error = false;
		var height_error = false;

		//If resize settings is not enabled, return true
		if ( !resize_checkbox.is( ':checked' ) ) {
			return true;
		}

		//Check if we have localised width and height
		if ( 'undefined' == typeof (wp_smushit_data.resize_sizes) || 'undefined' == typeof (wp_smushit_data.resize_sizes.width) ) {
			//Rely on server validation
			return true;
		}

		//Check for width
		if ( !height_only && 'undefined' != typeof width_input && parseInt( wp_smushit_data.resize_sizes.width ) > parseInt( width_input.val() ) ) {
			width_input.parent().addClass( 'sui-form-field-error' );
			width_error_note.show( 'slow' );
			width_error = true;
		} else {
			//Remove error class
			width_input.parent().removeClass( 'sui-form-field-error' );
			width_error_note.hide();
			if ( height_input.hasClass( 'error' ) ) {
				height_error_note.show( 'slow' );
			}
		}

		//Check for height
		if ( !width_only && 'undefined' != typeof height_input && parseInt( wp_smushit_data.resize_sizes.height ) > parseInt( height_input.val() ) ) {
			height_input.parent().addClass( 'sui-form-field-error' );
			//If we are not showing the width error already
			if ( !width_error ) {
				height_error_note.show( 'slow' );
			}
			height_error = true;
		} else {
			//Remove error class
			height_input.parent().removeClass( 'sui-form-field-error' );
			height_error_note.hide();
			if ( width_input.hasClass( 'error' ) ) {
				width_error_note.show( 'slow' );
			}
		}

		if ( width_error || height_error ) {
			return false;
		}
		return true;

	};

	/**
	 * Convert bytes to human readable form
	 * @param a, Bytes
	 * @param b, Number of digits
	 * @returns {*} Formatted Bytes
	 */
	var formatBytes = function ( a, b ) {
		var thresh = 1024;
		if ( Math.abs( a ) < thresh ) {
			return a + ' B';
		}
		var units = ['KB', 'MB', 'GB', 'TB', 'PB'];
		var u = -1;
		do {
			a /= thresh;
			++u;
		} while ( Math.abs( a ) >= thresh && u < units.length - 1 );
		return a.toFixed( b ) + ' ' + units[u];
	};

	/**
	 * Convert bytes to human readable form
	 * @param a, Bytes
	 * @param b, Number of digits
	 * @returns {*} Formatted Bytes
	 */
	var getSizeFromString = function ( formatted_size ) {
		return formatted_size.replace( /[a-zA-Z]/g, '' ).trim();
	};

	/**
	 * Convert bytes to human readable form
	 * @param a, Bytes
	 * @param b, Number of digits
	 * @returns {*} Formatted Bytes
	 */
	var getFormatFromString = function ( formatted_size ) {
		return formatted_size.replace( /[0-9.]/g, '' ).trim();
	};

	//Stackoverflow: http://stackoverflow.com/questions/1726630/formatting-a-number-with-exactly-two-decimals-in-javascript
	var precise_round = function ( num, decimals ) {
		var sign = num >= 0 ? 1 : -1;
		//Keep the percentage below 100
		num = num > 100 ? 100 : num;
		return (Math.round( (num * Math.pow( 10, decimals )) + (sign * 0.001) ) / Math.pow( 10, decimals ));
	};

	/**
	 * Update the progress bar width if we have images that needs to be resmushed
	 * @param unsmushed_count
	 * @returns {boolean}
	 */
	var update_progress_bar_resmush = function ( unsmushed_count ) {

		if ( 'undefined' == typeof unsmushed_count ) {
			return false;
		}

		var smushed_count = wp_smushit_data.count_total - unsmushed_count;

		//Update the Progress Bar Width
		// get the progress bar
		var $progress_bar = jQuery( '.bulk-smush-wrapper .wp-smush-progress-inner' );
		if ( $progress_bar.length < 1 ) {
			return;
		}

		var width = ( smushed_count / wp_smushit_data.count_total ) * 100;

		// increase progress
		$progress_bar.css( 'width', width + '%' );
	};

	var run_re_check = function ( button, process_settings ) {

		// Empty the button text and add loader class.
		button.text( '' ).addClass( 'sui-button-onload sui-icon-loader sui-loading' ).blur();

		//Check if type is set in data attributes
		var scan_type = button.data( 'type' );
		scan_type = 'undefined' == typeof scan_type ? 'media' : scan_type;

		//Remove the Skip resmush attribute from button
		$( 'button.wp-smush-all' ).removeAttr( 'data-smush' );

		//remove notices
		var el = $( '.sui-notice-top.sui-notice-success' );
		el.slideUp( 100, function () {
			el.remove();
		} );

		//Disable Bulk smush button and itself
		$( '.wp-smush-button' ).attr( 'disabled', 'disabled' );

		//Hide Settings changed Notice
		$( '.wp-smush-settings-changed' ).hide();

		//Ajax Params
		var params = {
			action: 'scan_for_resmush',
			type: scan_type,
			get_ui: true,
			process_settings: process_settings,
			wp_smush_options_nonce: jQuery( '#wp_smush_options_nonce' ).val()
		};

		//Send ajax request and get ids if any
		$.get( ajaxurl, params, function ( r ) {
			//Check if we have the ids,  initialize the local variable
			if ( 'undefined' != typeof r.data ) {
				//Update Resmush id list
				if ( 'undefined' != typeof r.data.resmush_ids ) {
					wp_smushit_data.resmush = r.data.resmush_ids;

					//Update wp_smushit_data ( Smushed count, Smushed Percent, Image count, Super smush count, resize savings, conversion savings )
					if ( 'undefinied' != typeof wp_smushit_data ) {
						wp_smushit_data.count_smushed = 'undefined' != typeof r.data.count_smushed ? r.data.count_smushed : wp_smushit_data.count_smushed;
						wp_smushit_data.count_supersmushed = 'undefined' != typeof r.data.count_supersmushed ? r.data.count_supersmushed : wp_smushit_data.count_supersmushed;
						wp_smushit_data.count_images = 'undefined' != typeof r.data.count_image ? r.data.count_image : wp_smushit_data.count_images;
						wp_smushit_data.size_before = 'undefined' != typeof r.data.size_before ? r.data.size_before : wp_smushit_data.size_before;
						wp_smushit_data.size_after = 'undefined' != typeof r.data.size_after ? r.data.size_after : wp_smushit_data.size_after;
						wp_smushit_data.savings_resize = 'undefined' != typeof r.data.savings_resize ? r.data.savings_resize : wp_smushit_data.savings_resize;
						wp_smushit_data.savings_conversion = 'undefined' != typeof r.data.savings_conversion ? r.data.savings_conversion : wp_smushit_data.savings_conversion;
						wp_smushit_data.count_resize = 'undefined' != typeof r.data.count_resize ? r.data.count_resize : wp_smushit_data.count_resize;
					}

					if ( 'nextgen' == scan_type ) {
						wp_smushit_data.bytes = parseInt( wp_smushit_data.size_before ) - parseInt( wp_smushit_data.size_after )
					}

					var smush_percent = ( wp_smushit_data.count_smushed / wp_smushit_data.count_total ) * 100;
					smush_percent = precise_round( smush_percent, 1 );

					//Update it in stats bar
					$( '.wp-smush-images-percent' ).html( smush_percent );

					//Hide the Existing wrapper
					var notices = $( '.bulk-smush-wrapper .sui-notice' );
					if ( notices.length > 0 ) {
						notices.hide();
						$( '.wp-smush-pagespeed-recommendation' ).hide();
					}
					//remove existing Re-Smush notices
					$( '.wp-smush-resmush-notice' ).remove();

					//Show Bulk wrapper
					$( '.wp-smush-bulk-wrapper' ).show();

					if ( 'undefined' !== typeof r.data.count ) {
						//Update progress bar
						update_progress_bar_resmush( r.data.count );
					}
				}
				//If content is received, Prepend it
				if ( 'undefined' != typeof r.data.content ) {
					$( '.bulk-smush-wrapper .sui-box-body' ).prepend( r.data.content );
				}
				//If we have any notice to show
				if ( 'undefined' != typeof r.data.notice ) {
					$( '.wp-smush-page-header' ).after( r.data.notice );
				}
				//Hide errors
				$( 'div.smush-final-log' ).hide();

				//Hide Super Smush notice if it's enabled in media settings
				if ( 'undefined' != typeof r.data.super_smush && r.data.super_smush ) {
					var enable_lossy = jQuery( '.wp-smush-enable-lossy' );
					if ( enable_lossy.length > 0 ) {
						enable_lossy.remove();
					}
					if ( 'undefined' !== r.data.super_smush_stats ) {
						$( '.super-smush-attachments .wp-smush-stats' ).html( r.data.super_smush_stats );
					}
				}
				update_stats( scan_type );
			}

		} ).always( function () {

			//Hide the progress bar
			jQuery( '.bulk-smush-wrapper .wp-smush-bulk-progress-bar-wrapper' ).hide();

			// Add check complete status to button.
			button.text( wp_smush_msgs.resmush_complete )
				.removeClass( 'sui-button-onload sui-icon-loader sui-loading' )
				.addClass( 'smush-button-check-success' );

			// Remove success message from button.
			setTimeout( function () {
				button.removeClass( 'smush-button-check-success' )
					.text( wp_smush_msgs.resmush_check );
			}, 2000 );

			$( '.wp-smush-button' ).removeAttr( 'disabled' );

			//If wp-smush-re-check-message is there, remove it
			if ( $( '.wp-smush-re-check-message' ).length ) {
				remove_element( $( '.wp-smush-re-check-message' ) );
			}
		} );
	};

	/**
	 * Set pro savings stats if not premium user.
	 *
	 * For non-premium users, show expected avarage savings based
	 * on the free version savings.
	 */
	var set_pro_savings = function () {

		// Default values.
		var savings = wp_smushit_data.savings_percent > 0 ? wp_smushit_data.savings_percent : 0;
		var savings_bytes = wp_smushit_data.savings_bytes > 0 ? wp_smushit_data.savings_bytes : 0;
		var orig_diff = 2.22058824;
		if ( savings > 49 ) {
			var orig_diff = 1.22054412;
		}
		//Calculate Pro savings
		if ( savings > 0 ) {
			savings = orig_diff * savings;
			savings_bytes = orig_diff * savings_bytes;
		}

		wp_smushit_data.pro_savings = {
			'percent': precise_round( savings, 1 ),
			'savings_bytes': formatBytes( savings_bytes, 1 )

		}
	};

	/**
	 * Adds the stats for the current image to existing stats
	 *
	 * @param image_stats
	 *
	 */
	var update_localized_stats = function ( image_stats, type ) {
		//Increase the smush count
		if ( 'undefined' == typeof wp_smushit_data ) {
			return;
		}

		//No need to increase attachment count, resize, conversion savings for directory smush
		if ( 'media' == type ) {
			wp_smushit_data.count_smushed = parseInt( wp_smushit_data.count_smushed ) + 1;

			//Increase smushed image count
			wp_smushit_data.count_images = parseInt( wp_smushit_data.count_images ) + parseInt( image_stats.count );

			//Increase super smush count, if applicable
			if ( image_stats.is_lossy ) {
				wp_smushit_data.count_supersmushed = parseInt( wp_smushit_data.count_supersmushed ) + 1;
			}

			//Add to Resize Savings
			wp_smushit_data.savings_resize = 'undefined' != typeof image_stats.savings_resize.bytes ? parseInt( wp_smushit_data.savings_resize ) + parseInt( image_stats.savings_resize.bytes ) : parseInt( wp_smushit_data.savings_resize );

			//Update Resize count
			wp_smushit_data.count_resize = 'undefined' != typeof image_stats.savings_resize.bytes ? parseInt( wp_smushit_data.count_resize ) + 1 : wp_smushit_data.count_resize;

			//Add to Conversion Savings
			wp_smushit_data.savings_conversion = 'undefined' != typeof image_stats.savings_conversion && 'undefined' != typeof image_stats.savings_conversion.bytes ? parseInt( wp_smushit_data.savings_conversion ) + parseInt( image_stats.savings_conversion.bytes ) : parseInt( wp_smushit_data.savings_conversion );
		} else if ( 'directory_smush' == type ) {
			//Increase smushed image count
			wp_smushit_data.count_images = parseInt( wp_smushit_data.count_images ) + 1;
		} else if ( 'nextgen' == type ) {
			wp_smushit_data.count_smushed = parseInt( wp_smushit_data.count_smushed ) + 1;
			wp_smushit_data.count_supersmushed = parseInt( wp_smushit_data.count_supersmushed ) + 1;

			//Increase smushed image count
			wp_smushit_data.count_images = parseInt( wp_smushit_data.count_images ) + parseInt( image_stats.count );
		}

		//If we have savings
		if ( image_stats.size_before > image_stats.size_after ) {
			//Update savings
			wp_smushit_data.size_before = 'undefined' != typeof image_stats.size_before ? parseInt( wp_smushit_data.size_before ) + parseInt( image_stats.size_before ) : parseInt( wp_smushit_data.size_before );
			wp_smushit_data.size_after = 'undefined' != typeof image_stats.size_after ? parseInt( wp_smushit_data.size_after ) + parseInt( image_stats.size_after ) : parseInt( wp_smushit_data.size_after );
		}
		//Add stats for resizing
		if ( 'undefined' != typeof image_stats.savings_resize ) {
			//Update savings
			wp_smushit_data.size_before = 'undefined' != typeof image_stats.savings_resize.size_before ? parseInt( wp_smushit_data.size_before ) + parseInt( image_stats.savings_resize.size_before ) : parseInt( wp_smushit_data.size_before );
			wp_smushit_data.size_after = 'undefined' != typeof image_stats.savings_resize.size_after ? parseInt( wp_smushit_data.size_after ) + parseInt( image_stats.savings_resize.size_after ) : parseInt( wp_smushit_data.size_after );
		}
		//Add stats for Conversion
		if ( 'undefined' != typeof image_stats.savings_conversion ) {
			//Update savings
			wp_smushit_data.size_before = 'undefined' != typeof image_stats.savings_conversion.size_before ? parseInt( wp_smushit_data.size_before ) + parseInt( image_stats.savings_conversion.size_before ) : parseInt( wp_smushit_data.size_before );
			wp_smushit_data.size_after = 'undefined' != typeof image_stats.savings_conversion.size_after ? parseInt( wp_smushit_data.size_after ) + parseInt( image_stats.savings_conversion.size_after ) : parseInt( wp_smushit_data.size_after );
		}
	};

	/**
	 * Update all stats sections based on the response.
	 *
	 * @param scan_type Current scan type.
	 */
	var update_stats = function ( scan_type ) {

		var super_savings = 0,
			smushed_count = 0;
		var is_nextgen = 'undefined' != typeof scan_type && 'nextgen' == scan_type;

		//Calculate updated savings in bytes
		wp_smushit_data.savings_bytes = parseInt( wp_smushit_data.size_before ) - parseInt( wp_smushit_data.size_after );

		var formatted_size = formatBytes( wp_smushit_data.savings_bytes, 1 );

		if ( is_nextgen ) {
			$( '.wp-smush-savings .wp-smush-stats-human' ).html( formatted_size );
		} else {
			$( '.wp-smush-savings .wp-smush-stats-human' ).html( getFormatFromString( formatted_size ) );
			$( '.sui-summary-large.wp-smush-stats-human' ).html( getSizeFromString( formatted_size ) );
		}

		//Update the savings percent
		wp_smushit_data.savings_percent = precise_round( ( parseInt( wp_smushit_data.savings_bytes ) / parseInt( wp_smushit_data.size_before ) ) * 100, 1 );
		if ( !isNaN( wp_smushit_data.savings_percent ) ) {
			$( '.wp-smush-savings .wp-smush-stats-percent' ).html( wp_smushit_data.savings_percent );
		}

		//Update Savings in share message
		//$( 'span.smush-share-savings' ).html( formatBytes( wp_smushit_data.savings_bytes, 1 ) );

		//Update Smush percent
		wp_smushit_data.smush_percent = precise_round( ( parseInt( wp_smushit_data.count_smushed ) / parseInt( wp_smushit_data.count_total ) ) * 100, 1 );
		$( 'span.wp-smush-images-percent' ).html( wp_smushit_data.smush_percent );

		//Super-Smush Savings
		if ( 'undefined' != typeof wp_smushit_data.savings_bytes && 'undefined' != typeof wp_smushit_data.savings_resize ) {
			super_savings = parseInt( wp_smushit_data.savings_bytes ) - parseInt( wp_smushit_data.savings_resize );
			if ( super_savings > 0 ) {
				$( 'li.super-smush-attachments span.smushed-savings' ).html( formatBytes( super_savings, 1 ) );
			}
		}

		//Update Image count
		if ( is_nextgen ) {
			$( '.sui-summary-details span.wp-smush-total-optimised' ).html( wp_smushit_data.count_images );
		} else {
			$( 'span.smushed-items-count span.wp-smush-count-total span.wp-smush-total-optimised' ).html( wp_smushit_data.count_images );
		}

		//Update image count in share message
		//$( 'span.smush-share-image-count' ).html( wp_smushit_data.count_images );

		//Update Resize image count
		$( 'span.smushed-items-count span.wp-smush-count-resize-total span.wp-smush-total-optimised' ).html( wp_smushit_data.count_resize );

		//Update Attachment image count
		//$( 'div.wp-smush-count-attachment-total span.wp-smush-total-optimised' ).html( wp_smushit_data.count_smushed );

		//Update image count in share message

		//Update supersmushed image count
		if ( $( 'li.super-smush-attachments .smushed-count' ).length && 'undefined' != typeof wp_smushit_data.count_supersmushed ) {
			$( 'li.super-smush-attachments .smushed-count' ).html( wp_smushit_data.count_supersmushed );
		}

		//Update Conversion Savings
		var smush_conversion_savings = $( '.smush-conversion-savings' );
		if ( smush_conversion_savings.length > 0 && 'undefined' != typeof ( wp_smushit_data.savings_conversion ) && wp_smushit_data.savings_conversion != '' ) {
			var conversion_savings = smush_conversion_savings.find( '.wp-smush-stats' );
			if ( conversion_savings.length > 0 ) {
				conversion_savings.html( formatBytes( wp_smushit_data.savings_conversion, 1 ) );
			}
		}

		//Update Resize Savings
		var smush_resize_savings = $( '.smush-resize-savings' );
		if ( smush_resize_savings.length > 0 && 'undefined' != typeof ( wp_smushit_data.savings_resize ) && wp_smushit_data.savings_resize != '' ) {
			// Get the resize savings in number.
			var savings_value = parseInt( wp_smushit_data.savings_resize );
			var resize_savings = smush_resize_savings.find( '.wp-smush-stats' );
			var resize_message = smush_resize_savings.find( '.wp-smush-stats-label-message' );
			// Replace only if value is grater than 0.
			if ( savings_value > 0 && resize_savings.length > 0 ) {
				// Hide message.
				if ( resize_message.length > 0 ) {
					resize_message.hide();
				}
				resize_savings.html( formatBytes( wp_smushit_data.savings_resize, 1 ) );
			}
		}

		//Update pro Savings
		set_pro_savings();

		// Updating pro savings stats.
		if ( 'undefined' != typeof (wp_smushit_data.pro_savings) ) {
			// Pro stats section.
			var smush_pro_savings = $( '.smush-avg-pro-savings' );
			if ( smush_pro_savings.length > 0 ) {
				var pro_savings_percent = smush_pro_savings.find( '.wp-smush-stats-percent' );
				var pro_savings_bytes = smush_pro_savings.find( '.wp-smush-stats-human' );
				if ( pro_savings_percent.length > 0 && 'undefined' != typeof (wp_smushit_data.pro_savings.percent) && wp_smushit_data.pro_savings.percent != '' ) {
					pro_savings_percent.html( wp_smushit_data.pro_savings.percent );
				}
				if ( pro_savings_bytes.length > 0 && 'undefined' != typeof (wp_smushit_data.pro_savings.savings_bytes) && wp_smushit_data.pro_savings.savings_bytes != '' ) {
					pro_savings_bytes.html( wp_smushit_data.pro_savings.savings_bytes );
				}
			}
		}
	};

	//Finds y value of given object
	function findPos( obj ) {
		var curtop = 0;
		if ( obj.offsetParent ) {
			do {
				curtop += obj.offsetTop;
			} while ( obj = obj.offsetParent );
			return [curtop];
		}
	}

	// Scroll the element to top of the page.
	var goToByScroll = function ( selector ) {
		// Scroll if element found.
		if ( $( selector ).length > 0 ) {
			$( 'html, body' ).animate( {
					scrollTop: $( selector ).offset().top - 100
				}, 'slow'
			);
		}
	};

	var disable_buttons = function ( self ) {
		self.attr( 'disabled', 'disabled' );
		$( '.wp-smush-browse' ).attr( 'disabled', 'disabled' );
	};

	var update_cummulative_stats = function ( stats ) {
		//Update Directory Smush Stats
		if ( 'undefined' != typeof ( stats.dir_smush ) ) {
			var stats_human = $( 'li.smush-dir-savings span.wp-smush-stats span.wp-smush-stats-human' );
			var stats_percent = $( 'li.smush-dir-savings span.wp-smush-stats span.wp-smush-stats-percent' );

			// Do not replace if 0 savings.
			if ( stats.dir_smush.bytes > 0 ) {
				// Hide selector.
				$( 'li.smush-dir-savings .wp-smush-stats-label-message' ).hide();
				//Update Savings in bytes
				if ( stats_human.length > 0 ) {
					stats_human.html( stats.dir_smush.human );
				} else {
					var span = '<span class="wp-smush-stats-human">' + stats.dir_smush.bytes + '</span>';
				}

				//Percentage section
				if ( stats.dir_smush.percent > 0 ) {
					// Show size and percentage separator.
					$( 'li.smush-dir-savings span.wp-smush-stats span.wp-smush-stats-sep' ).removeClass( 'sui-hidden' );
					//Update Optimisation percentage
					if ( stats_percent.length > 0 ) {
						stats_percent.html( stats.dir_smush.percent + '%' );
					} else {
						var span = '<span class="wp-smush-stats-percent">' + stats.dir_smush.percent + '%' + '</span>';
					}
				}
			}
		}

		//Update Combined stats
		if ( 'undefined' != typeof ( stats.combined_stats ) && stats.combined_stats.length > 0 ) {
			var c_stats = stats.combined_stats;

			var smush_percent = ( c_stats.smushed / c_stats.total_count ) * 100;
			smush_percent = precise_round( smush_percent, 1 );

			//Smushed Percent
			if ( smush_percent ) {
				$( 'div.wp-smush-count-total span.wp-smush-images-percent' ).html( smush_percent );
			}
			//Update Total Attachment Count
			if ( c_stats.total_count ) {
				$( 'span.wp-smush-count-total span.wp-smush-total-optimised' ).html( c_stats.total_count );
			}
			//Update Savings and Percent
			if ( c_stats.savings ) {
				$( 'span.wp-smush-savings span.wp-smush-stats-human' ).html( c_stats.savings );
			}
			if ( c_stats.percent ) {
				$( 'span.wp-smush-savings span.wp-smush-stats-percent' ).html( c_stats.percent );
			}
		}
	};

	//Remove span tag from URL
	function removeSpan( url ) {
		var url = url.slice( url.indexOf( '?' ) + 1 ).split( '&' );
		for ( var i = 0; i < url.length; i++ ) {
			var urlparam = decodeURI( url[i] ).split( /=(.+)/ )[1];
			return urlparam.replace( /<(?:.|\n)*?>/gm, '' );
		}
	}

	/**
	 * Handle the Smush Stats link click
	 */
	$( 'body' ).on( 'click', 'a.smush-stats-details', function ( e ) {

		//If disabled
		if ( 'disabled' == $( this ).attr( 'disabled' ) ) {
			return false;
		}

		// prevent the default action
		e.preventDefault();
		//Replace the `+` with a `-`
		var slide_symbol = $( this ).find( '.stats-toggle' );
		$( this ).parents().eq( 1 ).find( '.smush-stats-wrapper' ).slideToggle();
		slide_symbol.text( slide_symbol.text() == '+' ? '-' : '+' );


	} );

	/** Handle smush button click **/
	$( 'body' ).on( 'click', '.wp-smush-send:not(.wp-smush-resmush)', function ( e ) {
		// prevent the default action
		e.preventDefault();
		new WP_Smush.Smush( $( this ), false );
	} );

	/** Handle NextGen Gallery smush button click **/
	$( 'body' ).on( 'click', '.wp-smush-nextgen-send', function ( e ) {
		// prevent the default action
		e.preventDefault();
		new WP_Smush.Smush( $( this ), false, 'nextgen' );
	} );

	/** Handle NextGen Gallery Bulk smush button click **/
	$( 'body' ).on( 'click', '.wp-smush-nextgen-bulk', function ( e ) {
		// prevent the default action
		e.preventDefault();

		//Check for ids, if there is none (Unsmushed or lossless), don't call smush function
		if ( typeof wp_smushit_data == 'undefined' ||
			( wp_smushit_data.unsmushed.length == 0 && wp_smushit_data.resmush.length == 0 )
		) {

			return false;

		}

		jQuery( '.wp-smush-button, .wp-smush-scan' ).attr( 'disabled', 'disabled' );
		$( ".wp-smush-notice.wp-smush-remaining" ).hide();
		new WP_Smush.Smush( $( this ), true, 'nextgen' );

	} );

	/** Restore: Media Library **/
	$( 'body' ).on( 'click', '.wp-smush-action.wp-smush-restore', function ( e ) {
		var current_button = $( this );
		var smush_action = 'smush_restore_image';
		process_smush_action( e, current_button, smush_action, 'restore' );
		//Change the class oa parent div ( Level 2 )
		var parent = current_button.parents().eq( 1 );
		if ( parent.hasClass( 'smushed' ) ) {
			parent.removeClass( 'smushed' ).addClass( 'unsmushed' );
		}
	} );

	/** Resmush: Media Library **/
	$( 'body' ).on( 'click', '.wp-smush-action.wp-smush-resmush', function ( e ) {
		var current_button = $( this );
		var smush_action = 'smush_resmush_image';
		process_smush_action( e, current_button, smush_action, 'smushing' );
	} );

	/** Restore: NextGen Gallery **/
	$( 'body' ).on( 'click', '.wp-smush-action.wp-smush-nextgen-restore', function ( e ) {
		var current_button = $( this );
		var smush_action = 'smush_restore_nextgen_image';
		process_smush_action( e, current_button, smush_action, 'restore' );
	} );

	/** Resmush: NextGen Gallery **/
	$( 'body' ).on( 'click', '.wp-smush-action.wp-smush-nextgen-resmush', function ( e ) {
		var current_button = $( this );
		var smush_action = 'smush_resmush_nextgen_image';
		process_smush_action( e, current_button, smush_action, 'smushing' );
	} );

	//Scan For resmushing images
	$( 'body' ).on( 'click', '.wp-smush-scan', function ( e ) {

		e.preventDefault();

		//Run the Re-check
		run_re_check( $( this ), false );
	} );

	//Dismiss Welcome notice
	//@todo: Use it for popup
	$( '#wp-smush-welcome-box .smush-dismiss-welcome' ).on( 'click', function ( e ) {
		e.preventDefault();
		var $el = $( this ).parents().eq( 1 );
		remove_element( $el );

		//Send a ajax request to save the dismissed notice option
		var param = {
			action: 'dismiss_welcome_notice'
		};
		$.post( ajaxurl, param );
	} );

	//Remove Notice
	$( 'body' ).on( 'click', '.wp-smush-notice .icon-fi-close', function ( e ) {
		e.preventDefault();
		var $el = $( this ).parent();
		remove_element( $el );
	} );

	//On Click Update Settings. Check for change in settings
	$( 'input#wp-smush-save-settings' ).on( 'click', function ( e ) {
		e.preventDefault();

		var setting_type = '';
		var setting_input = $( 'input[name="setting-type"]' );
		//Check if setting type is set in the form
		if ( setting_input.length > 0 ) {
			setting_type = setting_input.val();
		}

		//Show the spinner
		var self = $( this );
		self.parent().find( 'span.sui-icon-loader.sui-loading' ).removeClass( 'sui-hidden' );

		//Save settings if in network admin
		if ( '' != setting_type && 'network' == setting_type ) {
			//Ajax param
			var param = {
				action: 'save_settings',
				nonce: $( '#wp_smush_options_nonce' ).val()
			};

			param = jQuery.param( param ) + '&' + jQuery( 'form#wp-smush-settings-form' ).serialize();

			//Send ajax, Update Settings, And Check For resmush
			jQuery.post( ajaxurl, param ).done( function () {
				jQuery( 'form#wp-smush-settings-form' ).submit();
				return true;
			} );
		} else {
			//Check for all the settings, and scan for resmush
			var wrapper_div = self.parents().eq( 1 );

			//Get all the main settings
			var strip_exif = document.getElementById( "wp-smush-strip_exif" );
			var super_smush = document.getElementById( "wp-smush-lossy" );
			var smush_original = document.getElementById( "wp-smush-original" );
			var resize_images = document.getElementById( "wp-smush-resize" );
			var smush_pngjpg = document.getElementById( "wp-smush-png_to_jpg" );

			var update_button_txt = true;

			$( '.wp-smush-hex-notice' ).hide();

			//If Preserve Exif is Checked, and all other settings are off, just save the settings
			if ( ( strip_exif === null || !strip_exif.checked )
				&& ( super_smush === null || !super_smush.checked )
				&& ( smush_original === null || !smush_original.checked )
				&& ( resize_images === null || !resize_images.checked )
				&& ( smush_pngjpg === null || !smush_pngjpg.checked )
			) {
				update_button_txt = false;
			}

			//Update text
			self.attr( 'disabled', 'disabled' ).addClass( 'button-grey' );

			if ( update_button_txt ) {
				self.val( wp_smush_msgs.checking )
			}

			//Check if type is set in data attributes
			var scan_type = self.data( 'type' );
			scan_type = 'undefined' == typeof scan_type ? 'media' : scan_type;

			//Ajax param
			var param = {
				action: 'scan_for_resmush',
				wp_smush_options_nonce: jQuery( '#wp_smush_options_nonce' ).val(),
				scan_type: scan_type
			};

			param = jQuery.param( param ) + '&' + jQuery( 'form#wp-smush-settings-form' ).serialize();

			//Send ajax, Update Settings, And Check For resmush
			jQuery.post( ajaxurl, param ).done( function () {
				jQuery( 'form#wp-smush-settings-form' ).submit();
				return true;
			} );
		}
	} );

	// On Resmush click.
	$( 'body' ).on( 'click', '.wp-smush-skip-resmush', function ( e ) {
		e.preventDefault();
		var self = jQuery( this );
		var container = self.parents().eq( 1 );

		// Remove Parent div.
		var $el = self.parent();
		remove_element( $el );

		// Remove Settings Notice.
		$( '.sui-notice-top.sui-notice-success' ).remove();

		// Set button attribute to skip re-smush ids.
		container.find( '.wp-smush-all' ).attr( 'data-smush', 'skip_resmush' );

		// Update Smushed count.
		wp_smushit_data.count_smushed = parseInt( wp_smushit_data.count_smushed ) + wp_smushit_data.resmush.length;
		wp_smushit_data.count_supersmushed = parseInt( wp_smushit_data.count_supersmushed ) + wp_smushit_data.resmush.length;

		// Update Stats.
		if ( wp_smushit_data.count_smushed == wp_smushit_data.count_total ) {

			// Show all done notice.
			$( '.wp-smush-notice.wp-smush-all-done, .wp-smush-pagespeed-recommendation' ).show();

			// Hide Smush button.
			$( '.wp-smush-bulk-wrapper ' ).hide()

		}
		//Remove Re-Smush Notice
		$( '.wp-smush-resmush-notice' ).remove();

		var type = $( '.wp-smush-scan' ).data( 'type' );
		type = 'undefined' == typeof type ? 'media' : type;

		var smushed_count = 'undefined' != typeof wp_smushit_data.count_smushed ? wp_smushit_data.count_smushed : 0;

		var smush_percent = ( smushed_count / wp_smushit_data.count_total ) * 100;
		smush_percent = precise_round( smush_percent, 1 );

		$( '.wp-smush-images-percent' ).html( smush_percent );

		//Update the Progress Bar Width
		// get the progress bar
		var $progress_bar = jQuery( '.bulk-smush-wrapper .wp-smush-progress-inner' );
		if ( $progress_bar.length < 1 ) {
			return;
		}

		// increase progress
		$progress_bar.css( 'width', smush_percent + '%' );

		//Show the default bulk smush notice
		$( '.wp-smush-bulk-wrapper' ).show();
		$( '.wp-smush-bulk-wrapper .sui-notice' ).show();

		var params = {
			action: 'delete_resmush_list',
			type: type
		};
		//Delete resmush list, @todo: update stats from the ajax response
		$.post( ajaxurl, params, function ( res ) {
			//Remove the whole li element on success
			if ( res.success && 'undefined' != typeof res.data.stats ) {
				var stats = res.data.stats;
				//Update wp_smushit_data ( Smushed count, Smushed Percent, Image count, Super smush count, resize savings, conversion savings )
				if ( 'undefinied' != typeof wp_smushit_data ) {
					wp_smushit_data.count_images = 'undefined' != typeof stats.count_images ? parseInt( wp_smushit_data.count_images ) + stats.count_images : wp_smushit_data.count_images;
					wp_smushit_data.size_before = 'undefined' != typeof stats.size_before ? parseInt( wp_smushit_data.size_before ) + stats.size_before : wp_smushit_data.size_before;
					wp_smushit_data.size_after = 'undefined' != typeof stats.size_after ? parseInt( wp_smushit_data.size_after ) + stats.size_after : wp_smushit_data.size_after;
					wp_smushit_data.savings_resize = 'undefined' != typeof stats.savings_resize ? parseInt( wp_smushit_data.savings_resize ) + stats.savings_resize : wp_smushit_data.savings_resize;
					wp_smushit_data.savings_conversion = 'undefined' != typeof stats.savings_conversion ? parseInt( wp_smushit_data.savings_conversion ) + stats.savings_conversion : wp_smushit_data.savings_conversion;
					//Add directory smush stats
					if ( 'undefined' != typeof ( wp_smushit_data.savings_dir_smush ) && 'undefined' != typeof ( wp_smushit_data.savings_dir_smush.orig_size ) ) {
						wp_smushit_data.size_before = 'undefined' != typeof wp_smushit_data.savings_dir_smush ? parseInt( wp_smushit_data.size_before ) + parseInt( wp_smushit_data.savings_dir_smush.orig_size ) : wp_smushit_data.size_before;
						wp_smushit_data.size_after = 'undefined' != typeof wp_smushit_data.savings_dir_smush ? parseInt( wp_smushit_data.size_after ) + parseInt( wp_smushit_data.savings_dir_smush.image_size ) : wp_smushit_data.size_after;
					}

					wp_smushit_data.count_resize = 'undefined' != typeof stats.count_resize ? parseInt( wp_smushit_data.count_resize ) + stats.count_resize : wp_smushit_data.count_resize;
				}
				//Smush Notice
				if ( $( '.bulk-smush-wrapper .wp-smush-remaining-count' ).length && 'undefined' != typeof wp_smushit_data.unsmushed ) {
					$( '.bulk-smush-wrapper .wp-smush-remaining-count' ).html( wp_smushit_data.unsmushed.length );
				}
				update_stats();
			}
		} );

	} );

	/**
	 * Enable resize in settings and scroll.
	 */
	var scroll_and_enable_resize = function () {
		// Enable resize, show resize settings.
		$( '#wp-smush-resize' ).prop( 'checked', true ).focus();
		$( 'div.wp-smush-resize-settings-wrap' ).show();

		// Scroll down to settings area.
		goToByScroll( "#column-wp-smush-resize" );
	}

	/**
	 * Enable super smush in settings and scroll.
	 */
	var scroll_and_enable_lossy = function () {
		// Enable super smush.
		$( '#wp-smush-lossy' ).prop( 'checked', true ).focus();

		// Scroll down to settings area.
		goToByScroll( "#column-wp-smush-lossy" );
	}

	// Enable super smush on clicking link from stats area.
	$( 'a.wp-smush-lossy-enable' ).on( 'click', function ( e ) {
		e.preventDefault();

		scroll_and_enable_lossy();
	} );

	// Enable resize on clicking link from stats area.
	$( '.wp-smush-resize-enable' ).on( 'click', function ( e ) {
		e.preventDefault();

		scroll_and_enable_resize();
	} );

	// If settings string is found in url, enable and scroll.
	if ( window.location.hash ) {
		var setting_hash = window.location.hash.substring( 1 );
		// Enable and scroll to resize settings.
		if ( 'enable-resize' === setting_hash ) {
			scroll_and_enable_resize();
		} else if ( 'enable-lossy' === setting_hash ) {
			// Enable and scroll to lossy settings.
			scroll_and_enable_lossy();
		}
	}

	//Trigger Bulk
	$( 'body' ).on( 'click', '.wp-smush-trigger-bulk', function ( e ) {
		e.preventDefault();
		//Induce Setting button save click
		$( 'button.wp-smush-all' ).click();
		$( 'span.sui-notice-dismiss' ).click();
	} );

	//Allow the checkboxes to be Keyboard Accessible
	$( '.wp-smush-setting-row .toggle-checkbox' ).focus( function () {
		//If Space is pressed
		$( this ).keypress( function ( e ) {
			if ( e.keyCode == 32 ) {
				e.preventDefault();
				$( this ).find( '.toggle-checkbox' ).click();
			}
		} );
	} );

	// Re-Validate Resize Width And Height.
	$( 'body' ).on( 'blur', '.wp-smush-resize-input', function () {

		var self = $( this );

		var wrapper_div = self.parents().eq( 4 );

		// Initiate the check.
		validate_resize_settings( wrapper_div, false, false ); // run the validation.
	} );

	// Handle Resize Checkbox toggle, to show/hide width, height settings.
	$( 'body' ).on( 'click', '#wp-smush-resize, #wp-smush-resize-quick-setup', function () {
		var self = $( this );
		var settings_wrap = $( '.wp-smush-resize-settings-wrap' );

		if ( self.is( ':checked' ) ) {
			settings_wrap.show();
		} else {
			settings_wrap.hide();
		}
	} );

	// Handle Automatic Smush Checkbox toggle, to show/hide image size settings.
	$( 'body' ).on( 'click', '#wp-smush-auto', function () {
		var self = $( this );
		var settings_wrap = $( '.wp-smush-image-size-list' );

		if ( self.is( ':checked' ) ) {
			settings_wrap.show();
		} else {
			settings_wrap.hide();
		}
	} );

	// Handle auto detect checkbox toggle, to show/hide highlighting notice.
	$( 'body' ).on( 'click', '#wp-smush-detection', function () {
		var self = $( this );
		var notice_wrap = $( '.smush-highlighting-notice' );

		if ( self.is( ':checked' ) ) {
			notice_wrap.show();
		} else {
			notice_wrap.hide();
		}
	} );

	// Handle PNG to JPG Checkbox toggle, to show/hide Transparent image conversion settings.
	$( '#wp-smush-png_to_jpg' ).click( function () {
		var self = $( this );
		var settings_wrap = $( '.wp-smush-png_to_jpg-wrap' );

		if ( self.is( ':checked' ) ) {
			settings_wrap.show();
		} else {
			settings_wrap.hide();
		}
	} );

	//Handle, Change event in Enable Networkwide settings
	$( '#wp-smush-networkwide' ).on( 'click', function ( e ) {
		if ( $( this ).is( ':checked' ) ) {
			$( '.network-settings-wrapper' ).show();
			$( '.sui-vertical-tabs li' ).not( '.smush-bulk' ).each( function ( n ) {
				$( this ).removeClass( 'sui-hidden' );
			} );
		} else {
			$( '.network-settings-wrapper' ).hide();
			$( '.sui-vertical-tabs li' ).not( '.smush-bulk' ).each( function ( n ) {
				$( this ).addClass( 'sui-hidden' );
			} );
		}
	} );

	//Handle Re-check button functionality
	$( "#wp-smush-revalidate-member" ).on( 'click', function ( e ) {
		e.preventDefault();
		//Ajax Params
		var params = {
			action: 'smush_show_warning',
		};
		var link = $( this );
		var parent = link.parents().eq( 1 );
		parent.addClass( 'loading-notice' );
		$.get( ajaxurl, params, function ( r ) {
			//remove the warning
			parent.removeClass( 'loading-notice' ).addClass( "loaded-notice" );
			if ( 0 == r ) {
				parent.attr( 'data-message', wp_smush_msgs.membership_valid );
				remove_element( parent, 1000 );
			} else {
				parent.attr( 'data-message', wp_smush_msgs.membership_invalid );
				setTimeout( function remove_loader() {
					parent.removeClass( 'loaded-notice' );
				}, 1000 )
			}
		} );
	} );

	//Initiate Re-check if the variable is set
	if ( 'undefined' != typeof (wp_smush_run_re_check) && 1 == wp_smush_run_re_check && $( '.wp-smush-scan' ).length > 0 ) {
		//Run the Re-check
		run_re_check( $( '.wp-smush-scan' ), false );
	}

	if ( $( 'li.smush-dir-savings' ).length > 0 ) {
		//Update Directory Smush, as soon as the page loads
		var stats_param = {
			action: 'get_dir_smush_stats'
		};
		$.get( ajaxurl, stats_param, function ( r ) {

			//Hide the spinner
			$( 'li.smush-dir-savings .sui-icon-loader' ).hide();

			//If there are no errors, and we have a message to display
			if ( !r.success && 'undefined' != typeof ( r.data.message ) ) {
				$( 'div.wp-smush-scan-result div.content' ).prepend( r.data.message );
				return;
			}

			//If there is no value in r
			if ( 'undefined' == typeof ( r.data) || 'undefined' == typeof ( r.data.dir_smush ) ) {
				//Append the text
				$( 'li.smush-dir-savings span.wp-smush-stats' ).append( wp_smush_msgs.ajax_error );
				$( 'li.smush-dir-savings span.wp-smush-stats span' ).hide();

			} else {
				//Update the stats
				update_cummulative_stats( r.data );
			}

		} );
	}
	//Close Directory smush modal, if pressed esc
	$( document ).keyup( function ( e ) {
		if ( e.keyCode === 27 ) {
			var modal = $( 'div.dev-overlay.wp-smush-list-dialog, div.dev-overlay.wp-smush-get-pro' );
			//If the Directory dialog is not visible
			if ( !modal.is( ':visible' ) ) {
				return;
			}
			modal.find( 'div.close' ).click();

		}
	} );

	//Dismiss Smush recommendation
	$( 'span.dismiss-recommendation' ).on( 'click', function ( e ) {
		e.preventDefault();
		var parent = $( this ).parent();
		//remove div and save preference in db
		parent.hide( 'slow', function () {
			parent.remove();
		} );
		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				'action': 'hide_pagespeed_suggestion'
			}
		} );
	} )

	//Remove API message
	$( 'div.wp-smush-api-message i.icon-fi-close' ).on( 'click', function ( e ) {
		e.preventDefault();
		var parent = $( this ).parent();
		//remove div and save preference in db
		parent.hide( 'slow', function () {
			parent.remove();
		} );
		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				'action': 'hide_api_message'
			}
		} );
	} );

} );
(function ( $ ) {
	var Smush = function ( element, options ) {
		var elem = $( element );

		var defaults = {
			isSingle: false,
			ajaxurl: '',
			msgs: {},
			msgClass: 'wp-smush-msg',
			ids: []
		};
	};
	$.fn.wpsmush = function ( options ) {
		return this.each( function () {
			var element = $( this );

			// Return early if this element already has a plugin instance
			if ( element.data( 'wpsmush' ) )
				return;

			// pass options to plugin constructor and create a new instance
			var wpsmush = new Smush( this, options );

			// Store plugin object in this element's data
			element.data( 'wpsmush', wpsmush );
		} );
	};

})( jQuery );