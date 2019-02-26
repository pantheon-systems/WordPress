/*global ajaxurl, _ */

var WPMLCore = WPMLCore || {};

WPMLCore.wizardFramework = function ( $, wizardImplementation ) {
	"use strict";

	var self = this;

	var init = function () {

		self.storage = {};

		self.backButton = $( '.js-wizard-back' );
		self.nextButton = $( '.js-wizard-next' );
		self.stepContent = $( '.js-wizard-step-content' );
		self.nonce = $( '.js-wpml-wizard' ).data( 'nonce' );
		self.steps = $( '.js-wizard-step' );

		self.currentStep = self.getCurrentStep();
		self.getCurrentStepIndex();

		self.initializeButtons();

		wizardImplementation.notifyCurrentStep( self.currentStep );

		self.fetchContent();

		self.hideNotices();
		self.hideScreenOptions();
	};

	var showElement = function ( element, state ) {
		if ( state ) {
			element.show();
		} else {
			element.hide();
		}
	};

	self.moveToStep = function ( newStep ) {
		var beforeCurrentStepClass = 'wizard-active-step';
		self.steps.removeClass( beforeCurrentStepClass );
		self.steps.each( function ( index ) {
			if ( index === self.currentStepIndex ) {
				$( this ).removeClass( 'wizard-current-step' );
			}
			if ( index === newStep ) {
				self.currentStep = $( this ).addClass( 'wizard-current-step' ).data( 'step-slug' );
				beforeCurrentStepClass = '';
			}
			if ( beforeCurrentStepClass ) {
				$( this ).addClass( beforeCurrentStepClass );
			}
		} );

		self.currentStepIndex = newStep;

		self.setBackButtonState();
		self.setNextButtonState();

		self.fetchContent();

	};


	self.showSteps = function ( state ) {
		showElement( $( '.js-wizard-steps-container' ), state );
	};

	self.moveToNextStep = function ( e ) {
		wizardImplementation.isOkToMoveToNextStep( self.currentStep, function () {
			self.moveToStep( self.currentStepIndex + 1 );
		} );
	};

	self.moveToPreviousStep = function ( e ) {
		self.moveToStep( self.currentStepIndex - 1 );
	};

	self.getCurrentStepIndex = function () {
		self.steps.each( function ( index ) {
			if ( self.currentStep === $( this ).data( 'step-slug' ) ) {
				self.currentStepIndex = index;
			}
		} );
	};

	self.getCurrentStep = function () {
		return self.stepContent.data( 'current-step' );
	};

	self.initializeButtons = function () {
		self.backButton.on( 'click', self.moveToPreviousStep );
		self.nextButton.on( 'click', self.moveToNextStep );

		self.setBackButtonState();
		self.setNextButtonState();
	};

	self.setBackButtonState = function () {
		if ( self.currentStepIndex === 0 ) {
			self.backButton.attr( 'disabled', 'disabled' );
		} else {
			self.backButton.removeAttr( 'disabled' );
		}
	};

	self.setNextButtonState = function () {
		self.enableNextButton( self.currentStepIndex !== self.steps.length - 1 );
	};

	self.fetchContent = function ( data ) {
		if ( !data ) {
			data = {};
		}
		data = _.extend( data, {
			action: 'wpml_wizard_fetch_content',
			step_slug: self.currentStep,
			nonce: self.nonce
		} );
		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			dataType: 'json',
			data: data,
			success: function ( response ) {
				$( '.js-wpml-wizard' ).show();
				if ( response.success ) {
					self.stepContent.html( response.data );
					wizardImplementation.notifyContentFetched( self.currentStep, self.stepContent );
				}
			}
		} );
	};

	self.showBackButton = function ( state ) {
		showElement( self.backButton, state );
	};

	self.showNextButton = function ( state ) {
		showElement( self.nextButton, state );
	};

	self.setNextButtonText = function ( text ) {
		self.nextButton.html( text );
	};

	self.enableNextButton = function ( state ) {
		if ( state ) {
			self.nextButton.removeAttr( 'disabled' );
		} else {
			self.nextButton.attr( 'disabled', 'disabled' );
		}
	};

	self.setNextButtonPrimary = function ( state ) {
		if ( state ) {
			self.nextButton.addClass( 'button-primary' ).removeClass( 'button-secondary' );
		} else {
			self.nextButton.addClass( 'button-secondary' ).removeClass( 'button-primary' );
		}
	};

	self.triggerNextStep = function () {
		self.nextButton.trigger( 'click' );
	};

	self.storeData = function ( key, data ) {
		self.storage[ key ] = data;
	};

	self.getData = function ( key, defaultValue ) {
		if ( self.storage[ key ] ) {
			return self.storage[ key ];
		} else {
			return defaultValue;
		}
	};

	self.hideNotices = function () {
		$( '.otgs-notice, .icl_admin_message' ).hide();
	};

	self.hideScreenOptions = function () {
		$( '#screen-meta-links' ).hide();
	};

	init();

};

WPMLCore.wizardFrameworkFactory = {
	create: function ( $, wizardImplementation ) {
		"use strict";

		return new WPMLCore.wizardFramework( $, wizardImplementation );
	}
};
