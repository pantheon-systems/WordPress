(function( $, _ ) {

	/**
	 * The object that handles the tool processing modal
	 */
	as3cfpro.tool = {

		/**
		 * Tool identifier used by AJAX hooks
		 *
		 * {string}
		 */
		ID: '',

		/**
		 * Timer tick
		 *
		 * {int}
		 */
		timerCount: 0,

		/**
		 * Current interval start in seconds since UNIX epoch.
		 *
		 * {int}
		 */
		intervalStart: 0,

		/**
		 * Elapsed interval
		 *
		 * {number}
		 */
		elapsedInterval: 0,

		/**
		 * HTML counter display
		 *
		 * {object}
		 */
		$counterDisplay: '',

		/**
		 * Holds the name of the next method to run and its arguments
		 *
		 * {object|bool}
		 */
		nextStepInProcess: false,

		/**
		 * Is an AJAX request happening
		 *
		 * {bool}
		 */
		doingAjax: false,

		/**
		 * Is the process paused
		 *
		 * {bool}
		 */
		processPaused: false,

		/**
		 * Have any non fatal errors occurred
		 *
		 * {bool}
		 */
		nonFatalErrors: false,

		/**
		 * Has a fatal process error happened, eg. AJAX
		 *
		 * {bool}
		 */
		processError: false,

		/**
		 * Is the process complete
		 *
		 * {bool}
		 */
		processCompleted: false,

		/**
		 * Has the process been cancelled
		 *
		 * {bool}
		 */
		processCancelled: false,

		/**
		 * Is the process happening
		 *
		 * {bool}
		 */
		currentlyProcessing: false,

		/**
		 * Have all the items been processed
		 *
		 * {bool}
		 */
		itemsProcessed: false,

		/**
		 * Is the main modal active
		 *
		 * {bool}
		 */
		progressModalActive: false,

		/**
		 * Percentage of process
		 *
		 * {number}
		 */
		progressPercent: 0,

		/**
		 * Bytes processed
		 *
		 * {number}
		 */
		progressBytes: 0,

		/**
		 * Total bytes to process
		 *
		 * {number}
		 */
		progressTotalBytes: 0,

		/**
		 * Amount of items processed
		 *
		 * {number}
		 */
		progressCount: 0,

		/**
		 * Total of items processed
		 *
		 * {number}
		 */
		progressTotalCount: 0,

		/**
		 * Height of modal in window
		 *
		 * {number}
		 */
		contentHeight: 0,

		/**
		 * HTML of open modal
		 *
		 * {object|bool}
		 */
		$progressContent: false,

		/**
		 * Copy of modal
		 *
		 * {object|bool}
		 */
		$progressContentOriginal: false,

		/**
		 * Open the tool modal
		 *
		 * @param {number} id
		 */
		open: function( id ) {
			this.ID = id;
			as3cfpro.tool.openModal();
		},

		/**
		 * Trigger a tool opening from a settings page URL
		 * eg. wp-admin/admin.php?page=amazon-s3-and-cloudfront&tool={tool_key}
		 */
		openFromURL: function() {
			var page_url = window.location.search.substring( 1 );
			var query_string = page_url.split( '&' );

			for ( var i = 0; i < query_string.length; i++ ) {
				var param_name = query_string[ i ].split( '=' );

				if ( 'tool' === param_name[ 0 ] ) {
					this.open( param_name[ 1 ] );

					return;
				}
			}
		},

		/**
		 * Clone and store the modal views
		 */
		cloneViews: function() {
			this.$progressContentOriginal = $( '.progress-content' ).clone();

			$( '.progress-content' ).remove();
		},

		/**
		 * Animate the progress bars for all sidebar tool blocks to the desired widths
		 */
		animateProgressBars: function() {
			$( '.as3cf-sidebar.pro .block .progress-bar-wrapper .progress-bar' ).each( function() {
				$( this ).animate( {
					width: $( this ).parent().data( 'percentage' ) + '%'
				}, 1200 );
			} );
		},

		/**
		 * Open the Tool modal
		 */
		openModal: function() {
			var docHeight = $( document ).height();

			$( 'body' ).append( '<div id="overlay"></div>' );

			$( '#overlay' )
				.height( docHeight )
				.css( {
					'position': 'fixed',
					'top': 0,
					'left': 0,
					'width': '100%',
					'z-index': 99999,
					'display': 'none'
				} );

			this.showProgressModal();
			this.init();

			$( '#overlay' ).show();
		},

		/**
		 * Start the Tool process
		 */
		init: function() {
			var self = this;

			this.currentlyProcessing = true;
			this.doingAjax = true;

			as3cfpro.Sidebar.setToolAsProcessing( this.ID );

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				cache: false,
				data: {
					action: this.getAjaxAction( 'initiate' ),
					nonce: this.getAjaxNonce( 'initiate' )
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					self.ajaxError( jqXHR, textStatus, errorThrown );

					return;
				},
				success: function( data ) {
					if ( self.isError( data ) ) {
						return;
					}

					var progress = {
						bytes: 0,
						files: 0,
						total_done: 0,
						total_bytes: 0,
						total_files: 0
					};

					if ( 'undefined' !== typeof data.progress ) {
						_.extend( progress, data.progress );
					}

					self.nextStepInProcess = { fn: self.calculateItemsRecursive, args: [ data.blogs, progress ] };
					self.executeNextStep();
				}
			} );
		},

		/**
		 * Show the main progress modal
		 */
		showProgressModal: function() {
			this.$progressContent = this.$progressContentOriginal.clone();

			this.$progressContent.find( 'h2.progress-title' ).html( this.getString( 'tool_title' ) );

			$( '#overlay' ).after( this.$progressContent );

			this.$progressContent.find( '.completed-control' ).hide();
			this.contentHeight = this.$progressContent.outerHeight();
			this.$progressContent.css( 'top', '-' + this.contentHeight + 'px' ).show().animate( { 'top': '0px' } );
			this.progressModalActive = true;
			this.showSpinner();
			this.setupCounter();
		},

		/**
		 * Cancel the Tool process
		 */
		cancel: function() {
			this.processCancelled = true;
			this.processPaused = false;
			this.disableElements( '.upload-control' );
			$( '.progress-text' ).html( this.getString( 'completing_current_request' ) );
			this.showSpinner();

			if ( false === this.doingAjax ) {
				this.executeNextStep();
			}
		},

		/**
		 * Helper to pad a string with another string
		 *
		 * @param {string} n
		 * @param {number} width
		 * @param {string} z
		 * @returns {*}
		 */
		pad: function( n, width, z ) {
			z = z || '0';
			n = n + '';
			return n.length >= width ? n : new Array( width - n.length + 1 ).join( z ) + n;
		},

		/**
		 * Initialize the counter display
		 */
		setupCounter: function() {
			this.timerCount = 0;
			this.intervalStart = Date.now() / 1000;
			this.$counterDisplay = $( '.timer' );
			this.elapsedInterval = setInterval( this.count, 1000 );
		},

		/**
		 * Increment the counter
		 */
		count: function() {
			var self = as3cfpro.tool;

			// Add current interval length to total second count.
			self.timerCount = Math.round( self.timerCount + ( Date.now() / 1000 ) - self.intervalStart );
			self.intervalStart = Date.now() / 1000;
			self.displayCount();
		},

		/**
		 * Render the counter display
		 */
		displayCount: function() {
			var hours = Math.floor( this.timerCount / 3600 ) % 24;
			var minutes = Math.floor( this.timerCount / 60 ) % 60;
			var seconds = this.timerCount % 60;
			var display = this.pad( hours, 2, 0 ) + ':' + this.pad( minutes, 2, 0 ) + ':' + this.pad( seconds, 2, 0 );

			this.$counterDisplay.html( display );
		},

		/**
		 * Format a number with comma thousand separator
		 *
		 * @param {number} number
		 * @returns {string}
		 */
		numberFormat: function( number ) {
			number += '';
			var x = number.split( '.' );
			var x1 = x[ 0 ];
			var x2 = x.length > 1 ? '.' + x[ 1 ] : '';
			var rgx = /(\d+)(\d{3})/;
			while ( rgx.test( x1 ) ) {
				x1 = x1.replace( rgx, '$1' + ',' + '$2' );
			}

			return x1 + x2;
		},

		/**
		 * Helper to format bytes
		 *
		 * @param {number} bytes
		 * @returns {string}
		 */
		sizeFormat: function( bytes ) {
			var thresh = 1024;
			var units = [ 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' ];

			if ( bytes < thresh ) {
				return bytes + ' B';
			}

			var unitsKey = -1;

			do {
				bytes = bytes / thresh;
				unitsKey++;
			} while ( bytes >= thresh );

			return bytes.toFixed( 1 ) + ' ' + units[ unitsKey ];
		},

		/**
		 * Pause and Resume the progress
		 *
		 * @param {object} event
		 */
		setPauseResumeButton: function( event ) {
			if ( true === this.processPaused ) {
				this.processPaused = false;
				this.doingAjax = true;
				this.showSpinner();
				$( '.pause-resume' ).html( this.getString( 'pause' ) );
				// Resume the timer
				this.intervalStart = Date.now() / 1000;
				this.elapsedInterval = setInterval( this.count, 1000 );
				this.executeNextStep();
			} else {
				this.processPaused = true;
				this.doingAjax = false;
				this.disableElements( '.upload-control' );
			}

			this.updateProgressMessage();
		},

		/**
		 * Update the progress bar
		 *
		 * @param {number} amount
		 * @param {number} total
		 * @param {number} bytesSent
		 * @param {number} bytesToSend
		 */
		updateProgress: function( amount, total, bytesSent, bytesToSend ) {
			this.progressBytes = bytesSent;
			this.progressTotalBytes = bytesToSend;
			this.progressCount = amount;
			this.progressTotalCount = total;

			// If file size available, use for percentage, else use file count
			if ( bytesToSend > 0 ) {
				this.progressPercent = Math.round( 100 * bytesSent / bytesToSend );
			} else {
				this.progressPercent = Math.round( 100 * this.progressCount / this.progressTotalCount );
			}

			$( '.progress-content .progress-bar' ).width( this.progressPercent + '%' );

			this.updateProgressMessage();
		},

		/**
		 * Update the text under the progress bar
		 */
		updateProgressMessage: function() {
			var uploadProgress = this.getString( 'files_processed' );
			var progressMessage = this.progressPercent + '% ' + this.getString( 'complete' );
			var bytesMessage = '';

			if ( this.progressTotalBytes > 0 && as3cfpro.settings.tools[ this.ID ].show_file_size ) {
				bytesMessage = '(' + this.sizeFormat( this.progressBytes ) + ' / ' + this.sizeFormat( this.progressTotalBytes ) + ')';
			}

			$( '.upload-progress' ).html(
				uploadProgress
					.replace( '%1$d', this.numberFormat( this.progressCount ) )
					.replace( '%2$d', this.numberFormat( this.progressTotalCount ) )
			);

			if ( false === this.processPaused ) {
				$( '.progress-text' ).html( progressMessage + ' ' + bytesMessage );
			} else {
				$( '.progress-text' ).html( this.getString( 'completing_current_request' ) );
			}
		},

		/**
		 * Get the tool specific string
		 *
		 * @param {string} name
		 * @returns {string}
		 */
		getString: function( name ) {
			if ( undefined !== as3cfpro.strings[ name ] ) {
				return as3cfpro.strings[ name ];
			}

			if ( undefined !== as3cfpro.strings.tools[ this.ID ][ name ] ) {
				return as3cfpro.strings.tools[ this.ID ][ name ];
			}

			return '';
		},

		/**
		 * Get the AJAX action
		 *
		 * @param {string} action
		 * @returns {string}
		 */
		getAjaxAction: function( action ) {
			return 'as3cfpro_' + action + '_' + this.ID;
		},

		/**
		 * Get the AJAX nonce
		 *
		 * @param {string} action
		 * @returns {string}
		 */
		getAjaxNonce: function( action ) {
			return as3cfpro.nonces[ action + '_' + this.ID ];
		},

		/**
		 * Refresh the media to upload notice
		 */
		updateSidebar: function() {
			var self = this;

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				cache: false,
				data: {
					action: 'as3cfpro_update_sidebar',
					nonce: as3cfpro.nonces.update_sidebar,
					tool: self.ID
				},
				success: function( response ) {
					if ( true !== response.success || 'undefined' === typeof response.data ) {
						return;
					}

					$.each( response.data, function( index, value ) {
						if ( 'undefined' === typeof value.block ) {
							return;
						}

						self.renderSidebarBlock( index, value.block );
						self.renderErrorNotices( index, value.notices );
					} );

					self.showSidebarBlocks();
				}
			} );
		},

		/**
		 * Render sidebar block.
		 *
		 * @param {string} id
		 * @param {string} html
		 */
		renderSidebarBlock: function( id, html ) {
			var $sidebar = $( '.as3cf-sidebar.pro' );

			$sidebar.find( '#' + id ).remove();

			if ( html.length ) {
				$sidebar.append( html );
			}

			if ( $( html ).find( '.pie-chart' ).length ) {
				as3cfpro.Sidebar.renderPieChart();
			}
		},

		/**
		 * Show sidebar blocks.
		 */
		showSidebarBlocks: function() {
			var $sidebar = $( '.as3cf-sidebar.pro' );
			var $blocks = $sidebar.find( '.block' );

			$blocks.sort( function( a, b ) {
				return a.getAttribute( 'data-priority' ) - b.getAttribute( 'data-priority' );
			} );

			$blocks.detach().appendTo( $sidebar );

			var tab = $( '.as3cf-main .nav-tab.nav-tab-active' ).data( 'tab' );

			$blocks.filter( '[data-tab="' + tab + '"]' ).show();
		},

		/**
		 * Render error notices.
		 *
		 * @param {string} id
		 * @param {bool|object} notices
		 */
		renderErrorNotices: function( id, notices ) {
			if ( 'undefined' === typeof notices || false === notices ) {
				return;
			}

			var $notice = $( '#' + as3cfpro.settings.errors_key_prefix + id );
			var tab = $( '#' + this.ID ).data( 'tab' );

			if ( $notice.length ) {
				$notice.remove();
			}

			$.each( notices, function( index, notice ) {
				$( '#tab-' + tab ).prepend( notice );
			} );
		},

		/**
		 * Recursively calculate total items
		 *
		 * @param {object} blogs
		 * @param {object} progress
		 */
		calculateItemsRecursive: function( blogs, progress ) {
			var self = as3cfpro.tool;

			// All blogs processed
			if ( _.isEmpty( blogs ) ) {
				self.updateProgress( progress.files, progress.total_files, progress.bytes, progress.total_bytes );

				self.nextStepInProcess = { fn: self.processItemRecursive, args: [ progress ] };
				self.executeNextStep();
				return;
			}

			var blogsToSend = as3cfpro.tool.sliceObject( blogs, 0, 500 );
			var blogsToHold = as3cfpro.tool.sliceObject( blogs, 500 );
			progress.more_blogs = Object.keys( blogsToHold ).length;

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				cache: false,
				data: {
					action: self.getAjaxAction( 'calculate_items' ),
					blogs: blogsToSend,
					progress: self.prepareProgressForPost( progress ),
					nonce: self.getAjaxNonce( 'calculate_items' )
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					self.ajaxError( jqXHR, textStatus, errorThrown );

					return;
				},
				success: function( data ) {
					if ( self.isError( data ) ) {
						return;
					}

					if ( false === _.isEmpty( blogsToHold ) ) {
						data.blogs = _.extend( data.blogs, blogsToHold );
					}

					self.nextStepInProcess = { fn: self.calculateItemsRecursive, args: [ data.blogs, data.progress ] };
					self.executeNextStep();
				}

			} );
		},

		/**
		 * Recursively process items
		 *
		 * @param progress
		 */
		processItemRecursive: function( progress ) {
			var self = as3cfpro.tool;

			if ( progress.files >= progress.total_files ) {
				// Finalise process
				self.itemsProcessed = true;
				self.nextStepInProcess = { fn: self.processComplete, args: [ progress ] };
				self.executeNextStep();

				return;
			}

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				cache: false,
				data: {
					action: self.getAjaxAction( 'process_items' ),
					progress: self.prepareProgressForPost( progress ),
					nonce: self.getAjaxNonce( 'process_items' )
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					self.ajaxError( jqXHR, textStatus, errorThrown );

					return;
				},
				success: function( data ) {
					if ( self.isError( data ) ) {
						return;
					}

					self.updateNonFatalErrors( data );
					self.updateProgress( data.total_done, data.total_files, data.bytes, data.total_bytes );

					self.nextStepInProcess = { fn: self.processItemRecursive, args: [ data ] };
					self.executeNextStep();
				}

			} );
		},

		/**
		 * Run server side events on completion of tool
		 */
		processCompleteEvents: function() {
			var self = this;

			if ( false === this.processError ) {
				if ( true === this.nonFatalErrors ) {
					$( '.progress-text' ).addClass( 'upload-error' );
					var message = this.getString( 'completed_with_some_errors' );

					if ( true === this.processCancelled ) {
						message = this.getString( 'partial_complete_with_some_errors' );
					}

					$( '.progress-text' ).html( message );
				}
			}

			// Reset upload variables so consecutive uploads work correctly
			this.processError = false;
			this.currentlyProcessing = false;
			this.processCompleted = true;
			this.processPaused = false;
			this.processCancelled = false;
			this.doingAjax = false;
			this.nonFatalErrors = false;

			self.transitionActiveControls( '.completed-control' );
			$( '.progress-label' ).remove();
			this.hideSpinner();
			$( '.close-progress-content' ).show();
			$( '#overlay' ).css( 'cursor', 'pointer' );
			clearInterval( this.elapsedInterval );

			as3cfpro.Sidebar.setToolAsIdle( this.ID );

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				cache: false,
				data: {
					action: this.getAjaxAction( 'finish' ),
					nonce: this.getAjaxNonce( 'finish' ),
					completed: this.itemsProcessed
				},
				success: function() {
					self.updateSidebar();
				}
			} );

			this.itemsProcessed = false;
		},

		/**
		 * Complete the process
		 */
		processComplete: function() {
			var self = as3cfpro.tool;
			self.transitionActiveControls( '.completed-control' );

			self.currentlyProcessing = false;

			if ( false === self.processError ) {
				$( '.progress-text' ).append( '<div class="dashicons dashicons-yes"></div>' );
				self.processCompleteEvents();
			}
		},

		/**
		 * Set the visible controls with a nice transition.
		 *
		 * This fades out any controls which are currently active, but are not in the new selection of active controls.
		 * Any controls to be activated are faded in only after all previous animations are complete.
		 *
		 * Active controls are transitioned only when there is a change in active state,
		 * which is stored as `data` on the element.
		 *
		 * @param {string} activeSelectors All control selectors to be shown.
		 */
		transitionActiveControls: function( activeSelectors ) {
			var controls = as3cfpro.tool.$progressContent.find( '.controls-row' );

			// Bail if the controls to show are already active
			if ( controls.data( 'active-controls' ) === controls ) {
				return;
			} else {
				controls.data( 'active-controls', controls );
			}

			$.when( controls.find( '.control' ).not( activeSelectors ).fadeOut() ).done( function() {
				controls.find( activeSelectors ).fadeIn();
			} );
		},

		/**
		 * Execute the next method
		 */
		executeNextStep: function() {
			var self = as3cfpro.tool;

			if ( true === self.processPaused ) {
				this.hideSpinner();
				// Pause the timer
				clearInterval( self.elapsedInterval );
				$( '.progress-text' ).html( self.getString( 'paused' ) );
				$( '.pause-resume' ).html( self.getString( 'resume' ) );
				this.enableElements( '.upload-control' );
			} else if ( true === self.processCancelled ) {
				$( '.progress-text' ).html( self.getString( 'process_cancelled' ) );
				self.processCompleteEvents();
			} else {
				self.nextStepInProcess.fn.apply( null, self.nextStepInProcess.args );
			}
		},

		/**
		 * Output and log an AJAX error
		 *
		 * @param {object} jqXHR
		 * @param {object} textStatus
		 * @param {string} errorThrown
		 */
		ajaxError: function( jqXHR, textStatus, errorThrown ) {
			var self = as3cfpro.tool;
			$( '.progress-title' ).html( self.getString( 'process_failed' ) );
			$( '.progress-text' ).not( '.media' ).html( jqXHR.responseText );
			$( '.progress-text' ).not( '.media' ).addClass( 'upload-error' );
			console.log( jqXHR );
			console.log( textStatus );
			console.log( errorThrown );
			self.processError = true;
			self.processCompleteEvents();
			self.doingAjax = false;
		},

		/**
		 * Perform common actions on error and display message
		 *
		 * @param {string} error
		 */
		returnError: function( error ) {
			var self = as3cfpro.tool;
			self.processError = true;
			self.processCompleteEvents();
			$( '.progress-title' ).html( self.getString( 'process_failed' ) );
			$( '.progress-text' ).addClass( 'upload-error' );
			$( '.progress-text' ).html( error );
			$( '.upload-control' ).fadeOut();
			self.doingAjax = false;
		},

		/**
		 * Check for certain errors from our init method
		 *
		 * @param {object} data
		 *
		 * @returns {boolean}
		 */
		isError: function( data ) {
			if ( 'undefined' !== typeof data.as3cfpro_error && 1 === data.as3cfpro_error ) {
				this.returnError( data.body );

				return true;
			}

			if ( 'undefined' !== typeof data.success && false === data.success ) {
				this.returnError( data.data );
				this.updateNonFatalErrors( data );

				return true;
			}

			return false;
		},

		/**
		 * Update the modals error list with non fatals
		 *
		 * @param {object} data
		 */
		updateNonFatalErrors: function( data ) {
			if ( 'undefined' !== typeof data.errors ) {
				var $errorCount = $( '.progress-errors-title .error-count' );

				if ( 'string' === typeof data.errorsHtml ) {
					$( '.progress-errors .progress-errors-detail' ).html( data.errorsHtml );
				}

				if ( data.error_count > 0 ) {
					this.$progressContent.find( '.progress-errors-title' ).show();
					this.nonFatalErrors = true;
				}

				$errorCount.html( data.error_count );

				if ( 1 === data.error_count ) {
					$( '.progress-errors-title .error-text' ).html( this.getString( 'error' ) );
				} else {
					$( '.progress-errors-title .error-text' ).html( this.getString( 'errors' ) );
				}
			}
		},

		/**
		 * Close the modal and hide the overlay
		 */
		hideOverlay: function() {
			$( '.modal-content' ).animate( { 'top': '-' + this.contentHeight + 'px' }, 400, 'swing', function() {
				$( '#overlay' ).remove();
				$( '.modal-content' ).remove();
			} );
			this.processCompleted = false;
			this.progressModalActive = false;
		},

		/**
		 * Maybe close the modal and hide the available
		 */
		maybeHideOverlay: function() {
			if ( true === this.progressModalActive && true === this.processCompleted ) {
				this.hideOverlay();
			}
		},

		/**
		 * Disable the given collection of elements.
		 *
		 * @param {string|Element|Array|jQuery} elements Any valid jQuery query parameter
		 */
		disableElements: function( elements ) {
			$( elements ).addClass( 'disabled' ).prop( 'disabled', true );
		},

		/**
		 * Enable the given collection of elements.
		 *
		 * @param {string|Element|Array|jQuery} elements Any valid jQuery query parameter
		 */
		enableElements: function( elements ) {
			$( elements ).removeClass( 'disabled' ).prop( 'disabled', false );
		},

		/**
		 * Display the spinner.
		 */
		showSpinner: function() {
			as3cfpro.tool.$progressContent.find( '.spinner' ).addClass( 'spinning' );
		},

		/**
		 * Hide the spinner.
		 */
		hideSpinner: function() {
			as3cfpro.tool.$progressContent.find( '.spinner' ).removeClass( 'spinning' );
		},

		/**
		 * Dismiss all tool errors for a media library item.
		 *
		 * @param event Event
		 */
		dismissAllItemErrors: function( event ) {
			event.preventDefault();

			var $dismissTrigger = $( this );
			var $media = $dismissTrigger.closest( '.media-error' );
			var mediaId = $media.data( 'mediaId' );

			// Tool ID must be set before the ajax call
			as3cfpro.tool.ID = $dismissTrigger.closest( '[data-tool]' ).data( 'tool' );

			$dismissTrigger.addClass( 'dismissed' );

			as3cfpro.tool.ajaxDismissItemErrors( {
				media_id: mediaId,
				blog_id: $media.data( 'blogId' ),
				errors: 'all'
			} ).done( function( response ) {

				// Select all instances within all tool lists on the page
				$( '[data-action="dismiss-item-errors"]', '.as3cf-' + as3cfpro.tool.ID + '-notice-list .media-error-' + mediaId ).fadeOut( 'slow' );

				var $mediaError = $( '.media-error-' + mediaId, '.as3cf-' + as3cfpro.tool.ID + '-notice-list' );
				$mediaError.find( '.dismiss-item-errors' ).hide();
				$mediaError.find( '.media-error-message' ).addClass( 'dismissed' );
			} ).fail( as3cfpro.tool.ajaxError );
		},

		/**
		 * Dismiss an individual error.
		 *
		 * @param event Event
		 */
		dismissError: function( event ) {
			event.preventDefault();

			var $media = $( this ).closest( '.media-error' );
			var $errorItem = $( this ).closest( '.media-error-message' );
			var mediaId = $media.data( 'mediaId' );

			// Tool ID must be set before the ajax call
			as3cfpro.tool.ID = $( this ).closest( '[data-tool]' ).data( 'tool' );

			as3cfpro.tool.ajaxDismissItemErrors( {
				media_id: mediaId,
				blog_id: $media.data( 'blogId' ),
				errors: $errorItem.data( 'idx' )
			} ).done( function( response ) {

				// Select all instances within all tool lists on the page
				var $mediaError = $( '.media-error-' + mediaId, '.as3cf-' + as3cfpro.tool.ID + '-notice-list' );
				$mediaError.find( '.media-error-message-' + $errorItem.data( 'idx' ) ).addClass( 'dismissed' );
			} ).fail( as3cfpro.tool.ajaxError );
		},

		/**
		 * Send AJAX request to dismiss errors for the current tool.
		 *
		 * @param data
		 *
		 * @returns {*|$.promise}
		 */
		ajaxDismissItemErrors: function( data ) {
			data = $.extend( data || {}, {
				action: as3cfpro.tool.getAjaxAction( 'dismiss_errors' ),
				nonce: as3cfpro.tool.getAjaxNonce( 'dismiss_errors' )
			} );

			return $.post( ajaxurl, data );
		},

		/**
		 * Prepare progress data for submit to the server.
		 *
		 * @param {Object} progress
		 *
		 * @returns {Object} Copy of input progress, prepared for POST to the server.
		 */
		prepareProgressForPost: function( progress ) {
			return _.omit( progress, [ 'errors', 'errorsHtml' ] );
		},

		/**
		 * Array.slice ... but for Object!
		 *
		 * @param {object} obj
		 * @param {int} start
		 * @param {int} end - Optional
		 * @returns {object}
		 *
		 * Note: Explicitly not extending Object.prototype because jQuery throws a wobbly.
		 */
		sliceObject: function( obj, start, end ) {
			var sliced = {};

			// Shortcut out if start is too large.
			if ( start >= obj.length ) {
				return sliced;
			}

			// If end not set we're going to end.
			if ( undefined === end ) {
				end = Object.keys( obj ).length;
			}

			var keys = Object.keys( obj ).slice( start, end );
			var values = Object.values( obj ).slice( start, end );

			return keys.reduce( function( obj, key, idx ) {
				obj[ key ] = values[ idx ];

				return obj;
			}, {} );
		}
	};

	window.onbeforeunload = function( e ) {
		if ( as3cfpro.tool.currentlyProcessing ) {
			e = e || window.event;

			var sure_string = as3cfpro.tool.getString( 'sure' );

			// For IE and Firefox prior to version 4
			if ( e ) {
				e.returnValue = sure_string;
			}

			// For Safari
			return sure_string;
		}
	};

	// Event Handlers
	$( document ).ready( function() {

		// Store the HTML of the views
		as3cfpro.tool.cloneViews();

		// Listen for any URLs for triggering tools
		as3cfpro.tool.openFromURL();

		// Display Tool Modal
		$( 'body' ).on( 'click', 'a.as3cf-pro-tool', function( e ) {
			e.preventDefault();

			if ( $( e.target ).hasClass( 'disabled' ) ) {
				return;
			}

			as3cfpro.tool.open( $( this ).closest( '.block' ).attr( 'id' ) );
		} );

		// Handle Pause / Resumes clicks
		$( 'body' ).on( 'click', '.progress-content .pause-resume', as3cfpro.tool.setPauseResumeButton.bind( as3cfpro.tool ) );

		// Handles Cancel clicks
		$( 'body' ).on( 'click', '.progress-content .cancel', as3cfpro.tool.cancel.bind( as3cfpro.tool ) );

		// Close modal
		$( 'body' ).on( 'click', '.close-progress-content-button', as3cfpro.tool.hideOverlay.bind( as3cfpro.tool ) );

		// Close modal on overlay click when possible
		$( 'body' ).on( 'click', '#overlay', as3cfpro.tool.maybeHideOverlay.bind( as3cfpro.tool ) );

		$( 'body' ).on( 'click', '.toggle-progress-errors', function( e ) {
			e.preventDefault();
			var $toggle = $( this );
			var $modal = $toggle.closest( '.progress-content' );
			var $errors = $modal.find( '.progress-errors' );

			$errors.toggleClass( 'expanded' );
			$toggle.html( $errors.hasClass( 'expanded' ) ? as3cfpro.tool.getString( 'hide' ) : as3cfpro.tool.getString( 'show' ) );
		} );

		// Dismiss all errors for a media library item
		$( 'body' ).on( 'click', '.media-error [data-action="dismiss-item-errors"]', as3cfpro.tool.dismissAllItemErrors );

		// Dismiss an individual error message for a media library item (eg: image size)
		$( 'body' ).on( 'click', '.media-error [data-action="dismiss-error"]', as3cfpro.tool.dismissError );

		// Show / hide helper description
		$( 'body' ).on( 'click', '.general-helper', function( e ) {
			e.preventDefault();
			var icon = $( this );
			var bubble = $( this ).next();

			// Close any that are already open
			$( '.helper-message' ).not( bubble ).hide();

			var position = icon.position();
			bubble.css( {
				'left': ( position.left - bubble.width() - 35 ) + 'px',
				'top': ( position.top - 25 ) + 'px'
			} );

			bubble.toggle();
			e.stopPropagation();
		} );

		$( 'body' ).click( function() {
			$( '.helper-message' ).hide();
		} );

		$( '.helper-message' ).click( function( e ) {
			e.stopPropagation();
		} );
	} );

})( jQuery, _ );
