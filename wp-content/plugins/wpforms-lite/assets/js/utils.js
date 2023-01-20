'use strict';

// eslint-disable-next-line no-unused-vars
const WPFormsUtils = window.WPFormsUtils || ( function( document, window, $ ) {

	/**
	 * Public functions and properties.
	 *
	 * @since 1.7.6
	 *
	 * @type {object}
	 */
	const app = {

		/**
		 * Wrapper to trigger a native or custom event and return the event object.
		 *
		 * @since 1.7.6
		 *
		 * @param {jQuery} $element  Element to trigger event on.
		 * @param {string} eventName Event name to trigger (custom or native).
		 * @param {Array}  args      Trigger arguments.
		 *
		 * @returns {Event} Event object.
		 */
		triggerEvent: function( $element, eventName, args = [] ) {

			let eventObject = new $.Event( eventName );

			$element.trigger( eventObject, args );

			return eventObject;
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );
