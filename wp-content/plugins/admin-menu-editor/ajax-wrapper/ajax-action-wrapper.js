var AjawV1 = window.AjawV1 || {};

AjawV1.AjaxAction = (function () {
	"use strict";

	function AjawAjaxAction(action, properties) {
		this.action = action;
		this.ajaxUrl = properties['ajaxUrl'];
		this.nonce = properties['nonce'];
		this.requiredMethod = (typeof properties['method'] !== 'undefined') ? properties['method'] : null;
	}

	/**
	 * Send a POST request.
	 *
	 * @param {Object} params
	 * @param {Function} success
	 * @param {Function} [error]
	 */
	AjawAjaxAction.prototype.post = function (params, success, error) {
		return this.request(params, success, error, 'POST');
	};

	/**
	 * Send a GET request.
	 *
	 * @param {Object} params
	 * @param {Function} success
	 * @param {Function} [error]
	 */
	AjawAjaxAction.prototype.get = function(params, success, error) {
		return this.request(params, success, error, 'GET');
	};

	/**
	 * Send an AJAX request using the specified HTTP method.
	 *
	 * @param {Object} params
	 * @param {Function} success
	 * @param {Function} [error]
	 * @param {String} [method]
	 */
	AjawAjaxAction.prototype.request = function(params, success, error, method) {
		if (typeof params === 'function') {
			//It looks like "params" was omitted and the first argument is actually the success callback.
			//Shift all arguments left one step. The reverse order is due to argument binding shenanigans.
			method = arguments[2];
			error = arguments[1];
			success = arguments[0];
			params = {};
		}

		if (typeof params === 'undefined') {
			params = {};
		}  else if (typeof params !== 'object') {
			//While jQuery accepts request data in object and string form, this library only supports objects.
			throw 'Data that\'s to be sent to the server must be an object, not ' + (typeof params);
		}

		if (typeof method === 'undefined') {
			method = this.requiredMethod || 'POST';
		}
		if (this.requiredMethod && (method !== this.requiredMethod)) {
			throw 'Wrong HTTP method. This action requires ' + this.requiredMethod;
		}

		//noinspection JSUnusedGlobalSymbols
		return jQuery.ajax(
			this.ajaxUrl,
			{
				method: method,
				data: this.prepareRequestParams(params),
				success: function(data, textStatus, jqXHR) {
					if (success) {
						success(data, textStatus, jqXHR);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					var data = jqXHR.responseText;
					if (typeof jqXHR['responseJSON'] !== 'undefined') {
						data = jqXHR['responseJSON'];
					} else if (typeof jqXHR['responseXML'] !== 'undefined') {
						data = jqXHR['responseXML'];
					}

					if (error) {
						error(data, textStatus, jqXHR, errorThrown);
					}
				}
			}
		);
	};

	AjawAjaxAction.prototype.prepareRequestParams = function(params) {
		if (params === null) {
			params = {};
		}

		params['action'] = this.action;
		if (this.nonce !== null) {
			params['_ajax_nonce'] = this.nonce;
		}
		return params;
	};

	return AjawAjaxAction;
}());

AjawV1.actionRegistry = (function() {
	var actions = {};

	return {
		/**
		 *
		 * @param {String} actionName
		 * @return {AjawAjaxAction}
		 */
		get: function(actionName) {
			if (actions.hasOwnProperty(actionName)) {
				return actions[actionName];
			}
			return null;
		},

		add: function(actionName, properties) {
			actions[actionName] = new AjawV1.AjaxAction(actionName, properties);
		}
	}
})();

/**
 * Get a registered action wrapper.
 *
 * @param {string} action
 * @return {AjawAjaxAction|null}
 */
AjawV1.getAction = function(action) {
	return this.actionRegistry.get(action);
};