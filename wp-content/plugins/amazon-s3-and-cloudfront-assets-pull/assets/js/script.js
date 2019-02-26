/* global as3cf_assets_pull */
(function( $, config ) {
	var $document = $( document );
	var $assetsTab = $( '#tab-assets_pull' );
	var $assetsSettings = $assetsTab.find( '.as3cf-settings-form' );
	var $primaryDomainInput = $assetsSettings.find( 'input[name="domain"]' );
	var currentPullDomain = config.settings.domain || '';
	var globalBackup = {};
	var CloudFrontSetupWizard;
	var assetsPullInit = _.once( function() {
		$document.trigger( 'as3cf.assetsPull.init' );
	} );

	/**
	 * SetupWizard class
	 * @param slug  Wizard unique identifier
	 * @constructor
	 */
	var SetupWizard = function( slug ) {
		/**
		 * @type {String} Wizard identifier
		 */
		this.slug = slug;

		/**
		 * @type {jQuery} jQuery instance holding the reference to the Wizard's primary element
		 */
		this.$el = $( '<div class="as3cf-setup-wizard"></div>' ).data( 'wizard', this );

		/**
		 * @type {jQuery} jQuery instance holding the reference to the Wizard's image frame
		 */
		this.$imageFrame = $( '<div class="as3cf-image-frame"><img></div>' );

		/**
		 * @type {SetupWizardStep|null} Reference to the current setup step object
		 */
		this.currentStep = null;

		/**
		 * Collection of SetupWizardStep objects
		 *
		 * @type {Object} Underscore collection
		 */
		this.steps = _( [] );
	};

	/**
	 * Initialize the wizard.
	 */
	SetupWizard.prototype.init = function() {
		this.initSteps( config.wizard[ this.slug ].steps );
		this.initDom();
		this.bindToDataActions();
	};

	/**
	 * SetupWizardStep class.
	 *
	 * Represents a single step for a setup wizard.
	 *
	 * @param {object} stepData
	 * @param {object} collection Underscore collection of all steps for the wizard it belongs to.
	 * @constructor
	 */
	var SetupWizardStep = function( stepData, collection ) {
		this.data = stepData;
		this.id = stepData.id;
		this.collection = collection;
	};

	/**
	 * Initialize the DOM element for the step.
	 *
	 * @returns {jQuery} jQuery instance of the step's DOM element
	 */
	SetupWizardStep.prototype.initEl = function() {
		this.index = this.collection.indexOf( this );
		this.nextStep = this.collection.value()[ this.index + 1 ];
		this.prevStep = this.collection.value()[ this.index - 1 ];
		this.total = this.collection.size() - 1;
		var $stepBody = $( '<div class="as3cf-wizard-step-body"></div>' ).html( this.data.html );

		this.$el = $( '<div class="as3cf-wizard-step"></div>' )
			.append( $stepBody )
			.attr( 'data-wizard-step', this.data.id )
			.append( this.generateControls() )
		;

		if ( this.isOverview() ) {
			this.$el.addClass( 'as3cf-wizard-step-overview' );
		} else if ( this.isLast() ) {
			this.$el.addClass( 'as3cf-wizard-step-last' );
		}

		return this.$el;
	};

	/**
	 * Whether or not this step is the overview step.
	 *
	 * @returns {boolean}
	 */
	SetupWizardStep.prototype.isOverview = function() {
		return 0 === this.index;
	};

	/**
	 * Whether or not this step is the last in the collection.
	 *
	 * @returns {boolean}
	 */
	SetupWizardStep.prototype.isLast = function() {
		return this === this.collection.last();
	};

	/**
	 * Generate html elements for the step's navigation.
	 *
	 * @returns {jQuery}
	 */
	SetupWizardStep.prototype.generateControls = function() {
		var $controls = $( '<div class="as3cf-wizard-controls"></div>' );
		var $primary = $( '<div class="as3cf-wizard-control-row as3cf-wizard-control-row-primary"></div>' );
		var $secondary = $( '<div class="as3cf-wizard-control-row as3cf-wizard-control-row-secondary"></div>' );
		var defaultNext = config.strings.next_prefix + ' ';

		if ( this.nextStep ) {
			defaultNext += this.nextStep.data.title;
			$primary.append( '<button class="button-primary" data-action-wizard="nextStep">' + ( this.data.next_step_text || defaultNext ) + '</button>' );
		}
		if ( this.isLast() ) {
			$primary.append( '<button class="button-primary" data-action-wizard="complete">' + ( this.data.complete_setup_text || config.strings.complete_setup ) + '</button>' );
		}
		if ( this.prevStep ) {
			$primary.append( '<a href="#" data-action-wizard="prevStep">' + config.strings.previous_step + '</a>' );
		}
		if ( this.index > 0 ) {
			$secondary.append( '<div class="as3cf-step-progress wp-ui-text-icon">Step ' + this.index + ' of ' + this.total + '</div>' );
			// .wp-ui-text-icon is the only lighter text utility class available for wp-admin :/ (sets color only)
		}

		$secondary.append( '<a href="#" data-action-wizard="exit">' + config.strings.skip_to_settings + '</a>' );
		$controls.append( $primary, $secondary );

		return $controls;
	};

	/**
	 * Get the proper camelCase jQuery.data() key for this wizard.
	 *
	 * @returns {string}
	 */
	SetupWizard.prototype.dataKey = function() {
		return 'actionWizard' + this.slug[0].toUpperCase() + this.slug.substr( 1 );
	};

	/**
	 * Get a function which proxies a given data attribute value to method calls on the wizard.
	 *
	 * @param {String} dataKey The proper camelCase jQuery.data() key who's value maps to a method name on the wizard.
	 *
	 * @returns {Function}
	 */
	SetupWizard.prototype.actionProxy = function( dataKey ) {
		var wizard = this;

		return function( event ) {
			var $el = $( this );
			var methodName = $el.data( dataKey );
			var wizardMethod = wizard[ methodName ];

			event.preventDefault();

			if ( 'function' === typeof wizardMethod ) {
				wizardMethod.call( wizard );
			} else {
				console.warn( 'The setup wizard for ' + wizard.slug + ' has no method "' + methodName + '"' );
			}
		};
	};

	/**
	 * Bind wizard data-action clicks to methods on this wizard.
	 */
	SetupWizard.prototype.bindToDataActions = function() {
		$document.on( 'click', '[data-action-wizard-' + this.slug + ']', this.actionProxy( this.dataKey() ) );
		this.$el.on( 'click', '[data-action-wizard]', this.actionProxy( 'actionWizard' ) );
		this.$el.on( 'click', '[data-action-wizard-goto-step]', function( event ) {
			var id = $( event.target ).data( 'actionWizardGotoStep' );
			event.preventDefault();
			this.goToStep( this.getStepById( id ) );
		}.bind( this ) );
	};

	/**
	 * Launch the wizard with a given step.
	 *
	 * If no step is provided, the wizard will begin from the first step.
	 *
	 * @param {String|SetupWizardStep} step
	 */
	SetupWizard.prototype.launch = function( step ) {
		if ( 'string' === typeof step ) {
			step = this.getStepById( step );
		}

		this.goToStep( step || this.steps.first() );
		this.display();
		this.$el.trigger( 'as3cf.wizard.launched', [ this ] );
	};

	/**
	 * Display the wizard.
	 */
	SetupWizard.prototype.display = function() {
		$assetsSettings.hide();
		this.$el.show();
	};

	/**
	 * Get a Step instance from the collection by its ID.
	 *
	 * @param {String} stepId
	 *
	 * @returns {SetupWizardStep|undefined}
	 */
	SetupWizard.prototype.getStepById = function( stepId ) {
		return this.steps.findWhere( { id: stepId } );
	};

	/**
	 * Updates the current step with the given target step and prepares it for display.
	 *
	 * @param targetStep
	 */
	SetupWizard.prototype.goToStep = function( targetStep ) {
		if ( ! ( targetStep instanceof SetupWizardStep ) ) {
			console.warn( 'Invalid step', targetStep );

			return;
		}

		if ( ! this.canChangeStep() ) {
			return;
		}

		this.steps.each( function( step ) {
			if ( step !== targetStep ) {
				step.$el.removeClass( 'active' );
			}
		} );

		this.currentStep = targetStep;
		this.updateUrl();
		targetStep.$el.addClass( 'active' );
	};

	/**
	 * Check if the user is able to go to a different step or not.
	 *
	 * @returns {boolean}
	 */
	SetupWizard.prototype.canChangeStep = function() {
		var wizard = this;

		if ( ! this.currentStep ) {
			return true;
		}

		try {
			this.currentStep.$el.find( '[data-as3cf-validate]' ).each( function( idx, el ) {
				var validations = $( el ).data( 'as3cfValidate' );
				validations = _.isString( validations ) ? validations.split( '|' ) : [];

				wizard.$el.trigger( 'as3cf.wizard.validate', [ validations, el, wizard ] );
			} );
		} catch ( e ) {
			alert( e );

			return false;
		}

		return true;
	};

	/**
	 * Update the browser's URL with current step data.
	 *
	 * This allows us to support back or forward navigation with the browser.
	 */
	SetupWizard.prototype.updateUrl = function() {
		var state = {
			namespace: 'as3cf.assetsPull',
			wizard: this.slug,
			step: this.currentStep ? this.currentStep.id : null
		};

		if ( 'function' !== typeof history.pushState ) {
			return;
		}

		// Only push state if it has changed
		if ( ! _.isEqual( state, history.state ) ) {
			var path = location.pathname;
			var queryParam = this.slug + '_setup_step';
			var query;

			if ( this.currentStep && location.search.match( '&' + queryParam ) ) {
				query = location.search.replace( new RegExp( '\\b' + queryParam + '=[^&#]+|$' ), queryParam + '=' + this.currentStep.id );
			} else if ( this.currentStep && ! location.search.match( '&' + queryParam ) ) {
				query = location.search + '&' + queryParam + '=' + this.currentStep.id;
			} else {
				query = location.search.replace( new RegExp( '&?' + queryParam + '=[^&#]+' ), '' );
			}

			history.pushState( state, '', path + query + location.hash );
		}
	};

	/**
	 * Advance the wizard to the next step if there is one.
	 */
	SetupWizard.prototype.nextStep = function() {
		if ( this.currentStep.nextStep ) {
			this.goToStep( this.currentStep.nextStep );
		} else {
			console.warn( 'No next step!' );
		}
	};

	/**
	 * Advance the wizard to the step before the current step, if there is one.
	 */
	SetupWizard.prototype.prevStep = function() {
		if ( this.currentStep.prevStep ) {
			this.goToStep( this.currentStep.prevStep );
		} else {
			console.warn( 'No previous step!' );
		}
	};

	/**
	 * Initialize the DOM for the wizard.
	 */
	SetupWizard.prototype.initDom = function() {
		var $el = this.$el
			.addClass( 'as3cf-setup-wizard-' + this.slug )
			.hide();

		this.steps.each( function( step ) {
			$el.append( step.initEl() );
		} );

		this.initImageFrame();
		$assetsTab.append( $el );
	};

	/**
	 * Initialize the wizard's image frame.
	 */
	SetupWizard.prototype.initImageFrame = function() {
		var $imageFrame = this.$imageFrame;

		this.$el
			.on( 'click', '[data-wizard-step] img', function() {
				$imageFrame.find( 'img' ).attr( 'src', this.src );
				$imageFrame.addClass( 'active' );
			} )
			.append( $imageFrame );

		$imageFrame.on( 'click', function() {
			$( this ).removeClass( 'active' );
		} );
	};

	/**
	 * Perform any actions necessary once the wizard has been completed.
	 */
	SetupWizard.prototype.complete = function() {
		if ( ! $assetsTab.find( '.as3cf-wizard-complete-' + this.slug ).length ) {
			$assetsTab.prepend( this.makeCompletedNotice() );

			/*
			 * Make the injected notice dismissible.
			 * (kind of a hack, but this is the only non-error event we can trigger after load that will call makeNoticesDismissible())
			 * see https://github.com/WordPress/WordPress/blob/8a07db035df0452eead65ffa42158e6138d04f86/wp-admin/js/common.js#L394-L415
			 */
			$document.trigger( 'wp-updates-notice-added' );
		}

		this.exit();
		this.$el.trigger( 'as3cf.wizardCompleted', [ this ] );
		this.$el.trigger( 'as3cf.' + this.slug + 'Wizard.completed', [ this ] );
	};

	/**
	 * Build the completed notice for this wizard.
	 *
	 * @return {jQuery} jQuery instance for the generated notice html.
	 */
	SetupWizard.prototype.makeCompletedNotice = function() {
		return $( '<div class="notice is-dismissible as3cf-updated updated inline show as3cf-wizard-complete"></div>' )
			.addClass( 'as3cf-wizard-complete-' + this.slug )
			.html( $( '<p></p>' ).text( config.wizard[ this.slug ].completed_message ) );
	};

	/**
	 * Close the wizard.
	 */
	SetupWizard.prototype.exit = function() {
		this.currentStep = null;
		this.updateUrl();
		this.$el.hide();
		$assetsSettings.show();
	};

	/**
	 * Load the steps for this wizard from the raw data.
	 *
	 * @param {Array} steps Array of step object data
	 */
	SetupWizard.prototype.initSteps = function( steps ) {
		var wizard = this;

		_.each( steps, function( step ) {
			wizard.steps.push( new SetupWizardStep( step, wizard.steps ) );
		} );
	};

	CloudFrontSetupWizard = new SetupWizard( 'cloudfront' );

	$document
		.on( 'as3cf.tabRendered', function( event, tab ) {
			if ( 'assets_pull' === tab ) {
				assetsPullInit();
			}
		} )
		.one( 'as3cf.assetsPull.init', function() {
			CloudFrontSetupWizard.init();

			if ( config.wizard.cloudfront.launch_on_load ) {
				CloudFrontSetupWizard.launch( config.wizard.cloudfront.launch_on_load );
			}

			if ( config.settings.domain && config.settings.domain !== config.domain_status.domain ) {
				checkDomainStatus();
			}
		} )
	;

	CloudFrontSetupWizard.$el
		.on( 'as3cf.wizard.launched', _.once( function() {
			CloudFrontSetupWizard.$el.find( '[name="domain"]' )
				.val( currentPullDomain || 'assets.' + location.hostname )
				.trigger( 'change' )
			;
		} ) )
		.on( 'as3cf.wizard.validate', function( event, validations, el, wizard ) {
			var $el = $( el );

			_.each( validations, function( validation ) {
				if ( 'domain' === validation ) {
					if ( ! domainIsValid( $el.val() ) ) {
						throw config.strings.invalid_domain;
					}
				}
			} );
		} )
		.on( 'change', '[data-as3cf-setting]', function( event ) {
			var changedEl = this;
			var $changed = $( this );
			var settingKey = $changed.data( 'as3cfSetting' );

			CloudFrontSetupWizard.$el.find( '[data-as3cf-setting="' + settingKey + '"]' )
				.not( ':input' )
				.not( changedEl )
				.text( $changed.val() )
			;

			CloudFrontSetupWizard.$el.trigger( 'as3cf.assetsPull.settingChanged', [ settingKey, $changed.val() ] );
		} )
		.on( 'as3cf.assetsPull.settingChanged', function( event, key, value ) {
			var subdomain;
			var segments;

			if ( 'domain' === key ) {
				currentPullDomain = value;
				segments = value.split( '.' );

				if ( segments.length > 2 ) {
					subdomain = segments.shift();
				} else {
					subdomain = '@';
				}

				$assetsTab.find( '[data-as3cf-setting="basedomain_ref"]' ).text( segments.join( '.' ) );
				$assetsTab.find( '[data-as3cf-setting="subdomain_ref"]' ).text( subdomain );
			}
		} )
	;

	/*
	* Check for browser support to enable click-to-copy functionality.
	* If it exists, we add a class for styling, and bind the click listener to make it happen.
	*/
	if ( 'function' === typeof document.execCommand ) {
		$assetsTab.addClass( 'as3cf-click-to-copy' )
			.on( 'click', '[data-as3cf-copy]', function() {
				selectText( this );
				document.execCommand( 'copy' );
				window.getSelection().removeAllRanges();
			} )
		;
	}

	/*
	* Extend the global 'onpopstate' event.
	* Store the reference to the existing callback if any.
	*/
	globalBackup.onpopstate = window.onpopstate;

	// Handle onpopstate events between steps for the CloudFront setup wizard
	window.onpopstate = function( event ) {
		if (
			_.has( event.state, 'step' ) &&
			_.isMatch( event.state, { wizard: 'cloudfront', namespace: 'as3cf.assetsPull' } )
		) {
			CloudFrontSetupWizard.launch( event.state.step );
		}

		// Call the global popstate callback if it was previously set
		if ( 'function' === typeof globalBackup.onpopstate ) {
			globalBackup.onpopstate.apply( this, arguments );
		}
	};

	/**
	 * Dispatch an async post request to check the status of the current pull domain.
	 *
	 * @param {object} data Optional extra post parameters
	 *
	 * @returns {jqXHR}
	 */
	function ajaxCheckDomainStatus( data ) {
		data = _.isObject( data ) ? data : {};

		return $.post( ajaxurl, _.extend( {}, data, {
			action: config.actions.check_assets_domain,
			_ajax_nonce: config.nonces.check_assets_domain,
			domain: $primaryDomainInput.val()
		} ) );
	}

	/**
	 * Initiate a domain check for the current pull domain, and update the UI accordingly.
	 *
	 * @param {object} data Optional extra post parameters
	 *
	 * @returns {jqXHR} ajax domain check promise
	 */
	function checkDomainStatus( data ) {
		var delay = $.Deferred();
		var ajax  = ajaxCheckDomainStatus( data );
		var $verifyDomain = $assetsTab.find( '.as3cf-verify-domain' )
			.attr( 'check-result', '' )
			.addClass( 'checking' );

		/*
		 * Here we use a timeout to set a minimum time for the function to run.
		 * This makes for a much smoother experience visually if the request happens quickly.
		 */
		setTimeout( function() {
			delay.resolve();
		}, 1500 );

		$.when( ajax, delay ).done( function( ajaxResolved ) {
			var response = ajaxResolved[0];

			$verifyDomain
				.removeClass( 'checking' )
				.addClass( 'has-checked' )
				.attr( 'check-result', !! response.success + 0 );

			if ( _.isObject( response.data ) ) {
				$verifyDomain.find( '[data-as3cf-bind="checked_domain"]' ).text( response.data.domain );
				$verifyDomain.find( '[data-as3cf-bind="last_checked_at"]' ).text( response.data.last_checked_at );
				$verifyDomain.find( '[data-as3cf-bind="check_message"]' ).text( response.data.message );
				if ( response.data.more_info ) {
					$verifyDomain.find( '[data-as3cf-bind-href="more_info"]' ).attr( 'href', response.data.more_info ).show();
				} else {
					$verifyDomain.find( '[data-as3cf-bind-href="more_info"]' ).hide();
				}
			}
		} );

		return ajax;
	}

	$assetsTab
		.on( 'click', '[data-action-as3cf-check-domain]', function( event ) {
			event.preventDefault();

			if ( domainIsValid( $primaryDomainInput.val() ) ) {
				checkDomainStatus();
			} else {
				alert( config.strings.invalid_domain );
			}
		} )
		.on( 'as3cf.cloudfrontWizard.completed', function() {
			$primaryDomainInput.val( currentPullDomain );

			checkDomainStatus( { save_domain: true } );
		} );

	/**
	 * Check if the given domain name appears valid.
	 *
	 * @param {string} domain
	 *
	 * @returns {boolean}
	 */
	function domainIsValid( domain ) {
		if (
			! _.isString( domain ) ||
			domain.match( /[^a-z0-9-\.]/i ) || // Cannot contain any other characters
			! domain.match( /^[a-z0-9].*[a-z0-9]$/i ) // Must start and end with an alpha-numeric character
		) {
			return false;
		}

		return true;
	}

	/**
	 * Selects the inner text of a given HTML element.
	 *
	 * @param {HTMLElement} element
	 *
	 * Based on
	 * @link https://stackoverflow.com/a/987376/1037938
	 */
	function selectText( element ) {
		var range;
		var selection;

		if ( document.body.createTextRange ) {
			range = document.body.createTextRange();
			range.moveToElementText( element );
			range.select();
		} else if ( window.getSelection ) {
			selection = window.getSelection();
			range = document.createRange();
			range.selectNodeContents( element );
			selection.removeAllRanges();
			selection.addRange( range );
		}
	}

})( jQuery, as3cf_assets_pull );
